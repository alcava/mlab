@extends("domain.layout")
@section("content")
  <h2>Hello {{ Sentry::getUser()->first_name }}</h2>
  <p>Choose a subdomain to manage:</p>

  	@foreach ($subdomain as $sub)
  	
  	<?php 
  	// Get the page we were before
   // $redirect = Session::get('loginRedirect', 'dashboard');
    // Unset the page we were before from the session
   // Session::forget('loginRedirect');
	//Session::get('subdomain');
  	$url = action('SubdomainUserController@profileAction', $sub); ?>
   	<p>Subdomain: <a href=" {{$url}} ">{{ $sub }}</a></p>
	@endforeach
  
@stop