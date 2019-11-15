<?php
	namespace Library\Validator\Filters;
  /*** class TrimFilter ***/
  
class TrimFilter extends AbstractFilter
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
    	if($this->is_set($var)) {
    		return trim($var);
    	}
    	else return $var;
    }

}
