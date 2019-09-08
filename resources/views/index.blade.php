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
@section('content')
    <div style="position: relative;height: 100%;">
        <div id="bg-container"></div>
        <!-- 点击相册弹层 -->
        <div id="album">
            <p></p>
            <img src="images/closea.png" id="closeBg" onclick="closeAlbum()" alt="">
            {{--<img src="images/pre.png" id="pre" onclick="clickPre()" alt="">--}}
            <div id="imageDiv">
                <img id="mainImg" src="" alt="">
            </div>
            {{--<img src="images/next.png" id="next" onclick="clickNext()" alt="">--}}
            {{--<img src="images/left.png" alt="" id="left" onclick="clickLeft()" style="visibility: hidden;">--}}
            {{--<div id="viewT">--}}
                {{--<ul id="thumbnail">--}}
                {{--</ul>--}}
            {{--</div>--}}
            {{--<img src="images/right.png" alt="" id="right" onclick="clickRight()" style="visibility: hidden;">--}}
            {{--<div id="addImg" onclick="document.getElementById('file1').click();">上传照片 （支持拖拽）</div>--}}
        </div>
        <div id="allmap" style="height: 100%;position: absolute;"></div>
    </div>


    <script type="text/javascript">
        let points = JSON.parse('<?php echo $points; ?>');
        // 百度地图API功能
        var map = new BMap.Map("allmap");
        map.centerAndZoom(new BMap.Point(116.404, 39.915), 5);
        map.enableScrollWheelZoom();


        var MAX = 10;
        var markers = [];
        var pt = null;

        let name, icon1, mark1, thumbnail1;
        console.log(points);
        for(name in points){
            //缩略图标示
            thumbnail1 = '/storage/thumbnails/' + name;
            icon1 = new BMap.Icon(thumbnail1,new BMap.Size(128,128));
            mark1 = new BMap.Marker(new BMap.Point(points[name]['GPSLongitude'], points[name]['GPSLatitude']),{icon: icon1, title: name});
            (function(marker, name){
                marker.addEventListener('click', function(){
                    // this.openInfoWindow(new BMap.InfoWindow(name));
                    let img = document.getElementById('mainImg');
                    img.src = '/storage/photos/' + name;
                    img.onload = function(){
                        document.getElementById('album').style.visibility = 'visible';
                        document.getElementById('bg-container').style.visibility = 'visible';
                    }
                });
            })(mark1, name);

            markers.push(mark1)
        }

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

        //最简单的用法，生成一个marker数组，然后调用markerClusterer类即可。
        var markerClusterer = new BMapLib.MarkerClusterer(map, {markers:markers});
    </script>
@endsection
