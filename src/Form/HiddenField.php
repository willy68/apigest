<?php
	namespace Library\Form;

	class HiddenField extends Field
	{

		public function buildWidget()
		{
			$widget = '';

			$widget .= '<input type="hidden" name="'.$this->name.'"';

			if (!empty($this->value) || is_numeric($this->value))
			{
				$widget .= ' value="'.htmlspecialchars($this->value).'"';
			}

			if (!empty($this->attributs))
			{
				$widget .= $this->renderAttributs();
			}

			return $widget .= ' />'."\n\t";
		}

	}

