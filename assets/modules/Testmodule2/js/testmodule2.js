/**
 * @author Rance Aaron
 * @description Module frontend controller
 * @since 12-29-2016
 */
$( document ).ready(function() {
    var chtObj1 = {
                    data : [
                        ['Mushrooms', 3],
                        ['Onions', 1],
                        ['Olives', 1],
                        ['Zucchini', 1],
                        ['Pepperoni', 2]
                      ],   
                    type : 'Piechart',   
                    name : 'test2'
    }, chtObj2 = {
                    data : [
                        ['Mushrooms', 3],
                        ['Onions', 1],
                        ['Olives', 1],
                        ['Zucchini', 1],
                        ['Pepperoni', 2]
                      ],   
                    type : 'Barchart',   
                    name : 'test3'
    },  chtObj3 = {
                    data : [
                        
                      ],   
                    type : 'Linechart',   
                    name : 'test4'
    }
    
    var ch = [];
    ch.push(chtObj1);
    ch.push(chtObj2);
    ch.push(chtObj3);
    /**********************Instanciate objects *************************/
    var testMod2 = Object.create(Module);     
    testMod2.init('Another Test Module','','',ch);
     
});