<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Individuals' Objective
 *
 * @ORM\Table(name="objective")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ObjectiveRepository")
 */
class Objective
{
    /**
     * @var int
     *
     * @ORM\Column(name="uuid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $uuid;

    /**
     * The individual of this Objective.
     *
     * Many Objectives have one Individual.
     *
     * @ORM\ManyToOne(targetEntity="Individual", inversedBy="objectives")
     * @ORM\JoinColumn(name="individual_uuid", referencedColumnName="uuid", nullable=false)
     */
    private $individual;

    /**
     * The objective's name text.
     *
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * The objective's goal text.
     *
     * @var string
     * @ORM\Column(name="goal_text", type="text")
     */
    private $goalText;

    /**
     * The objective's objective text.
     *
     * @var string
     * @ORM\Column(name="objective_text", type="text")
     */
    private $objectiveText;

    /**
     * The objective's 'guidance note' text.
     *
     * @var string
     * @ORM\Column(name="guidance_notes", type="text")
     */
    private $guidanceNotes;

    /**
     * The objective's frequency amount.
     *
     * @var int
     * @ORM\Column(name="freq_amount", type="integer")
     */
    private $freqAmount;

    /**
     * The objective's frequency kind.
     *
     * @var string
     * @ORM\Column(name="freq_kind", type="string", length=255)
     */
    private $freqKind;

    /**
     * Get uuid
     *
     * @return integer
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Objective
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set goalText
     *
     * @param string $goalText
     *
     * @return Objective
     */
    public function setGoalText($goalText)
    {
        $this->goalText = $goalText;

        return $this;
    }

    /**
     * Get goalText
     *
     * @return string
     */
    public function getGoalText()
    {
        return $this->goalText;
    }

    /**
     * Set objectiveText
     *
     * @param string $objectiveText
     *
     * @return Objective
     */
    public function setObjectiveText($objectiveText)
    {
        $this->objectiveText = $objectiveText;

        return $this;
    }

    /**
     * Get objectiveText
     *
     * @return string
     */
    public function getObjectiveText()
    {
        return $this->objectiveText;
    }

    /**
     * Set freqAmount
     *
     * @param integer $freqAmount
     *
     * @return Objective
     */
    public function setFreqAmount($freqAmount)
    {
        $this->freqAmount = $freqAmount;

        return $this;
    }

    /**
     * Get freqAmount
     *
     * @return integer
     */
    public function getFreqAmount()
    {
        return $this->freqAmount;
    }

    /**
     * Set freqKind
     *
     * @param string $freqKind
     *
     * "/Day", "/Week", "/Month" (Exactly as stated)
     * @return Objective
     */
    public function setFreqKind($freqKind)
    {
        $this->freqKind = $freqKind;

        return $this;
    }

    /**
     * Get freqKind
     *
     * @return string
     */
    public function getFreqKind()
    {
        return $this->freqKind;
    }

    /**
     * Set individual
     *
     * @param \AppBundle\Entity\Individual $individual
     *
     * @return Objective
     */
    public function setIndividual(\AppBundle\Entity\Individual $individual)
    {
        $this->individual = $individual;

        return $this;
    }

    /**
     * Get individual
     *
     * @return \AppBundle\Entity\Individual
     */
    public function getIndividual()
    {
        return $this->individual;
    }

    /**
     * Set guidanceNotes
     *
     * @param string $guidanceNotes
     *
     * @return Objective
     */
    public function setGuidanceNotes($guidanceNotes)
    {
        $this->guidanceNotes = $guidanceNotes;

        return $this;
    }

    /**
     * Get guidanceNotes
     *
     * @return string
     */
    public function getGuidanceNotes()
    {
        return $this->guidanceNotes;
    }
}