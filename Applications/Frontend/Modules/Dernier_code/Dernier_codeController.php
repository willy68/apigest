<?php

namespace Applications\Frontend\Modules\Dernier_code;

class Dernier_codeController extends \Applications\Frontend\Modules\ApiController
{

  protected function getList(\Library\HTTPRequest $request)
  {
    $options = array();

    if ($request->getExists('entreprise_id') && $request->getData('entreprise_id')) {
      $options['conditions'] = array('entreprise_id = ?', $request->getData('entreprise_id'));
    }

    if ($request->getExists('limit')) {
      $options['limit'] = $request->getData('limit');
    }

    if ($request->getExists('order')) {
      $options['order'] = $request->getData('order');
    }

    try {
      if (!empty($options)) {
        $dernier_codes = \Dernier_code::all($options);
      } else {
        $dernier_codes = \Dernier_code::all();
      }
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('User role not found on this server');
      return;
    }

    if (empty($dernier_codes)) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Aucunes dernier_codes trouvées sur ce serveur');
      return;
    }

    $json = $this->jsonArray($dernier_codes);
    header('Content-Type: application/json; charset=UTF-8');
    $this->page->setOutput($json);
  }

  protected function create(\Library\HTTPRequest $request)
  {
    /*$user = \User::find_by_email(array( 'email' => $request->postData('email')));
      if ($user) {
          header('HTTP/1.1 403 Forbiden');
          exit('Email ' . $request->postData('email') . ' allready exists');
      }*/

    $dernier_code = new \Dernier_code();

    $dernier_code->set_attributes(array(
      'table_nom' => $request->postData('table_nom'),
      'colonne' => $request->postData('colonne'),
      'code_table' => $request->postData('code_table'),
      'prochain_code' => $request->postData('prochain_code')
    ));

    if ($dernier_code->save()) {
      header('Content-Type: application/json; charset=UTF-8');
      $this->page->setOutput($dernier_code->to_json());
    } else {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('400 Bad request');
    }
  }

  protected function get(\Library\HTTPRequest $request)
  {
    try {
      $dernier_code = \Dernier_code::find($request->getData('id'));
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Dernier_code not found on this server');
      return;
    }

    $json = $dernier_code->to_json();

    header('Content-Type: application/json; charset=UTF-8');
    $this->page->setOutput($json);
  }

  protected function getLastbytablenom(\Library\HTTPRequest $request)
  {
    if (!$request->getExists('table_nom') && !$request->getData('table_nom')) {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('No table name specified, bad request');
    }

    $options = array();

    if ($request->getExists('entreprise_id') && $request->getData('entreprise_id')) {
      $options['conditions'] = array(
        'entreprise_id = ? AND table_nom = ?',
        $request->getData('entreprise_id'), $request->getData('table_nom')
      );
    } else {
      $options['conditions'] = array('table_nom = ?', $request->getData('table_nom'));
    }

    try {
      $dernier_code = \Dernier_code::last($options);
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Dernier_code for this table name not found on this server');
      return;
    }

    $json = $dernier_code->to_json();

    header('Content-Type: application/json; charset=UTF-8');
    $this->page->setOutput($json);
  }

  protected function update(\Library\HTTPRequest $request)
  {
    $id = $request->getData('id');

    try {
      $dernier_code = \Dernier_code::find($id);
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Dernier_code not found on this server');
      return;
    }
    if ($dernier_code->update_attributes($request->post())) {
      header('Content-Type: application/json; charset=UTF-8');
      $this->page->setOutput($dernier_code->to_json());
    } else {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('400 Bad request');
    }
  }

  protected function delete(\Library\HTTPRequest $request)
  {
    $id = $request->getData('id');

    try {
      $dernier_code = \Dernier_code::find($id);
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Dernier_code not found on this server');
      return;
    }

    if ($dernier_code->delete()) {
      header('Content-Type: application/json; charset=UTF-8');
      $this->page->setOutput($dernier_code->to_json());
    } else {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('400 Bad request');
    }
  }
}
