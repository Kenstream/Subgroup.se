<?php

class SG_Form_Element_Checkbox extends Zend_Form_Element_Checkbox
{
    public function init()
    {
        $this->setOptions(array('disableLoadDefaultDecorators' => true));
        $this->setDecorators(array(
            array('ViewHelper'),
            array('Description', array('tag' => 'div', 'class' => 'alert alert-info', 'style' => 'margin-bottom:10px;')),
            array('Errors', array('class' => 'alert alert-error', 'style' => 'margin-left:0; list-style:none;')),
        ));
    }

}