// Portfolio hosting on Azure — designed to cost $0.00 per month, permanently.
//
// Implementation choices:
// - App Service Free (F1) Linux plan: the only App Service tier that stays free for the life of
//   the subscription, including after Azure for Students converts to Pay-As-You-Go.
//   Accepted trade-offs: *.azurewebsites.net address only, no Always On (cold starts after
//   ~20 idle minutes), 60 CPU-minutes/day, 1 GB disk.
// - SQLite on the persistent /home share instead of a managed database: every managed database
//   on Azure is billed after its 12-month free period, and this site is single-author and read-mostly.
// - A $1 monthly budget on the whole subscription as a tripwire: the expected bill is $0.00,
//   so any alert means something billable exists — caught at cents, not at $100.
//
// Security considerations:
// - Secrets (APP_KEY, admin password) are @secure() parameters read from environment variables
//   by main.bicepparam; they never enter source control or deployment history in plain text.
// - Key Vault is deliberately not used: it bills per operation, which breaks the $0 goal.
//   App settings are encrypted at rest by App Service and only visible to the subscription owner.
//
// Deploy (Azure CLI, signed in with `az login`). Always preview first:
//   az deployment sub what-if --location francecentral --template-file infra/main.bicep --parameters infra/main.bicepparam
//   az deployment sub create  --location francecentral --template-file infra/main.bicep --parameters infra/main.bicepparam

targetScope = 'subscription'

@description('Region. The Azure for Students policy only allows these five.')
@allowed([
  'francecentral'
  'italynorth'
  'polandcentral'
  'austriaeast'
  'uaenorth'
])
param location string = 'francecentral'

@description('Globally unique app name. The site is served at https://<appName>.azurewebsites.net and the name cannot be changed later.')
@minLength(2)
@maxLength(60)
param appName string

@description('Email that receives the budget tripwire alerts.')
param alertEmail string

@description('Admin panel account. Must be listed in PORTFOLIO_ADMIN_EMAILS to reach /admin.')
param adminEmail string

@description('Display name of the admin account.')
param adminName string

@secure()
@description('Initial admin password. Only used when the database is seeded on first boot.')
param adminPassword string

@secure()
@description('Laravel APP_KEY ("base64:..."). Generate with `php artisan key:generate --show`.')
param appKey string

@description('Budget start, first day of a month (yyyy-MM-01). Fixed in main.bicepparam so redeploys never try to move it.')
param budgetStartDate string

var tags = {
  project: 'portfolio'
  costTarget: 'zero'
}

resource rg 'Microsoft.Resources/resourceGroups@2025-04-01' = {
  name: 'rg-portfolio'
  location: location
  tags: tags
}

module app 'app.bicep' = {
  name: 'portfolio-app'
  scope: rg
  params: {
    location: location
    appName: appName
    adminEmail: adminEmail
    adminName: adminName
    adminPassword: adminPassword
    appKey: appKey
    tags: tags
  }
}

// Subscription scope, not resource-group scope: the tripwire must also catch anything
// billable created outside rg-portfolio.
resource tripwire 'Microsoft.Consumption/budgets@2024-08-01' = {
  name: 'tripwire-1usd'
  properties: {
    category: 'Cost'
    amount: 1
    timeGrain: 'Monthly'
    timePeriod: {
      startDate: budgetStartDate
    }
    notifications: {
      // 10% of $1: any real charge at all, as soon as Cost Management records it (it lags ~8-24h).
      actualOverTenCents: {
        enabled: true
        operator: 'GreaterThanOrEqualTo'
        threshold: 10
        thresholdType: 'Actual'
        contactEmails: [
          alertEmail
        ]
      }
      // Azure projects the month will pass $1 — warns before the money is spent.
      forecastOverOneDollar: {
        enabled: true
        operator: 'GreaterThanOrEqualTo'
        threshold: 100
        thresholdType: 'Forecasted'
        contactEmails: [
          alertEmail
        ]
      }
    }
  }
}

output siteUrl string = app.outputs.siteUrl
output resourceGroup string = rg.name
