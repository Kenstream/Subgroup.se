<?php

class Application_Form_User extends SG_Form
{

    public function __construct($entityManager, $params = array())
    {
        parent::__construct();
        $this->_em = $entityManager;
        $this->_params = $params;
    }

    public function init()
    {
        parent::init();

        $this->setLegend($this->_translator->_("Update user profile information"));

        $email = new SG_Form_Element_Text('email');
        $email->setLabel($this->_translator->_("E-mail address:"));
        $email->setRequired(true);
        $email->addValidator('emailAddress');

        $newPassword = new SG_Form_Element_Password('newPassword');
        $newPassword->setLabel($this->_translator->_("New password:"));
        $newPassword->setDescription($this->_translator->_("Fill in the password only if you want to change your current password"));

        $confirmPassword = new SG_Form_Element_Password('confirmPassword');
        $confirmPassword->setLabel($this->_translator->_("Confirm password:"));

        $firstName = new SG_Form_Element_Text('firstName');
        $firstName->setLabel($this->_translator->_("First name:"));
        $firstName->setRequired(true);
        $firstName->addValidator('Alpha');

        $lastName = new SG_Form_Element_Text('lastName');
        $lastName->setLabel($this->_translator->_("Last name:"));
        $lastName->setRequired(true);
        $lastName->addValidator('Alpha');

        $birthDate = new SG_Form_Element_Birthdate('birthDate');
        $birthDate->setLabel($this->_translator->_("Birth date:"));

        $submit = new SG_Form_Element_Submit("submit");
        $submit->helper = "formSubmitWithContainer";
        $submit->getView()->addHelperPath(APPLICATION_PATH.'/views/helpers/', 'SG_View_Helper');
        $submit->setLabel($this->_translator->_("Save"));
        $submit->setOrder(10);

        $this->addElements(array(
            $firstName, $lastName, $birthDate, $email, $newPassword, $confirmPassword, $submit
        ));
    }

}