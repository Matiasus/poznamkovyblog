<?php
	
	namespace Vendor\Config;

	class Parser {
    
    /** @var String - path to INI file */
    private $path = '';

    /** @var Object */
    private $file = null;
	
		/***
		 * Konstruktor
		 *
		 * @param String - path to INI file
		 * @return Void
		 */
		public function __construct($path)
		{
      // check if object \PDO is loaded
      if (strlen($path) > 0) {
        // load object \PDO
        // @param String - DSN
        $this->path = $path;
		  }
	  }

		/***
		 * Load INI file
		 *
		 * @param Void
		 * @return Void
		 */
		public function load()
		{
      return $this->file = parse_ini_file($this->path, true);
    }
	}



	
