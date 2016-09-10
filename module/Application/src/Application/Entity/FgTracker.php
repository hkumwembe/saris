<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FgTracker
 *
 * @ORM\Table(name="fg_tracker", indexes={@ORM\Index(name="grademark", columns={"FK_SMID"}), @ORM\Index(name="inputter", columns={"CAPTUREDBY"}), @ORM\Index(name="flowid", columns={"FK_GRADEFLOWID"})})
 * @ORM\Entity
 */
class FgTracker
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_FGTRACKINGID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkFgtrackingid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="TIMESTAMP", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="INPUT_METHOD", type="text", nullable=true)
     */
    private $inputMethod = 'Automatic';

    /**
     * @var float
     *
     * @ORM\Column(name="GRADE", type="float", precision=10, scale=0, nullable=true)
     */
    private $grade;

    /**
     * @var string
     *
     * @ORM\Column(name="NOTES", type="string", length=255, nullable=true)
     */
    private $notes;

    /**
     * @var \Application\Entity\Gradeflow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Gradeflow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_GRADEFLOWID", referencedColumnName="PK_GRADEFLOWID")
     * })
     */
    private $fkGradeflowid;

    /**
     * @var \Application\Entity\Studentmark
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Studentmark")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_SMID", referencedColumnName="PK_SMID")
     * })
     */
    private $fkSmid;

    /**
     * @var \Application\Entity\Staff
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Staff")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CAPTUREDBY", referencedColumnName="PK_STAFFID")
     * })
     */
    private $capturedby;



    /**
     * Get pkFgtrackingid
     *
     * @return integer
     */
    public function getPkFgtrackingid()
    {
        return $this->pkFgtrackingid;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return FgTracker
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set inputMethod
     *
     * @param string $inputMethod
     *
     * @return FgTracker
     */
    public function setInputMethod($inputMethod)
    {
        $this->inputMethod = $inputMethod;

        return $this;
    }

    /**
     * Get inputMethod
     *
     * @return string
     */
    public function getInputMethod()
    {
        return $this->inputMethod;
    }

    /**
     * Set grade
     *
     * @param float $grade
     *
     * @return FgTracker
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Get grade
     *
     * @return float
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return FgTracker
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set fkGradeflowid
     *
     * @param \Application\Entity\Gradeflow $fkGradeflowid
     *
     * @return FgTracker
     */
    public function setFkGradeflowid(\Application\Entity\Gradeflow $fkGradeflowid = null)
    {
        $this->fkGradeflowid = $fkGradeflowid;

        return $this;
    }

    /**
     * Get fkGradeflowid
     *
     * @return \Application\Entity\Gradeflow
     */
    public function getFkGradeflowid()
    {
        return $this->fkGradeflowid;
    }

    /**
     * Set fkSmid
     *
     * @param \Application\Entity\Studentmark $fkSmid
     *
     * @return FgTracker
     */
    public function setFkSmid(\Application\Entity\Studentmark $fkSmid = null)
    {
        $this->fkSmid = $fkSmid;

        return $this;
    }

    /**
     * Get fkSmid
     *
     * @return \Application\Entity\Studentmark
     */
    public function getFkSmid()
    {
        return $this->fkSmid;
    }

    /**
     * Set capturedby
     *
     * @param \Application\Entity\Staff $capturedby
     *
     * @return FgTracker
     */
    public function setCapturedby(\Application\Entity\Staff $capturedby = null)
    {
        $this->capturedby = $capturedby;

        return $this;
    }

    /**
     * Get capturedby
     *
     * @return \Application\Entity\Staff
     */
    public function getCapturedby()
    {
        return $this->capturedby;
    }
}
