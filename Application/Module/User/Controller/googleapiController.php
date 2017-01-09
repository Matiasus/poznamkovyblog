<?php

  namespace Application\Module\User\Controller;

	class googleapiController extends \Vendor\Controller\Controller{

    const DELIMETER = "-";

		/**
		 * @var Objekt \Application\Module\Admin\Model\Menu
		 */
		private $menu;

		/**
		 * @var Objekt uzivatela
		 */
		private $user;

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
		 * @param Object \Vendor\Registry\Registry
		 * @return Void
		 */
		public function __construct(\Vendor\Registry\Registry $registry)
		{
      // objekt registra
			$this->registry = $registry;

			// objekt uzivatela
			$this->user = $this->registry->user->getLoggedUser();

			// Objekt databaoveho spojenia a spracovania
      // @var Object \Vendor\Mysql\Mysql
      // @var Object \Vendor\Session\Session
			$this->database = new \Vendor\Database\Database($this->registry->mysql, $this->registry->session);	
			// objekt uzivatela
			$this->content = new \Application\Model\Content($this->registry);	

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
				/* Presmerovanie po neuspesnom prihlaseni */
				$this->registry->route->redirect();
			}	else {
				/* Vytvorenie bocneho menu */
				$this->variables['User'] = $this->content->
														 							request(array("Username"),
																					 			  array("Id"=>$this->user['Id']),
																								  \Application\Config\Settings::$Detail->Mysql->Table_Users);

				/* privilegia */
				$this->variables["Email"] = $this->user['Email'];
				/* privilegia */
				$this->variables["Privileges"] = $this->user['Privileges'];
				/* Vytvorenie bocneho menu */
				$this->variables['articles'] = $this->content->
																							request(array("*", "LOWER(Category) as category"),
																											array("Id"=>$this->user['Id']));
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
		public function renderPlay(){

			if ($this->registry->user->getLoggedIn() === FALSE)	{
				/* Presmerovanie po neuspesnom prihlaseni */
				$this->registry->route->redirect();
			}	else {
				/* Vytvorenie bocneho menu */
				$this->variables['User'] = $this->content->
														 							request(array("Username"),
																					 			  array("Id"=>$this->user['Id']),
																								  \Application\Config\Settings::$Detail->Mysql->Table_Users);

				/* privilegia */
				$this->variables["Email"] = $this->user['Email'];

				/* privilegia */
				$this->variables["Privileges"] = $this->user['Privileges'];

				/* Vytvorenie bocneho menu */
				$this->variables['articles'] = $this->content->
																							request(array("*", "LOWER(Category) as category"),
																											array("Id"=>$this->user['Id']));
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

