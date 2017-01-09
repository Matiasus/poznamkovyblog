<?php

	namespace Vendor\Template;

	/***
	** Trieda template
	*/
	class Template extends \Vendor\Template\Replace_template{

		const BODY       = '</body>';
		const FLASH      = '{include flashmessage}';
		const FORMS      = '/\{formular (\w+)\}/i';
		const ERROR      = '{include errormessage}';
		const TITLE      = '{include title}';
		const EDITOR     = '{Editor}';
		const CONTENT    = '{include content}';
    const JAVASCRIPT = '{include javascript}';

		/***
		 * Konstruktor vytvorenia spojenia s registrom
		 *
		 * @param Object \Vendor\Registry\Registry
		 * @return Void
		 */
		public function __construct(\Vendor\Registry\Registry $registry)
		{
			$this->registry = $registry;
			$this->prepare();
		}

		/***
		 * Layout - nacitanie sablony
		 *
		 * @param Void
		 * @return Void
		 */
		protected function Layout() 
		{
			/* Overi existenciu subora */
			if (file_exists($this->layout)) {
				// read layout
				$content = file_get_contents($this->layout);

				if (!empty($content)) {
          // content <= layout
					$this->content = $content;
				}
			}	else {
        // error message
				echo "Chyba " . $this->layout . " layout!";
				exit(0);
			}
		}

		/***
		 * Title - nacitanie titulu
		 *
		 * @param Void
		 * @return Void
		 */
		protected function Title()	
		{
			/* Nahradenie {include errormessage} chybovou spravou */
			$this->content = str_replace(self::TITLE, \Application\Config\Settings::$Detail->Template->Title, $this->content);
		}

		/***
		 * Content - obsah sablony
		 *
		 * @param Void
		 * @return Void
		 */
		protected function Content()
    {
			/* Absolutna cesta k sablone podla modulu, kontrolera a pohladu */
			$content = ROOT_DIR . 'Application' . 
					(( !empty($this->registry->route->getModule()) ) ? DS . 'Module/' . ucfirst($this->registry->route->getModule()) : '') 
					. DS . 'Views' . DS . $this->registry->route->getController()
					. DS . $this->registry->route->getView() . '.tpl.php';

			/* Nacitanie upraveneho obsahu do obsahu */
			if(file_exists($content))
			{
				ob_start();
				require_once $content;
				$content = ob_get_contents();
				ob_end_clean();
				$this->content = str_replace(self::CONTENT, $content, $this->content);
			}
			else
			{
				$this->registry->errors->form  = 'Obsah <b>' . $this->content . '</b> in controller <b>';
				$this->registry->errors->form .= $this->registry->route->getController() . 'Controller';
				$this->registry->errors->form .= '</b> does not exist!<br/>';
			}
		}

		/***
		 * Form - nacitanie formulara
		 *
		 * @param Void
		 * @return Void
		 */
		protected function Forms()
		{
			/* 
			** Nahradenie {fromular meno_formulara} html kodom formulara
			*/
			$this->content = preg_replace_callback(self::FORMS, array($this, "callbackForm"), $this->content);
		}

		/***
		 * Form callback - nacitanie formulara
		 *
		 * @param Array
		 * @return Void
		 */
		protected function callbackForm($matches)
		{
			/**
			** Hladany nazov formulara 
			*/
			$form = $matches[1];

			/**
			** Hladana metoda formulara v zozname metod prislusneho kontrolera
			*/
			$method = 'form' . ucfirst($form);
			$methods = get_class_methods($this->registry->controller->getControllerName());

			if(in_array($method, $methods)) {
				/**
				** Metoda triedy (napr. formControllera) musi vratit Objekt Form
				** $controller => napr. formController
				** $method => napr. formKomentar
				*/
				try	{
					return $this->objectController->$method()->getFormContent($form);
				}	
        catch(Exception $exception)	{
					print $exception->getMessage();
				}
			}
			else
			{
				$this->registry->errors->form  = 'Method <b>' . $method . '</b> in controller <b>';
				$this->registry->errors->form .= $this->registry->route->controller . 'Controller';
				$this->registry->errors->form .= '</b> does not exist!<br/>';

			}

		}


		/***
		 * Flash - vypis flash spravy
		 *
		 * @param Void
		 * @return Void
		 */
		protected function Flash()
		{
			$message = "";

			if (isset($_SESSION))
			{
				foreach ($_SESSION as $key => $session)
				{
					if( !is_object($session) && strcmp($key, "flash") === 0 )
					{
						$message .= $session . "<br/>";
						$this->registry->session->destroy($key);
					}
				}

				if (strcmp($message, "") !== 0)
				{
					/***
					** Nahradenie obsahom flash spravy
					** => hladana znacka v \Config\Config::$Flash
					** => nacitanie obsahu session "flash"
					** => obsah 
					*/
					$this->content = str_replace(self::FLASH, 
																			 $message, 
																			 $this->content);
				}
			}

			/***
			** Nahradeni prazdnym retazcom
			** => hladana znacka v \Config\Config::$Flash
			** => nacitanie obsahu session "flash"
			** => obsah 
			*/
			$this->content = str_replace(self::FLASH,
																	 "", 
																	 $this->content);
			return TRUE;
		}

		/***
		 * Editor - vykreslenie editoru
		 *
		 * @param Void
		 * @return Void
		 */
		protected function Editor()
		{
			/* Obsah, ktory sa ma nahradit */
			$Editor = new \Vendor\Editor\Editor();

			if ((strpos($this->content, self::EDITOR) > 0))
			{
				/* Vymazanie znacky v sablone */
				$this->content = str_replace(self::EDITOR, "", $this->content);
				/* Nahradenie znacky v hlavnej sablone */
				$this->content = str_replace(self::BODY, $Editor->ckeditor() . "\n</body>", $this->content);
			}
		}

		/***
		 * Javascript
		 *
		 * @param Void
		 * @return Void
		 */
		protected function Javascript()
		{
      $join_delimiter = "','";
      $javascript_string = "";

      if (is_object($this->registry->javascript) &&
          is_array ($functions = $this->registry->javascript->getFunction()))
      {
        $javascript_string .= "\n  <script type=\"text/javascript\">\n";
        // load functions saved in registry
        foreach ($functions as $function => $parameters) {
          // start with name of function
          $javascript_string .= "    " . $function . "('";
          // check if function has parameters
          if (is_array($params = $parameters->getParameters())) {
            // load parameters of function
            foreach ($params as $parameter) {
              // connect parameters with function
              $javascript_string .= $parameter . $join_delimiter;
            }
          }
          // trim last 3 chars $join_delimiter
          $javascript_string = substr($javascript_string, 0, strlen($javascript_string) - strlen($join_delimiter)) . "');\n";
        }
        // replace </body> => <script>...functions(parameters)...</script>
        $javascript_string .= "  </script>\n</body>";
      }
      if ($javascript_string != "") {
        // Nahradenie znacky javascriptu javascriptom
        $this->content = str_replace(self::BODY, $javascript_string, $this->content);
      }
    }

		/***
		 * Chyby - errors
		 *
		 * @param Void
		 * @return Void
		 */
		protected function Errors()
		{
			/**
			** Kontrola chyb pri nacitavani zakladnych nastaveni
			*/
			if ($this->registry->errors->log !== FALSE)
			{
				/* Nahradenie {include errormessage} chybovou spravou */
				$this->content = str_replace(self::ERROR, $this->registry->errors->log, $this->content);
				$this->Title();
				$this->render();

				exit(0);
			}

			if (!empty($this->registry->errors->data))
			{
				/* Nahradenie {include errormessage} chybovymi spravami */
				foreach($this->registry->errors->data as $key => $error)
				{
					$this->content = str_replace(self::ERROR, $error, $this->content);
				}
			} else {
				/* Odstranenie prazdnym retazcom {include errormessage} */
				$this->content = str_replace(self::ERROR, "", $this->content);
			}
		}

	}


