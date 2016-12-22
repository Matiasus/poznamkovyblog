<?php

  namespace Application\Module\Front\Controller;

	class formController extends \Vendor\Controller\Controller{

		const USERS = 'Users';

		/**
		 * @var Object \Vendor\Registry\Registry
		 */
		private $registry;

		/**
		 * @var Object \Vendor\Database\Database
		 */
		private $database;

		/***
		 * Konstruktor vytvorenia spojenia s registrom
		 *
		 * @param Object \Vendor\Registry\Registry
		 * @return Void
		 */
		public function __construct(\Vendor\Registry\Registry $registry)
		{
			$this->registry = $registry;
			// Prepojenie na model spracovania formularov
			$this->formprocess = new \Application\Module\Front\Model\formproccess($this->registry);
		}

		/*******************************************************************
		** REGISTRACIA - Formular
		********************************************************************/
		/***
		 * Render - registration
		 *
		 * @param Object \Vendor\Registry\Registry
		 * @return Void
		 */
		public function renderRegistracia()
		{
		}

		/***
		 * Form - registration
		 *
		 * @param Void
		 * @return Void
		 */
		public function formRegistracia()
		{
			$form = new \Vendor\Form\Form( $this->registry );

			$form->setAction($this->registry->route->getFullUrl());
			$form->setInlineForm(false);

			$form->addEmail('Email', 'E-mail', '')->setRequired();
			$form->addText('Username', 'Meno/Name', '')->setRequired();
			$form->addPassword('Passwordname', 'Heslo/Password', '')->setRequired();
			$form->addSubmit('submit', 'Registrovať');

			/**
			 * Nastavenie tabulky, s ktorou sa ma pracovat
			 */
			$this->registry->mysql->setTable(self::USERS);

			/**
			 * Validacia zadanych nazvov jednotlivych prvkov formulara
			 * => ci sa zhoduju nazvy prvkov formulara s nazvami stlpcov prislusnej tabulky
			 */
			if ( $form->succeedSend() === TRUE )
			{
				$this->registraciaProccess($form);
			}
			return $form;
		}

		/***
		 * Form - callback
		 *
		 * @param Array
		 * @return Void
		 */
		private function registraciaProccess($form)
    {
			// Spracovanie registracie
			$this->formprocess->registration($form); 
		}

		/*******************************************************************
		** PRIHLASENIE
		********************************************************************/
		/***
		 * Render - logon
		 *
		 * @param Void
		 * @return Void
		 */
		public function renderDefault()
		{
			$user = $this->formprocess->validationCookie();
      $uri = $this->registry
                  ->cookie
                  ->get(\Application\Config\Settings::$Detail->Cookies->Last_uri);
      // check if user log on
			if (!empty($user))
			{
        // redirect to last visited uri
				$this->registry->route->redirect($uri);
			}
		}

		/***
		 * Form - logon
		 *
		 * @param Void
		 * @return Void
		 */	
		public function formPrihlasenie()
		{
			$form = new \Vendor\Form\Form( $this->registry );

			$form->setAction( $this->registry->route->getFullUrl() );
			$form->setInlineForm(false);

			$form->addText('Username', 'Meno/Name', '')->setRequired();
			$form->addPassword('Passwordname', 'Heslo/Password', '')->setRequired();
			$form->addCheckbox('Persistentlog', 'Pamataj', 'Ostať prihlásený');
			$form->addSubmit('submit', 'Prihlásiť');

			// Nastavenie tabulky, s ktorou sa ma pracovat
			$this->registry->mysql->setTable(self::USERS);

			/**
			 * Validacia zadanych nazvov jednotlivych prvkov formulara
			 * ci sa zhoduju s nazvami stlpcov prislusnej tabulky v MySQL
			 */
			if ($form->succeedSend()) {
        // callback logon
				$this->prihlasenieProccess($form);
			}
			return $form;
		}

		/***
		 * Callback - logon
		 *
		 * @param Array
		 * @return Void
		 */	
		private function prihlasenieProccess($form) 
		{
			// Spracovanie prihlasenia
			$this->formprocess->logon($form); 
		}

		/*******************************************************************
		** AKTIVACIA - Formular
		********************************************************************/
		/***
		 * Render - activation
		 *
		 * @param Void
		 * @return Void
		 */	
		public function renderAktivacia()
		{
			// Spracovanie registracie
			$this->formprocess->activation(); 
		}

	}

