<?php
  /*** class NumericRules ***/
  
class NumericRules extends AbstractRules
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
        if(is_numeric($var))
        {
			if(filter_var($var, FILTER_VALIDATE_INT) === false)
			{
				return false;			
				$this->errormsg = $this->message;
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
        return (int) filter_var($var, FILTER_SANITIZE_NUMBER_INT);
    }
    
} /*** end of class ***/
