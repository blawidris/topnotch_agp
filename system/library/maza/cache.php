<?php
/**
 * @package		Maza
 * @author		Jay Padaliya
 * @copyright           Copyright (c) 2021 Templatemaza.com
 * @license		per domain
 * @link		https://www.templatemaza.com
*/
namespace maza;
/**
* Cache class
*/
class Cache extends Library {
	private $adaptor;
    private $var = array(); // Cache variable
    private $canPageCache;// Allow route to cache
	
	/**
	 * Constructor
	 *
	 * @param string $adaptor The type of storage for the cache.
	 * @param int $expire Optional parameters
	 *
 	*/
	public function __construct($adaptor = 'file', $expire = 3600) {
        $class = 'Maza\\Cache\\' . $adaptor;

		if (class_exists($class)) {
			$this->adaptor = new $class($expire);
		} else {
			throw new \Exception('Error: Could not load cache adaptor ' . $adaptor . ' cache!');
		}
	}
        
	
    /**
     * Gets a cache by key name.
     * @param string $key The cache key name
     * @return string
     */
	public function get(string $key) {
        if($this->config->get('maza_cache_status')){
            return $this->adaptor->get($key);
        }
	}
	
    /**
     * Set a cache by key
     * @param string $key The cache key
     * @param mixed $value The cache value
     * @param mixed $expire expire timestamp
     * @return string
     */
	public function set(string $key, $value, $expire = true) {
        if($this->config->get('maza_cache_status') && $value){
		    return $this->adaptor->set($key, $value, $expire);
        }
	}
   
    /**
     * delete cache by key name
     * @param string $key The cache key
     */
	public function delete(string $key) {
		return $this->adaptor->delete($key);
	}
        
    /**
     * Clear cache
     * @param string $key The cache key
     */
	public function clear() {
		return $this->adaptor->clear();
	}
        
    /**
     * Is current page can cache
     */
    public function canPageCache(): bool {
        if(is_null($this->canPageCache)){
            $this->canPageCache = !$this->customer->isLogged() && !$this->cart->hasProducts();
        }
        return $this->canPageCache;
    }
    
    /**
     * get variable
     * @param string $key The variable name
     */
    public function getVar(string $key){
        if(isset($this->var[$key])){
            return $this->var[$key];
        }
    }
    
    /**
     * set variable
     * @param string $key The variable name
     * @param Mixed $value value
     */
    public function setVar(string $key, $value): void {
        $this->var[$key] = $value;
    }
    
    /**
     * delete variable
     * @param string $key The variable name
     */
    public function deleteVar(string $key): void {
        unset($this->var[$key]);
    }
    
    /**
     * check variable
     * @param string $key The variable name
     */
    public function isVar(string $key): bool{
        if(isset($this->var[$key])){
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Flush the expire cache
     */
    public function flush(){
        return $this->adaptor->flush();
    }
}
