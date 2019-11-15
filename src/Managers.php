<?php
	namespace Library;
	
	class Managers
	{
		protected $api = null;
		protected $dao = null;
		protected $managers = array();
		
		public function __construct($api = null, $dao = null)
		{
			$this->api = $api;
			$this->dao = $dao;
		}
		
		public function getManagerOf($module)
		{
			if (!is_string($module) || empty($module))
			{
				throw new InvalidArgumentException('Le module spécifié est invalide');
			}
			
			if (!isset($this->managers[$module]))
			{
				if ($this->dao == null || $this->api == null)
				{
					$connectCfg = Application::getInstance()->config()->getIniVar('connexion');
					if (!$connectCfg)
						throw new \RuntimeException('La configuration de connexion n\'a pas été définie dans le fichier app.ini');
					
					$this->api = $connectCfg['api'];
					
					$factory = '\\Library\\' . $this->api . 'Factory';
					$connectFunc = 'get' . ucfirst($connectCfg['sgdb']) . 'Connexion';
					$this->dao = $factory::$connectFunc($connectCfg['host'], $connectCfg['dbname'], 
														$connectCfg['user'], $connectCfg['password']);
					//$this->dao = PDOFactory::getMysqlConnexion();
				}
				$manager = '\Library\\Models\\'.$module.'Manager_'.$this->api;
				$this->managers[$module] = new $manager($this->dao);
			}
			
			return $this->managers[$module];
		}
	} 
