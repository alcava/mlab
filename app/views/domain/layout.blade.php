<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="../css/layout.css" />
    <title>mLab-superuser</title>
  </head>
  <body>
    @include("domain.header")
    <div class="content">
      <div class="container">
        @yield("content")
      </div>
    </div>
    @include("domain.footer")
  </body>
</html>