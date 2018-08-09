<?php

namespace OCFram;

use Entity\Cache;

class Page extends ApplicationComponent
{
    protected $contentFile;
    protected $vars = [];
    protected $contentCache;

    public function addVar($var, $value)
    {
        if (!is_string($var) || is_numeric($var) || empty($var)) {
            throw new \InvalidArgumentException('Le nom de la variable doit être une chaine de caractères non nulle');
        }
        $this->vars[$var] = $value;
    }


    public function getGeneratedPage()
    {
        if (!file_exists($this->contentFile()) && $this->contentCache == null) {
            throw new \RuntimeException('La vue spécifiée n\'existe pas');
        }

        $user = $this->app->user();

        extract($this->vars);

        ob_start();

        if($this->contentCache != null)
        {
            echo $this->contentCache;
        }
        else
        {
            require $this->contentFile;

        }

        $content = ob_get_clean();

        ob_start();
        require __DIR__ . '/../../App/' . $this->app->name() . '/Templates/layout.php';
        return ob_get_clean();
    }

    public function contentFile()
    {

        return $this->contentFile;
    }

    public function setContentFile($contentFile)
    {

        if (!is_string($contentFile) || empty($contentFile)) {
            throw new \InvalidArgumentException('La vue spécifiée est invalide');
        }
        $this->contentFile = $contentFile;
    }

    public function vars()
    {
        return $this->vars;
    }

    public function setContentCache($cacheView)
    {
        $this->contentCache = $cacheView;
    }
}