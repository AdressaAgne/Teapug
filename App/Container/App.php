<?php
namespace App\Container;

use Cache, Config, Protocol, EventListener;

class App extends Routing\RouteHandler{

    public function __construct(){
        
        // CSRF token - Cross-site Request Forgery
        $this->set_csrf();
        
        $cache = new Cache();

        if($cache->has_cached_file()) {
            echo $cache->get_cached_file();
            return;
        }
        
        $page = $this->get_page_data();    
        if(gettype($page) == 'string'){
            if(EventListener::have(E_CACHE))
                $cache->cache_file($page);
            
            echo $page;
            return;
        }
        
        header('Content-type: application/json');
        echo json_encode($page, JSON_UNESCAPED_UNICODE);
        return;
        
    }
    
    private function set_csrf(){
        if (!isset($_SESSION['_token'])){
            $_SESSION['_token'] = uniqid();
            Config::$form_token = $_SESSION['_token'];
        }
    }
}
