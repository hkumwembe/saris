<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Calendarevent
 *
 * @ORM\Table(name="calendarevent", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"PK_EVENTID"})}, indexes={@ORM\Index(name="eventtype", columns={"FK_EVENTTYPEID"}), @ORM\Index(name="academicperiod", columns={"FK_ACADEMICPERIODID"})})
 * @ORM\Entity
 */
class Calendarevent
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_EVENTID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkEventid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="_START", type="datetime", nullable=true)
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="_END", type="datetime", nullable=true)
     */
    private $end;

    /**
     * @var string
     *
     * @ORM\Column(name="TARGETGROUP", type="text", nullable=true)
     */
    private $targetgroup;

    /**
     * @var \Application\Entity\Academicyear
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Academicyear")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_ACADEMICPERIODID", referencedColumnName="PK_ACADEMICPERIODID")
     * })
     */
    private $fkAcademicperiodid;

    /**
     * @var \Application\Entity\Eventtype
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Eventtype")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_EVENTTYPEID", referencedColumnName="PK_EVENTTYPEID")
     * })
     */
    private $fkEventtypeid;



    /**
     * Get pkEventid
     *
     * @return integer
     */
    public function getPkEventid()
    {
        return $this->pkEventid;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     *
     * @return Calendarevent
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     *
     * @return Calendarevent
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set targetgroup
     *
     * @param string $targetgroup
     *
     * @return Calendarevent
     */
    public function setTargetgroup($targetgroup)
    {
        $this->targetgroup = $targetgroup;

        return $this;
    }

    /**
     * Get targetgroup
     *
     * @return string
     */
    public function getTargetgroup()
    {
        return $this->targetgroup;
    }

    /**
     * Set fkAcademicperiodid
     *
     * @param \Application\Entity\Academicyear $fkAcademicperiodid
     *
     * @return Calendarevent
     */
    public function setFkAcademicperiodid(\Application\Entity\Academicyear $fkAcademicperiodid = null)
    {
        $this->fkAcademicperiodid = $fkAcademicperiodid;

        return $this;
    }

    /**
     * Get fkAcademicperiodid
     *
     * @return \Application\Entity\Academicyear
     */
    public function getFkAcademicperiodid()
    {
        return $this->fkAcademicperiodid;
    }

    /**
     * Set fkEventtypeid
     *
     * @param \Application\Entity\Eventtype $fkEventtypeid
     *
     * @return Calendarevent
     */
    public function setFkEventtypeid(\Application\Entity\Eventtype $fkEventtypeid = null)
    {
        $this->fkEventtypeid = $fkEventtypeid;

        return $this;
    }

    /**
     * Get fkEventtypeid
     *
     * @return \Application\Entity\Eventtype
     */
    public function getFkEventtypeid()
    {
        return $this->fkEventtypeid;
    }
}
