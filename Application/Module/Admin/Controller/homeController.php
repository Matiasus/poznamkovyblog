<?php

  namespace Application\Module\Admin\Controller;

	class homeController extends \Vendor\Controller\Controller{

		/** @var Objekt \Application\Module\Admin\Model\Menu */
		private $menu;

		/** @var \Vendor\User\User->getLoggedUser() */
		private $user;

		/** @var \Vendor\Agent\Agent */
		private $agent;

		/** @var Objekt \Vendor\Registry\Registry */
		private $registry;

		/** @var Objekt \Application\Model\Menucreator */
		private $menucreator;

		/** @var Array - Ulozisko premennych */
		public $variables = array();

		/** @var String - tabulka Uzivatelov */
		private $tab_users;

		/** @var String - tabulka Pristupov */
		private $tab_logins;

		/**
		 * Konstruktor 
		 *
		 * @param Object \Vendor\Registry\Registry
		 * @return Void
		 */
		public function __construct(\Vendor\Registry\Registry $registry)
		{
      // objekt registra
			$this->registry = $registry;
			// objekt uzivatela
			$this->user = $this->registry->user->getLoggedUser();
      // agenti
      $this->agent = new \Vendor\Agent\Agent();
			// objekt uzivatela
			$this->content = new \Application\Model\Content($this->registry);	
			// objekt v modele na vytvorenie bocneho menu
			$this->menucreator = new \Application\Model\Menucreator($this->registry);		
			// Objekt databaoveho spojenia a spracovania
      // @var Object \Vendor\Mysql\Mysql
      // @var Object \Vendor\Session\Session
			$this->database = new \Vendor\Database\Database($this->registry->mysql, $this->registry->session);
      //tabulka Uzivatelov
      $this->tab_users = \Application\Config\Settings::$Detail->Mysql->Table_Users;
      // tabulka Poznamok
      $this->tab_logins = \Application\Config\Settings::$Detail->Mysql->Table_Logins;
		}

		/***
		 * Renderovacia metoda pracujuca s vystupom a sablonou
		 *
		 * @param Void
		 * @return Void
		 */		
		public function renderDefault()
    {
			if ($this->registry->user->getLoggedIn() === FALSE)	{
				// Presmerovanie po neuspesnom prihlaseni
				$this->registry->route->redirect();
			}	else {
        // Poziadavka / dotaz
        $select = array($this->tab_users.'.Username',
                        $this->tab_logins.'.Datum',
                        $this->tab_logins.'.Ip_address',
                        $this->tab_logins.'.User_agent');
        // odkial
        $from = array($this->tab_users,
                 array($this->tab_logins,
                       $this->tab_logins.'.Id_Users'=>
                        $this->tab_users.'.Id')
                );
        // zotriedenie
        $order = array($this->tab_logins.'.Datum DESC');
        // spracovanie poziadavky
        $logins = $this->database
                       ->select($select)
                       ->from($from) 
                       ->where()
                       ->order($order)
                       ->limit(5);
				// privilegia
				$this->variables['logins'] = $logins;
        // doplnenie systemu a browsera uzivatela
        foreach ($logins as $item => $value) {
          // nastavene agenta
          $this->agent->setUserAgent($value->User_agent);
          // extrahovanie systemu
          $logins[$item]->System = $this->agent->getSystem();
          // extrahovanie browsera
          $logins[$item]->Browser = $this->agent->getBrowser();
        }
				// Doplnenie poloziek do bocneho menu
				$this->menucreator->addSupplement(array('Items'=>array(
                                                  'Poznámky'=>array(
                                                    'Nástroje'=>array(
                                                      'Nová' =>'/'.$this->user['Privileges'].'/articles/insert/'
                                                  )))));
        // Vertikalne menu
    		$this->variables['menu'] = $this->menucreator->build($this->user['Privileges']);

			}
		}

	}

