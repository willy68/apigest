<?php
	namespace Library;
	
/*
Comportement composite des vues
*/
class View extends SingleView
{
	    protected $views = array();
        protected $layout;

		public function getView($viewName)
		{
			if (isset($this->views[$viewName])){
				return $this->views[$viewName];
			}
			return NULL;
		}
       
    // set a new view template
        public function setLayout($layout)
        {
            if (!is_string($layout) || empty($layout))
            {
                throw new \InvalidArgumentException('Le layout spécifié est invalide');
            }
            
            $this->layout = $layout;
        }
     
    	// get the view template
    	public function layout()
    	{
    	    return $this->layout;
    	}

	    // add a new view object
	    public function addView($viewName, AbstractView $view)
	    {
    	    if (!isset($this->views[$viewName])) {
        	    $this->views[$viewName] = $view; 
        	}
       		return $view;
    	}
   
    	// addViewAfter a view
    	public function addViewAfter($viewName, AbstractView $view, $viewNameAfter)
    	{
        	if (!isset($this->views[$viewName]) || isset($this->views[$viewNameAfter])) {
        	    $views = array();
        	    foreach ($this->views as $key => $_view) {
        	        if ($key !== $viewNameAfter) {
        	            $views[$key] = $_view;
        	        }
        	        else{
        	        	$views[$key] = $_view;
        	        	$views[$viewName] = $view;
        	        }
        	    }
        	    $this->views = $views;
        	}
       	    return $view;
    	}

    	// remove an existing view object
    	public function removeView($viewName)
    	{
        	if (isset($this->views[$viewName])) {
        	    $views = array();
        	    foreach ($this->views as $key => $_view) {
        	        if ($key !== $viewName) {
        	            $views[$key] = $_view;
        	        }
        	    }
        	    $this->views = $views;
        	}
       	    return $this;
    	}
   
	    // add a $var in all views
    	public function addVarForAll($var, $value)
    	{
        	if (!empty($this->views)) {
        	    foreach ($this->views as $view) {
        	    	if(is_callable(array($view,'addVarForAll'))){
        	    		$view->addVarForAll($var, $value);
        	    	}
        	    	else{
        	        	$view->addVar($var, $value);
        	        }
        	    }
        	}
        	$this->addVar($var, $value);
    	}

		//Add $var in one view
    	public function addVarInView($vars, $value, $viewName)
    	{
        	if (isset($this->views[$viewName])) {
        		$this->views[$viewName]->addVar($vars, $value);
        	}
    	}

    	// render each partial view (leaf) and optionally the composite view
    	public function getGeneratedView()
    	{
        	$innerView = '';
        	if (!empty($this->views)) {
        	    foreach ($this->views as $view) {
        	        $innerView .= $view->getGeneratedView();
        	    }
        	    $content = $innerView;
        	    $this->addVar('content', $content);
        	}
        	$compositeView = parent::getGeneratedView();   
        	return !empty($compositeView) ? $compositeView : $innerView;
    	}
    	
}// End View class
