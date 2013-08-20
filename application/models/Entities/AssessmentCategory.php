<?php

namespace Entities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="Repositories\AssessmentCategory")
 * @Table(name="assessment_categories")
 */
class AssessmentCategory
{
    const TYPE_MAIN     = 'M';
    const TYPE_OVERALL  = 'O';

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(length=255)
     */
    private $name;

    /**
     * @Column(length=1, nullable=true)
     */
    private $type;

    /**
     * @Column(type="text")
     */
    private $description;

    /**
     * @Column(type="smallint")
     */
    private $sequence;

    /**
     * @Column(type="boolean")
     */
    private $visible;

    /**
     * @OneToMany(targetEntity="Entities\Scenario\Element", mappedBy="assessmentCategory")
     */
    private $elements;

    /**
     * @OneToMany(targetEntity="Entities\AssessmentCategory", mappedBy="parentCategory")
     */
    private $childCategories;

    /**
     * @ManyToOne(targetEntity="Entities\AssessmentCategory", inversedBy="childCategories")
     * @JoinColumn(name="parentCategoryId", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parentCategory;


    /**
     * Entity constructor
     */
    public function __construct()
    {
        $this->invitationDate   = new \DateTime("now");
        $this->childCategories  = new ArrayCollection;
        $this->elements         = new ArrayCollection;
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

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
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

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
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

    public function getVisible()
    {
        return $this->visible;
    }

    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }

    public function getParentCategory()
    {
        return $this->parentCategory;
    }

    public function setParentCategory(\Entities\AssessmentCategory $category)
    {
        $this->parentCategory = $category;
        return $this;
    }

    public function getChildCategories()
    {
        return $this->childCategories;
    }

    public function addChildCategory(\Entities\AssessmentCategory $category)
    {
        $category->setParentCategory($this);
        $this->childCategories[] = $category;
        return $this;
    }

    public function removeChildCategory(\Entities\AssessmentCategory $category)
    {
        $category->setParentCategory(null);
        $this->childCategories->removeElement($category);
        return $this;
    }

    public function getElements()
    {
        return $this->elements;
    }

    public function addElement(\Entities\Scenario\Element $element)
    {
        $element->setAssessmentCategory($this);
        $this->elements[] = $element;
        return $this;
    }

    public function removeElement(\Entities\Scenario\Element $element)
    {
        $this->elements->removeElement($element);
        return $this;
    }
}

