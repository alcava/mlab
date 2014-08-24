@extends("domain.layout")
@section("content")
  <div class="container">
    {{ $message }}
  </div>
  <div class="container">
    <a href="{{ URL::to("user/login") }}">Come back ></a>
  </div>
@stop