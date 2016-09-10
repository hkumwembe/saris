<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Exampaper
 *
 * @ORM\Table(name="exampaper")
 * @ORM\Entity
 */
class Exampaper
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_PAPERID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkPaperid;

    /**
     * @var string
     *
     * @ORM\Column(name="PAPER_NAME", type="string", length=45, nullable=false)
     */
    private $paperName;



    /**
     * Get pkPaperid
     *
     * @return integer
     */
    public function getPkPaperid()
    {
        return $this->pkPaperid;
    }

    /**
     * Set paperName
     *
     * @param string $paperName
     *
     * @return Exampaper
     */
    public function setPaperName($paperName)
    {
        $this->paperName = $paperName;

        return $this;
    }

    /**
     * Get paperName
     *
     * @return string
     */
    public function getPaperName()
    {
        return $this->paperName;
    }
}
