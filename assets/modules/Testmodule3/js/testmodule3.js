/**
 * @author Rance Aaron
 * @description Module frontend controller
 * @since 12-29-2016
 */
$( document ).ready(function() {
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
    drp.push(drpObj1);
    /**********************Instanciate objects *************************/
    var testMod3 = Object.create(Module);     
    testMod3.init('Editor Module', '', '', '', ch, drp);
     
});