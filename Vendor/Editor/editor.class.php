<?php

	namespace Vendor\Editor;

	class Editor {

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
		 * CKEditor
		 *
		 * @param Void
		 * @return String - html, javascript code at the end of page (in front of </body>)
		 */
		public function ckeditor()
    {
			$ckeditor  = " <script>\n";
			$ckeditor .= "   ckeditorInit('id-editor');\n";
			$ckeditor .= " </script>\n";

      return $ckeditor;
		}

	}

