<?php

namespace Model;

class CacheFile
{
    private $_perempTime = array(
        'views' => 3600*24*3,
        'datas' => 3600*24*10
    );

    /**
     * Méthode permettant de savoir si le parametre cache est activé.
     * @return bool
     */
    public function isActivated()
    {
        $xml = new \DOMDocument;
        $xml->load('../App/Backend/Config/app.xml');

        $elements = $xml->getElementsByTagName('define');

        foreach ($elements as $element)
        {
            if($element->getAttribute('var') == 'activeCache' && $element->getAttribute('value') === '1')
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Méthode permettant de savoir si le fichier $filename est toujours valide.
     * @param $filename
     * @return bool
     */
    public function verifyPeremptionCache($filename)
    {
        $timesTamp = $this->getTimeStamp($filename);

        if(date(time()) <= $timesTamp)
        {
            return true;
        }
        else
        {
           $this->deleteThisCache($filename);
           return false;
        }

    }

    public function getPerempTime($type)
    {
        $addTime = null;

        foreach ($this->_perempTime as $key => $value)
        {
            if($key == $type)
            {
                $addTime = $value;
            }
        }
        return $addTime;
    }

    /**
     * Méthode permettant d'obtenir le timestamp du fichier $filename au moment de sa création.
     * @param $filename
     * @return mixed
     */
    public function getTimeStamp($filename)
    {
        if (file_exists($filename)) {

            $data_cache = file_get_contents($filename, "r");

            $timestamp_unserialized = unserialize($data_cache);

            return $timestamp_unserialized[0];

        }
    }

    /**
     * Méthode permettant de supprimer le fichier $filename.
     * @param $filename
     */
    public function deleteThisCache($filename)
    {
        if(file_exists($filename))
        {
            unlink($filename);
        }
    }

    /**
     * Méthode permettant de supprimer la totalité du cache.
     */
    public function deleteCache()
    {
        $folders = array(
            'datas' => '../tmp/cache/datas',
            'views' => '../tmp/cache/views'
        );
        foreach ($folders as $folder => $dir)
        {
            $dir_iterator = new \RecursiveDirectoryIterator($dir);
            $iterator = new \RecursiveIteratorIterator($dir_iterator);

            foreach ($iterator as $dirName => $file)
            {
               if($dirName != "../tmp/cache/$folder\." && $dirName != "../tmp/cache/$folder\..")
               {
                    unlink($file);
               }
            }
        }
    }

    /**
     * Méthode permettant de mettre en cache les datas $dataPDO à l'emplacement $filename.
     * @param $dataPDO
     * @param $filename
     */
    public function createDataCache($dataPDO, $filename)
    {
        $perempDate = date(time())+$this->getPerempTime('datas');

        $content = array($perempDate, $dataPDO);

        $content_serialize = serialize($content);

        $file = fopen($filename,'a+');

        fwrite($file,  $content_serialize);

        fclose($file);
    }

    /**
     * Méthode permettant de mettre en cache les views $viewDir.
     * @param $viewDir
     * @param $app
     * @param $module
     * @param $view
     */
    public function createViewCache($viewDir, $app, $module, $view)
    {
        $perempDate = date(time())+$this->getPerempTime('views');

        $content = array($perempDate,file_get_contents($viewDir));

        $file_serialized = serialize($content);

        $file = fopen('../tmp/cache/views/'.$app.'_'.$module.'_'.$view.'.txt','w+');

        fwrite($file,  $file_serialized);

        fclose($file);
    }

    /**
     * Méthode permettant de mettre en cache les datas $dataPDO des pages index à l'emplacement $filename.
     * @param $dataPDO
     * @param $app
     * @param $module
     */
    public function createIndexCache($dataPDO, $app, $module)
    {
        $perempDate = date(time())+(3600*24*3);

        $content = array($perempDate,$dataPDO);

        $content_serialize = serialize($content);

        $file = fopen('../tmp/cache/datas/'.$app.$module.'-index.txt','a+');

        fwrite($file,$content_serialize);

        fclose($file);

    }

    /**
     * Méthode permettant de récupérer les datas mises en cache.
     * @param $filename
     * @return mixed
     */
    public function getDataCache($filename)
    {
        $data_cache = file_get_contents($filename, "r");

        $data_unserialized = unserialize($data_cache);

        return $data_unserialized[1];

    }

    /**
     *  Méthode permettant de récupérer les views mises en cache.
     * @param $filename
     * @return mixed
     */
    public function getViewCache($filename)
    {
        $view_cache = file_get_contents($filename, "r");

        $view_unserialized = unserialize($view_cache);

        return $view_unserialized[1];
    }

}