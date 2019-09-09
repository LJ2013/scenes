# encoding:utf-8

# 纯脚本程序，对一组图片进行管理以下管理：
# 1. 读取并显示GPS信息，没有的可以编辑保存进去
# 2. 按照GPS信息进行聚类分组

import os
import sys
import piexif
import json
from PIL import Image

'''
    GPS坐标转换：度分秒转浮点数
    可以看到获取到的GPS经纬度坐标是纬度(N)[39, 16, 2259/50]/经度(E)[99, 48, 897/25]格式的，格式化为度分秒则是N39° 16′ 45.18″/E99° 48′ 35.88″。
    根据度分秒格式的GPS经纬度坐标可以在在线经纬度地图地图中找到所在位置，但比如在高德中则需要十进制的格式。
'''


def gps_trans(n1, n2, n3):
    # [39, 16, 2259/50]
    # [99, 48, 897/25]
    # 39 + 16/60 + (2259/50)/3600 = 39.279216667
    # 99 + 48/60 + (897/25)/3600 = 99.809966667
    return float(n1[0]/n1[1]) + (float(n2[0]/n2[1]) / 60) + (float(n3[0]/n3[1]) / 3600)


# def set_exif(filename):
#     im = Image.open(filename)
#     exif_dict = piexif.load(im.info["exif"])
#     # process im and exif_dict...
#     w, h = im.size
#     exif_dict["0th"][piexif.ImageIFD.XResolution] = (w, 1)
#     exif_dict["0th"][piexif.ImageIFD.YResolution] = (h, 1)
#     exif_bytes = piexif.dump(exif_dict)
#     im.save(filename, "jpeg", exif=exif_bytes)

def get_gps(image):
    exif_dict = piexif.load(image)
    # print(json.dumps(exif_dict, indent=2, ensure_ascii=False))
    # for ifd in ("0th", "Exif", "GPS", "1st"):
    #     for tag in exif_dict[ifd]:
    #         print(piexif.TAGS[ifd][tag]["name"], exif_dict[ifd][tag])
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


def set_gps(filename, longitude, latitude, altitude = None, outfilename = None):
    '''
    :param filename:
    :param longitude: 经度
    :param latitude: 纬度
    :param altitude: 海拔
    :return:
    '''
    if outfilename is None:
        i = filename.rfind('.')
        outfilename = "%s_cp%s" % (filename[0:i], filename[i:])

    im = Image.open(filename)
    exif_dict = piexif.load(im.info["exif"])
    # process im and exif_dict...
    exif_dict["GPS"][piexif.GPSIFD.GPSLongitude] = ((39, 1), (59, 1), (366722, 10000))
    exif_dict["GPS"][piexif.GPSIFD.GPSLatitude] = ((116, 1), (27, 1), (33060, 10000))
    if altitude is not None:
        exif_dict["GPS"][piexif.GPSIFD.GPSAltitude] = (39269, 1000)
    exif_bytes = piexif.dump(exif_dict)
    im.save(outfilename, "jpeg", exif=exif_bytes)



def gps_gather(pathname):
    gps_data = {}
    if os.path.isfile(pathname):
        file = pathname
        name = os.path.basename(file)
        thumbnail = os.path.join(os.path.dirname(file), '../thumbnails/' + name)
        gps_data[name] = get_gps(file)
        if not os.path.exists(thumbnail):
            im = Image.open(file)
            im.thumbnail((80, 80))
            # print(im.format, im.size, im.mode)
            im.save(thumbnail, 'JPEG')
        return gps_data
    else:
        dir = pathname
        for name in os.listdir(dir):
            file = os.path.join(dir, name)
            thumbnail = os.path.join(dir, '../thumbnails/' + name)
            # 生成缩略图
            if not os.path.exists(thumbnail):
                im = Image.open(file)
                im.thumbnail((80, 80))
                # print(im.format, im.size, im.mode)
                im.save(thumbnail, 'JPEG')

            gps_data[name] = get_gps(file)
        return gps_data

if __name__ == '__main__':
    cmd = sys.argv[1]
    if cmd == 'set_gps':
        set_gps(sys.argv[2], sys.argv[3], sys.argv[4])
        print('{"success": true}')
    elif cmd == 'get_gps':
        data = gps_gather(sys.argv[2])
        print(json.dumps(data))
