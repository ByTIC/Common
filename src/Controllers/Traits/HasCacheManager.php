<?php

namespace ByTIC\Common\Controllers\Traits;

use Nip_Cache_Manager as CacheManager;

trait HasCacheManager
{
    /**
     * @var CacheManager
     */
    protected $_cacheManager = null;

    /**
     * @return CacheManager
     */
    public function getCacheManager()
    {
        if ($this->_cacheManager == null) {
            $this->initCacheManager();
        }
        return $this->_cacheManager;
    }

    /**
     * @return CacheManager
     */
    protected function initCacheManager()
    {
        $this->_cacheManager = $this->newCacheManager();
        return $this;
    }


    /**
     * @return CacheManager
     */
    protected function newCacheManager()
    {
        return new CacheManager();
    }
}