<?php
namespace App\Container;

use Account, RouteHandler, Config, View, EventListener;
/**
*   Small Render Engine, very inspirated by Twig
*/
class Render {

    private static $functions = [];
    private static $regex = 'uimx';
    private $code = null;
    private $shortcuts = [
        'layout',
        'active_page',
        'isLoggedIn',
        'checked',
        'selected',
        'form',
        'formend',
        'format',
        'csrf',
        'sub'
    ];
    private $helpers = [
        'if',             //@if()         @endif
        'foreach',        //@foreach()    @endforeach
        'for',            //@for()        @endfor
        'while',          //@while()      @endwhile
    ];

    public function __construct($code){
        self::addFunction("Raw Output",     "{!([^\}\{]+)!}", "<?php echo $1 ?>");
        self::addFunction("Escaped Output", "{{([^\}\{]+)}}", "<?php echo htmlspecialchars($1, ENT_QUOTES, 'UTF-8') ?>");
        self::addFunction("shortcuts",      "@(".implode('|', $this->shortcuts)."){1}\\(([^\\)\\(]*)[\\)]","<?php Render::$1($2) ?>");
        self::addFunction("Helpers",        "@(".implode('|', $this->helpers)."){1}[\s]*\((.*)\)", "<?php $1($2) : ?>");
        self::addFunction("Helpers End",    "@end(".implode('|', $this->helpers)."){1}", "<?php end$1 ?>");
        self::addFunction("Else",           "@else", "<?php else : ?>");
        
        EventListener::call(E_RENDER, $this);
        
        $this->code = $this->render($code);
    }

    public static function code($code){
        return new Render($code);
    }

    public static function addFunction($name, $regex, $replacement){
        self::$functions[$name] = [
            'regex' => $regex,
            'replacement' => $replacement,
        ];
    }

    private function render($code){
        foreach(self::$functions as $key => $val){
             $code = preg_replace("/{$val['regex']}/{$this::$regex}", $val['replacement'], $code);
        }

        return $code;
    }

    public function __toString(){
        return $this->code;
    }

    public static function csrf(){
        echo $_SESSION['_token'];
    }

    // Render Functions, stuff you can use in the html @functionName

    /**
     * Universal function for layouts
     * @param string $page          php page inside the root view folder
     * @param array  [$vars         = null] variables to carrie over to file
     */
    public static function layout($page, $vars = null){
        echo View::includeFile('view/'.preg_replace("/\\./uimx", "/", $page).'.php', $vars);
    }

    /**
     * check if the page is active
     * @author Agne *degaard
     * @param string $page
     */
    public static function active_page($page){
        if(RouteHandler::page() == $page) {
            echo "nav__item--active";
        }
    }

    public static function isLoggedIn(){
        return Account::isLoggedIn();
    }

    public static function format($str){
        echo "<p>".preg_replace('/\\n/', '</p><p>', $str)."</p>";
    }

    public static function form($page = "", $method = "post", $attrs = null){
        $method = strtoupper($method);
        $token = $_SESSION['_token'];

        if($attrs != null){
            foreach($attrs as $key => &$value){
                $value = "$key='$value'";
            }
            $attrs = implode(' ', $attrs);
        } else {
            $attrs = '';
        }
        if($method == 'get'){
            echo "<form action='$page' method='GET' $attrs>";
        } else {
            echo "<form action='$page' method='POST' $attrs>";
        }
        echo "<input type='hidden' name='_method' value='$method' />";
        echo "<input type='hidden' name='_token' value='$token' />";

    }

    public static function formend(){
        echo "</form>";
    }

    public static function checked($i){
        if($i) echo 'checked';
    }
    public static function selected($i){
        if($i) echo 'selected';
    }
    
    public static function sub($msg, $sentences = 1){
    	echo implode(".", array_slice(explode('.', $msg), 0, $sentences));
    }    
}
