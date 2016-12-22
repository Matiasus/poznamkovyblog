<?php

  namespace Application\Module\Admin\Controller;

	class zdrojController extends \Vendor\Controller\Controller {

		/** @var Objekt uzivatela	*/
		private $user;

		/** @var Objekt routovania */
		private $route;

		/** @var Objekt registru */
		private $registry;

		/** @var Objekt menu */
		private $menucreator;

		/** @var Ulozisko premennych */
		public $variables = array();

		/**
		 * Konstruktor 
		 *
		 * @param void
		 * @return void
		 */
		public function __construct(\Vendor\Registry\Registry $registry)
		{
			// register
			$this->registry = $registry;
			// pripajanie na route
			$this->route = $this->registry->route;
			// Objekt uzivatela
			$this->user = $this->registry->user->getLoggedUser();
			// Objekt databaoveho spojenia a spracovania
      // @var Object \Vendor\Mysql\Mysql
      // @var Object \Vendor\Session\Session
			$this->database = new \Vendor\Database\Database($this->registry->mysql, $this->registry->session);	
			// objekt v modele na vytvorenie bocneho menu
			$this->menucreator = new \Application\Model\Menucreator($this->registry);		
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
				$this->route
             ->redirect();
			} else {
				// privilegia 
				$this->variables["privileges"] = $this->user["Privileges"];

        // Dotaz na clanok podla url parametra
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
				// clanky
				$select = array(\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Id as id',
												\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Title as title',
												\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Title_unaccent as title_unaccent',
												\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Category as category',
                        \Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Category_unaccent as category_unaccent',
                        \Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Type as type',
                        'DATE_FORMAT('.\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Registered, \'%d.%b. %Y\') as registered',
												\Application\Config\Settings::$Detail->Mysql->Table_Users.'.Username',
                        'LOWER('.\Application\Config\Settings::$Detail->Mysql->Table_Users.'.Username) as username');

        // odkial
        $from = array(\Application\Config\Settings::$Detail->Mysql->Table_Articles, 
                      array(\Application\Config\Settings::$Detail->Mysql->Table_Users,
												\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Usersid'=>\Application\Config\Settings::$Detail->Mysql->Table_Users.'.Id'));
        // podmienka
        $where = array(
                   array('=',\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Category'=>$this->route->getController()));
        // zotriedenie
        $order = array(\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Title');
        // spracovanie poziadavky
        $this->variables["articles"] = $this->database
                                            ->select($select)
                                            ->from($from) 
                                            ->where($where)
                                            ->order($order)
                                            ->query();
        // Bocne menu
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
				// Doplnenie poloziek do bocneho menu
				$this->menucreator->addSupplement(array("Items"=>array(
                                                  "Poznámky"=>array(
                                                    "Nástroje"=>array(
                                                      "Nová" =>"/".$this->user['Privileges']."/articles/insert/"
                                                  )))));
        // Vertikalne menu
    		$this->variables['menu'] = $this->menucreator->build($this->user['Privileges']);
			}
		}

		/***
		 * Renderovacia metoda pracujuca s vystupom a sablonou
		 *
		 * @param Void
		 * @return Void
		 */			
		public function renderDetail()
		{
			if ($this->registry->user->getLoggedIn() === FALSE)	{
				// Presmerovanie po neuspesnom prihlaseni
				$this->route->redirect();
			}	else {
				// uprava posledneho parametra url adresy (datumu registracie) do formy ulozeneje v databaze
        $parameters = $this->route->getParameters();
				// privilegia 
				$this->variables["Privileges"] = $this->user['Privileges'];

        // Dotaz na clanok podla url parametra
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // vyber vsetko
        $select = array('*');
        // z tabulky Articles
        $from = array(\Application\Config\Settings::$Detail->Mysql->Table_Articles);
        // podla zhody id s parametrom v url
        $where = array(
                  array('=', \Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Id'=>$parameters[1])
                 );
        // zorad podla Title
        $order = array(\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Title');
        // vytvor poziadavku
        $articles = $this->database
                         ->select($select)
                         ->from($from) 
                         ->where($where)
                         ->order($order)
                         ->query();
 
				// Vybrany clanok
				$this->variables['article'] = $articles[0];

        // Bocne menu
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
				// Doplnenie poloziek do bocneho menu
				$this->menucreator->addSupplement(array("Items"=>array(
                                                  "Poznámky"=>array(
                                                    "Nástroje"=>array(
                                                      "Nová" =>"/".$this->user['Privileges']."/articles/insert/",
                                                      "Uprav"=>"/".$this->user['Privileges']."/articles/edit/".$parameters[0]."/".$parameters[1],
	    																						 		"Vymaž"=>"/".$this->route->getFullUrl(true)."?do=remove"
                                                  )))));
        // Vertikalne menu
    		$this->variables['menu'] = $this->menucreator->build($this->user['Privileges']);
			}
		}
	}
