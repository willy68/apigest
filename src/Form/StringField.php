<?php
    namespace Library\Form;
    
    class StringField extends Field
    {
        protected $maxLength;
        
        public function buildWidget()
        {
			$widget = '';
            
            if (!empty($this->label))
            {
            	$widget .= $this->label->render();
            }
            
            $widget .= '<input type="text" name="'.$this->name.'"';
            
            if (!empty($this->value) || is_numeric($this->value))
            {
                $widget .= ' value="'.htmlspecialchars($this->value).'"';
            }
            
            if (!empty($this->maxLength))
            {
                $widget .= ' maxlength="'.$this->maxLength.'"';
            }
			
			if (!empty($this->attributs))
			{
				$widget .= $this->renderAttributs();
			}
			
            $widget .= ' />'."\n\t";
			
			return $widget;
        }
        
        public function setMaxLength($maxLength)
        {
            $maxLength = (int) $maxLength;
            
            if ($maxLength > 0)
            {
                $this->maxLength = $maxLength;
            }
            else
            {
                throw new \RuntimeException('La longueur maximale doit être un nombre supérieur à 0');
            }
        }
    }

