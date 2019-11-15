<?php
	namespace Library\Validator\Filters;
  /*** class StriptagsFilter ***/
  
class StriptagsFilter extends AbstractFilter
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
	 * return $var after filter if is set or just $var without filter
	 */
    public function filter($var)
    {
    	if(is_set($var)) {
    		return strip_tags($var);
    	}
    	else return $var;
    }
    
}
