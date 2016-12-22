<?php

	namespace Application\Module\Admin\Model;

	class articles{

		const TABLE_ARTICLES = "Articles";

		/***
		** Objekt \Vendor\Registry
		*/
		private $registry;

		/***
		** Objekt \Vendor\Database\Database
		*/
		private $database;

		/***
		** @parameter \Vendor\Registry - Konstruktor vytvorenia spojenia s registrom, 
		** @parameter String - typ pristupu {online, Offline}
		** @return Void
		*/
		public function __construct(\Vendor\Registry\Registry $registry)
		{
			/* Prepojenie na register */
			$this->registry = $registry;

			/***
			** @var stdObject User - prihlaseny uzivatel
			*/
			$this->user = (object) $this->registry->user->getLoggedUser();

			/***
			** @var Object Databazy
			*/
			$this->database =  new \Vendor\Database\Database($this->registry);
		}

		/***
		** Spracovanie poziadavky clanku
		** 
		** @parameter Array data z formulara
		** @Return Void
		*/
		public function process($data)
		{
			
			/* Uprava url adresy */
			$data['Url'] = $this->database->seoUrlAddress($data['Title']);

			/***
			** @var Array databazy
			** @var String nazov tabulky
			** @var Bool Prepare - s predpripravenim (sql injection)
			*/
			$this->database->insert($data, self::TABLE_ARTICLES, true);
			
			/* Flash oznam pre uspesne ulozenie do databazy */
			$this->registry->session->set("flash", "Dáta úspešne vložené do databázy!");

			/* Presmerovanie na domovsku stranku */
			$this->registry->route->redirect($this->user->Privileges. "/articles/default");
		}
	}

