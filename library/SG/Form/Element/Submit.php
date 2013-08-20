<?php

class SG_Form_Element_Submit extends Zend_Form_Element_Submit
{
    public function init()
    {
        $this->setOptions(array('disableLoadDefaultDecorators' => true));
        $this->setAttrib('class', 'btn btn-primary');
        $this->addDecorators(array(
            array('ViewHelper'),
        ));
    }

}