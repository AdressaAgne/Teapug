<?php
namespace App\Container;

use Render, Account, Direct, Controller, App\Plugins\TalePug\Compiler;

class View {

	public static function make($url, $vars = null){
		$url = preg_replace("/\\./uimx", "/", $url);
		$url = file_exists("view/{$url}.php") ? "view/{$url}.php" : "view/{$url}.pug";
		return self::includeFile($url, $vars);
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
			$info = pathinfo($filename);

			if(Config::$tale_pug){
				$code = shell_exec('php ../App/Plugins/Vendor/bin/tale-pug compile '.$filename.((Config::$tale_pug_pretty ? ' --pretty' : '')));
				if(preg_match('/^Fatal error/um', $code))
					dd('Pug Compiling Error:' . $code);
			} else {
				$code = Render::code(file_get_contents($filename));
			}

			ob_start();
				if(!is_null($vars)) extract($vars);
				if(!empty(Controller::$site_wide_vars)) extract(Controller::$site_wide_vars);
				eval("?>" . $code);

			return ob_get_clean();
		}

		return 'could not find: '.$filename;
	}

}
