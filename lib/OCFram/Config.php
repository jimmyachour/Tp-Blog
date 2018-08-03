<?php
namespace OCFram;

class Config extends ApplicationComponent
{
  protected $vars = [];
  protected $confDir;

    public function ConfDir()
    {
      return $this->confDir = __DIR__.'/../../App/'.$this->app->name().'/Config/app.xml';
    }

  public function get($var)
  {

    if (!$this->vars)
    {
      $xml = new \DOMDocument;
      $xml->load($this->confDir());

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
     * @param $name
     * @param $value
     */
  public function set($name, $value)
  {
      $xml = new \DOMDocument;

      $xml->load($this->confDir());

      $elements = $xml->getElementsByTagName('define');

      foreach($elements as $element)
      {
          if($element->getAttribute('var') == $name)
          {
              $element->setAttribute('value',$value);
          }
      }

      $xml->save($this->confDir);

  }

}