<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Authentication Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines are used during authentication for various
	| messages that we need to display to the user. You are free to modify
	| these language lines according to your application's requirements.
	|
	*/

	'failed'   => 'Uw gebruikersnaam of wachtwoord is onjuist',
	'throttle' => 'Te veel login pogingen. U kunt het opniew proberen over :seconds seconde(n).',

	'register' => [
		'form' => [
			'header'                => 'Registreren',
			'e-mail'                => 'E-mailadres',
			'first_name'            => 'Voornaam',
			'last_name'             => 'Achternaam',
			'password'              => 'Wachtwoord',
			'password_confirmation' => 'Wachtwoord (bevestigen)',
			'postal_code'           => 'Postcode',
			'number'                => 'Huisnummer',
			'street'                => 'Straat',
			'city'                  => 'Plaats',
			'phone_number'          => 'Telefoonnummer',
			'button'                => 'Registreren',
			'message'               => [
				'success'           => 'Bedankt. We hebben een e-mail met een bevestigingslink naar u toegestuurd om uw registratie te voltooien.'	,
			],
		],
	],
	'login' => [
		'form' => [
			'header'                => 'Inloggen',
			'e-mail'                => 'E-mailadres',
			'password'              => 'Wachtwoord',
			'remember_me'           => 'Onthouden',
			'button'                => 'Login',
			'forgot_password'       => 'Wachtwoord vergeten?',
		],
	],
	'confirm' => [
		'success' => 'Uw account is bevestigd. U kunt nu inloggen met uw gebruikersnaam en wachtwoord.',
		'error' => 'Uw bevestigingslink is ongeldig. Is uw account wellicht al bevestigd?',
	],

];
