<?php
  /*** class StringRules ***/
  
class UrlRules extends AbstractRules
{
    /**
     *
     * @the constructor
     *
     */
    public function __construct(array $options('message' => 'Url invalide'))
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
	        if(filter_var($var, FILTER_VALIDATE_URL) === false)
	        {
				$this->errormsg = $this->message;
				return false;
	        }
            return true;
        }
        return true;
	}

	/*
	* return $var after sanitize
	*/	
    public function sanitize($var)
    {
        return (string) filter_var($var,  FILTER_SANITIZE_URL);
    }
    
} /*** end of class ***/
