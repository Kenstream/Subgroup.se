<?php

class Application_Form_Scenario extends SG_Form
{

    public function __construct($entityManager, $params = array())
    {
        parent::__construct();
        $this->_em = $entityManager;
        $this->_params = $params;

        $scenarios = $this->_em->getRepository('Entities\Scenario')
            ->findAll();

        foreach($scenarios as $scenario) {
            $this->getElement('copyFrom')->addMultiOption($scenario->getId(), $scenario->getTitle());
        }
    }

    public function init()
    {
        parent::init();

        $this->setLegend($this->_translator->_("Create new scenario"));

        $title = new SG_Form_Element_Text('title');
        $title->setLabel($this->_translator->_("Title:"));
        $title->setRequired(true);

        $description = new SG_Form_Element_Textarea('description');
        $description->setLabel($this->_translator->_("Description:"));
        $description->setRequired(true);

        $copyFrom = new SG_Form_Element_Select('copyFrom');
        $copyFrom->addMultiOption('0', $this->_translator->_('-- Select scenario to copy from --'));

        $submit = new SG_Form_Element_Submit("submit");
        $submit->helper = "formSubmitWithContainer";
        $submit->setLabel($this->_translator->_("Create new scenario"));

        $this->addElements(array(
            $title, $description, $copyFrom, $submit
        ));

    }

}