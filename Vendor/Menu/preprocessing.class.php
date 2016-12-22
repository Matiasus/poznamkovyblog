<?php 

	namespace Vendor\Menu;

	/***
	 * @class Trieda pracujuca s vytvorenim bocneho menu podla obsahu databazy
	 */
	class Preprocessing {

		/** 
     * @var String - pohlad 
     */
		public $view;

		/** 
     * @var String - modul 
     */
		public $module;

		/** 
     * @var String - kontroller 
     */
		public $controller;

		/** 
     * @var String - kontroller 
     */
		public $parameters; 

		/**
		 * @var Objekt \Vendor\Route\Route - routovanie
		 */
		private $route;

		/** 
     * @var Array - kombinacie
     */
		public $combination;

		/**
		 * @var Object - \Vendor\Database\Database
		 */		
		private $database;  

		/**
		 * Konstruktor
		 *
		 * @param Void
		 * @return Void
		 */
		public function __construct (\Vendor\Route\Route $route, \Vendor\Database\Database $database)
		{
      $this->route = $route;
      $this->database = $database;
		}

		/**
		 * 
		 *
		 * @param Array
		 * @param String - privilegia [autorizacia]
     * @param String - delimeter, default = "-"
		 * @return Array | False
		 */
		public function buildArray ($array = array(), $privileges, $delimeter = "-")
		{
      if (!empty($array)) {
        // konverzia do pola
        $converted = \Application\Config\Settings::toArray($array);
        // uprava pola do vhodnej podoby pre vykreslenia pola
        $array_menu = \Application\Config\Settings::toMenuArray($converted, $this->database, $privileges, $delimeter);
        // ak je pole uspesne transformovane  
        if ($array_menu !== False) {
          return $array_menu;
        }
      }

      return False;
    }
  }
