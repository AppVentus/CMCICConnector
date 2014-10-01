<?php

namespace Av\CMCICConnector\Entity;

/**
 * CardAuthorisation
 *
 */
class CardAuthorisation
{
    const STATUS_UNKNOWN                      = 'unknown_error';
    const STATUS_AUTHORISATION_FAILED         = 'authorisation_failed';
    const STATUS_AUTHORISATION_SUCCESS        = 'authorisation_success';
    const STATUS_AUTHORISATION_3DS_PENDING    = 'authorisation_3ds_pending';
    const STATUS_TECHNICAL_ERROR              = 'technical_error';
    const STATUS_UNIDENTIFIED_SELLER          = 'unidentified_seller';
    const STATUS_MAC_ERROR                    = 'mac_error';
    const STATUS_EXPIRED_CARD                 = 'expired_card';
    const STATUS_CARD_NUMBER_ERROR            = 'card_number_error';
    const STATUS_EXPIRED_ORDER                = 'expired_order';
    const STATUS_AMOUNT_ERROR                 = 'amount_error';
    const STATUS_DATE_ERROR                   = 'date_error';
    const STATUS_CVX_ERROR                    = 'cvx_error';
    const STATUS_PAYMENT_ALLREADY_REGISTRED   = 'payment_allready_registred';
    const STATUS_PAYMENT_ALLREADY_ACCEPTED    = 'payment_allready_accepted';
    const STATUS_PAYMENT_ALLREADY_CANCELED    = 'payment_allready_canceled';
    const STATUS_PENDING                      = 'pending';
    const STATUS_BURNED_ORDER                 = 'burned_order';
    const STATUS_PARAMETER_ERROR              = 'parameter_error';
    const STATUS_3DS_ERROR                    = '3ds_error';
    const STATUS_DELAYED_PAYMENT_AMOUNT_ERROR = 'delayed_payment_error';
    const STATUS_DELAYED_PAYMENT_DATE_ERROR   = 'delayed_payment_error';
    const STATUS_PAYMENT_COUNT_ERROR          = 'payment_count_error';
    const STATUS_WRONG_VERSION                = 'wrong_version';
    const STATUS_FILTER_BLOCKED               = 'filter_blocked';

    protected $status = 'pending';
    protected $reference;
    protected $amount;
    protected $creationDate;
    protected $requestMac;
    protected $cardMac;
    protected $cdr;
    protected $cardOrigin;
    protected $comment;
    protected $lastUpdate;
    protected $transactions;
    protected $charged;

    public function __construct()
    {
        $this->creationDate = new \DateTime();
        $this->lastUpdate   = new \DateTime();
        $this->transactions = array();
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return CardAuthorisation
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
     * Set reference
     *
     * @param string $reference
     *
     * @return CardAuthorisation
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return CardAuthorisation
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
     * @return CardAuthorisation
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
     * Set requestMac
     *
     * @param string $requestMac
     *
     * @return CardAuthorisation
     */
    public function setRequestMac($requestMac)
    {
        $this->requestMac = $requestMac;

        return $this;
    }

    /**
     * Get requestMac
     *
     * @return string
     */
    public function getRequestMac()
    {
        return $this->requestMac;
    }

    /**
     * Set cardMac
     *
     * @param string $cardMac
     *
     * @return CardAuthorisation
     */
    public function setCardMac($cardMac)
    {
        $this->cardMac = $cardMac;

        return $this;
    }

    /**
     * Get cardMac
     *
     * @return string
     */
    public function getCardMac()
    {
        return $this->cardMac;
    }

    /**
     * Set cdr
     *
     * @param integer $cdr
     *
     * @return CardAuthorisation
     */
    public function setCdr($cdr)
    {
        $this->cdr = $cdr;

        return $this;
    }

    /**
     * Get cdr
     *
     * @return integer
     */
    public function getCdr()
    {
        return $this->cdr;
    }

    /**
     * Set cardOrigin
     *
     * @param string $cardOrigin
     *
     * @return CardAuthorisation
     */
    public function setCardOrigin($cardOrigin)
    {
        $this->cardOrigin = $cardOrigin;

        return $this;
    }

    /**
     * Get cardOrigin
     *
     * @return string
     */
    public function getCardOrigin()
    {
        return $this->cardOrigin;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return CardAuthorisation
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Add transactions
     *
     * @param \Av\CMCICPaymentBundle\Entity\Transaction $transactions
     *
     * @return CardAuthorisation
     */
    public function addTransaction(\Av\CMCICPaymentBundle\Entity\Transaction $transactions)
    {
        $this->transactions[] = $transactions;

        return $this;
    }

    /**
     * Remove transaction
     *
     * @param \Av\CMCICPaymentBundle\Entity\Transaction $transaction
     */
    public function removeTransaction(\Av\CMCICPaymentBundle\Entity\Transaction $transaction)
    {
        unset($this->transactions[array_search($transaction, $this->transactions)]);
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

    /**
     * Set charged
     *
     * @param \Guest\AppBundle\Entity\Company $charged
     *
     * @return Bill
     */
    public function setCharged($charged)
    {
        $this->charged = $charged;

        return $this;
    }

    /**
     * Get charged
     *
     * @return \Guest\AppBundle\Entity\Company
     */
    public function getCharged()
    {
        return $this->charged;
    }

    /**
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     *
     * @return CardAuthorisation
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }
}
