<?php

namespace Model;

use\Entity\Cache;
use OCFram\Page;
// A Supprimer si plus utilisé
class CacheManager
{

    public function createDataCache(Cache $cache)
    {
        $content_serialize = serialize($cache['data']);

        $file = fopen('../tmp/cache/datas/'.$cache['filename_data'],'w+');

        fwrite($file,  $content_serialize);

        fclose($file);
    }

    public function createViewCache(Cache $cache)
    {
        $view_to_cache = '../App/'.$cache['app'].'/Modules/'.$cache['module'].'/Views/'.$cache['action'].'.php';

        $file_to_cache = file_get_contents($view_to_cache);

        $file_serialized = serialize($file_to_cache);

        $file = fopen('../tmp/cache/views/'.$cache['filename_view'],'w+');

        fwrite($file,  $file_serialized);
        fclose($file);
    }

    public function getDataCache(Cache $cache)
    {
        $data_cache = file_get_contents('../tmp/cache/datas/'.$cache['filename_data'], "r");

        $data_unserialized = unserialize($data_cache);

        return $data_unserialized;

    }

    public function getViewCache(Cache $cache)
    {
        $view_cache = file_get_contents('../tmp/cache/views/'.$cache['filename_view']);

        $view_unserialized = unserialize($view_cache);

        return $view_unserialized;
    }

}