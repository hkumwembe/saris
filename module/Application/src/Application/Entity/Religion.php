<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Religion
 *
 * @ORM\Table(name="religion")
 * @ORM\Entity
 */
class Religion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_RELIGIONID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkReligionid;

    /**
     * @var string
     *
     * @ORM\Column(name="RELIGION_NAME", type="string", length=255, nullable=true)
     */
    private $religionName;



    /**
     * Get pkReligionid
     *
     * @return integer
     */
    public function getPkReligionid()
    {
        return $this->pkReligionid;
    }

    /**
     * Set religionName
     *
     * @param string $religionName
     *
     * @return Religion
     */
    public function setReligionName($religionName)
    {
        $this->religionName = $religionName;

        return $this;
    }

    /**
     * Get religionName
     *
     * @return string
     */
    public function getReligionName()
    {
        return $this->religionName;
    }
}
