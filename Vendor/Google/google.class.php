<?php 

	namespace Vendor\Google;

	/**
	** Trieda pracujuca s uzivatelom
	*/
	class Google{
		
		const DATA = 'data';
		const USER = 'user';

		const APPROVAL_PROMPT = 'force';
		const TABLE_GOOGLEAPI = "Googleapi";

		const ACCESS_TYPE_ONLINE = "online";
		const ACCESS_TYPE_OFFLINE = "offline";

		/***
		** @var Array Uzivatel
		*/
		protected $user;

		/***
		** @var stdObject Hodnoty z tabulky Googleapi
		*/
		protected $client;

		/***
		** @var Object Google_Client
		*/
		protected $data;

		/***
		** @var Object Google_Servis
		*/
		protected $service;

		/***
		** @var String typ pristupu Offline, Online
		*/
		protected $access = self::ACCESS_TYPE_OFFLINE;

		/***
		** @var Objekt \Vendor\Registry
		*/
		protected $registry;

		/***
		** @var Objekt \Vendor\Database - spracovanie databazy
		*/
		protected $database;

		/***
		** @var Objekt \Vendor\Mysql - spojenie s databazou \Vendor\Mysql
		*/
		protected $connection;

		/***
		** @parameter \Vendor\Registry - Konstruktor vytvorenia spojenia s registrom
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

			/* Informacie o uzivatelovi */
			$this->user = (object) $this->registry->user->getLoggedUser();

			/*****************************************************************
			** GOOGLE CLIENT
			*****************************************************************/

			/* Zavolanie autoloadera */
			require_once ROOT_DIR . 'Library/Google/autoload.php';

			/* Vytvorenie clienta */
			$this->client = new \Google_Client();
		}

		/***
		** Nastavenie pristupu
		** 
		** @parameter String
		** @Return Void
		*/
		public function setAccessType($type)
		{
			$this->access = $type;
		}

		/***
		** Praca s Google API - inicializacia
		** 
		** @parameter Array - bud $_POST s odoslanymi datami, alebo nic
		** @Return Void
		*/
		public function initialize($data = array())
		{
			if (!empty($data))
			{
				$this->data = (object) $data;
			}
		}

		/***
		** Ziskanie parametra Refresh Token
		** 
		** @parameter Void
		** @Return String
		*/
		public function createAuthUrl()
		{
			if (!empty($this->data->Client_id) &&
					!empty($this->data->Client_secret) &&
					!empty($this->data->Redirect_uri) &&
					!empty($this->data->Scopes))
			{
				$this->client->setAccessType($this->access);
				$this->client->setClientId($this->data->Client_id);
				$this->client->setClientSecret($this->data->Client_secret);
				$this->client->setRedirectUri($this->data->Redirect_uri);
				$this->client->setApprovalPrompt(self::APPROVAL_PROMPT);
				$this->client->setLoginHint($this->user->Email);
				$this->client->setScopes(array($this->data->Scopes));

				/* Pomocne pole pre ulozenie session s datami */
				$session = (array) $this->data;
				$user = $this->registry->user->getLoggedUser();

				/* ulozenie dat do session */
				$this->registry->session->set('data', $session);

				/* Presmerovanie */
				header("Location: " . filter_var($this->client->createAuthUrl(), FILTER_SANITIZE_URL) . "");
				exit;
			}
		}

		/***
		** Autentifikacia na zaklade kodu
		** 
		** @parameter String
		** @Return Void
		*/
		public function authenticate($code)
		{
			/* Nacitanie dat zo session */
			$data = (object) $this->registry->session->get(self::DATA);

			if (isset($code) && 
					isset($this->client) && 
					!empty($data))
			{
				
				$this->client->setClientId($data->Client_id);
				$this->client->setClientSecret($data->Client_secret);
				$this->client->setRedirectUri($data->Redirect_uri);
				$this->client->setScopes(array($data->Scopes));
				$this->client->setAccessType($this->access);

				/* Zrusenie session s datami */
				$this->registry->session->destroy(self::DATA, False);

				/* Autentifikacia */
				$this->client->authenticate($code);

				/* Odpoved */
				$respond = $this->client->getAccessToken();

				/* Odkodovanie odpovede v tvare json */
				$respond = json_decode($respond);

				if (!empty($respond->refresh_token))
				{
					/* Uzivatel nacitany z databazy */
					$user = $this->database
							 				 ->select(array("*"),
																array("Usersid"=>$this->user->Id),
																self::TABLE_GOOGLEAPI);

					if (empty($user[0]))
					{
						/* Predpriprava pola na ulozenie do databazy */
						$insert = array("Usersid" 	=> $this->user->Id,
														"Client_id" 	=> $data->Client_id,
														"Client_secret" => $data->Client_secret,
														"Redirect_uri" => $data->Redirect_uri,
														"Refresh_token" => $respond->refresh_token);

						/* Vlozenie udajov do databazy */
						$this->database->insert($insert, self::TABLE_GOOGLEAPI);

						/* Flash sprava  uspesnosti vlozenia */
						$this->registry->session->set("flash", "Refresh token úspešne uložený do databázy!");
					}
					else
					{
						/* Predpriprava pola na ulozenie do databazy */
						$update = array("Client_id" 	=> $data->Client_id,
														"Client_secret" => $data->Client_secret,
														"Redirect_uri" => $data->Redirect_uri,
														"Refresh_token" => $respond->refresh_token);

						/* Vlozenie udajov do databazy */
						$this->database
								 ->update($update, 
													array("Usersid"	=> $user[0]->Usersid), 
													self::TABLE_GOOGLEAPI);

						/* Flash sprava  uspesnosti vlozenia */
						$this->registry->session->set("flash", "Refresh token úspešne updatovany v databáze!");
					}
				}
				else
				{
					/* Flash sprava  uspesnosti vlozenia */
					$this->registry->session->set("flash", "Refresh token nebol v odpovedi servera!!!");
				}

				/***
				** Presmerovanie s parametrom True
				** @var Adresa
				** @var Typ (True) - absolutna adresa
				*/
				$this->registry->route->redirect($this->registry->route->getBaseUrl());
			}
		}

		/***
		** Extrahovanie udajov z tabulky Googleapi
		** 
		** @parameter Void
		** @Return Void
		*/
		public function getFromDatabase()
		{
			if ($this->user !== False)
			{
				/****
				** Selektovanie udajov z tabulky podla hodnot a podmienky
				** @var Array Stlpce, ktore sa snazim vytiahnut
				** @var Array Podmienka
				** @var String Tabulka
				*/
				$client = $this->database
											 ->select(array("*"), 
																array("Usersid"=>$this->user->Id), 
																self::TABLE_GOOGLEAPI);

				if (!empty($client) && is_array($client))
				{
					return $client[0];
				}
			}

			return False;
		}


		/***
		** Extrahovanie udajov z tabulky Googleapi
		** 
		** @parameter Void
		** @Return Void
		*/
		public function getClient()
		{
			if (!empty($this->client))
			{
				return $this->client;
			}
		}
	}
