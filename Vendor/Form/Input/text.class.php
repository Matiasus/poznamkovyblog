<?php

	namespace Vendor\Form\Input;

	class Text extends \Vendor\Form\Input {

		/***
		 * Konstruktor triedy, nastavi premenne a zavola funkciu fillContent() na naplnenie obsahu
		 * 
		 * @param String $name => meno pola text (bez diakritiky a medzery)
		 * @param String $label => pomenovanie pola vlavo od textoveho pola (aj s diakritikou a medzerami)
		 * @param String $value => preddefinovana hodnota (aj s diakritikou a medzerami)
		 * @return Void
		 */
		public function __construct($name = false, $label = false, $value = false, $maxlength = false)
    {
			$this->name  = $name;
			$this->label = $label;
			$this->value = $value;
      $this->maxlength = $maxlength;

			$this->fillContent();
		}

		/***
		 * Plni obsahom premennu $this->content
     *
		 * @param void
		 * @return void
		 */
		public function fillContent()
    {
			$this->content  = ($this->getDisplay() === false) ? "\n\t   <tr><td>" : "\n\t   <label for='id-".strtolower( $this->name )."'>" ;
			$this->content .= $this->label . (($this->required != '') ? '*' : '');
			$this->content .= (($this->getDisplay() === false) ? "</td><td>" : "</label><br/>" );
			$this->content .= "\n\t    <input type='text' ";
      $this->content .= ($this->maxlength !== false) ? "maxlength='".$this->maxlength."' " : "";
			$this->content .= " name='".$this->name."' id='id-".strtolower($this->name)."'";
			$this->content .= " value='" . $this->value  . "'" . $this->required . "/>";
			$this->content .= (($this->getDisplay() === false) ? "</td></tr>" : "<br/>");
		}

	}

