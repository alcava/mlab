<?php

namespace App\Services\Validators;

class RegisterUserValidator extends Validator
{

	public static $rules = array(
		"email" 	=> "required|email",
		"firstname" => "required",
		"lastname" 	=> "required",
		"subdomain"	=> "required|unique:subdomains,db|regex:/^[a-z1-9-]+$/",
		"password" 	=> "required|min:6"
	);

	public function getUserData()
	{

		$emailinput = strtolower(\Input::get("email"));
		$nameinput = \Input::get("firstname");
		$lastnameinput = \Input::get("lastname");
		$subdomaininput = strtolower(\Input::get("subdomain"));
		$passwordinput = \Input::get("password");
		
		$fieldemail = 'email';
		$fieldfirstname = 'first_name';
		$fieldlastname = 'last_name';
		$fieldsubdomain = 'db';
		$fieldpassword = "password";
		
		$arrayData[0] = array(
			$fieldemail => $emailinput,
			$fieldpassword => $passwordinput,
			$fieldfirstname => $nameinput,
			$fieldlastname => $lastnameinput,
		);
		$arrayData[1]= array(
			$fieldsubdomain => $subdomaininput
		);	

		return $arrayData;
			
	}

	
} 