import os
import requests
from time import sleep

def get_url_list(fileloc):
  with open(fileloc, 'r') as f:
    fr = f.read().split('\n')
    return fr

def ensure_dir(file_path):
  directory = os.path.dirname(file_path)
  if not os.path.exists(directory):
    os.makedirs(directory)

def download_image(url,folder):

  filename = os.path.basename(url)
  path = f'{folder}/{filename}'
  ensure_dir(path)
  if not os.path.exists(path):
    print("path ok")

    try:
      r = requests.get(url)
      sleep(2)
    except requests.exceptions.ConnectionError as e:
      print(e)

    if r.status_code == 200:
      print('Processing.. ', url)
      with open(path, 'wb') as f:
        f.write(r.content)

  else:
    print("exist")

