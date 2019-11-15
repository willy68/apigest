<?php
	namespace Applications\Frontend;

    class FrontendApplication extends \Library\Application
    {
        protected function __construct()
        {
            parent::__construct();
            
            $this->name = 'Frontend';
        }
        
        public function execute()
        {
            
            $controller = $this->getController();
            $controller->execute();
            
            $this->httpResponse->setPage($controller->page());
            $this->httpResponse->send();
        }
    } 
