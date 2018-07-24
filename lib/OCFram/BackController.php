<?php
namespace OCFram;

use Model\CacheFile;

abstract class BackController extends ApplicationComponent
{
  protected $action = '';
  protected $module = '';
  protected $page = null;
  protected $view = '';
  protected $managers = null;

  public function __construct(Application $app, $module, $action)
  {
    parent::__construct($app);

    $this->managers = new Managers('PDO', PDOFactory::getMysqlConnexion());
    $this->page = new Page($app);

    $this->setModule($module);
    $this->setAction($action);
    $this->setView($action);
  }

  public function execute()
  {
    $method = 'execute'.ucfirst($this->action);

    if (!is_callable([$this, $method]))
    {
      throw new \RuntimeException('L\'action "'.$this->action.'" n\'est pas définie sur ce module');
    }

    $this->$method($this->app->httpRequest());
  }

  public function page()
  {
    return $this->page;
  }

  public function setModule($module)
  {
    if (!is_string($module) || empty($module))
    {
      throw new \InvalidArgumentException('Le module doit être une chaine de caractères valide');
    }

    $this->module = $module;
  }

  public function setAction($action)
  {
    if (!is_string($action) || empty($action))
    {
      throw new \InvalidArgumentException('L\'action doit être une chaine de caractères valide');
    }

    $this->action = $action;
  }

  public function setView($view)
  {
    if (!is_string($view) || empty($view))
    {
      throw new \InvalidArgumentException('La vue doit être une chaine de caractères valide');
    }

    $this->view = $view;

    $viewCache = new CacheFile;

    if($viewCache->isActivated() == true && file_exists(__DIR__.'/../../tmp/cache/views/'.$this->app->name().'_'.$this->module.'_'.$this->view.'.txt'))
    {
        $this->page->setContentFile(__DIR__.'/../../tmp/cache/views/'.$this->app->name().'_'.$this->module.'_'.$this->view.'.txt');
    }
    else
    {
        $this->page->setContentFile(__DIR__.'/../../App/'.$this->app->name().'/Modules/'.$this->module.'/Views/'.$this->view.'.php');

        if($viewCache->isActivated() == true)
        {
            $viewCache->createViewCache('../App/'.$this->app->name().'/Modules/'.$this->module.'/Views/'.$this->view.'.php', $this->app->name(),$this->module,$this->view);
        }
    }
  }
}