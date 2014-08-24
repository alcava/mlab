<?php

namespace App\Services\Validators;

class LoginValidator extends Validator
{

	public static $rules = array(
		"username" => "required",
		"password" => "required|min:6"
	);

	public function getLoginCredentials()
	{
		$usernameinput = strtolower(\Input::get("username"));
		$passwordinput = \Input::get("password");
		
		
		$fieldusername = filter_var($usernameinput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
		$fieldpassword = "password";
		
		return [
			$fieldusername => $usernameinput,
			$fieldpassword => $passwordinput,
		];
	}

	
} 