<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Payu\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\ServerCompletePurchaseResponse as AbstractResponse;

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
        return in_array($this->getCode(), ['PAYMENT_AUTHORIZED', 'PAYMENT_RECEIVED', 'COMPLETE']);
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * Status code (string)
     *
     * @return string
     */
    public function getCode()
    {
        return $this->data['ipn_data']['ORDERSTATUS'];
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return '<EPAYMENT>'.$this->data['dateReturn'].'|'.$this->data['hashReturn'].'</EPAYMENT>';
    }

    /**  @noinspection PhpMissingParentCallCommonInspection
     * Is the response successful?
     *
     * @return boolean
     */
    public function isPending()
    {
        return in_array($this->getCode(), ['TEST']);
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * Is the transaction cancelled by the user?
     *
     * @return boolean
     */
    public function isCancelled()
    {
        return in_array($this->getCode(), ['REVERSED', 'REFUND'], true);
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * Gateway Reference
     *
     * @return null|string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference()
    {
        return $this->data['ipn_data']['REFNO'];
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * Get the transaction ID as generated by the merchant website.
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->data['ipn_data']['REFNOEXT'];
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return false|string
     */
    public function getTransactionDate()
    {
        return $this->data['ipn_data']['SALEDATE'];
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return bool
     */
    protected function canProcessModel()
    {
        return true;
    }
}
