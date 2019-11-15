<?php
	namespace Library;

    class HTTPResponse extends ApplicationComponent
    {
        protected $page;
        
        public function addHeader($header)
        {
            header($header);
        }
        
        public function redirect($location)
        {
            header('Location: '.$location);
            exit;
        }
        
        public function redirect404()
        {
            $this->page = new Page;
			$this->page->setLayout(__DIR__.'/../Applications/'.$this->app()->name().'/Templates/layout.php');
            $this->page->setContentFile(__DIR__.'/../Errors/404.php');
            
            $this->addHeader('HTTP/1.1 404 Not Found');
            
            $this->send();
        }
        
        public function send()
        {
            exit($this->page->getGeneratedPage());
        }
        
        public function setPage(AbstractView $page)
        {
            $this->page = $page;
        }
        
        // Changement par rapport à la fonction setcookie() : le dernier argument est par défaut à true
        public function setCookie($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = false, $httpOnly = true)
        {
            setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
        }
    } 
