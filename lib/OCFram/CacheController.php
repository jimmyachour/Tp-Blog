<?php

namespace OCFram;

class CacheController extends Entity
{
    protected $cacheContentFile;
    protected $cacheVars = [];

    public function addCacheVar($var, $value)
    {
        if (!is_string($var) || is_numeric($var) || empty($var))
        {
            throw new \InvalidArgumentException('Le nom de la variable doit être une chaine de caractères non nulle');
        }

        $this->cacheVars[$var] = $value;
    }

    public function createDataCache($filename)
    {
        $cacheVars = serialize($this->cacheVars);

        $file = fopen('../tmp/cache/datas/'.$filename,'w+');

        fwrite($file,  $cacheVars);

        fclose($file);

    }

    /*  public function createViewsCache($var_dir, $filename_view)
    {
        // chemin de la vue à mettre en cash
        $view_dir = '../App/'.$var_dir['app'].'/Modules/'.$var_dir['module'].'/Views/'.$var_dir['action'].'.php';

        $file_to_cache = file_get_contents($view_dir);

        $file_to_cache = serialize($file_to_cache); // Transforme $cache en chaine de caractères.

        // ouverture ou creation du fichier cache correspondant à la vue
        $file_view = fopen('../tmp/cache/views/'.$filename_view.'.txt','w+');


        fwrite($file_view, $file_to_cache);

        fclose($file_view);
    }
    */
    public function getCache($filename_data,$filename_view, $request)
    {
        $data_cache = file_get_contents('../tmp/cache/datas/'.$filename_data, "r"); // Récupération data

        $data_cache = unserialize($data_cache);

        $view_dir = '../App/Frontend/Modules/News/Views/show.php';

        $page = new Page($request);

        foreach ($data_cache as $key => $value)
        {

            $page->addVar($key, $value);

        }

        $page->setContentFile($view_dir);
        $page->getGeneratedPage();


    }

    public function deleteCache()
    {

    }

    public function modifyCache()
    {

    }

    public function cacheExist($request)
    {

    }

    public function contentFile()
    {
        return $this->cacheContentFile;
    }

    public function setCacheContentFile($cacheContentFile)
    {

        $this->cacheContentFile = $cacheContentFile;
    }
}