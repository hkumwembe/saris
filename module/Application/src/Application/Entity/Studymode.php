<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Studymode
 *
 * @ORM\Table(name="studymode")
 * @ORM\Entity
 */
class Studymode
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_STUDYMODEID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkStudymodeid;

    /**
     * @var string
     *
     * @ORM\Column(name="TITLE", type="string", length=50, nullable=false)
     */
    private $title;



    /**
     * Get pkStudymodeid
     *
     * @return integer
     */
    public function getPkStudymodeid()
    {
        return $this->pkStudymodeid;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Studymode
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
