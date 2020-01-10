<?php
	namespace Library\Plugin;
	require_once __DIR__.'/../ActiveRecord/ActiveRecord.php';

	class PluginActiveRecord extends AbstractPlugin
	{
		public function __construct(array $cfg)
		{
			parent::__construct($cfg);
			$this->initialize();
		}

		public function initialize()
		{
			if ($this->initialized()) return true;

			if (isset($this->cfg['connections'])) {
				$connections = $this->cfg['connections'];
			}
			else {
				$connectCfg = \Library\Application::getInstance()->config()->getIniVar('connexion');
				$user = (isset($connectCfg['password']) && $connectCfg['password']) ? $connectCfg['user'].
														':'.$connectCfg['password'] : $connectCfg['user'];
				$charset = (isset($connectCfg['charset']) && $connectCfg['charset']) ? '?charset='.$connectCfg['charset'] 
																					: '?charset=utf8';

				$connections = array('development' => $connectCfg['sgdb'].'://'.$user.'@'
													.$connectCfg['host'].'/'.$connectCfg['dbname'].$charset);
			}
			if (isset($this->cfg['directories'])) {
				$directories = $this->cfg['directories'];
			}
			else {
				$directories = array(__DIR__.'/../../src/Models');
			}

			\ActiveRecord\Config::initialize(function($cfg) use ($connections, $directories){
				$cfg->set_model_directories($directories);
				$cfg->set_connections($connections);
			});
			return ($this->initialized = true);
		}

		public function getObject()
		{
			return $this;
		}

	}
