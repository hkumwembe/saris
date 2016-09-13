<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StudentOnWaiver
 *
 * @ORM\Table(name="student_on_waiver", indexes={@ORM\Index(name="student", columns={"FK_STUDENTID"}), @ORM\Index(name="waivedsponsor", columns={"FK_SPONSORID"}), @ORM\Index(name="staff", columns={"CAPTURED_BY"})})
 * @ORM\Entity
 */
class StudentOnWaiver
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_SOWID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkSowid;

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
     * @ORM\Column(name="OTHER_REASON", type="text", length=65535, nullable=true)
     */
    private $otherReason;

    /**
     * @var \Application\Entity\Staff
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Staff")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CAPTURED_BY", referencedColumnName="PK_STAFFID")
     * })
     */
    private $capturedBy;

    /**
     * @var \Application\Entity\Sponsor
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Sponsor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_SPONSORID", referencedColumnName="PK_SPONSORID")
     * })
     */
    private $fkSponsorid;

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
     * Get pkSowid
     *
     * @return integer
     */
    public function getPkSowid()
    {
        return $this->pkSowid;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return StudentOnWaiver
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
     * @return StudentOnWaiver
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
     * Set otherReason
     *
     * @param string $otherReason
     *
     * @return StudentOnWaiver
     */
    public function setOtherReason($otherReason)
    {
        $this->otherReason = $otherReason;

        return $this;
    }

    /**
     * Get otherReason
     *
     * @return string
     */
    public function getOtherReason()
    {
        return $this->otherReason;
    }

    /**
     * Set capturedBy
     *
     * @param \Application\Entity\Staff $capturedBy
     *
     * @return StudentOnWaiver
     */
    public function setCapturedBy(\Application\Entity\Staff $capturedBy = null)
    {
        $this->capturedBy = $capturedBy;

        return $this;
    }

    /**
     * Get capturedBy
     *
     * @return \Application\Entity\Staff
     */
    public function getCapturedBy()
    {
        return $this->capturedBy;
    }

    /**
     * Set fkSponsorid
     *
     * @param \Application\Entity\Sponsor $fkSponsorid
     *
     * @return StudentOnWaiver
     */
    public function setFkSponsorid(\Application\Entity\Sponsor $fkSponsorid = null)
    {
        $this->fkSponsorid = $fkSponsorid;

        return $this;
    }

    /**
     * Get fkSponsorid
     *
     * @return \Application\Entity\Sponsor
     */
    public function getFkSponsorid()
    {
        return $this->fkSponsorid;
    }

    /**
     * Set fkStudentid
     *
     * @param \Application\Entity\Student $fkStudentid
     *
     * @return StudentOnWaiver
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
