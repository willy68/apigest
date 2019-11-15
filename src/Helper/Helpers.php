<?php
	namespace Library\Helper;
	
	class Helpers
	{
		protected $helpers = array();
		
		public function addHelper($helper)
		{
			if ($helper instanceof Helper)
			{
				$this->helpers[$helper->name()] = $helper;
			}
			return $this;
		}
		
		public function getHelper($name)
		{
			if (!is_string($name))
			{
				throw new InvalidArgumentException('Le nom de l\'helper spécifié est invalide');
			}
			
			if (!isset($this->helpers[$name]))
			{
				$helper = '\Library\Helper\\'.$name;
				try
				{
					$obj = new $helper;
				}catch (\Exception $e)
				{
					return null;
				}
				$this->helpers[$name] = $obj;
			}
			return $this->helpers[$name];		
		}
		
		public function __call($method, $args)
		{
			$helper = $this->getHelper(ucfirst($method).'Helper');
			
			if (null !== $helper)
			{
        		return call_user_func_array(array($helper, $method),$args);
        	}
        		
        	return $this;
		}
	
	}
