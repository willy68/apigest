<?php
	namespace Library\Validator;

class Validation{

    /*
    * @errors array
    */
    public $errors = array();

    /*
    * @the validation rules array
    */
    protected $validation_rules = array();

    /*
     * @the sanitized values array
     */
    public $sanitized = array();
     
    /*
     * @the source 
     */
    private $source;

    /**
     *
     * @the constructor, duh!
     *
     */
    public function __construct($source = null)
    {
    	if($source)
    		$this->addSource($source);
    }

    /**
     *
     * @add the source
     *
     * @paccess public
     *
     * @param array $source
     *
     */
    public function addSource($source)
    {
        if( is_array($source))
        {
        	$this->source = $source;
        }
        /*** allow chaining ***/
        return $this;
    }

    /**
     *
     * @run the validation rules
     *
     * @access public
     *
     */
    public function isValid($source = null)
    {
    	if($source != null)
    		$this->addSource($source);
    	
    	$valid = true;
    		
        foreach( new ArrayIterator($this->validation_rules) as $var => $rule)
        {
        	if($rule->isValid($this->getVar($var))) {
        		$this->sanitized[$var] = $rule->sanitize($this->getVar($var));
        	}
        	else {
       			$this->errors[$var] = $rule->getErrorMsg();
       			$valid = false;
        	}
        }
        return $valid;
    }

    /**
     *
     * @add a rule to the validation rules array
     *
     * @access public
     *
     * @param string $varname The variable name
     *
     * @param string $rule a derived AbstractRules object for specific rules
     *
     */
    public function addRule($varname, AbstractRules $rule)
    {
        $this->validation_rules[$varname] = $rule;
        /*** allow chaining ***/
        return $this;
    }

    /**
     *
     * @add multiple rules to the validation rules array
     *
     * @access public
     *
     * @param array $rules_array The array of rules to add ex: v->addRules(array('date_field' => new DateRules(
     *												array('format' => 'd/m/Y', 'message' => 'Date invalide'))));
     *
     */
    public function AddRules(array $rules_array)
    {
        $this->validation_rules = array_merge($this->validation_rules, $rules_array);
        /*** allow chaining ***/
        return $this;
    }

    /**
     *
     * @Return true if errors exists or false pour une variable donnée
     *
     * @access public
     *
     **/
    public function errorExists($varname)
    {
    	return isset($this->errors[$varname]);
    }

    /**
     *
     * @Return errors if exists or null
     *
     * @access public
     *
     **/
    public function errorData($varname)
    {
    	return isset($this->errors[$varname]) ? $this->errors[$varname] : null;
    }

    /**
     *
     * @Return all errors array
     *
     * @access public
     *
     **/
    public function allError()
    {
    	return $this->errors;
    }

    /**
     *
     * @Return POST variable or null if isn't set
     *
     * @access private
     *
     * @param string $varname The POST variable name to return
     *
     */
    private function getVar($varname)
    {
    	return isset($this->source[$varname]) ? $this->source[$varname] : null;
    }

    /**
     *
     * @Return POST Sanitize variable or POST variable or null if isn't set
     *
     * @access public
     *
     * @param string $varname The POST variable name to return
     *
     */
    public function validData($varname)
    {
    	return isset($this->sanitized[$varname]) ? $this->sanitized[$varname] : 
    	(isset($this->source[$varname]) ? $this->source[$varname] : null);
    }

} /*** end of class ***/

