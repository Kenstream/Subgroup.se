<?php

class Application_Form_ScenarioElement extends SG_Form
{
    const TYPE_TEXT             = 'text';
    const TYPE_TEXTAREA         = 'textarea';
    const TYPE_CHECKBOX         = 'checkbox';
    const TYPE_SELECT           = 'select';
    const TYPE_RADIO_2          = 'radio2';
    const TYPE_RADIO_5          = 'radio5';
    const TYPE_RADIO_5_INVERSE  = 'radio5inverse';
    const TYPE_RADIO_6_LEADER   = 'radio6leader';
    const TYPE_RADIO_6_MEMBER   = 'radio6member';


    public function __construct($entityManager, $scenarioId)
    {
        parent::__construct();
        $this->_em = $entityManager;

        $scenario = $this->_em->getRepository('Entities\Scenario')
            ->findOneBy(array('id' => $scenarioId));

        if ($scenario) {
            foreach($scenario->getSections() as $section) {
                $this->getElement('section')->addMultiOption($section->getId(), $section->getTitle());
            }
        }

        $assessmentCategories = $this->_em->getRepository('Entities\AssessmentCategory')
            ->getEndNodeCategories(Repositories\AssessmentCategory::END_NODE_MODE_SUB_ONLY);

        foreach($assessmentCategories as $assessmentCategory) {
            $this->getElement('category')->addMultiOption($assessmentCategory->getId(), $assessmentCategory->getName());
        }
    }

    public function init()
    {
        parent::init();

        $this->setLegend($this->_translator->_("Create new element"));

        $label = new SG_Form_Element_Text('label');
        $label->setLabel($this->_translator->_("Question/Text:"));
        $label->setRequired(true);

        $infoText = new SG_Form_Element_Textarea('infoText');
        $infoText->setLabel($this->_translator->_("Information/Instruction:"));

        $type = new Zend_Form_Element_Select('type');
        $type->setAttrib('id', 'selectType');
        $type->setLabel($this->_translator->translate("Type:"));
        $type->setMultiOptions(array(
            self::TYPE_TEXT => $this->_translator->translate("Text"),
            self::TYPE_TEXTAREA => $this->_translator->translate("Text Area"),
            self::TYPE_CHECKBOX => $this->_translator->translate("Check Box"),
            self::TYPE_SELECT => $this->_translator->translate("Select/Multi-options"),
            self::TYPE_RADIO_2 => $this->_translator->translate("Yes/No options"),
            self::TYPE_RADIO_5 => $this->_translator->translate("5 Values"),
            self::TYPE_RADIO_5_INVERSE => $this->_translator->translate("5 Values Inverse"),
            self::TYPE_RADIO_6_LEADER => $this->_translator->translate("6 Values (Not applicable)"),
            self::TYPE_RADIO_6_MEMBER => $this->_translator->translate("6 Values (Not know)"),
        ));

        $section = new Zend_Form_Element_Select('section');
        $section->setAttrib('id', 'selectSection');
        $section->setLabel($this->_translator->translate("Section:"));

        $category = new Zend_Form_Element_Select('category');
        $category->setAttrib('id', 'selectCategory');
        $category->setLabel($this->_translator->translate("Assessment category:"));
        $category->addMultiOption('0', $this->_translator->_('-- Select category --'));

        $submit = new SG_Form_Element_Submit("submit");
        $submit->helper = "formSubmitWithContainer";
        $submit->setLabel($this->_translator->_("Save"));

        $this->addElements(array(
            $label, $infoText, $type, $section, $category, $submit
        ));

        $decorators = array(array('ViewScript', array('viewScript' => '_form/scenarioElement.phtml')));
        $this->setDecorators($decorators);

    }

}