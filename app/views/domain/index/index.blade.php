@extends("domain/layout")
@section("content")
	Info on what we can do...
    <h2>Signap</h2>
    {{ Form::open() }}
    <div class="container">
      @if (Session::get("message"))
        {{ Session::get("message") }}<br />
      @endif
      @if (Session::get("error"))
        {{ Session::get("error") }}<br />
      @endif
      @foreach ($err=$errors->toArray()  as $e)
        {{$e[0]}}<br />
      @endforeach
    </div>
    {{ Form::label("email", "Email") }}
    {{ Form::text("email", Input::old("email")) }}
    {{ Form::label("name", "First Name") }}
    {{ Form::text("firstname", Input::old("firstname")) }}
    {{ Form::label("lastname", "Last Name") }}
    {{ Form::text("lastname", Input::old("lastname")) }}
    {{ Form::label("subdomain", "Sub-domain name") }}
    {{ Form::text("subdomain", Input::old("subdomain")) }}
    {{ Form::label("password", "Password") }}
    {{ Form::password("password") }}
    {{ Form::submit("Signup") }}
  {{ Form::close() }}

@stop