<?php

	namespace Vendor\Interfaces;

	/*
	** Trieda pracujuca s databazou
	*/
	interface IMysql 
	{
		public function connect($dsn, $user, $password, $options = array());
    public function __deconstruct();
	}

?>
