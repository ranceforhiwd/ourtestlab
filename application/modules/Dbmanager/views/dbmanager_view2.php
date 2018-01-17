<?php
/**
 * @package Dbmanager
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title></title>        
        <link rel="stylesheet" href="<?php echo base_url("assets/modules/Dbmanager/css/dbmanager.css"); ?>">
        <script type="text/javascript" src="<?php echo base_url("assets/modules/Dbmanager/js/dbmanager.js"); ?>"></script> 
</head>
<body>
    <div id="row1" class="row">
        <div class="title">Database Manager</div>                        
        <div id="dbtype">
            <label id="tables"><span style="margin-right: 5px;" id="tablecnt"></span>Tables <input id="tablesradio" class="icheck" type="radio" name="dbtype" /></label>
            <label id="views"><span style="margin-right: 5px;" id="viewcnt"></span>Views <input id="viewsradio" class="icheck" type="radio" name="dbtype" /></label>
            <label id="indexes"><span style="margin-right: 5px;" id="indexcnt"></span>Indexes</label>
        </div>
    </div>
        <div id="row2" class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header"></div>                   
                    <div id="dbmanager" class="box-content padded">
                        <div id="dbcontrols" class="row">                            
                            <div class="col-md-3">
                                <label>Target&nbsp;<select id="target"></select></label>
                            </div>
                            <div class="col-md-7">
                                <a class="btn btn-default tip dbbtn" title="" id="compare"><i style="" class="icon-cogs"></i>&nbsp;Compare</a>
                                <a class="btn btn-green tip dbbtn" title="" id="updatedb"><i style="" class="icon-cogs"></i>&nbsp;Update Db</a>
                                <a class="btn btn-blue tip dbbtn" title="" id="finishdbupdate"><i style="" class="icon-cogs"></i>&nbsp;Finish Db Update</a>
                                <a class="btn btn-default tip dbbtn" title="" id="certify"><i class="icon-save"></i>&nbsp;Save Db Config</a>
                                <a class="btn btn-default tip dbbtn" title="" id="changeBase"><i class=" icon-check"></i>&nbsp;Change Base</a>
                                <a class="btn btn-default tip dbbtn" title="" id="hideBase"><i class=" icon-check"></i>&nbsp;Hide Base</a>
                            </div>
                            <div class="col-md-2">                                
                                <a class="btn btn-default tip dbbtn" title="" id="backToTablesViews"><i style="" class="icon-arrow-left"></i>&nbsp;Back</a>
                            </div>                           
                        </div>
                        <div id="controls" class="row">
                            <div class="col-md-3">
                                <div id="tables" class="selections"><select style="width: 100%" id="tables"></select></div>
                                <div id="views" class="selections"><select style="width: 100%" id="views"></select></div> 
                            </div>
                            <div class="col-md-3">                                
                                <a class="btn btn-green tip" title="" id="selectedDBtype"><i class="icon-filter"></i>&nbsp;Select</a>                                
                            </div>
                            <div class="col-md-3">
                                <div id="keys"></div>
                                <div id="selectedTable"></div>
                            </div>
                            <div class="col-md-3">
                                <div id="manage"><a class="" title="" id="manage"><i class="icon-cog"></i>&nbsp;Manage Db Config</a></div>                                
                            </div>                          
                        </div>
                        <div style="color:red;" class="msg row"></div>
                        <div style="margin-top: 15px" id="tablesection" class="row">                            
                            <div id="viewdetails">                                
                                <div id="viewcomments" class="col-md-4"></div>
                                <div id="viewdef" class="col-md-6"></div>
                            </div>
                            <div id="manageDetails"></div>
                            <div id="tableDetails"></div>                                                       
                            <div id="keyDetails"></div>
                            <div id="dbCheck"></div>
                        </div>                                                                                                                                                                                                                                                                                                        
                    </div>                     
                </div>
            </div>
            <div class="modal" id="managedb"> 
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">            
                            <h4 class="modal-title"><span id="dialog_title"><i class='icon-cog'></i> Manage Database Configurations</span></h4>
                        </div>
                        <div class="modal-body">
                            <div id="dbdetails"></div>
                            <div id="dblist" class="row">
                                <input id="basecheck" type="hidden" />
                                <div class="col-md-6"><label class="setbase">Base <input id="setbase" type="checkbox" /></label></div>
                                <div class="col-md-6"><label class="updateversion">Version <input class="version" type="text" /></label></div>                              
                            </div>                                
                        </div>
                        <div id="manageMsgFooter" class="modal-footer">
                            <a class="btn btn-default tip" title="" id="updateDbStatus">Update</a>
                            <button id="manageMsgModal" type="button" class="btn btn-default" data-dismiss="modal">Close</button>                                
                        </div>
                    </div>
                </div>
            </div>          
        </div>
        <div id="row3" class="row"></div>
        
    </body>
</html>