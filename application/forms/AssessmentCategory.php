<?php

class Application_Form_AssessmentCategory extends SG_Form
{
    const TYPE_SUB_CATEGORY     = 'S';
    const TYPE_MAIN_CATEGORY    = 'M';

    public function __construct($entityManager, $type)
    {
        parent::__construct();
        $this->_em = $entityManager;

        if ($type == self::TYPE_SUB_CATEGORY) {
            $select = new Zend_Form_Element_Select('parentCategory');
            $select->setMultiOptions($this->_em->getRepository('Entities\AssessmentCategory')->getSelectOptions());
            $select->setLabel($this->_translator->_("Parent Category:"));
        } else {
            $select = new Zend_Form_Element_Select('type');
            $select->setMultiOptions(array(
                Entities\AssessmentCategory::TYPE_MAIN => $this->_translator->_("Main category"),
                Entities\AssessmentCategory::TYPE_OVERALL => $this->_translator->_("Overall category"),
            ));
            $select->setLabel($this->_translator->_("Category type:"));
        }
        $select->setOrder(3);

        $this->addElement($select);
    }

    public function init()
    {
        parent::init();

        $this->setLegend($this->_translator->_("Update category information"));

        $name = new SG_Form_Element_Text('name');
        $name->setLabel($this->_translator->_("Name:"));
        $name->setRequired(true);
        $name->setOrder(1);

        $description = new SG_Form_Element_Textarea('description');
        $description->setLabel($this->_translator->_("Description:"));
        $description->setRequired(true);
        $description->setOrder(2);

        $submit = new SG_Form_Element_Submit("submit");
        $submit->helper = "formSubmitWithContainer";
        $submit->setLabel($this->_translator->_("Save"));
        $submit->setOrder(5);

        $this->addElements(array(
            $name, $description, $submit
        ));
    }

}