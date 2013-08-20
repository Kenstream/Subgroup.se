<?php

class Application_Form_Project extends SG_Form
{

    public function __construct($entityManager, $params = array())
    {
        parent::__construct();
        $this->_em = $entityManager;
        $this->_params = $params;

        // get all user with type of administrator
        $administators = $this->_em->getRepository('Entities\User')
            ->findBy(array('type' => Entities\User::TYPE_ADMIN));

        $owner = $this->getElement('owner');
        $owner->addMultiOption("0", $this->_translator->_("-- Super Administrator --"));

        foreach($administators as $admin) {
            $owner->addMultiOption($admin->getId(), $admin->getFirstName() . ' ' . $admin->getLastName(). ' (' . $admin->getEmail() . ')' );
        }
    }

    public function init()
    {
        parent::init();

        $this->setLegend($this->_translator->_("Update project information"));

        $title = new SG_Form_Element_Text('title');
        $title->setLabel($this->_translator->_("Title:"));
        $title->setRequired(true);

        $description = new SG_Form_Element_Textarea('description');
        $description->setLabel($this->_translator->_("Description:"));
        $description->setRequired(true);

        $users = new Zend_Form_Element_Hidden('users');
        $users->setAttrib('id', 'hidUserList');

        $owner = new SG_Form_Element_Select('owner');
        $owner->setAttrib('id', 'ownerOfUser');
        $owner->setLabel($this->_translator->_("Owner/Administrator of this project"));

        $submit = new SG_Form_Element_Submit("submit");
        $submit->helper = "formSubmitWithContainer";
        $submit->setLabel($this->_translator->_("Save"));

        $this->addElements(array(
            $title, $description, $users, $owner, $submit
        ));

        $decorators = array(array('ViewScript', array('viewScript' => '_form/project.phtml')));
        $this->setDecorators($decorators);

    }

    public function getScenarioOptions()
    {
        return $this->_em->getRepository('Entities\Scenario')->getSelectOptions();
    }

}