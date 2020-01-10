<?php

namespace Applications\Frontend\Modules\User;

class CpvilleController extends \Applications\Frontend\Modules\ApiController
{

  public function beforeList(\Library\HTTPRequest $request)
  {

  }

  protected function getList(\Library\HTTPRequest $request)
  {
    $options = array();

    if ($request->getExists('limit')) {
      $options['limit'] = $request->getData('limit');
    }

    if ($request->getExists('order')) {
      $options['order'] = $request->getData('order');
    }

    try {
      if (!empty($options)) {
        $cpville = \Cpville::all($options);
      } else {
        $cpville = \Cpville::all();
      }
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Villes not found on this server');
      return;
    }

    if (empty($cpville)) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Aucunes villes trouvées sur ce serveur');
      return;
    }

    $json = $this->jsonArray($cpville);
    header('Content-Type: application/json; charset=UTF-8');
    $this->page->setOutput($json);
  }

  /**
   * Test si l'utilisateur peut créer un enregistrement de la table user
   * par la methode POST
   *
   * @param \Library\HTTPRequest $request
   * @return void
   */
  public function beforeCreate(\Library\HTTPRequest $request)
  {
  }

  protected function create(\Library\HTTPRequest $request)
  {
    $cpville = new \Cpville();

    $cpville->set_attributes(array(
      'CP' => $request->postData('CP'),
      'VILLE' => $request->postData('VILLE')
    ));

    if ($cpville->save()) {
      header('Content-Type: application/json; charset=UTF-8');
      $this->page->setOutput($cpville->to_json());
    } else {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('400 Bad request');
    }
  }

  public function beforeSearch(\Library\HTTPRequest $request)
  {

  }

  public function executeSearch(\Library\HTTPRequest $request)
  {
    $options = array();

    if ($request->getExists('limit')) {
      $options['limit'] = $request->getData('limit');
    }

    if ($request->getExists('order')) {
      $options['order'] = $request->getData('order');
    }

    if ($request->getExists('search')) {
      $options['conditions'] = array('CP = ?', '%'.$request->getExists('search').'%');
    }

    try {
      if (!empty($options)) {
        $cpville = \Cpville::all($options);
      } else {
        $cpville = \Cpville::all();
      }
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Villes not found on this server');
      return;
    }

    if (empty($cpville)) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Aucunes villes trouvées sur ce serveur');
      return;
    }

    $json = $this->jsonArray($cpville);
    header('Content-Type: application/json; charset=UTF-8');
    $this->page->setOutput($json);
  }

  protected function get(\Library\HTTPRequest $request)
  {
    try {
      $user = \User::find($request->getData('id'));
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('User not found on this server');
      return;
    }

    $json = $user->to_json();

    header('Content-Type: application/json; charset=UTF-8');
    $this->page->setOutput($json);
  }

  protected function update(\Library\HTTPRequest $request)
  {
    $id = $request->getData('id');

    try {
      $user = \User::find($id);
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('User not found on this server');
      return;
    }
    if ($user->update_attributes($request->post())) {
      header('Content-Type: application/json; charset=UTF-8');
      $this->page->setOutput($user->to_json());
    } else {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('400 Bad request');
    }
  }

  protected function delete(\Library\HTTPRequest $request)
  {
    $id = $request->getData('id');

    try {
      $user = \User::find($id);
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('User not found on this server');
      return;
    }

    if ($user->delete()) {
      header('Content-Type: application/json; charset=UTF-8');
      $this->page->setOutput($user->to_json());
    } else {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('400 Bad request');
    }
  }
}
