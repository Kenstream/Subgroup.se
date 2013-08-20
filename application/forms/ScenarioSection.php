<?php

class Application_Form_ScenarioSection extends SG_Form
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

        $this->setLegend($this->_translator->_("Update section information"));

        $title = new SG_Form_Element_Text('title');
        $title->setLabel($this->_translator->_("Title:"));
        $title->setRequired(true);

        $description = new SG_Form_Element_Textarea('description');
        $description->setLabel($this->_translator->_("Description/Instruction:"));

        $sequence = new SG_Form_Element_Text('sequence');
        $sequence->setLabel($this->_translator->_("Sequence:"));
        $sequence->setAttrib('style', 'max-width: 50px;');
        $sequence->setRequired(true);
        $sequence->addValidator('Digits');

        $submit = new SG_Form_Element_Submit("submit");
        $submit->helper = "formSubmitWithContainer";
        $submit->setLabel($this->_translator->_("Save"));

        $this->addElements(array(
            $title, $description, $sequence, $submit
        ));

    }

}