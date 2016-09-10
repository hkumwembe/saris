<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Maritalstatus
 *
 * @ORM\Table(name="maritalstatus")
 * @ORM\Entity
 */
class Maritalstatus
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_MARITALSTATUSID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkMaritalstatusid;

    /**
     * @var string
     *
     * @ORM\Column(name="STATUS_TITLE", type="string", length=60, nullable=true)
     */
    private $statusTitle;



    /**
     * Get pkMaritalstatusid
     *
     * @return integer
     */
    public function getPkMaritalstatusid()
    {
        return $this->pkMaritalstatusid;
    }

    /**
     * Set statusTitle
     *
     * @param string $statusTitle
     *
     * @return Maritalstatus
     */
    public function setStatusTitle($statusTitle)
    {
        $this->statusTitle = $statusTitle;

        return $this;
    }

    /**
     * Get statusTitle
     *
     * @return string
     */
    public function getStatusTitle()
    {
        return $this->statusTitle;
    }
}