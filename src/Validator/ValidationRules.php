<?php
namespace Library\Validator;

/*									 
['auteur' => 'required|max:50|min:3|filter:trim',
'email' => 'required|email|filter:trim',
'emailConfirm' => 'required|emailConfirm:email|filter:trim']
*/

class ValidationRules
{

	/*
	* @the error message
	*/
	protected $errormsg = array();

	protected $validationRules = array();
	
	protected $filterRules = array();
	
	protected $fieldName = '';

	public function __construct($rules = null, $fieldName = null)
	{
		if ( $rules !== null)
			$this->setRules($rules);

		if ($fieldName !== null)
			$this->setFieldName($fieldName);
	}
	
	public function setFieldName($fieldName)
	{
		if (is_string($fieldName) && !empty($fieldName)) {
			$this->fieldName = $fieldName;
		}
		return $this;
	}

	public function setRules($rules)
	{
		if (!is_string($rules) || empty($rules))
		{
			return $this;
		}

		$options = explode('|', $rules);
		
		foreach ($options as $option) {
			$rule = explode(':', $option);
			$className;
			$filter = false;
			foreach ($rule as $key => $value) {
				//If $key is 0 $value is the class name validation with no param
				if ($key === 0)
				{
					if (strtolower($value) === 'filter') {
						$filter = true;
					}
					else {
						$className = ucfirst($value).'Validation';
						$this->validationRules[$className] = '';
					}
				}
				//$key is associative string validation class name is in $key and array param is the value
				else
				{
					if ($filter) {
						$className = ucfirst($value).'Filter';
						$this->filterRules[$className] = '';
					}
					else {
						$this->validationRules[$className] = $value;
					}
				}
			}
		}
		return $this;
	}
	
	public function clean()
	{
		$this->fieldName = '';
		$this->validationRules = array();
		$this->filterRules = array();
		$this->errormsg = array();
		return $this;
	}

	public function isValid($var)
	{
		$valid = true;

		foreach ($this->filterRules as $key =>$param) {
			$className = '\\Library\\Validator\\Filters\\'.$key;
			$className = new $className();
			$var = $className->filter($var);
		}

		foreach ($this->validationRules as $key => $param)
		{
			$className = '\\Library\\Validator\\Validations\\'. $key;
			$validation = new $className();
			if (!empty($param))
			{
				$validation->parseParam($param);
			}
			if ($validation->isValid($var) === false) {
				$valid = false;
				$this->setErrorMsg($validation->getErrorMessage($this->fieldName, $var));
			}
		}	
		return $valid;
	}

	public function getErrorMsg($asArray = false)
	{
		if (!$asArray)
		{
			$error = '';
			$i = 0;
			foreach ($this->errormsg as $msg)
			{
				$error .= ($i > 0)? "\n\t".$msg : ''.$msg;
				$i++;
			}
			return $error;
		}
		else
		return $this->errormsg;
	}
	
	public function getFirstError()
	{
		if (!empty($this->errormsg))
			return $this->errormsg[0];
		return '';
	}
	
	public function setErrorMsg($errormsg)
	{
		if(is_string($errormsg))
			$this->errormsg[] = $errormsg;
		
		return $this;
	}
}
