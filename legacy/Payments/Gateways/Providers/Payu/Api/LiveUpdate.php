<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Payu\Api;

/**
 * Class Live Update v2.2
 *
 * This class generates HTML HTTP POST code for ePayment Live Update implementation
 *
 * Last update: December 2007, 04
 * More info: www.epayment.ro
 *
 * This class is distributed to ePayment partners as an example implementation only.
 * The example is provided "AS IS" and without warranty, express or implied. In no
 * event will GECAD ePayment be liable for any damages, including but not limited to
 * any lost profits, lost savings or any incidental or consequential damages, whether
 * resulting from impaired or lost data, software or computer failure or any other
 * cause, or for any other claim by the user or for any third party claim.
 *
 * You can freely modify the code to fit your needs for implementation.
 *
 * This class will generate the HTML CODE for the HTTP POST request. It will
 * not generate the entire HTML CODE. The <form></form> tags are not included,
 * allowing you to customize your form layout.
 * The class can be modified to include also the form tags. Please use the
 * $liveUpdateURL member for the action property of the form tag.
 *
 * The class has the default behaviour for most situations.
 *
 * The class contains a number of functions to set different variables: setLiveUpdateURL,
 * setTestMode, setLanguage, setSecretKey, setMerchant, setOrderRef, setOrderDate etc.
 * After calling any of these functions please check the return value to see if
 * the function succeeded. The functions will return an error if invalid data types
 * or values are received as parameters. If any of these functions will fail, the method
 * getLiveUpdateHTML might return an error. In case of an error, this method will
 * output an error as an HTML comment like the one below:
 * <!-- EPAYMENT ERROR: [error-message] -->
 *
 */
class LiveUpdate
{
    /*
     * Class setup members.
     * Set up the next members with the class default values.
     *
     */

    /**
     * Live Update URL
     * This is the URL address where HTTP POST should be sent
     * @var string
     */
    public $liveUpdateURL = "https://secure.epayment.ro/order/lu.php";

    /**
     * Test Mode
     * Set to true or or 1 for testing mode.
     * @var boolean
     */
    public $testMode = false;

    /**
     * Language
     * The language of the order interface. Default: "ro".
     * @var string
     */
    public $language = "ro";

    /**
     * Secret Key
     * Communication secret code. Used to create the HMAC signature.
     * @var string
     */
    public $secretKey = null;

    /**
     * Merchant Identifier
     * ePayment merchant code
     * @var string
     */
    public $merchant = null;

    /**
     * Merchant order reference
     * @var string
     */
    public $orderRef = '';

    /**
     * Order date
     * @var string
     */
    public $orderDate = null;

    /**
     * Product Names
     * @var array
     */
    public $orderPName = [];

    /**
     * Product Groups
     * @var array
     */
    public $orderPGroup = [];

    /**
     * Product Price Types (NET or GROSS)
     * @var array
     */
    public $orderPType = [];

    /**
     * Product Codes
     * @var array
     */
    public $orderPCode = [];

    /**
     * Product additional information
     * @var array
     */
    public $orderPInfo = [];

    /**
     * Product Prices (WITH NO VAT/TAXES if Product Price Types is not specified or NET)
     * @var array
     */
    public $orderPrice = [];

    /**
     * Product Quantities
     * @var array
     */
    public $orderQty = [];

    /**
     * Product VAT/Tax
     * @var array
     */
    public $orderVAT = [];

    /**
     * Product versions
     * @var array
     */
    public $orderVer = [];

    /**
     * Shipping cost
     * @var float
     */
    public $orderShipping = 0;

    /**
     * Order currency
     * @var string
     */
    public $pricesCurrency = '';

    /**
     * Order discount
     * @var float
     */
    public $discount = 0;

    /**
     * Destination city
     * @var string.
     */
    public $destinationCity = '';

    /**
     * Destination state
     * @var string
     */
    public $destinationState = '';

    /**
     * Destination country code
     * @var integer
     */
    public $destinationCountry = '';

    /**
     * Payment method
     * @var string
     */
    public $payMethod = '';

    /**
     * Order Hash
     */
    public $orderHash = '';


    /*
     * Billing information
     * Used to autofill the ordering form
     */
    public $billing = [
        "billFName" => '',
        "billLName" => '',
        "billCISerial" => '',
        "billCINumber" => '',
        "billCIIssuer" => '',
        "billCNP" => '',
        "billCompany" => '',
        "billFiscalCode" => '',
        "billRegNumber" => '',
        "billBank" => '',
        "billBankAccount" => '',
        "billEmail" => '',
        "billPhone" => '',
        "billFax" => '',
        "billAddress1" => '',
        "billAddress2" => '',
        "billZipCode" => '',
        "billCity" => '',
        "billState" => '',
        "billCountryCode" => '',
    ];
    public $billingSet = false;


    /*
     * Delivery information.
     * Used to autofill the ordering form
     */
    public $delivery = array(
        "deliveryFName" => '',
        "deliveryLName" => '',
        "deliveryCompany" => '',
        "deliveryPhone" => '',
        "deliveryAddress1" => '',
        "deliveryAddress2" => '',
        "deliveryZipCode" => '',
        "deliveryCity" => '',
        "deliveryState" => '',
        "deliveryCountryCode" => '',
    );
    public $deliverySet = false;

    /**
     * LiveUpdate class constructor
     *
     * @access        public
     * @param        $secretKey        string
     * @param        $liveUpdateURL    string
     * @param        $debugMode        boolean
     * @param        $testMode        boolean
     * @param        $language        string
     * @return        void
     */
    public function __construct()
    {
    }

    /**
     * setLiveUpdateURL class method
     *
     * Set up live update URL address
     *
     * @access        public
     * @param        $liveUpdateURL - live update url address
     * @return        boolean
     */
    public function setLiveUpdateURL($liveUpdateURL = 'https://secure.epayment.ro/order/lu.php')
    {
        if (!is_string($liveUpdateURL)) {  //invalid data type
            return false;
        }

        if (empty($liveUpdateURL)) {   //empty string
            return false;
        }

        $this->liveUpdateURL = $liveUpdateURL; //everything is ok
        return true;
    }

    /**
     * setTestMode class method
     *
     * This method sets the protocol in test mode or not.
     * Set the parameter to 1 or true to use the class in test mode. For
     * any other values, including false or 0, the class will work in
     * real mode
     *
     * @access        public
     * @param        $testMode - boolean
     * @return        boolean
     */
    public function setTestMode($testMode = false)
    {
        switch ($testMode) {
            case true:
            case 1:
                $this->testMode = true;
                break;

            case false:
            case 0:
            default:
                $this->testMode = false;
        }

        return true;
    }

    /**
     * setLanguage class method
     *
     * Sets the order language
     *
     * @access        public
     * @param        $language - string
     * @return        boolean
     */
    public function setLanguage($language = 'ro')
    {
        $language = trim(strtolower($language));
        switch ($language) {
            case 'ro':
                $this->language = 'ro';
                break;
            case 'en':
            default:
                $this->language = 'en';
        }

        return true;
    }

    /**
     * setSecretKey class method
     *
     * Sets the secret key used for HASH signature
     *
     * @access        public
     * @param        $secretKey - string
     * @return        boolean
     */
    public function setSecretKey($secretKey)
    {
        if (!is_string($secretKey)) {
            $this->secretKeyError = 'invalid type';

            return false;
        }

        if (empty($secretKey)) {
            $this->secretKeyError = 'empty string';

            return false;
        }

        if (strlen($secretKey) > 64) {
            $this->secretKeyError = 'secret key is length is too big';

            return false;
        }

        if (preg_match("/ /i", $secretKey)) {
            $this->secretKeyError = 'invalid format; white spaces not allowed';

            return false;
        }

        $this->secretKey = $secretKey;

        return true;
    }

    /**
     * setMerchant method
     *
     * Sets up merchant identifier code
     *
     * @access        public
     * @param        $merchant string: merchat identifier
     * @return        boolean
     */
    public function setMerchant($merchant)
    {
        if (!is_string($merchant)) {
            return false;
        }

        if (empty($merchant)) {
            return false;
        }

        $this->merchant = $merchant;

        return true;
    }

    /**
     * setOrderRef method
     *
     * @access        public
     * @param        $orderRef string
     * @return        boolean
     */
    public function setOrderRef($orderRef = '')
    {
        if (settype($orderRef, "string")) {
            if (strlen($orderRef) <= 32) {
                $this->orderRef = $orderRef;

                return true;
            }
            $this->orderRef = null;

            return false;
        }
        $this->orderRef = null;

        return false;
    }

    /**
     * setOrderDate class method
     *
     * @access        public
     * @param        $orderDate string
     * @return        boolean
     */
    public function setOrderDate($orderDate = '')
    {
        if (strtotime($orderDate) === -1) {
            $this->orderDate = null;

            return false;
        }

        $dateFormatPattern = "^((((19|20)(([02468][048])|([13579][26]))-02-29))|((20[0-9][0-9])|(19[0-9][0-9]))-((((0[1-9])|(1[0-2]))-((0[1-9])|(1\d)|(2[0-8])))|((((0[13578])|(1[02]))-31)|(((0[1,3-9])|(1[0-2]))-(29|30)))))^";

        if (!preg_match($dateFormatPattern, $orderDate)) {
            $this->orderDate = null;

            return false;
        }

        if (strtotime($orderDate) > strtotime(date("Y-m-d H:i:s"))) {
            $this->orderDate = null;

            return false;
        }

        $this->orderDate = $orderDate;

        return true;
    }

    /**
     * setOrderPName class method
     *
     * Sets shopping cart product names
     *
     * @access        public
     * @param        $orderPName array
     * @return        boolean
     */
    public function setOrderPName($orderPName)
    {
        if (!is_array($orderPName)) {
            $this->orderPName = null;

            return false;
        }
        $isValid = true;
        $index = 0;
        while ($isValid && $index < count($orderPName)) {
            if (strlen($orderPName[$index]) > 155) {
                $isValid = false;
            } else {
                $isValid = true;
            }
            $index++;
        }
        if (!$isValid) {
            $this->orderPName = null;

            return false;
        }
        $this->orderPName = $orderPName;

        return true;
    }

    /**
     * setOrderPGroup class method
     *
     * Sets products groups
     *
     * @access        public
     * @param        $orderPGroup array
     * @return        boolean
     */
    public function setOrderPGroup($orderPGroup)
    {
        if (!is_array($orderPGroup)) {
            $this->orderPGroup = null;

            return false;
        }
        $isValid = true;
        $index = 0;
        while ($isValid && $index < count($orderPGroup)) {
            if (strlen($orderPGroup[$index]) > 155) {
                $isValid = false;
            } else {
                $isValid = true;
            }
            $index++;
        }
        if (!$isValid) {
            $this->orderPGroup = null;

            return false;
        }
        $this->orderPGroup = $orderPGroup;

        return true;
    }

    /**
     * setOrderPType class method
     *
     * Sets products price types
     *
     * @access        public
     * @param        $orderPType array
     * @return        boolean
     */
    public function setOrderPType($orderPType)
    {
        if (!is_array($orderPType)) {
            $this->orderPType = null;

            return false;
        }
        $isValid = true;
        $index = 0;
        while ($isValid && $index < count($orderPType)) {
            if (!in_array($orderPType[$index], array('GROSS', 'NET'))) {
                $isValid = false;
            } else {
                $isValid = true;
            }
            $index++;
        }
        if (!$isValid) {
            $this->orderPType = null;

            return false;
        }
        $this->orderPType = $orderPType;

        return true;
    }

    /**
     * setOrderPCode class method
     *
     * Sets shopping cart product codes
     *
     * @access        public
     * @param        $orderPCode array
     * @return        boolean
     */
    public function setOrderPCode($orderPCode)
    {
        if (!is_array($orderPCode)) {
            $this->orderPCode = null;

            return false;
        }
        $isValid = true;
        $index = 0;
        while ($isValid && $index < count($orderPCode)) {
            if (strlen($orderPCode[$index]) > 20) {
                $isValid = false;
            } else {
                $isValid = true;
            }
            $index++;
        }
        if (!$isValid) {
            $this->orderPCode = null;

            return false;
        }
        $this->orderPCode = $orderPCode;

        return true;
    }

    /**
     * setOrderPInfo class method
     *
     * Sets additional information for the products in the shopping cart
     *
     * @access        public
     * @param        $orderPInfo array
     * @return        boolean
     */
    public function setOrderPInfo($orderPInfo)
    {
        if (!is_array($orderPInfo)) {
            $this->orderPInfo = null;

            return false;
        }
        $this->orderPInfo = $orderPInfo;

        return true;
    }

    /**
     * setOrderPrice class method
     *
     * Sets product prices
     *
     * @access        public
     * @param        $orderPrice array
     * @return        boolean
     */
    public function setOrderPrice($orderPrice)
    {
        if (!is_array($orderPrice)) {
            $this->orderPrice = null;

            return false;
        }
        $isValid = true;
        $index = 0;
        while ($isValid && $index < count($orderPrice)) {
            if (is_numeric($orderPrice[$index]) && $orderPrice[$index] > 0) {
                $isValid = true;
            } else {
                $isValid = false;
            }
            $index++;
        }
        if (!$isValid) {
            $this->orderPrice = null;

            return false;
        }
        $this->orderPrice = $orderPrice;

        return true;
    }

    /**
     * setOrderQTY class method
     *
     * Sets quantities for each product in the shopping cart
     * @access        public
     * @param        $orderQty array
     * @return        boolean
     */
    public function setOrderQTY($orderQty)
    {
        if (!is_array($orderQty)) {
            $this->orderQty = null;

            return false;
        }
        $isValid = true;
        $index = 0;
        while ($isValid && $index < count($orderQty)) {
            if (is_numeric($orderQty[$index]) && $orderQty[$index] > 0) {
                $isValid = true;
            } else {
                $isValid = false;
            }
            $index++;
        }
        if (!$isValid) {
            $this->orderQty = null;

            return false;
        }
        $this->orderQty = $orderQty;

        return true;
    }

    /**
     * setOrderVAT class method
     *
     * Sets VAT for each product in the shopping cart
     * @access        public
     * @param        $orderVAT array
     * @return        boolean
     */
    public function setOrderVAT($orderVAT)
    {
        if (!is_array($orderVAT)) {
            $this->orderVAT = null;

            return false;
        }
        $isValid = true;
        $index = 0;
        while ($isValid && $index < count($orderVAT)) {
            if (is_numeric($orderVAT[$index]) && $orderVAT[$index] >= 0 && $orderVAT[$index] < 100) {
                $isValid = true;
            } else {
                $isValid = false;
            }
            $index++;
        }
        if (!$isValid) {
            $this->orderVAT = null;

            return false;
        }
        $this->orderVAT = $orderVAT;

        return true;
    }

    /**
     * setOrderVer class method
     *
     * Sets products versions
     *
     * @access        public
     * @param        $orderVer array
     * @return        boolean
     */
    public function setOrderVer($orderVer)
    {
        if (!is_array($orderVer)) {
            $this->orderVer = null;

            return false;
        }
        $isValid = true;
        $index = 0;
        while ($isValid && $index < count($orderVer)) {
            if (settype($orderVer[$index], "string")) {
                if (strlen($orderVer[$index]) < 51) {
                    $isValid = true;
                } else {
                    $isValid = false;
                }
            } else {
                $isValid = false;
            }
            $index++;
        }
        if (!$isValid) {
            $this->orderVer = null;

            return false;
        }
        $this->orderVer = $orderVer;

        return true;
    }

    /**
     * setOrderShipping
     *
     * Sets the order shipping costs
     *
     * @access        public
     * @param        $orderShipping float
     * @return        boolean
     */
    public function setOrderShipping($orderShipping = 0)
    {
        if (is_numeric($orderShipping) && $orderShipping >= 0) {
            $this->orderShipping = $orderShipping;

            return true;
        } elseif (is_numeric($orderShipping) && $orderShipping < 0) {
            $this->orderShipping = -1;

            return true;
        }

        $this->orderShipping = null;

        return false;
    }

    /**
     * setPricesCurrency
     *
     * Sets the prices currency
     *
     * @access        public
     * @param        $pricesCurrency string[3]
     * @return        true
     */
    public function setCurrency($pricesCurrency = 'RON')
    {
        $pricesCurrency = strtoupper(trim($pricesCurrency));
        switch ($pricesCurrency) {
            case 'ROL':
                $this->currency = 'ROL';
                break;

            case 'EUR':
                $this->currency = 'EUR';
                break;

            case 'USD':
                $this->currency = 'USD';
                break;

            case 'GBP':
                $this->currency = 'GBP';
                break;

            case 'BGN':
                $this->currency = 'BGN';
                break;

            case 'RON':
            default:
                $this->currency = 'RON';
        }

        return true;
    }

    /**
     * setPricesCurrency
     *
     * Sets the prices currency
     *
     * @access        public
     * @param        $pricesCurrency string[3]
     * @return        true
     */
    public function setPricesCurrency($pricesCurrency = 'RON')
    {
        $pricesCurrency = strtoupper(trim($pricesCurrency));
        switch ($pricesCurrency) {
            case 'ROL':
                $this->pricesCurrency = 'ROL';
                break;

            case 'EUR':
                $this->pricesCurrency = 'EUR';
                break;

            case 'USD':
                $this->pricesCurrency = 'USD';
                break;

            case 'GBP':
                $this->pricesCurrency = 'GBP';
                break;

            case 'BGN':
                $this->pricesCurrency = 'BGN';
                break;

            case 'RON':
            default:
                $this->pricesCurrency = 'RON';
        }

        return true;
    }

    /**
     * setDiscount
     *
     * Sets the global order discount
     *
     * @access        public
     * @param        $discount float
     * @return        boolean
     */
    public function setDiscount($discount = 0)
    {
        if (is_numeric($discount) && $discount >= 0) {
            $this->discount = $discount;

            return true;
        }
        $this->discount = null;

        return false;
    }

    /**
     * setDestinationCity
     *
     * @access        public
     * @param        $destinationCity string
     * @return        boolean
     */
    public function setDestinationCity($destinationCity)
    {
        $destinationCity = trim($destinationCity);
        if (is_string($destinationCity) && strlen($destinationCity) > 0) {
            $this->destinationCity = $destinationCity;

            return true;
        }
        $this->destinationCity = null;

        return false;
    }

    /**
     * setDestinationState
     *
     * @access        public
     * @param        $destinationState string
     * @return        boolean
     */
    public function setDestinationState($destinationState)
    {
        $destinationState = trim($destinationState);
        if (is_string($destinationState) && strlen($destinationState) > 0) {
            $this->destinationState = $destinationState;

            return true;
        }
        $this->destinationState = null;

        return false;
    }

    /**
     * setDestinationCountry
     *
     * @access        public
     * @param        $destinationCountry integer
     * @return        boolean
     */
    public function setDestinationCountry($destinationCountry)
    {
        if (settype($destinationCountry, "string")) {
            if (strlen($destinationCountry) == 2) {
                $this->destinationCountry = $destinationCountry;

                return true;
            }
        }
        $this->destinationCountry = null;

        return false;
    }

    /**
     * setPayMethod
     *
     * @access        public
     * @param        $payMethod string
     * @return        boolean
     */
    public function setPayMethod($payMethod)
    {
        $payMethod = strtoupper($payMethod);
        switch ($payMethod) {
            case 'CCVISAMC':
                $this->payMethod = 'CCVISAMC';
                $retval = true;
                break;

            case 'CCAMEX':
                $this->payMethod = 'CCAMEX';
                $retval = true;
                break;

            case 'CCDINERS':
                $this->payMethod = 'CCDINERS';
                $retval = true;
                break;

            case 'CCJCB':
                $this->payMethod = 'CCJCB';
                $retval = true;
                break;

            case 'WIRE':
                $this->payMethod = 'WIRE';
                $retval = true;
                break;

            case 'CASH':
                $this->payMethod = 'CASH';
                $retval = true;
                break;

            default:
                $this->payMethod = null;
                $retval = false;
        }

        return $retval;
    }

    /**
     * setBilling
     *
     * Sets the billing information for the order
     *
     * @access        public
     * @param        $billing array
     */
    public function setBilling($billing)
    {
        foreach ($billing as $key => $val) {
            $this->billing[$key] = $val;
        }
        $this->billingSet = true;
    }

    /**
     * setDelivery
     *
     * Sets the delivery information for the order
     *
     * @access        public
     * @param        $delivery array
     */
    public function setDelivery($delivery)
    {
        while (is_array($delivery) && list($key, $val) = each($delivery)) {
            $$key = $val;
            switch ($key) {
                case 'deliveryCity':
                    if (is_string($this->destinationCity) && empty($this->destinationCity)) {
                        $this->delivery[$key] = $val;
                    } else {
                        $this->delivery[$key] = $this->destinationCity;
                    }
                    break;

                case 'deliveryState':
                    if (is_string($this->destinationState) && empty($this->destinationState)) {
                        $this->delivery[$key] = $val;
                    } else {
                        $this->delivery[$key] = $this->destinationState;
                    }
                    break;

                case 'deliveryCountryCode':
                    if ($this->destinationCountry === 0) {
                        $this->delivery[$key] = $val;
                    } else {
                        $this->delivery[$key] = $this->destinationCountry;
                    }
                    break;

                default:
                    $this->delivery[$key] = $val;
            }
        }
        $this->deliverySet = true;
    }

    /**
     * getLiveUpdateHTML
     *
     * This method returns the HTML code. This will include only the hidden
     * fields, not the <form></form> tags.
     *
     * @access        public
     * @access        public
     * @return        string
     */
    public function getLiveUpdateHTML()
    {
        $htmlCode = "";

        //add reference
        if (is_null($this->merchant)) {
            $htmlCode = "<!-- EPAYMENT ERROR: Invalid merchant id. -->";
        } else {
            $htmlCode .= $this->createHiddenField('merchant', $this->merchant, false);
        }


        //add reference
        if (!is_null($this->backRef)) {
            $htmlCode .= $this->createHiddenField('BACK_REF', $this->backRef, false);
        }

        //add external order ref
        if (!is_null($this->orderRef)) {
            $htmlCode .= $this->createHiddenField('order_ref', $this->orderRef, false);
        }

        //add order date
        if (is_null($this->orderDate)) {
            $htmlCode = "<!-- EPAYMENT ERROR: Invalid order date. -->";
        } else {
            $htmlCode .= $this->createHiddenField('order_date', $this->orderDate, false);
        }

        $sincronized = true;
        if ((count($this->orderPName) == count($this->orderPCode))
            && (count($this->orderPCode) == count($this->orderPrice))
            && (count($this->orderPCode) == count($this->orderPCode))
            && (count($this->orderQty) == count($this->orderVAT))
        ) {
            $count = count($this->orderPName);

            if (count($this->orderPInfo)) {
                if ($count != count($this->orderPInfo)) {
                    $sincronized = false;
                }
            }

            if (count($this->orderVer)) {
                if ($count != count($this->orderVer)) {
                    $sincronized = false;
                }
            }

            if (count($this->orderPGroup)) {
                if ($count != count($this->orderPGroup)) {
                    $sincronized = false;
                }
            }

            if (count($this->orderPType)) {
                if ($count != count($this->orderPType)) {
                    $sincronized = false;
                }
            }
        } else {
            $sincronized = false;
        }

        if ($sincronized) {
            //add order pname
            if (is_null($this->orderPName) || empty($this->orderPName)) {
                $htmlCode = "<!-- EPAYMENT ERROR: Invalid order name. -->";
            } else {
                $htmlCode .= $this->createHiddenField('order_pname', $this->orderPName);
            }

            //add order pcode
            if (is_null($this->orderPGroup)) {
                $htmlCode = "<!-- EPAYMENT ERROR: Invalid order group input. -->";
            } elseif (!empty($this->orderPGroup)) {
                $htmlCode .= $this->createHiddenField('ORDER_PGROUP', $this->orderPGroup);
            }

            //add order pcode
            if (is_null($this->orderPCode) || empty($this->orderPCode)) {
                $htmlCode = "<!-- EPAYMENT ERROR: Invalid product codes. -->";
            } else {
                $htmlCode .= $this->createHiddenField('order_pcode', $this->orderPCode);
            }

            //add order pinfo
            if (is_null($this->orderPInfo)) {
                $htmlCode = "<!-- EPAYMENT ERROR: Invalid product info. -->";
            } elseif (!empty($this->orderPInfo)) {
                $htmlCode .= $this->createHiddenField('order_pinfo', $this->orderPInfo);
            }

            //add order prices
            if (is_null($this->orderPrice) || empty($this->orderPrice)) {
                $htmlCode = "<!-- EPAYMENT ERROR: Invalid order prices. -->";
            } else {
                $htmlCode .= $this->createHiddenField('ORDER_PRICE', $this->orderPrice);
            }

            //add order price types
            if (is_null($this->orderPType)) {
                $htmlCode = "<!-- EPAYMENT ERROR: Invalid order price types. -->";
            } else {
                $htmlCode .= $this->createHiddenField('ORDER_PRICE_TYPE', $this->orderPType);
            }

            //add order qty
            if (is_null($this->orderQty) || empty($this->orderQty)) {
                $htmlCode = "<!-- EPAYMENT ERROR: Invalid quantity. -->";
            } else {
                $htmlCode .= $this->createHiddenField('order_qty', $this->orderQty);
            }

            //add order vat
            if (is_null($this->orderVAT) || empty($this->orderVAT)) {
                $htmlCode = "<!-- EPAYMENT ERROR: Invalid order vat. -->";
            } else {
                $htmlCode .= $this->createHiddenField('order_vat', $this->orderVAT);
            }

            //add order ver
            if (is_null($this->orderVer)) {
                $htmlCode = "<!-- EPAYMENT ERROR: Invalid product versions. -->";
            } elseif (!empty($this->orderVer)) {
                $htmlCode .= $this->createHiddenField('order_ver', $this->orderVer);
            }
        } else {
            return "<!-- EPAYMENT ERROR - sent arrays are not syncronized (variable length) -->";
        }

        //add shipping
        if (is_null($this->orderShipping)) {
            return "<!-- EPAYMENT ERROR: Invalid shipping value. -->";
        } elseif ((is_numeric($this->orderShipping)) && ($this->orderShipping >= 0)) {
            $htmlCode .= $this->createHiddenField('ORDER_SHIPPING', $this->orderShipping, false);
        } else {
            $htmlCode .= "";
        }


        //add currency
        if (!empty($this->pricesCurrency)) {
            $htmlCode .= $this->createHiddenField('PRICES_CURRENCY', $this->pricesCurrency, false);
        }

        //add discount
        if (is_null($this->discount)) {
            return "<!-- EPAYMENT ERROR: Invalid discount. -->";
        } elseif ($this->discount) {
            $htmlCode .= $this->createHiddenField('discount', $this->discount, false);
        }

        //add destination city
        if (is_null($this->destinationCity)) {
            $htmlCode = "<!-- EPAYMENT ERROR: Invalid destination city. -->";
        } elseif (!empty($this->destinationCity)) {
            $htmlCode .= $this->createHiddenField('DESTINATION_CITY', $this->destinationCity, false);
        }

        //add destination state
        if (is_null($this->destinationState)) {
            return "<!-- EPAYMENT ERROR: Invalid destination state. -->";
        } elseif (!empty($this->destinationState)) {
            $htmlCode .= $this->createHiddenField('DESTINATION_STATE', $this->destinationState, false);
        }

        //add destination country
        if (is_null($this->destinationCountry)) {
            return "<!-- EPAYMENT ERROR: Invalid destination country code. -->";
        } elseif (!empty($this->destinationCountry)) {
            $htmlCode .= $this->createHiddenField('DESTINATION_COUNTRY', $this->destinationCountry, false);
        }

        //add pay method
        if (!empty($this->payMethod)) {
            $htmlCode .= $this->createHiddenField('PAY_METHOD', $this->payMethod, false);
        }

        //add order hash
        $hmacHash = $this->hmac($this->getHmacString());
        $htmlCode .= $this->createHiddenField('ORDER_HASH', $hmacHash, false);

        //add test mode
        if ($this->testMode) {
            $htmlCode .= "<input name=\"TESTORDER\" type=\"hidden\" value=\"TRUE\">\n";
        }

        //add billing information if it is available
        if ($this->billingSet) {
            $billingFields = array(
                "billFName" => 'BILL_FNAME',
                "billLName" => 'BILL_LNAME',
                "billCISerial" => 'BILL_CISERIAL',
                "billCINumber" => 'BILL_CINUMBER',
                "billCIIssuer" => 'BILL_CIISSUER',
                "billCNP" => 'BILL_CNP',
                "billCompany" => 'BILL_COMPANY',
                "billFiscalCode" => 'BILL_FISCALCODE',
                "billRegNumber" => 'BILL_REGNUMBER',
                "billBank" => 'BILL_BANK',
                "billBankAccount" => 'BILL_BANKACCOUNT',
                "billEmail" => 'BILL_EMAIL',
                "billPhone" => 'BILL_PHONE',
                "billFax" => 'BILL_FAX',
                "billAddress1" => 'BILL_ADDRESS',
                "billAddress2" => 'BILL_ADDRESS2',
                "billZipCode" => 'BILL_ZIPCODE',
                "billCity" => 'BILL_CITY',
                "billState" => 'BILL_STATE',
                "billCountryCode" => 'BILL_COUNTRYCODE',
            );
            foreach ($this->billing as $key => $val) {
                $htmlCode .= $this->createHiddenField($billingFields[$key], $this->billing[$key], false);
            }
        }

        //add delivery information if it is available
        if ($this->deliverySet) {
            $deliveryFields = array(
                "deliveryFName" => 'DELIVERY_FNAME',
                "deliveryLName" => 'DELIVERY_LNAME',
                "deliveryCompany" => 'DELIVERY_COMPANY',
                "deliveryPhone" => 'DELIVERY_PHONE',
                "deliveryAddress1" => 'DELIVERY_ADDRESS',
                "deliveryAddress2" => 'DELIVERY_ADDRESS2',
                "deliveryZipCode" => 'DELIVERY_ZIPCODE',
                "deliveryCity" => 'DELIVERY_CITY',
                "deliveryState" => 'DELIVERY_STATE',
                "deliveryCountryCode" => 'DELIVERY_COUNTRYCODE',
            );
            foreach ($this->delivery as $key => $val) {
                $htmlCode .= $this->createHiddenField($deliveryFields[$key], $this->delivery[$key], false);
            }
        }

        if (is_string($this->currency) && !empty($this->currency)) {
            $htmlCode .= $this->createHiddenField('CURRENCY', $this->currency, false);
        }

        if (is_string($this->language) && !empty($this->language)) {
            $htmlCode .= $this->createHiddenField('LANGUAGE', $this->language, false);
        }

        return $htmlCode;
    }

    /**
     * createHiddenField
     *
     * @access        private
     * @param        $fieldName    string    name/id of the hidden field in html code
     * @param        $fieldValue    string    field/fields value/values
     * @param        $isArray    bool    specifies if it should generate an array or not
     * @return        string    output html code
     */
    public function createHiddenField($fieldName, $fieldValue, $isArray = true)
    {
        $fieldName = strtoupper($fieldName);
        $retval = "";
        if ($isArray) {
            for ($i = 0; $i < count($fieldValue); $i++) {
                $fieldValue[$i] = htmlspecialchars($fieldValue[$i]);
                $retval .= "<input name=\"".$fieldName."[]\" type=\"hidden\" value=\"".$fieldValue[$i]."\" id=\"$fieldName\">\n";
                //$retval .= "<input name=\"".$fieldName."[]\" type=\"text\" value=\"".$fieldValue[$i]."\" id=\"$fieldName\">\n";
            }
        } else {
            $fieldValue = htmlspecialchars($fieldValue);
            $retval = "<input name=\"$fieldName\" type=\"hidden\" value=\"$fieldValue\" id=\"$fieldName\">\n";
            //$retval = "<input name=\"$fieldName\" type=\"text\" value=\"$fieldValue\" id=\"$fieldName\">\n";
        }

        return $retval;
    }

    /**
     * hmac
     *
     * Build HMAC key. THIS FUNCTION SHOULD NOT BE MODIFIED.
     *
     * @param        $array string secret key
     * @param        @data string the source string that will be converted into hmac hash
     * @return        string hmac hash
     */
    public function hmac($data)
    {
        $key = $this->secretKey;

        $b = 64; // byte length for md5
        if (strlen($key) > $b) {
            $key = pack("H*", md5($key));
        }
        $key = str_pad($key, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;

        return md5($k_opad.pack("H*", md5($k_ipad.$data)));
    }

    /**
     * getHmacString
     *
     * Creates source string for hmac hash
     * THIS FUNCTION SHOULD NOT BE MODIFIED.
     *
     * @access        private
     * @return        string
     */
    public function getHmacString()
    {
        $retval = "";
        $retval .= $this->expandString($this->merchant);
        $retval .= $this->expandString($this->orderRef);
        $retval .= $this->expandString($this->orderDate);
        $retval .= $this->expandArray($this->orderPName);
        $retval .= $this->expandArray($this->orderPCode);
        if (is_array($this->orderPInfo) && !empty($this->orderPInfo)) {
            $retval .= $this->expandArray($this->orderPInfo);
        }
        $retval .= $this->expandArray($this->orderPrice);
        $retval .= $this->expandArray($this->orderQty);
        $retval .= $this->expandArray($this->orderVAT);
        if (is_array($this->orderVer) && !empty($this->orderVer)) {
            $retval .= $this->expandArray($this->orderVer);
        }

        //if(!empty($this->orderShipping))
        if (is_numeric($this->orderShipping) && $this->orderShipping >= 0) {
            $retval .= $this->expandString($this->orderShipping);
        }

        if (is_string($this->pricesCurrency) && !empty($this->pricesCurrency)) {
            $retval .= $this->expandString($this->pricesCurrency);
        }
        if (is_numeric($this->discount) && !empty($this->discount)) {
            $retval .= $this->expandString($this->discount);
        }
        if (is_string($this->destinationCity) && !empty($this->destinationCity)) {
            $retval .= $this->expandString($this->destinationCity);
        }
        if (is_string($this->destinationState) && !empty($this->destinationState)) {
            $retval .= $this->expandString($this->destinationState);
        }
        if (is_string($this->destinationCountry) && !empty($this->destinationCountry)) {
            $retval .= $this->expandString($this->destinationCountry);
        }
        if (is_string($this->payMethod) && !empty($this->payMethod)) {
            $retval .= $this->expandString($this->payMethod);
        }
        if (is_array($this->orderPGroup) && count($this->orderPGroup)) {
            $retval .= $this->expandArray($this->orderPGroup);
        }
        if (is_array($this->orderPType) && count($this->orderPType)) {
            $retval .= $this->expandArray($this->orderPType);
        }

        return $retval;
    }

    /**
     * expandString
     *
     * Outputs a string for hmac format. For a string like 'a' it will return '1a'.
     *
     * @access        private
     * @param        $string string
     * @return        string
     */
    public function expandString($string)
    {
        $retval = "";
        //$string = htmlspecialchars($string);
        $size = strlen($string);
        $retval = $size.$string;

        return $retval;
    }

    /**
     * expandArray
     *
     * The same as expandString except that it receives an array of strings and
     * returns the string from all values within the array.
     *
     * @param        $array array
     * @return        string
     */
    public function expandArray($array)
    {
        $retval = "";
        for ($i = 0; $i < count($array); $i++) {
            $retval .= $this->expandString($array[$i]);
        }

        return $retval;
    }

    public function setBackRef($url)
    {
        $this->backRef = $url;
    }
}
