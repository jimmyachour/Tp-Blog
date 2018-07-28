<?php
namespace OCFram;

class Config extends ApplicationComponent
{
  protected $vars = [];

  public function get($var)
  {
    if (!$this->vars)
    {
      $xml = new \DOMDocument;
      $xml->load(__DIR__.'/../../App/'.$this->app->name().'/Config/app.xml');

      $elements = $xml->getElementsByTagName('define');

      foreach ($elements as $element)
      {
        $this->vars[$element->getAttribute('var')] = $element->getAttribute('value');
      }
    }

    if (isset($this->vars[$var]))
    {
      return $this->vars[$var];
    }

    return null;
  }

    /**
     * Méthode permettant de modifier la valeur d'un parametre.
     * @param $param
     * @param $value
     */
// revoir le nom set($name, $value) par exemple plus parlant
  public function changeConfig($param, $value)
  {
      // A refacto avec la methode get, comportementcomme singleton
      // si déja chargé on utilise le tableau
      // avoir une variable pour ne pas répéter le nom de fichier (si on change
      // le fichier on doit el changer à un seul endroit
      $xml = new \DOMDocument;
      $xml->load(__DIR__.'/../../App/'.$this->app->name().'/Config/app.xml');

      $elements = $xml->getElementsByTagName('define');

      // surement une méthode plus rapide que de parcourir tous les éléments ;-)
      foreach($elements as $element)
      {
          if($element->getAttribute('var') == $param)
          {
              $element->setAttribute('value',$value);
          }

      }
      $xml->save(__DIR__.'/../../App/'.$this->app->name().'/Config/app.xml');
  }
}