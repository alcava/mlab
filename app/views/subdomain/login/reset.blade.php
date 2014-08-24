@extends("subdomain.layout")
@section("content")
  {{ Form::open() }}
    
    @if (Session::get("error"))
        <div class="container">
          {{ Session::get("error") }}
        </div>
    @endif
    
    {{ Form::label("email", "Email") }}
    {{ Form::text("email", Input::old("email")) }}
    <div class="container">{{ $errors->first("email") }}</div>
    {{ Form::label("password", "Password") }}
    {{ Form::password("password") }}
    <div class="container">{{ $errors->first("password") }}</div>
    {{ Form::label("password_confirmation", "Confirm") }}
    {{ Form::password("password_confirmation") }}
    <div class="container">{{ $errors->first("password_confirmation") }}</div>
    {{Form::hidden('resetCode', $resetCode)}}
    {{ Form::submit("reset") }}
  {{ Form::close() }}
@stop