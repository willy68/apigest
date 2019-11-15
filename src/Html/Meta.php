<?php
    namespace Library\Html;
    
    class Meta extends HtmlElement
    {
        
        public function buildWidget()
        {
            $widget = '<meta';
            
			if (!empty($this->attributs))
			{
				$widget .= $this->renderAttributs();
			}
            
            return $widget .= ' />'."\n\t";        
        }
        
        public function setContent($content)
        {
        	if (is_string($content))
        	{
        		$this->attributs['content'] = $content;
        	}
        	return $this;
        }
        
        public function setName($name)
        {
        	if (is_string($name))
        	{
        		$this->attributs['name'] = $name;
        	}
        	return $this;
        }
        
    }

