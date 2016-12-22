<?php

  namespace Application\Module\User\Controller;

	class articlesController extends \Vendor\Controller\Controller{
  
		/** @var Data formulara	 */
		private $form;

		/** @var Objekt uzivatela */
		private $user;

		/** @var Objekt routovania */
		private $route;

		/** @var Objekt obsahu */
		private $content;

		/** @var Objekt registru */
		private $article;

		/** @var Objekt registru */
		private $registry;

		/** @var Objekt database */
		private $database;

		/** @var Objekt javascriptu */
    private $javascript;

		/** @var Objekt menu */
		private $menucreator;

		/** @var Ulozisko premennych */
		public $variables = array();

		/** @var String - tabulka Uzivatelov */
		private $tab_users;

		/** @var String - tabulka Poznamok */
		private $tab_articles;

		/** @var String - tabulka Poznamok bez diakritiky a html tagov */
		private $tab_articles_unaccent;

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
			// informacie o prihlasenom uzivatelovi
			$this->user = $this->registry->user->getLoggedUser();
			// pripajanie na route
			$this->route = $this->registry->route;      
			// Objekt databaoveho spojenia a spracovania
      // @var Object \Vendor\Mysql\Mysql
      // @var Object \Vendor\Session\Session
			$this->database = new \Vendor\Database\Database($this->registry->mysql, $this->registry->session);
			// objekt na pracu s clankami
      // @var Object \Vendor\Database\Database
      // @var Object \Vendor\User\User
			$this->article = new \Application\Model\Article($this->database, $this->registry->session, $this->route, $this->registry->user);	
      // volanie javascript
      $this->javascript = new \Application\Module\Admin\Model\Javascript($this->registry);
			// objekt v modele na vytvorenie menu
			$this->menucreator = new \Application\Model\Menucreator($this->registry);
      //tabulka Uzivatelov
      $this->tab_users = \Application\Config\Settings::$Detail->Mysql->Table_Users;
      // tabulka Poznamok
      $this->tab_articles = \Application\Config\Settings::$Detail->Mysql->Table_Articles;
		}

		/**
		 * Renderovacia metoda pracujuca s vystupom a sablonou
		 *
		 * @param void
		 * @return void
		 */		
		public function renderDefault()
		{
			if ($this->registry->user->getLoggedIn() === FALSE)	{
				$this->route->redirect();
			}	else {
				// privilegia 
				$this->variables["privileges"] = $this->user["Privileges"];
				// clanky
				$select = array($this->tab_articles.'.Id as id',
												$this->tab_articles.'.Title as title',
												$this->tab_articles.'.Category as category',
                        $this->tab_articles.'.Type as type',
                        'DATE_FORMAT('.$this->tab_articles.'.Registered, \'%d.%b. %Y\') as registered',
												$this->tab_users.'.Username',
                        'LOWER('.$this->tab_users.'.Username) as username');
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
                   array('=',\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Type'=>'released')
                 );
        // zotriedenie
        $order = array(\Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Category', \Application\Config\Settings::$Detail->Mysql->Table_Articles.'.Title');
        // spracovanie poziadavky
        $this->variables["articles"] = $this->database
                                            ->select($select)
                                            ->from($from) 
                                            ->where($where)
                                            ->order($order);

        foreach ($this->variables["articles"] as $key => $value) {
          $this->variables["articles"][$key]->title_url = $this->database->unAccentUrl($value->title);
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

		/**
		 * Renderovacia metoda pracujuca s vystupom a sablonou
		 *
		 * @param void
		 * @return void
		 */		
		public function renderInsert()
		{
			if ($this->registry->user->getLoggedIn() === FALSE)	{
				$this->route->redirect();
			}	else {
				// credentials
				$this->variables["User"] = $this->user['Username'];
				// email address
				$this->variables["Email"] = $this->user['Email'];
				// privilegia
				$this->variables["Privileges"] = $this->user['Privileges'];
        // Vertikalne menu
    		$this->variables['menu'] = $this->menucreator->build($this->user['Privileges']);
        // call javascript to load ckeditor
        $this->javascript->ckeditor();
			}
		}

		/**
		 * Formular na zadavanie clankov
		 *
		 * @param void
		 * @return void
		 */		
		public function formVlozclanok()
		{
			$form = new \Vendor\Form\Form($this->registry);

			/* Druhy parameter vola metodu execute, ktora spracuje formular	*/
			$form->setAction($this->route->getFullUrl());
			$form->setInlineForm(true);

			$form->addText('Category', 'Rubrika', '', '15')->setRequired();
			$form->addText('Title', 'Názov článku', '')->setRequired();
			$form->addTextarea('Content', 'Obsah článku', 10, 50, 'editor');
			$form->addSubmit('submit', 'Vlož článok');

			// Nastavenie tabulky, s ktorou sa ma pracovat
			$this->registry->mysql->setTable($this->tab_articles);

			/**
			 * Validacia zadanych nazvov jednotlivych prvkov formulara
			 * ci sa zhoduju s nazvami stlpcov prislusnej tabulky v MySQL
			 * +--------------------------+
			 * | az po odoslani formulara |
			 * +--------------------------+
			 */
			if ($form->succeedSend())	{
        // data z formulara
				$data = $form->getData();
        // doplnenie usersid
				$data['Usersid'] = $this->user['Id'];
        // vlozenie dat do tabulky
				$this->article
             ->insert($data, 
                      $this->tab_articles,
                      true);
			}

			return $form;
		}
	}

