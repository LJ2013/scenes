@extends('layouts.app')

@section('head_content')
    <style>
        ul.photos{
            list-style: none;
            background-color: black;
            display: block;
        }
        ul.photos li{
            float: left;
            margin: 5px;
            padding: 5px;
            box-shadow:4px 4px 6px gray;
            width: 200px;
        }
        ul.photos li img{
            width: 100%;
        }
        .clearfix{
            clear: both;
        }
        #show_photo{
            width: 100%;
        }

        .bar{
            padding: 2px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    </style>
@endsection
@section('content')
<!--
<div class="alert alert-success alert-dismissible" role="alert" id="msgbox" style="display: none">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>上传成功!</strong>
</div>
-->

<div class="bar">
    <div><button onclick="$('#myModal').modal('show');" class="btn btn-primary ml-2 mr-2">上传</button></div>
    <div class="d-flex flex-nowrap align-items-center pt-lg-3">{!! $photos->appends(request()->all())->links() !!}</div>
</div>

<ul class="photos">
    @foreach($photos as $photo)
    <li>
        <img src="{{ 'storage/thumbnails/'.$photo->filename }}" alt="经纬度：{{ $photo->longitude . ', ' . $photo->latitude }}" name="{{ $photo->filename }}">
    </li>
    @endforeach
</ul>

<div id="myModal" class="modal fade m-auto" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog w-75">
        <div class="modal-content">
            <div class="modal-body" style="word-break: break-all">
                <form action="{{ route('upload') }}" method="POST" onsubmit="upload()" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="photo" id="photo">
                    <input type="submit" value="上传">
                </form>
            </div>
        </div>
    </div>
</div>

<div id="show_photo_modal" class="modal fade m-auto" tabindex="-1" role="dialog" style="display: none">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" style="word-break: break-all">
                <img src="" alt="" id="show_photo">
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        $('.photos li img').click(function(){
            obj = this;
            let show_photo = document.getElementById('show_photo');
            show_photo.src = 'storage/photos/' + obj.name;
            show_photo.onload = function(){
                $("#show_photo_modal").modal('show');
            };
        });

        function upload()
        {
            event.preventDefault();
            let obj = event.target;
            let formData = new FormData();
            formData.append('_token', $('input[name=_token]').val());
            formData.append('photo', document.getElementById('photo').files[0]);

            $.ajax({
                url: obj.action,
                type: 'POST',
                dataType: 'json',
                data: formData,
                processData: false, //关闭序列化
                contentType: false,
                success: function(resp){
                    if(resp.success){
                        $('#myModal').modal('hide');
                        window.location.reload();
                    }
                }
            });
        }
    </script>
@endsection