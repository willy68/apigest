<?php
    namespace Library\Form;
        
    abstract class FormBuilder
    {
        protected $form;
        
        public function __construct(array $options = null, array $source = null)
        {
            $this->setForm(new Form($options, $source));
        }
        
        abstract public function build();
        
        public function setForm(Form $form)
        {
            $this->form = $form;
        }
        
        public function form()
        {
            return $this->form;
        }
    }

