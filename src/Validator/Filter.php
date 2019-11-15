<?php
namespace Library\Validator;

/*
									//Filter class name => Without args array for class filter
	$options sous la forme: array( 'TrimFilter',
									//Filter class name => Some args array for class filter
								   'EncryptFilter' => array( 'method' => EncryptFilter::MD5)) 
									 


*/

class Filter
{

	protected $filterRules = array();

    public function __construct($options = array())
    {
	
		if (!empty($options))
		{
			$this->setRules($options);
		}
    }
    
	public function setRules(array $options)
	{
		foreach ($options as $key => $value)
		{
			//If $key is int, $value is the class name filter with no param
            if ($key === (int)$key)
			{
				$this->filterRules[ucfirst($value)] = array();
			}
			//$key is associative string filter class name is in $key and array param is the value
            elseif ($key === (string) $key && is_array($value))
            {
            	$this->filterRules[ucfirst($key)] = $value;
            }
		}
		return $this;
	}

    public function filter($var)
	{
		foreach ($this->filterRules as $key => $val)
		{
			$className = '\\Library\\Validator\\Filters\\'. $key;
			$filter = new $className();
			if (!empty($val))
			{
				$var = $filter->filter($val);
			}
		}	
		return $var;
	}
