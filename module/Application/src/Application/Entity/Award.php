<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Award
 *
 * @ORM\Table(name="award")
 * @ORM\Entity
 */
class Award
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_AWARDID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkAwardid;

    /**
     * @var string
     *
     * @ORM\Column(name="AWARD", type="string", length=45, nullable=false)
     */
    private $award;

    /**
     * @var string
     *
     * @ORM\Column(name="AWARD_CODE", type="string", length=7, nullable=true)
     */
    private $awardCode;



    /**
     * Get pkAwardid
     *
     * @return integer
     */
    public function getPkAwardid()
    {
        return $this->pkAwardid;
    }

    /**
     * Set award
     *
     * @param string $award
     *
     * @return Award
     */
    public function setAward($award)
    {
        $this->award = $award;

        return $this;
    }

    /**
     * Get award
     *
     * @return string
     */
    public function getAward()
    {
        return $this->award;
    }

    /**
     * Set awardCode
     *
     * @param string $awardCode
     *
     * @return Award
     */
    public function setAwardCode($awardCode)
    {
        $this->awardCode = $awardCode;

        return $this;
    }

    /**
     * Get awardCode
     *
     * @return string
     */
    public function getAwardCode()
    {
        return $this->awardCode;
    }
}
