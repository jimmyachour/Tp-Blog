<?php

namespace OCFram;

use Entity\Cache;
use Model\CacheManager;

class CacheController
{
    public function executeSaveCache(Cache $cache)
    {
        $cacheManager = new CacheManager();
        $cacheManager->createDataCache($cache);
        $cacheManager->createViewCache($cache);
    }

    public function executeGetDataCache(Cache $cache)
    {
        $cacheManager = new CacheManager();
        $dataCache = $cacheManager->getDataCache($cache);


        return $dataCache;
    }

    public function executeGetViewCache(Cache $cache)
    {
        $cacheManager = new CacheManager();
        $viewCache = $cacheManager->getViewCache($cache);

        return $viewCache;
    }



}