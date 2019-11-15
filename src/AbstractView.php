<?php
	namespace Library;
	
/*
Classe implementant une vue simple
*/
abstract class AbstractView extends ApplicationComponent
{
        protected $contentFile = '';
        protected $vars = array();
        protected $helpers;

		public function __construct()
		{
			$this->helpers = new \Library\Helper\Helpers;
		}
		
    	// render the view template
    	abstract public function getGeneratedView();

    	public function getGeneratedPage()
    	{
    		return $this->getGeneratedView();
    	}

    // set a new view template
        public function setContentFile($contentFile)
        {
            if (!is_string($contentFile) || empty($contentFile))
            {
                throw new \InvalidArgumentException('La vue spécifiée est invalide');
            }
            
            $this->contentFile = $contentFile;
        }
     
    	// get the view template
    	public function getContentFile()
    	{
    	    return $this->contentFile;
    	}
     
        public function addVar($var, $value)
        {
            if (!is_string($var) || is_numeric($var) || empty($var))
            {
                throw new \InvalidArgumentException('Le nom de la variable doit être une chaine de caractère non nulle');
            }
            
            $this->vars[$var] = $value;
        }
        
    	// get the specified property from the view
    	public function getVar($var)
    	{
        	if (!isset($this->vars[$var])) 
        	{
        	    throw new ViewException('Le nom de la variable demandée n\'existe pas dans cette vue.');     
        	}
        	return $this->vars[$var];
    	}

    	// remove the specified property from the view
    	public function unsetVar($var)
    	{
        	if (isset($this->vars[$var])) {
        	    unset($this->vars[$var]);
        	}
    	}   

		public function helpers()
		{
			return $this->helpers;
		}
		
}// End AbstractView class

class ViewException extends \Exception{}
