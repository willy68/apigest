<?php
    namespace Library\Form;
    
    class PasswordField extends StringField
    {
        public function buildWidget()
        {
			$widget = '';
            if (!empty($this->label))
            {
            	$widget .= $this->label->render();
            }
            
            $widget .= '<input type="password" name="'.$this->name.'"';
            
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
        
    }

