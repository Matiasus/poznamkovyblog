<?php 

	namespace Vendor\Http;

	/**
	** Trieda pracujuca s uzivatelom
	*/
	class Http{

		/***
		** @var Objekt \Vendor\Registry
		*/
		protected $registry;

		/***
		** Konstruktor na Register
		**
		** @par \Vendor\Registry - Konstruktor vytvorenia spojenia s registrom
		** @ret Void
		*/
		public function __construct(\Vendor\Registry\Registry $registry)
		{
			/* Prepojenie na register */
			$this->registry = $registry;
		}

		/***
		** Inicializacia cURL
		**
		** @par Void
		** @ret Void
		*/
		public function initCurl()
		{
			curl_init();
		}

		/***
		** Ukoncenie relacie s cURL
		**
		** @par Void
		** @ret Void
		*/
		public function exitCurl()
		{
			curl_close();
		}

	}
