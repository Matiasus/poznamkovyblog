<?php

	namespace Vendor\Javascript;

	class Javascript {

    private $script;

		/***
		 * Konstruktor vytvorenia spojenia s registrom
		 *
		 * @param Void
		 * @return Void
		 */
		public function __construct() 
    {
		}

		/***
		 * Konstruktor vytvorenia spojenia s registrom
		 *
		 * @param Void
		 * @return String
		 */
		public function set($script) 
    {
      if (is_scalar($script)) {
        // zapis javascriptu
        $this->script = $script;
      }
		}

		/***
		 * CKEditor
		 *
		 * @param Void
		 * @return String - html, javascript code at the end of page (in front of </body>)
		 */
		public function script()
    {
			return " <script>\n ".$this->script." </script>\n";
		}

	}

