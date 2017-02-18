/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*var MyObject = {
    init: function(name) {       
        this.name = name;       
        this.createMyObject();      
    },
    createMyObject: function() {
        $(" div#module div.mod div.module_content").append('');              
    }
};*/

var Waves = {
    init: function(name) {       
        this.name = name;       
        this.createWaves();      
    },
    createWaves: function() {
        $(" div#module div.mod div.module_content").append('');              
    }
};

var Gears = {
    init: function(name) {       
        this.name = name;       
        this.createGears();      
    },
    createGears: function() {
        
        var width = 910,
            height = 450,
            radius = 60,
            x = Math.sin(2 * Math.PI / 3),
            y = Math.cos(2 * Math.PI / 3);

        var offset = 0,
            speed = 4,
            start = Date.now();

        var svg = d3.select("div.d3space").append("svg")
            .attr("width", width)
            .attr("height", height)
          .append("g")
            .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")scale(.55)")
          .append("g");

        var frame = svg.append("g")
            .datum({radius: Infinity});
    
        frame.append("g")
            .attr("class", "annulus")
            .datum({teeth: 80, radius: -radius * 5, annulus: true})
          .append("path")
            .attr("d", gear);

        frame.append("g")
            .attr("class", "sun")
            .datum({teeth: 16, radius: radius})
          .append("path")
            .attr("d", gear);

        frame.append("g")
            .attr("class", "planet")
            .attr("transform", "translate(0,-" + radius * 3 + ")")
            .datum({teeth: 32, radius: -radius * 2})
          .append("path")
            .attr("d", gear);

        frame.append("g")
            .attr("class", "planet")
            .attr("transform", "translate(" + -radius * 3 * x + "," + -radius * 3 * y + ")")
            .datum({teeth: 32, radius: -radius * 2})
          .append("path")
            .attr("d", gear);

        frame.append("g")
            .attr("class", "planet")
            .attr("transform", "translate(" + radius * 3 * x + "," + -radius * 3 * y + ")")
            .datum({teeth: 32, radius: -radius * 2})
          .append("path")
            .attr("d", gear);
    
        $("path").css({"fill-rule":"evenodd","stroke":"#333","stroke-width":"2px"});
        $(".planet path").css({"fill":"#6baed6"});
        $(".sun path").css({"fill":"#6baed6"});
        $(".planet path").css({"fill":"#9ecae1"});
        $(".annulus path").css({"fill":"#c6dbef"});

        d3.selectAll("input[name=reference]")
          .data([radius * 5, Infinity, -radius])
            .on("change", function(radius1) {
              var radius0 = frame.datum().radius, angle = (Date.now() - start) * speed;
              frame.datum({radius: radius1});
              svg.attr("transform", "rotate(" + (offset += angle / radius0 - angle / radius1) + ")");
            });

        d3.selectAll("input[name=speed]")
            .on("change", function() { speed = +this.value; });

        function gear(d) {
            var n = d.teeth,
                r2 = Math.abs(d.radius),
                r0 = r2 - 8,
                r1 = r2 + 8,
                r3 = d.annulus ? (r3 = r0, r0 = r1, r1 = r3, r2 + 20) : 20,
                da = Math.PI / n,
                a0 = -Math.PI / 2 + (d.annulus ? Math.PI / n : 0),
                i = -1,
                path = ["M", r0 * Math.cos(a0), ",", r0 * Math.sin(a0)];
            while (++i < n) path.push(
                "A", r0, ",", r0, " 0 0,1 ", r0 * Math.cos(a0 += da), ",", r0 * Math.sin(a0),
                "L", r2 * Math.cos(a0), ",", r2 * Math.sin(a0),
                "L", r1 * Math.cos(a0 += da / 3), ",", r1 * Math.sin(a0),
                "A", r1, ",", r1, " 0 0,1 ", r1 * Math.cos(a0 += da / 3), ",", r1 * Math.sin(a0),
                "L", r2 * Math.cos(a0 += da / 3), ",", r2 * Math.sin(a0),
                "L", r0 * Math.cos(a0), ",", r0 * Math.sin(a0));
            path.push("M0,", -r3, "A", r3, ",", r3, " 0 0,0 0,", r3, "A", r3, ",", r3, " 0 0,0 0,", -r3, "Z");
            return path.join("");
        }

        d3.timer(function() {
          var angle = (Date.now() - start) * speed,
              transform = function(d) { return "rotate(" + angle / d.radius + ")"; };
          frame.selectAll("path").attr("transform", transform);
          frame.attr("transform", transform); // frame of reference
        });
    }
};
