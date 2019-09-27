# encoding:utf-8

# 纯脚本程序，对一组图片进行管理以下管理：
# 1. 读取并显示GPS信息，没有的可以编辑保存进去
# 2. 按照GPS信息进行聚类分组

import os
import sys
import piexif
import json
from PIL import Image


def gps_trans(n1, n2, n3):
    # [39, 16, 2259/50]
    # [99, 48, 897/25]
    # 39 + 16/60 + (2259/50)/3600 = 39.279216667
    # 99 + 48/60 + (897/25)/3600 = 99.809966667
    return float(n1[0]/n1[1]) + (float(n2[0]/n2[1]) / 60) + (float(n3[0]/n3[1]) / 3600)


def get_gps(image):
    exif_dict = piexif.load(image)

    ifd = 'GPS'
    gps = {}
    for tag in exif_dict[ifd]:
        name = piexif.TAGS[ifd][tag]["name"]
        value = exif_dict[ifd][tag]
        if name == 'GPSLongitude':
            gps['GPSLongitude'] = gps_trans(*value)
        if name == 'GPSLatitude':
            gps['GPSLatitude'] = gps_trans(*value)
    return gps



    gps_data = {}
    dir = pathname

def save_thumbnail(src, dst):
    if not os.path.exists(dst):
        im = Image.open(src)
        im.thumbnail((95, 95))
        im.save(dst, 'JPEG')


def save_compress(src, dst):
    if not os.path.exists(dst):
        im = Image.open(src)
        im.save(dst, 'JPEG')


if __name__ == '__main__':
    dir = '/Users/zhanglianjun/www/scenes/public/storage/photos/';
    thumbnail_dir = dir + '../thumbnails/'
    compress_dir = dir + '../compressed/'
    if not os.path.exists(thumbnail_dir):
        os.mkdir(thumbnail_dir)
    if not os.path.exists(compress_dir):
        os.mkdir(compress_dir)
    for name in os.listdir(dir):
        print(thumbnail_dir + name)
        file = dir + name
#        save_thumbnail(file, thumbnail_dir + name)
        save_compress(file, compress_dir + name)

#        gps_data[name] = get_gps(file)
