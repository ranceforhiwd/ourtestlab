$( document ).ready(function() {
    validate_session();
});

/**********************Nav Bar Controls ****************************/
$("html body nav.navbar").on ("click", "a.header", function () {     
    $.ajax({
        type: "POST",
        url: this.id+'/index',
        success: function(resp){        
            $("div#module-container.row div#module.col-md-9").html(resp);
        }   
    });   
});

/*********************Side Bar Controls *****************************/
$("body").on ("click", "a.main_menu", function () {   
    $.ajax({
        type: "POST",
        url: '../'+jsUcfirst(this.id)+'/'+this.id,
        data:{mod_name:this.id},
        success: function(resp){        
            $("div#module-container.row div#module.col-md-9").html(resp);            
        },
        complete: function(){            
        }
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
    $.ajax({
        type: "POST",
        url: 'home/validate_login',
        success: function(resp){
            v = JSON.parse(resp);
            console.log('v:',v);
            /*if(v != false){
                d3sp.destroy();
                get_user_menu(v);
            }*/            
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
    $.ajax({
        type: "POST",
        url: 'home/get_user_menu',
        data:{userid:u},
        success: function(resp){
            m = JSON.parse(resp);
            
            if(m != false){
                $("ul#modlist.main_menu").empty();
                for(var i in m){
                     $("ul#modlist.main_menu").append('<li id="userid" class="category"><a id="'+m[i]['name']+'" class="main_menu"><span class="type">'+m[i]['name']+'</span></a></li>');
                }               
            }            
        }   
    });
}

function do_ajax(x,y){
    $.ajax({
        type: "POST",
        url: x,
        data: y,
        success: function(resp){
            z = JSON.parse(resp);
            if(z != false){
               
            }            
        },
        complete: function(){
            
        }
    });
}