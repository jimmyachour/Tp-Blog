<?php

namespace Model;

use \OCFram\Manager;
use \Entity\News;

abstract class NewsManager extends Manager
{
    /**
     * Méthode permettant d'ajouter une news.
     * @param $news News La news à ajouter
     * @return void
     */
    abstract protected function add(News $news);

    public function save(News $news)
    {
        if ($news->isValid()) {
            $news->isNew() ? $this->add($news) : $this->modify($news);
        } else {
            throw new \RuntimeException('La news doit être validée pour être enregistrée');
        }
    }

    /**
     * Méthode renvoyant le nombre de news total.
     * @return int
     */
    abstract public function count();

    /**
     * Méthode permettant de supprimer une news.
     * @param $id int L'identifiant de la news à supprimer
     * @return void
     */
    abstract public function delete($id);

    /**
     * Méthode retournant une liste de news demandée.
     * @param $debut int La première news à sélectionner
     * @param $limite int Le nombre de news à sélectionner
     * @param $type string le type de donnée à mettre en cache
     * @param $dir_cache chemin d'accès où enregistrer les données
     * @return array La liste des news. Chaque entrée est une instance de News.
     */
    public function getList($debut = -1, $limite = -1,$type = null ,$dir_cache = null)
    {
        $dataCache = new CacheFile;

        if ($dataCache->isActivated() == true && file_exists($dir_cache) && $dataCache->checkCacheValidy($dir_cache) == true) {
            return $dataCache->getCache($dir_cache);

        } else {

            $dataPDO = $this->getListPDO($debut = -1, $limite = -1);

            if ($dataCache->isActivated() == true && $type != null && $dir_cache != null) {

                $dataCache->createCache($dataPDO, $type, $dir_cache);
            }

            return $dataPDO;
        }

    }

    abstract public function getListPDO($debut = -1, $limite = -1);

    /**
     * Méthode retournant une news précise.
     * @param $id int L'identifiant de la news à récupérer
     * @param $type string le type de donnée à mettre en cache
     * @param $dir_cache chemin d'accès où enregistrer les données
     * @return News La news demandée
     */
    public function getUnique($id, $type = null, $dir_cache = null)
    {
        $dataCache = new CacheFile;

        if ($dataCache->isActivated() == true && file_exists($dir_cache) && $dataCache->checkCacheValidy($dir_cache) === true) {
            return $dataCache->getCache($dir_cache);
        } else {
            $dataPDO = $this->getUniquePDO($id);

            if ($dataCache->isActivated() == true && $type != null && $dir_cache != null) {
                $dataCache->createCache($this->getUniquePDO($id), $type,  $dir_cache);
            }

            return $dataPDO;
        }

    }

    abstract public function getUniquePDO($id);

    /**
     * Méthode permettant de modifier une news.
     * @param $news news la news à modifier
     * @return void
     */
    abstract protected function modify(News $news);
}