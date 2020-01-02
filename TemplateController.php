<?php
return '<?php
namespace Applications\\' . $app . '\Modules\\' . $model_class . ';

class ' . $model_class . 'Controller extends \Applications\\' . $app . '\Modules\ApiController
	{

		protected function getList(\Library\HTTPRequest $request)
		{
			$options = array();

			if ($request->getExists(\'entreprise_id\') && $request->getData(\'entreprise_id\')) {
			  $options[\'entreprise_id\'] = $request->getData(\'entreprise_id\');
			}

			if ($request->getExists(\'limit\')) {
				$options[\'limit\'] = $request->getData(\'limit\');
			}
			
			if ($request->getExists(\'order\')) {
				$options[\'order\'] = $request->getData(\'order\');
			}

			try {
        if (!empty($options)) {
          $' . $model_name . 's = \\' . $model_class . '::all($options);
        } else {
          $' . $model_name . 's = \\' . $model_class . '::all();
        }
			} catch (\ActiveRecord\RecordNotFound $e) {
				header(\'HTTP/1.1 404 Not Found\');
				$this->page->setOutput(\'' . $model_class . ' not found on this server\');
				return;
      } catch (\ActiveRecord\ActiveRecordException $e) {
        header(\'HTTP/1.1 400 Bad request\');
        $this->page->setOutput(\'Un problème est survenu, impossible d\'avoir la liste de '. $model_name .'\');
        return;
      }
  
			if (empty($' . $model_name . 's))
			{
				header(\'HTTP/1.1 404 Not Found\');
				$this->page->setOutput(\'Aucunes ' . $model_name . 's trouvées sur ce serveur\');
				return;
			}

      $json = $this->jsonArray($' . $model_name . 's);
			header ( \'Content-Type: application/json; charset=UTF-8\' );
			$this->page->setOutput($json);
		}

		protected function create(\Library\HTTPRequest $request)
		{
      /*$user = \\' . $model_class . '::find_by_email(array( \'email\' => $request->postData(\'email\')));
      if ($user) {
          header(\'HTTP/1.1 403 Forbiden\');
          exit(\'Email \' . $request->postData(\'email\') . \' allready exists\');
      }*/

      $' . $model_name . ' = new \\' . $model_class . '();

			$' . $model_name . '->set_attributes(array(/*' . "\n"
    . $attributes . '*/
							));

      try {
        if ($' . $model_name . '->save())
        {
          header (\'Content-Type: application/json; charset=UTF-8\');
          $this->page->setOutput($' . $model_name . '->to_json());
        } else {
          header(\'HTTP/1.1 400 Bad request\');
          $this->page->setOutput(\'400 Bad request\');
        }
      } catch (\ActiveRecord\ActiveRecordException $e) {
        header(\'HTTP/1.1 400 Bad request\');
        $this->page->setOutput(\'Un problème est survenu, impossible d\'enregistrer le ' . $model_name . '\');
      }
		}

		protected function get(\Library\HTTPRequest $request)
		{
			try {
				$' . $model_name . ' = \\' . $model_class . '::find($request->getData(\'id\'));
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header(\'HTTP/1.1 404 Not Found\');
				$this->page->setOutput(\'' . $model_class . ' not found on this server\');
				return;
      } catch (\ActiveRecord\ActiveRecordException $e) {
        header(\'HTTP/1.1 400 Bad request\');
        $this->page->setOutput(\'Un problème est survenu, impossible de récuperer le ' . $model_name . '\');
        return;
      }

			$json = $' . $model_name . '->to_json();

			header ( \'Content-Type: application/json; charset=UTF-8\' );
			$this->page->setOutput($json);
		}

		protected function update(\Library\HTTPRequest $request)
		{
			$id = $request->getData(\'id\');

			try {
				$' . $model_name . ' = \\' . $model_class . '::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header(\'HTTP/1.1 404 Not Found\');
				$this->page->setOutput(\'' . $model_class . ' not found on this server\');
				return;
      }
      
      try {
        if ($' . $model_name . '->update_attributes($request->post()))
        {
          header ( \'Content-Type: application/json; charset=UTF-8\' );
          $this->page->setOutput($' . $model_name . '->to_json());
        } else {
          header(\'HTTP/1.1 400 Bad request\');
          $this->page->setOutput(\'400 Bad request\');
        }
      } catch (\ActiveRecord\ActiveRecordException $e) {
        header(\'HTTP/1.1 400 Bad request\');
        $this->page->setOutput(\'Un problème est survenu, impossible de sauvegarder le ' . $model_name . '\');
      }
		}

		protected function delete(\Library\HTTPRequest $request)
		{
			$id = $request->getData(\'id\');

			try {
				$' . $model_name . ' = \\' . $model_class . '::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header(\'HTTP/1.1 404 Not Found\');
				$this->page->setOutput(\'' . $model_class . ' not found on this server\');
				return;
			}

      try {
        if ($' . $model_name . '->delete()) {
          header ( \'Content-Type: application/json; charset=UTF-8\' );
          $this->page->setOutput($' . $model_name . '->to_json());
        } else {
          header(\'HTTP/1.1 400 Bad request\');
          $this->page->setOutput(\'400 Bad request\');
        }
      } catch (\ActiveRecord\ActiveRecordException $e) {
        header(\'HTTP/1.1 400 Bad request\');
        $this->page->setOutput(\'Un problème est survenu, impossible de supprimer le ' . $model_name . '\');
      }
		}

	}
';
