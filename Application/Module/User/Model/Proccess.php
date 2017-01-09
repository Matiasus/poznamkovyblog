<?php

	namespace Application\Module\User\Model;

	class Proccess {

		const TABLE_USERS = "Users";
		const TABLE_AUTHS = "Authentication";

		/***
		** Objekt \Vendor\Registry
		*/
		private $registry;

		/***
		** Objekt \Vendor\Database - spracovanie databazy
		*/
		private $database;

		/***
		** Objekt \Vendor\Mysql - spojenie s databazou \Vendor\Mysql
		*/
		private $connection;

		/***
		** Objekt Uzivatela
		*/
		private $user;

		/***
		** @parameter |Vendor\Registry - Konstruktor vytvorenia spojenia s registrom
		** @return Void
		*/
		public function __construct(\Vendor\Registry\Registry $registry)
		{
			/* Prepojenie na register */
			$this->registry = $registry;
			/* Prepojenie na MySQL */
			$this->connection = $this->registry->mysql;
			/* Vytvorenie instancie na Databazu */
			$this->database = new \Vendor\Database\Database($this->registry);
			/* Prihlaseny uzivatel */
			$this->user = $this->registry->user->getLoggedUser();
		}

		/***
		** Registracia po uspesnej kontrole nazvov stlpcov
		** 
		** @parameter Formular
		** @Return Object
		*/
		public function infouser()
		{
			$user = $this->user;
			/* Odstrani prvy prvok isLoggedIn */
			array_shift($user);
			/* Vrati ako objekt */
			return (object) $user;
		}

	}

