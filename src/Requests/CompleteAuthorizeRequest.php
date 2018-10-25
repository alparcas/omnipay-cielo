<?php

namespace Omnipay\Cielo30\Requests;

use Omnipay\Cielo30\Responses\CompleteAuthorizeResponse;

class CompleteAuthorizeRequest extends AbstractRequest
{
    protected $requestType = self::REQUEST_TYPE_QUERY;
    protected $endpoint = '/1/sales/{payment_id}';
    protected $requestMethod = 'GET';

    protected function createResponse($response)
    {
        return $this->response = new CompleteAuthorizeResponse($this, $response);
    }

    private function mountEndpoint()
    {
        $this->validate('payment_id');

        return str_replace('{payment_id}', $this->payment_id, $this->endpoint);
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->endpoint = $this->mountEndpoint();

        return [];
    }

    public function setPaymentId($value)
    {
        $this->setParameter('payment_id', $value);
    }
}
