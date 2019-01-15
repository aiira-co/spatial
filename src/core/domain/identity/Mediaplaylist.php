<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Mediaplaylist
 *
 * @ORM\Table(name="MediaPlaylist")
 * @ORM\Entity
 */
class Mediaplaylist
{
    /**
     * @var int
     *
     * @ORM\Column(name="playlistId", type="integer", nullable=false)
     */
    private $playlistid;

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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var bool
     *
     * @ORM\Column(name="played", type="boolean", nullable=false)
     */
    private $played;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set playlistid.
     *
     * @param int $playlistid
     *
     * @return Mediaplaylist
     */
    public function setPlaylistid($playlistid)
    {
        $this->playlistid = $playlistid;

        return $this;
    }

    /**
     * Get playlistid.
     *
     * @return int
     */
    public function getPlaylistid()
    {
        return $this->playlistid;
    }

    /**
     * Set userid.
     *
     * @param int $userid
     *
     * @return Mediaplaylist
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
     * @return Mediaplaylist
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
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return Mediaplaylist
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
     * Set played.
     *
     * @param bool $played
     *
     * @return Mediaplaylist
     */
    public function setPlayed($played)
    {
        $this->played = $played;

        return $this;
    }

    /**
     * Get played.
     *
     * @return bool
     */
    public function getPlayed()
    {
        return $this->played;
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
