<?php

	/***
	* INFOSERVIS Copyright (c) 2015 
	* 
	* Autor:		Mato Hrinko
	*	Datum:		12.7.2015
	*	Adresa:		http://matiasus.cekuj.net
	* 
	* ------------------------------------------------------------
	* Inspiracia: 		
	*
	***/
	namespace Vendor\Errors;

	class Errors{

		/** @var Array - Uloziste dat	*/		
		private $data = array();

		/** @var Object \Vendor\Registry\Registry */
		private $registry;

    /***
     * Konstruktor vytvorenia spojenia s registrom
     *
     * @param Object \Vendor\Registry\Registry
     * @return Void
     */
		public function __construct(\Vendor\Registry\Registry $registry) 
		{
			$this->registry = $registry;
		}

		/***
		** Vratenie objektu podla kluca $key
		** @parameter string kluc
		** @return Object
		**/
		public function __get($key)
		{
			if (array_key_exists($key, $this->data)) {
				return $this->data[$key];
			}

			return FALSE;
		}

		/***
		 * Vratenie nastavenia objektu
     *
		 * @param string kluc
		 * @return Bool isset
		 */
		public function __isset($key)
		{
			return isset($this->data[$key]);
		}

		/***
		 * Vytvorenie a ulozenie objektu pod kluc $key
     *
		 * @param string objekt
		 * @return string kluc
		 */
		public function __set($key, $value)
		{
			$this->data[$key] = $value;
		}

		/***
		 * Konverzia do retazca
		 *
		 * @param Array
		 * @return String | Boolean
		 */
		public function toString($array = array())
		{
      $string = "";
      // overenie, ci pole je neprazdne
			if (!empty($array) &&
          is_array($array)) {
        // prechod cez prvky
        foreach ($array as $key => $value) {
          if (!is_array($value)) {
            // zapis hodnot do retazca
            $string .= "[".$key."]: ".$value."<br/>\n";
          } else {
            $string .= $this->toString($value)."<br/>";
          }
        }
        return $string;
      }

      return false;
		}

		/***
		 * Vratenie vsetkych chybovych hlaseni ako retazec
     *
		 * @param Void
		 * @return String vsetky chyby
		 */
		public function getWholeAsString( )
		{
			$allerrors = "";

			foreach ($this->data as $key => $error)
			{
				$allerrors .= " | " . ucfirst($key);
			}

			return $allerrors;
		}

	}
