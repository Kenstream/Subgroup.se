<?php

namespace Entities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="Repositories\Project")
 * @Table(name="projects")
 * @HasLifecycleCallbacks
 */
class Project
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(length=255)
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
     * @Column(type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @OneToMany(targetEntity="Entities\Invitation", mappedBy="project", cascade={"persist", "remove"})
     */
    private $invitations;

    /**
     * @ManyToOne(targetEntity="Entities\User", inversedBy="projects")
     * @JoinColumn(name="userId", referencedColumnName="id", columnDefinition="INT(11) NOT NULL DEFAULT '1'", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * Entity constructor
     */
    public function __construct()
    {
        $this->invitations  = new ArrayCollection;
        $this->creationDate = new \DateTime("now");
        $this->startDate = new \DateTime("now");
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

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setStartDate($datetime)
    {
        $this->startDate = new \Date($datetime);
        return $this;
    }

    public function getInvitations()
    {
        return $this->invitations;
    }

    public function addInvitation(\Entities\Invitation $invitation)
    {
        $invitation->setProject($this);
        $this->invitations[] = $invitation;
        return $this;
    }

    public function removeInvitation(\Entities\Invitation $invitation)
    {
        $this->invitations->removeElement($invitation);
        return $this;
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

}