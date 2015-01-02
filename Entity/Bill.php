<?php

namespace Av\CMCICConnector\Entity;

/**
 * Bill
 *
 */
class Bill
{
    const STATUS_PAYED   = 'payed';
    const STATUS_PENDING = 'pending';

    protected $status = self::STATUS_PENDING;
    protected $value;

    protected $details;

    protected $creationDate;

    protected $charged;
    protected $transactions;

    public function __construct()
    {
        $this->creationDate = new \DateTime();
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Bill
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
     * Set value
     *
     * @param integer $value
     *
     * @return Bill
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set details
     *
     * @param string $details
     *
     * @return Bill
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Bill
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
     * Add transactions
     *
     * @param Av\CMCICConnector\Transaction $transaction
     *
     * @return Bill
     */
    public function addTransaction(Av\CMCICConnector\Transaction $transaction)
    {
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * Remove transactions
     *
     * @param Av\CMCICConnector\Transaction $transaction
     */
    public function removeTransaction(Av\CMCICConnector\Transaction $transaction)
    {
        $this->transactions->removeElement($transaction);
    }

    /**
     * Get transactions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
}
