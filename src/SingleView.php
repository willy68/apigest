<?php
	namespace Library;
	
/*
Comportement d'une vue simple
*/
    class SingleView extends AbstractView
    {
/*	    // add a new view (implemented by view subclasses)
    	public function addView($viewName, AbstractView $view)
    	{
    		throw new ViewException('A partial view cannot add another view.');
    	}
   
    	// addViewAfter a view (implemented by view subclasses)
    	public function addViewAfter($viewName, AbstractView $view, $viewNameAfter)
    	{
    		throw new ViewException('A partial view cannot add another view.');
    	}

    	// remove a view (implemented by view subclasses)
    	public function removeView($viewName)
    	{
    		throw new ViewException('A partial view cannot remove another view.');
    	}

	    // add a new view (implemented by view subclasses)
    	public function addVarForAll($vars, $value)
    	{
    		throw new ViewException('A partial view cannot add var for all views.');
    	}
    	
    	public function addVarInView($vars, $value, $viewName)
    	{
    		throw new ViewException('A partial view cannot add var in one view.');
    	}
    	*/   

    	// render the view template
    	public function getGeneratedView()
    	{
            if (!file_exists($this->contentFile))
            {
                throw new ViewException('La vue spécifiée n\'existe pas');
            }
            
            if($this->contentFile !==''){
        	   extract($this->vars);
        	   ob_start();
        	   require $this->contentFile;
        	   return ob_get_clean();
        	}
    	}
    	 	        
    } 
