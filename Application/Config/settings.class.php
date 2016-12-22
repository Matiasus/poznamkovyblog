<?php
	
	namespace Application\Config;

	/***
	** Konfiguracne nastavenia pristupne v celej aplikacii
	**
	** Volanie globalnych nastaveni sa vykonova v tvare:
	** -------------------------------------------------------------------- 
	** \Config\Settings::$Detail->(sekcia inicializacneho suboru)->hodnota
	** --------------------------------------------------------------------
	**/
	class settings {
	
		const HOST = ":host=";
		const DATABASE = ";dbname=";

		/***
		 * @var Array - premena na rozklad konfiguracneho subora do pola 
		 */
		public static $Parser = array();

		/***
		 * @var Array - Typ databazy
		 */
		private static $types = array("mysql", 
													 				"sqlite");

		/***
		 * @var stdClass - pomocna premena pri konverzii pola do objektu 
		 */
		public static $Object;

		/***
		 * @var stdClass - Prekonvertovne pole do objektu s detailami konfiguracneho suboru
		 */
		public static $Detail;


		/***
		 * Nacitanie zakladnych nastaveni z konfiguracneho suboru
		 * konvertovaneho do objektu
		 *
		 * @parameter Void
		 * @return Void
		 */
		public static function Load($file)
		{
			/***
			 * Extrahovanie obsahu inicializacneho suboru do pola
			 */
			self::$Parser = parse_ini_file($file, true);

			self::$Object = new \stdClass();
			self::$Detail = new \stdClass();

			/***
			 * Konverzia z pola do objektu
			 */
			self::$Detail = self::Convert(self::$Parser);

			self::DSNConstant();
		}

		/***
		 * Konverzia z pola do objektu - rekurzivna funkcia
		 *
		 * @parameter Object (stdObject)
		 * @return Object (stdObject)
		 */
		public static function Convert($array) 
		{
			if (is_array($array))
			{
				return (object) array_map(array(get_called_class(), 'Convert'), $array);
			}
			else 
			{
			 return $array;
			}
		}

    /**
     * Konverzia pola, pola s objektom do pola
     *
     * @param Array || Object
     * @return stdClass - Object
     */
    public static function toArray($object_or_array)
    {
      $array = array();

      if (is_array($object_or_array) ||
          is_object($object_or_array)) 
      {
        foreach ($object_or_array as $key => $value) {
          if (is_array($value) ||
              is_object($value)) 
          {
            $array[$key] = self::toArray($value);
          } else {
            $array[$key] = $value;
          }
        }
        return $array;
      }

      return $array;
    }

    /**
     * Konverzia pola, pola s objektom do objektu
     *
     * @param Array || Object
     * @return stdClass - Object
     */
    public static function toObject($object_or_array)
    {
      $array = new \stdClass;

      if (is_array($object_or_array) ||
          is_object($object_or_array)) 
      {
        foreach ($object_or_array as $key => $value) {
          if (is_array($value) ||
              is_object($value)) 
          {
            $array->$key = self::toObject($value);
          } else {
            $array->$key = $value;
          }
        }
        return $array;
      }

      return $array;
    }

    /**
     * Pospajanie poli podla kluca
     *
     * Alternativa k funkcii
     *******************************************************************
     *  // inicializacne pole
     *  $merged = array();
     *  // argumenty funkcie
     *  $arguments = func_get_args();
     *
     *  // overenie ci argumentom je pole
     *  if (!empty($arguments) &&
     *      is_array($arguments)) 
     *  {
     *    // prechod cez jednotlive argumenty /ktore musia byt polom/
     *    foreach ($arguments as $key => $argument) {
     *      // overi, ci argument je polom
     *      if (is_array($argument)) {
     *        // spoki s inicializacnym polom
     *        $merged = array_merge_recursive($merged, $argument);
     *      }
     *    }
     *    // spojene pole
     *    return $merged;
     *  }
     *  return False;
     *******************************************************************
     *
     * @param Array || Object
     * @return stdClass - Object
     */
    public static function arrayMerge($array)
    {
      return call_user_func_array('array_merge_recursive', $array);
    }

    /**
     * Uprava pola exportovaneho z databazy do podoby pre menu
     *
     * @param Array
     * @param Object \Vendor\Database\Database 
		 * @param String - privilegia [autorizacia]
     * @param String - delimeter - ak posledna hodnota je zviazana napr. s Id
     * @return Array | False
     */
    public static function toMenuArray($array = array(), \Vendor\Database\Database $database, $privileges, $delimeter = false)
    {
      if (!empty($array) && 
          is_array($array))
      {
        $temp = array();
        $field = array();
        $merged = array();

        foreach ($array as $key => $value) {
          // ak je hodnota polom
          if (is_array($value)) {
            // tesuje, ci je nenulova/neprazdna hodnota 
            while (!empty($value)) {
              // vyberie posledny prvok pola a skrati pole o posledny prvok
              $item = array_pop($value);
              // testuje, ci sa jedna o prvu vybranu hodnotu
              if (empty($temp)) {
                if ($delimeter !== False) {
                  // cast pred poslednym vyskytom delimetra
                  $afore = substr($item, 0, strrpos($item, $delimeter));
                  // cast za poslednym vyskytom delimetra
                  $behind = substr($item, strrpos($item, $delimeter) + 1, strlen($item));
                  // zapis url adresy do prvej vybranej hodnoty
                  $field[$afore] = "/".$privileges."/".implode("/", array_map(array($database, "unAccentUrl"), $value))."/detail/".$database->unAccentUrl($afore)."/".$behind."/";
                } else {
                  // ak sa neziada rozkladat hodnotu podla delimetra
                  $field[$item] = "/".$privileges."/".implode("/", array_map(array($database, "unAccentUrl"), $value))."/detail/".$database->unAccentUrl($item)."/";
                }
              } else {
                // zapis dalsich casti pola
                $field[$item] = $temp;
              }
              // docasne uchovanie pola
              $temp = $field;
              // mazanie pomocneho pola
              unset($field);
              $field = array();      
            }
            // pospajanie poli
            $merged = array_merge_recursive($merged, $temp);
            // mazanie pomocneho pola
            unset($temp);
            $temp = array();
          }
        }

        return $merged;
      }

      return False;
    }

		/***
		 * Spracovanie DSN parametra pre PDO()
		 *
		 * @parameter Void
		 * @return Void
		 */
		public static function DSNConstant()
		{
			if (is_array(self::$Parser) && !empty(self::$Parser)) 
			{
				foreach (self::$Parser as $section => $content) 
				{
					foreach (self::$types as $type) 
					{
						if (strcmp(strtolower($section), $type) === 0) 
						{
							self::$Detail->{ucfirst(strtolower($section))}->DSNparameter  = $type . self::HOST . self::$Detail->{ucfirst(strtolower($section))}->Host;
						 	self::$Detail->{ucfirst(strtolower($section))}->DSNparameter .= self::DATABASE . self::$Detail->{ucfirst(strtolower($section))}->Database;
						}
					}
				}
			}
		} 


	}



	
