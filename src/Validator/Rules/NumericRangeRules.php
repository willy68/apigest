<?php
  /*** class NumericRangeRules ***/
  
class NumericRangeRules extends NumericRules
{
    /**
     *
     * @the constructor
     *
     */
    public function __construct(array $options('message' => 'Ce nombre doit être compris entre 0 et '.PHP_INT_MAX, 'min' = 0, 'max' => PHP_INT_MAX))
    {
    	parent::__construct($options);
    }

	public function setMin($min)
	{
		$this->addValidation(new MinValidation($min, $this->message));
		return $this;
	}

	public function setMax($max)
	{
		$this->addValidation(new MinValidation($max, $this->message));
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
