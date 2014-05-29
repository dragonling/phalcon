moment.lang('zh-cn', {
    months : "一月_二月_三月_四月_五月_六月_七月_八月_九月_十月_十一月_十二月".split("_"),
    monthsShort : "1月_2月_3月_4月_5月_6月_7月_8月_9月_10月_11月_12月".split("_"),
    weekdays : "星期日_星期一_星期二_星期三_星期四_星期五_星期六".split("_"),
    weekdaysShort : "周日_周一_周二_周三_周四_周五_周六".split("_"),
    weekdaysMin : "日_一_二_三_四_五_六".split("_"),
    longDateFormat : {
        LT : "Ah点mm",
        L : "YYYY-MM-DD",
        LL : "YYYY年MMMD日",
        LLL : "YYYY年MMMD日LT",
        LLLL : "YYYY年MMMD日ddddLT",
        l : "YYYY-MM-DD",
        ll : "YYYY年MMMD日",
        lll : "YYYY年MMMD日LT",
        llll : "YYYY年MMMD日ddddLT"
    },
    meridiem : function (hour, minute, isLower) {
        var hm = hour * 100 + minute;
        if (hm < 600) {
            return "凌晨";
        } else if (hm < 900) {
            return "早上";
        } else if (hm < 1130) {
            return "上午";
        } else if (hm < 1230) {
            return "中午";
        } else if (hm < 1800) {
            return "下午";
        } else {
            return "晚上";
        }
    },
    calendar : {
        sameDay : function () {
            return this.minutes() === 0 ? "[今天]Ah[点整]" : "[今天]LT";
        },
        nextDay : function () {
            return this.minutes() === 0 ? "[明天]Ah[点整]" : "[明天]LT";
        },
        lastDay : function () {
            return this.minutes() === 0 ? "[昨天]Ah[点整]" : "[昨天]LT";
        },
        nextWeek : function () {
            var startOfWeek, prefix;
            startOfWeek = moment().startOf('week');
            prefix = this.unix() - startOfWeek.unix() >= 7 * 24 * 3600 ? '[下]' : '[本]';
            return this.minutes() === 0 ? prefix + "dddAh点整" : prefix + "dddAh点mm";
        },
        lastWeek : function () {
            var startOfWeek, prefix;
            startOfWeek = moment().startOf('week');
            prefix = this.unix() < startOfWeek.unix()  ? '[上]' : '[本]';
            return this.minutes() === 0 ? prefix + "dddAh点整" : prefix + "dddAh点mm";
        },
        sameElse : 'LL'
    },
    ordinal : function (number, period) {
        switch (period) {
            case "d":
            case "D":
            case "DDD":
                return number + "日";
            case "M":
                return number + "月";
            case "w":
            case "W":
                return number + "周";
            default:
                return number;
        }
    },
    relativeTime : {
        future : "%s内",
        past : "%s前",
        s : "几秒",
        m : "1分钟",
        mm : "%d分钟",
        h : "1小时",
        hh : "%d小时",
        d : "1天",
        dd : "%d天",
        M : "1个月",
        MM : "%d个月",
        y : "1年",
        yy : "%d年"
    },
    week : {
        // GB/T 7408-1994《数据元和交换格式·信息交换·日期和时间表示法》与ISO 8601:1988等效
        dow : 1, // Monday is the first day of the week.
        doy : 4  // The week that contains Jan 4th is the first week of the year.
    }
});
//
moment.lang('zh-cn');
//
Highcharts.setOptions({
    global: {
        useUTC: false
    }
});

//添加 trim 方法
if (!String.prototype.trim) {
    String.prototype.trim = function () {
        return this.replace(/^\s+|\s+$/g, '');
    };
}

var tool = {
    /**
     * 转换dom上设置的data-?的值转换为js对象
     * @param str
     * @returns
     */
    parseDomData : function(str) {
        if (typeof str !== 'string') {
            return ;
        }
        //str = str.replace(/\s/g, '');
        var array = str.split(';');
        var obj = {};
        for (var l=array.length-1; l>-1; l--) {
            var item = array[l];
            var index = item.indexOf(':');
            if (index > -1 && (index < item.length - 1)) {
                var key = item.substring(0, index).trim();
                key = key.replace(/-\w/g, function(word) {
                    return word.charAt(1).toUpperCase();
                });
                var value = item.substring(index+1).trim();
                //类型转换 boolean
                switch (value) {
                    case 'true':
                        value = true;
                        break;
                    case 'false':
                        value = false;
                        break;
                }
                //类型转换 number
                if (/^(-?\d+)(\.\d+)?$/.test(value)) {
                    value = + value;
                }
                obj[key] = value;
            }
        }
        return obj;
    }
};
