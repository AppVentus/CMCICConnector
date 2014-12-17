<?php

namespace Av\CMCICConnector;

use Av\CMCICConnector\Av\CMCICPaymentBundle\Entity\Bill;
use Av\CMCICConnector\Entity\Card;
use Av\CMCICConnector\Entity\CardAuthorisation;
use Av\CMCICConnector\Entity\Transaction;

class BankConnector
{
    const KEY_VERSION                = 'version';
    const KEY_SOCIETY                = 'societe';
    const KEY_TPE                    = 'TPE';
    const KEY_DATE                   = 'date';
    const KEY_ORDER_DATE             = 'date_commande';
    const KEY_AMOUNT                 = 'montant';
    const KEY_ORDER_AMOUNT           = 'montant_a_capturer';
    const KEY_ALLREADY_ORDER_AMOUNT  = 'montant_deja_capture';
    const KEY_REMAINING_ORDER_AMOUNT = 'montant_restant';
    const KEY_REFERENCE              = 'reference';
    const KEY_RETURN_URL             = 'url_retour';
    const KEY_RETURN_URL_OK          = 'url_retour_ok';
    const KEY_RETURN_URL_KO          = 'url_retour_err';
    const KEY_EMAIL                  = 'mail';
    const KEY_MAC                    = 'MAC';
    const KEY_LANGUAGE               = 'lgue';
    const KEY_COMMENT                = 'text-libre';
    const KEY_VALIDITY_YEAR          = 'annee_validite';
    const KEY_VALIDITY_MONTH         = 'mois_validite';
    const KEY_CARD_NUMBER            = 'numero_carte';
    const KEY_CVX                    = 'cvx';
    const KEY_PAYMENT_COUNT          = 'nbrech';
    const KEY_PAYMENT_DATE_1         = 'dateech1';
    const KEY_PAYMENT_AMOUNT_1       = 'montantech1';
    const KEY_PAYMENT_DATE_2         = 'dateech2';
    const KEY_PAYMENT_AMOUNT_2       = 'montantech2';
    const KEY_PAYMENT_DATE_3         = 'dateech3';
    const KEY_PAYMENT_AMOUNT_3       = 'montantech3';
    const KEY_PAYMENT_DATE_4         = 'dateech4';
    const KEY_PAYMENT_AMOUNT_4       = 'montantech4';

    /**
     * parameters
     *
     * @var array
     */
    protected $parameters;

    /**
     * Contructeur
     *
     * @param Array $parameters security
     */
    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Create a card authorisation entry
     *
     * @param Card    $card    The current card
     * @param integer $value   charged value
     * @param string  $email   charged email
     * @param Charged $charged charged entity
     */
    public function createCardAuthorisation(Card $card, $value, $email, $charged)
    {
        $cardAuthorisation = new $this->parameters['classes']['cardAuthorisation']();
        $cardAuthorisation->setAmount($value);
        $cardAuthorisation->setCreationDate(new \DateTime());
        $cardAuthorisation->setLastUpdate(new \DateTime());
        // $cardAuthorisation->setCharged($charged); //TODO: uncomment it
        $cardAuthorisation->setReference(time());

        $params = array(
            self::KEY_VERSION        => $this->parameters['tpe']['version'],
            self::KEY_SOCIETY        => $this->parameters['tpe']['society'],
            self::KEY_TPE            => $this->parameters['tpe']['tpe_number'],
            self::KEY_DATE           => $cardAuthorisation->getCreationDate()->format('d/m/Y:H:i:s'),
            self::KEY_AMOUNT         => ($cardAuthorisation->getAmount()) . $this->parameters['tpe']['currency'],
            self::KEY_REFERENCE      => $cardAuthorisation->getReference(),
            self::KEY_RETURN_URL     => $this->parameters['tpe']['return_url'],
            self::KEY_RETURN_URL_OK  => $this->parameters['tpe']['return_url_ok'],
            self::KEY_RETURN_URL_KO  => $this->parameters['tpe']['return_url_nok'],
            self::KEY_EMAIL          => $email,
            self::KEY_LANGUAGE       => $this->parameters['tpe']['language'],
            self::KEY_COMMENT        => '',
            self::KEY_VALIDITY_YEAR  => $card->getValidityYear(),
            self::KEY_VALIDITY_MONTH => $card->getValidityMonth(),
            self::KEY_CARD_NUMBER    => $card->getCardNumber(),
            self::KEY_CVX            => $card->getConfirmationNumber()
        );

        $params[self::KEY_MAC] = $this->certifyCardAuthorisation($params, $this->parameters['tpe']['secret']);

        error_log(print_r($params, true));

        $result = $this->parseXmlResponse($this->request($params, $this->parameters['tpe']['api_url_registration']));
        error_log(print_r($result, true));

        if ($result['status'] == 200) {
            if ($result['valid_xml']) {

                $cardAuthorisation->setRequestMac($params[self::KEY_MAC]);
                $cardAuthorisation->setCardMac($result['content']->hpancb);
                $cardAuthorisation->setCdr($result['content']->cdr);
                $cardAuthorisation->setCardOrigin($result['content']->originecb);

                $cardAuthorisation->setStatus($this->getStatusForCode($result['content']->cdr));
            } else {
                $cardAuthorisation->setComment($result['content']['']);

                $cardAuthorisation->setStatus('error_xml');
            }
        } else {
            $cardAuthorisation->setComment($result['content']);

            $cardAuthorisation->setStatus('error_' . $result['status']);
        }

        return $cardAuthorisation;
    }

    public function createTransaction(CardAuthorisation $cardAuthorisation, Bill $bill)
    {
        $transaction = new Transaction();
        $transaction->setBill($bill);
        $transaction->setCardAuthorisation($cardAuthorisation);
        $transaction->setAmount($bill->getValue() / 100);
        $transaction->setCreationDate(new \DateTime());

        $params = array(
            self::KEY_VERSION                => $this->parameters['tpe']['version'],
            self::KEY_TPE                    => $this->parameters['tpe']['tpe_number'],
            self::KEY_DATE                   => $transaction->getCreationDate()->format('d/m/Y:H:i:s'),
            self::KEY_ORDER_DATE             => $cardAuthorisation->getCreationDate()->format('d/m/Y'),
            self::KEY_AMOUNT                 => ($cardAuthorisation->getAmount()) . $this->parameters['tpe']['currency'],
            self::KEY_ORDER_AMOUNT           => ($transaction->getAmount()) . $this->parameters['tpe']['currency'],
            self::KEY_ALLREADY_ORDER_AMOUNT  => '0' . $this->parameters['tpe']['currency'],
            self::KEY_REMAINING_ORDER_AMOUNT => ($cardAuthorisation->getAmount() - $transaction->getAmount()) . $this->parameters['tpe']['currency'],
            self::KEY_REFERENCE              => $cardAuthorisation->getReference(),
            self::KEY_COMMENT                => '',
            self::KEY_LANGUAGE               => $this->parameters['tpe']['language'],
            self::KEY_SOCIETY                => 'EMU_' . $this->parameters['tpe']['society'],
        );

        $params[self::KEY_MAC] = $this->certifyTransaction($params, $this->parameters['tpe']['secret']);

        $result = $this->parseTextResponse($this->request($params, $this->parameters['tpe']['api_url_capture']));

        error_log(print_r($result, true));

        if ($result['status'] == 200) {
            $transaction->setStatus($this->getStatusForLabel($result['content']['lib']));
        } else {
            $transaction->setComment($result['content']);
            $transaction->setStatus('error_' . $result['status']);
        }

        return $transaction;
    }

    private function certifyCardAuthorisation($params, $secret)
    {
        $data = '';
        $fields = array(
            self::KEY_TPE,
            self::KEY_DATE,
            self::KEY_AMOUNT,
            self::KEY_REFERENCE,
            self::KEY_COMMENT,
            self::KEY_VERSION,
            self::KEY_LANGUAGE,
            self::KEY_SOCIETY,
            self::KEY_EMAIL,
            self::KEY_PAYMENT_COUNT,
            self::KEY_PAYMENT_DATE_1,
            self::KEY_PAYMENT_AMOUNT_1,
            self::KEY_PAYMENT_DATE_2,
            self::KEY_PAYMENT_AMOUNT_2,
            self::KEY_PAYMENT_DATE_3,
            self::KEY_PAYMENT_AMOUNT_3,
            self::KEY_PAYMENT_DATE_4,
            self::KEY_PAYMENT_AMOUNT_4
        );

        foreach ($fields as $field) {
            if (isset($params[$field])) {
                $data .= $params[$field];
            } else {
                $data .= '';
            }

            $data .= '*';
        }

        return strtolower(hash_hmac('sha1', $data, pack('H*', $secret)));
    }

    private function certifyTransaction($params, $secret)
    {
        $data = '';

        $fields = array(
            self::KEY_TPE,
            self::KEY_DATE,
            self::KEY_ORDER_AMOUNT,
            self::KEY_ALLREADY_ORDER_AMOUNT,
            self::KEY_REMAINING_ORDER_AMOUNT,
            self::KEY_REFERENCE,
            self::KEY_COMMENT,
            self::KEY_VERSION,
            self::KEY_LANGUAGE,
            self::KEY_SOCIETY
        );

        foreach ($fields as $field) {
            if (isset($params[$field])) {
                $data .= $params[$field];
            } else {
                $data .= '';
            }

            if ($field != self::KEY_ORDER_AMOUNT &&
                $field != self::KEY_ALLREADY_ORDER_AMOUNT) {
                $data .= '*';
            }
        }

        return strtolower(hash_hmac('sha1', $data, pack('H*', $secret)));
    }

    private function request($params, $url)
    {
        error_log(http_build_query($params));
        $request = curl_init();

        curl_setopt_array($request, array(
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CAINFO         => '/certificates/cacert.pem',
        ));

        // Execute request and get response and status code
        $content = curl_exec($request);
        $status  = curl_getinfo($request, CURLINFO_HTTP_CODE);

        // Close connection
        curl_close($request);
        error_log($content);

        return array('content' => $content, 'status' => $status);
    }

    private function getStatusForCode($code)
    {
        $status = CardAuthorisation::STATUS_UNKNOWN;

        switch ($code) {
            case 2:
                $status = CardAuthorisation::STATUS_AUTHORISATION_3DS_PENDING;
            break;
            case 1:
                $status = CardAuthorisation::STATUS_AUTHORISATION_SUCCESS;
            break;
            case 0:
                $status = CardAuthorisation::STATUS_AUTHORISATION_FAILED;
            break;
            case -1:
                $status = CardAuthorisation::STATUS_TECHNICAL_ERROR;
            break;
            case -2:
                $status = CardAuthorisation::STATUS_UNIDENTIFIED_SELLER;
            break;
            case -3:
                $status = CardAuthorisation::STATUS_MAC_ERROR;
            break;
            case -4:
                $status = CardAuthorisation::STATUS_EXPIRED_CARD;
            break;
            case -5:
                $status = CardAuthorisation::STATUS_CARD_NUMBER_ERROR;
            break;
            case -6:
                $status = CardAuthorisation::STATUS_EXPIRED_ORDER;
            break;
            case -7:
                $status = CardAuthorisation::STATUS_AMOUNT_ERROR;
            break;
            case -8:
                $status = CardAuthorisation::STATUS_DATE_ERROR;
            break;
            case -9:
                $status = CardAuthorisation::STATUS_CVX_ERROR;
            break;
            case -10:
                $status = CardAuthorisation::STATUS_PAYMENT_ALLREADY_REGISTRED;
            break;
            case -11:
                $status = CardAuthorisation::STATUS_PAYMENT_ALLREADY_ACCEPTED;
            break;
            case -12:
                $status = CardAuthorisation::STATUS_PAYMENT_ALLREADY_CANCELED;
            break;
            case -13:
                $status = CardAuthorisation::STATUS_PENDING;
            break;
            case -14:
                $status = CardAuthorisation::STATUS_BURNED_ORDER;
            break;
            case -15:
                $status = CardAuthorisation::STATUS_PARAMETER_ERROR;
            break;
            case -16:
                $status = CardAuthorisation::STATUS_3DS_ERROR;
            break;
            case -17:
                $status = CardAuthorisation::STATUS_DELAYED_PAYMENT_AMOUNT_ERROR;
            break;
            case -18:
                $status = CardAuthorisation::STATUS_DELAYED_PAYMENT_DATE_ERROR;
            break;
            case -19:
                $status = CardAuthorisation::STATUS_PAYMENT_COUNT_ERROR;
            break;
            case -20:
                $status = CardAuthorisation::STATUS_WRONG_VERSION;
            break;
            case -21:
                $status = CardAuthorisation::STATUS_FILTER_BLOCKED;
            break;
        }

        return $status;
    }

    private function getStatusForLabel($label)
    {
        $status = Transaction::STATUS_UNKNOWN;

        switch ($label) {
            case 'paiement accepte':
                $status = Transaction::STATUS_PAYMENT_SUCCESS;
            break;
            case 'commande annulee':
                $status = Transaction::STATUS_ORDER_CANCELED;
            break;
            case 'recurrence stoppee':
                $status = Transaction::STATUS_RECURSION_STOPPED;
            break;
            case 'commande non authentifiee':
                $status = Transaction::STATUS_UNKNOWN_ORDER;
            break;
            case 'commande expiree':
                $status = Transaction::STATUS_EXPIRED_ORDER;
            break;
            case 'commande grillee':
                $status = Transaction::STATUS_BURNED_ORDER;
            break;
            case 'autorisation refusee':
                $status = Transaction::STATUS_AUTHORISATION_REFUSED;
            break;
            case 'la commande est deja annulee':
                $status = Transaction::STATUS_PAYMENT_ALLREADY_CANCELED;
            break;
            case 'paiement deja accepte':
                $status = Transaction::STATUS_PAYMENT_ALLREADY_ACCEPTED;
            break;
            case 'signature non valide':
                $status = Transaction::STATUS_MAC_ERROR;
            break;
            case 'verification echouee (mode de paiement)':
                $status = Transaction::STATUS_VERIFICATION_ERROR;
            break;
            case 'la demande ne peut aboutir':
                $status = Transaction::STATUS_PARAMETER_ERROR;
            break;
            case 'montant errone ':
                $status = Transaction::STATUS_AMOUNT_ERROR;
            break;
            case 'commercant non identifie':
                $status = Transaction::STATUS_UNIDENTIFIED_SELLER;
            break;
            case 'traitement en cours':
                $status = Transaction::STATUS_PENDING;
            break;
            case 'date erronee':
                $status = Transaction::STATUS_DATE_ERROR;
            break;
            case 'autre traitement en cours':
                $status = Transaction::STATUS_OTHER_PENDING;
            break;
            case 'probleme technique':
                $status = Transaction::STATUS_TECHNICAL_ERROR;
            break;
        }

        return $status;
    }

    private function parseTextResponse($response)
    {
        $fields = explode(chr(10), substr($response['content'], 0, strlen($response['content']) - 1));

        $result = array();
        foreach ($fields as $field) {
            list($key, $data) = explode('=', $field);
            $result[$key] = $data;
        }

        return array(
            'status'  => $response['status'],
            'content' => $result
        );
    }

    private function parseXmlResponse($response)
    {
        try {
            $xml = new \SimpleXMLElement($response['content']);
        } catch (\Exception $e) { }

        return array(
            'status'    => $response['status'],
            'valid_xml' => isset($xml),
            'content'   => isset($xml) ? $xml : $response['content']
        );
    }
}
