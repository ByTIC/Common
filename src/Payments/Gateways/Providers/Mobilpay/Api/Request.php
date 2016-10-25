<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Api;

use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Api\Request\Notify;

/**
 * Class Request
 * This class can be used for accessing mobilpay.ro payment interface for your configured online services
 * @copyright NETOPIA System
 * @author Claudiu Tudose
 * @version 1.0
 *
 * @property Notify objPmNotify
 *
 * This class uses  OpenSSL
 * In order to use the OpenSSL functions you need to install the OpenSSL package.
 * See PHP documentation for installing OpenSSL package
 */
class Request
{
    const PAYMENT_TYPE_SMS = 0x01;
    const PAYMENT_TYPE_CARD = 0x02;
    #declare member variables
    /**
     * m_signatue (Mandatory)    - signature received from mobilpay.Ro that identifies merchant account
     *
     * @var string(24)
     */
    public $m_signature = null;

    /**
     * m_service (Mandatory)    - signature received from mobilpay.ro thet identifies the online service for which you are requesting a payment
     */
    public $m_service = null;

    /**
     * m_tran_id (Mandatory)    - payment transaction generated by seller; helps seller to interpret a request to return url; it should be unique for the specified service
     *
     * @var string(64)
     */
    public $m_type = self::PAYMENT_TYPE_SMS;
    public $m_details = null;
    public $m_price = null;
    public $m_currency = null;
    public $m_tran_id = null;

    /**
     * m_timetsamp (Mandatory)    - merchant's site datetime when the transaction was generated expressed as YYYYMMDDhhmmss
     *
     * @var string
     */
    public $m_timestamp = null;

    /**
     * m_return_url (Optional)    - URL where the user is redirected from mobilpay.Ro payment interface
     * when the transaction is canceled or confirmed. If it is not supplied the application will use
     * return URL configured for the specified service in control panel
     *
     * @var string
     */
    public $m_return_url = null;

    /**
     * m_confirm_url (Optional)    - URL of the seller that will be requested when mobilpay.ro will make
     * a decision about payment (e.g. confirmed, canceled). If it is not supplied the application will use
     * confirm URL configured for the specified service in control panel
     *
     * @var string
     */
    public $m_confirm_url = null;

    /**
     * m_first_name    (Optional)    - First name of the customer.
     *
     * @var string(255)
     */
    public $m_first_name = null;

    /**
     * m_last_name    (Optional)    - Last name of the customer.
     *
     * @var string(255)
     */
    public $m_last_name = null;

    /**
     * m_msisdn    (Optional)        - MSISDN (mobile phone numner) of the customer. If it's supplied it should be in 07XXXXXXXX format.
     * If it's supplied mobilpay.ro will use it for checking if payment is allowed for the specified service automaticaly
     *
     * @var string(10)
     */
    public $m_msisdn = null;
    /**
     * m_params (Optional)        - additional parameters sent to mobilpay.ro secure payment portal
     *
     * @var array
     */
    public $m_params = [];

    static function buildQueryString($params)
    {
        $crc_pairs = [];
        foreach ($params as $key => $value) {
            $crc_pairs[] = "{$key}={$value}";
        }

        return implode('&', $crc_pairs);
    }

    function Mobilpay_Payment_Request()
    {

    }

    /**
     * access Mobilpay.Ro secure payment portal
     *
     * @param resource $public_key - obtained by calling openssl_pkey_get_public
     * @param string &$env_key - returns envelope key base64 encoded or null if function fails
     * @param string &$enc_data - returns data to post base64 encoded or null if function fails
     *
     * @return boolean
     */
    public function buildAccessParameters($public_key, &$env_key, &$enc_data)
    {
        $params = $this->builParametersList();
        if (is_null($params)) {
            return false;
        }
        $src_data = Mobilpay_Payment_Request::buildQueryString($params);
        $enc_data = '';
        $env_keys = [];
        $result = openssl_seal($src_data, $enc_data, $env_keys, array($public_key));
        if ($result === false) {
            $env_key = null;
            $enc_data = null;

            return false;
        }
        $env_key = base64_encode($env_keys[0]);
        $enc_data = base64_encode($enc_data);

        return true;
    }

    public function builParametersList()
    {
        if (is_null($this->m_signature) || /*is_null($this->m_service) || */
            is_null($this->m_tran_id) || is_null($this->m_timestamp)
        ) {
            return null;
        }
        $params['signature'] = urlencode($this->m_signature);
        if ($this->m_service != null) {
            $params['service'] = urlencode($this->m_service);
        }
        $params['tran_id'] = urlencode($this->m_tran_id);
        $params['timestamp'] = urlencode($this->m_timestamp);
        if ($this->m_type == null) {
            $this->m_type = self::PAYMENT_TYPE_SMS;
        }
        $params['type'] = urlencode($this->m_type);
        if ($this->m_details != null) {
            $params['details'] = urlencode($this->m_details);
        }
        if ($this->m_price != null) {
            $params['price'] = urlencode(sprintf('%.02f', $this->m_price));
        }
        if ($this->m_currency != null) {
            $params['currency'] = urlencode($this->m_currency);
        }
        if (!is_null($this->m_return_url)) {
            $params['return_url'] = urlencode($this->m_return_url);
        }
        if (!is_null($this->m_confirm_url)) {
            $params['confirm_url'] = urlencode($this->m_confirm_url);
        }
        if (!is_null($this->m_first_name)) {
            $params['first_name'] = urlencode($this->m_first_name);
        }
        if (!is_null($this->m_last_name)) {
            $params['last_name'] = urlencode($this->m_last_name);
        }
        if (!is_null($this->m_msisdn)) {
            $params['msisdn'] = urlencode($this->m_msisdn);
        }
        if (is_array($this->m_params)) {
            foreach ($this->m_params as $key => $value) {
                if (isset($params[$key])) {
                    continue;
                }
                $params[$key] = urlencode($value);
            }
        }
        $params['crc'] = Mobilpay_Global::buildCRC($params);

        return $params;
    }
}
