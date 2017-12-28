<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            div.section{position: relative; margin-bottom: 5px; padding: 5px;}
            .section-title, .section-content{position: relative;}
        </style>
</head>
<body>   
    <div class="col-md-12">
        <div class="section">
                <h3>Settings</h3>           		
        </div>
        <div class="section col-md-6">
            <h4 class="section-title">Add Modules</h4>
            <div class="section-content col-md-12">               
                <?php echo form_open_multipart('upload/do_upload');?>                
                    <div class="form-group col-md-4">                    
                        <label class="btn btn-primary" for="my-file-selector">                       
                            <input id="my-file-selector"  type="file" name="userfile" style="display:none;" onchange="$('#upload-file-info').html($(this).val());" />
                            Choose File
                        </label>
                        &nbsp;
                        <span class='label label-default' id="upload-file-info"></span>
                        <button type="submit" class="btn btn-default" value="upload">Upload</button>
                    </div>
                    <div class="form-group col-md-8"></div>
                </form>
            </div>
        </div>
        <div class="section col-md-6">
            <h4>Installed Modules</h4>
            <ul class="modulelist">
                <?php
                    if(isset($uploads) && !empty($uploads)){
                        foreach ($uploads as $m) {
                            echo '<li>'.$m.'</li>';
                        }
                    }                    
                ?>
            </ul>
        </div>       			
    </div>
    <div class="col-md-12">
        <div class="section">
                <h3>Module Assignments</h3>           		
        </div> 
        <div class="section col-md-6">
            <ul id="modlist"></ul>
        </div>
        <div class="section col-md-6"></div>   
    </div>    
</body>
</html>