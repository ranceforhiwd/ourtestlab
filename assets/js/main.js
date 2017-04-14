$( document ).ready(function() {
    d3sp = Object.create(D3space);
    d3sp.init('rance');
});

/**********************Nav Bar Controls ****************************/
$("body").on ("click", "a.header", function () {
    $.ajax({
        type: "POST",
        url: this.id+'/index',
        success: function(resp){        
            $("html body div#main.container div#module-container.row ").html(resp);
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
    
    AjaxController.do_ajax(parm).done(function(x){        
        $("div#module-container ").html(x);
    });        
});

$("body").on ("click", "a.sub_menu", function () {
    var parm = {
        url: this.id,
        data:{mod_name:this.id},
        dataType:'text'
    };
    
    $("body div#main.container div.d3space").hide();
    
    AjaxController.do_ajax(parm).done(function(x){        
        $("div#module-container ").html(x);
    });        
});
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
    parm = {type: "POST",url: '../home/validate_login',dataType:'json'};
     
    AjaxController.do_ajax(parm).then(function(x){
        if(x != false){
                d3sp.destroy();
                get_user_menu(x);
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
            $("ul#modlist.main_menu").empty();
            for(var i in x){
                 $("ul#modlist.main_menu").append('<li id="userid" class="category"><a id="'+x[i]['name']+'" class="main_menu"><span class="type">'+x[i]['name']+'</span></a></li>');
            }               
        }
    });
}
    
/*AjaxController.do_ajax(parm).then(function(respJson){
   console.log(respJson[0].name); 
});*/