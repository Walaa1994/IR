<!doctype html>
<html lang="en">
  <head>
    <title>our search engine</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700,900|Raleway" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo base_url(); ?>/assets/css/bootstrap.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/assets/css/animate.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/assets/css/owl.carousel.min.css">

    <link rel="stylesheet" href="<?php echo base_url(); ?>/assets/fonts/ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/assets/fonts/fontawesome/css/font-awesome.min.css">

    <!-- Theme Style -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>/assets/css/style.css">
  </head>
  <body>
    
    <header role="banner" style="background-image: url(<?php echo base_url(); ?>/assets/images/big_image_1.jpg);">
     
      <nav class="navbar navbar-expand-md navbar-dark bg-light">
        <div class="container">
          <a class="navbar-brand" href="index.html">BEHRW</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample05" aria-controls="navbarsExample05" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse navbar-light" id="navbarsExample05">
                 <form class="form-inline element-animate" id="search-form" action="<?php echo site_url('token/execute');?>" method="post">
              <label for="s" class="sr-only">Location</label>
              <input type="text" name="query_text" class="form-control form-control-block search-input" id="autocomplete" placeholder="write your query" onFocus="geolocate()" style="width: 500px;">
              <button type="submit" class="btn btn-primary">Search</button>
            </form>
          </div>
        </div>
      </nav>
    </header>
    <div id="content">
      <!--content of search result  -->
      <?php foreach ($output as $key =>$value) {?>
           <div class="content-row">
           <a href="#"><?php echo $key;?></a>
           <p><?php echo $value?></p>
           </div>
      <?php }?>
      <!-- <div class="content-row">
        <a href="#">doc1</a>  
      </div>
      <div class="content-row">
        <a href="#">doc2</a>  
      </div>
      <div class="content-row">
        <a href="#">doc3</a>  
      </div>
 -->        
    </div>
    <!-- END header -->
       
    <!-- loader -->
    <div id="loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#f4b214"/></svg></div>

    <script src="<?php echo base_url(); ?>/assets/js/jquery-3.2.1.min.js"></script>
    <script src="<?php echo base_url(); ?>/assets/js/jquery-migrate-3.0.0.js"></script>
    <script src="<?php echo base_url(); ?>/assets/js/popper.min.js"></script>
    <script src="<?php echo base_url(); ?>/assets/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>/assets/js/owl.carousel.min.js"></script>
    <script src="<?php echo base_url(); ?>/assets/js/jquery.waypoints.min.js"></script>
    <script src="<?php echo base_url(); ?>/assets/js/jquery.stellar.min.js"></script>

    
<!-- 
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&libraries=places&callback=initAutocomplete"
        async defer></script> -->

    <script src="<?php echo base_url(); ?>/assets/js/main.js"></script>
  </body>
</html>