<?php
namespace App\Container\Helpers;

class Request {
    
    private $vars = [];
    public $post = [];
    public $get = [];
    
    public function __construct($vars){
        $this->vars = $vars;
        
        if(isset($vars['post']['_token'])) unset($vars['post']['_token']);

        $this->post = (object) $vars['post'];
        $this->get = (object) $vars['get'];
    }
    
    public function url(){
        return $this->vars['without_vars'];
    }
    
    public function get_url(){
        return '/'.$this->vars['url'];
    }
    
    public function get_beardcrubs(){
        return explode('/', $this->url());
    }
    
    public function method(){
        return isset($this->vars['post']['_method']) ? strtolower($this->vars['post']['_method']) : 'get';
    }
    
}