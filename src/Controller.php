<?php
	namespace Library;

	abstract class Controller extends ApplicationComponent
	{
		protected $action = '';
		protected $managers = null;
		protected $module = '';
		protected $page = null;
		protected $cache = null;
		protected $view = '';
		protected $excludeCacheViews = array();

		public function __construct($module, $action)
		{

			$this->managers = new Managers();

			$this->page = new Page;

			$this->setModule($module);
			$this->setAction($action);
			$this->setView($action);
			$this->setLayout();
		}

		public function execute()
		{
			$method = 'execute'.ucfirst($this->action);

			if (!is_callable(array($this, $method)))
			{
				throw new \RuntimeException('L\'action "'.$this->action.'" n\'est pas définie sur ce module');
			}

			if (is_callable(array($this, 'before')))
			{
				$this->before($this->app()->httpRequest());
			}

			// Cache
			if ((int)$this->app()->config()->getIniVar('cache', 'cache.enable') === 1)
			{
				if ($this->createCache() && $this->cache->cacheExists()) {
					if ($this->app()->user()->getAttribute('newid') === true) {
						$this->cache->cleanAll(__DIR__.'/../Applications/'.$this->app()->name().'/Cache/Views');
						$this->app()->user()->setAttribute('newid', false);
					}
					else if ($this->cache->checkLifetime()) {
						$this->page->setOutput($this->cache->read());
						return;
					}
				}
			}

			$beforeAction = 'before'.ucfirst($this->action);
			if (is_callable(array($this, $beforeAction)))
			{
				$this->$beforeAction($this->app()->httpRequest());
			}

			$this->$method($this->app()->httpRequest());

			$afterAction = 'after'.ucfirst($this->action);
			if (is_callable(array($this, $afterAction)))
			{
				$this->$afterAction($this->app()->httpRequest());
			}

			if (is_callable(array($this, 'after')))
			{
				$this->after($this->app()->httpRequest());
			}

			// Cache
			if ($this->cache) {
				$content = $this->page()->getGeneratedPage();
				$this->cache->setOutput($content)->save();
				$this->page->setOutput($content);
			}

		}

		abstract public function setLayout();

		public function excludeCacheView($view)
		{
			if (!is_string($view) || empty($view))
			{
				throw new \InvalidArgumentException('La vue doit être une chaine de caractères valide');
			}

			$this->excludeCacheViews[] = $view;
			return $this;
		}

		public function createCache()
		{
			if (!in_array($this->action, $this->excludeCacheViews))
			{
				$lifetime = (int)$this->app()->config()->getIniVar('cache', 'cache.lifetime');
				$file = __DIR__.'/../Applications/'.$this->app()->name().'/Cache/Views/'
						.ucfirst($this->app()->name()).ucfirst($this->module).ucfirst($this->action).
						implode('_', $this->app()->httpRequest()->get()).'.html';

				$this->cache = new Cache($file, $lifetime);
			}
			return $this->cache;
		}

		public function page()
		{
			return $this->page;
		}
		
		public function setModule($module)
		{
			if (!is_string($module) || empty($module))
			{
				throw new \InvalidArgumentException('Le module doit être une chaine de caractères valide');
			}

			$this->module = $module;
		}

		public function setAction($action)
		{
			if (!is_string($action) || empty($action))
			{
				throw new \InvalidArgumentException('L\'action doit être une chaine de caractères valide');
			}

			$this->action = $action;
		}

		public function setView($view)
		{
			if (!is_string($view) || empty($view))
			{
				throw new \InvalidArgumentException('La vue doit être une chaine de caractères valide');
			}

			$this->view = $view;

			$this->page->setContentFile(__DIR__.'/../Applications/'.$this->app()->name().'/Modules/'.$this->module.'/Views/'.$this->view.'.php');
		}
	} 
