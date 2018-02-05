<?php
/**
 * Created by PhpStorm.
 * User: sahmed6
 * Date: 10/3/2017
 * Time: 7:26 PM
 */

namespace AppBundle\Entity;

use Doctrine\DBAL\Types\GuidType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Serializable;

/**
 * Represents an account which you can log into the application with.
 *
 * @ORM\Table(name="app_users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements AdvancedUserInterface, Serializable
{
    /**
     * The identifier of the User/Staff member.
     * Don't really need this since username is unique, but ehh.
     *
     * @ORM\Id
     * @ORM\Column(name="uuid", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $uuid;

    /**
     * The notes this staff member has authored.
     *
     * One Staff has many Notes.
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Note", mappedBy="staff")
     */
    private $authoredNotes;

    /**
     * The notes this staff member has reviewed notes.
     *
     * One Staff has many Notes.
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Note", mappedBy="reviewer")
     */
    private $reviewedNotes;

    /**
     * The username of the staff member.
     *
     * @var string
     * @ORM\Column(type="string", length=25, nullable=false, unique=true)
     */
    private $username;

    /**
     * The first name of the staff member.
     *
     * @var string
     * @ORM\Column(name="firstName", type="string", length=255, nullable=false)
     */
    private $firstName;

    /**
     * The last name of the staff member.
     *
     * @var string
     * @ORM\Column(name="lastName", type="string", length=255, nullable=false)
     */
    private $lastName;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * The password of the staff member. (Encrypted)
     *
     * @var string
     * @ORM\Column(name="password", type="string", length=64, nullable=false)
     */
    private $password;

    /**
     * Controls whether the staff member is able to login.
     *
     * @var boolean
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive = true;

    /**
     * The roles the staff member has.
     *
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return GuidType
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Add authoredNote
     *
     * @param \AppBundle\Entity\Note $authoredNote
     *
     * @return User
     */
    public function addAuthoredNote(\AppBundle\Entity\Note $authoredNote)
    {
        $this->authoredNotes[] = $authoredNote;

        return $this;
    }

    /**
     * Remove authoredNote
     *
     * @param \AppBundle\Entity\Note $authoredNote
     */
    public function removeAuthoredNote(\AppBundle\Entity\Note $authoredNote)
    {
        $this->authoredNotes->removeElement($authoredNote);
    }

    /**
     * Get authoredNotes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthoredNotes()
    {
        return $this->authoredNotes;
    }

    /**
     * Add reviewedNote
     *
     * @param \AppBundle\Entity\Note $reviewedNote
     *
     * @return User
     */
    public function addReviewedNote(\AppBundle\Entity\Note $reviewedNote)
    {
        $this->reviewedNotes[] = $reviewedNote;

        return $this;
    }

    /**
     * Remove reviewedNote
     *
     * @param \AppBundle\Entity\Note $reviewedNote
     */
    public function removeReviewedNote(\AppBundle\Entity\Note $reviewedNote)
    {
        $this->reviewedNotes->removeElement($reviewedNote);
    }

    /**
     * Get reviewedNotes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviewedNotes()
    {
        return $this->reviewedNotes;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->authoredNotes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reviewedNotes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getSalt()
    {
        // bcrypt so this is null.
        return null;
    }

    public function getRoles()
    {
        $roles = $this->roles;
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }
        return $roles;
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->uuid,
            $this->username,
            $this->password,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->uuid,
            $this->username,
            $this->password,
            ) = unserialize($serialized);
    }

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        return $this->isActive;
    }
}
