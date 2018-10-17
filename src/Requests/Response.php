<?php

namespace Omnipay\Cielo30\Requests;

use function foo\func;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Response extends AbstractResponse
{
    const OK = 200;
    const CREATED = 201;

    protected $statusCode;
    protected $baseResponse;

    /**
     * Constructor
     *
     * @param RequestInterface  $request the initiating request.
     * @param ResponseInterface $response
     */
    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->baseResponse = $response;
        $this->statusCode = $response->getStatusCode();
        $this->data = json_decode($response->getbody()->getContents(), true);
    }

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return ($this->statusCode === self::OK || $this->statusCode === self::CREATED);
    }

    /**
     * Response Message
     *
     * @return null|array A response message from the payment gateway
     */
    public function getMessage()
    {
        return ($this->statusCode !== self::OK) ? $this->getErrorMessages() : null;
    }

    /**
     * Response code
     *
     * @return null|array A response code from the payment gateway
     */
    public function getCode()
    {
        return ($this->statusCode !== self::OK) ? $this->getErrorCodes() : null;
    }

    private function getErrorMessages()
    {
        return array_map(function ($data) {
            return $data['Message'];
        }, $this->data);
    }

    private function getErrorCodes()
    {
        return array_map(function ($data) {
            return $data['Code'];
        }, $this->data);
    }
}
