<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    |
    | The name of the database table used to store configuration values.
    |
    */
    'table_name' => 'config_values',

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configuration caching settings. Values are cached to reduce database
    | queries. Set TTL to 0 to disable caching.
    |
    */
    'cache' => [
        'key' => 'system_config:all',
        'ttl' => 86400, // 24 hours in seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Page Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the system configuration page appearance and behavior.
    |
    */
    'page' => [
        'title' => 'System Configuration',
        'navigation_label' => 'Configuration',
        'navigation_group' => null, // Set to a string to group in navigation
        'navigation_sort' => 100,
        'slug' => 'system/configuration',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sidebar Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the sidebar navigation for configuration sections.
    | Define groups and their section keys to organize the sidebar.
    |
    */
    'sidebar' => [
        'title' => 'Configuration',
        'description' => 'System Settings',

        /*
        |--------------------------------------------------------------------------
        | Sidebar Groups
        |--------------------------------------------------------------------------
        |
        | Define how sections are grouped in the sidebar. Each group contains
        | an array of section keys. Leave empty to auto-generate from registered
        | sections.
        |
        | Example:
        | 'groups' => [
        |     'General' => ['general', 'design', 'currency'],
        |     'Sales' => ['sales', 'tax', 'shipping'],
        |     'Advanced' => ['features', 'system'],
        | ],
        |
        */
        'groups' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    |
    | Configure the permission names used for access control.
    | Set to false to disable permission checking.
    |
    */
    'permissions' => [
        'view' => 'view_system_configuration',
        'update' => 'update_system_configuration',
    ],

    /*
    |--------------------------------------------------------------------------
    | File Uploads
    |--------------------------------------------------------------------------
    |
    | Configure file upload settings for image fields.
    |
    */
    'uploads' => [
        'disk' => 'public',
        'directory' => 'config',
        'visibility' => 'public',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Configure notification messages.
    |
    */
    'notifications' => [
        'save' => [
            'title' => 'Configuration saved',
            'body' => 'Your settings have been saved successfully.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    |
    | Configure action labels and behavior.
    |
    */
    'actions' => [
        'save' => [
            'label' => 'Save Configuration',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Configuration Values
    |--------------------------------------------------------------------------
    |
    | These values are used as defaults when no configuration is found in the
    | database. Define defaults for your configuration paths here.
    |
    | Example:
    | 'defaults' => [
    |     'general/store_information/store_name' => 'My Store',
    |     'general/locale_options/currency' => 'USD',
    | ],
    |
    */
    'defaults' => [],

];
