<?php
  /*** class NumericMaxRules ***/
  
class NumericMaxRules extends NumericRules
{
    /**
     *
     * @the constructor
     *
     */
    public function __construct(array $options('message' => 'Ce nombre ne peut dépasser', 'max' => PHP_INT_MAX))
    {
    	parent::__construct($options);
    }

	public function setMax($max)
	{
		$this->addValidation(new MaxValidation($max, $this->message));
		return $this;
	}

	/*
	* return $var after sanitize
	*/	
    public function sanitize($var)
    {
        return (int) filter_var($var, FILTER_SANITIZE_NUMBER_INT);
    }
    
} /*** end of class ***/
