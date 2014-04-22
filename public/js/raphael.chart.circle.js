/**
 * 圈形图表
 */
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