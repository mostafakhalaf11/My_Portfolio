// The web app itself: one Free (F1) Linux plan running PHP 8.4, plus the settings Laravel reads.
// Deployed by main.bicep into rg-portfolio; see main.bicep for the reasoning behind the $0 design.

param location string
param appName string
param adminEmail string
param adminName string

@secure()
param adminPassword string

@secure()
param appKey string

param tags object

// Absolute path on the persistent /home share, outside wwwroot: deployments replace wwwroot,
// so the database must live somewhere a deployment never touches.
var databasePath = '/home/data/portfolio.sqlite'

resource plan 'Microsoft.Web/serverfarms@2024-11-01' = {
  name: 'plan-${appName}'
  location: location
  tags: tags
  kind: 'linux'
  sku: {
    name: 'F1'
    tier: 'Free'
  }
  properties: {
    reserved: true // required for a Linux plan
  }
}

resource site 'Microsoft.Web/sites@2024-11-01' = {
  name: appName
  location: location
  tags: tags
  kind: 'app,linux'
  properties: {
    serverFarmId: plan.id
    httpsOnly: true
    clientAffinityEnabled: false // single instance; no sticky-session cookie needed
    siteConfig: {
      linuxFxVersion: 'PHP|8.4'
      // Points nginx at public/, migrates, seeds an empty database once, builds caches.
      appCommandLine: 'bash /home/site/wwwroot/deploy/azure/startup.sh'
      alwaysOn: false // not available on F1; the app sleeps after ~20 idle minutes
      ftpsState: 'Disabled'
      minTlsVersion: '1.2'
      http20Enabled: true
      appSettings: [
        { name: 'APP_NAME', value: 'Portfolio' }
        { name: 'APP_ENV', value: 'production' }
        { name: 'APP_DEBUG', value: 'false' }
        { name: 'APP_KEY', value: appKey }
        // Only the host lives here: changing to a custom domain later is this one setting.
        { name: 'APP_URL', value: 'https://${appName}.azurewebsites.net' }
        { name: 'LOG_CHANNEL', value: 'stderr' } // shows up in App Service log stream
        { name: 'LOG_LEVEL', value: 'warning' }
        { name: 'DB_CONNECTION', value: 'sqlite' }
        { name: 'DB_DATABASE', value: databasePath }
        { name: 'DB_BUSY_TIMEOUT', value: '5000' }
        // Cookie sessions keep public page views from writing to SQLite on every request.
        { name: 'SESSION_DRIVER', value: 'cookie' }
        { name: 'SESSION_SECURE_COOKIE', value: 'true' }
        { name: 'CACHE_STORE', value: 'file' }
        { name: 'QUEUE_CONNECTION', value: 'sync' }
        { name: 'MAIL_MAILER', value: 'log' }
        { name: 'PORTFOLIO_ADMIN_EMAILS', value: adminEmail }
        { name: 'PORTFOLIO_ADMIN_NAME', value: adminName }
        { name: 'PORTFOLIO_ADMIN_PASSWORD', value: adminPassword }
        // CI ships vendor/ and public/build/ ready-made; Azure must not rebuild them.
        { name: 'SCM_DO_BUILD_DURING_DEPLOYMENT', value: 'false' }
        { name: 'WEBSITES_ENABLE_APP_SERVICE_STORAGE', value: 'true' } // keep /home persistent
      ]
    }
  }
}

// GitHub Actions deploys with a publish profile, which needs SCM basic auth. A student tenant
// usually cannot create the app registration that OIDC would need. FTP stays off.
resource scmPublishing 'Microsoft.Web/sites/basicPublishingCredentialsPolicies@2024-11-01' = {
  parent: site
  name: 'scm'
  properties: {
    allow: true
  }
}

resource ftpPublishing 'Microsoft.Web/sites/basicPublishingCredentialsPolicies@2024-11-01' = {
  parent: site
  name: 'ftp'
  properties: {
    allow: false
  }
}

output siteUrl string = 'https://${site.properties.defaultHostName}'
