<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Subscriptionprice
 *
 * @ORM\Table(name="SubscriptionPrice")
 * @ORM\Entity
 */
class Subscriptionprice
{
    /**
     * @var int
     *
     * @ORM\Column(name="subscriptionId", type="integer", nullable=false)
     */
    private $subscriptionid;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modifiedDate", type="datetime", nullable=false)
     */
    private $modifieddate;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set subscriptionid.
     *
     * @param int $subscriptionid
     *
     * @return Subscriptionprice
     */
    public function setSubscriptionid($subscriptionid)
    {
        $this->subscriptionid = $subscriptionid;

        return $this;
    }

    /**
     * Get subscriptionid.
     *
     * @return int
     */
    public function getSubscriptionid()
    {
        return $this->subscriptionid;
    }

    /**
     * Set amount.
     *
     * @param string $amount
     *
     * @return Subscriptionprice
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount.
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return Subscriptionprice
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
     * Set modifieddate.
     *
     * @param \DateTime $modifieddate
     *
     * @return Subscriptionprice
     */
    public function setModifieddate($modifieddate)
    {
        $this->modifieddate = $modifieddate;

        return $this;
    }

    /**
     * Get modifieddate.
     *
     * @return \DateTime
     */
    public function getModifieddate()
    {
        return $this->modifieddate;
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
