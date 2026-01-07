<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Cache duration for user permissions in seconds.
    | In debug mode, defaults to 5 minutes. In production, defaults to 1 hour.
    |
    */
    'cache_time' => env('VUE_SIDEBAR_MENU_CACHE_TIME', null),

    /*
    |--------------------------------------------------------------------------
    | Member Model
    |--------------------------------------------------------------------------
    |
    | If your application has a Member model that should not have admin
    | permissions, specify it here. Set to null to disable this check.
    |
    */
    'member_model' => env('VUE_SIDEBAR_MENU_MEMBER_MODEL', null),

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The user model class name. This is used for favorite menu items.
    | Defaults to the Laravel auth provider user model.
    |
    */
    'user_model' => config('auth.providers.users.model', 'App\Models\User'),
];

