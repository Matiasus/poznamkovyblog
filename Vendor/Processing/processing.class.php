<?php

	namespace Vendor\Processing;

	class Processing {

		const USER = "User";
    const STATUS_DRAFT = "draft";
    const STATUS_RELEASED = "released";

		/** @var Objekt \Vendor\Route\Route */
		private $route;

		/** @var Objekt \Vendor\Database\Database */
		private $database;

		/** @var Objekt \Vendor\Registry\Registry */
		private $registry;

		/** @var Array - prihlaseny uzivatel */
		private $logged_user = array();

		/** @var Array parametre url adresy */
		private $parameters = array();

		/***
		 * Konstruktor vytvorenia spojenia s registrom
		 *
		 * @param Object \Vendor\Registry\Registry
		 * @return Void
		 */
		public function __construct(\Vendor\Registry\Registry $registry)
		{
      // prepojenie na register
			$this->registry = $registry;
      // objekt routovania
      $this->route = $this->registry->route;
      // objekt routovania
      $this->database = $this->registry->database;
      // prihlaseny uzavatel
      $this->logged_user = $this->registry->user->getLoggedUser();
      // parametre url cesty
      $this->parameters = $this->route->getParameters();
      // akcia spracovania
			$this->action();
		}

		/**
		 * Volanie funkcie podla nastavenej hodnoty GET ['do']
     *
		 * @param void
		 * @return void
		 */
		public function action()
		{
			/* Vyvolanie automaticky akcie ak je v parametry $_GET['do'] nastavena nejaka akcia */
			if ($this->route->processing != "")	{
        // volanie pozadovanej funkcie
				$this->{$this->route->processing}();
			}
		}

		/**
		 * Odhlasenie uzivatela
		 *
		 * @param void
		 * @return void
		 */
		private function odhlas()
		{
/*
      $this->logged_user['Id'];
      $this->database
           ->update

*/
			// Unset & destroy SESSSION
			$this->registry->session->destroy(self::USER, True);
      // Adresa posledneho prihlasenia
			$this->registry->cookie->destroy(\Application\Config\Settings::$Detail->Cookies->Last_uri);
			// Token
			$this->registry->cookie->destroy(\Application\Config\Settings::$Detail->Cookies->Token->One);
			// Id 
			$this->registry->cookie->destroy(\Application\Config\Settings::$Detail->Cookies->Token->Two);
      //
      



			// Presmerovanie na hlavnu stranku
			$this->route->redirect("");
		}

		/**
		 * Zmena stavu clanku draft/released
		 *
		 * @parameter void
		 * @return void
		 */
		private function status()
		{ 
      // zmena statusu
      if (strcmp($this->parameters[0], 
                 self::STATUS_DRAFT) === 0) 
      {
        $text = "uverejniť";
        $status = self::STATUS_RELEASED;  
      } else {
        $text = "stiahnuť";
        $status = self::STATUS_DRAFT;
      }
      // presmerovanie pri suhlase
      $redirect_ok = "/".$this->route->getModule()."/articles/public/".$status."/".$this->parameters[1]."/";
      // presmerovanie pri zamietnuti
      $redirect_cancel = "/".substr($this->route->getFullUrl(true), 0, strpos($this->route->getFullUrl(true), "?"));
      // funkcia javascriptu
      $this->registry->javascript
                     ->setFunction("changeStatus")
                     ->setParameters(array("redirect_ok"=>$redirect_ok, 
                                           "redirect_cancel"=>$redirect_cancel,
                                           "text"=>$text));
		}

		/**
		 * Odhlasenie uzivatela
		 *
		 * @parameter void
		 * @return void
		 */
		private function remove()
		{
      // presmerovanie pri suhlase
      $redirect_ok = "/".$this->route->getModule()."/articles/remove/".$this->parameters[1];
      // presmerovanie pri zamietnuti
      $redirect_cancel = "/".substr($this->route->getFullUrl(true), 0, strpos($this->route->getFullUrl(true), "?"));
      // funkcia javascriptu
      $this->registry->javascript
                     ->setFunction("confirmDelete")
                     ->setParameters(array("redirect_ok"=>$redirect_ok, 
                                           "redirect_cancel"=>$redirect_cancel));
		}
	}

