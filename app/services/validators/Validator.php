<?php

namespace App\Services\Validators;

abstract class Validator {
	
	protected $data;
	public $errors;
	public static $rules;

	public function __construct($data = null)
	{
		$this->data = $data ?: \Input::all();
	}

	public function isPostRequest()
	{
		return \Input::server("REQUEST_METHOD") == "POST";
	}

	public function passes()
	{
		
			$validation = \Validator::make($this->data, static::$rules);
			if ($validation->passes()) return true;
			$this->errors = $validation->messages();
		
		return false;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function setErrors() 
	{
		$this->errors = null;
	}
}