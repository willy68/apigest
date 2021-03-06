<?php
	namespace Library\Validator\Filters;
  /*** class TrimFilter ***/
  
class EncryptFilter extends AbstractFilter
{

	const MD5 = 'MD5';
	
	const CUSTOM = 'CUSTOM';
	
	protected $method = self::MD5;
	
	protected $customMethod;

    public function __construct($method = self::MD5, $customName = null)
    {
    	$this->setMethod($method);
    	$this->setCustomMethod($customName);
    }

	public function setMethod($method)
	{
		if (is_string($method))
			$this->method = $method;
	}
	
	public function setCustomMethod($customName)
	{
		if (is_string($customName))
			$this->customMethod = $customName;
	}
	/**
	 *
	 * filter $var method
	 * return $var after filter if is set or just $var without filter
	 */
    public function filter($var)
    {
    	if ($this->is_set($var))
    	{
    		switch ($this->method)
    		{
    			case self::MD5:
    				return md5($var);    		
    		}
    	}
    	else return $var;
    }

}
