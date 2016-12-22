<?php

	/***
	* INFOSERVIS Copyright (c) 2015 
	* 
	* Autor:		Mato Hrinko
	*	Datum:		12.7.2015
	*	Adresa:		http://matiasus.cekuj.net
	* 
	* ------------------------------------------------------------
	* @description: 		
	*
  * Autoloading tried - vytvorenie objektov (instancii) je vykonavane jednoduchym volanim triedy, 
  * nie je potrebne definovanie celej cesty umiestnenie triedy napr. $class = new Class(), namiesto
  * require(ADRESA ULOZENIA TRIEDY); $class = new Class();
  */
	class Autoloader{

    const EXTENSION_PHP = ".php";
    const EXTENSION_CLASS = ".class";

		private $_class = array();

		/***
		 * Konstruktor
		 *
		 * @param Void
		 * @return Void
		 */
		public function __construct()
		{
			spl_autoload_register(array($this, 'autoload'));
		}

		/***
		 * Autoloader - nacitanie volanych tried
		 *
		 * @param String - namespace volanej triedy
		 * @return Void
		 */
		public function autoload($class)
		{
			//Rozlozenie adresy v pripade volania namespace
			$class = explode('\\', $class);

			// posledny prvok pola malymi pismenami
			$phpfile = lcfirst(end($class));

			// Odstrani posledny prvok pola
			array_pop($class);
	
			$file = implode("/", $class);
			$file = $file . "/" . $phpfile;

      // overi existenciu suboru s priponou .class.php 
			if (file_exists($file . 
                      self::EXTENSION_CLASS . 
                      self::EXTENSION_PHP)) 
      {
        // subor s priponou .class.php
				require_once($file.self::EXTENSION_CLASS.self::EXTENSION_PHP);
      // overi existenciu suboru s priponou .php 
			} else if (file_exists($file . 
                             self::EXTENSION_PHP))
      {
        // subor s priponou .php
        require_once($file.self::EXTENSION_PHP);
      } else {
        // chyba
        throw new \Exception("Trieda <b>".$file."</b> neexistuje!!!");
      }
		}
	}



	
