<?php

	namespace Vendor\Route;

	class Route {

		const CALLFUNCT	 = 'do';

		/** @var String - volanie funkcie cez parameter */
		public $processing;

		/** @var Array - obsah rozlozenie url cesty	*/
		private $urlexploded = array();

		/** @var String - pohlad */
		private $view;

		/** @var String - modul */
		private $module;

		/** @var String - kontroller */
		private $controller;

		/** @var Array - parametre */
		private $parameters = array();
		 
		/** @var Object \Vendor\Registry\Registry - objekt registru	*/
		private $registry;
	
		/** @var String - url adresa */
		private $urlpath;

		/** @var Boolean - existencia modulu */
		private $layers = true;

    /***
     * Konstruktor vytvorenia spojenia s registrom
     *
     * @param Object \Vendor\Registry\Registry
     * @return Void
     */
    public function __construct(\Vendor\Registry\Registry $registry) 
    {
      $this->registry = $registry;	
    }

    /***
     * Manualne zadavanie adresy
     *
     * @param String - nastavenie url adresy volania
     * @return Void
     */
		public function setUrlPath($path)
    {
			$this->urlpath = $path;
		}

    /***
     * Ziskanie zakladnej adresy (admin/show/ubuntu/?call=script)
     *
     * @param Void
     * @return String
     */
		public function getUrlPath()
    {
			return $this->urlpath;
		}

		/***
		 * Ziskanie zakladnej adresy (http://www.chat.com/show/ubuntu/?call=script)
     *
     * @param Boolean - vytvorenie adresy komplexnej adresy aj s http:// alebo bez
		 * @return String - url adresa 
		 */
		public function getFullUrl($http = False)
		{
			if ($http === False) {
				return 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			}
			else {
				return ltrim($_SERVER['REQUEST_URI'], "/");
			}
		}

		/***
		 * Ziskanie adresy v tvare (http://www.chat.com/show/ubuntu)
     *
     * @param Boolean - vytvorenie adresy komplexnej adresy aj s http:// alebo bez
		 * @return String - url adresa 
		 */
		public function getBaseUrl($http = False)
		{
			if ($http === False) {
				return 'http://' . $_SERVER['SERVER_NAME'] . DS . $this->urlpath;
			}
			else {
				return DS . $this->urlpath;
			}
		}

		/***
		 * Ziskanie zakladnej adresy v tvare (http://www.chat.com/)
     *
     * @param Boolean - vytvorenie adresy komplexnej adresy aj s http:// alebo bez
		 * @return String - url adresa 
		 */
		public function getUrl($http = False)
    {
			if ($http === False) {
				return 'http://' . $_SERVER['SERVER_NAME'] . DS;
			} 
			else {
				return $_SERVER['SERVER_NAME'] . DS;
			}
		}

		/***
		 * Rozklad url adresy
     *
     * @param Void
		 * @return Void
		 */
		public function getExplodedUrl()
    {
			$this->urlpath = (isset($_GET['route']) && !empty($_GET['route'])) ? $_GET['route'] : '';
	
			if ($this->urlpath != '')
			{
				// Rozklad podla lomitka do pola
				$this->urlexploded = explode( '/', $this->urlpath);
				// Cyklus, ktory odstrani prazne obsahy prvkov polia urlexploded od posledneho prvka
				while (!strlen(end($this->urlexploded)))
				{
					array_pop($this->urlexploded);
				}
			}
			
			// Osetrenie v pripade, ze je volana funkcia cez parameter GET a 'do'
			if (isset($_GET) && !empty($_GET[self::CALLFUNCT])) {
				$this->processing = $_GET[self::CALLFUNCT];
			}
			/* Rozlozenie adresy na modul (ak existuje), kontroler, view */
			$this->urlToModuleControllerView();
		}

		/***
		 * Nastavenie modulov true - moduly nastavene, false - bez modulov
		 * Dolezite z hladiska routovania url adresy, ak module = TRUE prva
		 * hodnota pola $urlexploded oznacuje modul a nasledne controller a
		 * pri module = FALSE prva hodnota controller a nasledne view
     *
     * @param Boolean
		 * @return Void
		 */
		public function setLayers($bool)
    {
			$this->layers = $bool;
		}

		/***
		 * Nastavenie modulov
     *
     * @param Void
		 * @return Boolean
		 */
		public function getLayers()
    {
			return $this->layers;
		}

		/***
		 * Nastavenie modulov
     *
     * @param Void
		 * @return Boolean
		 */
		public function getExplodedArray()
    {
			return $this->urlexploded;
		}

		/***
		 * Z adresy url zisti aky modul, controller a view sa ma sputit (a parameter)
     *		 
     * @param Void
		 * @return Void
		 */
		public function urlToModuleControllerView()
    {
			$index = 0;
			$parameters = array('module'     => \Application\Config\Settings::$Detail->Route->Module, 
											 		'controller' => \Application\Config\Settings::$Detail->Route->Controller, 
													'view'       => \Application\Config\Settings::$Detail->Route->View);
		
			// Testuje, ci je pritomne modulovanie (admin, front, user) module		
			if($this->layers === FALSE) {
				array_shift($parameters);
			}

			// Priradenie jednotlivym klucom hodnoty rozlozenej url adresy
			foreach ($parameters as $key => $parameter) {
				$this->$key = !empty($this->urlexploded[$index]) ? $this->urlexploded[$index] : $parameter;
				$index += 1;
			}

			// Pripojenie parametrov
			if (count($this->urlexploded) > $index) {
				while ($index < count($this->urlexploded)) {
					array_push($this->parameters, $this->urlexploded[$index]);
					$index += 1;
				}
			}
		}

    /***
     * Modul
     *
     * @param Void
     * @return String
     */
		public function getModule()
    {
      return $this->module;
    }

    /***
     * Kontroler
     *
     * @param Void
     * @return String
     */
		public function getController()
    {
      return $this->controller;
    }

    /***
     * Pohlad
     *
     * @param Void
     * @return String
     */
		public function getView()
    {
      return $this->view;
    }

    /***
     * Parametre
     *
     * @param Void
     * @return Array
     */
		public function getParameters()
    {
      return $this->parameters;
    }

		/***
		 * Presmerovanie na url adresu, ktoru mozno zadat v dvoch tvaroch ako relativnu alebo absolutnu
		 * http://localhost/admin/home/default
		 *
		 * @params String - adresa presmerovania
		 * @params Bool  True  - absolutna adresa http://localhost/admin/home/default
		 *	 						 False - relativna adresa admin/home/default
		 * @return void
		 */
		public function redirect($addr = false)
		{
			if ($addr == "" || 
         strcmp($addr, "/") === 0) 
      {
				// Presmerovanie na adresu
				header( "Location: " . $this->getUrl() . $addr );
			}	else if (strpos($addr, "http") === False) {
					// Presmerovanie na adresu
					header( "Location: " . $this->getUrl() . $addr );
			} else {
				// Presmerovanie na adresu
				header( "Location: " . $addr );
			}
			// Dolezite pri zobrazeni flash spravy a naslednom zruseni session premennej
			exit(0);
		}
	}
