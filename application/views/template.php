	<?php foreach($css_files as $file): ?>
        <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
 
    <?php endforeach; ?>
    <div style='height:20px;'></div>  
    <div>
    <a href="<?php echo site_url('Upload/add_file');?>"> Add file</a>
        <?php echo $output; ?>
    </div>
	<?php foreach($js_files as $file): ?>
        <script src="<?php echo $file; ?>"></script>
    <?php endforeach; ?>