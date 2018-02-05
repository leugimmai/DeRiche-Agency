<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JsonSerializable;

/**
 * This is the Individual's representation in the database.
 *
 * @ORM\Table(name="individual")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IndividualRepository")
 */
class Individual implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(name="uuid", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $uuid;

    /**
     * Protected field, don't show to unauthorized users.
     *
     * @var integer
     *
     * @ORM\Column(name="medicalId", type="integer", unique=true)
     */
    private $medicalId;

    /**
     * The first name of the individual.
     *
     * @var string
     * @ORM\Column(name="firstName", type="string", length=255, nullable=false)
     */
    private $firstName;

    /**
     * The last name of the individual.
     *
     * @var string
     * @ORM\Column(name="lastName", type="string", length=255, nullable=false)
     */
    private $lastName;

    /**
     * Is the individual active or not.
     *
     * @var boolean
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = true;

    /**
     * Does this individual have seizures?
     *
     * @var boolean
     * @ORM\Column(name="seizure", type="boolean", nullable=true)
     */
    private $seizure = false;

    /**
     * Does this individual need their bowel movements tracked?
     *
     * @var boolean
     * @ORM\Column(name="bowel", type="boolean", nullable=true)
     */
    private $bowel = false;

    /**
     * The notes written about this individual
     *
     * One Individual has many Notes.
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Note", mappedBy="individual")
     */
    private $notes;

    /**
     * The objectives for this individual.
     *
     * One Individual has many Objectives.
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Objective", mappedBy="individual")
     */
    private $objectives;

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return [
            'uuid' => $this->getUuid(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'medicalId' => $this->getMedicalId(),
            'notes' => $this->getNotes()->toArray()
        ];
    }

    public function __toString()
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set medicalId
     *
     * @param integer $medicalId
     *
     * @return Individual
     */
    public function setMedicalId($medicalId)
    {
        $this->medicalId = $medicalId;

        return $this;
    }

    /**
     * Get medicalId
     *
     * @return integer
     */
    public function getMedicalId()
    {
        return $this->medicalId;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Individual
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
     * @return Individual
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
     * Set active
     *
     * @param boolean $active
     *
     * @return Individual
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Add note
     *
     * @param \AppBundle\Entity\Note $note
     *
     * @return Individual
     */
    public function addNote(\AppBundle\Entity\Note $note)
    {
        $this->notes[] = $note;

        return $this;
    }

    /**
     * Remove note
     *
     * @param \AppBundle\Entity\Note $note
     */
    public function removeNote(\AppBundle\Entity\Note $note)
    {
        $this->notes->removeElement($note);
    }

    /**
     * Get notes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Add objective
     *
     * @param \AppBundle\Entity\Objective $objective
     *
     * @return Individual
     */
    public function addObjective(\AppBundle\Entity\Objective $objective)
    {
        $this->objectives[] = $objective;

        return $this;
    }

    /**
     * Remove objective
     *
     * @param \AppBundle\Entity\Objective $objective
     */
    public function removeObjective(\AppBundle\Entity\Objective $objective)
    {
        $this->objectives->removeElement($objective);
    }

    /**
     * Get objectives
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getObjectives()
    {
        return $this->objectives;
    }

    /**
     * Set seizure status
     *
     * @param boolean $seizure
     *
     * @return Individual
     */
    public function setSeizure($seizure)
    {
        $this->seizure = $seizure;

        return $this;
    }

    /**
     * Get seizure status
     *
     * @return boolean
     */
    public function getSeizure()
    {
        return $this->seizure;
    }

    /**
     * Set bowel status
     *
     * @param boolean $bowel
     *
     * @return Individual
     */
    public function setBowel($bowel)
    {
        $this->bowel = $bowel;

        return $this;
    }

    /**
     * Get bowel status
     *
     * @return boolean
     */
    public function getBowel()
    {
        return $this->bowel;
    }
}