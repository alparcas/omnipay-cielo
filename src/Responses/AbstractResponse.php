<?php

namespace Omnipay\Cielo30\Responses;

use Omnipay\Common\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractResponse extends \Omnipay\Common\Message\AbstractResponse
{
    const HTTP_STATUS_OK = 200;
    const HTTP_STATUS_CREATED = 201;
    const HTTP_STATUS_ERROR = 400;

    /**
     * @var int
     */
    protected $statusCode;

    protected $baseResponse;

    /**
     * Constructor
     *
     * @param RequestInterface $request the initiating request.
     * @param AbstractResponse $response
     */
    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->baseResponse = $response;
        $this->statusCode = $response->getStatusCode();
        $this->data = $this->decode($response->getbody()->getContents());
    }

    protected function decode($data)
    {
        return json_decode($data, true);
    }
}
