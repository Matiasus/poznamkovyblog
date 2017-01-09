<?php 

	namespace Vendor\Authenticate;

	/*
	** Trieda pracujuca s prihlasovanim 
	** uzivatelov
	*/
	class Authenticate{

		const CONNECTION = " AND ";
		const AUTH_TABLE = "Authentication";

		/**
		** Objekt registru
		*/
		private $registry;

		/***
     * Konstruktor
     *
		 * @param Object \Vendor\Registry\Registry - Konstruktor vytvorenia spojenia s registrom
		 * @return Void
		 */
		public function __construct(\Vendor\Registry\Registry $registry)
		{
			$this->registry = $registry;
			// Objekt databaoveho spojenia a spracovania
      // @var Object \Vendor\Mysql\Mysql
      // @var Object \Vendor\Session\Session
			$this->database = new \Vendor\Database\Database($this->registry->mysql, $this->registry->session);	
		}

		/**
		 * Konstruktor databázového objektu - ulozenie do premennej registry odkaz na register
		 *
		 * @param Array - Prihlasovacie udaje
		 * @return Boolean | Array - Zaznam o uzivatelovy ak je uspesne zvalidovany 
		 */
		public function checkLogin($user = array())
		{

      if (!empty($user)) {
        // vylistovanie poziadavky
        list($select, $from, $where) = $user;
        // spracovanie poziadavky
        $user = $this->database
                      ->select($select)
                      ->from($from) 
                      ->where($where)
                      ->query();
				// Overenie, ci podla podmienky existuje iba jeden zaznam v tabulke Users 
				if (count($user) === 1) { 
          // prihlaseny uzivatel      
		      return $user;
        }
      }
      return False;
		}
		/**
		** Vytvorenie trvaleho prihlasenia prostrednictvom cookie
		**
		** @parameter Void
		** @return Void
		*/
		public function createPersistentLogin()
		{
			// Volanie generatora tokenu
			$generator = new \Vendor\Generator\Generator($this->registry);
			// Vytvorenie tokenu */
			$token = $generator->create();
			// Naciatnie nastavenych hodnot expiracie zo suboru config.php.ini
			$expiration = \Application\Config\Settings::$Detail->Cookies->Expiration;

			// Trieda pracujuca s datumom a casom
			$datum = new \Vendor\Datum\Datum($this->registry);
			$nowday = $datum->getActualTime();

			// Ak prihlasenie uzivatela prebehlo uspesne
			if ($this->registry->user->getLoggedUser() !== False)
			{
				// Konverzia do objektu
				$loggeduser = $this->registry->session->toObject($this->registry->user->getLoggedUser());
				// Overenie, ci uz bol niekedy uzivatle prihlaseny */
				$last = $this->database
										 ->select(array("*"))
                     ->from(array(self::AUTH_TABLE))
										 ->where(array("Usersid"=>$loggeduser->Id))
                     ->query();

				// Ak uzivatel nebol prihlaseny nikdy vloz aktualny datum a cas
				if ($last === FALSE)
				{
					// Predpriprava pola na ulozenie do databazy
					$insert = array("Token" 	=> $token,
													"Usersid" => $loggeduser->Id,
													"Session" => session_id(),
													"Expires" => $datum->getFutureTime());

					// Vlozenie udajov do databazy
					$this->database
               ->insert($insert, 
                        self::AUTH_TABLE);

					// Ulozenie platne Session uzivatela 
					$this->registry->cookie->set(\Application\Config\Settings::$Detail->Cookies->Token->Two, 
                                       session_id(), 
																			 time() + $datum->getFutureTimeInSeconds());
				}	else {
					// Updejtuje hodnoty
					$update = array("Token" 	=> $token,
													"Session" => session_id(),
													"Expires" => $datum->getFutureTime());
					// Update udajov do databazy
					$this->database
							 ->update($update, 
												array("Usersid"	=> $loggeduser->Id), 
												self::AUTH_TABLE);
					// Ulozenie platnej Session uzivatela
					$this->registry
               ->cookie
               ->set(\Application\Config\Settings::$Detail->Cookies->Token->Two, 
                     session_id(), 
										 time() + $datum->getFutureTimeInSeconds());
				}
				// Ulozenie tokena
				$this->registry->cookie
                       ->set(\Application\Config\Settings::$Detail->Cookies->Token->One, 
                             $token, 
														 time() + $datum->getFutureTimeInSeconds());
			}

		}

	}
