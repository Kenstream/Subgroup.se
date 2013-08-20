<?php

class Application_Form_Login extends SG_Form
{
    public function init()
    {
        parent::init();

        $email = new SG_Form_Element_Text('email');
        $email->removeDecorator('Label');
        $email->setAttrib('placeholder', $this->_translator->_("E-mail address"));
        $email->addValidator('emailAddress');
        $email->setRequired(TRUE);

        $password = new SG_Form_Element_Password('password');
        $password->removeDecorator('Label');
        $password->setAttrib('placeholder', $this->_translator->_("Password"));
        $password->setRequired(TRUE);

        $submit = new SG_Form_Element_Submit('submit');
        $submit->setLabel($this->_translator->_("Sign in"));

        $this->addElements(array($email, $password, $submit));
    }
}

