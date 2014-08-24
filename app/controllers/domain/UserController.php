<?php

use App\Services\Validators\LoginValidator;
use App\Services\Validators\ResetValidator;
use App\Services\Validators\RequestValidator;
use App\Services\Validators\RegisterUserValidator;
use App\Services\Subdomain\BaseSubdomain;
use App\Services\Subdomain\CreateSubdomain;


class DomainUserController extends Controller
{
	//protected $layout = 'domain';

	public function getIndexDomainAction()
	{
		return View::make("domain/index/index");
	}

	public function postIndexDomainAction()
	{

		$validation = new RegisterUserValidator;

		if($validation->passes())
		{

			$data = $validation->getUserData();

			try
			{
    			// Create the new one user
				$user = Sentry::register($data[0]);
				$setSubdomain = new createSubdomain((string)$data[1]['db']);

				// Insert db name, username, password, etc. in subdomain table
				$subdomain = new Subdomain();
				$subdomain->db = $setSubdomain->getDbName();
				$subdomain->username = $setSubdomain->getDbUser();
				$subdomain->password = $setSubdomain->getDbPassword();
				$subdomain->user_id = $user->id;
				$subdomain->save();

			    // Assign the group to the user
				$userGroup = Sentry::getGroupProvider()->findByName('User');
				$user->addGroup($userGroup);

				// Get Activation Code
				$activationCode = $user->getActivationCode();
				$subdomainId = $subdomain->id;
				$code = array(
					"ac"	=> $activationCode,
					"id"	=> $subdomainId
				);
				
				// Send email with activation code
				Mail::queue('emails.welcome', $code, function($message) use ($user)
				{
					$message->from('noreplay@mlab.com', 'Welcome to mlab.com');
					$message->to($user->email);
					$message->subject('Activation Code');
				});

				return Redirect::action('DomainUserController@getIndexDomainAction')
				->with("message", "User registered www.mlab.com/user/activate/".$code['ac']."/".$code['id']);
				
			}
			catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
			{
				return Redirect::action('DomainUserController@getIndexDomainAction')
				->with("error", "Login field is required.");
			}
			catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
			{
				return Redirect::action('DomainUserController@getIndexDomainAction')
				->with("error", "Password field is required.");
			}
			catch (Cartalyst\Sentry\Users\UserExistsException $e)
			{
				return Redirect::action('DomainUserController@getIndexDomainAction')
				->with("error", "User with this login already exists.");
			}
			catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
			{
				return Redirect::action('DomainUserController@getIndexDomainAction')
				->with("error", "Group was not found.");
			}

		}
		else
		{

			return Redirect::action('DomainUserController@getIndexDomainAction')
			->withInput()
			->withErrors($validation->getErrors());
		}
	}

	public function getActivateAction($activationCode = null, $subdomainId = null )
	{

		if (is_null($activationCode) || is_null($subdomainId))
		{ 
			return Redirect::action('DomainUserController@getIndexDomainAction')
			->with("error", 'activation code or id not found');
		} 
		try
		{
			$user = Sentry::findUserByActivationCode($activationCode);
			//$dbname = $user->subdomains()->where('id', '=', $subdomainId)->pluck('db');
			$credential = $user->subdomains()->where('id', '=', $subdomainId)->get()->toArray();

			//print_r($credential[0]['db']); die();
			// Attempt to activate the user

		    if ($user->attemptActivation($activationCode) && $credential)
		    {
		    	$newSubdomain = new createSubdomain($credential[0]);

		    	if($newSubdomain->createNewDb() && $newSubdomain->createTablesDb())
		    	{
			    	// Switch database
			    	Config::set('database.connections.mysql_tenant.database',$newSubdomain->getDbName());
	  				Config::set('database.connections.mysql_tenant.username',$newSubdomain->getDbUser());
	  				Config::set('database.connections.mysql_tenant.password',$newSubdomain->getDbPassword());
	  				$connection = DB::reconnect('mysql_tenant');
	  				DB::setDefaultConnection('mysql_tenant');

			    	// Create admin user on subdomain database
					Sentry::getUserProvider()->create(array(
			            'email'       => $user->email,
			            'password'    => $user->password,
			            'first_name'  => $user->first_name,
			            'last_name'   => $user->last_name,
			            'activated'   => 1,
			        ));
			 
			 		// Create admin group on subdomain database
			        Sentry::getGroupProvider()->create(array(
			            'name'        => 'Admin',
			            'permissions' => array('admin' => 1),
			        ));

			        // Create teacher group on subdomain database
			        Sentry::getGroupProvider()->create(array(
			            'name'        => 'Teacher',
			            'permissions' => array('teacher' => 1),
			        ));

			        // Create user group on subdomain database
			        Sentry::getGroupProvider()->create(array(
			            'name'        => 'User',
			            'permissions' => array('user' => 1),
			        ));
			 
			        // Assign user permissions on subdomain database
			        $adminUser  = Sentry::getUserProvider()->findByLogin($user->email);
			        $adminGroup = Sentry::getGroupProvider()->findByName('Admin');
			        $adminUser->addGroup($adminGroup);

			        DB::setDefaultConnection('mysql');

			        return Redirect::action('DomainUserController@getIndexDomainAction')
					->with("message", "User activate");
				}
		    }
		    else
		    {
		        return Redirect::action('DomainUserController@getIndexDomainAction')
				->with("error", "Activation failed");
		    }
		}
		catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
		{
			return Redirect::action('DomainUserController@getIndexDomainAction')
			->with("error", "Login field is required.");
		}
		catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
		{
			return Redirect::action('DomainUserController@getIndexDomainAction')
			->with("error", "Password field is required.");
		}
		catch (Cartalyst\Sentry\Users\UserExistsException $e)
		{
			return Redirect::action('DomainUserController@getIndexDomainAction')
			->with("error", "User with this login already exists.");
		}
		catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
		{
			return Redirect::action('DomainUserController@getIndexDomainAction')
			->with("error", "Group was not found.");
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			return Redirect::action('DomainUserController@getIndexDomainAction')
				->with("error", "User was not found.");
		}
		catch (Cartalyst\Sentry\Users\UserAlreadyActivatedException $e)
		{
			return Redirect::action('DomainUserController@getIndexDomainAction')
				->with("error", "User is already activated.");
		}
		
		
		return View::make("domain/index/activation")->with('resetCode', $resetCode);
	}

	public function getLoginAction()
	{
		//$test = Sentry::getCookie();
		return View::make("domain/login/login");
	}

	public function postLoginAction()
	{

		$validation = new LoginValidator;

		if($validation->passes())
		{

			$remember = (bool)\Input::get("remember", false);

			$credentials = $validation->getLoginCredentials();
			
			try
			{
				$user = Sentry::authenticate($credentials, $remember);
				$subdomain = array();
				$index = 0;
				foreach ($user->Subdomains()->get() as $sub)
				{
					$subdomain = array_add($subdomain, $index, $sub->db);
					$index++;
				}
				if(is_array($subdomain))
				{
					if(count($subdomain)==1)
					{
						return Redirect::action('SubdomainUserController@profileAction',$subdomain);
					}
					elseif(count($subdomain)>1)
					{
						return $view = View::make("domain/admin/chooseProfile")->with('subdomain',$subdomain);
					}
				}

				// Take group name for this user
				$group = $user->getGroups()->toArray()[0];

				if($user && strtolower($group['name']) == 'admin')
				{
					return Redirect::action('DomainUserController@profileAction');
				}
				elseif($user)
				{
					Sentry::logout();
				}
					
				return Redirect::route("user/login");

			}
			catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
			{
			    return Redirect::action('DomainUserController@getLoginAction')
				->with("error", "Login field is required.");
			}
			catch(Cartalyst\Sentry\Users\WrongPasswordException $e)
	        {
	            return Redirect::action('DomainUserController@getLoginAction')
				->with("error", "Credentials invalid 1");
	        }
	        catch(Cartalyst\Sentry\Users\UserNotFoundException $e)
	        {
	            return Redirect::action('DomainUserController@getLoginAction')
				->with("error", "Credentials invalid 2");
	        }
	        catch(Cartalyst\Sentry\Users\UserNotActivatedException $e)
	        {
	            return Redirect::action('DomainUserController@getLoginAction')
				->with("error", "You have to activate your account");
	        }
	        catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
			{
			    return Redirect::action('DomainUserController@getLoginAction')
				->with("error", "Sorry, you are suspended");
			}
	        catch(Cartalyst\Sentry\Throttling\UserBannedException $e)
	        {
	            return Redirect::action('DomainUserController@getLoginAction')
				->with("error", "Sorry, you are banned");
	        }
			/*
			catch(\Exception $e)
			{
				return Redirect::action('DomainUserController@getLoginAction',[$sub])
				->with("error", "Credentials invalid");

				// for more information
                // ->withErrors(array('login' => $e->getMessage()));
			}
			*/

		}
		else 
		{

			return Redirect::action('DomainUserController@getLoginAction')
			->withInput()
			->withErrors($validation->getErrors());

		}

	}

	public function getChooseProfileAction()
	{
		//View::share('subdomain', $subdomain);
		return View::make("domain/admin/chooseProfile",compact('subdomain'));
	}

	public function getRequestAction()
	{
		return View::make("domain/login/request");
	}

	public function postRequestAction()
	{

		$validation = new RequestValidator;

		if($validation->passes())
		{
			$email = $validation->getEmailAddress();
			try
			{
    			// Find the user using the user email address
				$user = Sentry::findUserByLogin($email);

    			// Get the password reset code
				$resetCode = $user->getResetPasswordCode();
				$data = array(
					"resetCode" => $resetCode
				);

				// Now I can send this code to my user via email.
				Mail::queue('emails.resetPassword', $data, function($message)
				{
					$message->from('noreplay@mlab.com', 'Reset Password');
					$message->to(Input::get('email'));
					$message->subject('Reset Password!');
				});
				return Redirect::action('DomainUserController@getLoginAction');    
			}
			catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
			{
				return Redirect::action('DomainUserController@getRequestAction')
				->withInput()
				->with("error", "user not found");
			}
				
		}
		else
		{
			return Redirect::action('DomainUserController@getRequestAction')
			->withInput()
			->withErrors($validation->getErrors());
		}
	}

	public function getResetAction($resetCode = null)
	{

		if (is_null($resetCode))
		{
			return Redirect::action('DomainUserController@getLoginAction')
			->with("error", 'reset code not found');
		} 

		try
		{
		    $user = Sentry::findUserByResetPasswordCode($resetCode);
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			return Redirect::action('DomainUserController@getRequestAction')
				->withInput()
				->with("error", 'User was not found');
		}
		
		return View::make("domain/login/reset")->with('resetCode', $resetCode);
	}

	public function postResetAction($resetCode)
	{
		$validation = new ResetValidator;

		if($validation->passes())
		{
			$credentials = $validation->getResetCredentials();

			try
			{
				$user = Sentry::findUserByLogin($credentials['email']);

				if ($user->checkResetPasswordCode($credentials['resetCode']))
				{
	        		// Attempt to reset the user password
					if ($user->attemptResetPassword($credentials['resetCode'], $credentials['password']))
					{
						return Redirect::action('DomainUserController@getLoginAction');
					}
					else
					{
						return Redirect::action('DomainUserController@getRequestAction')
						->withInput()
						->with("error", "Reset password failed");
					}
				}
				else
				{
					return Redirect::action('DomainUserController@getRequestAction')
					->withInput()
					->with("error", "The provided password reset code is Invalid");
				}
			}
			catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
			{
				return Redirect::action('DomainUserController@getRequestAction')
				->withInput()
				->with("error", 'user not found');
			}
		}
		else
		{
			return Redirect::action('DomainUserController@getResetAction', [$resetCode])
				->withInput()
				->withErrors($validation->getErrors());
		}

	}

	public function profileAction()
	{
		$group = Sentry::findGroupByName('User');
		$users = Sentry::findAllUsersInGroup($group);
		return View::make("domain/admin/profile")->with('users',$users);
	}

	public function logoutAction()
	{
		Sentry::logout();

		return Redirect::route("user/login");
	}
	
}