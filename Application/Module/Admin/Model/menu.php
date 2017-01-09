<?php

	namespace Application\Module\Admin\Model;

	class menu {

		/** @var Objekt registru */
		private $registry;

    public $find = array();


		/**
		 * Konstruktor 
		 *
		 * @param Void
		 * @return Void
		 */
		public function __construct()
    {
		}

		/***
		 * Look for accordance
		 * 
		 * @param Void
		 * @return Void
		 */
		public function lookFor($url, $items = array())
		{
      // foreach items
      foreach ($items as $key => $item) {
        // check if is not array
        if (is_array($item)) {
          // check if accordance found
          if (!empty($accordance = $this->lookFor($url, $item))) {
            return $accordance;
          }
          // return accordance
        // not array
        } else {
          // compare url with item
          if (strcmp($url, $item) === 0 ||
              strcmp("/".$url, $item) === 0 ||
              strcmp($url."/", $item) === 0) 
          {
            // return key
            return array($key => $item);
          }
        }
      }
		}
	}

