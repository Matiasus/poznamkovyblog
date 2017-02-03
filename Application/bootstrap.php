<?php

  // Konfiguracia
  $configFile = __DIR__ . "/Config/settings.class.php";

  // Spracovanie konfiguracnych nastaveni
  try {
    if (!file_exists($configFile) || 
        !is_readable($configFile)) 
    {
      // throw to exception
      throw new Exception('File <b>'.$configFile.'</b> not exists!');
    }

    // Volanie konfiguracneho suboru
    require_once($configFile);
    // Nacitanie konfiguracneho suboru
    Application\Config\Settings::Load(__DIR__ . "/Config/config.php.ini");

    // Separator
    define('DS', DIRECTORY_SEPARATOR);
    // Define Mysql access
    define ('HOST', Application\Config\Settings::$Detail->Mysql->Host);
    define ('NAME', Application\Config\Settings::$Detail->Mysql->Name);
    define ('DBN' , Application\Config\Settings::$Detail->Mysql->Database);
    define ('PAS' , Application\Config\Settings::$Detail->Mysql->Password);
    define ('DSN' , Application\Config\Settings::$Detail->Mysql->DSNparameter);
    // Zadefinovanie cesty k domovskemu adresaru projektu
    define ('ROOT_DIR', dirname(dirname(__FILE__)) . DS);
    // Zadefinovanie cesty k adresaru obsahujuci autoload.php
    define ('BOOT_DIR', dirname(__FILE__) . DS);

    // Volanie autoloaderu - zabezpeci volanie triedy na zaklade mena triedy bez vkladania include alebo require 
    require_once(BOOT_DIR . 'Config/autoloader.class.php');

    // Vytvorenie objektu triedy Autoloader 
    $autoload = new Autoloader();

    // Vytvorenie registra
    $registry = new \Vendor\Registry\Registry;

    // Vytvorenie hlasenia a spracovania chyb
    $registry->errors = '\Vendor\Errors\Errors';
    // Vytvorenie objektu Session na ukladanie docasnych flash sprav
    $registry->session = '\Vendor\Session\Session';
    // Vytvorenie objektu Cookie na ukladanie docasnych flash sprav
    $registry->cookie = '\Vendor\Cookie\Cookie';
	  // Vytvorenie objektu Cookie na ukladanie docasnych flash sprav
	  $registry->javascript = '\Vendor\Javascript\Javascript';
    // Vytvorenie objektu Mysql pre pracu s databazou
    $registry->mysql = '\Vendor\Mysql\Mysql';
    // Vytvorenie spojenia podla dsn
    $registry->mysql->connect('connection'
               , DSN
               , NAME
               , PAS
               , array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
               );

    // Vytvorenie objektu routovania
    $registry->route = '\Vendor\Route\Route';
    // Rozklad url adrsu do modulov (ak su pritomne), kontorlera, pohladu a parametrov (ak su pritomne)
    $registry->route->getExplodedUrl();

    // Vytvorenie objektu uzivatela
    $registry->user = '\Vendor\User\User';

    //Vytvorenie objektu kontrolera
    $registry->controller = '\Vendor\Controller\Controller';

    // Vytvorenie objektu sablony
    $registry->template = '\Vendor\Template\Template';

  } 
  catch(\Exception $exception) {
    // nacitanie kniznice, kedze autoruter nefunguje
    require_once (dirname(dirname(__FILE__))."/Vendor/Errors/template.class.php");
    // sablona zobrazenia chyb
    $template = new \Vendor\Errors\Template;
    // nastvenie titulu
    $template->set('title', 'ERROR');
    // nastavenie obsahu
    $template->set('body', $exception->getMessage());
    // vypis
    $template->render();
  }

