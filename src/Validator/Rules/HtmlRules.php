<?php
  /*** class StringRules ***/
  
class HtmltRules extends AbstractRules
{
    /**
     *
     * @the constructor
     *
     */
    public function __construct(array $options('message' => 'Chaine html invalide'))
    {
    	parent::__construct($options);
    }

	/*
	* return true ou false
	*/	
	public function validate($var)
	{
        if($this->is_set($var))
        {
            if(!is_string($var))
	        {
				$this->errormsg = $this->message;
				return false;
	        }
            return true;
        }
        return false;
	}

	/*
	* return $var after sanitize
	*/	
    public function sanitize($var)
    {
        return $var;
    }
    
} /*** end of class ***/
