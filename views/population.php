<?php
$data_array = json_decode($data);

$temp = [];
$total = [];

if(isset($data_array->year)) {
    foreach ($data_array->year as $k => $t) {
        $timestamp = $t->time;

        if ($this->radix->archive) {
            $datetime = new \DateTime(date('Y-m-d H:i:s', $timestamp), new \DateTimeZone('America/New_York'));
            $datetime->setTimezone(new \DateTimeZone('UTC'));
            $timestamp = strtotime($datetime->format('Y-m-d H:i:s'));
        }

        $temp[] = [
            'group' => 'anons',
            'time' => $timestamp,
            'count' => $t->anons
        ];
        $temp[] = [
            'group' => 'names',
            'time' => $timestamp,
            'count' => $t->names
        ];
        $temp[] = [
            'group' => 'trips',
            'time' => $timestamp,
            'count' => $t->trips
        ];
    }
}

// totals
if (isset($data_array->total)) {
    foreach ($data_array->total as $k => $t) {
        $timestamp = $t->time;

        if ($this->radix->archive) {
            $datetime = new \DateTime(date('Y-m-d H:i:s', $timestamp), new \DateTimeZone('America/New_York'));
            $datetime->setTimezone(new \DateTimeZone('UTC'));
            $timestamp = strtotime($datetime->format('Y-m-d H:i:s'));
        }

        $total[] = [
            'group' => 'anons',
            'time' => $timestamp,
            'count' => $t->anons
        ];
        $total[] = [
            'group' => 'names',
            'time' => $timestamp,
            'count' => $t->names
        ];
        $total[] = [
            'group' => 'trips',
            'time' => $timestamp,
            'count' => $t->trips
        ];
    }
}

// manually truncate results
array_pop($temp);
array_pop($temp);
array_pop($temp);

array_pop($total);
array_pop($total);
array_pop($total);

// create an empty nest data set
if (empty($temp)) {
    $temp[] = [
        'group' => 'anons',
        'time' => 0,
        'count' => 0
    ];
    $temp[] = [
        'group' => 'names',
        'time' => 0,
        'count' => 0
    ];
    $temp[] = [
        'group' => 'trips',
        'time' => 0,
        'count' => 0
    ];
}
// create an empty nest data set
if (empty($total)) {
    $total[] = [
        'group' => 'anons',
        'time' => 0,
        'count' => 0
    ];
    $total[] = [
        'group' => 'names',
        'time' => 0,
        'count' => 0
    ];
    $total[] = [
        'group' => 'trips',
        'time' => 0,
        'count' => 0
    ];
}
?>

<div id="graphs"></div>

<script src="<?= $this->plugin->getAssetManager()->getAssetLink('d3/d3.v3.min.js') ?>" type="text/javascript"></script>
<script>
    // data
    data_board = <?= json_encode($temp) ?>;
    data_total = <?= json_encode($total) ?>;
    var ggg = document.getElementById("graphs");

    data_board.forEach(function(d) {
        d.time = new Date(d.time * 1000);
        d.count = +d.count;
    });
    data_total.forEach(function(d) {
        d.time = new Date(d.time * 1000);
        d.count = +d.count;
    });
    // d3.js
    var  m = [20, 30, 30, 60], w, h, x, y, z, color, xAxis, xAxisScaler, yAxis, stack, nest, line, svg_board, svg_total, lineAvg, area, layers;
    function updatewh(wind) {
        w = wind - m[2] - m[1];
        h = .7 * w;
    }
    render();
    window.addEventListener('resize', render);
    function render() {
        var newwidth = 0;
        var ticks = 0;
        if (document.getElementById("main").clientWidth >= 960) {
            newwidth = 960;
            ticks = 1;
        } else {
            newwidth = document.getElementById("main").clientWidth - 30;
            ticks = 3;
        }
        updatewh(newwidth);

        x = d3.time.scale().range([0, w]);
        y = d3.scale.linear().range([h, 0]);

        color = d3.scale.ordinal()
            .range(["#008000", "#ff0000", "#0000ff"]);
        xAxis = d3.svg.axis()
            .scale(x)
            .orient("bottom")
            .ticks(d3.time.months, ticks);
        yAxis = d3.svg.axis()
            .scale(y)
            .orient("left");
        stack = d3.layout.stack()
            .offset("zero")
            .values(function (d) {
                return d.values;
            })
            .x(function (d) {
                return d.time;
            })
            .y(function (d) {
                return d.count;
            });
        nest = d3.nest()
            .key(function (d) {
                return d.group
            });
        line = d3.svg.line()
            .interpolate("basis")
            .x(function (d) {
                return x(d.time);
            })
            .y(function (d) {
                return y(d.y);
            });
        area = d3.svg.area()
            .interpolate("basis")
            .x(function (d) {
                return x(d.time);
            })
            .y0(h)
            .y1(function (d) {
                return y(d.y);
            });

        // graph
        ggg.innerHTML = '';
        svg_board = d3.select("#graphs").append("svg")
            .attr("width", w + m[3] + m[1])
            .attr("height", h + m[0] + m[2])
            .append("g")
            .attr("transform", "translate(" + m[3] + "," + m[0] + ")");

        layers = stack(nest.entries(data_board));
        x.domain(d3.extent(data_board, function (d) {
            return d.time;
        })).range([0, w]);
        y.domain([0, d3.max(data_board, function (d) {
            return d.y + d.y0 + 2;
        })]).range([h, 0]);

        svg_board.selectAll(".layer")
            .data(layers)
            .enter().append("path")
            .attr("class", "layer")
            .attr("d", function (d) {
                return area(d.values);
            })
            .style("fill", function (d, i) {
                return color(i);
            })
            .attr("fill-opacity", ".2");

        svg_board.selectAll(".line")
            .data(layers)
            .enter().append("path")
            .attr("class", "line")
            .attr("stroke", function (d, i) {
                return color(i);
            })
            .attr("d", function (d) {
                return line(d.values);
            })
            .style("fill", "none");

        svg_board.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + h + ")")
            .call(xAxis);

        svg_board.append("g")
            .attr("class", "y axis")
            .call(yAxis);

        svg_board.append("text")
            .attr("x", 10)
            .attr("dy", 20)
            .text("Population (Year)")
            .style("font-weight", "bold");

        // total graph
        svg_total = d3.select("#graphs").append("svg")
            .attr("width", w + m[3] + m[1])
            .attr("height", h + m[0] + m[2])
            .append("g")
            .attr("transform", "translate(" + m[3] + "," + m[0] + ")");

        layers = stack(nest.entries(data_total));
        x.domain(d3.extent(data_total, function (d) {
            return d.time;
        })).range([0, w]);
        y.domain([0, d3.max(data_total, function (d) {
            return d.y + d.y0 + 2;
        })]).range([h, 0]);

        svg_total.selectAll(".layer")
            .data(layers)
            .enter().append("path")
            .attr("class", "layer")
            .attr("d", function (d) {
                return area(d.values);
            })
            .style("fill", function (d, i) {
                return color(i);
            })
            .attr("fill-opacity", ".2");

        svg_total.selectAll(".line")
            .data(layers)
            .enter().append("path")
            .attr("class", "line")
            .attr("stroke", function (d, i) {
                return color(i);
            })
            .attr("d", function (d) {
                return line(d.values);
            })
            .style("fill", "none");

        svg_total.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + h + ")")
            .call(xAxis);

        svg_total.append("g")
            .attr("class", "y axis")
            .call(yAxis);

        svg_total.append("text")
            .attr("x", 10)
            .attr("dy", 20)
            .text("Population (Total)")
            .style("font-weight", "bold");

        // graph legend
        d3.selectAll("svg").each(function (d) {
            e = d3.select(this);

            e.append("rect")
                .attr("x", newwidth - 140)
                .attr("y", 15)
                .attr("width", 25).attr("height", 15)
                .style("stroke", color(0))
                .style("stroke-width", "1px")
                .style("fill", color(0))
                .attr("fill-opacity", ".2");

            e.append("text")
                .attr("x", newwidth - 100)
                .attr("y", 27.5)
                .text("Anons");

            e.append("rect")
                .attr("x", newwidth - 140)
                .attr("y", 40)
                .attr("width", 25).attr("height", 15)
                .style("stroke", color(1))
                .style("stroke-width", "1px")
                .style("fill", color(1))
                .attr("fill-opacity", ".2");

            e.append("text")
                .attr("x", newwidth - 100)
                .attr("dy", 52.5)
                .text("Namefags");

            e.append("rect")
                .attr("x", newwidth - 140)
                .attr("y", 65)
                .attr("width", 25).attr("height", 15)
                .style("stroke", color(2))
                .style("stroke-width", "1px")
                .style("fill", color(2))
                .attr("fill-opacity", ".2");

            e.append("text")
                .attr("x", newwidth - 100)
                .attr("dy", 77.5)
                .text("Tripfriends");
        });
    }
</script>
