<?php

	Namespace Vendor\Javascript;

	class Javascript {

    private $functions = array();

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
		 * Konstruktor vytvorenia spojenia s registrom
		 *
		 * @param Void
		 * @return String
		 */
		public function setFunction($key) 
    {
      if (is_scalar($key)) {
        // zapis javascript parametrov
        return $this->functions[$key] = new \Vendor\Javascript\Parameters();
      }

		}

		/***
		 * CKEditor
		 *
		 * @param Void
		 * @return Object | Array of Objects
		 */
		public function getFunction($key = false)
    {
      if ($key) {
        return $this->functions[$key];
      } else {
        return $this->functions;
      }
		}

	}

