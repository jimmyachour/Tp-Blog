<?php

namespace Entity;

use OCFram\Entity;

class Cache extends Entity
{
    protected $app;
    protected $module;
    protected $id;
    protected $action;
    protected $timeCache;

    protected $filename_data;
    protected $filename_view;

    protected $view;
    protected $data;


    public function dataFileExist()
    {
        return file_exists('../tmp/cache/data/'.$this->filename_data);
    }

    public function viewFileExist()
    {
        return file_exists('../tmp/cache/view/'.$this->filename_view);
    }

    // SETTER //

    public function setApp($app)
    {
        $this->app = $app;
    }

    public function setModule($module)
    {
        $this->module = $module;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setAction($action)
    {
        $this->action = $action;
    }

    public function setFilename_data($filename_data)
    {
        $this->filename_data = $filename_data;
    }

    public function setFilename_view($filename_view)
    {
        $this->filename_view = $filename_view;
    }

    public function setTimeCache($timeCache)
    {
        $this->timeCache = $timeCache;
    }

    public function setView($view)
    {
        $this->view = $view;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    // GETTER //

    public function app()
    {
        return $this->app;
    }

    public function module()
    {
        return $this->module;
    }

    public function id()
    {
        return $this->id;
    }

    public function action()
    {
        return $this->action;
    }

    public function filename_data()
    {
        return $this->filename_data;
    }

    public function filename_view()
    {
        return $this->filename_view;
    }

    public function timeCache()
    {
        return $this->timeCache;
    }

    public function view()
    {
        return $this->view;
    }

    public function data()
    {
        return $this->data;
    }


}