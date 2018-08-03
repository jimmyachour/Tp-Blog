<?php

namespace Model;

class CacheFile
{
    private $_timeUp = array(
        'views' => 3600 * 24 * 3,
        'datas' => 3600 * 24 * 10,
        'comments' => 3600 * 24 * 10,
        'index' => 3600 * 24 * 10
    );

    private $_folders = array(
        'datas' => '../tmp/cache/datas',
        'views' => '../tmp/cache/views',
        'comments' => '../tmp/cache/comments'
    );

    private $dir_cacheConfig = '../App/Backend/Config/app.xml';

    /**
     * Méthode permettant de savoir si le parametre cache est activé.
     * @return bool
     */
    public function isActivated()
    {
        $xml = new \DOMDocument;
        $xml->load($this->dir_cacheConfig);

        $elements = $xml->getElementsByTagName('define');

        foreach ($elements as $element) {
            if ($element->getAttribute('var') == 'activeCache' && $element->getAttribute('value') === '1') {
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
    public function checkCacheValidy($filename)
    {
        if (date(time()) <= $this->getTimeStamp($filename)) {
            return true;
        } else {
            $this->deleteCache($filename);
            return false;
        }

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
    public function deleteCache($filename)
    {
        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    /**
     * Méthode permettant de supprimer la totalité du cache.
     */
    public function deleteAllCache()
    {
        $folders = $this->_folders;

        foreach ($folders as $folder => $dir) {
            $dir_iterator = new \RecursiveDirectoryIterator($dir);
            $iterator = new \RecursiveIteratorIterator($dir_iterator);

            foreach ($iterator as $dirName => $file) {
                if ($dirName != "../tmp/cache/$folder\." && $dirName != "../tmp/cache/$folder\..") {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Méthode permettant de mettre en cache les datas $dataPDO à l'emplacement $filename.
     * @param $content
     * @param $type
     * @param $fileName
     */
    public function createCache($content, $type , $fileName)
    {
        if($type === 'views')
        {
            $cacheFile = array(date(time()) + $this->_timeUp[$type], file_get_contents($content));
        }
        else
        {
            $cacheFile = array(date(time()) + $this->_timeUp[$type], $content);
        }

        $cacheFile_serialize = serialize($cacheFile);

        $file = fopen($fileName, 'a+');

        if (fwrite($file, $cacheFile_serialize) === false) {
            exit;
        }
        fclose($file);
    }

    /**
     * Méthode permettant de récupérer les datas mises en cache.
     * @param $filename
     * @return mixed
     */
    public function getCache($filename)
    {
        $data_cache = file_get_contents($filename, "r");

        $data_unserialized = unserialize($data_cache);

            return $data_unserialized[1];
    }

}