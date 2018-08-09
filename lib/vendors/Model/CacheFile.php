<?php

namespace Model;

/**
 * Class CacheFile
 * @package Model
 *
 * Amelioration possible : configurer le type de serializer le serialiser php ou json (de plus en plus utilisé et cross language)
 */
class CacheFile
{


    private $_timeUp = array(
        'views' => 3600 * 24 * 3,
        'data' => 3600 * 24 * 10, // for all models
        'index' => 3600 * 24 * 10
    );

    private $_folders = array(
        'data' => '../tmp/cache/data',
        'views' => '../tmp/cache/views'
    );

    const ENTITY_NEWS = 'news';
    const ENTITY_COMMENT = 'comment';
    const LIST_CACHE = 'list';


    private $_data = null;
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
     * Méthode permettant de récupérer les datas mises en cache.
     * @param $filename
     * @return mixed
     */
    public function getCache($key)
    {
        $filename = $this->_getFilename($key);
        $this->_loadData($filename);
        return ((isset($this->_data[1])) ? $this->_data[1] : null);
    }

    /**
     * Méthode permettant de savoir si le fichier $filename est toujours valide.
     * @param $filename
     * @return bool
     */
    public function checkCacheValidy($key)
    {
        $filename = $this->_getFilename($key);

        if (date(time()) <= $this->getTimeStamp($filename)) {
            return true;
        } else {
            $this->deleteCache($filename);
            return false;
        }
    }

    private function _loadData($filename)
    {
        if (!$this->_data) {
            if (file_exists($filename)) {
                $this->_data = unserialize(file_get_contents($filename));
            }
        }
    }

    /**
     * Méthode permettant d'obtenir le timestamp du fichier $filename au moment de sa création.
     * @param $filename
     * @return mixed
     */
    public function getTimeStamp($filename)
    {
        $this->_loadData($filename);
        return ((isset($this->_data[0])) ? $this->_data[0] : false);

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

        //@FIXME review this, directory is hardcoded and the double foreach
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
     * @param $key
     */
    public function createCache($content, $type, $key)
    {
        if ($type === "views") {
            $data = array(date(time()) + $this->_timeUp[$type], file_get_contents($content));
            $filename = $key; //Not change View cache for moment
        } else {
            $data = array(date(time()) + $this->_timeUp[$type], $content);
            $filename = $this->_getFilename($key);
        }

        if (file_put_contents($filename, serialize($data)) === false) {
            exit; //FIXME add logs into application to save errors
        }
    }

    /**
     * Get filename by Cache Key
     * @param $key
     * @return string
     */
    private function _getFilename($key)
    {
        $keyParts = explode("-", $key);

        switch ($keyParts[0]) {

            case self::ENTITY_NEWS :
            case self::ENTITY_COMMENT :
            case self::LIST_CACHE :
                return $this->_folders["data"] . "/" . $key . ".txt";
                break;

            default:
                return $key;
                break;
        }
    }


}