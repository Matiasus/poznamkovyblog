<?php 

	namespace Vendor\Notification;

	/**
	** Trieda pracujuca s posielanim notifikacii, 
	** napr. prostrednictvom emailov
	*/
	class Notification {

		const FROM    = "From: Infoservis";
		const SUBJECT = "Registrácia na Infoservis.com";	
		const CONTROLLERVIEW = "/form/aktivacia";

		/***
		** Objekt registru
		*/
		private $registry;

		/***
		** Konstruktor databázového objektu - ulozenie do premennej registry odkaz na register
		**
		** @parameter Registry objekt
		** @return Void
		*/
		public function __construct( \Vendor\Registry\Registry $registry )
		{
			$this->registry = $registry;
		}

		/***
		** Spracovanie udajov potrebnych na odoslanie notifikacie emailom
		**
		** @parameter Details Array
		** @return Details Array - pole obsahujuce: Komu, Predmet, Spravu, Od koho
		*/
		public function Preprocessing( $details = array() )
		{
			/***
			** Vylistovanie pola
			*/
			list($to, $nick, $password) = func_get_args();

			/***
			** Zhasovanie prihlasovacieho mena (nick) a registracnych udajov
			*/			
			$code = $this->registry->user->hashpassword($to . $nick . $password);

			/***
			** Validacna adresa vytvorena zo:
			** => zakladnej adresy
			** => kontrolera "acivate"
			** => zahashovany nick, odpada uprava url adresy + uprava pred porovnanim s databazou
			** => zahashovane prihlasovacie udaje 
			*/
			$addr = $this->registry->route->getModule() . self::CONTROLLERVIEW;
			$link = $this->registry->route->getUrl() . $addr . DS . $code;

			/***
			** Sprava pre adresata
			*/
			$message  = "Vitajte na Poznámkovom blogu,<br/><br/>ďakujeme Vám za registráciu. Zároveň pevne veríme, že sa Vám poznámkový blog bude páčiť.<br/>";
			$message .= "Účet je potrebné aktivovať kliknutím na následujúci link:<br/>";
			$message .= $link . "<br/><br/>";
			$message .= "Meno: " . $nick . "<br/>";
			$message .= "Heslo: " . $password . "<br/>Emailová adresa: " . $to . "<br/><br/>";
			$message .= "Váš tým poznamkovyblog.com";

			$headers  = "Content-Type: text/html; charset = \"UTF-8\";\n";
			$headers .= "Reply-to: Mato Hrinko <mato.hrinko@gmail.com>\r\n";
			$headers .= "Return-path: Mato Hrinko <mato.hrinko@gmail.com>\r\n";
			$headers .= 'From: Poznamkovyblog <poznamkovyblog@srv4.endora.cz>' . "\r\n";
			$headers .= 'Cc: Poznamkovyblog <poznamkovyblog@srv4.endora.cz>' . "\r\n";
			$headers .= 'Bcc: Poznamkovyblog <poznamkovyblog@srv4.endora.cz>' . "\r\n";

			return array($to, self::SUBJECT, $message, $headers, $code);

		}

		/***
		** Konstruktor databázového objektu - ulozenie do premennej registry odkaz na register
		**
		** @param User array
		** @return Bool
		*/
		public function Email($parameters = array())
		{
			/***
			** Triada na odosielanie emailov
			** @var Array $to, $subject, $message, $headers
			*/
			$email = new \Vendor\Mailer\Mailer($parameters);

			/* Odosielanie prostrednictvom mail funkcie */
			if ( $email->send(\Vendor\Mailer\Mailer::MAIL) !== FALSE )
			{
				/***
				** V pripade uspesneho odoslania mailu presmerovanie na domovsku stranku
				** a vypis flash spravy
				*/
				$this->registry->session->set("flash", "Pre dokončenie úspešnej registrácie kliknite na validačný kód odoslaný na Vašu emailovú adresu!", false);
				$this->registry->route->redirect();

				return TRUE;
			}
			else
			{
				/***
				** V pripade neuspesneho odoslania mailu presmerovanie na domovsku stranku
				** a vypis flash spravy
				*/
				$this->registry->session->set("flash", "Email neodoslany!!!", false);
				$this->registry->route->redirect();

				return FALSE;		
			}
		}
	}
