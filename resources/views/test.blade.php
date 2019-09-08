<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
        body, html {width: 100%;height: 100%;margin:0;font-family:"微软雅黑";}
        #allmap{width:100%;height:800px;}
        p{margin-left:5px; font-size:14px;}
    </style>
    <script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=iWKEGdsj2orvnolSOfF9d1qQ9EpHiumn"></script>
    <script type="text/javascript" src="https://api.map.baidu.com/library/TextIconOverlay/1.2/src/TextIconOverlay_min.js"></script>
    <script type="text/javascript" src="https://api.map.baidu.com/library/MarkerClusterer/1.2/src/MarkerClusterer_min.js"></script>
    <title>点聚合</title>
</head>
<body>
<div id="allmap"></div>
<p>缩放地图，查看点聚合效果</p>
</body>
</html>
<script type="text/javascript">
    // 百度地图API功能
    var map = new BMap.Map("allmap");
    map.centerAndZoom(new BMap.Point(116.404, 39.915), 5);
    // map.enableScrollWheelZoom();

    var datas = [{
        'localtion': '120.585239,31.298881'
    }, {
        'localtion': '120.585239,31.298881'
    }, {
        'localtion': '120.585239,31.298881'
    }, {
        'localtion': '120.585239,31.298881'
    }, {
        'localtion': '120.585239,31.298881'
    }];
    //先创建初始marker点
    var markers = [];
    for (var i = 0; i < datas.length; i++) {
        var data = datas[i];
        var localtion = data.localtion.split(',');
        var m = new BMap.Marker(new BMap.Point(localtion[0], localtion[1]));
        m.data = data;
        markers.push(m);
    }
    //调用聚合封装函数
    markerClustersPoint(markers);
    //
    //地图缩放重新计算聚合点
    map.addEventListener("zoomend",function(){
        markerClustersPoint(markers);
    });

    //聚合添加
    function markerClustersPoint(markers){
        if (markerClusterer) {
            markerClusterer.clearMarkers();//清除聚合
        }
        markerClusterer = new BMapLib.MarkerClusterer(this.map, {
            markers: markers,
            minClusterSize: 3, //最小的聚合数量，小于该数量的不能成为一个聚合，默认为2
            styles: [
                {
                    //此处仅放置style，不要写任何内容，否则会有默认聚合的数字显示溢出
                    // url: "img/info.png",
                    // size: new BMap.Size(0, 0)
                }
            ]
        });
        console.log("进入聚合函数markerClusterer", markerClusterer);
        //
        // 拿到所有的聚合点
        //markerClusterer中的 _clusters是一个数组，包含了可视范围的所有聚合点
        var cluster = markerClusterer._clusters;
        var oldmk = [];
        for (var i = 0; i < cluster.length; i++) {
            //cluster[i]._markers中包含此聚合点的所有marker集合
            //marker长度大于2时不进行聚合效果显示
            if (cluster[i]._markers.length < 2) continue;
            //自定义函数内容，可进行聚合点数据获取操作
            //......
            //......
            //拿到聚合点中的marker数量，用于数字显示
            var cluserMakerSum = cluster[i]._markers.length;
            //添加marker
            oldmk.push(addMarkerCluser(cluster[i]._center));
        }
    };
    // 标记自定义marker
    function addMarkerCluser(point) {
        var markerdef = new BMap.Marker(point, {
            // icon: 设置marker样式
            icon: new BMap.Symbol(BMap_Symbol_SHAPE_CIRCLE, {
                scale: 20,
                strokeWeight: 1,
                strokeColor: "white",
                fillColor: "blue",
                fillOpacity: 0.59
            })
        });
        //设置marker的label
        var labelTitleCluser = cluserMakerSum ;
        let label = new BMap.Label(labelTitleCluser, {
            offset: new BMap.Size(12, 12)
        });
        //设置label样式
        label.setStyle({
            color: "#fff",
            fontSize: "14px",
            backgroundColor: "0.05",
            border: "0px "
        });
        markerdef.setLabel(label);
        //监听点击事件
        markerdef.addEventListener("click", function() {
            console.log("点击自定义聚合maker");
        });
        map.addOverlay(markerdef);
        //
        return markerdef;
    }

</script>
