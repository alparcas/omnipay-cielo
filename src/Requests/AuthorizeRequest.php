<?php

namespace Omnipay\Cielo30\Requests;

use Cielo\API30\Ecommerce\Payment;
use Omnipay\Cielo30\Responses\AuthorizeResponse;

class AuthorizeRequest extends AbstractRequest
{
    protected $requestType = self::REQUEST_TYPE_API;
    protected $endpoint = '/1/sales/';
    protected $requestMethod = 'POST';

    protected function createResponse($response)
    {
        return $this->response = new AuthorizeResponse($this, $response);
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     * @throws \Exception
     */
    public function getData()
    {
        $expiryMonth = $this->formatExpiryValue($this->getCard()->getExpiryMonth());
        $expiryYear = $this->formatExpiryValue($this->getCard()->getExpiryYear());

        $data = [
            "MerchantOrderId" => $this->order_id,
            "Customer"        => [
                "Name" => $this->customer_name
            ],
            "Payment"         => [
                "Type"           => $this->translateType(),
                "Amount"         => $this->getAmountInteger(),
                "Installments"   => $this->installments,
                "SoftDescriptor" => $this->soft_descriptor
            ]
        ];

        if ($this->translateType() == Payment::PAYMENTTYPE_CREDITCARD) {
            $data["Payment"]["CreditCard"] = [
                "CardNumber"     => $this->getCard()->getNumber(),
                "Holder"         => $this->getCard()->getName(),
                "ExpirationDate" => $expiryMonth . '/' . $expiryYear,
                "SecurityCode"   => $this->getCard()->getCvv(),
                "Brand"          => $this->getCardBrand(),
            ];
        }

        if ($this->translateType() == Payment::PAYMENTTYPE_DEBITCARD) {
            $data["Payment"]["DebitCard"] = [
                "CardNumber"     => $this->getCard()->getNumber(),
                "Holder"         => $this->getCard()->getName(),
                "ExpirationDate" => $expiryMonth . '/' . $expiryYear,
                "SecurityCode"   => $this->getCard()->getCvv(),
                "Brand"          => $this->getCardBrand(),
            ];

            $data['Payment']['Authenticate'] = true;
            $data['Payment']['ReturnUrl'] = $this->getReturnUrl();
        }

        return $data;
    }

    public function setOrderId($value)
    {
        $this->setParameter('order_id', $value);
    }

    public function setType($value)
    {
        $this->setParameter('type', $value);
    }

    public function setInstallments($value)
    {
        $this->setParameter('installments', $value);
    }

    public function setSoftDescriptor($value)
    {
        $this->setParameter('soft_descriptor', $value);
    }

    public function setCustomerName($value)
    {
        $this->setParameter('customer_name', $value);
    }

    /**
     * @return string
     */
    private function formatExpiryValue($value): string
    {
        return str_pad($value, 2, 0, STR_PAD_LEFT);
    }

    /**
     * Get Cielo type according to credit card type
     *
     * @return string
     * @throws \Exception
     */
    private function translateType()
    {
        $type = $this->type;

        switch ($type) {
            case 'credit':
                return Payment::PAYMENTTYPE_CREDITCARD;
                break;
            case 'debit':
                return Payment::PAYMENTTYPE_DEBITCARD;
                break;
            default:
                throw new \Exception('Payment type not supported');
                break;
        }
    }

    public function getCardBrand()
    {
        return $this->getParameter('card_brand');
    }

    public function setCardBrand($value)
    {
        $this->setParameter('card_brand', $value);
    }
}
