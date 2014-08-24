@extends("subdomain.layout")
@section("content")
  <h2>Hello {{ Sentry::getUser()->first_name }}</h2>
  <p>Welcome to your sparse profile page.</p>
@stop