<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Helpers\Tools;
use App\Photo;

class PhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $filename;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        echo $this->filename;
        //保存原图、读取经纬度存库、 生成缩略图、压缩图
//        $dir = storage_path('app/public/photos');
//        $pathname = "$dir/$this->filename";
//        $script = base_path('bin/geo.py');
//        $cmd = "python $script get_gps $pathname";
//        $gps = current((array)Tools::execute($cmd));
//
//        $photo = new Photo();
//        $photo->fill([
//            'filename' => $this->filename,
//            'longitude' => $gps->GPSLongitude ?? null,
//            'latitude' => $gps->GPSLatitude ?? null,
//        ]);
//        $success = $photo->save();
//        $data = ['success' => $success, 'data' => $photo->toArray()];
    }
}
