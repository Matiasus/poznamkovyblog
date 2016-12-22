<?php

	/***
	* INFOSERVIS Copyright (c) 2016 
	* 
	* Autor:		Mato Hrinko
	*	Datum:		16.6.2016
	*	Adresa:		http://matiasus.cekuj.net
	* 
	* ------------------------------------------------------------
	* Inspiracia: 		
	*
	***/
	namespace Vendor\Errors;

	class Template{

    /** @var String - telo */
		public $body = "";

    /** @var String - titul */
		public $title = "";
	
		/***
		 * Konstruktor
		 *
		 * @param Void
		 * @return Void
		 */
		public function __construct()
		{
		}

		/***
		 * Nastavenie potrebnych premennych
		 *
		 * @param String, String
		 * @return Void
		 */
		public function set($param, $value)
		{
			$this->{$param} = $value;
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
		 * Vykreslenie stranky
		 *
		 * @param Void
		 * @return Void
		 */
		public function render()
		{
			echo "<html>\n";
			echo "  <head>\n";
			echo "  <title>". $this->title ."</title>\n";
      echo " <link rel=\"icon\" href=\"images/favicon.ico\" />\n";
      echo " <link rel=\"stylesheet\" style=\"text/css\" href=\"/Public/css/error.css\" />\n";
			echo "  </head>\n";
			echo "  <body>\n";
			echo "    <div class=\"main\">\n";
			echo "      <div class=\"content\">\n";
      echo "       <h1>ERROR</h1>";
			echo "       ".$this->body . "\n";
			echo "      </div>\n";
			echo "    </div>\n";
			echo "  </body>\n";
			echo "</html>";
		}
	}
