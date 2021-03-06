<?php

namespace Applications\Frontend\Modules\Adresse;

class AdresseController extends \Applications\Frontend\Modules\ApiController
{

  protected function getList(\Library\HTTPRequest $request)
  {
    $options = array();
    $conditions = array();

    if ($request->getExists('client_id') && $request->getData('client_id')) {
      $conditions = array('client_id = ?', $request->getData('client_id'));
    }

    if ($request->getExists('adresse_type_id') && $request->getExists('adresse_type_id')) {
      if (!empty($conditions)) {
        $conditions = array(
          'client_id = ? AND adresse_type_id = ?',
          $request->getData('client_id'), $request->getData('adresse_type_id')
        );
      } else {
        $conditions = array('adresse_type_id = ?', $request->getData('adresse_type_id'));
      }
    }

    if (!empty($conditions)) {
      $options['conditions'] = $conditions;
    }

    if ($request->getExists('limit')) {
      $options['limit'] = $request->getData('limit');
    }

    if ($request->getExists('order')) {
      $options['order'] = $request->getData('order');
    }

    try {
      if (!empty($options)) {
        $adresses = \Adresse::all($options);
      } else {
        $adresses = \Adresse::all();
      }
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Adresse not found on this server');
      return;
    } catch (\ActiveRecord\ActiveRecordException $e) {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('Un problème est survenu, impossible de récuperer la liste d\'adresses');
      return;
    }

    if (empty($adresses)) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Aucunes adresses trouvées sur ce serveur');
      return;
    }

    $json = $this->jsonArray($adresses);
    header('Content-Type: application/json; charset=UTF-8');
    $this->page->setOutput($json);
  }

  protected function create(\Library\HTTPRequest $request)
  {
    if (!$request->postExists('client_id') && !$request->postData('client_id')) {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('Il faut specifier l\'id du client, opération impossible');
    }

    if (!$request->postExists('adresse_type_id') && !$request->postData('adresse_type_id')) {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('Il faut specifier l\'id du type d\'adresse, opération impossible');
    }

    $adresse = new \Adresse();

    $adresse->set_attributes(array(
      'client_id' => $request->postData('client_id'),
      'adresse_1' => $request->postData('adresse_1'),
      'adresse_2' => $request->postData('adresse_2'),
      'adresse_3' => $request->postData('adresse_3'),
      'cp' => $request->postData('cp'),
      'ville' => $request->postData('ville'),
      'pays' => $request->postData('pays'),
      'adresse_type_id' => $request->postData('adresse_type_id')
    ));

    try {
      if ($adresse->save()) {
        header('Content-Type: application/json; charset=UTF-8');
        $this->page->setOutput($adresse->to_json());
      } else {
        header('HTTP/1.1 400 Bad request');
        $this->page->setOutput('400 Bad request');
      }
    } catch (\ActiveRecord\ActiveRecordException $e) {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('Un problème est survenu, impossible d\'enregistrer l\'adresse');
    }
  }

  protected function get(\Library\HTTPRequest $request)
  {
    try {
      $adresse = \Adresse::find($request->getData('id'));
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Adresse not found on this server');
      return;
    } catch (\ActiveRecord\ActiveRecordException $e) {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('Un problème est survenu, impossible de récuperer l\'adresse');
      return;
    }

    $json = $adresse->to_json();

    header('Content-Type: application/json; charset=UTF-8');
    $this->page->setOutput($json);
  }

  protected function update(\Library\HTTPRequest $request)
  {
    $id = $request->postData('id');

    try {
      $adresse = \Adresse::find($id);
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Adresse not found on this server');
      return;
    }

    try {
      if ($adresse->update_attributes($request->post())) {
        header('Content-Type: application/json; charset=UTF-8');
        $this->page->setOutput($adresse->to_json());
      } else {
        header('HTTP/1.1 400 Bad request');
        $this->page->setOutput('400 Bad request');
      }
    } catch (\ActiveRecord\ActiveRecordException $e) {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('Impossible de sauvegarder l\'adresse!');
    }
  }

  protected function delete(\Library\HTTPRequest $request)
  {
    $id = $request->getData('id');

    try {
      $adresse = \Adresse::find($id);
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Adresse not found on this server');
      return;
    }

    try {
      if ($adresse->delete()) {
        header('Content-Type: application/json; charset=UTF-8');
        $this->page->setOutput($adresse->to_json());
      } else {
        header('HTTP/1.1 400 Bad request');
        $this->page->setOutput('400 Bad request');
      }
    } catch (\ActiveRecord\ActiveRecordException $e) {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('Impossible de supprimer l\'adresse!');
    }
  }
}
