<?php

  namespace Vendor\Registry;

  class Registry {

    /** @var Premenna obsahujuca objekty */
    public $variables = array();

    /** @var Premenna obsahujuca objekty */
    private $objects = array();

    /***
    * Konstruktor
    *
    * @param Void
    * @return Void
    */
    public function __constuct() 
    { 
    }

    /**
    * Vytvorenie a ulozenie objektu z priecinku /registry (nazvy objektov su ulozene $objekt.class.php)
    *
    * @param String - kluc
    * @param String - menny priestor triedy
    * @return string kluc
    */
    public function __set($key, $class) 
    {
      if (!is_scalar($class)) {
        // vynimka objekt musi byt skalar
        throw new \Exception('Object must be given as a scalar!');	
      }
      if (!class_exists($class)) {
        // trieda musi existovat
        throw new \Exception("Class {$class} does not exist!");
      }
      // vytvorenie objektu
      $this->objects[$key] = new $class($this);
    }

    /**
    * Vratenie objektu podla kluca
    *
    * @parameter string kluc
    * @return Object
    */
    public function __get($key)
    {
      if (isset($this->objects[$key]))
      {
        return $this->objects[$key];
      }
    }

  }
