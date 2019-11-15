<?php
	namespace Library;
	

	class PluginsManager extends ApplicationComponent
	{
		protected $pluginsCfg = array();
		protected $plugins = array();

		public function loadCfg()
		{
			$file = __DIR__.'/../Applications/'.$this->app()->name().'/Config/plugins.ini';
			$cfg = $this->app()->config()->parseIniFile($file);

			if($cfg) {
				$this->pluginsCfg = array_merge($this->pluginsCfg, $cfg);
			}
		}

		public function loadPlugins()
		{
			if(!$this->pluginsCfg)
				$this->loadCfg();

			if($this->pluginsCfg) {
				foreach($this->pluginsCfg as $plugin => $value) {
					if (!isset($this->plugins[$plugin]) && (int)$value['plugin.enable'] ===  1) {
						$pluginClass = '\\Library\\Plugin\\Plugin'.$plugin;
						$this->plugins[$plugin] = new $pluginClass($value);
					}
					//if ((int)$value['plugin.enable'] ===  1 && !$this->plugins[$plugin]->initialized()) {
					//	$this->plugins[$plugin]->initialize();
					//}
				}
			}
		}

		public function loadPlugin($plugin)
		{
			if (!is_string($plugin) || empty($plugin)) {
				throw new InvalidArgumentException('Le plugin spécifié est invalide');
			}

			if(!$this->pluginsCfg)
				$this->loadCfg();

			if (!isset($this->plugins[$plugin]) && isset($this->pluginsCfg[$plugin]) 
				&& $this->pluginsCfg[$plugin]['plugin.enable'] ===  1) {
					$pluginClass = '\\Library\\Plugin\\Plugin'.$plugin;
					$this->plugins[$plugin] = new $pluginClass($this->pluginsCfg[$plugin]);
					//$this->plugins[$plugin]->initialize();
			}
			else {
				$this->plugin[$plugin] = null;
			}

			return $this->plugins[$plugin];
		}

		public function getPlugin($plugin)
		{
			if (!is_string($plugin) || empty($plugin)) {
				throw new InvalidArgumentException('Le plugin spécifié est invalide');
			}

			if (!isset($this->plugins[$plugin])) {
				$this->loadPlugins();
			}
			return isset($this->plugins[$plugin]) ? $this->plugins[$plugin] : null;
		}

	}
