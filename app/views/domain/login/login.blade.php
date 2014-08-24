@extends("domain.layout")
@section("content")
    {{ Form::open() }}
    <div class="container">
      @if (Session::get("error"))
        {{ Session::get("error") }}<br />
      @endif
      @foreach ($err=$errors->toArray()  as $e)
        {{$e[0]}}<br />
      @endforeach
    </div>
    {{ Form::label("username", "Username") }}
    {{ Form::text("username", Input::old("username")) }}
    {{ Form::label("password", "Password") }}
    {{ Form::password("password") }}
    {{ Form::label("remember", "Remember me") }}
    {{ Form::checkbox('remember', 'true', false)}}
    {{ Form::submit("login") }}
  {{ Form::close() }}
  <div class="container">
    <a href="{{ URL::to("user/request") }}">remind password ></a>
  </div>
@stop