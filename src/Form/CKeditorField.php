<?php
    namespace Library\Form;
    
    class CKeditorField extends TextField
    {  
    	
    	protected $ckParam;
    	
    	public function renderJS()
    	{
    		$js = '<script type="text/javascript">'."\n\t";
			
			$js .= 'var editor = CKEDITOR.replace( \''.$this->name.'\'';
			
			if (!empty($this->ckParam))
			{
				$js .= ',{'.$this->ckParam.'}';
			}
			
			$js .= ' );</script>'."\n\t";
    		
    		return $js;
    	}
          
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
            
            $widget .= '</textarea>'."\n\t";
            
            $widget .= $this->renderJS();
            
            return $widget;
        }
        
        public function setCkParam($ckParam)
        {
        	if (is_string($ckParam))
        		$this->ckParam = $ckParam;
        }
        
    }

