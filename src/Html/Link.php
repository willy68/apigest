<?php
    namespace Library\Html;
    
    class Link extends HtmlElement
    {
		
		protected $attrKey = array('charset', 'href', 'hreflang', 'media', 'rel', 'type', 'title');
        
        public function buildWidget()
        {
            $widget = '<link';
            
			if (!empty($this->attributs))
			{
				$widget .= $this->renderAttributs();
			}
            
            return $widget .= ' />'."\n\t";        
        }
        
        public function setHref($href)
        {
        	if (is_string($href))
        	{
        		$this->attributs['href'] = $href;
        	}
        	return $this;
        }
        
        public function setRel($rel)
        {
        	if (is_string($rel))
        	{
        		$this->attributs['rel'] = $rel;
        	}
        	return $this;
        }
        
        public function setType($type)
        {
        	if (is_string($type))
        	{
        		$this->attributs['type'] = $type;
        	}
        	return $this;
		}        
    }

