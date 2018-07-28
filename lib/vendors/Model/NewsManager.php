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
        if ($news->isValid())
        {
            $news->isNew() ? $this->add($news) : $this->modify($news);
        }
        else
        {
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
     * @return array La liste des news. Chaque entrée est une instance de News.
     */
    // revenir sur le prototype std getList($debut = -1, $limite = -1)
    // pas compris l'ajout de app et module ...
    // ton cache doit être calculé en fonction de debut et limite
    // tu peux par exemple faire md5($debut.':'.$limite) pour avoir un id de cache
    public function getList($debut = -1, $limite = -1, $app, $module)
    {
        $dataCache = new CacheFile;

        // ne doit pas être connu du manager
        $filename = '../tmp/cache/datas/'.$app.$module.'-index.txt';

        // pas mal comme implementation, ah voir si en une lecture fichier tu peux checker le timestamp et renvoyer les données
        // optimization un seul I/O pour le même fichier
        if($dataCache->isActivated() == true && $dataCache->verifyPeremptionCache($filename) === true && file_exists($filename))
        {
            return $dataCache->getDataCache($filename);

        }else {

            $dataPDO = $this->getListPDO($debut = -1, $limite = -1);

            if ($dataCache->isActivated() == true) {

                $dataCache->createIndexCache($dataPDO, $app, $module);

            }

            return $dataPDO;
        }

    }
    abstract public function getListPDO($debut = -1, $limite = -1);

    /**
     * Méthode retournant une news précise.
     * @param $id int L'identifiant de la news à récupérer
     * @return News La news demandée
     */
    public function getUnique($id,$module)
    {
        $dataCache = new CacheFile;
        // le fichier de cache n'a pas a être connu du Manager,
        // lui doit donner juste l'id de cache de la ressource qu'il souhaite traiter
        $filename = '../tmp/cache/datas/'.$module.'-'.$id.'.txt';

        if($dataCache->isActivated() == true && file_exists($filename) && $dataCache->verifyPeremptionCache($filename) === true)
        {
            return $dataCache->getDataCache($filename);
        }
        else
        {
            $dataPDO = $this->getUniquePDO($id); // methode appelé deux fois ...

            if($dataCache->isActivated() == true)
            {
                $dataCache->createDataCache($this->getUniquePDO($id), $filename);
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