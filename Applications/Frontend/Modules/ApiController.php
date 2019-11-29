<?php
  namespace Applications\Frontend\Modules;

  /**
   * ApiController class
   */
  class ApiController extends \Applications\Frontend\BackController
  {
    /**
     * Undocumented variable
     *
     * @var boolean
     */
    protected $checkAuth = false;

    /**
     * Undocumented function
     *
     * @param [type] $check
     * @return void
     */
    public function setCheckAuth($check)
    {
      $this->checkAuth = $check;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function authenticated()
    {
      if (!$this->checkAuth) return;
      if (!$this->isAuthorized()) {
        header('HTTP/1.1 401 Unauthorized');
        exit('Utilisateur non authentifié');
      }
    }

    /**
     * Undocumented function
     *
     * @param [type] $records
     * @return void
     */
    public function jsonArray($records)
    {
      $i = 0;
      $json = null;
      foreach ($records as $record) {
        $js = $record->to_json();
        if ($i !== 0) {
            $json .= "," . $js;
        } else {
            $json = $js;
        }
        $i ++;
      }
      return $json;
    }

    /**
     * Undocumented function
     *
     * @param \Library\HTTPRequest $request
     * @return void
     */
    public function beforeList(\Library\HTTPRequest $request)
    {
        //Test if user can list users with token
        if ($this->method === 'GET') {
          $this->authenticated();
        }
    }

    /**
     * Undocumented function
     *
     * @param \Library\HTTPRequest $request
     * @return void
     */
    public function executeList(\Library\HTTPRequest $request)
    {
        if ($this->method === 'GET') {
            $this->getList($request);
        } elseif ($this->method === 'POST') {
            $this->create($request);
        }
    }

    /**
     * Undocumented function
     *
     * @param \Library\HTTPRequest $request
     * @return void
     */
    public function getList(\Library\HTTPRequest $request)
    {

    }

    /**
     * Undocumented function
     *
     * @param \Library\HTTPRequest $request
     * @return void
     */
    public function create(\Library\HTTPRequest $request)
    {

    }

    /**
     * Undocumented function
     *
     * @param \Library\HTTPRequest $request
     * @return void
     */
    public function beforeBy_id(\Library\HTTPRequest $request)
    {
        //Test if user can get, update or delete a record with token
        $this->authenticated();
    }

    /**
     * Undocumented function
     *
     * @param \Library\HTTPRequest $request
     * @return void
     */
    public function executeBy_id(\Library\HTTPRequest $request)
    {
        if ($this->method === 'GET') {
            $this->get($request);
        } elseif ($this->method === 'PUT') {
            $this->update($request);
        } elseif ($this->method === 'DELETE') {
            $this->delete($request);
        }
    }

    /**
     * Undocumented function
     *
     * @param \Library\HTTPRequest $request
     * @return void
     */
    public function get(\Library\HTTPRequest $request)
    {

    }

    /**
     * Undocumented function
     *
     * @param \Library\HTTPRequest $request
     * @return void
     */
    public function update(\Library\HTTPRequest $request)
    {

    }

    /**
     * Undocumented function
     *
     * @param \Library\HTTPRequest $request
     * @return void
     */
    public function delete(\Library\HTTPRequest $request)
    {

    }

  }