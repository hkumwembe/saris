<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cohortregistration
 *
 * @ORM\Table(name="cohortregistration", indexes={@ORM\Index(name="Cohort", columns={"FK_SCID"}), @ORM\Index(name="Studentid", columns={"FK_STUDENTID"})})
 * @ORM\Entity
 */
class Cohortregistration
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_CREGID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkCregid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="REGISTRATION_DATE", type="datetime", nullable=true)
     */
    private $registrationDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="NEXT_REGISTRATION", type="integer", nullable=true)
     */
    private $nextRegistration;

    /**
     * @var string
     *
     * @ORM\Column(name="STATUS", type="text", nullable=true)
     */
    private $status = '0';

    /**
     * @var \Application\Entity\Cohort
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Cohort")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_SCID", referencedColumnName="PK_SCID")
     * })
     */
    private $fkScid;

    /**
     * @var \Application\Entity\Student
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Student")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_STUDENTID", referencedColumnName="PK_STUDENTID")
     * })
     */
    private $fkStudentid;



    /**
     * Get pkCregid
     *
     * @return integer
     */
    public function getPkCregid()
    {
        return $this->pkCregid;
    }

    /**
     * Set registrationDate
     *
     * @param \DateTime $registrationDate
     *
     * @return Cohortregistration
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    /**
     * Get registrationDate
     *
     * @return \DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * Set nextRegistration
     *
     * @param integer $nextRegistration
     *
     * @return Cohortregistration
     */
    public function setNextRegistration($nextRegistration)
    {
        $this->nextRegistration = $nextRegistration;

        return $this;
    }

    /**
     * Get nextRegistration
     *
     * @return integer
     */
    public function getNextRegistration()
    {
        return $this->nextRegistration;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Cohortregistration
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set fkScid
     *
     * @param \Application\Entity\Cohort $fkScid
     *
     * @return Cohortregistration
     */
    public function setFkScid(\Application\Entity\Cohort $fkScid = null)
    {
        $this->fkScid = $fkScid;

        return $this;
    }

    /**
     * Get fkScid
     *
     * @return \Application\Entity\Cohort
     */
    public function getFkScid()
    {
        return $this->fkScid;
    }

    /**
     * Set fkStudentid
     *
     * @param \Application\Entity\Student $fkStudentid
     *
     * @return Cohortregistration
     */
    public function setFkStudentid(\Application\Entity\Student $fkStudentid = null)
    {
        $this->fkStudentid = $fkStudentid;

        return $this;
    }

    /**
     * Get fkStudentid
     *
     * @return \Application\Entity\Student
     */
    public function getFkStudentid()
    {
        return $this->fkStudentid;
    }
}
