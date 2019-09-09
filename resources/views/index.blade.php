@extends('layouts.app')

@section('head_content')
    <style type="text/css">
        body, html {width: 100%;height: 100%;margin:0;font-family:"微软雅黑";}
        #allmap{width:100%;height: 100%;}
        p{margin-left:5px; font-size:14px;}
    </style>
    <link rel="stylesheet" href="{{ asset('css/index.css') . '?' . filemtime(public_path('css/index.css')) }}">
    <script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=iWKEGdsj2orvnolSOfF9d1qQ9EpHiumn"></script>
    <script type="text/javascript" src="https://api.map.baidu.com/library/TextIconOverlay/1.2/src/TextIconOverlay_min.js"></script>
    <script type="text/javascript" src="https://api.map.baidu.com/library/MarkerClusterer/1.2/src/MarkerClusterer_min.js"></script>
@endsection

@section('my_navbar')
    <!-- --经纬度指示器-->
    <div>当前经纬度：<span id="current_axis"></span></div>
@endsection

@section('content')
    <div style="position: relative;height: 100%;">
        <div id="bg-container"></div>
        <!-- 点击相册弹层 -->
        <div id="album">
            <p></p>
            <img src="images/closea.png" id="closeBg" onclick="closeAlbum()" alt="">
            <div id="imageDiv">
                <img id="mainImg" src="" alt="">
            </div>
            <img src="images/pre.png" id="pre" onclick="clickPre()" alt="">
            <img src="images/next.png" id="next" onclick="clickNext()" alt="">
            <img src="images/left.png" alt="" id="left" onclick="clickLeft()" style="visibility: hidden;">
            <div id="viewT">
                <ul id="thumbnail">
                </ul>
            </div>
            <img src="images/right.png" alt="" id="right" onclick="clickRight()" style="visibility: hidden;">
        </div>
        <div id="allmap" style="height: 100%;position: absolute;"></div>
    </div>



    <script type="text/javascript">
        let points = JSON.parse('<?php echo $points; ?>');
        // 百度地图API功能
        var map = new BMap.Map("allmap");
        map.centerAndZoom(new BMap.Point(116.404, 39.915), 5);
        map.enableScrollWheelZoom();

        /*
        function throttle(fn, interval) {
            let last = 0
            return function () {
                let context = this
                let args = arguments
                let now = +new Date()
                if (now - last >= interval) {
                    last = now;
                    fn.apply(context, args);
                }
            }
        }
        const hover_axis = throttle(function(e){
            console.log('sss')
            document.getElementById('current_axis').innerText = e.point.lng + ", " + e.point.lat;
        }, 1000);
        map.addEventListener('click', function(e){return hover_axis(e);});
        */

        var markers = [];
        let name, icon1, mark1, thumbnail1;
        console.log(points);
        for(name in points){
            if(!points[name]['GPSLongitude']) continue;
            //缩略图标示
            thumbnail1 = '/storage/thumbnails/' + name;
            icon1 = new BMap.Icon(thumbnail1,new BMap.Size(128,128));
            mark1 = new BMap.Marker(new BMap.Point(points[name]['GPSLongitude'], points[name]['GPSLatitude']),{icon: icon1, title: name});
            markers.push(mark1)
        }


        markerClustersPoint();
        map.addEventListener('zoomend', function () {
            markerClustersPoint(markers)
        });


        //修改点聚合显示方式，点击聚合点改为显示相册而非散点
        function markerClustersPoint()
        {
            let markerClusterer;
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

            // 拿到所有的聚合点
            //markerClusterer中的 _clusters是一个数组，包含了可视范围的所有聚合点
            var clusters = markerClusterer._clusters;
            var oldmk = [];

            //所有聚合
            for (var i = 0; i < clusters.length; i++) {
                //cluster[i]._markers中包含此聚合点的所有marker集合
                //marker长度大于2时不进行聚合效果显示
                // if (cluster[i]._markers.length < 2) continue;
                //自定义函数内容，可进行聚合点数据获取操作
                //......
                //......
                //添加marker
                oldmk.push(addMarkerCluser(clusters[i]));
            }


        }

        // 标记自定义marker
        function addMarkerCluser(cluster)
        {
            var markerdef = new BMap.Marker(cluster._center, {
                // icon: 设置marker样式
                icon: new BMap.Symbol(BMap_Symbol_SHAPE_CIRCLE, {
                    scale: 20,
                    strokeWeight: 1,
                    strokeColor: "white",
                    fillColor: "blue",
                    fillOpacity: 0.59
                }),
            });
            //设置marker的label
            let label = new BMap.Label(cluster._markers.length, {
                offset: new BMap.Size(15, 12)
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
            markerdef.addEventListener("click", function () {
                console.log("点击自定义聚合maker");

                let cluster_markers = cluster._markers;
                var thumbnail = document.getElementById('thumbnail')
                thumbnail.innerHTML = "";

                //大图
                let img = document.getElementById('mainImg');
                img.src = '/storage/photos/' + cluster_markers[0]['z']['title'];
                img.onload = function(){
                    document.getElementById('album').style.visibility = 'visible';
                    document.getElementById('bg-container').style.visibility = 'visible';
                };
                //缩略图列表
                for (let i = 0; i < cluster_markers.length; i++) {
                    let url = '/storage/thumbnails/' + cluster_markers[i]['z']['title'];

                    if( i == 0 ){
                        let li = document.createElement("li");
                        li.setAttribute("id","on");
                        let newImg = document.createElement("img");
                        newImg.setAttribute("src", url);
                        newImg.setAttribute("onclick","imgOn(this)");
                        li.appendChild(newImg);
                        thumbnail.appendChild(li);
                    }else {
                        let li = document.createElement("li");
                        let newImg = document.createElement("img");
                        newImg.setAttribute("src", url);
                        newImg.setAttribute("onclick","imgOn(this)");
                        li.appendChild(newImg);
                        thumbnail.appendChild(li);
                        if(i > 7){
                            document.getElementById('left').style.visibility = "visible";
                            document.getElementById('right').style.visibility = "visible";
                        }
                    }

                }

            });
            map.addOverlay(markerdef);
            return markerdef;
        }
    </script>



    <script>
        //关闭相册
        function closeAlbum()
        {
            var bg = document.getElementById('bg-container');
            bg.style.visibility = 'hidden';
            var album = document.getElementById('album');
            album.style.visibility = 'hidden';
            // document.getElementById('left').style.visibility = "hidden";
            // document.getElementById('right').style.visibility = "hidden";
        }

        function photo_url(thumbnail_url)
        {
            return thumbnail_url.replace('/thumbnails/', '/photos/');
        }
        //添加选中
        function imgOn(e)
        {
            var lili = document.getElementById('thumbnail').getElementsByTagName('li');
            for(let i = 0;i < lili.length;i++ ){
                lili[i].id = "";
            }
            var li = e.parentElement
            li.setAttribute("id","on");
            document.getElementById('mainImg').setAttribute("src", photo_url(e.src));
        }
        //点击上一张
        function clickPre()
        {
            let on = document.getElementById("on");
            if(on.parentNode.firstChild == on){
                return;
            }else {
                let ps = on.previousSibling;
                let lili = on.parentNode.childNodes;
                for(let i = 0;i < lili.length;i++ ){
                    lili[i].id = "";
                }
                ps.id = "on";
                document.getElementById('mainImg').setAttribute("src", photo_url(ps.firstChild.src));
                for(var i = 0; on.parentNode.childNodes[i].id !== "on"; i++);
                let viewT = on.parentNode.parentNode;
                if(i*100 - viewT.scrollLeft < 0){
                    viewT.scrollLeft -= 100;
                }
            }
        }
        //点击下一张
        function clickNext()
        {
            let on = document.getElementById("on");
            if(on.parentNode.lastChild == on){
                return;
            }else {
                let ps = on.nextSibling;
                let lili = on.parentNode.childNodes;
                for(let i = 0;i < lili.length;i++ ){
                    lili[i].id = "";
                }
                ps.id = "on";
                document.getElementById('mainImg').setAttribute("src", photo_url(ps.firstChild.src));
                for(var i = 0; on.parentNode.childNodes[i].id !== "on"; i++);
                let viewT = on.parentNode.parentNode;
                if(i*100 - viewT.scrollLeft > 700){
                    viewT.scrollLeft += 100;
                }



            }
        }
    </script>
@endsection
