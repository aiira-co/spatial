<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Download
 *
 * @ORM\Table(name="Download")
 * @ORM\Entity
 */
class Download
{
    /**
     * @var int
     *
     * @ORM\Column(name="userId", type="integer", nullable=false)
     */
    private $userid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="mediaId", type="date", nullable=false)
     */
    private $mediaid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="count", type="integer", nullable=false)
     */
    private $count;

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
     * @return Download
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
     * @param \DateTime $mediaid
     *
     * @return Download
     */
    public function setMediaid($mediaid)
    {
        $this->mediaid = $mediaid;

        return $this;
    }

    /**
     * Get mediaid.
     *
     * @return \DateTime
     */
    public function getMediaid()
    {
        return $this->mediaid;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return Download
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
     * Set count.
     *
     * @param int $count
     *
     * @return Download
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count.
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
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
