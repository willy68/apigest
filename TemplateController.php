<?php
return '<?php
namespace Applications\\' . $app . '\Modules\\' . $model_class . ';

class ' . $model_class . 'Controller extends \Applications\\' . $app . '\Modules\ApiController
	{

		protected function getList(\Library\HTTPRequest $request)
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

			try {
        if (!empty($options)) {
          $' . $model_name . 's = \\' . $model_class . '::all($options);
        } else {
          $' . $model_name . 's = \\' . $model_class . '::all();
        }
			} catch (\ActiveRecord\RecordNotFound $e) {
				header(\'HTTP/1.1 404 Not Found\');
				$this->page->setOutput(\'User role not found on this server\');
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
      /*$user = \User::find_by_email(array( \'email\' => $request->postData(\'email\')));
      if ($user) {
          header(\'HTTP/1.1 403 Forbiden\');
          exit(\'Email \' . $request->postData(\'email\') . \' allready exists\');
      }*/

      $' . $model_name . ' = new \\' . $model_class . '();

			$' . $model_name . '->set_attributes(array(/*' . "\n"
	. $attributes . '*/
							));

			if ($' . $model_name . '->save())
			{
				header (\'Content-Type: application/json; charset=UTF-8\');
				$this->page->setOutput($' . $model_name . '->to_json());
			} else {
				header(\'HTTP/1.1 400 Bad request\');
				$this->page->setOutput(\'400 Bad request\');
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
			if ($' . $model_name . '->update_attributes($request->post()))
			{
				header ( \'Content-Type: application/json; charset=UTF-8\' );
				$this->page->setOutput($' . $model_name . '->to_json());
			} else {
				header(\'HTTP/1.1 400 Bad request\');
				$this->page->setOutput(\'400 Bad request\');
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
			
			if ($' . $model_name . '->delete()) {
				header ( \'Content-Type: application/json; charset=UTF-8\' );
				$this->page->setOutput($' . $model_name . '->to_json());
			} else {
				header(\'HTTP/1.1 400 Bad request\');
				$this->page->setOutput(\'400 Bad request\');
			}			
		}

	}
';
