<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hostelroom
 *
 * @ORM\Table(name="hostelroom", indexes={@ORM\Index(name="roomhostel", columns={"FK_HOSTELID"}), @ORM\Index(name="room_type", columns={"FK_RCID"})})
 * @ORM\Entity
 */
class Hostelroom
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_ROOMID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkRoomid;

    /**
     * @var integer
     *
     * @ORM\Column(name="CAPACITY", type="integer", nullable=false)
     */
    private $capacity = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ROOM_NUMBER", type="string", length=60, nullable=false)
     */
    private $roomNumber = '0';

    /**
     * @var \Application\Entity\Hostel
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Hostel")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_HOSTELID", referencedColumnName="PK_HOSTELID")
     * })
     */
    private $fkHostelid;

    /**
     * @var \Application\Entity\Roomcategory
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Roomcategory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_RCID", referencedColumnName="PK_RCID")
     * })
     */
    private $fkRcid;



    /**
     * Get pkRoomid
     *
     * @return integer
     */
    public function getPkRoomid()
    {
        return $this->pkRoomid;
    }

    /**
     * Set capacity
     *
     * @param integer $capacity
     *
     * @return Hostelroom
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * Get capacity
     *
     * @return integer
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * Set roomNumber
     *
     * @param string $roomNumber
     *
     * @return Hostelroom
     */
    public function setRoomNumber($roomNumber)
    {
        $this->roomNumber = $roomNumber;

        return $this;
    }

    /**
     * Get roomNumber
     *
     * @return string
     */
    public function getRoomNumber()
    {
        return $this->roomNumber;
    }

    /**
     * Set fkHostelid
     *
     * @param \Application\Entity\Hostel $fkHostelid
     *
     * @return Hostelroom
     */
    public function setFkHostelid(\Application\Entity\Hostel $fkHostelid = null)
    {
        $this->fkHostelid = $fkHostelid;

        return $this;
    }

    /**
     * Get fkHostelid
     *
     * @return \Application\Entity\Hostel
     */
    public function getFkHostelid()
    {
        return $this->fkHostelid;
    }

    /**
     * Set fkRcid
     *
     * @param \Application\Entity\Roomcategory $fkRcid
     *
     * @return Hostelroom
     */
    public function setFkRcid(\Application\Entity\Roomcategory $fkRcid = null)
    {
        $this->fkRcid = $fkRcid;

        return $this;
    }

    /**
     * Get fkRcid
     *
     * @return \Application\Entity\Roomcategory
     */
    public function getFkRcid()
    {
        return $this->fkRcid;
    }
}
