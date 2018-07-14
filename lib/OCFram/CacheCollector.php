<?php

namespace OCFram;


use Entity\Cache;

trait CacheCollector
{
    public function startingCache($app, $module, $id, $action)
    {
        $varCache = [
            'app'           => $app,
            'module'        => $module,
            'id'            => $id,
            'action'        => $action,
            'filename_data' => $module.'-'.$id.'.txt',
            'filename_view' => $app.'_'.$module.'_'.$action.'.txt',
            'timeCache'     => date(time())+(3600*24*3)
        ];

       return $varCache;
    }
}