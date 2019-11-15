<?php
	namespace Library\Validator\Rules;
  /*** class AbstractRules ***/

abstract class AbstractRules
{
    /*
    * @the validation rules array
    */
    protected $validation_rules = array();

    /*
    * @the filters array
    */
    protected $filter_rules = array();

    /*
    * @the error message
    */
    protected $errormsg = '';

    /*
    * @the error message
    */
    protected $message = '';

    /**
     *
     * @the constructor
     *
     */
    public function __construct(array $options = array())
    {
    	if(!empty($options))
    		$this->hydrate($options);
    }

	public function hydrate($options)
    {
    	foreach ($options as $type => $value)
        {
        	$method = 'set'.ucfirst($type);
                
            if (is_callable(array($this, $method)))
            {
            	$this->$method($value);
            }
        }
    }
    
	/*
	* specific validation ex: email numeric url etc...
	* return true ou false
	*/	
	abstract public function validate($var);

	/*
	* return true ou false
	*/	
	public function isValid($var)
	{
		/* common validation ex: min max required etc... */
		if(!empty($this->validation_rules)) {
			foreach($this->validation_rules as $validation) {
				if(!$validation->isValid($var)) {
					$this->errormsg = $validation->getErrorMsg();
					return false;
				}
			}
		}
		/* specific validation */
		if(!$this->validate($var))
			return false;
					
		return true;
	}
	
	/*
	* return $var after sanitize
	* specific sanitize
	*/	
    abstract public function sanitize($var);

	public function filter($var)
	{
		/* common filter ex: trim strip_tags etc...*/
		if(!empty($this->filter_rules)) {
			foreach($this->filter_rules as $filter) {
				$var = $filter->filter($var);
			}
		}
		/* specific sanitize*/		
		return $this->sanitize($var);	
	}
	
	public function addValidation(\Library\Validator\Validation\AbstractValidation $validation)
	{
		$this->validation_rules[] = $validation;
		return $this;
	}
	
	public function addFilter(\Library\Validator\Filters\AbstractFilter $filter)
	{
		$this->filter_rules[] = $filter;
		return $this;
	}
	
	public function setValidation(array $validations)
	{
		foreach ($validations as $validation)
            {
                if ($validation instanceof \Library\Validator\Validation\AbstractValidation && !in_array($validation, $this->validation_rules))
                {
                    $this->addValidation($validation);
                }
            }
		return $this;
	}
	
	public function setFilter(array $filters)
	{
		foreach ($filters as $filter)
            {
                if ($filter instanceof \Library\Validator\Filters\AbstractFilter && !in_array($filter, $this->filter_rules))
                {
                    $this->addFilter($filter);
                }
            }
		return $this;
	}

	public function setMessage($message)
	{
		if(is_string($message))
			$this->message = $message;
		return $this;
	}
	
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
        if(!isset($var))
			return false;
        else if(!strlen($var))
        	return false;
        	
        return true;
    }
    
	/*
	* return error message
	*/	
    public function getErrorMsg()
    {
    	return $this->errormsg;
    }

} /*** end of class ***/

