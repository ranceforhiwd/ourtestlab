<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            div.section{position: relative; margin-bottom: 5px; padding: 5px; border:#000000 solid 1px;}
            .section-title, .section-content{position: relative;}
        </style>
</head>
<body>	
    <div class="col-md-4">
            <h4>Settings</h4>
            <hr/>
            <p>Name: <?php echo $uname; ?></p>
            <p>Email: <?php echo $uemail; ?></p>			
    </div>
    <div class="col-md-8">
        <div class="section">
            <h4 class="section-title">Modules</h4>
            <div class="section-content">
               <?php //echo $error;?>
                <?php echo form_open_multipart('upload/do_upload');?>
                <input type="file" name="userfile" size="20" />
                <br /><br />
                <input type="submit" value="upload" />
                </form>
            </div>
        </div>
        <div class="section"><p>Section</p></div>       			
    </div>
</body>
</html>