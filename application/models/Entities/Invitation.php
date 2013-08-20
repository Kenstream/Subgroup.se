<?php

namespace Entities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="Repositories\Invitation")
 * @Table(name="invitations")
 */
class Invitation
{
    const TYPE_LEADER = 'L';
    const TYPE_MEMBER = 'M';

    const STATUS_FINISHED       = 'F';
    const STATUS_IN_PROGRESS    = 'P';

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(length=1)
     */
    private $status;

    /**
     * @Column(length=1)
     */
    private $type;

    /**
     * @Column(type="datetime")
     */
    private $invitationDate;

    /**
     *  @Column(type="datetime", nullable=true)
     */
    private $submissionDate;

    /**
     * @Column(type="text", nullable=true)
     */
    private $jsonData;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private $lastUpdateDate;

    /**
     * @ManyToOne(targetEntity="Entities\User", inversedBy="invitations")
     * @JoinColumn(name="userId", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @ManyToOne(targetEntity="Entities\Project", inversedBy="invitations")
     * @JoinColumn(name="projectId", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $project;

    /**
     * @ManyToOne(targetEntity="Entities\Scenario", inversedBy="scenarios")
     * @JoinColumn(name="scenarioId", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $scenario;

    /**
     * @OneToMany(targetEntity="Entities\Answer", mappedBy="invitation", cascade={"persist", "remove"})
     */
    private $answers;

    /**
     * Entity constructor
     */
    public function __construct()
    {
        $this->invitationDate   = new \DateTime("now");
        $this->answers          = new ArrayCollection;
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

    public function getJsonData()
    {
        return $this->jsonData;
    }

    public function setJsonData($jsonData)
    {
        $this->jsonData = $jsonData;
        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
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

    public function getInvitationDate()
    {
        return $this->invitationDate;
    }

    public function setInvitationDate($datetime)
    {
        $this->invitationDate = new \DateTime($datetime);
        return $this;
    }

    public function getLastUpdateDate()
    {
        return $this->lastUpdateDate;
    }

    public function setLastUpdateDate($datetime)
    {
        $this->lastUpdateDate = new \DateTime($datetime);
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

    public function getProject()
    {
        return $this->project;
    }

    public function setProject(\Entities\Project $project)
    {
        $this->project = $project;
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

    public function getAnswers()
    {
        return $this->answers;
    }

    public function addAnswer(\Entities\Answer $answer)
    {
        $answer->setInvitation($this);
        $this->answers[] = $answer;
        return $this;
    }

    public function removeAnswer(\Entities\Answer $answer)
    {
        $this->answers->removeElement($answer);
        return $this;
    }


}

