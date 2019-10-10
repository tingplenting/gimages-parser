import re
import requests
import json

from functools import partial
from multiprocessing import Pool
from get_image import download_image

def get_google_image(term):
  headers =  {
    "User-Agent": "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.73 Safari/537.36",
    "Referer":"localhost",
    "Accept": "text/xml,application/xml,application/xhtml+xml,",
    "Cache-Control": "max-age=0",
    "Connection": "keep-alive",
    "Accept-Charset": "ISO-8859-1,utf-8;q=0.7,*;q=0.7",
    "Accept-Language": "en-us,en;q=0.5",
    "Pragma": "no-cache"
  }

  url = "https://www.google.com/search?q=" + term + "&client=firefox-b&source=lnms&tbm=isch"

  r = requests.get(url, headers=headers)
  if r.status_code == 200:
    content = r.content.decode('utf-8')

    find_id = re.findall(r'{[^{}]*?"id".*?}', content, re.I | re.M)
    img_url = []

    for data in find_id:

      item = json.loads(data)
      h = item['oh']
      w = item['ow']
      u = item['ou']
      if h > w and h >= 1000 and re.match(r'^https?://(?:[a-z0-9\-]+\.)+[a-z]{2,6}(?:/[^/#?]+)+\.(?:jpg|gif|png)$',u,re.I):
        img_url.append(u)

    return img_url

  return False


if __name__ == '__main__':
  
  term = "floral+wallpaper"

  folder = "images"
  urlist = get_google_image(term)

  p = Pool(4)
  p.map(partial(download_image, folder=folder), urlist)
