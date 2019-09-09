<?php

namespace App\Http\Controllers;

use App\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GeoController extends Controller
{
    public function test()
    {
        return view('test');
    }
    //图片管理页面
    public function index()
    {
//        $photos = Photo::all();
//        $photos = $photos->toJson();
        $photos = $this->photos();
        return view('index', ['points' => $photos]);
    }

    public function upload(Request $request)
    {
        if($request->getMethod() == 'GET'){
            return view('upload');
        }
        if(!$request->hasFile('photo') || !($file = $request->file('photo'))->isValid()){
            return;
        }
        $file = $request->file('photo');

        $name = $file->getClientOriginalName();
        if(Photo::where('filename', $name)->exists()){
            $data = ['success' => false, 'message' => '失败：存在同名文件！'];
        }else{
//            $success = $file->storeAs($dir, $uniqueName); //写不上的bug
            $dir = storage_path('app/public/photos');
            $pathname = "$dir/$name";
            move_uploaded_file($_FILES['photo']['tmp_name'], $pathname);
            $gps = current($this->gps($pathname));

            $photo = new Photo();
            $photo->fill([
                'filename' => $name,
                'longitude' => $gps->GPSLongitude ?? null,
                'latitude' => $gps->GPSLatitude ?? null,
            ]);
            $success = $photo->save();
            $data = ['success' => $success, 'data' => $photo->toArray()];
        }

        return \response()->json($data);
    }

    public function list()
    {
        $photos = Photo::latest()->paginate(3);
        return view('list', compact('photos'));
    }

    public function edit(Request $request, $id)
    {
        $photo = Photo::findOrFail($id);
        if($request->getMethod() == 'GET'){
            return view('edit', compact('photo'));
        }

        $photo->fill($request->all());
        $success1 = $photo->save();

        //调用脚本修改图片GPS附加信息
        $script = base_path('bin/geo.py');
        $file = storage_path('app/public/photos/'.$photo->filename);
        $cmd = "python $script set_gps $file $photo->longitude $photo->latitude";
        $output = $this->execute($cmd);
        $success2 = $output->success ?? false;

        return response()->json(['success' => $success1 && $success2, 'photo' => $photo->toArray()]);
    }

    public function album()
    {
        $photos = $this->photos();
        return view('geo_album', ['points' => $photos]);
    }

    private function photos()
    {
        $dir = storage_path('app/public/photos');
        $script = base_path('bin/geo.py');
        $cmd = "python $script get_gps $dir";
        $photos = $this->execute($cmd);

        $photos = json_encode($photos, JSON_FORCE_OBJECT);
        return $photos;
    }

    private function gps($file)
    {
        $script = base_path('bin/geo.py');
        $output = shell_exec("python $script $file");
        $photo = json_decode($output, true);
        return $photo;
    }





    private function uniqueName($filename)
    {
        $i = strrpos($filename, '.');
        return substr_replace($filename, '_'.uniqid(), $i, 0);
    }

    public function execute($cmd)
    {
        Log::info('shell-commands: '.$cmd);
        $output = shell_exec($cmd);
        Log::info('shell-output: '. var_export($output, true));
        $obj = json_decode($output);
        return $obj;
    }
}
