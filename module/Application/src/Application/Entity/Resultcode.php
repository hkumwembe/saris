<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Resultcode
 *
 * @ORM\Table(name="resultcode")
 * @ORM\Entity
 */
class Resultcode
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_RESULTCODE", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkResultcode;

    /**
     * @var string
     *
     * @ORM\Column(name="CODE", type="string", length=4, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRIPTION", type="string", length=200, nullable=true)
     */
    private $description;



    /**
     * Get pkResultcode
     *
     * @return integer
     */
    public function getPkResultcode()
    {
        return $this->pkResultcode;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Resultcode
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Resultcode
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
