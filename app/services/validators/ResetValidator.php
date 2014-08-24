<?php

namespace App\Services\Validators;

class ResetValidator extends Validator
{

	public static $rules = array(
		"email" 				=> "required|email",
		"password" 				=> "required|min:6",
		"password_confirmation" => "same:password",
		"resetCode"				=> "exists:users,reset_password_code"
	);

	public function getResetCredentials()
	{
		return \Input::all();

	/*	return \Input::only(
				"email",
				"password",
				"password_confirmation"
				"resetCode");
		*/		
	}
	
	/*public function resetPassword($credentials)
	{
		
		
		return \Password::reset($credentials, function($user, $pass) {
			$user->password = \Hash::make($pass);
			$user->save();
		});
		
	}*/
} 