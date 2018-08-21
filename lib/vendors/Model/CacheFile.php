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

    private $_errorLogs = '../tmp/cache/errorLogs.xml';

    private $_folders = array(
        'data' => '../tmp/cache/data',
        'views' => '../tmp/cache/views',
    );

    const ENTITY_NEWS = 'news';
    const ENTITY_COMMENT = 'comment';
    const LIST_CACHE = 'list';
    const VIEW_CACHE = 'view';

    private $_data = null;
    private $_view = null;

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
     * Méthode permettant d'obtenir le timestamp du fichier $filename au moment de sa création.
     * @param $filename
     * @return mixed
     */
    public function getTimeStamp($type, $filename)
    {
        $this->_loadData($filename);
        return ((isset($this->_data[0])) ? $this->_data[0] : false);
    }

    /**
     * Méthode permettant de savoir si le fichier $filename est toujours valide.
     * @param $filename
     * @return bool
     */
    public function checkCacheValidy($type, $key)
    {
        $filename = $this->_getFilename($type, $key);

        if (date(time()) <= $this->getTimeStamp($type, $filename)) {
            return true;
        } else {
            $this->deleteCache($filename);
            return false;
        }
    }

    /**
     * Get filename by Cache Key
     * @param $key
     * @param $type
     * @return string
     */
    private function _getFilename($type, $key)
    {
        $keyParts = explode("-", $key);
        switch ($keyParts[0]) {
            case self::ENTITY_NEWS :
            case self::ENTITY_COMMENT :
            case self::LIST_CACHE :
            case self::VIEW_CACHE :
                return $this->_folders[$type] . "/" . $key . ".txt";
                break;
            default:
                return $key;
                break;
        }
    }

    /**
     * Méthode permettant de mettre en cache les data $dataPDO à l'emplacement $filename.
     * @param $content
     * @param $type
     * @param $key
     */
    public function createCache($content, $type, $key)
    {
        if ($type === "views") {

            $data = array(date(time()) + $this->_timeUp[$type], $content);
            $filename = $this->_getFilename($type, $key); //Not change View cache for moment

        } else {

            $data = array(date(time()) + $this->_timeUp[$type], $content);
            $filename = $this->_getFilename($type, $key);

        }

        if (file_put_contents($filename, serialize($data)) === false) {
            $this->errorLogs($filename);
            exit;
        }
    }

    private function _loadData($filename)
    {

        if (!$this->_data && !$this->_view) {
            if (file_exists($filename)) {
                $this->_data = unserialize(file_get_contents($filename));
                $this->_view = unserialize(file_get_contents($filename));
            }
        }
    }

    /**
     * Méthode permettant de récupérer les data mises en cache.
     * @param $filename
     * @return mixed
     */
    public function getCache($type, $key)
    {
        $filename = $this->_getFilename($type, $key);
        $this->_loadData($filename);
        return ((isset($this->_data[1])) ? $this->_data[1] : null);
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
        foreach ($this->_folders as $dir) {
            $dir_iterator = new \RecursiveDirectoryIterator($dir);
            $iterator = new \RecursiveIteratorIterator($dir_iterator);

            foreach ($iterator as $file) {
                if ($file != $dir . '\\.' && $file != $dir . '\\..') {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Write error in cache's log
     * @param $filename
     */
    public function errorLogs($filename)
    {

        $xml = new \DOMDocument('1.0', 'utf-8');

        $xml->appendChild($error = $xml->createElement('ERROR'));

        $error->appendChild($xml->createElement('time', date('Y-m-d H:i:s')));
        $error->appendChild($xml->createElement('filename', $filename));

        $xmlContent = $xml->saveXML();

        file_put_contents($this->_errorLogs, $xmlContent);
    }

}