<?php 

	namespace Vendor\User;

	/**
	** Trieda pracujuca s uzivatelom
	*/
	class User{

		const USER = "user";
		const PREG_AGENT = "/[a-zA-Z0-9.\/]+/";

		const SESS_ID  = "Id";
		const SESS_NAME  = "Username";
		const SESS_EMAIL = "Email";
		const SESS_LOGIN = "isLoggedIn";
		const SESS_LOGON  = "Logon";
    const IP_ADDRESS = 'Ip';
    const USER_AGENT = 'Agent';
		const SESS_PRIVILEGES  = "Privileges";
		const SESS_CODEVALIDATION  = "Codevalidation";

		/**
		** Objekt registru
		*/
		private $registry;

		/**
		** Objekt registru
		*/
		private $database;

		/**
		** Detaily, popis uzivatela
		*/
		private $loggeduser = array();

		/**
		** Prihlasenost uzvatela
		*/
		private $isLoggedIn = false;

		/**
		** Konstruktor databázového objektu - ulozenie do premennej registry odkaz na register
		**
		** @parameter Registry objekt
		** @return Void
		*/
		public function __construct(\Vendor\Registry\Registry $registry)
		{
			$this->registry = $registry;

			$this->setLoggedIn();
			$this->setLoggedUser();
		}

		/**
		** Nastavenie prihlasenie uzivatela
		**
		** @parameter Void
		** @return Void
		*/
		public function setLoggedIn()
		{
			$session = $this->registry->session->get(self::USER);

			if (!empty($session))	{
				$this->isLoggedIn = True;
			}	else {
				$this->isLoggedIn = False;
			}
		}

		/***
		 * Nastavenie prihlasenie uzivatela
     *
		 * @param Void
		 * @return Boolean
		 */
		public function getLoggedIn()
		{
			return $this->isLoggedIn;
		}

		/***
		 * Ulozenie prihlaseneho uzivatela na zaklade session
		 *
		 * @param Void
		 * @return Void
		 */
		private function setLoggedUser()
		{
			if(is_array($this->registry->session->get(self::USER)))	{
				$session = $this->registry->session->get(self::USER);
				if (!empty($session))	{
					$this->loggeduser = $session;
				}
			}
		}

		/**
		 * Volanie prihlaseneho uzivatela
		 * 
		 * @param Void
		 * @return Object uzivatela alebo Bool false
		 */
		public function getLoggedUser()
		{
			if(!empty($this->loggeduser)) {
				return $this->loggeduser;
		  }	else {
				return FALSE;
			}
		}

		/***
		 * Prihlasenie uzivatela prostrednictvom triedy Authenticate
     *		 
     * @param String - nick a heslo
		 * @return Boolean
		 */
		public function login($user = array(), $persistent = false)
    {
			// Autentifikacna trieda
			$authenticate = new \Vendor\Authenticate\Authenticate($this->registry);
			$allUserData  = $authenticate->checkLogin($user);

			// Overenie, ci je vratena hodnota overenia prihlasovacich udajov neprazdne pole 		
			if(is_array($allUserData) && 
 				 !empty($allUserData))
			{
				// Nastavenie prihlasenia uzivatela
				$this->registry
						 ->session
						 ->set(self::USER, array(
																self::SESS_LOGIN	=> TRUE,
																self::SESS_ID => $allUserData[0]->Id_Users,
																self::SESS_EMAIL	=> $allUserData[0]->Email,
																self::SESS_NAME	=> $allUserData[0]->Username,
																self::SESS_PRIVILEGES	=> $allUserData[0]->Privileges
									), True);

				$this->setLoggedIn();
				$this->setLoggedUser();

				// Poziadavka na trvale prihlasovanie
				if ($persistent === true) { 
					$authenticate->createPersistentLogin();
				}
				return true;
			}
			return false;
		}

		/**
		 * Hashovanie hesla 
		 * @param String heslo
		 * @return String hash heslo
		 */
		public function hashpassword($password)
		{
			return hash("sha256", $password);
		}

	}
