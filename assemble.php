#!/usr/bin/php
<?php

// NOT COMPLETE JUST A TEST!!!!!

use \App\Container\Config as Config;

/**
 * SPL autoloader, so we dont need to include files everywhere
 * @author Agne *degaard
 * @param function function($class)
 */
spl_autoload_register(function($class){
    $file = str_replace('\\', '/', "{$class}.php");
    if(file_exists($file)){
        require_once($file);
    }
});

// Setting up aliases
foreach(Config::$aliases as $key => $value){
    class_alias($key, $value);
}

// Define constants
foreach (Config::$constants as $key => $value) {
    define($key, $value);
}

function dd(...$param){
    @header('Content-type: application/json');
    die(print_r($param, true));
}

array_shift($argv);

function makeDir($dir){
    if(!file_exists($dir)){
        mkdir($dir, 0777, true);
        print 'Making folder: '.$dir.PHP_EOL;
    }
}

function writeFile($file){
    $code = Render::code(file_get_contents($file));
    $name = $file;
    $folder = explode('/', $file);
    array_pop($folder);
    $folder = 'render/'.implode('/', $folder);
    makeDir($folder);
    $file = fopen('render/'.$file, 'w+');
    
    $w = fwrite($file, "<!--- Generated on ".date('H:i:s - d/m/y', time())." --->\n".$code);
    
    fclose($file);
    print 'File Generated as '.$name.PHP_EOL;
}

function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags); 
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}

$help = "MVC ASSEMBLER".PHP_EOL."
Usage:".PHP_EOL."
php assemble.php".PHP_EOL."
    - render [file optional]".PHP_EOL."
    - help".PHP_EOL."
".PHP_EOL;


if(!isset($argv[0])) die($help);

switch($argv[0]){
    case 'render':
        if(isset($argv[1])){
            writeFile($argv[1]);
        } else {
            foreach (rglob('public/view/*.php') as $key => $value) {
                writeFile($value);
            }
        }
    break;
    
    case 'help':
        print $help;
    break;
    
    default:
        print $argv[0].' is not a command'.PHP_EOL.$help;
    break;
}
    

    
    
