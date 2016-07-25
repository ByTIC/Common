<?php

namespace ByTIC\Common\Controllers\Traits;

/**
 * Class HasStatus
 * @package ByTIC\Common\Controllers\Traits
 *
 * @property \ApplicationController $this
 *
 */
trait HasStatus
{

    public function initViewStatuses()
    {
        $this->getView()->set('statuses', $this->getModelManager()->getStatuses());
    }

    public function changeStatus()
    {
        $this->item = $this->checkItem();

        $status = $_GET['status'];
        $availableStatuses = $this->getModelManager()->getStatusProperty('name');
        if (in_array($status, $availableStatuses)) {
            $this->item->setStatus($status);
            $this->changeStatusRedirect($this->item);
        }
        $this->flashRedirect($this->getModelManager()->getMessage('status.invalid-status'), $redirect, 'error');
    }

    public function changeStatusRedirect($item)
    {
        $redirect = $_SERVER['HTTP_REFERER'];
        $this->flashRedirect($this->getModelManager()->getMessage('status.success'), $redirect);
    }

}