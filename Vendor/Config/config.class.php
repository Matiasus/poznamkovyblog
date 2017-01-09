<?php
	
	namespace Vendor\Config;

	class Config {
	
		const HOSTNAME = ":host=";
		const DATABASE = ";dbname=";

    /** @var Object \Vendor\Config\Parser */
    private $parser = null;

		/***
		 * Konstruktor
		 *
		 * @param String - path to INI file
		 * @return Void
		 */
		public function __construct($path)
		{
       // check if is non empty String
      if (!file_exists($path) ||
          !is_readable($path))
      {
        // throw to exception
        throw new \Exception("File <strong>'".$path."()'</strong> is not exists or is not readable!");
      }
      // check if is non empty String
      if (!is_string($path) ||
          !(strlen($path) > 0))
      {
        // throw to exception
        throw new \Exception("Method <strong>'".__METHOD__."()'</strong> in class <strong>'".__CLASS__."'</strong> must be String with <strong>string length ></strong> 0!");
      }

      // $parameter is not a string || strlen  = 0
      // @param String path to file
      // @return Object \Vendor\Config\Parser
      $this->parser = new \Vendor\Config\Parser($path);
    }

		/***
		 * Parser
		 *
		 * @param Void
		 * @return Void
		 */
		public function getParser()
		{
      // check if parser is loaded
      if ($this->parser === null) {
        // parser not loaded - throw to exception
        throw new \Exception("Method <strong>'".__METHOD__."()'</strong> in class <strong>'".__CLASS__."'</strong> must be not NULL!");
      }
      // return @var Object \Vendor\Config\Parser
      return $this->parser->load();
    }
	}



	
