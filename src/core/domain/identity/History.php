<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * History
 *
 * @ORM\Table(name="History")
 * @ORM\Entity
 */
class History
{
    /**
     * @var int
     *
     * @ORM\Column(name="userId", type="integer", nullable=false)
     */
    private $userid;

    /**
     * @var int
     *
     * @ORM\Column(name="mediaId", type="integer", nullable=false)
     */
    private $mediaid;

    /**
     * @var string
     *
     * @ORM\Column(name="lastLength", type="string", length=10, nullable=false)
     */
    private $lastlength;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set userid.
     *
     * @param int $userid
     *
     * @return History
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid.
     *
     * @return int
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Set mediaid.
     *
     * @param int $mediaid
     *
     * @return History
     */
    public function setMediaid($mediaid)
    {
        $this->mediaid = $mediaid;

        return $this;
    }

    /**
     * Get mediaid.
     *
     * @return int
     */
    public function getMediaid()
    {
        return $this->mediaid;
    }

    /**
     * Set lastlength.
     *
     * @param string $lastlength
     *
     * @return History
     */
    public function setLastlength($lastlength)
    {
        $this->lastlength = $lastlength;

        return $this;
    }

    /**
     * Get lastlength.
     *
     * @return string
     */
    public function getLastlength()
    {
        return $this->lastlength;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return History
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
