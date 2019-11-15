<?php
    namespace Library\Form;
    
    class TextField extends Field
    {
        protected $cols;
        protected $rows;
        
        public function buildWidget()
        {
            $widget = '';
			
            if (!empty($this->label))
            {
            	$widget .= $this->label->render();
            }
            
            $widget .= '<textarea name="'.$this->name.'"';
            
            if (!empty($this->cols))
            {
                $widget .= ' cols="'.$this->cols.'"';
            }
            
            if (!empty($this->rows))
            {
                $widget .= ' rows="'.$this->rows.'"';
            }
            
			if (!empty($this->attributs))
			{
				$widget .= $this->renderAttributs();
			}

            $widget .= '>';
            
            if (!empty($this->value) || is_numeric($this->value))
            {
                $widget .= htmlspecialchars($this->value);
            }
            
            return $widget.'</textarea>'."\n\t";
        }
        
        public function setCols($cols)
        {
            $cols = (int) $cols;
            
            if ($cols > 0)
            {
                $this->cols = $cols;
            }
        }
        
        public function setRows($rows)
        {
            $rows = (int) $rows;
            
            if ($rows > 0)
            {
                $this->rows = $rows;
            }
        }
    }

