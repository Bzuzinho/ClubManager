<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backup Configuration
    |--------------------------------------------------------------------------
    */

    'backup' => [
        'name' => env('APP_NAME', 'ClubManager'),
        'source' => [
            'files' => [
                'include' => [
                    storage_path('app/documents'),
                    storage_path('app/public'),
                ],
                'exclude' => [
                    storage_path('app/temp'),
                    storage_path('framework/cache'),
                    storage_path('framework/sessions'),
                    storage_path('framework/views'),
                ],
                'follow_links' => false,
            ],
            'databases' => [
                env('DB_CONNECTION', 'mysql'),
            ],
        ],

        'destination' => [
            'filename_prefix' => '',
            'disks' => [
                env('BACKUP_DISK', 'local'),
            ],
        ],

        'temporary_directory' => storage_path('app/backup-temp'),
    ],

    'notifications' => [
        'notifications' => [
            \Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification::class => ['mail'],
        ],

        'notifiable' => \Spatie\Backup\Notifications\Notifiable::class,

        'mail' => [
            'to' => env('BACKUP_NOTIFICATION_EMAIL', 'admin@clubmanager.pt'),
            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'noreply@clubmanager.pt'),
                'name' => env('MAIL_FROM_NAME', 'ClubManager'),
            ],
        ],

        'slack' => [
            'webhook_url' => env('BACKUP_SLACK_WEBHOOK_URL', ''),
            'channel' => env('BACKUP_SLACK_CHANNEL'),
            'username' => env('BACKUP_SLACK_USERNAME'),
            'icon' => env('BACKUP_SLACK_ICON'),
        ],

        'discord' => [
            'webhook_url' => env('BACKUP_DISCORD_WEBHOOK_URL', ''),
            'username' => 'ClubManager Backup',
            'avatar_url' => '',
        ],
    ],

    'monitor_backups' => [
        [
            'name' => env('APP_NAME', 'ClubManager'),
            'disks' => [env('BACKUP_DISK', 'local')],
            'health_checks' => [
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 1,
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 5000,
            ],
        ],
    ],

    'cleanup' => [
        'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,
        'default_strategy' => [
            'keep_all_backups_for_days' => 7,
            'keep_daily_backups_for_days' => 16,
            'keep_weekly_backups_for_weeks' => 8,
            'keep_monthly_backups_for_months' => 4,
            'keep_yearly_backups_for_years' => 2,
            'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
        ],
    ],

];
