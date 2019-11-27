<?php
return '<?php
namespace Applications\\'.$app.'\Modules\\'.$model_class.';

class '.$model_class.'Controller extends \Applications\\'.$app.'\Modules\ApiController
	{

		private function getList(\Library\HTTPRequest $request)
		{
			$options = array();

			if ($request->getExists(\'id\')) {
			  $options[\'entreprise_id\'] = $request->getData(\'entreprise_id\');
			}

			if ($request->getExists(\'limit\')) {
				$options[\'limit\'] = $request->getData(\'limit\');
			}
			
			if ($request->getExists(\'order\')) {
				$options[\'order\'] = $request->getData(\'order\');
			}

			$'.$model_name.'s = \\'.$model_class.'::all($options);

			if (empty($'.$model_name.'s))
			{
				header(\'HTTP/1.1 404 Not Found\');
				$this->page->setOutput(\'Aucunes '.$model_name.'s trouvées sur ce serveur\');
				return;
			}

      $json = $this->jsonArray($'.$model_name.'s);
			header ( \'Content-Type: application/json; charset=UTF-8\' );
			$this->page->setOutput(\'[\' . $json . \']\');
		}

		private function create(\Library\HTTPRequest $request)
		{
      /*$user = \User::find_by_email(array( \'email\' => $request->postData(\'email\')));
      if ($user) {
          header(\'HTTP/1.1 403 Forbiden\');
          exit(\'Email \' . $request->postData(\'email\') . \' allready exists\');
      }*/

      $'.$model_name.' = new \\'.$model_class.'();

			$'.$model_name.'->set_attributes(array(\'siret\' => $request->postData(\'siret\'),
								\'nom\' => $request->postData(\'nom\'),
								\'ape\' => $request->postData(\'ape\'),
								\'tva_intracom\' => $request->postData(\'tva_intracom\'),
								\'adresse\' => $request->postData(\'role\'),
								\'suite_adresse\' => $request->postData(\'suite_adresse\'),
								\'cp\' => $request->postData(\'cp\'),
								\'ville\' => $request->postData(\'ville\'),
								\'tel\' => $request->postData(\'tel\'),
								\'portable\' => $request->postData(\'portable\'),
								\'email\' => $request->postData(\'email\'),
								\'regime_commercial\' => $request->postData(\'regime_commercial\')
							));

			if ($'.$model_name.'->save())
			{
				header (\'Content-Type: application/json; charset=UTF-8\');
				$this->page->setOutput($'.$model_name.'->to_json());
			} else {
				header(\'HTTP/1.1 400 Bad request\');
				$this->page->setOutput(\'400 Bad request\');
			}
		}

		private function get(\Library\HTTPRequest $request)
		{
			try {
				$'.$model_name.' = \\'.$model_class.'::find($request->getData(\'id\'));
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header(\'HTTP/1.1 404 Not Found\');
				$this->page->setOutput(\''.$model_class.' not found on this server\');
				return;
			}

			$json = $'.$model_name.'->to_json();

			header ( \'Content-Type: application/json; charset=UTF-8\' );
			$this->page->setOutput($json);

		}

		private function update(\Library\HTTPRequest $request)
		{
			$id = $request->getData(\'id\');

			try {
				$'.$model_name.' = \\'.$model_class.'::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header(\'HTTP/1.1 404 Not Found\');
				$this->page->setOutput(\''.$model_class.' not found on this server\');
				return;
			}
			if ($'.$model_name.'->update_attributes($request->post()))
			{
				header ( \'Content-Type: application/json; charset=UTF-8\' );
				$this->page->setOutput($'.$model_name.'->to_json());
			} else {
				header(\'HTTP/1.1 400 Bad request\');
				$this->page->setOutput(\'400 Bad request\');
			}
			
		}
		
		private function delete(\Library\HTTPRequest $request)
		{
			$id = $request->getData(\'id\');

			try {
				$'.$model_name.' = \\'.$model_class.'::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header(\'HTTP/1.1 404 Not Found\');
				$this->page->setOutput(\''.$model_class.' not found on this server\');
				return;
			}
			
			if ($'.$model_name.'->delete()) {
				header ( \'Content-Type: application/json; charset=UTF-8\' );
				$this->page->setOutput($'.$model_name.'->to_json());
			} else {
				header(\'HTTP/1.1 400 Bad request\');
				$this->page->setOutput(\'400 Bad request\');
			}			
		}

	}
';
