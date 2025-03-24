<!DOCTYPE html>
<html>
  <head>
  <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Flow - Plateforme de Gestion de Ville Intelligente</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" href="{{ asset("./assets/logo2.png") }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Simple Marker</title>
    <!-- The callback parameter is required, so we use console.debug as a noop -->
    <script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCucPKHUjomqAvneDLQlnzZuNZZHktt6_U&callback=console.debug&libraries=maps,marker&v=beta">
    </script>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      gmp-map {
        height: 100%;
      }

      /* Optional: Makes the sample page fill the window. */
      html,
      body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #google-map {
        border: 1px;
        width: 50vw;
        height: 85vh;
        align-self : center;
        margin: 0 auto;
      }
      .container {
        display : grid;
        grid-template-columns : 2fr 1fr;
        gap : 10px;
      }
      .card{
        padding : 20px;
        border : 1px solid #ccc;
      }
      .card-L {
        background-color: #1c78c9;

      }
      .card-S{
        background-color: #53a137;
      }
    </style>
  </head>
  <body>
  <div class = "container">
    <div class = "card card-L">
        <div id="google-map">    
<iframe src="https://snazzymaps.com/embed/694751" width="750px" height="600px" style="border:none;"></iframe>     
      </div>
    </div>
      <div class = "card card-S">
        partie info
    </div>
  </div>

  </body>
</html>