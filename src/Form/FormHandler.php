<?php
    namespace Library\Form;
        
    class FormHandler
    {
        protected $form;
        protected $manager;
        protected $request;

        public function __construct(Form $form, \Library\Manager $manager, \Library\HTTPRequest $request)
        {
            $this->setForm($form);
            $this->setManager($manager);
            $this->setRequest($request);
        }
        
        public function process(array $source = null)
        {
            if($this->request->method() == 'POST' && $this->form->isValid($source))
            {
                $this->manager->save($this->form->record());
                
                return true;
            }

            return false;
        }
        
        public function setForm(Form $form)
        {
            $this->form = $form;
        }
        
        public function setManager(\Library\Manager $manager)
        {
            $this->manager = $manager;
        }
        
        public function setRequest(\Library\HTTPRequest $request)
        {
            $this->request = $request;
        }
    }

