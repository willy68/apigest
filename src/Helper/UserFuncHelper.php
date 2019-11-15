<?php
	namespace Library\Helper;
	
	class UserFuncHelper extends Helper
	{
		const SET = 'SET';
		const PREPEND = 'PREPEND';
		const APPEND = 'APPEND';
		
		// tableau associatif au format array('funcName'=> array('userFuncName' => funcName, 'obj' => obj_proprietaire))
		protected $userFuncs = array();

		public function __construct()
		{
			$this->name = 'UserFuncHelper';
		}
		
		public function userFuncs()
		{
			return $this->userFuncs;
		}
		
		public function userFunc($userFunc = null)
		{
			if (null !== $userFunc && is_array($userFunc))
			{
				$this->addUserFunc($userFunc);
			}
		
		
			return $this;
		}
		
		// // tableau associatif au format array('userFuncName' => func, 'obj' => obj_proprietaire)		
		public function setUserFunc($userFunc = array())
		{
			if (!empty($userFunc))
			{
				if (array_key_exists('userFuncName',$userFunc) && array_key_exists('obj',$userFunc))
					$this->userFuncs = array($userFunc['userFuncName'] => $userFunc);
			}
			return $this;
		}
		
		public function addUserFunc($userFunc = array())
		{
			if (!empty($userFunc))
			{
				if (array_key_exists('userFuncName',$userFunc) && array_key_exists('obj',$userFunc))
					$this->userFuncs[$userFunc['userFuncName']] = $userFunc;
			}
			return $this;
		}
		
		public function getUserFunc($userFuncName)
		{
			if (!is_string($userFuncName))
			{
				throw new InvalidArgumentException('Le nom de la fonction spécifiée est invalide');
			}
			
			if (isset($this->userFuncs[$userFuncName]))
			{
				return $this->userFuncs[$userFuncName];			
			}
			return null;
		
		}

		public function __call($method, $args)
		{
			$userMethod = $this->getUserFunc($method);
			
			if (null !== $userMethod)
			{
        		return call_user_func_array(array($userMethod['obj'], $method),$args);
        	}
        		
        	return $this;
		}
		
	}
