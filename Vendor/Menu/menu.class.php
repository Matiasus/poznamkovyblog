<?php 

	namespace Vendor\Menu;

	/*
	** Trieda pracujuca s vytvorenim bocneho menu
	** podla obsahu databazy
	*/
	class Menu{

    const RESET = "\n<ul>";
		const PATTERN = "/\"{[0-9:]+}\"/i";

		/**
		 * @var Int - inkrementor
		 */		
		private $i = -1;

		/**
	   * @var String - inicializacna hodnota
		 */		
		private $code = self::RESET;

		/**
		 * @var Object - \Vendor\Database\Database
		 */		
		private $database;

		/**
		 * @var Object - \Vendor\Menu\Style
		 */		
		private $preprocessing;

		/**
		 * Konstruktor
		 *
		 * @param Void
		 * @return Void
		 */
		public function __construct (\Vendor\Route\Route $route, \Vendor\Database\Database $database)
		{
      $this->database = $database;
      $this->preprocessing = new \Vendor\Menu\Preprocessing($route, $database);
		}

		/**
		 * Style - vertical, horizontal
		 *
		 * @param Void
		 * @return Object \Vendor\Menu\Style | False
		 */
		public function preprocessing ()
		{
      if (!empty($this->preprocessing)) {
        return $this->preprocessing;
      }
  
      return False;
		}

		/***
		 * Vytvorenie vertikalneho menu s obsahom podla databazy
		 *
		 * @param $Array Spracovane data funkciou process
		 * @return $String code kod menu
		 */
		public function build ($data = array(), $keyLink = false)
		{
			foreach ($data as $key => $value) {
				// Overenie pola
				if (is_array($value)) {
					// zaciatok zakladania novej podurovne <ul>
					$this->code .= ((!$keyLink) ? ("\n <li><strong>".$key."</strong>\n  <ul>") : ("\n <li><a href=\"".$value."\">".$key."</a>\n  <ul>"));
					// rekurzia 
					$this->build($value);
					// uzavretie podurovne
					$this->code .= "\n  </ul>\n </li>";
				} else {
					// jednotlive polozky vsetkych urovni
					$this->code .= "\n   <li><a href=\"".$value."\">".$key."</a></li>";
				}
			}

			return $html = $this->code."\n</ul>\n";
		}

		/**
		 * Vychodzi obsah pre menu
		 *
		 * @param String
		 * @return Void
		 */
		public function setCode ($code)
		{
      $this->code = $code;
    }

		/***
		 * Nastavenie Id
		 *
		 * @param String - Id
		 * @return Void
		 */
		public function setId ($id)
		{
			if (strlen($this->code) != 0) {
				$this->code = "\n<ul id=\"".$id."\">";
			}
		}
  }

