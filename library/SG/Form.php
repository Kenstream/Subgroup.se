<?php

class SG_Form extends Zend_Form
{
    protected $_em;
    protected $_params;
    protected $_translator;

    public function init()
    {
        parent::init();

        $this->_translator = Zend_Registry::get('Zend_Translate');

        $this->setOptions(array('disableLoadDefaultDecorators' => true));
        $this->setDecorators(array(
            'FormElements',
            'Fieldset',
            'Form'
        ));
    }

}