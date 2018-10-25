<?php

namespace Omnipay\Cielo30\Responses;


use Omnipay\Common\Message\RequestInterface;

class AuthorizeResponse extends AbstractResponse
{
    const SUCCESS_STATUSES = [1];
    const CANCELED_STATUSES = [10, 11, 13];
    const REDIRECT_STATUS = 0;

    protected $paymentStatus;
    protected $paymentReturnCode;

    public function __construct(RequestInterface $request, $response)
    {
        parent::__construct($request, $response);

        $this->paymentStatus = $this->getPaymentStatus();
        $this->paymentReturnCode = $this->getPaymentReturnCode();
    }


    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return (
            ($this->statusCode === self::HTTP_STATUS_OK || $this->statusCode === self::HTTP_STATUS_CREATED)
            && in_array($this->paymentStatus, self::SUCCESS_STATUSES)
        );
    }

    /**
     * Does the response require a redirect?
     *
     * @return boolean
     */
    public function isRedirect()
    {
        $authUrl = isset($this->data['Payment']['AuthenticationUrl'])
            ? $this->data['Payment']['AuthenticationUrl']
            : '';

        return ($this->paymentStatus === self::REDIRECT_STATUS && !empty($authUrl));
    }

    private function getPaymentStatus()
    {
        return (isset($this->data['Payment']['Status'])) ? intval($this->data['Payment']['Status']) : false;
    }

    private function getPaymentReturnCode()
    {
        return (isset($this->data['Payment']['ReturnCode'])) ? $this->data['Payment']['ReturnCode'] : false;
    }

    /**
     * Is the transaction cancelled by the user?
     *
     * @return boolean
     */
    public function isCancelled()
    {
        return (in_array($this->paymentStatus, self::CANCELED_STATUSES));
    }

    /**
     * Gets the redirect target url.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->data['Payment']['AuthenticationUrl'];
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

        return $this->data['Payment']['ReturnMessage'];
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

        return $this->data['Payment']['ReturnCode'];
    }

    /**
     * Gateway Reference
     *
     * @return null|string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference()
    {
        return $this->data['Payment']['PaymentId'];
    }

    /**
     * Get the transaction ID as generated by the merchant website.
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->data['Payment']['Tid'];
    }
}
