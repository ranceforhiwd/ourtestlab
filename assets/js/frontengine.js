/**
 * @author Rance Aaron
 * @class Table
 * @type object
 * @version 1.5 Under Git control and Grunt task managers. 
 * @description Wrapper for {@link https://datatables.net DataTables}.
 *  Table objects allow easy construction for data tables with connections 
 *  to the data sources.
 * 
 */
var Table = {
    /**
     *  
     * @function init     
     * @description Initializes the table object
     * @memberOf Table 
     * @param {string} name Name used as dom element id
     * @param {string} url Url to data source
     * @param {array} columns Column names
     * @returns void
     */
    init: function(name,url,columns) {
        this.col_array = columns;
        this.name = name;
        this.url = url;
        this.createTable();
                
        $('table#'+this.name).DataTable( {        
                serverSide: true,
                ajax: url,
                columns: this.getColumns()
        });
        
        var dTable = $('table#'+this.name).DataTable();
        for(var i in this.col_array){
            $(dTable.column(i).header()).text(this.col_array[i]);
        }
        
    },
    /**
     * @function createTable
     * @description Creates a dom element for the table object
     * @memberOf Table  
     * @returns void
     */
    createTable: function() {
        $("div#module-container div.mod div.module_content.row ").append('<table id="'+this.name+'" class="display" cellspacing="0" width="100%" />');              
    },
    /**
     * @function getColumns
     * @description Return an array of column data 
     * @memberOf Table 
     * @returns {Array|Table.getColumns.c}
     */
    getColumns: function(){
        var c = [];
        
        for(var k in this.col_array){
            c.push({ "data": this.col_array[k]});
        }
        
        return c;
    }
};
/**
 * @class Module
 * @type object
 * @description Front end module object used by frontengine to render modules.
 */
(function () {
    Module = {
    /**
     * @function init
     * @memberOf Module
     * @description Initialize module object using arrays of frontend objects 
     * as parameters to build a module.
     * @param {string} name Module name
     * @param {array} tbl Array of table objects
     * @param {array} cal Array of calendar objects
     * @param {array} cht Array of chart objects
     * @param {array} edt Array of editor objects
     * @param {array} drp Array of date range picker objects
     * @returns {void}
     */
    init: function(name, tbl, cal, cht, edt, drp) {        
        this.name = name;
        this.tables = tbl;
        this.cal = cal;
        this.chart = cht;
        this.editor = edt;
        this.daterange = drp;
        this.createModule();        
    },
    /**
     * @function createModule
     * @description Create front end module object
     * @memberOf Module
     * @returns {void}
     */
    createModule: function() {        
        $("body div#main.container.row.col-md-12 div#module-container div#container").html('<div class="mod" />');
        $("div.mod").append('<div class="row"><h3 style="padding:25px;">'+this.name+'</h3></div>');
        $("div.mod").append('<div style="padding:25px;" class="module_content row"></div>');
        var t = [];
        var c = [];
        var ch = [];
        var ed = [];
        var dr = [];
        
        for(var k in this.tables){
            t[k] = Object.create(Table);
            t[k].init(this.tables[k].name, this.tables[k].url, this.tables[k].columns);
        }
        
        for(var k in this.cal){
            c[k] = Object.create(Calendar);
            c[k].init(this.cal[k].name);
        }
                
        for(var k in this.chart){
            ch[k] = Object.create(Chart);
            ch[k].init(this.chart[k].name, this.chart[k].type, this.chart[k].data);
        }
        
        for(var k in this.editor){
            ed[k] = Object.create(Editor);
            ed[k].init(this.editor[k].name, this.editor[k].type, this.editor[k].data);
        }
        
        for(var k in this.daterange){
            dr[k] = Object.create(DateRangePicker);
            dr[k].init(this.daterange[k].name,this.daterange[k].start,this.daterange[k].end);
        }
        
    }
};
}());
/**
 * @class Calendar
 * @type object
 * @description Wrapper objects for Full Calendar JS API
 */
var Calendar = {
    init: function(name) {       
        this.name = name;       
        this.createCalendar();
        
        $('#calendar').fullCalendar({
           
        });
       
    },
    createCalendar: function() {
        $(" div#module-container div.mod div.module_content.row").append('<div id="calendar"></div>');              
    }
};
/**
 * @class D3space
 * @type object
 * @description Wrapper objects for D3 Data Visualization API
 */
(function () {
 D3space = {
    init: function(name) {       
        this.name = name;       
        this.createD3space();      
    },
    createD3space: function() {
        $("html body div#main.container").append('<div class="d3space" style="position:absolute;z-index:2;top:150px;left:0px;" />');
        gears = Object.create(Gears);
        gears.init('rance');
    },
    destroy : function(){
        $("div.d3space").empty();
    }
};
}());
/**
 * @class Chart
 * @type object
 * @description Wrapper objects created using Google Charts API
 */
(function () {
var Chart = {
    /**
     * @function init
     * @description Initialize a chart object
     * @memberOf Chart
     * @param {string} name
     * @param {string} type
     * @param {array} data
     * @returns {void}
     */
    init: function(name, type, data) {       
        this.name = name;
        this.type = type;
        this.data = data;
        this.selectChart(this.type, this.name, this.data);          
    },
    /**
     * @function selectChart
     * @description Select chart type for api call
     * @memberOf Chart
     * @param {string} t chart type
     * @param {type} n chart name
     * @param {type} d chart 
     * @returns {void}
     */
    selectChart: function (t,n,d){
        switch (t) {
            case "Piechart":
              this.createPieChart(n, d); 
              break;
            case "Barchart":
              this.createBarChart(n, d); 
              break;
          case "Linechart":
              this.createLineChart(n, d); 
              break;
        }
    },
    /**
     * @function createBarChart
     * @description Create a bar chart
     * @memberOf Chart
     * @param {string} n
     * @param {array} d
     * @returns {void}
     */
    createBarChart: function(n, d) {       
      $(" div#module div.mod div.module_content").append('<div class="barchart" id="chart_bar"></div>');
      
      // Load the Visualization API and the corechart package.
      google.charts.load('current', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {            
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', 'Slices');
        data.addRows(d);
       
        var options = {'title':n,
                       'width':700,
                       'height':600};
       
        var chart = new google.visualization.BarChart(document.getElementById('chart_bar'));
        chart.draw(data, options);
      }
    },
    /**
     * 
     * @function createBarChart
     * @memberOf Chart
     * @description Create bar chart object
     * @param {string} n Name of chart
     * @param {array} d Chart data
     * @returns {void}
     */
    createPieChart: function(n, d) {       
      $(" div#module div.mod div.module_content").append('<div class="piechart" id="chart_pie"></div>');
      
      // Load the Visualization API and the corechart package.
      google.charts.load('current', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {            
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', 'Slices');
        data.addRows(d);
       
        var options = {'title':n,
                       'width':700,
                       'height':600};
       
        var chart = new google.visualization.PieChart(document.getElementById('chart_pie'));
        chart.draw(data, options);
      }
    },
    /**
     * @function createLineChart
     * @description Create line chart objects
     * @memberOf Chart
     * @param {string} n Name of chart
     * @param {array} d Chart data
     * @returns {void}
     */
    createLineChart: function(n, d) {       
      $(" div#module div.mod div.module_content").append('<div id="curve_chart" style="width: 900px; height: 500px"></div>');
      
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Year', 'Sales', 'Expenses'],
          ['2004',  1000,      400],
          ['2005',  1170,      460],
          ['2006',  660,       1120],
          ['2007',  1030,      540]
        ]);

        var options = {
          title: 'Company Performance',
          curveType: 'function',
          legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
      
    }
};
}());
/**
 * @class Editor
 * @description Wrapper object for text editor
 * @type object
 */
var Editor = {
    init: function(name) {       
        this.name = name;       
        this.createEditor(this.name);      
    },
    createEditor: function(n) {
        $(" div#module div.mod div.module_content").append('<textarea id="'+n+'">Type here...</textarea>');
        tinymce.init({
            selector:'textarea',
            plugins: ["image", "code", "media"]
        });
    }
};
/**
 * @class DateRangePicker
 * @description Wrapper object for date range picker
 * @type object
 */
var DateRangePicker = {
    init: function(name, start, end) {       
        this.name = name;
        this.start = start;
        this.end = end;
        this.createDateRangePicker();      
    },
    createDateRangePicker: function() {
        $(" div#module div.mod div.module_content").append('<input type="text" name="daterange" />');
        
        $('input[name="daterange"]').daterangepicker();
    }
};
           
var AjaxController = (function () {
        
    return {
        do_ajax: function(options){                       
            return $.ajax({
                type: "POST",
                url: options.url,
                dataType : options.dataType,
                data: options.data,
                success: function(resp){                    
                    if(resp != false){
                       return resp;
                    }            
                },
                complete: function(){

                }
            });                        
        }
    };
    
    return _results;
})();

/*var MyObject = {
    init: function(name) {       
        this.name = name;       
        this.createMyObject();      
    },
    createMyObject: function() {
        $(" div#module div.mod div.module_content").append('');              
    }
};*/