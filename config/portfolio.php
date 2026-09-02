<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin allowlist
    |--------------------------------------------------------------------------
    |
    | Only these email addresses may reach the Filament panel. A user row alone
    | is not enough — this is the second gate, so a stray or seeded account
    | can never administer the site.
    |
    */
    'admin_emails' => array_filter(array_map(
        'trim',
        explode(',', (string) env('PORTFOLIO_ADMIN_EMAILS', 'mostafkhalaf11@gmail.com')),
    )),

    'admin_name' => env('PORTFOLIO_ADMIN_NAME', 'Mostafa Khalaf Mohamed'),

    /*
    | Read by AdminUserSeeder only. Kept in .env (gitignored) so a real password
    | never lands in source control. Null in any environment that has not set it,
    | in which case the seeder skips rather than creating a guessable account.
    */
    'admin_password' => env('PORTFOLIO_ADMIN_PASSWORD'),
];
