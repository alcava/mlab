<?php

use App\Services\Validators\LoginValidator;
use App\Services\Validators\ResetValidator;
use App\Services\Validators\RequestValidator;

class SubdomainUserController extends Controller
{
	public function getLoginAction()
	{
		//$test = Sentry::getCookie();
		return View::make("subdomain/login/login");
	}

	public function postLoginAction($sub)
	{

		$validation = new LoginValidator;

		if($validation->passes())
		{

			$remember = (bool)\Input::get("remember", false);

			$credentials = $validation->getLoginCredentials();
			
			try
			{
				$user = Sentry::authenticate($credentials, $remember);
				if ($user) {
					return Redirect::route("user/profile");
				}
			}
			catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
			{
			    return Redirect::action('SubdomainUserController@getLoginAction',[$sub])
				->with("error", "Login field is required.");
			}
			catch(Cartalyst\Sentry\Users\WrongPasswordException $e)
	        {
	            return Redirect::action('SubdomainUserController@getLoginAction',[$sub])
				->with("error", "Credentials invalid 1");
	        }
	        catch(Cartalyst\Sentry\Users\UserNotFoundException $e)
	        {
	            return Redirect::action('SubdomainUserController@getLoginAction',[$sub])
				->with("error", "Credentials invalid 2");
	        }
	        catch(Cartalyst\Sentry\Users\UserNotActivatedException $e)
	        {
	            return Redirect::action('SubdomainUserController@getLoginAction',[$sub])
				->with("error", "You have to activate your account");
	        }
	        catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
			{
			    return Redirect::action('SubdomainUserController@getLoginAction',[$sub])
				->with("error", "Sorry, you are suspended");
			}
	        catch(Cartalyst\Sentry\Throttling\UserBannedException $e)
	        {
	            return Redirect::action('SubdomainUserController@getLoginAction',[$sub])
				->with("error", "Sorry, you are banned");
	        }
			/*
			catch(\Exception $e)
			{
				return Redirect::action('SubdomainUserController@getLoginAction',[$sub])
				->with("error", "Credentials invalid");

				// for more information
                // ->withErrors(array('login' => $e->getMessage()));
			}
			*/

		}
		else 
		{

			return Redirect::action('SubdomainUserController@getLoginAction',[$sub])
			->withInput()
			->withErrors($validation->getErrors());

		}

	}

	public function getRequestAction()
	{
		return View::make("subdomain/login/request");
	}

	public function postRequestAction($sub)
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
				return Redirect::action('SubdomainUserController@getLoginAction',[$sub]);    
			}
			catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
			{
				return Redirect::action('SubdomainUserController@getRequestAction',[$sub])
				->withInput()
				->with("error", "user not found");
			}
				
		}
		else
		{
			return Redirect::action('SubdomainUserController@getRequestAction',[$sub])
			->withInput()
			->withErrors($validation->getErrors());
		}
	}

	public function getResetAction($sub, $resetCode = null)
	{

		if (is_null($resetCode))
		{
			return Redirect::action('SubdomainUserController@getLoginAction',[$sub])
			->with("error", 'reset code not found');
		} 

		try
		{
		    $user = Sentry::findUserByResetPasswordCode($resetCode);
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			return Redirect::action('SubdomainUserController@getRequestAction',[$sub])
				->withInput()
				->with("error", 'User was not found');
		}
		
		return View::make("subdomain/login/reset")->with('resetCode', $resetCode);
	}

	public function postResetAction($sub, $resetCode)
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
						return Redirect::action('SubdomainUserController@getLoginAction', [$sub]);
					}
					else
					{
						return Redirect::action('SubdomainUserController@getRequestAction', [$sub])
						->withInput()
						->with("error", "Reset password failed");
					}
				}
				else
				{
					return Redirect::action('SubdomainUserController@getRequestAction', [$sub])
					->withInput()
					->with("error", "The provided password reset code is Invalid");
				}
			}
			catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
			{
				return Redirect::action('SubdomainUserController@getRequestAction', [$sub])
				->withInput()
				->with("error", 'user not found');
			}
		}
		else
		{
			return Redirect::action('SubdomainUserController@getResetAction', [$resetCode, $sub])
				->withInput()
				->withErrors($validation->getErrors());
		}

	}

	public function profileAction()
	{
		return View::make("subdomain/admin/profile");
	}

	public function logoutAction($sub)
	{
		Sentry::logout();

		return Redirect::route("user/login");
	}
	
}