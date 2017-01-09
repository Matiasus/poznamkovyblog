<?php

	namespace Vendor\Container;

	class Container {

    private $objects;

    /***
     * Konstruktor
     *
     * @param Void
     * @return Void
     */
    public function __construct() 
    {
      if (!empty($this->objects)){
        $this->objects = new stdClass;
      }
    }

		/***
		 * Volanie
		 *
		 * @param String - key
		 * @return String | Array
		 */
		public function __get($object)
		{
			if (!empty($this->objects) && 
          isset($this->objects->{$object}))
			{
				return $this->objects->{$object};
			}
      // neuspech
      return false;
		}

		/***
		 * Nastavenie
		 *
		 * @param String, String - key, Value
		 * @return Void
		 */
		public function set($key, $object)
		{
			if (isset($this->objects->{$key})){
        // volanie
				$this->objects->{$key} = new $object();
			}
      // neuspech
      return false;
		}
  }
