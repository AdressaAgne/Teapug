<?php
namespace App\Container;

use Render, Account, Direct, Controller;

class View {

	public static function make($url, $vars = null){
		$url = preg_replace("/\\./uimx", "/", $url);

		return self::includeFile("view/{$url}.php", $vars);
	}

	public static function auth($url, $direct = '/login', $vars = null){
		if(Account::isLoggedIn()){
			return self::make($url, $vars);
		}
		return Direct::re($direct);
	}

	/**
	 * Return a php file in the view folder
	 * @param  string  $filename
	 * @param  array   [$vars         = null]
	 * @return string/boolean
	 */
	public static function includeFile($filename, $vars = null){

		if (is_file($filename)) {
			$code = Render::code(file_get_contents($filename));
			
			//die(print_r(htmlspecialchars($code), true));
			
			ob_start();
				if(!is_null($vars)) extract($vars);
				if(!empty(Controller::$site_wide_vars)) extract(Controller::$site_wide_vars);
				eval("?>" . $code);
			return ob_get_clean();
		}

		return 'could not find: '.$filename;
	}

}
