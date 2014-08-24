<?php

namespace App\Services\Validators;

class RequestValidator extends Validator
{

	public static $rules = array(
		"email" => "required|email",
	);

	public function getEmailAddress()
	{
		$email = \Input::get("email");
		
		return $email;
	}

	public function getPasswordRemindResponse()
	{
		return \Password::remind(\Input::only("email"));
	}
	
	public function isInvalidUser($response)
	{
		return $response === \Password::INVALID_USER;
	}
} 