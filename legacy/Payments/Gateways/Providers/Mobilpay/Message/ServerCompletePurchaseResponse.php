<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\ServerCompletePurchaseResponse as AbstractResponse;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Api\Request;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Api\Request\Notify;
use DateTime;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
class ServerCompletePurchaseResponse extends AbstractResponse
{

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->getCode() == 0 && in_array($this->getAction(), ['confirmed']);
    }

    /**
     * Response code
     *
     * @return null|string A response code from the payment gateway
     */
    public function getCode()
    {
        return $this->getDataProperty('code');
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->getRequestNotify()->action;
    }

    /**
     * @return Notify
     */
    public function getRequestNotify()
    {
        return $this->getRequestData()->objPmNotify;
    }

    /**
     * @return Request\Card
     */
    public function getRequestData()
    {
        return $this->data['requestData'];
    }

    /**
     * Is the transaction cancelled by the user?
     *
     * @return boolean
     */
    public function isPending()
    {
        if ($this->getCode() == 0) {
            return in_array($this->getAction(), ['paid', 'paid_pending', 'confirmed_pending']);
        }

        return parent::isCancelled(); // TODO: Change the autogenerated stub
    }

    /**
     * Is the transaction cancelled by the user?
     *
     * @return boolean
     */
    public function isCancelled()
    {
        if ($this->getCode() == 0) {
            return in_array($this->getAction(), ['credit', 'canceled']);
        }

        return parent::isCancelled(); // TODO: Change the autogenerated stub
    }

    public function send()
    {
        header('Content-type: application/xml');
        parent::send();
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $content = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";

        if ($this->getCodeType() == 0) {
            $content .= "<crc>{$this->getDataProperty('notificationCrc')}</crc>";
        } else {
            $content .= "<crc error_type=\"{$this->getCodeType()}\" error_code=\"{$this->getCode()}\">";
            $content .= $this->getMessage();
            $content .= "</crc>";
        }

        return $content;
    }

    /**
     * Response code
     *
     * @return null|string A response code from the payment gateway
     */
    public function getCodeType()
    {
        return $this->getDataProperty('codeType');
    }

    /**
     * Response Message
     *
     * @return null|string A response message from the payment gateway
     */
    public function getMessage()
    {
        return $this->getDataProperty('message');
    }

    /**
     * @return false|string
     */
    public function getTransactionDate()
    {
        $timestamp = $this->getRequestNotify()->timestamp;
        $dateTime = DateTime::createFromFormat('YmdHis', $timestamp);

        return $dateTime->format('Y-m-d H:i:s');
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * Gateway Reference
     *
     * @return null|string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference()
    {
        return $this->getRequestNotify()->purchaseId;
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return bool
     */
    protected function canProcessModel()
    {
        return true;
    }
}
