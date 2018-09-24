<?php

return [
	'confirm_account' => [
		'salutation' => 'Beste :first_name :last_name,',
		'text' => 'Je hebt een account aangevraagd op :home_url. Je kunt je account bevestigen via de volgende link:<br><br>:confirm_url',
		'signature' => '<br>Met vriendelijke groet,<br>:app_name',
	],
	'reset_password' => [
		'why' => 'You are receiving this email because we received a password reset request for your account.',
		'action' => 'Reset Password',
		'not_requested' => 'If you did not request a password reset, no further action is required.',
	],
    'user-created' => [
        'why' => 'U heeft deze e-mail ontvangen omdat er account voor uw is aangemaakt',
        'action' => 'Aan de slag',
        'signature' => '<br>Met vriendelijke groet,<br>:app_name',
    ]

];