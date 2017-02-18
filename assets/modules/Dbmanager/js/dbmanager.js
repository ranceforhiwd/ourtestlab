/**
 * Database Manager Frontend Module
 *
 * @category   Database
 * @author     Rance Aaron <raaron@bgea.org>
 * @copyright  2016-2017 DMS BGEA
 * @version    4.60
 * @link       /pwdadmin/js/_modules/dbmanager_module.js
 * @since      File available since 12-07-2016
 *             
 *
 */
 $(document).ready(function(){
     
    var table;
    var ktable;
    var select_flag;
    var selected_target;
        
    function get_table_names(){
        $("div#dbmanager div.msg").text('Analyzing Database...');
        
        $.ajax({
            type: "POST",
            url: 'dbmanager/dbmanager_controller/getTableNames',            
            success: function(resp){
                var sn1 = JSON.parse(resp);
                var sn = sn1['tables'];
                
                $("div#dbtype label span#tablecnt").empty();
                $("div#controls select#tables").empty();
                for(var key in sn){
                   if(sn[key]['collation'] != 'utf8_general_ci'){
                        $("div#controls select#tables").append('<option value="'+sn[key].name+'"><span>*</span>'+sn[key].name+'</option>');
                   }else{
                        $("div#controls select#tables").append('<option value="'+sn[key].name+'"> '+sn[key].name+'</option>');
                   } 
                    
                }                
                
                $("div#dbtype label span#tablecnt").text(sn1['counts']['table_count']);
                $("span#indexcnt").text(sn1['counts']['index_total']);
            },
            complete: function(){                
                get_view_names();
            }
        });
    }
    
    function get_db_names(){               
        $.ajax({
            type: "POST",
            url: 'dbmanager/dbmanager_controller/getDbNames',
            success: function(resp){
                var dn = JSON.parse(resp);                
                $("div#dbcontrols select#base").empty();
                $("div#dbcontrols select#target").empty();
                for(var key in dn){                   
                        $("div#dbcontrols select#base").append('<option value="'+dn[key]+'"> '+dn[key]+'</option>'); 
                        $("div#dbcontrols select#target").append('<option value="'+dn[key]+'"> '+dn[key]+'</option>');
                }               
            },
            complete: function(){                
                $("div.msg").empty();                                                                 
            }
        });
    }
            
    function get_view_names(){
        $.ajax({
            type: "POST",
            url: 'dbmanager/dbmanager_controller/getViewNames',            
            success: function(resp){
                var sn = JSON.parse(resp);                
                $("div#controls select#views").empty();
                $("div#dbtype label span#viewcnt").empty();
                
                for(var key in sn){
                    if(key !== 'view_count'){
                        $("div#controls select#views").append('<option value="'+key+'"> '+key+'</option>');
                    }                    
                }                
                
                $("div#dbtype label span#viewcnt").text(sn['view_count']);
            },
            complete: function(){
                get_base_dbconfig();                
            }
         });
    }
    
    function get_column_names(t){
        var gcn = '<table id="tableDetails" class="dataTable no-footer"><thead><tr><th>Column</th><th>Column Type</th><th>Data Type</th><th>Key</th></tr></thead>';
        
        $.ajax({
            type: "POST",
            url: 'dbmanager/dbmanager_controller/getColumnNames',
            data: {'tablename':t},
            success: function(resp){
                var c = JSON.parse(resp);                
                                
                for(var x in c){
                    for(var y in c[x]){
                        gcn += '<tr><td class="name">'+c[x][y]['column_name']+'</td><td class="coltype">'+c[x][y]['column_type']+'</td><td class="dtype">'+c[x][y]['data_type']+'</td><td class="key"><a class="selectkey" id="'+c[x][y]['column_name']+'">'+c[x][y]['column_key']+'</a></td><tr>';                       
                    }                   
                }
                
                gcn += '</table>';                 
                
                $("div#controls div#keys").html('<a id="'+t+'" class="showkeys">Show Keys</a>');                              
                $("table#keyDetails").hide();                
                $("div#selectedTable").hide();
                $("div#keys").show();
                $('div#tablesection.row div#tableDetails').html(gcn);
                $('div#tablesection.row div#tableDetails').show();
            }
         });
    }
       
    function list_tables(){
       $("div#dbcontrols").hide();
       $("div#views.selections").hide();
       $("div#manageDetails").hide();
       $("div#tables.selections").show();
    }

    function list_views(){
       $("div#dbcontrols").hide();
       $("div#manageDetails").hide();
       $("div#tables.selections").hide();
       $("div#views.selections").show();
       $("div#keys").hide();
       $("div#controls div#selectedTable").hide();
       $("table#keyDetails").hide();
    }
            
    function get_table_keys(t){
        $("div#dbmanager div.msg").text('Please wait...');
        var tkd = '<table id="keyDetails" class="dataTable no-footer"><thead><tr><th>Key Name<span id="keycnt"></span></th><th>Columns</th></tr></thead>';
        
        $.ajax({
            type: "POST",
            url: 'dbmanager/dbmanager_controller/getTableKeys',
            data: {'t':t},
            success: function(resp){             
                var keys = JSON.parse(resp);
                                    
                for(var x in keys){                    
                    for(var index in keys[x]){                       
                        if(keys[x][index].length > 1){
                            tkd += '<tr><td>'+index+'</td><td><span style="color:blue;">'+keys[x][index].join(", ")+'</span></td></tr>';                            
                        }else{
                            tkd += '<tr><td>'+index+'</td><td>'+keys[x][index].join(", ")+'</td></tr>';                            
                        }                        
                    }
                }
                
                tkd += '</table>';
                
                $("div#keyDetails").html(tkd);                                                
                $("div#keys").hide();
                $('table#tableDetails').hide();
                $("div#selectedTable").html('<a class="showcolumns">Show Table Columns</a>');
                $("span#keycnt").text(' ('+keys.length+')');
                $("div#keyDetails").show();
                $("div#selectedTable").show();
            },
            complete: function(){
                $("div#dbmanager div.msg").empty();
            }
         });
    }
    
    function get_view_details(v){
        $.ajax({
            type: "POST",
            url: 'dbmanager/dbmanager_controller/getViewDetails',
            data: {'viewname':v },
            success: function(resp){
                var vdef = JSON.parse(resp);                
                var vcomments = '<h4>Dependencies</h4>';
                $("div#viewdef").html('<h4>View Definition</h4>'+vdef['def']);
                $("div#viewcomments").html(vcomments);
                $("div#viewcomments").append('<ul />');
                
                for(var i in vdef['dep']['tables']){                    
                    $("div#viewcomments ul").append('<li style="list-style-type: none;">'+vdef['dep']['tables'][i]+'</li>');   
                }
                
                for(var j in vdef['dep']['views']){
                    if(v !== vdef['dep']['views'][j]){
                        $("div#viewcomments ul").append('<li style="list-style-type: none;"><a class="deplink" style="color:blue;text-decoration:underline" id="'+vdef['dep']['views'][j]+'">'+vdef['dep']['views'][j]+'</a></li>');   
                    }                    
                }                
            },
            complete: function(){
                $("div#views.selections select#views").val(v);
                $("div#views.selections span#select2-views-container.select2-selection__rendered").text(v);
                $("div#viewdetails").show();
            }
         });
    }
    
    function get_all_dbconfig(){
        var dblist = '<table id="dblist" class="dataTable no-footer"><thead><th>Base Configuration</th><th>Time</th><th>Version</th></thead>';
        $.ajax({
            type: "POST",
            url: 'dbmanager/dbmanager_controller/getAllDbConfig',
            success: function(resp){              
                var sdb = JSON.parse(resp);
                
                for(var key in sdb){
                    if(sdb[key]['status'] == 1){
                        dblist += '<tr><td><i style="color:green;" class="icon-ok"></i></td><td><a id="'+key+'" class="dbtimestamp" data-version="'+sdb[key]['version']+'" data-status="'+sdb[key]['status']+'">'+sdb[key]['timestamp']+'</a></td><td class="version">'+sdb[key]['version']+'</td></tr>';
                    }else{
                        dblist += '<tr><td></td><td><a id="'+key+'" class="dbtimestamp" data-version="'+sdb[key]['version']+'" data-status="'+sdb[key]['status']+'">'+sdb[key]['timestamp']+'</a></td><td id="'+key+'" class="version">'+sdb[key]['version']+'</td></tr>';
                    }                    
                }
                
                dblist += '</table>';
                $("div#manageDetails").html(dblist);
            },
            complete: function(){                
                $("div#manageDetails").show();
            }
         });
    }
    
    function get_base_dbconfig(){
        $.ajax({
            type: "POST",
            url: 'dbmanager/dbmanager_controller/getBaseDbConfig',
            success: function(resp){              
                var current_dbconfig = JSON.parse(resp);
                console.log('current dbconfig:', current_dbconfig);
            },
            complete: function(){
                get_db_names();                
            }
         });
    }
    
    function get_db_record(x){        
        $("div#dbdetails").html('<input type="hidden" value="'+x.id+'" />');
        $("div#dblist .updateversion input.version").val(x.version);
        
        if(x.status == 1){            
            $('div#dblist label.setbase input').iCheck('check');
        }else{
            $('div#dblist label.setbase input').iCheck('uncheck');
        }
        
        $("div#managedb.modal").show();
    }
    
    function compare_database(db){
        $('div#dbcontrols a').attr('disabled', true);
        $('rebuildviews').attr('disabled', true);
        $('select#target').attr('disabled', true);
        var r = '';
        var a = '';
        var b = '';
        var m = '';
        var n = '';
        var mkeys = '';
        var w = '';
        var resultsTable = '<table id="dbcheck" class="dataTable no-footer"><thead><th>Add Tables</th><th>Remove Tables</th><th>Remove Columns</th><th>Modify Column Data Types</th><th>Update Keys</th></thead>';        
        resultsTable += '<tr><td valign="top" id="add"><ul id="add" /></td><td valign="top" id="remove"><ul id="remove" /></td><td valign="top" id="modify"><ul id="modifyRemove" /></td><td valign="top"><ul id="columnTypes" /></td><td valign="top"><ul id="keys" /></td></tr></table>';
        $("div#row2.row div#dbmanager div.msg.row").html('<span><i style="" class="icon-spinner icon-spin"></i> Comparing databases</span>');
        $.ajax({
            type: "POST",
            url: 'dbmanager/dbmanager_controller/compareDb',
            data: {dbname: db},
            success: function(resp){
                var tables_missing_columns = [];
                var missing_columns = [];
                var c = JSON.parse(resp);              
                var rbase = Object.keys(c.remove).map(function (key) { return c.remove[key]; });
                var abase = Object.keys(c.add).map(function (key) { return c.add[key]; });
                var mbase = Object.keys(c.modifyAdd).map(function (key) { return c.modifyAdd[key]; });
                var nbase = Object.keys(c.modifyRemove).map(function (key) { return c.modifyRemove[key]; });
                var kbase = Object.keys(c.keystoupdate).map(function (key) { return c.keystoupdate[key]; });
                var dbase = Object.keys(c.dataTypes).map(function (key) { return c.dataTypes[key]; });
                var checksum = 0;
                
                for(var i in abase){
                    if(i){
                        a += '<li style="list-style-type: none;"><span style="color:green">'+abase[i]+'</span></li>';
                    }                    
                }                
                                                
                for(var k in rbase){
                    if(k){                        
                        r += '<li style="list-style-type: none;"><span style="color:red;">'+rbase[k]+'</span></li>';
                    }                    
                }
                
                for(var j in mbase){
                    if(j){
                        for(var l in mbase[j]){
                            tables_missing_columns.push(l);                            
                            for(var l2 in mbase[j][l]){
                                missing_columns.push(mbase[j][l][l2]);                                
                            }
                            tables_missing_columns[l] = missing_columns
                        }                        
                    }                    
                }
                                
                for(var h in nbase){
                    if(h){
                        for(var p in nbase[h]){
                            n += '<li class="tablename" style="list-style-type: none;">'+p+'<ul>';
                            for(var p2 in nbase[h][p]){
                                n += '<li style="list-style-type: none;"><span style="padding-left:10px;font-size:0.08;color:red;">'+nbase[h][p][p2]+'</span></li>';
                            }
                            n += '</ul></li>';
                        }                        
                    }                    
                }
                
                for(var q in kbase){
                    if(q){                        
                            b += '<li style="list-style-type: none;">'+kbase[q]+'</li>';                                             
                    }                    
                }
                
                for(var v in dbase){
                    if(v){
                            for(var v2 in dbase[v]){
                                if($.inArray(v2, tables_missing_columns) != -1){
                                    w += '<li class="tablename" style="list-style-type: none;"><span style="color:red;text-decoration:underline;font-size:1.2em;" class="tablename">'+v2+'</span><ul class="tablelist">';
                                }else{
                                    w += '<li class="tablename" style="color:black;list-style-type: none;"><span style="text-decoration:underline;font-size:1.2em;" class="tablename">'+v2+'</span><ul class="tablelist">';
                                }
                                
                                w += '<ul style="padding-bottom:10px;">';
                                
                                for(var v3 in dbase[v][v2]){
                                    w += '<li style="list-style-type: none;padding-left:5px;">'+v3+'</li>';
                                    w += '<ul>';
                                    for(var v4 in dbase[v][v2][v3]){
                                        w += '<li class="fieldvalue" style="font-size:0.8em;color:blue;">'+v4+' : '+dbase[v][v2][v3][v4]+'</li>';
                                    }
                                    w += '</ul>';
                                }
                                w += '</ul>';
                                
                                w += '</ul></li>';
                            }                                                                        
                    }                    
                }
                
                clear_msg();                
                checksum = abase.length+rbase.length+mbase[0].length+nbase[0].length+kbase.length+dbase.length;
                if(checksum == 0){
                    $("div#row2.row div#dbmanager div.msg.row").html('<span>Database tables ok...press Update Db to update views</span>');
                    $("div#tablesection.row div#dbCheck").empty();
                }
            },
            complete: function(){                
                $("div#dbCheck").append(resultsTable).show();                
                $("ul#add").append(a);
                $("ul#remove").append(r);
                $("ul#modifyAdd").append(m);
                $("ul#modifyRemove").append(n);               
                $("ul#keys").append(b);
                $("ul#columnTypes").append(w);
                $('div#dbcontrols a').attr('disabled', false);                
                $('div#dbcontrols a#certify').attr('disabled', true);
                $('div#dbcontrols a#changeBase').attr('disabled', true);
                $('div#dbcontrols a#rebuildviews').attr('disabled', true);
                show_update_db();
            }
        });      
    }
    
    function add_tables(st){
        $("div#row2.row div#dbmanager div.msg.row").html('<span><i style="" class="icon-spinner icon-spin"></i> Adding tables</span>');
        $.ajax({
                type: "POST",
                url: 'dbmanager/dbmanager_controller/addTables',
                data: {dbname: st},
                success: function(resp){
                    var dbc = JSON.parse(resp);
                    console.log('Added Tables new:', dbc);
                    clear_msg();  
                   remove_tables(st, dbc['remove']);
                },
                complete: function(){                    
                    $("div#dbCheck table#dbcheck ul#add").empty();
                    $("div#dbCheck table#dbcheck ul#add").html('<li>Done</li>');              
                }
        });
    }
    
    function remove_tables(st, t){
        $("div#row2.row div#dbmanager div.msg.row").html('<span><i style="" class="icon-spinner icon-spin"></i> Renaming tables</span>');
        $.ajax({
                type: "POST",
                url: 'dbmanager/dbmanager_controller/removeTables',
                data: {dbname: st,removelist:t},
                success: function(resp){
                    var dbc = JSON.parse(resp);
                    console.log('Removed Tables:', dbc);
                    clear_msg();  
                    remove_columns(st, dbc['modifyRemove']);
                },
                complete: function(){                    
                    $("div#dbCheck table#dbcheck ul#remove").empty();
                    $("div#dbCheck table#dbcheck ul#remove").html('<li>Done</li>');              
                }
        });
    }
    
    function remove_columns(st, c){
        $("div#row2.row div#dbmanager div.msg.row").html('<span><i style="" class="icon-spinner icon-spin"></i> Removing columns</span>');
        $.ajax({
                type: "POST",
                url: 'dbmanager/dbmanager_controller/removeColumns',
                data: {dbname: st,removecols:c},
                success: function(resp){
                    var dbc = JSON.parse(resp);
                    console.log('Removed Columns:', dbc);
                    clear_msg();
                    modify_columns(st, dbc['dataTypes']);
                },
                complete: function(){                      
                    $("div#dbCheck table#dbcheck ul#modifyRemove").empty();
                    $("div#dbCheck table#dbcheck ul#modifyRemove").html('<li>Done</li>');              
                }
        });
    }
    
    function modify_columns(st, m){
        $("div#row2.row div#dbmanager div.msg.row").html('<span><i style="" class="icon-spinner icon-spin"></i> Updating columns</span>');
            $.ajax({
                    type: "POST",
                    url: 'dbmanager/dbmanager_controller/modifyColumns',
                    data: {dbname: st,modify:m},
                    success: function(resp){
                        var dbc = JSON.parse(resp);
                        console.log('Modified Columns:', dbc);
                        clear_msg();  
                        add_columns(st, dbc['modifyAdd']);
                    },
                    complete: function(){                        
                        $("div#dbCheck table#dbcheck ul#modifyRemove").empty();
                        $("div#dbCheck table#dbcheck ul#modifyRemove").html('<li>Done</li>');              
                    }
            });        
    }
    
    function add_columns(st, ac){
        $("div#row2.row div#dbmanager div.msg.row").html('<span><i style="" class="icon-spinner icon-spin"></i> Adding missing columns</span>');
        $.ajax({
                type: "POST",
                url: 'dbmanager/dbmanager_controller/addColumns',
                data: {dbname: st,modifyAdd:ac},
                success: function(resp){
                    var dbc = JSON.parse(resp);
                    console.log('Added Columns:', dbc);
                    clear_msg();  
                    update_indexes(st, dbc['keystoupdate']);
                },
                complete: function(){                    
                    $("div#dbCheck table#dbcheck ul#columnTypes").empty();
                    $("div#dbCheck table#dbcheck ul#columnTypes").html('<li>Done</li>');              
                }
        });
    }
    
    function update_indexes(st, i){
        $("div#row2.row div#dbmanager div.msg.row").html('<span><i style="" class="icon-spinner icon-spin"></i> Updating indexes/keys</span>');
        $.ajax({
                type: "POST",
                url: 'dbmanager/dbmanager_controller/updateIndexes',
                data: {dbname: st,tablelist:i},
                success: function(resp){                    
                    var update_results = JSON.parse(resp);
                    console.log(update_results);
                    clear_msg();
                },
                complete: function(){
                    $("div#dbCheck table#dbcheck ul#keys").empty();
                    $("div#dbCheck table#dbcheck ul#keys").html('<li>Done</li>');                    
                    update_views(st);
                }
        });
    }
    
    function update_views(st){
        $("div#row2.row div#dbmanager div.msg.row").append('<span><i style="" class="icon-spinner icon-spin"></i> Updating database views</span>');
        $.ajax({
                type: "POST",
                url: 'dbmanager/dbmanager_controller/updateViews',
                data: {dbname: st},
                success: function(resp){                    
                    var update_results = JSON.parse(resp);
                    console.log(update_results);
                    get_audit();
                },
                complete: function(){ 
                    clear_msg();
                    $("a#updatedb").hide();
                    $("a#compare").show();
                }
        });
    }
    
    function manual_update_views(st){
        $("div#row2.row div#dbmanager div.msg.row").append('<span><i style="" class="icon-spinner icon-spin"></i> Updating database views</span>');
        $.ajax({
                type: "POST",
                url: 'dbmanager/dbmanager_controller/updateViews',
                data: {dbname: st},
                success: function(resp){                    
                    var update_results = JSON.parse(resp);
                    console.log(update_results);                    
                },
                complete: function(){ 
                    clear_msg();
                    $("a#updatedb").hide();
                    $("a#compare").show();
                }
        });
    }
    
    function get_audit(){
        $("div#row2.row div#dbmanager div.msg.row").append('<span><i style="" class="icon-spinner icon-spin"></i> Creating audit log</span>');
        $.ajax({
                type: "POST",
                url: 'dbmanager/dbmanager_controller/getAuditLog',                
                success: function(resp){                    
                    var audit_log = JSON.parse(resp);
                    console.log(audit_log);
                },
                complete: function(){
                    clear_session_data();
                    clear_msg();                   
                }
        });
    }
    
    function clear_session_data(){        
        $.ajax({
                type: "POST",
                url: 'dbmanager/dbmanager_controller/clearAuditSessionData',
                success: function(){
                    $('a#changeBase').attr('disabled', false);
                    $('a#certify').attr('disabled', false);
                    $('select#target').attr('disabled', false);
                    $("a#backToTablesViews").attr('disabled', false);                    
                    $('div#dbcontrols a#rebuildviews').attr('disabled', false);
                }
        });
    }
    
    function clear_msg(){
        $("div#row2.row div#dbmanager div.msg.row").empty();
    }
            
    function show_update_db(){
        $("a#compare.btn.btn-default").hide();
        $("a#updatedb.btn.btn-green").show();
    }
    
    function show_compare_db(){
        $("a#updatedb.btn.btn-green").hide();
        $("a#compare.btn.btn-default").show();
    }
    
    function show_change_base(){
        $("a#hideBase").hide();
        $("a#changeBase").show();
    }
    
    function hide_change_base(){
        $("a#changeBase").hide();
        $("a#hideBase").show();
    }
    
    function show_browse_view(){
        
    }
    
    function show_manage_view(){        
        $("div#controls").hide();
        $("div#tableDetails").hide();
        $("div#keyDetails").hide();
        $("div#viewdetails").hide();
        show_change_base();
        show_compare_db(); 
        $("div#dbcontrols.row").show();               
    }
    
    // Select tables or views
    $('div#dbtype input.icheck').on('ifChecked', function(event){        
        if(this.id == 'tablesradio'){
            list_tables();
             $("table#keyDetails").hide();
             $("div#viewdetails").hide();
             select_flag = 'select#tables';
        }else{
            list_views();
            $("table#tableDetails").hide();
            $("div#viewdetails").show();
            select_flag = 'select#views';
        }
    });
    // Watch keys, talbes, or view detail links
    $("div#controls div#keys").on ("click", "a.showkeys", function () {        
        $("table#tableDetails").hide();
        get_table_keys(this.id);
    });
    $("div#controls div#selectedTable").on ("click", "a.showcolumns", function () {       
        $("table#keyDetails").hide();
        $("div#selectedTable").hide();
        $("table#tableDetails").show();
        $("div#keys").show();
    });   
    $("div#viewdetails").on ("click", "a.deplink", function () {      
        get_view_details(this.id);
    });
    
    /*********Manage saved db structures **********************/
    
    $("div#manage").on ("click", "a#manage", function () {
        $('div#dbcontrols a').attr('disabled', false);
        $('select#target').attr('disabled', false);
        show_manage_view();       
    });
    
    $("div#manageDetails").on ("click", "a.dbtimestamp", function () {                
        get_db_record({id:this.id, status:$(this).data('status'), version:$(this).data('version')});
    });
    
    /******************Listen for base config selection checkboxes ***************************/
    $('div#managedb.modal div#dblist label.setbase input#setbase').on('ifChecked', function(event){      
        $("div#managedb.modal div#dblist.row input#basecheck").val(1);
    });
    
    $('div#managedb.modal div#dblist label.setbase input#setbase').on('ifUnchecked', function(event){
        $("div#managedb.modal div#dblist.row input#basecheck").val(0);
    });
    
    // Update Db config record
    $("div#managedb div#manageMsgFooter").on ("click", "a#updateDbStatus.btn", function () {      
        var update_version = $("div#managedb.modal input.version").val();
        var update_base = $("div#managedb.modal div#dblist.row input#basecheck").val();
        var update_id = $("div#managedb.modal div#dbdetails input").val();
        
        if(update_base == 1){
            if(update_version.trim() !=null && update_version.trim() != ''){
                $.ajax({
                    type: "POST",
                    url: 'dbmanager/dbmanager_controller/updateDbRecord',
                    data: {id:update_id,version:update_version,status:update_base},
                    success: function(resp){
                        var sn = JSON.parse(resp);
                        console.log('update db config status:', sn);
                    },
                    complete: function(){
                        $("div#managedb.modal").hide();
                        get_all_dbconfig();
                        console.log('update complete');                
                    }
                });
            }else{
                alert('must have a version for base config');
            }
        }else{
            $.ajax({
                    type: "POST",
                    url: 'dbmanager/dbmanager_controller/updateDbRecord',
                    data: {id:update_id,version:update_version},
                    success: function(resp){
                        var sn = JSON.parse(resp);                             
                    },
                    complete: function(){
                        $("div#managedb.modal").hide();
                        get_all_dbconfig();
                        console.log('update complete');                
                    }
                });
        }
        
    });
    
    $("div#manageMsgFooter").on ("click", "button#manageMsgModal", function () {       
        $("div#managedb.modal").hide();
    });
            
    $("div#dbcontrols").on ("click", "a#changeBase", function () {       
        $("table#dblist").show();        
        $("a#changeBase").hide();
        $("a#hideBase").show();
        get_all_dbconfig();
    });
    
    $("div#dbcontrols").on ("click", "a#hideBase", function () {       
        $("table#dblist").hide();
        $("a#hideBase").hide();
        $("a#changeBase").show();
    });
    
    $("div#dbcontrols").on ("click", "a#backToTablesViews", function () {
        clear_msg();
        list_tables();
        $("div#controls").show();
        $("div#tableDetails").show();
        $("div#dbCheck").hide();
    });
    
    /*************Database Compare and Check *********************************/
    $("div#dbmanager div#dbcontrols").on ("click", "a#compare", function () {
        clear_msg();
        var selected_base = $("div#dbcontrols.row select#base").val();
        selected_target = $("div#dbcontrols.row select#target").val();
        $("div#dbCheck").empty();
        //Get tablenames from selected target
        if(selected_target != 'none selected'){
            compare_database(selected_target);
        }else{
            $("div#row2.row div#dbmanager div.msg.row").html('<span><i style="" class="icon-exclamation-sign"></i> Select a database to check</span>');
        }        
    });
    
    /*************Update Database Routine *********************************/
    $("div#dbcontrols").on ("click", "a#updatedb", function () {       
        console.log('update db:', selected_target);        
        $("a#backToTablesViews").attr('disabled', true);        
        add_tables(selected_target);
    });
    
    /*************Rebuild Database Views **********************************/
    $("div#dbcontrols").on ("click", "a#rebuildviews", function () {
        clear_msg();
        selected_target = $("div#dbcontrols.row select#target").val();
        
        if(selected_target != 'none selected'){
            console.log('rebuild views for db:', selected_target);
            manual_update_views(selected_target);
        }else{
            console.log('select a database');
            $("div#row2.row div#dbmanager div.msg.row").html('<span><i style="" class="icon-exclamation-sign"></i> Select a database to rebuild views</span>');
        }        
    });
    
    /*************Certify Db Structure ************************/ 
    $("div#dbcontrols").on ("click", "a#certify", function () {
        console.log('certifying db');
        $("div#tablesection div.msg").text('Saving database structure...');
        var version = 4.7;
        
        $.ajax({
            type: "POST",
            url: 'dbmanager/dbmanager_controller/certifyDb',
            data: {'version':version, database:'peacewithgod'},
            success: function(resp){
                var cd = JSON.parse(resp);                
            },
            complete: function(){
                $("div#tablesection div.msg").empty();
            }
         });
    });
    
    $("div#controls").off 
    $("div#controls").on ("click", "a#selectedDBtype", function () {        
        if(select_flag == 'select#views'){            
            get_view_details($("div#controls "+select_flag).val());
        }else{
            get_column_names($("div#controls "+select_flag).val());
        }        
    });
        
    $("select#tables").select2({
       placeholder: "Tables"
     });

    $("select#views").select2({
       placeholder: "Views"
     });
     
    $('input').iCheck({
        radioClass: 'iradio_flat-blue',
        checkboxClass: 'icheckbox_flat-blue',
    });

    // Initialize
    list_tables();
    $('input#tablesradio').iCheck('check');
    $("div#dbcontrols.row").hide();
    get_table_names();    
});
