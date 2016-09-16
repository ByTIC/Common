<?php

namespace ByTIC\Common\Records\Traits\HasStatus;

use ByTIC\Common\Records\Statuses\Generic as GenericStatus;
use Exception;
use Nip\Records\Record;

trait RecordsTrait
{
    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordsTrait;

    protected $_statuses = null;
    protected $_statusesPath = null;

    public function getStatusProperty($name)
    {
        $return = array();
        $types = $this->getStatuses();

        foreach ($types as $type) {
            $return[] = $type->$name;
        }

        return $return;
    }

    public function getStatuses()
    {
        if ($this->_statuses == null) {
            $this->initStatuses();
        }
        return $this->_statuses;
    }

    public function initStatuses()
    {
        $files = \Nip_File_System::instance()->scanDirectory($this->getStatusesDirectory());
        $this->_statuses = array();
        foreach ($files as $name) {
            $name = str_replace('.php', '', $name);
            if (!in_array($name, array('Abstract', 'Generic', 'AbstractStatus'))) {
                $object = $this->newStatus($name);
                $this->_statuses[$object->getName()] = $object;
            }
        }
    }

    public function getStatusesDirectory()
    {
        if ($this->_statusesPath == null) {
            $this->initStatusesDirectory();
        }
        return $this->_statusesPath;
    }

    public function initStatusesDirectory()
    {
        $reflector = new \ReflectionObject($this);
        $this->_statusesPath = dirname($reflector->getFileName()) . '/Statuses';
    }

    /**
     * @param string $name
     * @return GenericStatus
     * @throws Exception
     */
    public function getStatus($name = null)
    {
        $statuses = $this->getStatuses();
        if (!isset($statuses[$name])) {
            throw new Exception('Bad status [' . $name . '] for [' . $this->getController() . ']');
        }
        return $statuses[$name];
    }

    /**
     * @param string $type
     * @return GenericStatus
     */
    public function newStatus($type = null)
    {
        $className = $this->getStatusClass($type);
        $object = new $className();
        /** @var GenericStatus $object */
        $object->setManager($this);
        return $object;
    }

    public function getStatusClass($type = null)
    {
        $type = $type ? $type : $this->getDefaultStatus();
        return $this->getStatusRootNamespace() . inflector()->classify($type);
    }

    public function getStatusRootNamespace()
    {
        return $this->getRootNamespace() . inflector()->classify($this->getController()) . '\Statuses\\';
    }

    public function getDefaultStatus()
    {
        return 'in-progress';
    }
}