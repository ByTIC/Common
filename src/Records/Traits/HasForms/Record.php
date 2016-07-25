<?php

namespace ByTIC\Common\Records\Traits\HasForms;

trait RecordTrait
{

    protected $_forms = array();

    /**
     * @param string $type
     * @return \Nip_Form
     */
    public function getForm($type = NULL)
    {
        if (!$this->_forms[$type]) {
            $form = $this->getManager()->newForm($type);

            $this->_forms[$type] = $this->initForm($form);
        }

        return $this->_forms[$type];
    }

    public function initForm($form)
    {
        $form->setModel($this);
        return $form;
    }

}