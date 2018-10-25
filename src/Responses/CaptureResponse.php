<?php

namespace Omnipay\Cielo30\Responses;

use Omnipay\Common\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CaptureResponse extends AbstractResponse
{
    const SUCCESS_STATUS = 2;

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return ($this->statusCode == self::HTTP_STATUS_OK
            && $this->data['Status'] === self::SUCCESS_STATUS);
    }

    /**
     * Response Message
     *
     * @return null|string A response message from the payment gateway
     */
    public function getMessage()
    {
        if ($this->statusCode === self::HTTP_STATUS_ERROR){
            return $this->data[0]['Message'];
        }

        return $this->data['ReturnMessage'];
    }

    /**
     * Response code
     *
     * @return null|string A response code from the payment gateway
     */
    public function getCode()
    {
        if ($this->statusCode === self::HTTP_STATUS_ERROR){
            return $this->data[0]['Code'];
        }

        return $this->data['ReturnCode'];
    }
}
