<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Table(name="question", indexes={@ORM\Index(name="assessmentquestion", columns={"FK_AIID"}), @ORM\Index(name="staffquestion", columns={"FK_STAFFID"}), @ORM\Index(name="exampaper_idx", columns={"FK_PAPERID"})})
 * @ORM\Entity
 */
class Question
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_QID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkQid;

    /**
     * @var string
     *
     * @ORM\Column(name="QUESTION", type="text", length=65535, nullable=true)
     */
    private $question;

    /**
     * @var integer
     *
     * @ORM\Column(name="QUESTION_NUMBER", type="integer", nullable=true)
     */
    private $questionNumber;

    /**
     * @var float
     *
     * @ORM\Column(name="MARK_OUT_OF", type="float", precision=10, scale=0, nullable=true)
     */
    private $markOutOf;

    /**
     * @var string
     *
     * @ORM\Column(name="IN_HAND", type="text", nullable=true)
     */
    private $inHand = '1';

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
     * @var \Application\Entity\Exampaper
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Exampaper")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_PAPERID", referencedColumnName="PK_PAPERID")
     * })
     */
    private $fkPaperid;

    /**
     * @var \Application\Entity\Staff
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Staff")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_STAFFID", referencedColumnName="PK_STAFFID")
     * })
     */
    private $fkStaffid;



    /**
     * Get pkQid
     *
     * @return integer
     */
    public function getPkQid()
    {
        return $this->pkQid;
    }

    /**
     * Set question
     *
     * @param string $question
     *
     * @return Question
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set questionNumber
     *
     * @param integer $questionNumber
     *
     * @return Question
     */
    public function setQuestionNumber($questionNumber)
    {
        $this->questionNumber = $questionNumber;

        return $this;
    }

    /**
     * Get questionNumber
     *
     * @return integer
     */
    public function getQuestionNumber()
    {
        return $this->questionNumber;
    }

    /**
     * Set markOutOf
     *
     * @param float $markOutOf
     *
     * @return Question
     */
    public function setMarkOutOf($markOutOf)
    {
        $this->markOutOf = $markOutOf;

        return $this;
    }

    /**
     * Get markOutOf
     *
     * @return float
     */
    public function getMarkOutOf()
    {
        return $this->markOutOf;
    }

    /**
     * Set inHand
     *
     * @param string $inHand
     *
     * @return Question
     */
    public function setInHand($inHand)
    {
        $this->inHand = $inHand;

        return $this;
    }

    /**
     * Get inHand
     *
     * @return string
     */
    public function getInHand()
    {
        return $this->inHand;
    }

    /**
     * Set fkAiid
     *
     * @param \Application\Entity\Assessmentitem $fkAiid
     *
     * @return Question
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
     * Set fkPaperid
     *
     * @param \Application\Entity\Exampaper $fkPaperid
     *
     * @return Question
     */
    public function setFkPaperid(\Application\Entity\Exampaper $fkPaperid = null)
    {
        $this->fkPaperid = $fkPaperid;

        return $this;
    }

    /**
     * Get fkPaperid
     *
     * @return \Application\Entity\Exampaper
     */
    public function getFkPaperid()
    {
        return $this->fkPaperid;
    }

    /**
     * Set fkStaffid
     *
     * @param \Application\Entity\Staff $fkStaffid
     *
     * @return Question
     */
    public function setFkStaffid(\Application\Entity\Staff $fkStaffid = null)
    {
        $this->fkStaffid = $fkStaffid;

        return $this;
    }

    /**
     * Get fkStaffid
     *
     * @return \Application\Entity\Staff
     */
    public function getFkStaffid()
    {
        return $this->fkStaffid;
    }
}
