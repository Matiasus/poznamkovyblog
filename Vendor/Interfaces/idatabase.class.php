<?php

	namespace Vendor\Interfaces;

	/*
	** Trieda pracujuca s databazou
	*/
	interface IDatabase 
	{
		public function connect();
    public function disconnect();
	}

?>
