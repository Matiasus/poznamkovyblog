<?php

  namespace Application\Module\User\Controller;

	class ckeditorController extends \Vendor\Controller\Controller {

		/**
		 * @var Objekt uzivatela
		 */
		private $user;

		/**
		 * @var Objekt routovania
		 */
		private $route;

		/**
		 * @var Objekt obsahu
		 */
		private $content;

		/**
		 * @var Objekt registru
		 */
		private $registry;

		/**
		 * @var Objekt menu
		 */
		private $menucreator;

		/**
		 * @var Ulozisko premennych
		 */
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
        // Poziadavka / dotaz
        $select = array('DISTINCT('.\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Category)');
        // odkial
        $from = array(\Application\Config\Settings::$Detail->Mysql->Table_Articles);
        // podmienka
        $order = array(\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Category');
        // spracovanie poziadavky
        $unique_categories = $this->database
                                  ->select($select)
                                  ->from($from) 
                                  ->where()
                                  ->order($order);

        // hladanie zhody unikatnej kategorie upravenej do podoby url
        // s hodnotou url kontrolera
        foreach ($unique_categories as $item) {
          if (strcmp($this->database
                          ->unAccentUrl($item->Category), 
                     $this->route
                          ->getController()) === 0) {
            $accent_category = $item->Category;
          }
        }

				// privilegia 
				$this->variables["privileges"] = $this->user["Privileges"];
				// clanky
				$select = array(\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Id as id',
												\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Title as title',
												\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Category as category',
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
                   array('=',\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Category'=>$accent_category));
        // zotriedenie
        $order = array(\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Title');
        // spracovanie poziadavky
        $this->variables["articles"] = $this->database
                         ->select($select)
                         ->from($from) 
                         ->where($where)
                         ->order($order);
        // doplnenie poli
        foreach ($this->variables["articles"] as $key => $value) {
          // uprava titulu
          $this->variables["articles"][$key]->title_url = $this->database->unAccentUrl($value->title);
          // uprava kategorie
          $this->variables["articles"][$key]->category_url = $this->database->unAccentUrl($value->category);
        }
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

        // Poziadavka / dotaz
        $select = array('*');
        // odkial
        $from = array(\Application\Config\Settings::$Detail->Mysql->Table_Articles);
        // podmienka
        $where = array(
                  array('=', \Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Id'=>$parameters[1])
                 );

        // zoradenie
        $order = array(\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Title');

        // spracovanie poziadavky
        $articles = $this->database
                         ->select($select)
                         ->from($from) 
                         ->where($where)
                         ->order($order);
 
				// Vybrany clanok
				$this->variables['article'] = $articles[0];
        $this->variables["article"]->title_url = $this->database->unAccentUrl($this->variables['article']->Title);
        $this->variables["article"]->category_url = $this->database->unAccentUrl($this->variables['article']->Category);

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
	}
