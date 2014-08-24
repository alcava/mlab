@extends("subdomain.layout")
@section("content")
  <h2>Hello {{ Sentry::getUser()->first_name }}</h2>
  <p>Choose a subdomain to manage:</p>
  	@foreach ($subdomain as $sub)
    <p>Subdomain: <a href="{{ URL::action('SubdomainUserController@profileAction',$sub,Sentry::getUser()) }}">{{ $sub }}</a></p>
	@endforeach
  
@stop