<?php 

	namespace Vendor\Generator;

	/**
	** Trieda pracujuca s uzivatelom
	*/
	class Generator{

		const IDENTITY	= "Identificator";
		const ADDRESS 	= "Address";
		const ACCEPT 		= "Accept";
		const AGENT			= "Agent";
		const USER 			= "User";

		const TOKEN_DELIMITER = ":";
		const PATTERN_USER_AGENT = "/[a-zA-Z0-9.\/]+/";

		private static $indices = array(self::ADDRESS => "REMOTE_ADDR",
																		self::ACCEPT	=> "HTTP_ACCEPT", 
																		self::AGENT		=> "HTTP_USER_AGENT");

		/**
		** @Objekt registru
		*/
		private $registry;

		/**
		** @var Token
		*/		
		private $token;

		/**
		** @array User
		*/		
		private $user;

		/**
		** Konstruktor databázového objektu - ulozenie do premennej registry odkaz na register
		**
		** @parameter Registry objekt
		** @return Void
		*/
		public function __construct( \Vendor\Registry\Registry $registry )
		{
			$this->registry = $registry;
			$this->user = $this->registry->user->getLoggedUser();
		}

		/**
		** Vytvorenie tokenu pre trvale prihlasenie
		**
		** @parameter Void
		** @return Void
		*/
		public function create()
		{
			/* Spracovanie glabalneho pola $_SERVER s pozadovanymi exponentmi */
			foreach (self::$indices as $key => $value)
			{
				$token[$key] = (!empty($_SERVER[$value])) ? $_SERVER[$value] : "";
			}

			/* @Array Rozklad user agenta */
			preg_match_all(self::PATTERN_USER_AGENT, $token[self::AGENT], $result);

			/* @Array Rozklad user agenta do pola */
			if (is_array($result) && !empty($result))
			{
				foreach($result[0] as $key => $value)
				{
					$agent[] = $value;
				}

				/* @String Spojenie user do retazca agenta bez medzier */
				$token[self::AGENT] = implode("\0", $agent);

				/* @String Spojenie s akceptaciou, ip a id prihlaseneho uzivatela */
				$this->token = implode(self::TOKEN_DELIMITER, $token);

				/* Zahashovanie tokenu pomocou md5 */
				return $this->token = md5($this->token);
			}
		}

		/**
		** Zvalidovanie tokenu pre trvale prihlasenie
		**
		** @parameter Void
		** @return Void
		*/
		private function validate()
		{
			$token = $this->create();

			echo $token;
		}

	}
