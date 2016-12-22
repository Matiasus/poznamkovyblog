<?php

	namespace Vendor\Form;

	class Input extends \Vendor\Form\Form {

		/*
		** Zadefinovanie premennych, pritomnych aj triedach v priecinku form
		*/
		public $id;
		public $rows;
		public $cols;
		public $name;
		public $label;
		public $value;
    public $maxlength;

		public $content = '';
		public $required = '';

		/*
		** Konstruktor triedy
		*/
		public function __construct(){

		}

		/***
		 * Nastavenie vyzadovaneho vstupneho pola pre input type={text; password; textarea} 
		 * => nahradzuje obsah premennej $this->content volanim funkcie fillContent()
		 * 
		 * @param void
		 * @return void
		 */
		public function setRequired()
    {
			$this->required = ' required';
			$this->fillContent();

			self::$temporary[count(self::$temporary)-1] = $this->content;
		}

		/*
		** Vracia obsah premennej $this->content
		** @parameter void
		** @return String $this->content
		*/
		public function getContent()
    {
			return $this->content;
		}

	}

