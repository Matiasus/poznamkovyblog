<?php

  namespace Application\Module\Admin\Controller;

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
        // odkial
        $from = array($this->tab_articles, 
                      array($this->tab_users,
												$this->tab_articles.'.Usersid'=>$this->tab_users.'.Id'));
        // podmienka
        $where = array(
                   array('=',$this->tab_articles.'.Usersid'=>$this->user['Id']));
        // zotriedenie
        $order = array($this->tab_articles.'.Category', $this->tab_articles.'.Title');
        // spracovanie poziadavky
        $this->variables["articles"] = $this->database
                                            ->select($select)
                                            ->from($from) 
                                            ->where($where)
                                            ->order($order)
                                            ->query();

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
        // unaccent category
        $data['Category_unaccent'] = $this->database->unAccentUrl($data['Category']);
        // unaccent title
        $data['Title_unaccent'] = $this->database->unAccentUrl($data['Title']);
        // vlozenie dat do tabulky
				$this->article
             ->insert($data, 
                      $this->tab_articles,
                      true);
/*
        // odstranenie html tagov
        $striped_tags = $this->database
                             ->stripHtmlTags($data['Content']);
        // odstranenie pripadnych entit
        $content_unaccent = $this->database
                                 ->unAccentUrl($striped_tags, ' ');

        // id clanku
        $id_articles = $this->database
                            ->lastInsertId();
        // hodnoty pre zapis do tabulky bez diakritiky
        $data_unaccent = array("Category"=>$data['Category'], 
                               "Title"=>$data['Title'], 
                               "Content"=>$content_unaccent,
                               "Id_articles"=>$id_articles);
        // vlozenie dat do tabulky
        $this->article
             ->insert($data_unaccent, 
                      $this->tab_articles_unaccent,
                      true);
*/
			}

			return $form;
		}
		/**
		 * Renderovacia metoda na editaciu clankov
		 *
		 * @param void
		 * @return void
		 */		
		public function renderEdit()
		{
			if ($this->registry->user->getLoggedIn() === FALSE)	{
				$this->registry->route->redirect();
			} else {
				/* Prihlasovacie meno */
				$this->variables["User"] = $this->user['Username'];
				/* privilegia */
				$this->variables["Email"] = $this->user['Email'];
				/* privilegia */
				$this->variables["Privileges"] = $this->user['Privileges'];
				// Doplnenie poloziek do bocneho menu
				$this->menucreator->addSupplement(array("Items"=>array(
                                                  "Poznámky"=>array(
                                                    "Nástroje"=>array(
                                                      "Nová" =>"/".$this->user['Privileges']."/articles/insert/",
	    																						 		"Vymaž"=>"/".$this->route->getFullUrl(true)."?do=remove"
                                                  )))));
        // Vertikalne menu
    		$this->variables['menu'] = $this->menucreator->build($this->user['Privileges']);
        // call javascript to load ckeditor
        $this->javascript->ckeditor();
			}
		}

		/**
		 * Formular na editovanie clankov
		 *
		 * @param void
		 * @return void
		 */		
		public function formEdit()
		{
      // parametre url adresy
      $parameters = $this->route->getParameters();

			// clanok
			$select = array($this->tab_articles.'.Title',
											$this->tab_articles.'.Category',
                      $this->tab_articles.'.Content');
      // odkial
      $from = array($this->tab_articles);
      // podmienka
      $where = array(
                 array('=',$this->tab_articles.'.Id'=>$parameters[1]));
      // spracovanie poziadavky
      $article = $this->database
                      ->select($select)
                      ->from($from) 
                      ->where($where)
                      ->query();
      // formular na edotovanie clankov
			$form = new \Vendor\Form\Form($this->registry);

			// Druhy parameter vola metodu execute, ktora spracuje formular
			$form->setAction($this->route->getFullUrl());
			$form->setInlineForm(true);

			$form->addText('Category', 'Rubrika', $article[0]->Category)->setRequired();
			$form->addText('Title', 'Názov článku', $article[0]->Title)->setRequired();
			$form->addTextarea('Content', 'Obsah článku', 10, 50, 'editor', $article[0]->Content);
			$form->addSubmit('submit', 'Edituj článok');

			// Nastavenie tabulky, s ktorou sa ma pracovat
			$this->registry->mysql->setTable($this->tab_articles);

			/**
			 * Validacia zadanych nazvov jednotlivych prvkov formulara
			 * ci sa zhoduju s nazvami stlpcov prislusnej tabulky v MySQL
			 * +--------------------------+
			 * | az po odoslani formulara |
			 * +--------------------------+
			 */
			if ($form->succeedSend())
			{
				$data = $form->getData();
				$data['Usersid'] = $this->user['Id'];

				$this->article
             ->update($data, 
							 			  array("Articles.Id" => $parameters[1]),
											$this->tab_articles);
			}

			return $form;
		}
		/**
		 * Renderovacia metoda na vymazanie clanku
		 *
		 * @param void
		 * @return void
		 */		
		public function renderRemove()
		{
			if ($this->registry->user->getLoggedIn() === FALSE)
			{
				$this->registry->route->redirect();
			}
			else
			{
        // parametre url adresy
        $parameters = $this->route->getParameters();
				// privilegia 
				$this->variables["Privileges"] = $this->user['Privileges'];
				// Doplnenie poloziek do bocneho menu
				$this->menucreator
             ->addSupplement(array("Items"=>array(
                                     "Články"=>array('Vlož'=>"/".$this->user['Privileges']."/articles/insert/"))));
				// Vertikalne menu
				$this->variables['menu'] = $this->menucreator
                                        ->create($this->menucreator->variables['Items']);
        // call javascript to load ckeditor
        $this->javascript->ckeditor();
        // delete article
				$this->article->delete(array("Articles.Id"=>$parameters[0]),
															 $this->tab_articles);
			}
		}
		/**
		 * Renderovacia metoda na zmenu statusu clanku
		 *
		 * @param void
		 * @return void
		 */		
		public function renderPublic()
		{
			if ($this->registry->user->getLoggedIn() === FALSE)	{
        // presmerovanie na prihlasenie
				$this->registry->route->redirect();
			}	else {
        // parametre url adresy
        $parameters = $this->route->getParameters();
				// privilegia 
				$this->variables["Privileges"] = $this->user['Privileges'];
				// Doplnenie poloziek do bocneho menu
				$this->menucreator
             ->addSupplement(array("Items"=>array(
                                    "Poznámky"=>array(
                                      "Nástroje"=>array(
                                        "Nová" =>"/".$this->user['Privileges']."/articles/insert/",
																		 		"Vymaž"=>"/".$this->route->getFullUrl(true)."?do=remove"
                                                  )))));
        // Vertikalne menu
    		$this->variables['menu'] = $this->menucreator->build($this->user['Privileges']);
        // call javascript to load ckeditor
        $this->javascript->ckeditor();
        // aktualizacia stavu
        $data = array("Type" => $parameters[0]);
        // change status article
				$this->article
             ->update($data, 
							 			  array("Id" => $parameters[1]),
											$this->tab_articles);
			}
		}
	}

