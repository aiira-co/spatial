<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Usertransaction
 *
 * @ORM\Table(name="UserTransaction")
 * @ORM\Entity
 */
class Usertransaction
{
    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=11, scale=2, nullable=false)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="charges", type="decimal", precision=11, scale=2, nullable=false)
     */
    private $charges;

    /**
     * @var string
     *
     * @ORM\Column(name="amountAfterCharges", type="decimal", precision=11, scale=2, nullable=false)
     */
    private $amountaftercharges;

    /**
     * @var string
     *
     * @ORM\Column(name="transactionId", type="text", length=65535, nullable=false)
     */
    private $transactionid;

    /**
     * @var int
     *
     * @ORM\Column(name="responseCode", type="integer", nullable=false)
     */
    private $responsecode;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="clientReference", type="string", length=32, nullable=false)
     */
    private $clientreference;

    /**
     * @var string
     *
     * @ORM\Column(name="externalTransactionId", type="string", length=32, nullable=false)
     */
    private $externaltransactionid;

    /**
     * @var string
     *
     * @ORM\Column(name="amountCharged", type="decimal", precision=11, scale=2, nullable=false)
     */
    private $amountcharged;

    /**
     * @var int
     *
     * @ORM\Column(name="subscriptionId", type="integer", nullable=false)
     */
    private $subscriptionid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $date = 'CURRENT_TIMESTAMP';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set amount.
     *
     * @param string $amount
     *
     * @return Usertransaction
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
     * Set charges.
     *
     * @param string $charges
     *
     * @return Usertransaction
     */
    public function setCharges($charges)
    {
        $this->charges = $charges;

        return $this;
    }

    /**
     * Get charges.
     *
     * @return string
     */
    public function getCharges()
    {
        return $this->charges;
    }

    /**
     * Set amountaftercharges.
     *
     * @param string $amountaftercharges
     *
     * @return Usertransaction
     */
    public function setAmountaftercharges($amountaftercharges)
    {
        $this->amountaftercharges = $amountaftercharges;

        return $this;
    }

    /**
     * Get amountaftercharges.
     *
     * @return string
     */
    public function getAmountaftercharges()
    {
        return $this->amountaftercharges;
    }

    /**
     * Set transactionid.
     *
     * @param string $transactionid
     *
     * @return Usertransaction
     */
    public function setTransactionid($transactionid)
    {
        $this->transactionid = $transactionid;

        return $this;
    }

    /**
     * Get transactionid.
     *
     * @return string
     */
    public function getTransactionid()
    {
        return $this->transactionid;
    }

    /**
     * Set responsecode.
     *
     * @param int $responsecode
     *
     * @return Usertransaction
     */
    public function setResponsecode($responsecode)
    {
        $this->responsecode = $responsecode;

        return $this;
    }

    /**
     * Get responsecode.
     *
     * @return int
     */
    public function getResponsecode()
    {
        return $this->responsecode;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Usertransaction
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set clientreference.
     *
     * @param string $clientreference
     *
     * @return Usertransaction
     */
    public function setClientreference($clientreference)
    {
        $this->clientreference = $clientreference;

        return $this;
    }

    /**
     * Get clientreference.
     *
     * @return string
     */
    public function getClientreference()
    {
        return $this->clientreference;
    }

    /**
     * Set externaltransactionid.
     *
     * @param string $externaltransactionid
     *
     * @return Usertransaction
     */
    public function setExternaltransactionid($externaltransactionid)
    {
        $this->externaltransactionid = $externaltransactionid;

        return $this;
    }

    /**
     * Get externaltransactionid.
     *
     * @return string
     */
    public function getExternaltransactionid()
    {
        return $this->externaltransactionid;
    }

    /**
     * Set amountcharged.
     *
     * @param string $amountcharged
     *
     * @return Usertransaction
     */
    public function setAmountcharged($amountcharged)
    {
        $this->amountcharged = $amountcharged;

        return $this;
    }

    /**
     * Get amountcharged.
     *
     * @return string
     */
    public function getAmountcharged()
    {
        return $this->amountcharged;
    }

    /**
     * Set subscriptionid.
     *
     * @param int $subscriptionid
     *
     * @return Usertransaction
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
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return Usertransaction
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
