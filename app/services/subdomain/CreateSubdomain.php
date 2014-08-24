<?php

namespace App\Services\Subdomain;

//use  Illuminate\Database;

class CreateSubdomain extends BaseSubdomain
{

	/**
	 * Create Subdomain 
	 * Create DB for subdomain user.
	 */

	private $sql;
	private $dbhost;
	private $pdo;
	private $credential;
	private $postNameDb;


	public function __construct($credential)
    {
    	if (is_string($credential)) 
    	{
    		parent::__construct($credential);
        } 
        elseif (is_array($credential)) 
        {
        	$this->postNameDb 	= '.mlab.com';
	        $this->credential 	= $credential;
	        $this->setDbName();
	        $this->setDbUser();
	        $this->setDbHost();
	        $this->setDbPassword();
	        $this->setPdo();
        }
    }

	public function setDbName()
	{
		$this->dbname = $this->credential['db'].$this->postNameDb;
	}
	public function setDbUser()
	{
		$this->dbuser = $this->credential['username'];
	}
	public function getDbHost()
	{
		return $this->dbhost;
	}
	public function setDbHost()
	{
		$this->dbhost = "'".$this->dbuser."'@'localhost'";
	}
	public function setDbPassword()
	{		
		return $this->dbpassword = $this->credential['password'];
	}
	public function getPdo()
	{		
		return $this->pdo;
	}
	public function setPdo()
	{	
		$this->pdo = \DB::connection()->getPdo();
	}
	public function createNewDb()
	{
		try {
			// Create new database & grant privileges for new mySql user
			$this->sql="
			SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
			SET time_zone = '+00:00';
			CREATE DATABASE IF NOT EXISTS 
			`".$this->dbname."` 
			DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
			$this->pdo->exec( $this->sql );

			// Create mySql user
			$this->sql = "GRANT USAGE ON *.* TO ".$this->dbhost." IDENTIFIED BY '".$this->dbpassword."';";
			$this->pdo->exec( $this->sql );

			// Grant privileges for new mySql user
			$this->sql = "GRANT ALL PRIVILEGES ON `".$this->dbname."`.* TO ".$this->dbhost. " IDENTIFIED BY '".$this->dbpassword."'";
			$this->pdo->exec( $this->sql );
			return true;
		}
		catch(PDOException $e)
		{

			$userMessage = trapError($e);

			return false;
		}
	}
	
	public function createTablesDb()
	{
		try {
			// Create tables on new database
			$this->sql = "
			USE `".$this->dbname."`;

			CREATE TABLE IF NOT EXISTS `groups` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`permissions` text COLLATE utf8_unicode_ci,
				`created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
				`updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
				PRIMARY KEY (`id`),
				UNIQUE KEY `groups_name_unique` (`name`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

			CREATE TABLE IF NOT EXISTS `migrations` (
				`migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`batch` int(11) NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

			CREATE TABLE IF NOT EXISTS `throttle` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`user_id` int(10) unsigned DEFAULT NULL,
				`ip_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				`attempts` int(11) NOT NULL DEFAULT '0',
				`suspended` tinyint(1) NOT NULL DEFAULT '0',
				`banned` tinyint(1) NOT NULL DEFAULT '0',
				`last_attempt_at` timestamp NULL DEFAULT NULL,
				`suspended_at` timestamp NULL DEFAULT NULL,
				`banned_at` timestamp NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `throttle_user_id_index` (`user_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

			CREATE TABLE IF NOT EXISTS `users` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`permissions` text COLLATE utf8_unicode_ci,
				`activated` tinyint(1) NOT NULL DEFAULT '0',
				`activation_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				`activated_at` timestamp NULL DEFAULT NULL,
				`last_login` timestamp NULL DEFAULT NULL,
				`persist_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				`reset_password_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				`first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				`last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				`created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
				`updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
				PRIMARY KEY (`id`),
				UNIQUE KEY `users_email_unique` (`email`),
				KEY `users_activation_code_index` (`activation_code`),
				KEY `users_reset_password_code_index` (`reset_password_code`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=31 ;

			CREATE TABLE IF NOT EXISTS `users_groups` (
				`user_id` int(10) unsigned NOT NULL,
				`group_id` int(10) unsigned NOT NULL,
				PRIMARY KEY (`user_id`,`group_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			";
			$this->pdo->exec( $this->sql );
			return true;
		}
		catch(PDOException $pdo_error)
		{

			$userMessage = trapError($e);
			
			return false;
		}
	}

	private function trapError($e)
	{
		Log::error( 'Failed to execute query:\n' . $this->$sql . '\nWith Error:\n' . $e->getMessage());

		if ( App::Environment('local') )
		{
			$message = explode(' ', $e->getMessage());
			$dbCode = rtrim($message[1], ']');
			$dbCode = trim($dbCode, '[');

			// codes specific to MySQL
			switch ($dbCode) {
				case 1049:
				$userMessage = 'Unknown database - probably config error:';
				break;
				case 2002:
				$userMessage = 'DATABASE IS DOWN:';
				break;
				default:
				$userMessage = 'Untrapped Error:';
				break;
			}

			$userMessage = $userMessage . '<br>' . $e->getMessage(); 

		}
		else
		{
			$userMessage = 'We are experiencing a bad bad problem. We are sorry for the inconvenience!'; 
			
		}
		return $userMessage;
	}
}