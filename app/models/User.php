<?php
use Illuminate\Database\Eloquent\Model as Eloquent;
use Cartalyst\Sentry\Users\Eloquent\User as SentryModel;

class User extends SentryModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	public function subdomains()
	{
		return $this->hasMany('Subdomain');
	}

}
