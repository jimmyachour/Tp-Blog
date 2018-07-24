<?php

namespace App\Backend\Modules\Cache;

use Model\CacheFile;
use \OCFram\BackController;
use \OCFram\HTTPRequest;

class CacheController extends BackController
{
    public function executeIndex(HTTPRequest $request)
    {
        $activeCache = $this->app->config()->get('activeCache');

        $this->page->addVar('title', 'Configuration du cache');
        $this->page->addVar('activeCache', $activeCache);

        if($request->method() == 'POST')
        {
            $cacheConfig = array(
                'activeCache' => $request->postData('activeCache')
            );
            $this->saveConfig($cacheConfig);
        }
    }

    public function saveConfig($cacheConfig)
    {
        foreach ($cacheConfig as $element => $value)
        {
            $this->app->config()->changeConfig($element, $value);
        }

        $this->app->httpResponse()->redirect('cache-config.html');
    }

    public function executeDeleteCache()
    {
        $cacheFile = new CacheFile();
        $cacheFile->deleteCache();

        $this->app->httpResponse()->redirect('cache-config.html');
    }
}