<?php

namespace Entities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="Repositories\Scenario")
 * @Table(name="scenarios")
 * @HasLifecycleCallbacks
 */
class Scenario
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
     * @Column(type="text")
     */
    private $description;

    /**
     * @Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ManyToOne(targetEntity="Entities\User", inversedBy="scenarios")
     * @JoinColumn(name="userId", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @OneToMany(targetEntity="Entities\Scenario\Section", mappedBy="scenario", cascade={"persist", "remove"})
     * @OrderBy({"sequence" = "ASC"})
     */
    private $sections;

    /**
     * @OneToMany(targetEntity="Entities\Scenario\Element", mappedBy="scenario", cascade={"persist", "remove"})
     * @OrderBy({"sequence" = "ASC"})
     */
    private $elements;

    /**
     * @OneToMany(targetEntity="Entities\Invitation", mappedBy="project", cascade={"persist", "remove"})
     */
    private $invitations;

    /**
     * Entity constructor
     */
    public function __construct()
    {
        $this->elements = new ArrayCollection;
        $this->sections = new ArrayCollection;
        $this->invitations = new ArrayCollection;

        $this->creationDate = new \DateTime("now");
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

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(\Entities\User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function getElements()
    {
        return $this->elements;
    }

    public function addElement(\Entities\Scenario\Element $element)
    {
        $element->setScenario($this);
        $this->elements[] = $element;
        return $this;
    }

    public function removeElement(\Entities\Scenario\Element $element)
    {
        $this->elements->removeElement($element);
        return $this;
    }

    public function getSections()
    {
        return $this->sections;
    }

    public function addSection(\Entities\Scenario\Section $section)
    {
        $section->setScenario($this);
        $this->sections[] = $section;
        return $this;
    }

    public function removeSection(\Entities\Scenario\Section $section)
    {
        $this->sections->removeElement($section);
        return $this;
    }

    public function getInvitations()
    {
        return $this->invitations;
    }

    public function addInvitation(\Entities\Invitation $invitation)
    {
        $invitation->setScenario($this);
        $this->invitations[] = $invitation;
        return $this;
    }

    public function removeInvitation(\Entities\Invitation $invitation)
    {
        $this->invitations->removeElement($invitation);
        return $this;
    }

}

