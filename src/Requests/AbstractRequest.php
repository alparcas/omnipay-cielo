<?php

namespace Omnipay\Cielo30\Requests;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    const SANDBOX_API_BASE_URL = 'https://apisandbox.cieloecommerce.cielo.com.br';
    const SANDBOX_QUERY_BASE_URL = 'https://apiquerysandbox.cieloecommerce.cielo.com.br/';

    const PRODUCTION_API_BASE_URL = 'https://api.cieloecommerce.cielo.com.br/';
    const PRODUCTION_QUERY_BASE_URL = 'https://apiquery.cieloecommerce.cielo.com.br/';

    const REQUEST_TYPE_QUERY = 'QUERY';
    const REQUEST_TYPE_API = 'API';

    protected $apiBaseUrl = self::SANDBOX_API_BASE_URL;
    protected $queryBaseUrl = self::SANDBOX_QUERY_BASE_URL;

    protected $endpoint = '/1/sales/{payment_id}';
    protected $requestMethod = 'GET';
    protected $requestType = self::REQUEST_TYPE_QUERY;

    public function initialize(array $parameters = array())
    {
        if (isset($parameters['environment']) && $parameters['environment'] === 'production') {
            $this->apiBaseUrl = self::PRODUCTION_API_BASE_URL;
            $this->queryBaseUrl = self::PRODUCTION_QUERY_BASE_URL;
        }

        return parent::initialize($parameters);
    }

    /**
     * @param mixed $data
     *
     * @return \Omnipay\Common\Message\ResponseInterface|void
     * @throws \Exception
     */
    public function sendData($data)
    {
        $method = $this->requestMethod;
        $url = $this->mountRequestUrl($data);
        $headers = $this->mountHeaders();

        $httpResponse = $this->httpClient->request(
            $method,
            $url,
            $headers,
            json_encode($data)
        );

        return $this->createResponse($httpResponse);
    }

    /**
     * @return array
     */
    private function mountHeaders(): array
    {
        $headers = [
            'MerchantId'      => $this->getMerchantId(),
            'MerchantKey'     => $this->getMerchantKey(),
            'Accept'          => 'application/json',
            'Accept-Encoding' => 'gzip',
            'Content-Type'    => 'application/json',
            'RequestId'       => uniqid()
        ];

        return $headers;
    }

    /**
     * @param $response
     *
     * @throws \Exception
     */
    protected function createResponse($response)
    {
        throw new \Exception("Response not implemented!");
    }

    private function mountRequestUrl($data)
    {
        $baseUrl = ($this->requestType === self::REQUEST_TYPE_QUERY) ? $this->queryBaseUrl : $this->apiBaseUrl;

        return $baseUrl . $this->endpoint;
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchant_id');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchant_id', $value);
    }

    public function getMerchantKey()
    {
        return $this->getParameter('merchant_key');
    }

    public function setMerchantKey($value)
    {
        return $this->setParameter('merchant_key', $value);
    }

    public function getEnvironment()
    {
        return $this->getParameter('environment');
    }

    public function setEnvironment($value)
    {
        return $this->setParameter('environment', $value);
    }

    public function __get($name)
    {
        return $this->getParameter($name);
    }
}
