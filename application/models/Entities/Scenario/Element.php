<?php

namespace Entities\Scenario;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="Repositories\Scenario\Element")
 * @Table(name="elements")
 */
class Element
{
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_TEXT     = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_RADIO    = 'radio';
    const TYPE_SELECT   = 'select';

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(length=100)
     */
    private $type;

    /**
     * @Column(type="boolean")
     */
    private $isRequired;

    /**
     * @Column(type="smallint")
     */
    private $sequence;

    /**
     * @Column(type="text")
     */
    private $label;

    /**
     * @Column(type="text", nullable=true)
     */
    private $infoText;

    /**
     * @Column(length=1, nullable=true)
     */
    private $infoType;

    /**
     * @Column(type="text", nullable=true)
     */
    private $jsonDefaultValue;

    /**
     * @OneToMany(targetEntity="Entities\Answer", mappedBy="element")
     */
    private $answers;

    /**
     * @ManyToOne(targetEntity="Entities\Scenario", inversedBy="elements")
     * @JoinColumn(name="scenarioId", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $scenario;

    /**
     * @ManyToOne(targetEntity="Entities\Scenario\Section", inversedBy="elements")
     * @JoinColumn(name="sectionId", referencedColumnName="id", onDelete="SET NULL")
     */
    private $section;

    /**
     * @ManyToOne(targetEntity="Entities\AssessmentCategory", inversedBy="elements")
     * @JoinColumn(name="assessmentCategoryId", referencedColumnName="id", onDelete="SET NULL")
     */
    private $assessmentCategory;

    /**
     * Entity constructor
     */
    public function __construct()
    {
        $this->answers = new ArrayCollection;
    }


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    public function getInfoText()
    {
        return $this->infoText;
    }

    public function setInfoText($text)
    {
        $this->infoText = $text;
        return $this;
    }

    public function getInfoType()
    {
        return $this->infoType;
    }

    public function setInfoType($infoType)
    {
        $this->infoType = $infoType;
        return $this;
    }

    public function getJsonDefaultValue()
    {
        return $this->jsonDefaultValue;
    }

    public function setJsonDefaultValue($jsonDefaultValue)
    {
        $this->jsonDefaultValue = $jsonDefaultValue;
        return $this;
    }

    public function getSequence()
    {
        return $this->sequence;
    }

    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
        return $this;
    }

    public function getScenario()
    {
        return $this->scenario;
    }

    public function setScenario(\Entities\Scenario $scenario)
    {
        $this->scenario = $scenario;
        return $this;
    }

    public function isRequired()
    {
        return $this->isRequired;
    }

    public function setRequired($required)
    {
        $this->isRequired = $required;
        return $this;
    }

    public function getSection()
    {
        return $this->section;
    }

    public function setSection(\Entities\Scenario\Section $section)
    {
        $this->section = $section;
        return $this;
    }

    public function getAssessmentCategory()
    {
        return $this->assessmentCategory;
    }

    public function setAssessmentCategory(\Entities\AssessmentCategory $category)
    {
        $this->assessmentCategory = $category;
        return $this;
    }

    public function getAnswers()
    {
        return $this->answers;
    }

    public function addAnswer(\Entities\Answer $answer)
    {
        $answer->setElement($this);
        $this->answers[] = $answer;
        return $this;
    }

    public function removeAnswer(\Entities\Answer $answer)
    {
        $this->answers->removeElement($answer);
        return $this;
    }
}

