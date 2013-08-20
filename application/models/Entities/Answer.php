<?php

namespace Entities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="Repositories\Answer")
 * @Table(name="answers")
 */
class Answer
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="text")
     */
    private $value;

    /**
     * @ManyToOne(targetEntity="Entities\Scenario\Element", inversedBy="answers")
     * @JoinColumn(name="elementId", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $element;

    /**
     * @ManyToOne(targetEntity="Entities\Invitation", inversedBy="answers")
     * @JoinColumn(name="invitationId", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $invitation;


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function getElement()
    {
        return $this->element;
    }

    public function setElement(\Entities\Scenario\Element $element)
    {
        $this->element = $element;
        return $this;
    }

    public function getInvitation()
    {
        return $this->invitation;
    }

    public function setInvitation(\Entities\Invitation $invitation)
    {
        $this->invitation = $invitation;
        return $this;
    }

}

