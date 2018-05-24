<!DOCTYPE html>
<html>
<head>
<link href="<?php echo base_url();?>/assets/upload.css" rel="stylesheet">
</head>
<body>
<div class="col-md-12 text-center">
<form  action='<?php echo base_url();?>index.php/Upload/do_upload' method="post" enctype="multipart/form-data">
	
	<input  type="file" name="userFile">
	<input  type="submit" value="upload">
</form>
</div>
</body>