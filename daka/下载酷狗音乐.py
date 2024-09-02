import tkinter as tk
from tkinter import ttk
from tkinter import messagebox
import os
import requests
import json
import urllib
from hashlib import md5
import time
import hashlib
import urllib.parse
import threading

# MD5编码
def get_signature(text):
    new_md5 = md5()
    new_md5.update(text.encode(encoding='utf-8'))
    signature = new_md5.hexdigest()
    return signature

headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36 Edg/117.0.2045.47'
    }

# 构造搜索的url，并获取搜索结果
def get_list(keyword):
    mid = 'ccc842dce7da774774ce9278c0591119'
    url = 'https://complexsearch.kugou.com/v2/search/song?callback=callback123&srcappid=2919&clientver=1000&clienttime={time}&mid={mid}&uuid={mid}&dfid=0R7g5f2OX6eY2EBfN92rrRN0&keyword={keyword}&page=1&pagesize=30&bitrate=0&isfuzzy=0&inputtype=0&platform=WebFilter&userid=0&iscorrection=1&privilege_filter=0&filter=10&token=&appid=1014&signature={signature}'
    key_code = 'NVPh5oo715z5DIWAeQlhMDsWXXQV4hwtappid=1014bitrate=0callback=callback123clienttime={time}clientver=1000dfid=0R7g5f2OX6eY2EBfN92rrRN0filter=10inputtype=0iscorrection=1isfuzzy=0keyword={keyword}mid={mid}page=1pagesize=30platform=WebFilterprivilege_filter=0srcappid=2919token=userid=0uuid={mid}NVPh5oo715z5DIWAeQlhMDsWXXQV4hwt'
    millis = str(round(time.time() * 1000))
    p = key_code.format(time=millis, mid=mid, keyword=keyword)
    signature = get_signature(p)
    search_url = url.format(keyword=keyword, time=millis, signature=signature, mid=mid)
    list_res = requests.get(search_url, headers=headers)
    return list_res

def deurl(id):
    # 参数字典
    params = {
        'srcappid': '2919',
        'clientver': '20000',
        'mid': '4bd1767507fe4aac45a50eef40ce3fd2',
        'uuid': '4bd1767507fe4aac45a50eef40ce3fd2',
        'dfid': '3x7cn613ZpwI3I2dK32ncFst',
        'appid': '1014',
        'platid': '4',
        'encode_album_audio_id': id,
        'token': '',
        'userid': '0'
    }

    # 获取当前时间的毫秒级时间戳
    clienttime = int(time.time() * 1000)

    # 将clienttime作为参数加入到params字典中
    params['clienttime'] = clienttime

    # 拼接参数字符串
    param_string = ''.join([f"{k}={str(params[k])}" for k in sorted(params.keys())])

    # 添加固定字符串到参数字符串的开头和结尾
    secret_key = 'NVPh5oo715z5DIWAeQlhMDsWXXQV4hwt'
    param_string = f"{secret_key}{param_string}{secret_key}"

    # 计算MD5哈希
    signature = hashlib.md5(param_string.encode()).hexdigest()

    # 在URL中添加signature参数
    url = f"https://wwwapi.kugou.com/play/songinfo?{urllib.parse.urlencode(params)}&signature={signature}"
    return url

def show_list(song_list, os_path):
    for i, song in enumerate(song_list):
        a = deurl(song.get("EMixSongID"))
        data = requests.get(a, headers=headers).json()['data']['play_backup_url']
        resp = requests.get(data).content
        with open(os_path + song.get("SongName") + '.mp3', 'wb') as f:
            f.write(resp)
        print(f'第{i + 1}首---{song.get("SongName")}---下载完成！')

def download():
    keyword = song_entry.get()
    if not keyword:
        messagebox.showerror("Error", "Please enter a song name.")
        return
    threading.Thread(target=download_song, args=(keyword,)).start()

def download_song(keyword):
    os_path = os.getcwd() + '/MP3/'
    if not os.path.exists(os_path):
        os.mkdir(os_path)
    if not os.path.exists(os_path + f'/{keyword}/'):
        os.mkdir(os_path + f'/{keyword}/')
    list_res = get_list(keyword)
    song_list = json.loads(list_res.text[12:-2])['data']['lists']
    show_list(song_list, os_path + f'/{keyword}/')

# Create GUI window
root = tk.Tk()
root.title("Song Downloader")

# Create song name input
song_label = tk.Label(root, text="歌手名称:")
song_label.grid(row=0, column=0, padx=5, pady=5, sticky="w")
song_entry = tk.Entry(root)
song_entry.grid(row=0, column=1, padx=5, pady=5, sticky="we")

# Create download button
download_button = tk.Button(root, text="下载", command=download)
download_button.grid(row=1, column=0, columnspan=2, padx=5, pady=5, sticky="we")

# Create progress bar
progress_bar = ttk.Progressbar(root, orient="horizontal", length=200, mode="determinate")
progress_bar.grid(row=2, column=0, columnspan=2, padx=5, pady=5, sticky="we")

# Run the GUI
root.mainloop()
