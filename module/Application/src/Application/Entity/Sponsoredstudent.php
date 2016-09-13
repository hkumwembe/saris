<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sponsoredstudent
 *
 * @ORM\Table(name="sponsoredstudent", indexes={@ORM\Index(name="sponsor_id", columns={"FK_SPONSORID"}), @ORM\Index(name="sponsoredstudent", columns={"FK_STUDENTPROGRAMID"})})
 * @ORM\Entity
 */
class Sponsoredstudent
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_SSID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkSsid;

    /**
     * @var \Application\Entity\Studentprogram
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Studentprogram")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_STUDENTPROGRAMID", referencedColumnName="PK_STUDENTPROGRAMID")
     * })
     */
    private $fkStudentprogramid;

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
     * Get pkSsid
     *
     * @return integer
     */
    public function getPkSsid()
    {
        return $this->pkSsid;
    }

    /**
     * Set fkStudentprogramid
     *
     * @param \Application\Entity\Studentprogram $fkStudentprogramid
     *
     * @return Sponsoredstudent
     */
    public function setFkStudentprogramid(\Application\Entity\Studentprogram $fkStudentprogramid = null)
    {
        $this->fkStudentprogramid = $fkStudentprogramid;

        return $this;
    }

    /**
     * Get fkStudentprogramid
     *
     * @return \Application\Entity\Studentprogram
     */
    public function getFkStudentprogramid()
    {
        return $this->fkStudentprogramid;
    }

    /**
     * Set fkSponsorid
     *
     * @param \Application\Entity\Sponsor $fkSponsorid
     *
     * @return Sponsoredstudent
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
}
