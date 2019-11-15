<?php
	namespace Library;
	
/*
Comportement semi-composite: 
Un layout auquel est integré plusieurs vues dans la variable tableau $views utilisées dans le layout
et un contenu dans la variable $content du layout s'il existe
*/
    class Page extends View
    {
		protected $output = null;

		public function setOutput($output)
		{

			$this->output = $output;
		}

    	// render the view template
    	public function getGeneratedView()
    	{
			if ($this->output !== null) return $this->output;

            if (!file_exists($this->contentFile))
            {
                throw new ViewException('La vue spécifiée n\'existe pas');
            }
            
            $user = $this->app()->user();

        	if (!empty($this->views)) {
				extract($this->views);
			}
			extract($this->vars);
			ob_start();
			require $this->contentFile;
			$content = ob_get_clean();
        	
        	if(!empty($this->layout)){
        		if(!file_exists($this->layout))
        		{
        			throw new ViewException('Le layout spécifié n\'existe pas');
        		}
            	ob_start();
                require $this->layout;
				$output = ob_get_clean();
            	return $output;
            }
            
            return $content;
    	}

    } 
