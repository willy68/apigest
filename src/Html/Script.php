<?php
    namespace Library\Html;
    
    class Script extends HtmlElement
    {
        
        public function buildWidget()
        {
            $widget = '<script';
            
			if (!empty($this->attributs))
			{
				$widget .= $this->renderAttributs();
			}
			
			$widget .= ' >';

            if (!empty($this->value))
            {
            	$widget .= $this->value;
            }
            
            return $widget .= '</script>'."\n\t";        
        }
        
        public function setSrc($src)
        {
        	if (is_string($src))
        	{
        		$this->attributs['src'] = $src;
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

        public function setLanguage($language)
        {
        	if (is_string($language))
        	{
        		$this->attributs['language'] = $language;
        	}
        	return $this;
        }
        
        public function setCharset($charset)
        {
        	if (is_string($charset))
        	{
        		$this->attributs['charset'] = $charset;
        	}
        	return $this;
        }
    }

