<?php

  namespace Application\Module\Admin\Controller;

	class formController extends \Vendor\Controller\Controller {

		/**
		 * Objekt uzivatela
		 */
		private $user;

		/**
		 * @var Objekt registru
		 */
		private $registry;

		/**
		 * @var Objekt Mysql - spojenie s databazou registry\mysql
		 */
		private $database;

		/**
		 * @var Objekt menu
		 */
		private $menucreator;

		/**
		 * @var Ulozisko premennych
		 */
		public $variables = array();
		/***
		 * Konstruktor vytvorenia spojenia s registrom
		 *
		 * @param \Vendor\Registry\Registry
		 * @return Void
		 */
		public function __construct( \Vendor\Registry\Registry $registry )
		{
			/* Prepojenie na register */
			$this->registry = $registry;
			/* @var stdObject User - prihlaseny uzivatel */
			$this->user = $this->registry->user->getLoggedUser();
			// Objekt uzivatela
			$this->content = new \Application\Model\Content($this->registry);	
			// objekt v modele na vytvorenie menu
			$this->menucreator = new \Application\Model\Menucreator($this->registry);		
		}

		/**
		** Zobrazovacia metoda pre pridanie clanku
		*/
		public function renderPridajclanok()
		{
			if( !( $user = $this->registry->user->getLoggedUser() ) )	{
				$this->registry->route->redirect( 'front/form/prihlasenie/' );
			}
		}

		/**
		** Zadefinovanie formulara pre pridavanie clankov
		*/
		public function formPridajclanok(){

			$form = new \library\forms\Form( $this->registry );

			/* Druhy parameter vola metodu execute, ktora spracuje formular	*/
			$form->setAction( $this->registry->route->getFullUrl(), "vloz" );
			$form->setInlineForm( true );

			$form->addText('Posted', 'Rubrika', '')->setRequired();
			$form->addText('Title', 'Názov', '')->setRequired();
			$form->addTextarea('Content', 'Obsah', 10, 50, 'editor');
			$form->addSubmit('submit', 'Vlož článok');

			/**
			** Nastavenie tabulky, s ktorou sa ma pracovat
			*/
			$this->registry->mysql->setTable( "Contents" );

			/**
			** Validacia zadanych nazvov jednotlivych prvkov formulara
			** ci sa zhoduju s nazvami stlpcov prislusnej tabulky
			*/
			if( $form->succeedSend() ){

				$call = call_user_func( array($this, "pridajclanokSucced") );

			}

			return $form;

		}

		/**
		** Overenie, ci zadane prihlasovacie udaje existuju
		** @parameter void
		** @return formular
		*/
		private function pridajclanokSucced(){


		}

		/**
		** Zobrazovacia metoda pre editovanie clanku
		*/
		public function renderEditujclanok(){

			if ($this->registry->user->getLoggedIn() === FALSE)
			{
				$this->registry->route->redirect();
			}
			else
			{
				/* Prihlasovacie meno */
				$this->variables["User"] = $this->user['Username'];
				/* privilegia */
				$this->variables["Email"] = $this->user['Email'];
				/* privilegia */
				$this->variables["Privileges"] = $this->user['Privileges'];

				/* Vertikalne menu */
				$this->variables['menu'] = $this->menucreator->create($this->menucreator->variables['Items'], 
																															$this->menucreator->variables['Links']);
			}			
		}

		/**
		** Zadefinovanie formulara pre pridavanie clankov
		*/
		public function formEditujclanok(){

			$data = $this->database->select();

			$form = new \Vendor\Form\Form($this->registry);

			/**
			* Druhy parameter vola metodu execute, ktora spracuje formular	
			*/
			$form->setAction( $this->registry->route->getFullUrl(), "edituj" );
			$form->setInlineForm( true );

			$form->addText('Posted', 'Rubrika', '')->setRequired();
			$form->addText('Title', 'Názov', '')->setRequired();
			$form->addTextarea('Content', 'Obsah', 10, 50, 'editor', '');
			$form->addSubmit('submit', 'Vlož článok');

			/**
			** Validacia zadanych nazvov jednotlivych prvkov formulara
			** ci sa zhoduju s nazvami stlpcov prislusnej tabulky
			*/
			if( $form->succeedSend() ){

				$call = call_user_func( array($this, "editujclanokSucced") );

			}

			return $form;

		}

		/**
		** Overenie, ci zadane prihlasovacie udaje existuju
		** @parameter void
		** @return formular
		*/
		private function editujclanokSucced(){


		}

		/*
		** Vytvorenie bocneho menu
		** @parameter void
		** @return void
		*/
		private function Content()
		{
			/**
			** Vytvorenie bocneho menu
			** volanie triedy Menu z modelu
			*/
			$this->model = new \Application\Model\Content( $this->registry );
			$this->variables = $this->model->variables;

			/**
			** Uzivatel
			*/
			$this->variables['user'] = $this->registry->user->getLoggedUser();


		}

	}

