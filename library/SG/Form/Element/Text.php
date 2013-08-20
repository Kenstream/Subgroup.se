<?php

class SG_Form_Element_Text extends Zend_Form_Element_Text
{
    public function init()
    {
        $this->setOptions(array('disableLoadDefaultDecorators' => true));
        $this->setAttrib('class', 'input-block-level');
        $this->setDecorators(array(
            array('Label'),
            array('ViewHelper'),
            array('Description', array('tag' => 'div', 'class' => 'alert alert-info', 'style' => 'margin-bottom:10px;')),
            array('Errors', array('class' => 'alert alert-error', 'style' => 'margin-left:0; list-style:none;')),
        ));
    }

}