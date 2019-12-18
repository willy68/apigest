<?php
namespace Applications\Frontend\Modules\Entreprise;

class EntrepriseController extends \Applications\Frontend\Modules\ApiController
	{

		protected function getList(\Library\HTTPRequest $request)
		{
			$options = array();

			if ($request->getExists('user_id')) {
				$options['joins'] = array('administrateurs');
				$options['conditions'] = array("`administrateur`.user_id in (?)",
					array($request->getData('user_id')));
			}

			if ($request->getExists('limit')) {
				$options['limit'] = $request->getData('limit');
			}
			
			if ($request->getExists('order')) {
				$options['order'] = $request->getData('order');
			}

			try {
        if (!empty($options)) {
          $entreprises = \Entreprise::all($options);
        } else {
          $entreprises = \Entreprise::all();
        }
			} catch (\ActiveRecord\RecordNotFound $e) {
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Entreprise not found on this server');
				return;
			}

			if (empty($entreprises))
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Aucunes entreprises trouvées sur ce serveur');
				return;
			}

      $json = $this->jsonArray($entreprises);
			header ( 'Content-Type: application/json; charset=UTF-8' );
			$this->page->setOutput($json);
		}

		protected function create(\Library\HTTPRequest $request)
		{
			if (!$request->getExists('user_id')) {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('Aucun utilisateur spécifié pour créer l\'entreprise');
			}

      try {
          $entreprise = \Entreprise::find_by_siret(array( 'siret' => $request->postData('siret')));
          if ($entreprise) {
              header('HTTP/1.1 400 Bad request');
              exit('L\'entreprise ' . $request->postData('siret') . ' allready exists');
          }
      } catch (\ActiveRecord\RecordNotFound $e) {
			} catch (\Exception $e) {
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Un problème est survenu, accès à la base de donnée impossible');
				return;
			}

      $entreprise = new \Entreprise();

			$entreprise->set_attributes(array(
        'siret' => $request->postData('siret'),
        'nom' => $request->postData('nom'),
        'ape' => $request->postData('ape'),
        'tva_intracom' => $request->postData('tva_intracom'),
        'adresse' => $request->postData('adresse'),
        'suite_adresse' => $request->postData('suite_adresse'),
        'cp' => $request->postData('cp'),
        'ville' => $request->postData('ville'),
        'tel' => $request->postData('tel'),
        'portable' => $request->postData('portable'),
        'email' => $request->postData('email'),
        'regime_commercial' => $request->postData('regime_commercial'),
        'logo' => $request->postData('logo')
			));

      try {
          if ($entreprise->save()) {
              $admin = new \Administrateur();
              $admin->set_attributes(array(
                    'user_id' => $request->getData('user_id'),
                    'entreprise_id' => $entreprise->id
                ));
              $admin->save();
              header('Content-Type: application/json; charset=UTF-8');
              $this->page->setOutput($entreprise->to_json());
          } else {
              header('HTTP/1.1 400 Bad request');
              $this->page->setOutput('400 Bad request');
          }
      } catch (\Exception $e) {
        if (isset($entreprise->id)) {
          $entreprise->delete();
        }
        header('HTTP/1.1 400 Bad request');
        $this->page->setOutput('400 Bad request');
      }
		}

		protected function get(\Library\HTTPRequest $request)
		{
			try {
				$entreprise = \Entreprise::find($request->getData('id'));
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Entreprise not found on this server');
				return;
			}

			$json = $entreprise->to_json();

			header ( 'Content-Type: application/json; charset=UTF-8' );
			$this->page->setOutput($json);
		}

		protected function update(\Library\HTTPRequest $request)
		{
			$id = $request->getData('id');

			try {
				$entreprise = \Entreprise::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Entreprise not found on this server');
				return;
			}
			if ($entreprise->update_attributes($request->post()))
			{
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput($entreprise->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}
		}
		
		protected function delete(\Library\HTTPRequest $request)
		{
			$id = $request->getData('id');

			try {
				$entreprise = \Entreprise::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Entreprise not found on this server');
				return;
			}
			
			if ($entreprise->delete()) {
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput($entreprise->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}			
		}

	}
