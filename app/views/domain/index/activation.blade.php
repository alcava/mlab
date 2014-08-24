@extends("domain/layout")
@section("content")
	Info on what we can do...
    <h2>Activation</h2>

    <div class="container">
      @if (Session::get("message"))
        {{ Session::get("message") }}<br />
      @endif
      @if (Session::get("error"))
        {{ Session::get("error") }}<br />
      @endif
    </div>

@stop