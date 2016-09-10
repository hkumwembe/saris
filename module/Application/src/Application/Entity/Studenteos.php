<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Studenteos
 *
 * @ORM\Table(name="studenteos", indexes={@ORM\Index(name="resultcodesystem", columns={"RESULTCODE_SYSTEM"}), @ORM\Index(name="resultcodedept", columns={"RESULTCODE_DEPT"}), @ORM\Index(name="resultcodefaculty", columns={"RESULTCODE_FACULTY"}), @ORM\Index(name="resultcodecollege", columns={"RESULTCODE_COLLEGE"}), @ORM\Index(name="studentaverage", columns={"FK_STUDENTCLASSID"})})
 * @ORM\Entity
 */
class Studenteos
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_EOSID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkEosid;

    /**
     * @var float
     *
     * @ORM\Column(name="EOSAVERAGE_SYSTEM", type="float", precision=10, scale=0, nullable=true)
     */
    private $eosaverageSystem;

    /**
     * @var float
     *
     * @ORM\Column(name="EOSAVERAGE_DEPT", type="float", precision=10, scale=0, nullable=true)
     */
    private $eosaverageDept;

    /**
     * @var float
     *
     * @ORM\Column(name="EOSAVERAGE_FACULTY", type="float", precision=10, scale=0, nullable=true)
     */
    private $eosaverageFaculty;

    /**
     * @var float
     *
     * @ORM\Column(name="EOSAVERAGE_COLLEGE", type="float", precision=10, scale=0, nullable=true)
     */
    private $eosaverageCollege;

    /**
     * @var \Application\Entity\Resultcode
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Resultcode")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="RESULTCODE_COLLEGE", referencedColumnName="PK_RESULTCODE")
     * })
     */
    private $resultcodeCollege;

    /**
     * @var \Application\Entity\Resultcode
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Resultcode")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="RESULTCODE_DEPT", referencedColumnName="PK_RESULTCODE")
     * })
     */
    private $resultcodeDept;

    /**
     * @var \Application\Entity\Resultcode
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Resultcode")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="RESULTCODE_FACULTY", referencedColumnName="PK_RESULTCODE")
     * })
     */
    private $resultcodeFaculty;

    /**
     * @var \Application\Entity\Resultcode
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Resultcode")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="RESULTCODE_SYSTEM", referencedColumnName="PK_RESULTCODE")
     * })
     */
    private $resultcodeSystem;

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
     * Get pkEosid
     *
     * @return integer
     */
    public function getPkEosid()
    {
        return $this->pkEosid;
    }

    /**
     * Set eosaverageSystem
     *
     * @param float $eosaverageSystem
     *
     * @return Studenteos
     */
    public function setEosaverageSystem($eosaverageSystem)
    {
        $this->eosaverageSystem = $eosaverageSystem;

        return $this;
    }

    /**
     * Get eosaverageSystem
     *
     * @return float
     */
    public function getEosaverageSystem()
    {
        return $this->eosaverageSystem;
    }

    /**
     * Set eosaverageDept
     *
     * @param float $eosaverageDept
     *
     * @return Studenteos
     */
    public function setEosaverageDept($eosaverageDept)
    {
        $this->eosaverageDept = $eosaverageDept;

        return $this;
    }

    /**
     * Get eosaverageDept
     *
     * @return float
     */
    public function getEosaverageDept()
    {
        return $this->eosaverageDept;
    }

    /**
     * Set eosaverageFaculty
     *
     * @param float $eosaverageFaculty
     *
     * @return Studenteos
     */
    public function setEosaverageFaculty($eosaverageFaculty)
    {
        $this->eosaverageFaculty = $eosaverageFaculty;

        return $this;
    }

    /**
     * Get eosaverageFaculty
     *
     * @return float
     */
    public function getEosaverageFaculty()
    {
        return $this->eosaverageFaculty;
    }

    /**
     * Set eosaverageCollege
     *
     * @param float $eosaverageCollege
     *
     * @return Studenteos
     */
    public function setEosaverageCollege($eosaverageCollege)
    {
        $this->eosaverageCollege = $eosaverageCollege;

        return $this;
    }

    /**
     * Get eosaverageCollege
     *
     * @return float
     */
    public function getEosaverageCollege()
    {
        return $this->eosaverageCollege;
    }

    /**
     * Set resultcodeCollege
     *
     * @param \Application\Entity\Resultcode $resultcodeCollege
     *
     * @return Studenteos
     */
    public function setResultcodeCollege(\Application\Entity\Resultcode $resultcodeCollege = null)
    {
        $this->resultcodeCollege = $resultcodeCollege;

        return $this;
    }

    /**
     * Get resultcodeCollege
     *
     * @return \Application\Entity\Resultcode
     */
    public function getResultcodeCollege()
    {
        return $this->resultcodeCollege;
    }

    /**
     * Set resultcodeDept
     *
     * @param \Application\Entity\Resultcode $resultcodeDept
     *
     * @return Studenteos
     */
    public function setResultcodeDept(\Application\Entity\Resultcode $resultcodeDept = null)
    {
        $this->resultcodeDept = $resultcodeDept;

        return $this;
    }

    /**
     * Get resultcodeDept
     *
     * @return \Application\Entity\Resultcode
     */
    public function getResultcodeDept()
    {
        return $this->resultcodeDept;
    }

    /**
     * Set resultcodeFaculty
     *
     * @param \Application\Entity\Resultcode $resultcodeFaculty
     *
     * @return Studenteos
     */
    public function setResultcodeFaculty(\Application\Entity\Resultcode $resultcodeFaculty = null)
    {
        $this->resultcodeFaculty = $resultcodeFaculty;

        return $this;
    }

    /**
     * Get resultcodeFaculty
     *
     * @return \Application\Entity\Resultcode
     */
    public function getResultcodeFaculty()
    {
        return $this->resultcodeFaculty;
    }

    /**
     * Set resultcodeSystem
     *
     * @param \Application\Entity\Resultcode $resultcodeSystem
     *
     * @return Studenteos
     */
    public function setResultcodeSystem(\Application\Entity\Resultcode $resultcodeSystem = null)
    {
        $this->resultcodeSystem = $resultcodeSystem;

        return $this;
    }

    /**
     * Get resultcodeSystem
     *
     * @return \Application\Entity\Resultcode
     */
    public function getResultcodeSystem()
    {
        return $this->resultcodeSystem;
    }

    /**
     * Set fkStudentclassid
     *
     * @param \Application\Entity\Studentclass $fkStudentclassid
     *
     * @return Studenteos
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
