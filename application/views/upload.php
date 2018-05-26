
<link href="<?php echo base_url();?>/assets/upload.css" rel="stylesheet">

<div class="col-md-12 text-center">
<form  action='<?php echo base_url();?>index.php/Upload/do_upload' method="post" enctype="multipart/form-data">
	
	<input  type="file" name="userFile">
	<input  type="submit" value="upload">
</form>
</div>
<script src="<?php echo base_url(); ?>/assets/js/jquery-3.2.1.min.js"></script>
    <script src="<?php echo base_url(); ?>/assets/js/jquery-migrate-3.0.0.js"></script>
    <script src="<?php echo base_url(); ?>/assets/js/popper.min.js"></script>
    <script src="<?php echo base_url(); ?>/assets/js/bootstrap.min.js"></script>