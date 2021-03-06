<?php

namespace Emmanix2002\Moneywave\Service;

use Emmanix2002\Moneywave\Enum\Currency;
use Emmanix2002\Moneywave\Enum\Endpoints;
use Emmanix2002\Moneywave\Exception\ValidationException;
use Emmanix2002\Moneywave\Moneywave;
use Emmanix2002\Moneywave\MoneywaveResponse;

/**
 * Disburse funds from your Moneywave wallet to a multiple recipient bank accounts.
 *
 * To successfully transfer money from a funded wallet to more than one account at a time, you need to call
 * v1/disburse/bulk and supply all the necessary parameters.
 * The ref for the individual disburse transactions must be UNIQUE, and the ref for the whole disburse transaction must
 * be UNIQUE for each disburse transaction.
 *
 * @package Emmanix2002\Moneywave\Service
 *
 * @property string $lock           the password of your wallet
 * @property array  $recipients     the password of your wallet (Use DisburseBulk::addRecipient() to add recipients)
 * @property string $currency       the currency to send money in. One of the Currency::* constants (default: Naira)
 * @property string $senderName     the name of the sender
 * @property string $ref            a UNIQUE reference code for this transaction
 */
class DisburseBulk extends AbstractService
{
    /** @var array  */
    private $disburseRecipients = [];
    
    /**
     * DisburseBulk constructor.
     *
     * @param Moneywave $moneyWave
     */
    public function __construct(Moneywave $moneyWave)
    {
        parent::__construct($moneyWave);
        $this->currency = Currency::NAIRA;
        $this->setRequiredFields('lock', 'recipients', 'currency', 'senderName', 'ref');
    }
    
    /**
     * Returns the HTTP request method for the service
     *
     * @return string
     */
    public function getRequestMethod(): string
    {
        return 'POST';
    }
    
    /**
     * Returns the API request path for the service
     *
     * @return string
     */
    public function getRequestPath(): string
    {
        return Endpoints::DISBURSE_BULK;
    }
    
    /**
     * Adds a recipient to the list of recipients for this transaction
     *
     * @param string      $bankCode         the recipient bank code. One of the Banks::* constants
     * @param string      $accountNumber    the recipient account number
     * @param float       $amount           the amount to be transferred
     * @param string|null $reference        the unique reference (default: recipient index)
     *
     * @return DisburseBulk
     */
    public function addRecipient(
        string $bankCode,
        string $accountNumber,
        float $amount,
        string $reference = null
    ): DisburseBulk {
        $reference = $reference ?: (string) (count($this->disburseRecipients) + 1);
        $this->disburseRecipients[] = [
            'bankcode' => $bankCode,
            'accountNumber' => $accountNumber,
            'amount' => $amount,
            'ref' => $reference
        ];
        return $this;
    }
    
    /**
     * Sends the request to the endpoint.
     * There is the possibility of an unsuccessful request status, that should be watched out for.
     *
     * @return MoneywaveResponse
     *
     * @throws ValidationException
     */
    public function send(): MoneywaveResponse
    {
        if (empty($this->disburseRecipients)) {
            throw new ValidationException('You need to provide at least 1 recipient');
        }
        $this->recipients = $this->disburseRecipients;
        return parent::send();
    }
}