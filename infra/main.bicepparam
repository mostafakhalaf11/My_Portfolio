using 'main.bicep'

param location = 'francecentral'
param appName = 'mostafa-khalaf'
param alertEmail = 'mostafkhalaf11@gmail.com'
param adminEmail = 'mostafkhalaf11@gmail.com'
param adminName = 'Mostafa Khalaf Mohamed'
param budgetStartDate = '2026-11-01'

// Secrets come from the shell, never from this file. Before deploying (PowerShell):
//   $env:PORTFOLIO_APP_KEY = (php artisan key:generate --show)
//   $env:PORTFOLIO_ADMIN_PASSWORD = '<a strong password>'
param appKey = readEnvironmentVariable('PORTFOLIO_APP_KEY')
param adminPassword = readEnvironmentVariable('PORTFOLIO_ADMIN_PASSWORD')
