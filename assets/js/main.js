$( document ).ready(function() {
    var user = {};
    var d3sp = Object.create(D3space);
    d3sp.init('rance');
   

/**********************Nav Bar Controls ****************************/
$("body").on ("click", "a.header", function () {
    $.ajax({
        type: "POST",
        url: this.id+'/index',
        success: function(resp){        
            $("html body div#main.container div#module-container").html(resp);
        }   
    });   
});

/*********************Side Bar Controls *****************************/
$("body").on ("click", "a.main_menu", function () {
    var parm = {
        url: '../'+jsUcfirst(this.id)+'/'+this.id,
        data:{mod_name:this.id},
        dataType:'text'
    };
    
    send_request(parm);        
});

$("body").on ("click", "a.sub_module", function () {
    var parm = {
        url: '../'+jsUcfirst(this.id)+'/'+this.id,
        data:{mod_name:this.id},
        dataType:'text'
    };
    
    send_request(parm);       
});

$("body").on ("click", "a.sub_menu", function () {
    var parm = {
        url: '../'+this.id,
        data:{mod_name:this.id},
        dataType:'text'
    };
    
    $("body div#main.container div.d3space").hide();
    send_request(parm);            
});
/**
 * 
 * @param {mixed} $r
 * @returns {void}
 */
function send_request($r){
    AjaxController.do_ajax($r).done(function(x){        
        $("div#module-container ").html(x);
    });
}
/**
 * @function jsUcfirst
 * @description Forces the first letter to uppercase in a string
 * @param {string} string
 * @returns {string}
 */
function jsUcfirst(string){
    return string.charAt(0).toUpperCase() + string.slice(1);
}
/**
 * @function validate_session
 * @description Validate the current session via an ajax request to the backend session controller
 * @returns {void}
 */
function validate_session(){
    parm = {
        type: "POST",
        url: '../home/validate_login',
        dataType:'json'
    };
     
    return AjaxController.do_ajax(parm).then(function(x){
        if(x != false){
            d3sp.destroy();
            //user.id = x;
            //get_user_menu(x);
            return x;
        }        
    });    
}
/**
 * @function get_user_menu
 * @description Use the given userid to display the proper menu according to user menu assignments in the database.
 * @param {int} u User id of logged in user.
 * @returns {void}
 */
function get_user_menu(u){   
    var parm = {
        url:'../home/get_user_menu',
        data:{userid:u},
        dataType:'json'
    };
    
    AjaxController.do_ajax(parm).then(function(x){
        if(x != false){
            $("ul#modules.dropdown-menu").empty();
            for(var i in x){
                 $("ul#modules.dropdown-menu").append('<li id="'+x[i]['module_id']+'"><a id="'+x[i]['name']+'" class="sub_module"><span class="modulelabel">'+x[i]['label']+'</span></a></li>');
            }               
        }
    });
}


    
});