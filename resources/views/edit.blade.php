@extends('layouts.app')

@section('content')
    <div class="w-25 p-4">
        <form action="{{ route('edit', ['id' => $photo->id]) }}" method="POST" enctype="multipart/form-data" onsubmit="update()">
            @csrf
            <div style="width: 300px;height: 300px;">
                <img src="{{ asset('storage/compressed/'.$photo->filename) }}" alt="" style="height: 100%;">
            </div>
            <div class="form-group">
                <label class="col-form-label" for="longitude">经度(小数)</label>
                <input class="form-control" type="text" id="longitude" name="longitude" value="{{ $photo->longitude ?? '' }}">
            </div>
            <div class="form-group">
                <label class="col-form-label" for="latitude">纬度(小数)</label>
                <input class="form-control" type="text" id="latitude" name="latitude" value="{{ $photo->latitude ?? '' }}">
            </div>
            <input type="submit" value="提交" style="position: relative; left: 39%;">
        </form>
    </div>
@endsection

@section('script')
    <script>
        function update()
        {
            event.preventDefault();
            let obj = event.target;
            $.ajax({
                url: obj.action,
                type: 'POST',
                dataType: 'json',
                data: $(obj).serialize(),
                success: function(resp){
                    if(resp.success){
                        alert('修改成功！')
                    }else{
                        alert('修改失败！');
                    }
                }
            });
        }
    </script>    
@endsection