<?php
  /*** class StringRules ***/
  
class FloatRules extends AbstractRules
{
    /**
     *
     * @the constructor
     *
     */
    public function __construct(array $options('message' => 'Nombre invalide'))
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
	        if(filter_var($var, FILTER_VALIDATE_FLOAT) === false)
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
        return filter_var($var, FILTER_SANITIZE_NUMBER_FLOAT);
    }
    
} /*** end of class ***/
