/**
 * @author Rance Aaron
 * @description Module frontend controller
 * @since 12-29-2016
 */
$( document ).ready(function() {
    /**********************Calendar Section ***************************/
    var calObj1 = {name: 'rance'}
    /********************* Module DataTable section********************/
    
    var tblObj1 = {
                    columns : ["id","name","position","office","age","startdate","salary"],   
                    url : '../Testmodule1/Testmodule1/getData',   
                    name : 'breakpoint'
    }, 
    tblObj2 = {
                columns : ["id","name","salary"],   
                url : '../Testmodule1/Testmodule1/getData',   
                name : 'another'
    };    
      
    /**********************Instanciate objects *************************/
    var testMod = Object.create(Module);    
    var c = [];    
    var t = [];
    t.push(tblObj1);
    t.push(tblObj2);
    c.push(calObj1);
    testMod.init('My Test Module',t, c);
    console.log('running module'); 
});