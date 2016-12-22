<?php 

	namespace Vendor\Interfaces;

	/**
	** Trieda pracujuca s posielanim emailov
	*/
	interface iNotification
	{
		const FROM    = "From: Infoservis";
		const SUBJECT = "Registrácia na Infoservis.com";

		public function Email ($details = array());
		public function Preprocessing ($detials = array());
	}
