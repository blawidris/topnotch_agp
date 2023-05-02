<?php
/**
 * @package		Maza
 * @author		Jay Padaliya
 * @copyright   Copyright (c) 2021, Templatemaza.com
 * @license		per domain
 * @link		https://www.templatemaza.com
*/
namespace maza;

use ScssPhp\ScssPhp\Compiler;
use Padaliyajay\PHPAutoprefixer\Autoprefixer;


/**
* Scss class
*/
class Scss extends Library{
	private $compiler;
        
    /**
	 * Constructor
	 *
 	*/
	public function __construct() {
        $this->compiler = new Compiler();
        
        $this->registerFunction();
	}
	
        
    /**
     * Get value of property
     * @param String $prop property name
     * @return Mixed value
     */
    public function __get($prop) {
            return $this->compiler->$prop;
    }
    
    /**
     * Set property value to compiler
     * @param String $prop property name
     * @param Mixed $value property value
     */
    public function __set($prop, $value) {
		$this->compiler->$prop = $value;
	}
        
    /**
     * Call methods of compiler
     * @param String $method method name
     * @param Array $arguments method arguments
     * @return Mixed return data of method
     */
    public function __call($method, $arguments) {
        return call_user_func_array(array($this->compiler, $method), $arguments);
    }
    
    /**
     * Compile SASS to css and autoprefix CSS vendor
     * @param String $sass sass code
     * @param Boolean $autoprefix css autoprefix
     * @return String CSS code
     */
    public function compile($sass, $autoprefix = true){
        $css = $this->compiler->compile($sass);
        
        if($autoprefix){
            $autoprefixer = new Autoprefixer($css);
            return $autoprefixer->compile();
        } else {
            return $css;
        }
        
    }
    
    /**
     * Register custom function to sass
     */
    private function registerFunction(){
        // Config
        $this->compiler->registerFunction('mz_config', $this->getCallable('maza\Registry::config'));
    
        // Skin config
        $this->compiler->registerFunction('mz_skin_config', $this->getCallable('maza\Registry::skin'));
        
        // Theme config
        $this->compiler->registerFunction('mz_theme_config', $this->getCallable('maza\Registry::theme'));
        
        // Get url extension
        $this->compiler->registerFunction('mz_getURLExtension', $this->getCallable(function($url){ return pathinfo($url, PATHINFO_EXTENSION); }));
    }
    
    /**
     * Create callable function for custom sass function
     */
    private function getCallable($callable){
        return function($args) use($callable) {
            if(isset($args[0][0]) && $args[0][0] == 'string' && !empty($args[0][2][0])){
                if(is_callable($callable)){
                    return $callable($args[0][2][0]);
                } else {
                    return call_user_func($name, $args[0][2][0]);
                }
            }
        };
    }
    
    /**
     * Convert PHP array to Scss variables
     * @param Array $data Array of variables
     * @return String Scss variables
     */
    public static function getVariableFormat($data){
        $sass_var = '';
        
        foreach($data as $name => $value){
            if ($name[0] === '$') {
                $name = substr($name, 1);
            }
            
            if(!in_array($value, [null, '', []], true)){
                $sass_var .= '$' . $name . ': ' . self::toSassType($value) . ';' . PHP_EOL;
            }
        }
        
        return $sass_var;
    }
    
    /**
     * Convert php data type to Sass type format string
     * @param Mixed $data PHP type
     * @return String Sass type
     */
    protected static function toSassType($data) {
        if(is_bool($data)) { // Boolean type
            return $data?'true':'false';
        } elseif(is_null($data)) { // Null type
            return 'null';
        } elseif(is_string($data)) { // string type
            return $data;
        } elseif(is_array($data)) { // array type
            $key_pair = array();
            foreach($data as $arr_key => $arr_val){
                $key_pair[] = $arr_key . ': ' . self::toSassType($arr_val);
            }
            return '(' . implode(', ', $key_pair) . ')';
        }
        
        return $data;
    }
        
}
