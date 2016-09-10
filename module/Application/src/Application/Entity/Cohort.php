<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cohort
 *
 * @ORM\Table(name="cohort")
 * @ORM\Entity
 */
class Cohort
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_SCID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkScid;

    /**
     * @var string
     *
     * @ORM\Column(name="COURSE_NAME", type="string", length=250, nullable=false)
     */
    private $courseName;

    /**
     * @var integer
     *
     * @ORM\Column(name="FK_PROGRAMID", type="integer", nullable=true)
     */
    private $fkProgramid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="START_DATE", type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="END_DATE", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRIPTION", type="text", length=65535, nullable=true)
     */
    private $description;



    /**
     * Get pkScid
     *
     * @return integer
     */
    public function getPkScid()
    {
        return $this->pkScid;
    }

    /**
     * Set courseName
     *
     * @param string $courseName
     *
     * @return Cohort
     */
    public function setCourseName($courseName)
    {
        $this->courseName = $courseName;

        return $this;
    }

    /**
     * Get courseName
     *
     * @return string
     */
    public function getCourseName()
    {
        return $this->courseName;
    }

    /**
     * Set fkProgramid
     *
     * @param integer $fkProgramid
     *
     * @return Cohort
     */
    public function setFkProgramid($fkProgramid)
    {
        $this->fkProgramid = $fkProgramid;

        return $this;
    }

    /**
     * Get fkProgramid
     *
     * @return integer
     */
    public function getFkProgramid()
    {
        return $this->fkProgramid;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Cohort
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Cohort
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Cohort
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
