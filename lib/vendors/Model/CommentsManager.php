<?php
namespace Model;
use \OCFram\Manager;
use \Entity\Comment;
abstract class CommentsManager extends Manager
{
    /**
     * Méthode permettant d'ajouter un commentaire.
     * @param $comment Le commentaire à ajouter
     * @return void
     */
    abstract protected function add(Comment $comment);
    /**
     * Méthode permettant de supprimer un commentaire.
     * @param $id L'identifiant du commentaire à supprimer
     * @return void
     */
    abstract public function delete($id);
    /**
     * Méthode permettant de supprimer tous les commentaires liés à une news
     * @param $news L'identifiant de la news dont les commentaires doivent être supprimés
     * @return void
     */
    abstract public function deleteFromNews($news);
    /**
     * Méthode permettant d'enregistrer un commentaire.
     * @param $comment Le commentaire à enregistrer
     * @return void
     */
    public function save(Comment $comment)
    {
        if ($comment->isValid()) {
            $comment->isNew() ? $this->add($comment) : $this->modify($comment);
        } else {
            throw new \RuntimeException('Le commentaire doit être validé pour être enregistré');
        }
    }
    /**
     * Méthode permettant de récupérer une liste de commentaires.
     * @param $news La news sur laquelle on veut récupérer les commentaires
     * @return array
     */
    public function getListOf($news, $cache = 0)
    {
        $commentsCache = new CacheFile;
        $cacheKey = sprintf("list-comment-%d", $news);
        $commentsListPDO = null;
        if ($cache && $commentsCache->isActivated() == true && $commentsCache->checkCacheValidy('data', $cacheKey) === true) {
            $commentsListPDO = $commentsCache->getCache('data', $cacheKey);
        }
        if (!$commentsListPDO) {
            $commentsListPDO = $this->getListOfPDO($news);
            if ($commentsCache->isActivated() == true && count($commentsListPDO) > 0) {
                $commentsCache->createCache($commentsListPDO, 'data', $cacheKey);
            }
        }
        return $commentsListPDO;
    }
    abstract public function getListOfPDO($news);
    /**
     * Méthode permettant de modifier un commentaire.
     * @param $comment Le commentaire à modifier
     * @return void
     */
    abstract protected function modify(Comment $comment);
    /**
     * Méthode permettant d'obtenir un commentaire spécifique.
     * @param $id L'identifiant du commentaire
     * @return Comment
     */
    abstract public function get($id);
}