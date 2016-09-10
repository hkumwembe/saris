<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Studentmodule
 *
 * @ORM\Table(name="studentmodule", indexes={@ORM\Index(name="studentclassid", columns={"FK_STUDENTCLASSID"}), @ORM\Index(name="studentclassmodule", columns={"FK_CLASSMODULEID"})})
 * @ORM\Entity
 */
class Studentmodule
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_STUDENTMODULE", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkStudentmodule;

    /**
     * @var string
     *
     * @ORM\Column(name="STUDYMODE", type="text", nullable=true)
     */
    private $studymode;

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
     * @var \Application\Entity\Classmodule
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Classmodule")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_CLASSMODULEID", referencedColumnName="PK_CLASSMODULEID")
     * })
     */
    private $fkClassmoduleid;



    /**
     * Get pkStudentmodule
     *
     * @return integer
     */
    public function getPkStudentmodule()
    {
        return $this->pkStudentmodule;
    }

    /**
     * Set studymode
     *
     * @param string $studymode
     *
     * @return Studentmodule
     */
    public function setStudymode($studymode)
    {
        $this->studymode = $studymode;

        return $this;
    }

    /**
     * Get studymode
     *
     * @return string
     */
    public function getStudymode()
    {
        return $this->studymode;
    }

    /**
     * Set fkStudentclassid
     *
     * @param \Application\Entity\Studentclass $fkStudentclassid
     *
     * @return Studentmodule
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
     * Set fkClassmoduleid
     *
     * @param \Application\Entity\Classmodule $fkClassmoduleid
     *
     * @return Studentmodule
     */
    public function setFkClassmoduleid(\Application\Entity\Classmodule $fkClassmoduleid = null)
    {
        $this->fkClassmoduleid = $fkClassmoduleid;

        return $this;
    }

    /**
     * Get fkClassmoduleid
     *
     * @return \Application\Entity\Classmodule
     */
    public function getFkClassmoduleid()
    {
        return $this->fkClassmoduleid;
    }
}
