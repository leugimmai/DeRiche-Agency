<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Types\FormType;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the form that gets attached to a note.
 *
 * @ORM\Table(name="form")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FormRepository")
 */
class Form implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="uuid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Forms are attached to a single Note.
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Note", inversedBy="forms")
     * @ORM\JoinColumn(name="note_id", referencedColumnName="uuid")
     */
    private $note;

    /**
     * Type of Form
     * @var string
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     */
    private $type;

    /**
     * The content of the Form. This is serialized.
     * @ORM\Column(type="json_array")
     */
    private $data;

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
            'id' => $this->getId(),
            'type' => $this->getType(),
            'data' => $this->getData(),
        ];
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set data
     *
     * @param array $data
     *
     * @return Form
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Form
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set note
     *
     * @param \AppBundle\Entity\Note $note
     *
     * @return Form
     */
    public function setNote(\AppBundle\Entity\Note $note = null)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return \AppBundle\Entity\Note
     */
    public function getNote()
    {
        return $this->note;
    }
}
