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
    public function getList($debut = -1, $limite = -1, $cache = 0)
    {
        $dataCache = new CacheFile;
        $cacheKey = sprintf("list-news-%d-%d", $debut, $limite);
        $dataPDO = null;

        if ($cache && $dataCache->isActivated() == true && $dataCache->checkCacheValidy('data', $cacheKey) == true) {
            $dataPDO = $dataCache->getCache('data', $cacheKey);
        }
        if (!$dataPDO) {
            $dataPDO = $this->getListPDO($debut, $limite);
            if ($dataCache->isActivated() == true) {
                $dataCache->createCache($dataPDO, "data", $cacheKey);
            }
        }
        return $dataPDO;
    }
    abstract public function getListPDO($debut = -1, $limite = -1);
    /**
     * Méthode retournant une news précise.
     * @param $id int L'identifiant de la news à récupérer
     * @return News La news demandée
     */
    public function getUnique($id, $cache = 0)
    {
        $dataCache = new CacheFile;
        $cacheKey = "news-" . $id;
        $dataPDO = null;
        if ($cache && $dataCache->isActivated() == true && $dataCache->checkCacheValidy('data', $cacheKey) === true) {
            $dataPDO = $dataCache->getCache('data', $cacheKey);
        }
        if (!$dataPDO) {
            $dataPDO = $this->getUniquePDO($id);
            if ($dataCache->isActivated() == true) {
                $dataCache->createCache($dataPDO, 'data', $cacheKey);
            }
        }
        return $dataPDO;
    }
    abstract public function getUniquePDO($id);
    /**
     * Méthode permettant de modifier une news.
     * @param $news news la news à modifier
     * @return void
     */
    abstract protected function modify(News $news);
}