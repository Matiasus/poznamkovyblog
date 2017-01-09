<?php

	namespace Vendor\Log;

	class Log {

		private $data;

		/**
		 * Konstruktor databázového objektu
		 *
		 * @params \Vendor\Registry\Registry - register
		 * @return Void
		 */
		public function __construct()
		{
			return $this->data = new \Vendor\Log\a;
		}

    public function get()
    {
      return $this->data;
    }

	}

