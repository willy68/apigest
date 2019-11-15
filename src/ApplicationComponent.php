<?php
	namespace Library;

    abstract class ApplicationComponent
    {
        public function app()
        {
            return \Library\Application::getInstance();
        }
    }

