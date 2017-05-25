<?php

namespace App\Container;


class Config {


    /**
    *   when true: No cache, adding /migrate and /route, missing controller checks
    */
    public static $debug_mode = true;

    /**
    *   Use Tale-Pug
    *   https://github.com/Talesoft/tale-pug
    */
    public static $tale_pug = true;
    public static $tale_pug_pretty = false;

    /**
    *   Database Connection
    */

    public static $host     = 'localhost';
    public static $database = 'test';
    public static $username = 'root';
    public static $password = 'root';

    /**
    *   CSRF token, this will be randomly generated for each session
    */

    public static $form_token = 'jlhkgfdlkshdjkskdfskjdhf';

    /**
    *   Chache
    */

    public static $cookie_time  = 86400 * 30;
    public static $cache_time   = 3600;
    public static $cache_folder = 'cache/';

    /**
    *   Do not change
    */

    public static $route  = '/';
    public static $source = '';

    /**
    *   File Uploading
    */

    public static $files    = [
        "original"          => "/public/images/original/",
        "compressed"        => "/public/images/compressed/",
        "compressedSize"    => 600,
        "compressedSize2"   => 1000,
    ];

    public static $theme = 'basic';

    /**
    *   Namespace for controllers
    */

    public static $controllers = '\App\Controllers\\';

    /**
    *   Class aliases
    */

    public static $aliases = [

        '\App\Container\App'                    => 'App',

        // Config
        '\App\Container\Config'                 => 'Config',

        // Database
        '\App\Container\Database\Database'      => 'DB',

        '\App\Container\Database\Row'           => 'Row',
        '\App\Container\Database\PID'           => 'PID',
        '\App\Container\Database\Integer'       => 'Integer',
        '\App\Container\Database\Varchar'       => 'Varchar',
        '\App\Container\Database\Boolean'       => 'Boolean',
        '\App\Container\Database\Timestamp'     => 'Timestamp',
        '\App\Container\Database\Migrations'    => 'Migrations',


        // Extension package
        '\App\Auth\Account'                     => 'Account',

        // Routing
        '\App\Container\View'                   => 'View',
        '\App\Container\Routing\Direct'         => 'Direct',
        '\App\Container\Routing\Route'          => 'Route',
        '\App\Container\Routing\RouteHandler'   => 'RouteHandler',
        '\App\Container\Render'                 => 'Render',

        // Helpres

        '\App\Container\Helpers\Uploader'       => 'Uploader',
        '\App\Container\Helpers\Compressor'     => 'Compressor',
        '\App\Container\Helpers\Sorting'        => 'Sorting',
        '\App\Container\Helpers\Cache'          => 'Cache',
        '\App\Container\Helpers\Request'        => 'Request',
        '\App\Container\Controller\Controller'  => 'Controller',
        '\App\Container\Helpers\Protocol'       => 'Protocol',
        '\App\Container\Helpers\EventListener'  => 'EventListener',

        // Interfaces

        'App\Container\Interfaces\ApiController'      => 'ApiController',
        'App\Container\Interfaces\Module'             => 'Module',
        'App\Container\Interfaces\StackController'    => 'StackController',
        'App\Container\Interfaces\NormalController'   => 'NormalController',

        // Traits

        'App\Container\Traits\IndexTrait'             => 'IndexTrait',
        'App\Container\Traits\MigrateTrait'           => 'MigrateTrait',

        // Modules
        '\App\Modules\User'                 => 'User',
        '\App\Modules\Category'             => 'Category',
        '\App\Modules\Image'                => 'Image',

    ];

    /**
     *  Constants
     */

    public static $constants = [

        // Methods

        'GET'      => 'GET',
        'POST'     => 'POST',
        'PUT'      => 'PUT',
        'PATCH'    => 'PATCH',
        'DELETE'   => 'DELETE',
        'ERROR'    => 'ERROR',

        // Events

        'E_AUTH' => 'event_authenticate',
        'E_AFTER' => 'event_afterController',
        'E_CACHE' => 'event_cache',
        'E_BEFORE' => 'event_beforeController',
        'E_RENDER' => 'event_rendercode',
        'E_LOGIN' => 'event_authlogin',
        'E_REGISTER' => 'event_authregister',

    ];
}
