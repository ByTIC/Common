<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway;

use ByTIC\Common\Payments\Gateways\Manager;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\AbstractRequest;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\CompletePurchaseResponse;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\PurchaseResponse;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\ServerCompletePurchaseResponse;
use ByTIC\Common\Payments\Models\Methods\Traits\RecordTrait as PaymentMethodRecord;
use ByTIC\Common\Payments\Models\Purchase\Traits\IsPurchasableModelTrait;
use ByTIC\Common\Records\Record;
use ByTIC\Common\Records\Traits\Media\Files\RecordTrait as HasFilesRecord;
use Nip\Utility\Traits\NameWorksTrait;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\ResponseInterface as MessageResponseInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * Class Gateway
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway
 *
 * @method MessageResponseInterface authorize(array $options = [])
 * @method MessageResponseInterface completeAuthorize(array $options = [])
 * @method MessageResponseInterface capture(array $options = [])
 * @method MessageResponseInterface refund(array $options = [])
 * @method MessageResponseInterface void(array $options = [])
 * @method MessageResponseInterface createCard(array $options = [])
 * @method MessageResponseInterface updateCard(array $options = [])
 * @method MessageResponseInterface deleteCard(array $options = [])
 */
abstract class Gateway extends AbstractGateway
{

    use NameWorksTrait;

    /**
     * @var null|string
     */
    protected $name = null;

    /**
     * @var null|string
     */
    protected $label = null;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var PaymentMethodRecord
     */
    protected $paymentMethod;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @param array $parameters
     * @return CompletePurchaseResponse
     */
    public function completePurchase(array $parameters = [])
    {
        return $this->createNamepacedRequest('CompletePurchaseRequest', $parameters);
    }

    /**
     * @param $class
     * @param array $parameters
     * @return AbstractRequest|null
     */
    protected function createNamepacedRequest($class, array $parameters)
    {
        $class = $this->getNamespacePath().'\Message\\'.$class;

        if (class_exists($class)) {
            return $this->createRequest($class, $parameters); // TODO: Change the autogenerated stub
        }

        return null;
    }

    /**
     * @param array $parameters
     * @return ServerCompletePurchaseResponse
     */
    public function serverCompletePurchase(array $parameters = [])
    {
        return $this->createNamepacedRequest('ServerCompletePurchaseRequest', $parameters);
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        if ($this->name === null) {
            $this->initName();
        }

        return $this->name;
    }

    /**
     * @param null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function initName()
    {
        $this->setName($this->generateName());
    }

    /**
     * @return string
     */
    protected function generateName()
    {
        return strtolower($this->getLabel());
    }

    /**
     * @return null|string
     */
    public function getLabel()
    {
        if ($this->label === null) {
            $this->initLabel();
        }

        return $this->label;
    }

    /**
     * @param null|string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function initLabel()
    {
        $this->setLabel($this->generateLabel());
    }

    /**
     * @return string
     */
    public function generateLabel()
    {
        return $this->getNamespaceParentFolder();
    }

    /**
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param Manager $manager
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return PaymentMethodRecord|Record|HasFilesRecord
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param PaymentMethodRecord $paymentMethod
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @param HttpRequest $httpRequest
     */
    public function setHttpRequest($httpRequest)
    {
        $this->httpRequest = $httpRequest;
    }

    /**
     * @return Form
     */
    public function getOptionsForm()
    {
        if (!$this->form) {
            $this->initOptionsForm();
        }

        return $this->form;
    }

    public function initOptionsForm()
    {
        $this->form = $this->newOptionsForm();
    }

    /**
     * @return Form
     */
    public function newOptionsForm()
    {
        $class = $this->getNamespacePath().'\Form';
        $form = new $class();
        /** @var Form $form */
        $form->setGateway($this);

        return $form;
    }

    /**
     * @param IsPurchasableModelTrait $record
     * @return AbstractRequest
     */
    public function purchaseFromModel($record)
    {
        $parameters = $record->getPurchaseParameters();

        return $this->purchase($parameters);
    }

    /**
     * @param array $parameters
     * @return PurchaseResponse
     */
    public function purchase(array $parameters = [])
    {
        return $this->createNamepacedRequest('PurchaseRequest', $parameters);
    }
}