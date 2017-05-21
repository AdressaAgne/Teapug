<?php

namespace App\Container\Controller;
use DB, Account, User, Config, Direct, View;

class Controller extends DB{
    
    public static $site_wide_vars = [
        'user' => null,
        'assets' => null,
        'source' => null,
        'global' => null,
    ];
    
    public $user;
    public $global;
    
    /**
     * This code runs with all controllers
     * @private
     * @author Agne *degaard
     */
    public function __construct(){
        parent::__construct();
        
        
        $source = $this->get_source();
        self::$site_wide_vars['source'] = $source;
        self::$site_wide_vars['assets'] = $source.'/assets';
        
        
        if(Account::isLoggedIn()){
            $this->user = new User($_SESSION['uuid']);
            self::$site_wide_vars['user'] = $this->user;
        }

        $globalController = "\\".Config::$controllers."GlobalController";
        if(function_exists($globalController)){
            $this->global = new $globalController($this);
            self::$site_wide_vars['global'] = $this->global;
        }
            
    }
    
    private function get_source(){
        $_GET['param'] = isset($_GET['param']) ? $_GET['param'] : '/';
        
        $source = str_replace($_GET['param'], '', $_SERVER['REQUEST_URI']);
        
        $source = '/'.trim($source, '/');
        
        return $source == '/' ? '' : $source;
    }
    
    
    public function __call($method, $params){
        die("Could not find method <b>$method</b> in <em>".static::class."</em>");
    }
    
    
}