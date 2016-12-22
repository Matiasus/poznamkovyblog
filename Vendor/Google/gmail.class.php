<?php 

	namespace Vendor\Google;

	/**
	** Trieda pracujuca s uzivatelom
	*/
	class Gmail{

		/* Google client */
		private $client;

		/***
		** Konstruktor
		**
		** @parameter \Vendor\Registry - Konstruktor vytvorenia spojenia s registrom
		** @parameter String - typ pristupu {online, Offline}
		** @return Void
		*/
		public function __construct(\Google_Client $client)
		{
			$this->client = $client;

			/* Osetrenie v pripade ak neprebehla uspesne inicializacia Google clienta */
			if (!empty($this->client))
			{
				/* Volanie sluzby Gmail	*/
				$this->service = new \Google_Service_Gmail($this->client);
			}
		}

		/***
		** Sluzba na listovanie sprav
		**
		** @parameter Void
		** @return Object Google_Service_Gmail_messages
		*/
		public function listMessages()
		{
			if ($this->client->getAccessToken())
			{
				/* Nastavenie access tokenu */
				$this->client->setAccessToken($this->client->getAccessToken());
	
				/* Doplnkove parametre */
				$params = array("maxResults"=>"5", 
												"labelIds"=>"INBOX");
				/* Odpoved servera so spravami */
				$response = $this->service->users_messages->listUsersMessages("me", $params);
				
				if (!empty($response))
				{
					return $response->messages;
				}
			}

			return False;
		}

		/***
		** Sluzba so spravami
		**
		** @parameter Void
		** @return Object Google_Service_Gmail_messages
		*/
		public function getMessage($id)
		{
			if ($this->client->getAccessToken())
			{
				/* Nastavenie access tokenu */
				$this->client->setAccessToken($this->client->getAccessToken());

				/* Odpoved servera so spravami */
				$response = $this->service->users_messages->get("me", $id);

				/* Od koho prisla sprava */
				$from = $response->getPayload()->headers[4]->value;
			
				if (!empty($response->getPayload()->parts[0]))
				{
					$message = base64_decode(strtr($response->getPayload()->parts[0]->body['data'], '-_', '+/'));
				}
				else
				{
					$message = base64_decode(strtr($response->getPayload()->body['data'], '-_', '+/'));
				}			

				if (!empty($response))
				{
					return (object) array("from" => $from, "message" => $message);
				}
			}

			return False;
		}

	}
