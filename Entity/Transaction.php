<?php

namespace Av\CMCICConnector\Entity;

/**
 * Transaction
 *
 */
class Transaction
{
    const STATUS_UNKNOWN                   = 'unknown_error';
    const STATUS_PAYMENT_SUCCESS           = 'payment_success';
    const STATUS_ORDER_CANCELED            = 'order_canceled';
    const STATUS_RECURSION_STOPPED         = 'recursion_stopped';
    const STATUS_UNKNOWN_ORDER             = 'unknown_order';
    const STATUS_EXPIRED_ORDER             = 'expired_order';
    const STATUS_BURNED_ORDER              = 'burned_order';
    const STATUS_AUTHORISATION_REFUSED     = 'authorisation_refused';
    const STATUS_PAYMENT_ALLREADY_CANCELED = 'payment_allready_canceled';
    const STATUS_PAYMENT_ALLREADY_ACCEPTED = 'payment_allready_accepted';
    const STATUS_MAC_ERROR                 = 'mac_error';
    const STATUS_VERIFICATION_ERROR        = 'verification_error';
    const STATUS_PARAMETER_ERROR           = 'parameter_error';
    const STATUS_AMOUNT_ERROR              = 'amount_error';
    const STATUS_UNIDENTIFIED_SELLER       = 'unidentified_seller';
    const STATUS_PENDING                   = 'pending';
    const STATUS_DATE_ERROR                = 'date_error';
    const STATUS_OTHER_PENDING             = 'other_pending';
    const STATUS_TECHNICAL_ERROR           = 'technical_error';

    protected $status = self::STATUS_PENDING;
    protected $amount;
    protected $creationDate;
    protected $bill;
    protected $cardAuthorisation;

    public function __construct()
    {
        $this->creationDate = new \DateTime();
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Transaction
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Transaction
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Transaction
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set bill
     *
     * @param \Av\CMCICConnector\Entity\Bill $bill
     *
     * @return Transaction
     */
    public function setBill(\Av\CMCICConnector\Entity\Bill $bill = null)
    {
        $this->bill = $bill;

        return $this;
    }

    /**
     * Get bill
     *
     * @return \Av\CMCICConnector\Entity\Bill
     */
    public function getBill()
    {
        return $this->bill;
    }

    /**
     * Set cardAuthorisation
     *
     * @param \Av\CMCICConnector\Entity\CardAuthorisation $cardAuthorisation
     *
     * @return Transaction
     */
    public function setCardAuthorisation(\Av\CMCICConnector\Entity\CardAuthorisation $cardAuthorisation = null)
    {
        $this->cardAuthorisation = $cardAuthorisation;

        return $this;
    }

    /**
     * Get cardAuthorisation
     *
     * @return \Av\CMCICConnector\Entity\CardAuthorisation
     */
    public function getCardAuthorisation()
    {
        return $this->cardAuthorisation;
    }
}
