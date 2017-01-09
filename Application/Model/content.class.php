<?php

	namespace Application\Model;

	class Content {

		const TABLE_ARTICLES = "Articles";

		/**
		 * Objekt uzivatela
		 */
		private $user;

		/**
		 * @var Objekt registru
		 */
		private $registry;

		/**
		 * @var Objekt Mysql - spojenie s databazou registry\mysql
		 */
		private $database;

		/***
		 * Konstruktor vytvorenia spojenia s registrom
		 *
		 * @param \Vendor\Registry\Registry
		 * @return Void
		 */
		public function __construct(\Vendor\Registry\Registry $registry) 
		{
			/* Prepojenie na register */
			$this->registry = $registry;
			/* @var stdObject User - prihlaseny uzivatel */
			$this->user = (object) $this->registry->user->getLoggedUser();
			/* @var Object Databazy	*/
			$this->database =  new \Vendor\Database\Database($this->registry->mysql, $this->registry->session);
		}

		/**
		 * Poziadavka na mysql
		 *
		 * @param String
		 * @return Array Or False	
		 */
		public function query($query)
		{
			if (strlen($query) != 0) {
				// clanky
				$qrespond = $this->registry->
													 mysql->
													 executeQuery($query);

				$content = $this->registry->
													mysql->
													getRows();
				return $content;
			}
			else {
				// vypis flash hlasky
				$this->registry->session->set("flash", "Zle zadaná požiadavka!!!", false);
				// navrat prazdne pole
				return array();
			}
		}

		/**
		 * Clanky daneho uzivatela
		 *
		 * @param Array
		 * @param Array
		 * @param String
		 * @return Array Or False	
		 */
		public function request($operation, $condition = array(), $table = self::TABLE_ARTICLES)
		{
			/* Sql poziadavka a vykonanie */
			$respond = $this->database
											->select($operation, 
											 				 $condition, 
															 $table);
			if (!empty($respond)) {
				// Obsah poziadavky
				return $respond;
			}

			return false;
		}
	}

