<?php

use App\Container\Config as Config;

// Start a session if it does not exist
if(!isset($_SESSION)) session_start();

// Generate a new PHP Session ID to prevent session hijacking.
session_regenerate_id();


/**
 * SPL autoloader, so we dont need to include files everywhere
 * @author Agne *degaard
 * @param function function($class)
 */
spl_autoload_register(function($class){
    $file = str_replace('\\', '/', "../{$class}.php");
    if(file_exists($file)){
        require_once($file);
    }
});

// Turn off error reporting when we are not in debug mode
if(!Config::$debug_mode) error_reporting(0);

// Setting up aliases
foreach(Config::$aliases as $key => $value){
    class_alias($key, $value);
}

// Define constants
foreach (Config::$constants as $key => $value) {
    define($key, $value);
}

function dd(...$param){
    // @: Header will output a notice if there is an error already, 
    // since we can not send the header after text has been sent.
    // So we remove all error logging from the header()
    @header('Content-type: application/json');
    die(print_r($param, true));
}

// Adding routing
require_once('../App/RouteSetup.php');

// require the application
require_once("../App/Container/App.php");

// Run App constrcut everything
new App();