/**
 * 圈形图表
 */
(function(){
    function CircleChart(option) {
        this.paper = option.paper;
        this.cx = option.cx;
        this.cy = option.cy;
        this.or = option.or;
        this.ir = option.ir;
        this.title = option.title;
        this.data = option.data;
        this.style = option.style;
        this.init(option);
    };
    CircleChart.prototype.init = function(option) {
        this.chart = {
            sectors: [],
            texts  : [],
            labels : []
        };
        this.values = [];
        this.labels = [];
        this.colors = ['#d27569', '#60d38a', '#666'];
        this.defaultColor = '#333';
        this.total;
        this.angle;
        this.rad = Math.PI / 180;
        this.initData(option.data);
    };
    CircleChart.prototype.initData = function(data) {
        this.parseData(data);
        this.process();
    };
    CircleChart.prototype.parseData = function(data) {
        this.total = 0;
        for (var l = data.length - 1; l > -1; l--) {
            if (typeof data[l] === 'object') {
                this.values[l] = parseFloat(data[l].value);
                this.labels[l] = data[l].label;
                this.colors[l] = data[l].color || this.colors[l] || this.defaultColor;
            } else {
                this.values[l] = parseFloat(data[l]);
                this.labels[l] = '';
                this.colors[l] = this.colors[l] || this.defaultColor;
            }
            this.total += this.values[l];
        }
    };
    CircleChart.prototype.updateData = function(data) {
        this.parseData(data);
        this.paper.clear();
        this.process();
    };
    CircleChart.prototype.path = function(cx, cy, or, ir, startAngle, endAngle) {
        var outer = {},
            inner = {};
        var flag = 0;
        if (endAngle - startAngle > 180) {
            flag = 1;
        }
        outer.x1 = cx + or * Math.cos(-startAngle * this.rad);
        outer.x2 = cx + or * Math.cos(-endAngle * this.rad);
        outer.y1 = cy + or * Math.sin(-startAngle * this.rad);
        outer.y2 = cy + or * Math.sin(-endAngle * this.rad);
        inner.x1 = cx + ir * Math.cos(-startAngle * this.rad);
        inner.x2 = cx + ir * Math.cos(-endAngle * this.rad);
        inner.y1 = cy + ir * Math.sin(-startAngle * this.rad);
        inner.y2 = cy + ir * Math.sin(-endAngle * this.rad);
        return [
            "M", outer.x1, outer.y1, "A", or, or, 0, flag, 0, outer.x2, outer.y2,
            "L", inner.x2, inner.y2, "A", ir, ir, 0, flag, 1, inner.x1, inner.y1, "z"
        ];
    };
    CircleChart.prototype.process = function() {
        var ms = 500, delta = 5;
        var angle = 90;
        var total = this.total;
        for (var l = this.values.length - 1; l > -1; l--) {
            var value = this.values[l];
            var pathAngle = 360 * value / total;
            var textAngle = angle + pathAngle / 2;
            var path = this.path(this.cx, this.cy, this.or, this.ir, angle, angle + pathAngle);
            this.paper.path(path).attr({fill: this.colors[l], stroke: '#fff', "stroke-width": 1});
            this.paper.text(
                this.cx + (this.ir + 15) * Math.cos(-textAngle * this.rad),
                this.cy + (this.ir + 15) * Math.sin(-textAngle * this.rad),
                Math.floor(value * 100 / total) + '%'
            ).attr({
                fill: '#fff',
                stroke: "none",
                "font-size": 12,
                "font-weight": "bold",
                "font-family": "Consolas, monospace"
            });
            //
            //this.chart.sectors.push(sector);
            //this.chart.texts.push(text);
            angle += pathAngle;
        }
        this.paper.text(this.cx, this.cy, this.title).attr({
            fill: '#fff',
            stroke: "none",
            "font-size": 14,
            "font-family": 'Verdana Tahoma "Hiragino Sans GB", "Microsoft YaHei", "WenQuanYi Micro Hei", sans-serif'
        });
    };

    Raphael.fn.circleChart = function (inputOptions) {
        inputOptions.paper = this;
        return new CircleChart(inputOptions);
    }
})();
/*
Raphael.fn.circleChart = function (cx, cy, or, ir, data, stroke) {
    var paper = this,
        rad = Math.PI / 180,
        chart = this.set(),
        angle = 90,
        total = 0,
        values = [],
        labels = [],
        colors = ['#d27569', '#60d38a', '#666'],
        defaultColor = '#333',
        l;
    for (l = data.length - 1; l > -1; l--) {
        if (typeof data[l] === 'object') {
            values[l] = parseFloat(data[l].value);
            labels[l] = data[l].label;
            colors[l] = data[l].color || colors[l] || defaultColor;
        } else {
            values[l] = parseFloat(data[l]);
            labels[l] = '';
            colors[l] = colors[l] || defaultColor;
        }
        total += values[l];
    }
    for (l = data.length - 1; l > -1; l--) {
        process(l);
    }
    return chart;

    function process(i) {
        var ms = 500, delta = 5;
        var value = values[i];
        var pathAngle = 360 * value / total;
        var textAngle = angle + pathAngle / 2;
        var path = sector(cx, cy, or, ir, angle, angle + pathAngle, {fill: colors[i], stroke: stroke, "stroke-width": 1});
        var text = paper.text(
            cx + (ir + 15) * Math.cos(-textAngle * rad),
            cy + (ir + 15) * Math.sin(-textAngle * rad),
            Math.floor(value * 100 / total) + '%'
        ).attr(
            { fill: '#fff', stroke: "none", "font-size": 14, "font-weight": "bold", "font-family": "Consolas, monospace"}
        );
        //
        angle += pathAngle;
        chart.push(path);
        chart.push(text);
    };
    function sector(cx, cy, or, ir, startAngle, endAngle, params) {
        var outer = {},
            inner = {};
        var flag = 0;
        if (endAngle - startAngle > 180) {
            flag = 1;
        }
        outer.x1 = cx + or * Math.cos(-startAngle * rad);
        outer.x2 = cx + or * Math.cos(-endAngle * rad);
        outer.y1 = cy + or * Math.sin(-startAngle * rad);
        outer.y2 = cy + or * Math.sin(-endAngle * rad);
        inner.x1 = cx + ir * Math.cos(-startAngle * rad);
        inner.x2 = cx + ir * Math.cos(-endAngle * rad);
        inner.y1 = cy + ir * Math.sin(-startAngle * rad);
        inner.y2 = cy + ir * Math.sin(-endAngle * rad);
        return paper.path([
            "M", outer.x1, outer.y1, "A", or, or, 0, flag, 0, outer.x2, outer.y2,
            "L", inner.x2, inner.y2, "A", ir, ir, 0, flag, 1, inner.x1, inner.y1, "z"
        ]).attr(params);
    }
};
*/
