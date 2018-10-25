<?php

namespace Omnipay\Cielo30\Requests;

use Omnipay\Cielo30\Responses\CaptureResponse;

class CaptureRequest extends AbstractRequest
{
    protected $requestType = self::REQUEST_TYPE_API;
    protected $endpoint = '/1/sales/{payment_id}/capture';
    protected $requestMethod = 'PUT';

    protected function createResponse($response)
    {
        return $this->response = new CaptureResponse($this, $response);
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

        return null;
    }

    public function setPaymentId($value)
    {
        $this->setParameter('payment_id', $value);
    }
}
