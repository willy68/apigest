<?php
	namespace Library\Validator\Filters;
  /*** class AbstractFilter ***/
  
abstract class AbstractFilter
{

    /**
     *
     * @the constructor
     *
     */
    public function __construct()
    {
    }

	/**
	 *
	 * filter $var method
	 * return $var after filter
	 */
    abstract public function filter($var);

    /**
     *
     * @Check if POST variable is set
     *
     * @access private
     *
     * @param string $var The POST variable to check
     *
     */
    protected function is_set($var)
    {
		$check = true;
        if(!isset($var))
			$check = false;
        else if(!strlen($var))
        	$check = false;
        	
        return $check;
    }    
    
}
