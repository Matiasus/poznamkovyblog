<?php

	namespace Vendor\Form;

	class Form {

		const GET = 'get', 
					POST = 'post';
		const ADDRESS = 'index.php';
		const REQUIRED = true, 
					UNREQUIRED = false;

		/*
		** Premenne obsahujuce defaultne nastavenia
		*/
		private $id;
		private $data;
		private $values;
		private $process;
		private $action = self::ADDRESS;
		private $method = self::POST;
		private $errors;

		private $text;
		private $email;
		private $submit;
		private $password;
		private $textarea;
		private $validation;

		private $table;

		private $inline = false;
		private $variables = array();

		public static $display = false;
		public static $temporary = array();

		/***
		 * @var Object \Vendor\Registry\Registry
		 */
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
		}

		/***
		 * Nastavenie akcie 
     *
		 * @param String adresa kam ma formular odosielat data
		 * @return void
		 */
		public function setAction($action, $parameter = false) 
    {
			if( $parameter === false ) {
				$this->action = $action;
			} else {
				$this->action = $action . "?do=" . $parameter;
			}
		}

		/***
		 * Nastavenie zobrazenia nazvu pola bud v riadku za sebou alebo pod sebou ako block
     *
		 * @param Bool 
		 * @return Void
		 */
		public function setInlineForm($display)
    {
			self::$display = (bool) $display;
		}

		/***
		 * Nastavenie zobrazenia nazvu pola bud v riadku za sebou alebo pod sebou ako block
     *
		 * @param Void 
		 * @return Boolean
		 */
		public function getDisplay()
    {
			return (bool) self::$display;
		}

		/***
		 * Nastavenie metody
     *
		 * @param String GET || POST (default)
		 * @return Void
		 */
		public function setMethod($method)
    {
			if (strcmp(strtolower($method), self::POST) === 0 || 
          strcmp(strtolower($method), self::GET)  === 0 )
			{
				$this->method = strtolower($method);
			}
			else
			{
				$this->method  = "";
				$this->registry->errors->form  = "<span class=\"error\">Chyba pri nastaveni metody <br/>";
				$this->registry->errors->form .= "(Mozna volba: \"post\" /default/ alebo \"get\")</span>)";
			}

		}

		/***
		 * Vytvorenie textoveho pola input
     *
		 * @param String - meno pola text (bez diakritiky a medzery)
		 * @param String - pomenovanie pola vlavo od textoveho pola (aj s diakritikou a medzerami)
		 * @param String - preddefinovana hodnota (aj s diakritikou a medzerami)
		 * @return Object \library\forms\supplement\inputText
		 */
		public function addText($name = false, $label = false, $value = false, $maxlength = false)
    {
			$this->text = new \Vendor\Form\Input\Text($name, $label, $value, $maxlength);
			self::$temporary[] = $this->text->getContent();

			return $this->text;
		}

		/***
		 * Vytvorenie textoveho pola input email
     *
		 * @param String - meno pola text (bez diakritiky a medzery)
		 * @param String - pomenovanie pola vlavo od textoveho pola (aj s diakritikou a medzerami)
		 * @param String - preddefinovana hodnota (aj s diakritikou a medzerami)
		 * @return Object \library\forms\supplement\inputText
		 */
		public function addEmail($name = false, $label = false, $value = false)
    {
			$this->email = new \Vendor\Form\Input\Email($name, $label, $value);
			self::$temporary[] = $this->email->getContent();

			return $this->email;
		}

		/***
		 * Vytvorenie hesloveho pola input
     *
		 * @param String - meno pola text (bez diakritiky a medzery)
		 * @param String - pomenovanie pola vlavo od textoveho pola (aj s diakritikou a medzerami)
		 * @param String - preddefinovana hodnota (aj s diakritikou a medzerami)
		 * @return Object \library\forms\supplement\inputPassword
		 */
		public function addPassword($name = false, $label = false, $value = false)
    {
			$this->password = new \Vendor\Form\Input\Password($name, $label, $value);
			self::$temporary[] = $this->password->getContent();

			return $this->password;
		}

		/***
		 * Vytvorenie textoveho pola textarea
		 * @parameter String $name => meno pola text (bez diakritiky a medzery)
		 * @parameter String $label => pomenovanie pola vlavo od textoveho pola (aj s diakritikou a medzerami)
		 * @parameter Int $rows => pocet riadkov
		 * @parameter Int $cols => pocet stlpcov
		 * @parameter String $value => preddefinovana hodnota (aj s diakritikou a medzerami)
		 * @return Object \library\forms\supplement\textarea
		 */
		public function addTextarea($name = false, $label = false, $rows = false, $cols = false, $id = false, $value = false)
    {
			$this->textarea = new \Vendor\Form\Input\Textarea($name , $label , $rows , $cols , $id, $value);
			self::$temporary[] = $this->textarea->getContent();

			return $this->textarea;
		}

		/***
		 * Vytvorenie checkboxu
     *
		 * @param String - meno pola text (bez diakritiky a medzery)
		 * @param String - pomenovanie pola vlavo od textoveho pola (aj s diakritikou a medzerami)
		 * @param String - preddefinovana hodnota (aj s diakritikou a medzerami)
		 * @return Object \library\forms\supplement\inputPassword
		 */
		public function addCheckbox($name = false, $value = false, $label = false, $check = false)
		{
			$this->checkbox = new \Vendor\Form\Input\Checkbox($name , $value, $label, $check);
			self::$temporary[] = $this->checkbox->getContent();

			return $this->checkbox;
		}

		/***
		 * Vytvorenie potvrdzovacieho tlacitka input submit
     *
		 * @param String - meno pola text (bez diakritiky a medzery)
		 * @param String - preddefinovana hodnota (aj s diakritikou a medzerami)
		 * @return Void
		 */
		public function addSubmit($name = false, $value = false, $label = false)
		{
			$this->submit = new \Vendor\Form\Input\Submit( $name , $value );
			self::$temporary[] = $this->submit->getContent();
		}

		/***
		 * Vytvorenie formulara funkcia volana v template
     *
		 * @param Void
		 * @return Void
		 */
		public function create()
		{
			$this->values  = "\n\t<form action='".$this->action."' method='".$this->method."' id='".$this->id."'>";
			$this->values .= (($this->getDisplay() === false ) ? "\n\t  <table class='".$this->id."'>":"");

			// Rozklada pole $temporary na retazec, ktory nahradzuje obsah
			$this->values .= implode("", self::$temporary);
			$this->values .= $this->errors;

			$this->values .= (( $this->getDisplay() === false ) ? "\n\t  </table>" : "");
			$this->values .= "\n\t</form>";

			// Potrebne vyprazdnit pole, aby nedochadzalo k pamataniu z predosleho formulara
			// v pripade viacerych formularov v jednej sablone
			self::$temporary = array();

			return $this->values;
		}

		/***
		 * Funkcia vracajuca obsah, ktory ma byt nahradeny v sablone {formular meno formulara}
     *
		 * @param String - id formulara
		 * @return String - html, ktory bude nahradeny {formular meno_formulara}
		 */
		public function getFormContent( $id = false )
    {
			$this->id = $id;
			$form = $this->create();

			return $form;
		}

		/***
		 * Validacia zadanych nazvov jednotlivych prvkov formulara
		 * ci sa zhoduju s nazvami stlpcov prislusnej tabulky
     *
		 * @param void
		 * @return Bool
		 */		
		public function succeedSend()
    {
			if (isset($_POST) && 
          !empty($_POST))
			{
        // overenie posielanych dat
				$this->validation = call_user_func(array($this, "validation"));
        // navratova hodnota
				return $this->validation;
			}

		}

		/***
		 * Overuje, ci sa nastavene $_POSTy zhoduju s nazvami stlpcov v tabulke
     *
		 * @param Void
		 * @return Bool
		 */		
		private function validation($allerrors = false)
    {
      // inicializacia chybovej hlasky
			$this->registry->errors->sql = "";
			// Prechod jednotlivych prvkov odoslanych metodou POST
			foreach($_POST as $key => $value)	{
				// Overenie existencie nayvu stlpca v MySQL tabulke databazy
				if ($this->registry->mysql->existenceOfColumn($key) !== TRUE)
				{
					// Osetrenie submitu
					if (!empty($this->submit->name)) {
						if (strcmp($key, $this->submit->name) === 0) {
							continue;
						}
					}
					// Osetrenie checkboxu
					if (!empty($this->checkbox->name)) {
						if (strcmp($key, $this->checkbox->name) === 0) {
							continue;
						}
					}
					// Vypis flash spravy
					$this->registry
							 ->session
							 ->set("flash", "Stlpec <strong>" . $key . "</strong> v tabulke <b>" . $this->registry->mysql->getTable() . "</b> neexistuje!<br/>", false);
					// Ak najde stlpec, ktory sa v tabulke nenachadza vratti FALSE
					return FALSE;
				}
				else
				{
					// Zapis udajov do pola data bez submit hodnoty
					$this->data[$key] = $value;
				}
			}

			return TRUE;
		}

		/***
		 * Ziskanie dat
     *
		 * @param Void
		 * @return Array
		 */	
		public function getData()
    {
			return $this->data;
		}

	}
