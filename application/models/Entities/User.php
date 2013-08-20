<?php

namespace Entities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="Repositories\User")
 * @Table(name="users")
 * @HasLifecycleCallbacks
 */
class User
{
    const TYPE_GUEST        = 'G';
    const TYPE_ADMIN        = 'A';
    const TYPE_SUPER_ADMIN  = 'S';
    const TYPE_USER         = 'U';

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(length=100, unique=true)
     */
    private $email;

    /**
     * @Column(length=40)
     */
    private $password;

    /**
     * @Column(length=32)
     */
    private $salt;

    /**
     * @Column(length=1)
     */
    private $type;

    /**
     * @Column(length=100)
     */
    private $firstName;

    /**
     * @Column(length=100)
     */
    private $lastName;

    /**
     * @Column(type="datetime")
     */
    private $birthDate;

    /**
     * @Column(type="datetime")
     */
    private $creationDate;

    /**
     * @Column(type="datetime")
     */
    private $accessLimitDate;

    /**
     * @OneToMany(targetEntity="Entities\Invitation", mappedBy="user", cascade={"persist", "remove"})
     * @OrderBy({"status" = "DESC", "invitationDate" = "ASC"})
     */
    private $invitations;

    /**
     * @OneToMany(targetEntity="Entities\Scenario", mappedBy="user", cascade={"persist", "remove"})
     */
    private $scenarios;

    /**
     * @OneToMany(targetEntity="Entities\Project", mappedBy="user", cascade={"persist", "remove"})
     */
    private $projects;

    /**
     * @ManyToOne(targetEntity="Entities\User", inversedBy="users")
     * @JoinColumn(name="creatorId", referencedColumnName="id", columnDefinition="INT(11) NOT NULL DEFAULT '1'", nullable=false, onDelete="CASCADE")
     */
    private $creator;

    /**
     * @OneToMany(targetEntity="Entities\User", mappedBy="creator", cascade={"persist", "remove"})
     */
    private $users;



    /**
     * Entity constructor
     */
    public function __construct()
    {
        $this->scenarios = new ArrayCollection();
        $this->invitations = new ArrayCollection();

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

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password, $salt = null)
    {
        if (is_null($salt)) {
            $this->salt = md5(time());
            $this->password = sha1($password . $this->salt);
        } else {
            $this->salt = $salt;
            $this->password = $password;
        }
        return $this;
    }

    public function getSalt()
    {
        return $this->salt;
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

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getBirthDate()
    {
        return $this->birthDate;
    }

    public function setBirthDate($datetime)
    {
        $this->birthDate = new \DateTime($datetime);
        return $this;
    }

    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function getAccessLimitDate()
    {
        return $this->accessLimitDate;
    }

    public function setAccessLimitDate($datetime)
    {
        $this->accessLimitDate = new \DateTime($datetime);
        return $this;
    }

    public function getInvitations()
    {
        return $this->invitations;
    }

    public function addInvitation(\Entities\Invitation $invitation)
    {
        $invitation->setUser($this);
        $this->invitations[] = $invitation;
        return $this;
    }

    public function removeInvitation(\Entities\Invitation $invitation)
    {
        $this->invitations->removeElement($invitation);
        return $this;
    }

    public function getScenarios()
    {
        return $this->scenarios;
    }

    public function addScenario(\Entities\Scenario $scenario)
    {
        $scenario->setUser($this);
        $this->scenarios[] = $scenario;
        return $this;
    }

    public function removeScenario(\Entities\Scenario $scenario)
    {
        $this->scenarios->removeElement($scenario);
        return $this;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function addUser(\Entities\User $user)
    {
        $user->setCreator($this);
        $this->users[] = $user;
        return $this;
    }

    public function removeUser(\Entities\User $user)
    {
        $this->users->removeElement($user);
        return $this;
    }

    public function getCreator()
    {
        return $this->creator;
    }

    public function setCreator(\Entities\User $creator)
    {
        $this->creator = $creator;
        return $this;
    }

    public function getProjects()
    {
        return $this->projects;
    }

    public function addProject(\Entities\Project $project)
    {
        $project->setUser($this);
        $this->projects[] = $project;
        return $this;
    }

    public function removeProject(\Entities\Project $project)
    {
        $this->projects->removeElement($project);
        return $this;
    }
}