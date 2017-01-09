<?php

	namespace Vendor\Agent;

	class Agent {

		/** @var Array - operacne systemy */
		private $systems = array('/windows nt 10/i'      =>  'Windows 10',
                             '/windows nt 6.3/i'     =>  'Windows 8.1',
                             '/windows nt 6.2/i'     =>  'Windows 8',
                             '/windows nt 6.1/i'     =>  'Windows 7',
                             '/windows nt 6.0/i'     =>  'Windows Vista',
                             '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                             '/windows nt 5.1/i'     =>  'Windows XP',
                             '/windows xp/i'         =>  'Windows XP',
                             '/windows nt 5.0/i'     =>  'Windows 2000',
                             '/windows me/i'         =>  'Windows ME',
                             '/win98/i'              =>  'Windows 98',
                             '/win95/i'              =>  'Windows 95',
                             '/win16/i'              =>  'Windows 3.11',
                             '/macintosh|mac os x/i' =>  'Mac OS X',
                             '/mac_powerpc/i'        =>  'Mac OS 9',
                             '/linux/i'              =>  'Linux',
                             '/ubuntu/i'             =>  'Ubuntu',
                             '/iphone/i'             =>  'iPhone',
                             '/ipod/i'               =>  'iPod',
                             '/ipad/i'               =>  'iPad',
                             '/android/i'            =>  'Android',
                             '/blackberry/i'         =>  'BlackBerry',
                             '/webos/i'              =>  'Mobile');
		 
		/** @var Array - prehliadace */
		private $browsers = array('/msie/i'      => 'Internet Explorer',
                              '/trident/i'   => 'Internet Explorer',
                              '/firefox/i'   => 'Firefox',
                              '/safari/i'    => 'Safari',
                              '/chromium/i'  => 'Chromium',
                              '/chrome/i'    => 'Chrome',
                              '/edge/i'      => 'Edge',
                              '/opera/i'     => 'Opera',
                              '/netscape/i'  => 'Netscape',
                              '/maxthon/i'   => 'Maxthon',
                              '/konqueror/i' => 'Konqueror',
                              '/mobile/i'    => 'Handheld Browser');

		/** @var String - user agent */
		private $user_agents = array();

    /***
     * Konstruktor vytvorenia spojenia s registrom
     *
     * @param Array - $_SERVER['HTTP_USER_AGENT']
     * @return Void
     */
    public function __construct() 
    {
    }

    /***
     * 
     *
     * @param String
     * @return Void | Boolean
     */
		public function setUserAgent($user_agent)
    {
      if (!is_scalar($user_agent)) {
        return false;
      }
      // agent nastaveny
      $this->user_agent = $user_agent;
      // agent nastaveny
      return true;
		}

    /***
     * 
     *
     * @param Void
     * @return String | Boolean
     */
		public function getBrowser()
    {
      if (!empty($this->user_agent)) {
        return $this->execute($this->browsers);
      }

      return false;
		}

    /***
     * 
     *
     * @param Void
     * @return String | Boolean
     */
		public function getSystem()
    {
      if (!empty($this->user_agent)) {
        return $this->execute($this->systems);
      }

      return false;
		}

    /***
     * 
     *
     * @param Void
     * @return Boolean
     */
		public function execute($systems_or_browsers)
    {
      if (empty($systems_or_browsers) ||
          !is_array($systems_or_browsers))
      {
        return false; 
      }
      // inicializacia
      $find_match = '';
      // prechod cez prvky pola
      foreach ($systems_or_browsers as $pattern => $system_or_browser) {
        // ak je najdena zhoda
        if (1 === preg_match($pattern, $this->user_agent, $match)) {
          // nahradenie agenta systemom alebo browserom
          $find_match = $system_or_browser;
        }
      }
      // String browser alebo system
      return $find_match;
		}

  }
