<?php

namespace ByTIC\Common\Payments\Gateways\AbstractGateway;

use Nip_Form as NipForm;

/**
 * Class Form
 * @package ByTIC\Common\Payments\Gateways\AbstractGateway
 */
abstract class Form
{

    /**
     * @var Gateway
     */
    protected $gateway;

    /**
     * @var NipForm
     */
    protected $form;

    protected $elements;

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (strpos($name, 'add') === 0) {
            $type = str_replace('add', '', $name);
            $type[0] = strtolower($type[0]);
            $name = '' . $this->getGateway()->getName() . '[' . $arguments[0] . ']';
            $this->elements[$arguments[0]] = $name;
            $label = $arguments[1];
            $isRequired = $arguments[2];
            return $this->getForm()->add($name, $label, $type, $isRequired);
        }

        trigger_error('Call to undefined method: [' . $name . ']', E_USER_ERROR);
        return false;
    }

    /**
     * @return Gateway
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @param Gateway $gateway
     * @return $this
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;
        return $this;
    }

    /**
     * @return NipForm
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param $form
     * @return $this
     */
    public function setForm($form)
    {
        $this->form = $form;
        return $this;
    }

    public function init()
    {
        $this->initElements();
        if (is_array($this->elements) && count($this->elements) > 0) {
            $this->getForm()->addDisplayGroup($this->elements, $this->getGateway()->getLabel());
            $this->getForm()->getDisplayGroup($this->getGateway()->getLabel())
                ->setAttrib('class', 'payment_gateway_fieldset')
                ->setAttrib('id', 'payment_gateway_' . $this->getGateway()->getName());
        }
    }

    /**
     * @return bool
     */
    public function initElements()
    {
        return false;
    }

    /**
     *
     */
    public function getDataFromModel()
    {
        if (is_array($this->elements) && count($this->elements) > 0) {
            $gName = $this->getGateway()->getName();
            $options = $this->getForm()->getModel()->getOption($gName);
            foreach ($this->elements as $name => $inputName) {
                $element = $this->getForm()->$inputName;
                $element->setValue($options[$name]);
            }
        }
    }


    /**
     * @param $request
     * @return bool
     */
    public function getDataFromRequest($request)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function processValidation()
    {
        return true;
    }


    public function saveToModel()
    {
        if (is_array($this->elements) && count($this->elements) > 0) {
            $gName = $this->getGateway()->getName();
            if ($this->getForm()->getModel()->getOption('payment_gateway') == $gName) {
                $options = [];
                foreach ($this->elements as $name => $inputName) {
                    $element = $this->getForm()->$inputName;
                    $options[$name] = $element->getValue();
                }

                $this->getForm()->getModel()->setOption($gName, $options);
            }
        }
    }

    public function process()
    {
        return true;
    }
}
