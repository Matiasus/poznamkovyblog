<?php

	namespace Vendor\Database;

	/**
   * @class Database
	 */	
	class Database{

		const NOTEQUAL  = 0;
		const WITHEQUAL = 1;

		const MYSQL_NOW = "NOW()";

		/** @var Objekt \Vendor\Mysql\Mysql */
		private $mysql;

		/** @var Objekt \Vendor\Session\Session */
		private $session;

		/** @var Objekt Mysql - spojenie s databazou registry\mysql */
		private $connection;

    /** @var String */
    private $select_query = "SELECT ";

    /***
     * Konstruktor vytvorenia spojenia s registrom
     *
     * @param Object \Vendor\Mysql\Mysql
     * @return Void
     */
		public function __construct(\Vendor\Mysql\Mysql $mysql, 
                                \Vendor\Session\Session $session) 
    {
			$this->mysql = $mysql;
      $this->session = $session;
		}

		/**
		 * 
		 *
		 * @param String
		 * @return Array Or False	
		 */
		public function lastInsertId()
		{
      return $this->mysql->lastInsertId();
    }

		/**
		 * Poziadavka na mysql
		 *
		 * @param String
		 * @return Array Or False	
		 */
		public function query($query)
		{
			if (strlen($query) != 0) {
				// vykonanie pozaidavky na mysql
				$qrespond = $this->mysql->
													 executeQuery($query);
        // ziskanie obsahu
				$content = $this->mysql->
													getRows();
				return $content;
			}
			// vypis flash hlasky
			$this->session->set("flash", "Zle zadaná požiadavka!!!", false);

			// navrat prazdne pole
			return False;
		}

		/***
		 * Vkladanie udajov do databazy
		 *
		 * @param Array pole dat, ktore maju byt ulozene do databazy
		 * @param String - tabulka do ktore sa maju vlozit data
		 * @param Boolean - predpriprava udajov - osetrenie pred sql injection
		 * @return Boolean
		 */
		public function insert($data = array(), $table, $prepare = false)
    {
			$binds = "";
			$names = "(";
			$values = "";

			if (!empty($data)) {

				foreach ($data as $key => $value) {

					$binds .= ":" . $key . ", ";
					$names .= $key . ", ";

					// Osetrenie pripadu ak je ukladany terajsi datum a cas funkciou NOW()
					if (strrpos($value, self::MYSQL_NOW) === FALSE) {
						// Ulozenie hodnot s uvodzovkami
						$values .= "'" . $value . "', ";
					} else {
						// Ulozenie caz funkciu sql bez uvodzoviek
						$values .= "" . $value . ", ";
					}
				}
			}

			/**
			 * Orezanie poslednych dvoch znakov - ciarky a prazdnu medzeru 
			 * retazca do MySql syntaxu
			 */
			$names = substr($names, 0, strlen($names) - 2) . ")";
			$binds = substr($binds, 0, strlen($binds) - 2);
			//$values = substr( $values, 0, strlen( $values ) - 2);

			/**
			 * Sql prikaz na vlozenie udajov do databazy 
			 */
			$sqlquery = "INSERT INTO $table $names VALUES ($binds);";
			$this->mysql->executeQuery($sqlquery, $data, $prepare);

			return TRUE;
		}

		/***
		 * Selektovanie udajov z tabulky podla hodnot a podmienky
		 *
		 * @param String
		 * @return Bool
		 */
		public function select($query = false)
    {
      // spojovnik
      $join = ", ";
      // vychodzi string
      $select = $this->select_query;

      if (empty($query) ||
          !is_array($query))
      {
        // zaznam chyby
        throw new \Exception(get_class($this).'-'.__FUNCTION__.'-'.__LINE__, 'Parameter ma byt neprazdne pole!');
      } else {
        // prechod cez prvky pola
        foreach ($query as $key => $value) {
          // overi, ci je scalar
          if (!is_scalar($value)) {
            // zaznam chyby
            throw new \Exception(get_class($this).'-'.__FUNCTION__.'-'.__LINE__, 'Hodnota musi byt skalar!');
          } else {
            // zapis hodnoty
            $select .= $value.$join;
          }
        }
        // orezanie poslednych hodnot
        $select = substr($select, 0, strlen($select) - strlen($join));
      }
      // uspesny navrat
      return new \Vendor\Database\Db_select_from($this->mysql, $select);
		}

		/**
		 * Update udajov z tabulky podla hodnot a podmienky
		 *
		 * @param Array - hodnoty
		 * @param Array - podmienka
		 * @param String - tabulka
		 * @return Bool
		 */
		public function update($values = array(), $conditions = array(), $table)
		{
			$value = $this->process($values, self::WITHEQUAL);
			$condition = $this->process($conditions, self::WITHEQUAL, " AND ");

			/**
			** Sql prikaz na update udajov do databazy 
			*/
			$sqlquery = "UPDATE {$table} SET $value WHERE $condition;";

			$this->mysql->executeQuery($sqlquery);

			return TRUE;
		}

		/**
		 * Vymazavanie udajov z databazy
		 *
		 * @param Array - hodnoty
		 * @param Array - podmienka
		 * @param String - tabulka
		 * @return Bool
		 */
		public function delete($conditions = array(), $table)
		{
			// spracovanie podmienky vymazania zaznamu
			$condition = $this->process($conditions, self::WITHEQUAL, " AND ");
			// Sql prikaz na vymazanie udajov z databazy 
			$sqlquery = "DELETE FROM {$table} WHERE ".$condition.";";
			// vykonanie dotazu
			$this->mysql->executeQuery($sqlquery);

			return TRUE;
		}

		/***
		 * Uprava retazca vhodneho do url adresy
		 *
		 * @param String - retazec, ktory ma byt konvertovany
		 * @return String - konvertovany / upraveny retazec
		 */
    public function unAccentUrl($string, $delimeter = '-')
    {
      // Trim empty characters
      $string = trim($string);

      /***
       * Á: &Aacute;
       * À: &Agrave;
       * Â: &Acirc;
       * Ã: &Atilde;
       * Ä: &Auml;
       * Å: &Aring;
       * Æ: &AElig;
       * Ç: &Ccedil;
       * Ø: &Oslash;
       * Č: &Ccaron;
       * Ĳ: &IJlig;
       * Ŀ: &Lmidot;
       * Ī: &Imacr;
       */  
      $utf8_name  = "acute|";
      $utf8_name .= "grave|";
      $utf8_name .= "cedil|";
      $utf8_name .= "slash|";
      $utf8_name .= "caron|";
      $utf8_name .= "tilde|";
      $utf8_name .= "midot|";
      $utf8_name .= "circ|";
      $utf8_name .= "macr|";
      $utf8_name .= "ring|";
      $utf8_name .= "uml|";
      $utf8_name .= "lig|";
      $utf8_name .= "orn|";
      $utf8_name .= "th";

      // Convert string to htm entities 
      $string = htmlentities($string, ENT_HTML5 | ENT_QUOTES, 'UTF-8');

      // Find html entites defined by &[a-zA-Z]$utf8_name;
      // example see above $utf8_name
      $pattern = "/&([a-z]{1,2})(?:".$utf8_name.");/i";

      // Replace html entities by given char
      // note: array of replacement chars is placed in first item of found array
      $converted = strtolower(preg_replace($pattern, $replacement = '$1', $string));

      // Replaced the rest untranslited characters
      $replaced = preg_replace($pattern = "/[^a-z0-9]/", $replacement = $delimeter, $converted);

      $clean_url = $replaced;

      // Replaced multiple '-' characters
      if ($delimeter !== '') {
        $clean_url = preg_replace($pattern = "/[".$delimeter."]+/", $replacement = $delimeter, $replaced);
      }

      return $clean_url;
    }

		/***
		 * Odstranenie html tagov
		 *
		 * @param String - retazec, ktory ma byt konvertovany
		 * @return String - konvertovany / upraveny retazec
		 */
    public function stripHtmlTags($string)
    {
      $strip_ent = preg_replace('#&.{1,20};#i', '', $string);
      $strip_tag = preg_replace('#<[^>]+>#i', '', $strip_ent);

      return $strip_tag;
    }

		/***
		 * Spracovanie pola do retazca
		 *
		 * @param Array - spracovavane pole
		 * @return String - spracovane pole do ratazca
		 */
		private function process($data = array(), $by, $join_delimiter = False)
		{
			/**
			** Inicializacia navratovej hodnoty
			*/			
			$hodnota = "";
			($join_delimiter === False) ? $junction = ", " : $junction = $join_delimiter;

			switch ($by)
			{	
				case self::NOTEQUAL:

					if (!empty($data)) {
						foreach ($data as $key => $value)	{
							$hodnota .= $value.$junction;
						}
					}

					/**
					 * Orezanie poslednych dvoch znakov - ciakry a prazdnu medzeru 
					 * retazca do MySql syntaxu
					 */
					$hodnota = substr($hodnota, 0, strlen( $hodnota) - strlen($junction));

					return $hodnota;
	
				case self::WITHEQUAL:

					if (!empty($data)) {
            // prechod cez jednotlive prvky
						foreach ($data as $key => $value) {
							// Osetrenie pripadu ak je ukladany terajsi datum a cas funkciou NOW()
							if (strrpos($value, self::MYSQL_NOW) === FALSE)	{
								// Ulozenie hodnot s uvodzovkami
								$hodnota .= $key."='".addslashes($value)."'".$junction;
							}	else {
								// Ulozenie caz funkciu sql bez uvodzoviek
								$hodnota .= $key."=".$value.$junction;
							}
						}
					}	else {
						return False;
					}
					/**
					 * Orezanie poslednych dvoch znakov - ciakry a prazdnu medzeru 
					 * retazca do MySql syntaxu
					 */
					$hodnota = substr($hodnota, 0, strlen($hodnota) - strlen($junction));

					return $hodnota;

			}		
		}

		/***
		 * Spracovanie url adresy do retazca
		 *
		 * @param Array - spracovavane pole
		 * @return String - spracovane pole do ratazca
		 */
    public function dateToDatabase($url)
    {
      $length = \Application\Config\Settings::$Detail->Framework->Length_date;
      return substr($url, 0, $length).
										" ".
										strtr(substr($url, $length + 1), $to = "-", $from = ":");
    }
	}

