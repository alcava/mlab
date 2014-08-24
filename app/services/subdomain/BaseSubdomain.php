<?php

namespace App\Services\Subdomain;

class BaseSubdomain
{
	/**
	 * Bae Subdomain
	 * Setup the Db name, Db user and DB password.
	 */

	protected  $dbname;
	protected  $dbuser;
	protected  $dbpassword;
	protected  $nameSubdomain;

	public function __construct($nameSubdomain)
    {
        $this->nameSubdomain 	= $nameSubdomain;
        $this->setBaseDbName();
        $this->setBaseDbUser();
        $this->createCryptPassword();
    }
    public function getDbName()
    {
    	return $this->dbname;
    }
	public function setBaseDbName()
	{
		$this->dbname = str_limit($this->nameSubdomain, 55, $end = '');
	}
	public function getDbUser()
    {
    	return $this->dbuser;
    }
	public function setBaseDbUser()
	{
		$this->dbuser = str_limit($this->nameSubdomain, 16, $end = '');
	}
	public function getDbPassword()
	{		
		return $this->dbpassword;
	}
	public function createCryptPassword()
	{
		$rndstr = str_random(12);
		$cost = 10;
		// Create a random salt
		$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
		// "$2a$" Means we're using the Blowfish algorithm.
		$salt = sprintf("$2a$%02d$", $cost) . $salt;
		// Hash the password with the salt
		$this->dbpassword = crypt($rndstr, $salt);
		
	}
	
} 