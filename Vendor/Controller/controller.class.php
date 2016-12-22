<?php

	namespace Vendor\Controller;

	class Controller{

    const MODULE = "Module";
    const BACKSLASH = "\\";
		const CONTROLLER = "Controller";
    const APPLICATION = "Application";

		/** @var String - Controller name */
		protected $__controller_name;

    /** @var Object Controller object */
		private $__controller_object;

    /** @var Array - array of exception */
		private $exceptions = array('form', 'activate');

		/** @var Objekt \Vendor\Registry\Registry */
		private $registry;

		/***
		 * Konstruktor vytvorenia spojenia s registrom
		 *
		 * @param Object \Vendor\Registry\Registry
		 * @return Void
		 */
		public function __construct(\Vendor\Registry\Registry $registry)
    {
			$this->registry = $registry;

			// Pridanie parametrov do renderovacej metody ak su pritomne v url adrese
			if ($this->createController() !== FALSE && 
					!isset($this->registry->route->processing))
			{
        // Vykreslenie volaneho kontrolera
				$this->renderController();
			} elseif (isset($this->registry->route->processing)) {
        // Vykreslenie volaneho kontrolera
				$this->renderController();
        // Vytvorenie novej instancie Processing a zavolanie funkcie spracovania podla parametra $_GET['do'] 
				$this->registry->processing = '\Vendor\Processing\Processing';
			} else {
        // Vypis do chybovej hlasky, v pripade ak nebol najdeny prislusny kontrole  
				$this->registry->errors->log  = "<span style=\"color: black\">Controller</span> $this->__controller_name";
				$this->registry->errors->log .= "<span style=\"color: black\"> does not exists!</span>";
			}
		}
		/***
		 * Vracia odkaz na kontroler podla kluca
		 *
		 * @param Void
		 * @return Object Controller object
		 */
		public function getController()
    {
			if (isset($this->__controller_object)) {
        // controller object
				return $this->__controller_object;
			}
		}

		/***
		 * Vracia odkaz na kontroler podla kluca
		 *
		 * @param Void
		 * @return String | Boolean - Controller object
		 */
		public function getControllerName()
    {
			if (isset( $this->__controller_name))	{
        // controller name
				return $this->__controller_name;
			}

      return false;
		}

		/***
		 * Vytvorenie potrebneho kontrolera
		 *
		 * @param Void
		 * @return Void | Boolean (False)
		 */
		private function createController()
		{
      // namespace objektu kontrolera extrahovaneho z url adresy
      $this->__controller_name = self::BACKSLASH.self::APPLICATION.
                                 self::BACKSLASH.ucfirst($this->registry->route->getModule()).
                                 self::BACKSLASH.self::CONTROLLER.
                                 self::BACKSLASH.$this->registry->route->getController(). 
                                 self::CONTROLLER;
      // pripad pre aktivny modul
      if ($this->registry->route->getLayers() === True) {
        // namespace objektu kontrolera extrahovaneho z url adresy
        $this->__controller_name = self::BACKSLASH.self::APPLICATION.
                                   self::BACKSLASH.self::MODULE.
                                   self::BACKSLASH.ucfirst($this->registry->route->getModule()).
                                   self::BACKSLASH.self::CONTROLLER.
                                   self::BACKSLASH.$this->registry->route->getController(). 
                                   self::CONTROLLER;
      }
			// Vytvorenie potrebneho controlera
			if (class_exists($this->__controller_name)) {
				// Vytvorenie kontrolera
				$this->__controller_object = new $this->__controller_name($this->registry);
			} else {
        // chyba
				return FALSE;
			}

		}

		/***
		 * Volanie zobrazovacej metody parametre pritomne (napr. /mvc/show/2/)
		 *
		 * @param Void
		 * @return Void
		 */
		private function renderController()
		{
			/**
			 * Naplnenie premnnych, $render <- meno renderovacej metody,  
			 * $parameter <- posielanie parametrov
			 */
			$render = 'render' . ucfirst($this->registry->route->getView());

			// Doplnenie parametrov do renderovacej metody
			if (method_exists($this->__controller_object, $render))
			{
        $token_1st = $this->registry
                          ->cookie
                          ->get(\Application\Config\Settings::$Detail->Cookies->Token->One);
        $token_2nd = $this->registry
                          ->cookie
                          ->get(\Application\Config\Settings::$Detail->Cookies->Token->Two);

        $last_uri = $this->registry->route->getFullUrl(true);

				// Overenie trvaleho prihlasenia
				if ($token_1st  && 
            $token_2nd)
        {
          if ($last_uri !== '') {
			      // Ulozenie url 
			      $this->registry->cookie
                           ->set(\Application\Config\Settings::$Detail->Cookies->Last_uri, 
                                  $last_uri, 
													        time() + 60*60);
          }
        }
        // volanie renderovacej metody
				$this->__controller_object->$render();
			} else {
        // Vypis chybovej hlasky
				$this->registry->errors->log  = "<span style=\"color: black\">Method</span> $render";
				$this->registry->errors->log .= "<span style=\"color: black\"> in</span> $this->__controller_name";
				$this->registry->errors->log .= "<span style=\"color: black\"> does not exists!</span>";
			}

		}
	}

