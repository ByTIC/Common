<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\RedirectResponse;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\AbstractRequest;
use Nip\View;
use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Common\Message\RedirectResponseInterface;
use Symfony\Component\HttpFoundation\RedirectResponse as HttpRedirectResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Class RedirectResponseTrait
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages\RedirectResponse
 *
 * @method AbstractRequest getRequest
 * @method array getData
 */
trait RedirectTrait
{

    /**
     * @var View
     */
    protected $view = null;

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return false;
    }

    /**
     * Returns whether the transaction should continue
     * on a redirected page
     *
     * @return boolean
     */
    public function isRedirect()
    {
        return true;
    }

    /**
     * @return HttpRedirectResponse|HttpResponse
     */
    public function getRedirectResponse()
    {
        if (!$this instanceof RedirectResponseInterface || !$this->isRedirect()) {
            throw new RuntimeException('This response does not support redirection.');
        }

        if ('GET' === $this->getRedirectMethod()) {
            return HttpRedirectResponse::create($this->getRedirectUrl());
        } elseif ('POST' === $this->getRedirectMethod()) {
            $hiddenFields = $this->getInputsHTML();
            $output = $this->getRedirectHTML();

            return HttpResponse::create($output);
        }

        throw new RuntimeException('Invalid redirect method "'.$this->getRedirectMethod().'".');
    }

    /**
     * Returns redirect URL method
     *
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'POST';
    }

    /**
     * Returns the redirect URL
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getRequest()->getEndpoint();
    }

    /**
     * @return string
     */
    public function getInputsHTML()
    {
        $hiddenFields = '';
        foreach ($this->getRedirectData() as $key => $value) {
            $hiddenFields .= sprintf(
                    '<input type="hidden" name="%1$s" value="%2$s" />',
                    htmlentities($key, ENT_QUOTES, 'UTF-8', false),
                    htmlentities($value, ENT_QUOTES, 'UTF-8', false)
                )."\n";
        }

        return $hiddenFields;
    }

    /**
     * Returns the FORM data for the redirect
     *
     * @return array
     */
    public function getRedirectData()
    {
        return $this->getData();
    }

    /**
     * @return string
     */
    public function getRedirectHTML()
    {
        return $this->getView()->load($this->getViewFile());
    }

    /**
     * @return View|null
     */
    public function getView()
    {
        if ($this->view === null) {
            $this->initView();
        }

        return $this->view;
    }

    public function initView()
    {
        $this->view = new View();
        $this->view->set('response', $this);
        $this->view->setBasePath(dirname(__FILE__).DIRECTORY_SEPARATOR);
    }

    /**
     * @return string
     */
    public function getViewFile()
    {
        return '/redirect';
    }
}
