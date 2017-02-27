/**
 * @author Rance Aaron
 * @description Module frontend controller
 * @since 12-29-2016
 */
$( document ).ready(function() {
    console.log('running docs');
    //$("body").on ("click", "a#docs.main_menu", function () {
        $("ul#modlist.main_menu").html('<li id=""><a>PHP</a></li>');
        $("ul#modlist.main_menu").append('<li id=""><a>JS</a></li>');
        $("html body div#main.container div#module-container.row div#module.col-md-9").html('<p>Documentation for API Here</p>');
    //});
    /*
     * 
    $.ajax({
        type: "POST",
        url: this.id+'/index',
        success: function(resp){        
            $("html body div#main.container div#module-container.row div#module.col-md-9").html(resp);
        }   
    });
    
    var ch = [];
    var drp = [];
    var edtObj1 = {
                    data : [], 
                    name : 'test3'
    }    
    
    var drpObj1 = {
        name:'example range0',
        start:'2016-11-23',
        end:'2016-12-25'
    }
    
    ch.push(edtObj1);
    drp.push(drpObj1);*/
    /**********************Instanciate objects *************************/
    //var testMod3 = Object.create(Module);     
    //testMod3.init('Editor Module', '', '', '', ch, drp);
     
});