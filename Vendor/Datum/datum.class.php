<?php

namespace Vendor\Datum;

class Datum extends \DateTime {

	const TIME_NOWDAY = "NOW";
	const TIME_FORMAT = "Y-m-d G:i:s";

	/** @var Objekt registru */
	private $registry;

	/** @var Aktualny cas	*/
	private $actualTime;

	/** @var Instancia triedy DateTime */
	private $instance;

	/***
	 * Konstruktor databázového objektu - ulozenie do premennej registry odkaz na register
	 *
	 * @param Registry objekt
	 * @return Void
	 */
	public function __construct( \Vendor\Registry\Registry $registry )
	{
		$this->registry = $registry;
		// Nazov triedy
		$this->instance = get_parent_class();
		// Terajsok - momentalny cas
		$this->actualTime = new $this->instance(self::TIME_NOWDAY);
	}

	/***
	 * Aktualny datum a cas
	 *
	 * @param Void
	 * @return Void
	 */
	public function getActualTime()
	{
		return $this->actualTime->format(self::TIME_FORMAT);
	}

	/***
	 * Porovnanie dvoch datumov, z ktorych jeden je terajsok
	 *
	 * @param String - datum
	 * @return Int 0, 1, -1
	 */
	public function difference($date)
	{
		// Pozadovany cas
		$newDatum = new $this->instance($date);
		// Ak je porovnanvany datum zhodny s momentalnym casom
		if ($newDatum == $this->actualTime)	{
			return 0;
		}
		// Ak je porovnanvany datum buducnostou
		if ($newDatum > $this->actualTime) {
			return 1;
		}
		// Ak je porovnanvany datum minulostou
		if ($newDatum < $this->actualTime) {
			return -1;
		}
	}

	/***
	 * Nastavenie casu do pozadovaneho formatu 
	 * citanie z #! config.php.ini !# zo sekcie @! Cookie !@
	 *
	 * @param Void
	 * @return String - format datumu
	 */
	public function getFutureTime()
	{
		// Naciatnie nastavenych hodnot expiracie zo suboru config.php.ini
		$expiration = \Application\Config\Settings::$Detail->Cookies->Expiration;
		// Inicializacia formatu - P ako period
		$format = "P";
		// Prechadzanie jednotlivych hodnot objektu expiracie
		foreach ($expiration as $key => $value) {
      // overi, ci sa jedna o hodinu
			if (strcmp($key, "Hours") === 0) {
				// Pred hodinami je vlozene T ako Time - odlisovaci znak pre oddelenie datumu od casu
				$format .= "T" . $value . substr($key, 0, 1 - strlen($key));
			}	else {
				$format .= $value . substr($key, 0, 1 - strlen($key));
			}
		}

		/***
		 * Vytvorenie datumu a casu expiracie pomocou triedy DateInterval 
		 * 
		 * @parameter to DateInterval($spec_interval) format napr
		 * "P1DT10H" - 1 den, 10 hodin
		 * "P2YT1M10S" - 2 roky, 1 minuta, 10 sekund
		 */
		$dateTime = $this->actualTime->add(new \DateInterval($format));

		return $dateTime->format(self::TIME_FORMAT);
	}

	/***
	 * Nastavenie casu do pozadovaneho formatu 
	 * citanie z #! config.php.ini !# zo sekcie @! Cookie !@
	 *
	 * @param Void
	 * @return String - format datumu
	 */
	public function getFutureTimeInSeconds()
	{
		// naciatnie nastavenych hodnot expiracie zo suboru config.php.ini
		$expiration = \Application\Config\Settings::$Detail->Cookies->Expiration;
    // nasobitelia
    $multiplier = array("Seconds" =>  1, 
                        "Minutes" => 60, 
                        "Hours"   => 60, 
                        "Days"    => 24, 
                        "Months"  => 30.417, 
                        "Years"   => 12);
    // hodnota v sekundach
    $secs = 0;
    // pomocny vypocet
    $temp = 1;
  
    foreach ($multiplier as $key => $value) {
      // osetrenie chyby - ak chyba premenna v config.php.ini
      if (!isset($expiration->$key)) {
        // report chyby       
        $this->registry->errors->log = "Does not find parameter <b>".$key."</b> in config file!";
        // return 
        return false;
      }
      // pomocny vypocet
      $temp = $temp * $value;
      // overenie, ci je nastavena hodnota
      if ($expiration->$key != 0) {
        // pripocitanie docasne uchovanej hodnoty
        $secs = $secs + $expiration->$key * $temp;
      } 
    }
    // hodnota v sekundach
    return $secs;
  }
}

