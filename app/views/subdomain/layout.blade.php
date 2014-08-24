<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="../css/layout.css" />
    <title>mLab-user</title>
  </head>
  <body>
    @include("subdomain.header")
    <div class="content">
      <div class="container">
        @yield("content")
      </div>
    </div>
    @include("subdomain.footer")
  </body>
</html>