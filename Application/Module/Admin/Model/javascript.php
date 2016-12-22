<?php

	namespace Application\Module\Admin\Model;

	class javascript {

		/**
		 * @var Objekt registru
		 */
		private $registry;

		/**
		 * @var Objekt javascriptu
		 */
    private $javascript;

		/**
		 * Konstruktor 
		 *
		 * @param void
		 * @return void
		 */
		public function __construct(\Vendor\Registry\Registry $registry)
    {
      // register
			$this->registry = $registry;
      // javascript 
      $this->javascript = $this->registry->javascript;
		}

		/***
		 * CKeditor
		 * 
		 * @param Void
		 * @return Void
		 */
		public function ckeditor()
		{
      // call ckeditor
      $this->registry->javascript
                     ->setFunction("ckeditorInit")
                     ->setParameters(array("editor-id"=>"id-editor"));
		}
	}

