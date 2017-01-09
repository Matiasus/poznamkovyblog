<?php

	/***
	* INFOSERVIS Copyright (c) 2015 
	* 
	* Autor:		Mato Hrinko
	*	Datum:		12.7.2015
	*	Adresa:		http://matiasus.cekuj.net
	* 
	* ------------------------------------------------------------
	* Inspiracia: 		
	*
	***/
	namespace Vendor\Cookie;

	class Cookie{

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
			$this->registry = $registry;
		}

		/**
		** Volanie premennej ulozenej v poli $session
		**
		** @parameter String - vyber premennej podla kluca
		** @return String - premenna session
		*/
		public function get($key)
		{
			if (array_key_exists($key, $_COOKIE))
			{
				return $_COOKIE[$key];
			}
			else
			{
				return FALSE;
			}
		}

		/**
		** Nastavenie cookie
		**
		** @parameter String - meno cookie
		** @parameter String - hodnota cookie
		** @parameter String - doba platnosti cookie
		** @return Void
		*/
		public function set($name, $value, $expire, $path = "/", $domain = false)
		{
			setcookie($name, $value, $expire, $path, $domain);
		}

		/**
		** Zrusenie $_COOKIE
		**
		** @parameter String - meno cookie
		** @return Void
		*/
		public function destroy($name)
		{
			if ($this->get($name) !== FALSE)
			{
				unset($_COOKIE[$name]);
				setcookie($name, null, -1, '/');
			}
			else
			{
				$this->registry->errors->log  = "<span style = \"color: #000;\">COOKIE NEEXISTUJE!</span><br/>";
			}
		}

	}

