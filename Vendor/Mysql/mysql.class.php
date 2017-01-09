<?php

	namespace Vendor\Mysql;

	/*
	** Trieda pracujuca s databazou
	*/
	class Mysql {

		/***
		** Objekt registru
		*/
		private $registry;

		/***
		** Docasne ulozenie dat vykonanych z poziadaviek
		*/
		private $data = array();

		/*
		** Docasne ulozenie poziadaviek
		*/
		private $queryCache = array();

		/***
		** Posledna ziadost
		*/
		private $last;

		/***
		** Pripojenia k databazam 
		*/
		private $connections = array();

		/***
		** Cislo aktivneho pripojenia, defaultne je nastavena 0 
		*/
		private $activeConnection = 0;

		/***
		** Zvolenie aktivnej tabulky
		*/
		private $table;

		/** @var String - nazov pripojenia */
		private $name;

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
		 * Pripojenie k databaze pomocou PDO
     *
		 * @param String - $dsn - mysql: host=localhost; dbname=phpbook
		 * @param String - $user - meno uzivatela databazy
		 * @param String - $password - heslo k databaze
     * @param Array - pole parametrov
     * @return \PDO
		 */		
		public function connect($name, $dsn, $user, $password, $options = array())
		{
      // nazov noveho pripojenia
      $this->name = $name;

      // ak existuje nazov pripojenia
      if ($this->existsName() === true) {
        // zaznam chyby
        throw new \Exception(get_class($this).'-'.__FUNCTION__.'-'.__LINE__, 'Nazov pripojenia <b>'.$this->name.'</b> existuje! Zadajne, prosim iny nazov!');
      }
      // pripojenie na databazu prostrednictvom PDO
			try {
        // pripojenie na databazu
				$this->connections[$this->name] = new \PDO($dsn  
                                                  ,$user
                                                  ,$password
                                                  ,$options
                                                  );
        // nastavenie kodovania na utf8
				$this->connections[$this->name]->exec("set names utf8");
        // aktivne pripojenie = posledne pripojenie
        $this->active = $this->name;
			}
      // Vynimka ak je nespravne zadana poziadavka
			catch(\PDOException $exception) {
        // zaznam chyby
        throw new \Exception('[CLASS:] '.get_class($this).' [FUN:] '.__FUNCTION__.' [LINE:] '.__LINE__.' <br>Error in SQL syntax or query! <b>Error message: </b>'.$exception->getMessage());
			}
      // return \PDO Object
			return $this->connections[$this->name];
		}

		/***
		 * Overenie existencie nazvu pripojenia
		 *
		 * @param Void
		 * @return Boolean
		 */
		private function existsName()
		{
      // zadane meno pripojenie uz existuje?
			if (array_key_exists($this->name
                          ,$this->connections)) {
        // ak ano
        return true;
      }
      // ak nie
      return false;
		}

		/***
		 * Aktivne spojenie s databazou
     *
		 * @param Void
		 * @return Object
		 */
		public function getActiveConnection()
		{
			return $this->activeConnection;
		}

		/***
		 * Id posledneho vlozeneho zaznamu
     *
		 * @param Void
		 * @return Object
		 */
		public function lastInsertId()
		{
			return $this->connections[$this->active]->lastInsertId();
		}

		/***
		 * Nastavnie aktivnej tabulky
     *
		 * @param String nazov tabulky
		 * @return Void
		 */
		public function setTable($table){

			$this->table = $table;
		}

		/***
		 * Nazov tabulky
     *
		 * @param Void
		 * @return String nazov tabulky
		 */
		public function getTable()
		{
			return $this->table;
		}

		/***
		 * Spracovanie poziadavky
     *
		 * @param String ziadost na vykonanie
		 * 	poznamka - pri prepare metode dochadza aj k osetreni (sanitizacii) dat, preto samotna metoda sanitize v triede 
		 *						 nie je zadefinovana
		 * @param Array - pole dat
		 * @param Boolean - bindParam
		 * @return Boolean
		 */
		public function executeQuery($query, $data = array(), $prepare = false){

			try
			{
				if ($prepare === true) {
          // predpriprava parametrov
					$this->last = $this->connections[$this->active]->prepare($query);
          // doplnenie parametrov
					foreach ($data as $key => $value)	{
						$this->last->bindParam(":".$key, $value, \PDO::PARAM_STR);
					}
          // vykonanie
					$this->last->execute($data);
				}	else {
          // predpriprava
					$this->last = $this->connections[$this->active]->prepare($query);
          // vykonanie
					$this->last->execute($data);
				}
	
				return TRUE;
			}
			catch(\PDOException $exception) 
			{
				// Vypis hlavnej chybovej spravy	
				throw new \Exception('[CLASS:] '.get_class($this).' [FUN:] '.__FUNCTION__.' [LINE:] '.__LINE__.' ERROR with executing \'' . $query . '\' :<br/>' . $exception->getMessage());
			}
		}

		/***
		 * Metoda, ktora zistuje, ci je v tabulke prislusny sltpec
     *
		 * @param String - nazov stlpca
		 * @return Boolean
		 */
		public function existenceOfColumn($column){

			// MySQL Syntax na zistenie pritomnosti stlpca
			$query = "SHOW COLUMNS FROM " . $this->registry->mysql->getTable() . "
								LIKE '" . ucfirst($column) . "'; ";	
			
			if ($this->executeQuery($query, array()) === TRUE) {
				if (count($this->getRows()) > 0) {
					return TRUE;
				}
			}

			return FALSE;
		}

    /***
     * Nacitanie obsahu
     *
     * @param Void
     * @return Object
     */
		public function getRows()
		{
			return $this->last->fetchAll(\PDO::FETCH_OBJ);
		}

    /***
     * Destruktor - ukoncenie spojenia
     *
     * @param Void
     * @return Void
     */
    public function __deconstruct() 
		{
      // ukoncenie vsetkych spojeni
    	foreach($this->connections as $connection)	
			{
    		$connection = null;
    	}
    }

	}

?>
