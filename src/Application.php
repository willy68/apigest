<?php
	namespace Library;

	abstract class Application extends Singleton
	{
		protected $config;
		protected $pluginsManager;
		protected $httpRequest;
		protected $httpResponse;
		protected $name;
		protected $user;

		protected function __construct()
		{
			$this->config = new Config;
			I18n::setLang('fr');
			I18n::addDir(__DIR__ . '/' . 'I18n');
			$this->pluginsManager = new PluginsManager;
			$this->httpRequest = new HTTPRequest;
			$this->httpResponse = new HTTPResponse;
			$this->user = new User;

			$this->name = '';
		
		}

		public function getController()
		{
			$module = 'Index';
			$action = 'index';

			$rewrite = $this->config->getIniVar('url', 'rewrite');
			if ($rewrite === null || $rewrite === '1') {
				$matchedRoute = $this->getRoute();
				$module = ucfirst($matchedRoute->module());
				$action = $matchedRoute->action();
			}
			else {
				//url sous la forme: Builder.php?module=index&action=index[&id=1]
				if ($this->httpRequest->getExists('module')) $module = ucfirst($this->httpRequest->getData('module'));
				if ($this->httpRequest->getExists('action')) $action = strtolower($this->httpRequest->getData('action'));
			}

			// On instancie le contrôleur
			$controllerClass = 'Applications\\'.$this->name.'\\Modules\\'.$module.'\\'.$module.'Controller';
			return new $controllerClass($module, $action);
		}

		public function getRoute(){
			$router = new \Library\Router;

			$xml = new \DOMDocument;
			$xml->load(__DIR__.'/../Applications/'.$this->name.'/Config/routes.xml');

			$routes = $xml->getElementsByTagName('route');

			// On parcoure les routes du fichier XML
			foreach ($routes as $route)
			{
				$vars = array();
				
				// On regarde si des variables sont présentes dans l'URL
				if ($route->hasAttribute('vars'))
				{
					$vars = explode(',', $route->getAttribute('vars'));
				}

				// On ajoute la route au routeur
				$router->addRoute(new Route($route->getAttribute('url'), $route->getAttribute('module'), $route->getAttribute('action'), $vars));
			}

			try
			{
				// On récupère la route correspondante à l'URL
				$matchedRoute = $router->getRoute($this->httpRequest->requestURI());
			}
			catch (\RuntimeException $e)
			{
				if ($e->getCode() == \Library\Router::NO_ROUTE)
				{
					// Si aucune route ne correspond, c'est que la page demandée n'existe pas
					$this->httpResponse->redirect404();
				}
			}

			// On ajoute les variables de l'URL au tableau $_GET
			$_GET = array_merge($_GET, $matchedRoute->vars());

			return $matchedRoute;
		}

		public function run()
		{
			$this->loadPlugins();
			$this->user->isAuthenticated();
			$this->execute();
			
		}

		abstract public function execute();

		public function loadPlugins()
		{
			if ($this->pluginsManager)
				$this->pluginsManager->loadPlugins();
		}

		public function config()
		{
			return $this->config;
		}

		public function pluginsManager()
		{
			return $this->pluginsManager;
		}

		public function httpRequest()
		{
			return $this->httpRequest;
		}

		public function httpResponse()
		{
			return $this->httpResponse;
		}

		public function name()
		{
			return $this->name;
		}

		public function user()
		{
			return $this->user;
		}
	}

