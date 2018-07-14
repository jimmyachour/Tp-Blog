<?php
namespace App\Frontend\Modules\News;

use Entity\Cache;
use \OCFram\BackController;
use OCFram\CacheCollector;
use OCFram\CacheController;
use \OCFram\HTTPRequest;
use \Entity\Comment;
use \FormBuilder\CommentFormBuilder;
use \OCFram\FormHandler;


class NewsController extends BackController
{
    use CacheCollector;

    public function executeIndex(HTTPRequest $request)
    {
        $nombreNews = $this->app->config()->get('nombre_news');
        $nombreCaracteres = $this->app->config()->get('nombre_caracteres');

        // On ajoute une définition pour le titre.
        $this->page->addVar('title', 'Liste des '.$nombreNews.' dernières news');

        // On récupère le manager des news.
        $manager = $this->managers->getManagerOf('News');

        $listeNews = $manager->getList(0, $nombreNews);

        foreach ($listeNews as $news)
        {
            if (strlen($news->contenu()) > $nombreCaracteres)
            {
                $debut = substr($news->contenu(), 0, $nombreCaracteres);
                $debut = substr($debut, 0, strrpos($debut, ' ')) . '...';

                $news->setContenu($debut);
            }
        }

        // On ajoute la variable $listeNews à la vue.
        $this->page->addVar('listeNews', $listeNews);
    }

    /**
     * @param HTTPRequest $request
     */
    public function executeShow(HTTPRequest $request)
    {
        $varCache = $this->startingCache($this->app->name(), $this->module, $request->getData('id'), $this->action);

        $cache = new Cache($varCache);
        $cacheController = new CacheController;

        if ($cache->dataFileExist() ==  false || $cache->viewFileExist() == false)
        {
            $news = $this->managers->getManagerOf('News', $this->app->name(), $request->getData('id'), $this->action)->getUnique($request->getData('id'));

            if (empty($news))
            {
                $this->app->httpResponse()->redirect404();
            }

            $this->page->addVar('title', $news->titre());
            $this->page->addVar('news', $news);
            $this->page->addVar('comments', $this->managers->getManagerOf('Comments')->getListOf($news->id()));

            $cache->setView($this->page->contentFile());
            $cache->setData($this->page->vars());
            $cacheController->executeSaveCache($cache);
        }
        $dataCache = $cacheController->executeGetDataCache($cache);

        foreach ($dataCache as $key => $value)
        {
            $this->page->addVar($key, $value);
        }

    }

    public function executeInsertComment(HTTPRequest $request)
    {
        // Si le formulaire a été envoyé.
        if ($request->method() == 'POST')
        {
            $comment = new Comment([
                'news' => $request->getData('news'),
                'auteur' => $request->postData('auteur'),
                'contenu' => $request->postData('contenu')
            ]);
        }
        else
        {
            $comment = new Comment;
        }

        $formBuilder = new CommentFormBuilder($comment);
        $formBuilder->build();

        $form = $formBuilder->form();

        $formHandler = new FormHandler($form, $this->managers->getManagerOf('Comments'), $request);

        if ($formHandler->process())
        {
            $this->app->user()->setFlash('Le commentaire a bien été ajouté, merci !');

            $this->app->httpResponse()->redirect('news-'.$request->getData('news').'.html');
        }

        $this->page->addVar('comment', $comment);
        $this->page->addVar('form', $form->createView());
        $this->page->addVar('title', 'Ajout d\'un commentaire');
    }
}