<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hostel
 *
 * @ORM\Table(name="hostel", indexes={@ORM\Index(name="hostel_type", columns={"FK_HTID"}), @ORM\Index(name="hostel_campus", columns={"FK_CAMPUSID"})})
 * @ORM\Entity
 */
class Hostel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_HOSTELID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkHostelid;

    /**
     * @var string
     *
     * @ORM\Column(name="HOSTEL_NAME", type="string", length=45, nullable=false)
     */
    private $hostelName;

    /**
     * @var \Application\Entity\Campus
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Campus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_CAMPUSID", referencedColumnName="PK_CAMPUSID")
     * })
     */
    private $fkCampusid;

    /**
     * @var \Application\Entity\Hostelcategory
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Hostelcategory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_HTID", referencedColumnName="PK_HTID")
     * })
     */
    private $fkHtid;



    /**
     * Get pkHostelid
     *
     * @return integer
     */
    public function getPkHostelid()
    {
        return $this->pkHostelid;
    }

    /**
     * Set hostelName
     *
     * @param string $hostelName
     *
     * @return Hostel
     */
    public function setHostelName($hostelName)
    {
        $this->hostelName = $hostelName;

        return $this;
    }

    /**
     * Get hostelName
     *
     * @return string
     */
    public function getHostelName()
    {
        return $this->hostelName;
    }

    /**
     * Set fkCampusid
     *
     * @param \Application\Entity\Campus $fkCampusid
     *
     * @return Hostel
     */
    public function setFkCampusid(\Application\Entity\Campus $fkCampusid = null)
    {
        $this->fkCampusid = $fkCampusid;

        return $this;
    }

    /**
     * Get fkCampusid
     *
     * @return \Application\Entity\Campus
     */
    public function getFkCampusid()
    {
        return $this->fkCampusid;
    }

    /**
     * Set fkHtid
     *
     * @param \Application\Entity\Hostelcategory $fkHtid
     *
     * @return Hostel
     */
    public function setFkHtid(\Application\Entity\Hostelcategory $fkHtid = null)
    {
        $this->fkHtid = $fkHtid;

        return $this;
    }

    /**
     * Get fkHtid
     *
     * @return \Application\Entity\Hostelcategory
     */
    public function getFkHtid()
    {
        return $this->fkHtid;
    }
}
