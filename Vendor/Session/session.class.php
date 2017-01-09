<?php

	/***
	* INFOSERVIS Copyright (c) 2015 
	* 
	* @Author:		Mato Hrinko
	*	@Date:		20.5.2015
	*	@Link:		http://matiasus.cekuj.net
	* 
	* ------------------------------------------------------------
	* Inspiracia: 		http://www.pehapko.cz/programujeme-v-php/sessions
	*
	***/

	namespace Vendor\Session;

	class Session  {

		const MAX_LIFE_TIME = 3600;

		/***
		** @var Spustenie session
		*/
		private static $launched;

		/***
		** @var Zakladne nastavenia
		*/
		private static $configuration = array(

			/* Minimalna zivotnost session */
			"session.gc_maxlifetime" 		=> self::MAX_LIFE_TIME,

			/* Zabezpeci, aby poziadavka na session bola realizovana prostrednictvom cookie, nie pomocou GET parametra */
			"session.use_cookies" 			=> 1,
			/* Zabezpeci, aby poziadavka na session bola realizovana IBA prostrednictvom cookie */
			"session.use_only_cookies"	=> 1,
			/* Musi byt nastavene na 1 - nebude citat ani zahrnovat SID do url */
			"session.use_trans_sid"		 	=> 0,

			/* */
			"session.cookie_lifetime" 	=> self::MAX_LIFE_TIME,
			/* Nastavuje cestu, kde sa ma vytvarat cookie - cookie vramci celej domeny  */
			"session.cookie_path" 			=> "/",
			/* Nastavuje domenu, v ramci ktorej je mozne pouzivat cookie */
//			"session.cookie_domain" 		=> "",
			/* Zabezpeci, ze cookie je pristupne iba prostrednictvom http alebo https */
			"session.cookie_secure"	 		=> FALSE,
			/* Zabezpeci, ze cookie je pristupne iba prostrednictvom http, nie cez Javascript */
			"session.cookie_httponly" 	=> TRUE
		);

		/**
		** Objekt registru
		*/
		private $registry;

		/**
		** Konstruktor vytvorenia spojenia s registrom
		**
		** @parameter Registry objekt
		** @return void
		*/
		public function __construct( \Vendor\Registry\Registry $registry ) 
		{
			/* @var Object Register */
			$this->registry = $registry;

			/* @fun Spustenie session */
			$this->launchSession();
		}

		/**
		** Spustenie spracovania session
		**
		** @parameter Void
		** @return Void
		*/
		public function launchSession()
		{
			/* Overenie existencie session */
			if (session_id() == "")
			{
				/* Naciatnie nastavenych konfiguracii */
				$this->loadConfig();
				/* Zahajenie session */
				session_start();
			}
		}

		/**
		** Naciatnie zakladnych nkonfiguracii
		**
		** @parameter Void
		** @return Void
		*/
		private function loadConfig()
		{
			foreach (self::$configuration as $var => $value)
			{
				if (function_exists('ini_set'))
				{
					ini_set($var, $value);
				}
			}
		}

		/**
		** Volanie premennej ulozenej v poli $session
		**
		** @parameter String - vyber premennej podla kluca
		** @return String - premenna session
		*/
		public function getId()
		{
				return session_id();
		}

		/**
		** Volanie premennej ulozenej v poli $session
		**
		** @parameter String - vyber premennej podla kluca
		** @return String - premenna session
		*/
		public function get($key)
		{
			if (array_key_exists($key, $_SESSION)) {
				return $_SESSION[$key];
			}
	
			return False;
		}

		/***
		 * Vlozenie premennej $_SESSION do pola $session
		 *
		 * @param String - vyber premennej podla kluca
		 * @param String | Array - vyber premennej podla kluca
		 * @param Boolean - s regeneraciou
		 * @return String - premenna session
		 */
		public function set($key, $value, $regenerate = False)
		{
			/* Nastavi premennu $_SESSION */
			$_SESSION[$key] = $value;

			/* Regeneruje session id */
			if ($regenerate === True)
			{
				$this->regenerate();
			}
		}

		/**
		** Rekurzia, ktore transponuje pole na objekt
		**
		** @parameter Array - transponovane pole
		** @return Object (stdClass) - pole v tvare objektu
		*/
		public function toObject($pole)
		{
			/* V kazdom rekurzivnom volani vytvoreny novy objekt aby NEDOCHADZALO k prepisovaniu */
			$toObject = new \stdClass();

			/* Prechod cez jednotlive prvky */
			foreach ($pole as $key => $value)
			{
				if (is_array($value))
				{
					$toObject->{$key} = $this->toObject($value);
				}
				else
				{
					$toObject->{$key} = $value;
				}
			}

			return $toObject;
		}


		/**
		** Regeneracia session identifikatora
		**
		** @parameter Void
		** @return Void
		*/
		public function regenerate()
		{
			session_regenerate_id(true);
		}

		/**
		** Zrusenie $_SESSION
		**
		** @parameter String - kluca Session
		** @parameter Boolean - TRUE - zrusenie pomocou session_destroy()
		** @return void
		*/
		public function destroy($key, $destroy = false)
		{
			/* Zrusenie/odnastavenie session */
			unset($_SESSION[$key]);

			/* Znicenie session */
			if ($destroy === true)
			{
				session_unset();
				session_destroy();
			}
		}

	}

