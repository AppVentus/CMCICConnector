<?php

namespace Av\CMCICConnector\Entity;


/**
 * Card
 */
class Card
{
    protected $cardNumber;

    protected $validityMonth;
    protected $validityYear;

    protected $confirmationNumber;

    /**
     * Set cardNumber
     *
     * @param string $cardNumber
     *
     * @return Card
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;

        return $this;
    }

    /**
     * Get cardNumber
     *
     * @return string
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * Set validityMonth
     *
     * @param string $validityMonth
     *
     * @return Card
     */
    public function setValidityMonth($validityMonth)
    {
        $this->validityMonth = $validityMonth;

        return $this;
    }

    /**
     * Get validityMonth
     *
     * @return string
     */
    public function getValidityMonth()
    {
        return $this->validityMonth;
    }

    /**
     * Set validityYear
     *
     * @param string $validityYear
     *
     * @return Card
     */
    public function setValidityYear($validityYear)
    {
        $this->validityYear = $validityYear;

        return $this;
    }

    /**
     * Get validityYear
     *
     * @return string
     */
    public function getValidityYear()
    {
        return $this->validityYear;
    }

    /**
     * Set confirmationNumber
     *
     * @param string $confirmationNumber
     *
     * @return Card
     */
    public function setConfirmationNumber($confirmationNumber)
    {
        $this->confirmationNumber = $confirmationNumber;

        return $this;
    }

    /**
     * Get confirmationNumber
     *
     * @return string
     */
    public function getConfirmationNumber()
    {
        return $this->confirmationNumber;
    }
}
