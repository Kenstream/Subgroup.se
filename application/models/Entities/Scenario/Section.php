<?php

namespace Entities\Scenario;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="sections")
 */
class Section
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="text")
     */
    private $title;

    /**
     * @Column(type="smallint")
     */
    private $sequence;

    /**
     * @Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ManyToOne(targetEntity="Entities\Scenario", inversedBy="sections")
     * @JoinColumn(name="scenarioId", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $scenario;

    /**
     * @OneToMany(targetEntity="Entities\Scenario\Element", mappedBy="section")
     * @OrderBy({"sequence" = "ASC"})
     */
    private $elements;

    public function __construct()
    {
        $this->elements = new ArrayCollection;
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

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
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

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
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

    public function getElements()
    {
        return $this->elements;
    }

    public function addElement(\Entities\Scenario\Element $element)
    {
        $element->setSection($this);
        $this->elements[] = $element;
        return $this;
    }

    public function removeElement(\Entities\Scenario\Element $element)
    {
        $this->elements->removeElement($element);
        return $this;
    }
}

