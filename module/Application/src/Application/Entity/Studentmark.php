<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Studentmark
 *
 * @ORM\Table(name="studentmark", indexes={@ORM\Index(name="classmoduleidfk", columns={"FK_AIID"}), @ORM\Index(name="studentmarkid", columns={"FK_STUDENTCLASSID"}), @ORM\Index(name="uploadeby", columns={"UPLOADBY"}), @ORM\Index(name="gradeflow_fk", columns={"MARK_LEVEL"})})
 * @ORM\Entity
 */
class Studentmark
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_SMID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkSmid;

    /**
     * @var integer
     *
     * @ORM\Column(name="MARK", type="integer", nullable=true)
     */
    private $mark;

    /**
     * @var string
     *
     * @ORM\Column(name="PUBLISH_STATUS", type="text", nullable=true)
     */
    private $publishStatus = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="EXAMDATE", type="datetime", nullable=true)
     */
    private $examdate;

    /**
     * @var \Application\Entity\Assessmentitem
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Assessmentitem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_AIID", referencedColumnName="PK_AIID")
     * })
     */
    private $fkAiid;

    /**
     * @var \Application\Entity\Gradeflow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Gradeflow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="MARK_LEVEL", referencedColumnName="PK_GRADEFLOWID")
     * })
     */
    private $markLevel;

    /**
     * @var \Application\Entity\Studentclass
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Studentclass")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_STUDENTCLASSID", referencedColumnName="PK_STUDENTCLASSID")
     * })
     */
    private $fkStudentclassid;

    /**
     * @var \Application\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="UPLOADBY", referencedColumnName="PK_USERID")
     * })
     */
    private $uploadby;



    /**
     * Get pkSmid
     *
     * @return integer
     */
    public function getPkSmid()
    {
        return $this->pkSmid;
    }

    /**
     * Set mark
     *
     * @param integer $mark
     *
     * @return Studentmark
     */
    public function setMark($mark)
    {
        $this->mark = $mark;

        return $this;
    }

    /**
     * Get mark
     *
     * @return integer
     */
    public function getMark()
    {
        return $this->mark;
    }

    /**
     * Set publishStatus
     *
     * @param string $publishStatus
     *
     * @return Studentmark
     */
    public function setPublishStatus($publishStatus)
    {
        $this->publishStatus = $publishStatus;

        return $this;
    }

    /**
     * Get publishStatus
     *
     * @return string
     */
    public function getPublishStatus()
    {
        return $this->publishStatus;
    }

    /**
     * Set examdate
     *
     * @param \DateTime $examdate
     *
     * @return Studentmark
     */
    public function setExamdate($examdate)
    {
        $this->examdate = $examdate;

        return $this;
    }

    /**
     * Get examdate
     *
     * @return \DateTime
     */
    public function getExamdate()
    {
        return $this->examdate;
    }

    /**
     * Set fkAiid
     *
     * @param \Application\Entity\Assessmentitem $fkAiid
     *
     * @return Studentmark
     */
    public function setFkAiid(\Application\Entity\Assessmentitem $fkAiid = null)
    {
        $this->fkAiid = $fkAiid;

        return $this;
    }

    /**
     * Get fkAiid
     *
     * @return \Application\Entity\Assessmentitem
     */
    public function getFkAiid()
    {
        return $this->fkAiid;
    }

    /**
     * Set markLevel
     *
     * @param \Application\Entity\Gradeflow $markLevel
     *
     * @return Studentmark
     */
    public function setMarkLevel(\Application\Entity\Gradeflow $markLevel = null)
    {
        $this->markLevel = $markLevel;

        return $this;
    }

    /**
     * Get markLevel
     *
     * @return \Application\Entity\Gradeflow
     */
    public function getMarkLevel()
    {
        return $this->markLevel;
    }

    /**
     * Set fkStudentclassid
     *
     * @param \Application\Entity\Studentclass $fkStudentclassid
     *
     * @return Studentmark
     */
    public function setFkStudentclassid(\Application\Entity\Studentclass $fkStudentclassid = null)
    {
        $this->fkStudentclassid = $fkStudentclassid;

        return $this;
    }

    /**
     * Get fkStudentclassid
     *
     * @return \Application\Entity\Studentclass
     */
    public function getFkStudentclassid()
    {
        return $this->fkStudentclassid;
    }

    /**
     * Set uploadby
     *
     * @param \Application\Entity\User $uploadby
     *
     * @return Studentmark
     */
    public function setUploadby(\Application\Entity\User $uploadby = null)
    {
        $this->uploadby = $uploadby;

        return $this;
    }

    /**
     * Get uploadby
     *
     * @return \Application\Entity\User
     */
    public function getUploadby()
    {
        return $this->uploadby;
    }
}
