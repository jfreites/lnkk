<?php

function connect_db() {

	try {

		$user     = 'homestead';
		$pass     = 'secret';

		return new PDO('mysql:host=localhost;dbname=lnkk', $user, $pass);

	} catch (PDOException $e) {
		print "¡Error!: " . $e->getMessage() . "<br/>";
    	die();
	}

}