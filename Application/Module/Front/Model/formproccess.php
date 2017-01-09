<?php

	namespace Application\Module\Front\Model;

	class formproccess{

  	/** @var Objekt \Vendor\Registry */
		private $registry;

		/** @var Objekt \Vendor\Mysql - spojenie s databazou \Vendor\Mysql	*/
		private $connection;

		/** Objekt \Vendor\Database - spracovanie databazy */
		private $database;

		/***
     * Konstruktor
     *
		 * @param Object \Vendor\Registry\Registry - Konstruktor vytvorenia spojenia s registrom
		 * @return Void
		 */
		public function __construct(\Vendor\Registry\Registry $registry)
		{
			$this->registry = $registry;
			// Prepojenie na MySQL
			$this->connection = $this->registry->mysql;
			// Objekt databaoveho spojenia a spracovania
      // @var Object \Vendor\Mysql\Mysql
      // @var Object \Vendor\Session\Session
			$this->database = new \Vendor\Database\Database($this->registry->mysql, $this->registry->session);	
		}

		/***
		 * Registracia po uspesnej kontrole nazvov stlpcov
		 * 
		 * @param Formular
		 * @Return Void
		 */
		public function registration($form)
		{
			// Extrahovanie dat z formulara
			$user = $form->getData();

			if ( $user["Email"] != "" && $user["Username"] != "" && $user["Passwordname"] != "")
			{
				// Notifikacia, posielanie emailov s tokenom pre uspesnu registraciu
				$notification = new \Vendor\Notification\Notification($this->registry);

				/***
				 * Predspracovanie udajov pre odoslanie emailom
			   * @Parameters: Sting, String, String
				 *   1.@parameter = Komu
				 *   2.@parameter = Meno/Nick
				 *   3.@parameter = Heslo/Password
				 *
				 * @return: Array
				 *   0.@return => Komu
				 *   1.@return => Predmet
				 *   2.@return => Sprava
				 *   3.@return => Od koho
				 *   4.@return => Validacny kod
				 */
				$parameters = $notification->Preprocessing($user["Email"], $user["Username"], $user["Passwordname"]);

				// Hash hesla
				$user["Passwordname"] = $this->registry->user->hashpassword($user["Passwordname"]);

				// Pridanie na koniec pola validacny kod
				$user["Codevalidation"] = $parameters[4];

				// Overenie existencie uzivatela v databaze na zaklde emailu
				$check = $this->database
                      ->select(array("*"), 
                               array("Email"=>$user["Email"]), 
                               \Application\Config\Settings::$Detail->Mysql->Table_Users);

				if ($check === FALSE)	{
					// Vlozenie uzivatela do databazy
					$this->database
               ->insert($user,  
                        \Application\Config\Settings::$Detail->Mysql->Table_Users);

					// Odoslanie emailu podla predspracovanych udajov
					$notification->Email($parameters);

				}	else {
          // flash sprava
					$this->registry
               ->session
               ->set("flash", "Užívateľ so zadaným emailom už existuje !!!", false);
          // presmerovanie
					$this->registry
               ->route
               ->redirect("/front/form/registracia");
				}
			}
		}

		/***
		 * Spracovanie prihlasovacieho formulara
		 * 
		 * @param Form
		 * @return Void
		 */
		public function logon($form)
		{
			// Uzivatel
			$user = $this->registry->user;
			// Odoslane data
			$data = $form->getData();
			// Trvale prihlasenie
			$persistent = False;

			// Overenie, ci je poziadavka na trvale prihlasenie
			if (isset($_POST['Persistentlog'])) { 
				$persistent = True;	
			}

      // Poziadavka / dotaz
      $select = array("*");
      // odkial
      $from = array(\Application\Config\Settings::$Detail->Mysql->Table_Users, 
               array(\Application\Config\Settings::$Detail->Mysql->Table_Profiles,
                     \Application\Config\Settings::$Detail->Mysql->Table_Profiles.'.Id_Users'=>
                      \Application\Config\Settings::$Detail->Mysql->Table_Users.'.Id')
              );
      // podmienka
      $where = array(
                array('=', \Application\Config\Settings::$Detail->Mysql->Table_Users.'.Username'=>$data['Username']), 
                     'AND', 
                array('=', \Application\Config\Settings::$Detail->Mysql->Table_Users.'.Passwordname'=>$user->hashpassword($data['Passwordname'])),
                     'AND',
                array('=', \Application\Config\Settings::$Detail->Mysql->Table_Users.'.Validation'=>'valid')
               );

      // poziadavaka na overenie uzivatela
      $query_select = array($select, $from, $where);

			// Overenie prihlasovacich udajov
			if ($user->login($query_select, $persistent)) {
        // prihlaseny uzivatel
        $logged_user = $user->getLoggedUser();
        // privilegia
				$privileges = $logged_user['Privileges'];
				// Update z invalid na valid
				$this->database
             ->insert(array('Id_Users'   => $logged_user['Id'],
                            'Datum'      => date("Y-m-d H:i:s"), 
                            'Ip_address' => $_SERVER['REMOTE_ADDR'].':'.$_SERVER['REMOTE_PORT'], 
                            'User_agent' => $_SERVER['HTTP_USER_AGENT'] 
											      ), 
											\Application\Config\Settings::$Detail->Mysql->Table_Logins,
                      true);
        // presmerovanie
				$this->registry->route->redirect($privileges . "/home/default");
			} else {
        // flash sprava
				$this->registry->session->set("flash", "Nesprávne meno, heslo alebo neaktívnosť účtu !!!", false);
        // presmerovanie na prihlasovaciu stranku
				$this->registry->route->redirect("");
			}
		}

		/***
		 * Aktivacia na zaklade url tokenu
		 * 
		 * @param Void
		 * @return Void
		 */
		public function activation()
		{
      $parameters = $this->registry->route->getParameters();

			if (!empty($parameters[0]))
			{
				$user = $this->database->select(array("*"), 
																				array("Codevalidation"=>$parameters[0]), 
																				\Application\Config\Settings::$Detail->Mysql->Table_Users);
				
				// Overenie, ci podla validacneho kluca existuje iba jeden zaznam v tabulke Users 
				if (count($user) == 1)
				{
					// Update z invalid na valid
					$this->database->update(array("Validation"=>"valid"), 
																	array("Codevalidation"=>$parameters[0]), 
																	\Application\Config\Settings::$Detail->Mysql->Table_Users);

					// Vypis flash spravy
					$this->registry->session->set("flash", "Váš účet bol úspešne aktivovaný, pokračujte prosím prihlásením!", false);

					// Presmerovanie na prihlasovaciu stranku
					$this->registry->route->redirect("");
				}
			}
		}

		/***
		 * Overenie platnosti tokenu
		 * 
		 * @param Void
		 * @return String - token
		 */
		public function validationCookie()
		{
			// Volanie generatora tokenu
			$generator = new \Vendor\Generator\Generator($this->registry);
			// Vytvorenie tokenu
			$token = $generator->create();

			// Trieda pracujuca s datumom a casom
			$datum = new \Vendor\Datum\Datum($this->registry);

			// Nazov First
			$cookieToken = \Application\Config\Settings::$Detail->Cookies->Token->One;
			// Nazov Second 
			$cookieSession = \Application\Config\Settings::$Detail->Cookies->Token->Two;

			if (isset($_COOKIE)) {
				if (!empty($_COOKIE[$cookieToken]) && 
            !empty($_COOKIE[$cookieSession]))	{
					// Porovnanie tokena v $_COOKIE s vygenerovanym tokenom
					if (strcmp($_COOKIE[$cookieToken], $token) === 0) {
            // Dotaz na uzivatela
            // ~~~~~~~~~~~~~~~~~~
            // vyber vsetko
            $select = array('*');
            // z tabulky Articles
            $from = array(\Application\Config\Settings::$Detail->Mysql->Table_Authentication);
            // podla zhody id s parametrom v url
            $where = array(
                      array('=', \Application\Config\Settings::$Detail->Mysql->Table_Authentication.'.Session'=>$_COOKIE[$cookieSession])
                     );
						// Overenie existencie tokena v databaze
						$item = $this->database
                         ->select($select)
                         ->from($from) 
                         ->where($where)
                         ->query();

						// Ak zaznam s tokenom existuje
						if ($item !== False)
						{
							// Porovnanie zhody tokena a id uzivatela v databaze s $_COOKIE
							if (strcmp($_COOKIE[$cookieToken], $item[0]->Token) === 0)
							{	
								// Overenie, ci nie expirovany datum a cas
								if ($datum->difference($item[0]->Expires))
								{
                  // Overenie existencie tokena v databaze
                  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                  // vyber vsetko
                  $select = array('*');
                  // z tabulky Articles
                  $from = array(\Application\Config\Settings::$Detail->Mysql->Table_Users);
                  // podla zhody id s parametrom v url
                  $where = array(
                            array('=', \Application\Config\Settings::$Detail->Mysql->Table_Users.'.Id'=>$item[0]->Usersid)
                           );
						      // Overenie existencie tokena v databaze
						      $user = $this->database
                               ->select($select)
                               ->from($from) 
                               ->where($where)
                               ->query();
                  // prihlaseny uzivatel
									return $user[0];
								}
							}
						}
					}
				}
			}
		
			return False;
		}

	}

