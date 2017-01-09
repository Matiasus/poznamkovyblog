<?php

	namespace Application\Model;

	class Article{

		/** @var Objekt \Vendor\Registry\Registry */
		private $user;

		/** @var Objekt \Vendor\Route\Route */
		private $route;

		/** @var Objekt \Vendor\Session\Session */
		private $session;

		/** @var Objekt \Vendor\Database\Database */
		private $database;

		/***
		 * @param Object \Vendor\Database\Database
		 * @param Object \Vendor\User\User
		 * @return Void
		 */
		public function __construct(\Vendor\Database\Database $database, 
                                \Vendor\Session\Session $session,
                                \Vendor\Route\Route $route,
                                \Vendor\User\User $user)
		{
			// @var stdObject User - prihlaseny uzivatel
			$this->user = (object) $user->getLoggedUser();
			//@var Object Databazy
			$this->route = $route;
			//@var Object Databazy
			$this->session = $session;
			//@var Object Databazy
			$this->database = $database;
		}

		/***
		 * Spracovanie poziadavky clanku
		 * 
		 * @param Array data z formulara
     * @param String - tabulka dotazu
		 * @return Void
		 */
		public function insert($data, $table, $redirect = true)
		{
			/***
			 * @var Array databazy
			 * @var String nazov tabulky
			 * @var Bool Prepare - s predpripravenim (sql injection)
			 */
			$this->database
           ->insert($data, 
                    $table, 
                    true);
			
			// Flash oznam pre uspesne ulozenie do databazy
			$this->session->set("flash", "Dáta úspešne vložené do databázy!");

      if ($redirect === true) {
			  // Presmerovanie na domovsku stranku
			  $this->route->redirect($this->user->Privileges. "/articles/default");
      }
		}

		/**
		 * Update clanku
		 * 
		 * @param Array - data
		 * @param Array - podmienka
		 * @param String - tabulka
		 * @Return Void
		 */
		public function update($data, $condition, $table)
		{
			/***
			 * @var Array databazy
			 * @var String nazov tabulky
			 * @var Bool Prepare - s predpripravenim (sql injection)
			 */
			$this->database->update($data, $condition, $table);
			
			// Flash oznam pre uspesne ulozenie do databazy
			$this->session->set("flash", "Dáta úspešne aktualizované v databáze!");

			// Presmerovanie na domovsku stranku
			$this->route->redirect($this->user->Privileges. "/articles/default");
		}

		/**
		 * Vymazanie clanku
		 * 
		 * @param Array - podmienka
		 * @param String - tabulka
		 * @Return Void
		 */
		public function delete($condition, $table)
		{
			/***
			 * @var Array databazy
			 * @var String nazov tabulky
			 * @var Bool Prepare - s predpripravenim (sql injection)
			 */
			$this->database->delete($condition, $table);
			
			// Flash oznam pre uspesne ulozenie do databazy/
			$this->session->set("flash", "Dáta úspešne vymazané z databázy!");

			// Presmerovanie na domovsku stranku/
			$this->route->redirect($this->user->Privileges. "/articles/default");
		}

	}

