<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Programmefee
 *
 * @ORM\Table(name="programmefee", indexes={@ORM\Index(name="programme", columns={"FK_PROGRAMID"}), @ORM\Index(name="entry", columns={"FK_ENTRYMANNERID"})})
 * @ORM\Entity
 */
class Programmefee
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_PROGRAMFEEID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $pkProgramfeeid;

    /**
     * @var string
     *
     * @ORM\Column(name="GROUPING", type="text", nullable=true)
     */
    private $grouping = '1';

    /**
     * @var float
     *
     * @ORM\Column(name="FEE", type="float", precision=10, scale=0, nullable=true)
     */
    private $fee = '0';

    /**
     * @var \Application\Entity\Entrymanner
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Application\Entity\Entrymanner")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_ENTRYMANNERID", referencedColumnName="PK_ENTRYMANNERID")
     * })
     */
    private $fkEntrymannerid;

    /**
     * @var \Application\Entity\Program
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Application\Entity\Program")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_PROGRAMID", referencedColumnName="PK_PROGRAMID")
     * })
     */
    private $fkProgramid;



    /**
     * Set pkProgramfeeid
     *
     * @param integer $pkProgramfeeid
     *
     * @return Programmefee
     */
    public function setPkProgramfeeid($pkProgramfeeid)
    {
        $this->pkProgramfeeid = $pkProgramfeeid;

        return $this;
    }

    /**
     * Get pkProgramfeeid
     *
     * @return integer
     */
    public function getPkProgramfeeid()
    {
        return $this->pkProgramfeeid;
    }

    /**
     * Set grouping
     *
     * @param string $grouping
     *
     * @return Programmefee
     */
    public function setGrouping($grouping)
    {
        $this->grouping = $grouping;

        return $this;
    }

    /**
     * Get grouping
     *
     * @return string
     */
    public function getGrouping()
    {
        return $this->grouping;
    }

    /**
     * Set fee
     *
     * @param float $fee
     *
     * @return Programmefee
     */
    public function setFee($fee)
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * Get fee
     *
     * @return float
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * Set fkEntrymannerid
     *
     * @param \Application\Entity\Entrymanner $fkEntrymannerid
     *
     * @return Programmefee
     */
    public function setFkEntrymannerid(\Application\Entity\Entrymanner $fkEntrymannerid)
    {
        $this->fkEntrymannerid = $fkEntrymannerid;

        return $this;
    }

    /**
     * Get fkEntrymannerid
     *
     * @return \Application\Entity\Entrymanner
     */
    public function getFkEntrymannerid()
    {
        return $this->fkEntrymannerid;
    }

    /**
     * Set fkProgramid
     *
     * @param \Application\Entity\Program $fkProgramid
     *
     * @return Programmefee
     */
    public function setFkProgramid(\Application\Entity\Program $fkProgramid)
    {
        $this->fkProgramid = $fkProgramid;

        return $this;
    }

    /**
     * Get fkProgramid
     *
     * @return \Application\Entity\Program
     */
    public function getFkProgramid()
    {
        return $this->fkProgramid;
    }
}
