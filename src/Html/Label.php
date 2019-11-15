<?php
    namespace Library\Html;
    
    class Label extends HtmlElement
    {
        
        public function buildWidget()
        {
            $widget = '<label';
            
			if (!empty($this->attributs))
			{
				$widget .= $this->renderAttributs();
			}
            
			$widget .= '>';
			
            if (!empty($this->value))
            {
            	$widget .= $this->value;
            }
            
            return $widget .= '</label>'."\n\t";
        
        }
        
    }

