<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Questionmark
 *
 * @ORM\Table(name="questionmark", indexes={@ORM\Index(name="studentmark_idx", columns={"FK_STUDENTCLASSID"}), @ORM\Index(name="questionmark_idx", columns={"FK_QID"})})
 * @ORM\Entity
 */
class Questionmark
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_QGID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkQgid;

    /**
     * @var float
     *
     * @ORM\Column(name="MARK", type="float", precision=10, scale=0, nullable=true)
     */
    private $mark;

    /**
     * @var \Application\Entity\Question
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Question")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_QID", referencedColumnName="PK_QID")
     * })
     */
    private $fkQid;

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
     * Get pkQgid
     *
     * @return integer
     */
    public function getPkQgid()
    {
        return $this->pkQgid;
    }

    /**
     * Set mark
     *
     * @param float $mark
     *
     * @return Questionmark
     */
    public function setMark($mark)
    {
        $this->mark = $mark;

        return $this;
    }

    /**
     * Get mark
     *
     * @return float
     */
    public function getMark()
    {
        return $this->mark;
    }

    /**
     * Set fkQid
     *
     * @param \Application\Entity\Question $fkQid
     *
     * @return Questionmark
     */
    public function setFkQid(\Application\Entity\Question $fkQid = null)
    {
        $this->fkQid = $fkQid;

        return $this;
    }

    /**
     * Get fkQid
     *
     * @return \Application\Entity\Question
     */
    public function getFkQid()
    {
        return $this->fkQid;
    }

    /**
     * Set fkStudentclassid
     *
     * @param \Application\Entity\Studentclass $fkStudentclassid
     *
     * @return Questionmark
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
}
