@section("header")
  <div class="header">
    <div class="container">
      <h1>mLab-user</h1>
      @if (Sentry::check())
        <a href="{{ URL::to("user/logout") }}">
          logout
        </a> |
        <a href="{{ URL::to("user/profile") }}">
          profile
        </a>
      @else
        <a href="{{ URL::to("user/login") }}">
          login
        </a>
      @endif
    </div>
  </div>
@show