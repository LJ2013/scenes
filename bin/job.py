# -*- coding:utf-8 -*-

import os
import sys
import json
from PIL import Image
import sqlite3
from gps_helper import find_GPS_image as get_gps


def do_batch(dir = None):
    conn = sqlite3.connect('/Users/zhanglianjun/www/scenes/storage/app/geo_album.db')
    c = conn.cursor()

    # c.execute('''DROP TABLE IF EXISTS `photos`''')
    c.execute('''
    CREATE TABLE IF NOT EXISTS photos(
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      filename VARCHAR(400) NOT NULL,
      GPSLongitude VARCHAR(200),
      GPSLatitude VARCHAR(200),
      GPSAltitude VARCHAR(200),
      GPSLongitudeRef VARCHAR(100),
      GPSLatitudeRef VARCHAR(100),
      GPSAltitudeRef VARCHAR(100),
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    ''')

    if dir is None:
        dir = os.path.join(os.path.dirname(__file__), 'mapAlbum/photos')
    thumbnail_dir = os.path.join(dir, '../thumbnails/')
    compress_dir = os.path.join(dir, '../compressed/')
    if not os.path.exists(thumbnail_dir):
        os.mkdir(thumbnail_dir)
    if not os.path.exists(compress_dir):
        os.mkdir(compress_dir)

    for name in os.listdir(dir):
        # save compressed and thumbnail files
        im = Image.open(os.path.join(dir, name))
        compress = os.path.join(compress_dir, name)
        thumbnail = os.path.join(thumbnail_dir, name)
        try:
            if not os.path.exists(compress):
                im.save(compress, 'JPEG')
            if not os.path.exists(thumbnail):
                im.thumbnail((95, 95))
                im.save(thumbnail, 'JPEG')
        except Exception as e:
            print('photo %s write error: %s', (name, e))
        finally:
            pass

        # read exif and get gps info
        image_pathname = os.path.join(dir, name)
        gps_info = get_gps(image_pathname)
        gps = gps_info['GPS_information']
        if 'GPSLongitude' not in gps or gps['GPSLongitude'] == '':
            print(name + ' cannot read gps info.')
            continue
        GPSLongitude = gps['GPSLongitude']
        GPSLatitude = gps['GPSLatitude']
        GPSAltitude = gps['GPSAltitude'] if 'GPSAltitude' in gps else ''

        GPSLongitudeRef = gps['GPSLongitudeRef'] if 'GPSLongitudeRef' in gps else ''
        GPSLatitudeRef = gps['GPSLatitudeRef'] if 'GPSLatitudeRef' in gps else ''
        GPSAltitudeRef = gps['GPSAltitudeRef'] if 'GPSAltitudeRef' in gps else ''

        # insert if not exists
        find_sql = "SELECT * FROM photos WHERE filename = '%s'" % name
        cursor = c.execute(find_sql)
        count = len(cursor.fetchall())
        if count == 0:
            insert_sql = "INSERT INTO photos(filename, GPSLongitude, GPSLatitude, GPSAltitude, GPSLongitudeRef, GPSLatitudeRef, GPSAltitudeRef) VALUES('%s', '%s', '%s', '%s','%s', '%s','%s')" % (name, GPSLongitude, GPSLatitude, GPSAltitude, GPSLongitudeRef, GPSLatitudeRef, GPSAltitudeRef)
            print(insert_sql)
            c.execute(insert_sql)
    conn.commit()
    conn.close()


if __name__ == '__main__':
    dir = '/Users/zhanglianjun/www/scenes/storage/app/public/photos'
    do_batch(dir)



