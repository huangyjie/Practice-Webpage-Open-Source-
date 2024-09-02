var selectedDate = null;

// 节日数据
const holidays = [
    { title: '元旦', date: '2024-01-01' },
    { title: '小寒', date: '2024-01-06' },
    { title: '中国人民警察节', date: '2024-01-10' },
    { title: '腊八节', date: '2024-01-18' },
    { title: '北方小年', date: '2024-02-02' },
    { title: '南方小年', date: '2024-02-03' },
    { title: '立春', date: '2024-02-04' },
    { title: '除夕', date: '2024-02-09' },
    { title: '春节', date: '2024-02-10' },
    { title: '情人节', date: '2024-02-14' },
    { title: '元宵节', date: '2024-02-24' },
    { title: '妇女节', date: '2024-03-08' },
    { title: '龙抬头', date: '2024-03-11' },
    { title: '植树节', date: '2024-03-12' },
    { title: '消费者权益日', date: '2024-03-15' },
    { title: '愚人节', date: '2024-04-01' },
    { title: '清明节', date: '2024-04-04' },
    { title: '劳动节', date: '2024-05-01' },
    { title: '青年节', date: '2024-05-04' },
    { title: '母亲节', date: '2024-05-12' },
    { title: '儿童节', date: '2024-06-01' },
    { title: '端午节', date: '2024-06-10' },
    { title: '父亲节', date: '2024-06-16' },
    { title: '建党节', date: '2024-07-01' },
    { title: '七七事变', date: '2024-07-07' },
    { title: '建军节', date: '2024-08-01' },
    { title: '七夕节', date: '2024-08-10' },
    { title: '中元节', date: '2024-08-18' },
    { title: '教师节', date: '2024-09-10' },
    { title: '中秋节', date: '2024-09-17' },
    { title: '九一八事变', date: '2024-09-18' },
    { title: '丰收节', date: '2024-09-22' },
    { title: '国庆节', date: '2024-10-01' },
    { title: '重阳节', date: '2024-10-11' },
    { title: '感恩节', date: '2024-11-28' },
    { title: '国家公祭日', date: '2024-12-13' },
    { title: '平安夜', date: '2024-12-24' },
    { title: '圣诞节', date: '2024-12-25' }
];

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'zh-cn',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,dayGridYear'
        },
        buttonText: {
            today: '今天',
            month: '月',
            week: '周',
            day: '日',
            year: '年'
        },
        events: holidays.map(holiday => ({
            title: holiday.title,
            start: holiday.date,
            backgroundColor: '#ff9f00',
            borderColor: '#ff9f00'
        })),
        dateClick: function(info) {
            selectedDate = info.dateStr;
        }
    });
    calendar.render();

    $('#punchInBtn').show();

    function getMacAddress() {
        return '00:11:22:33:44:55';
    }

    function updatePunchTime(date) {
        $('#punchTimeDisplay').text(date);
    }

    $(document).ready(function() {
        var macAddress = getMacAddress();
        $.post("authenticate.php", { mac_address: macAddress }, function(data) {
            if (data === "authenticated") {
                alert("设备认证成功！");
            } else {
                alert("设备认证失败！");
            }
        });

        $('#punchInBtn').click(function() {
            var dateToPunchIn = selectedDate || new Date().toISOString().split('T')[0];
            $.post("punch_in.php", { date: dateToPunchIn, mac_address: macAddress }, function(data) {
                alert(data);
                updatePunchTime(dateToPunchIn);
            });
        });

        $('#prevMonth').click(function() {
            calendar.prev();
            $('#prevMonth').addClass('shake');
            setTimeout(function() {
                $('#prevMonth').removeClass('shake');
            }, 500);
        });

        $('#nextMonth').click(function() {
            calendar.next();
            $('#nextMonth').addClass('shake');
            setTimeout(function() {
                $('#nextMonth').removeClass('shake');
            }, 500);
        });

        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeString = `${hours}:${minutes}:${seconds}`;
            document.getElementById('clock').textContent = timeString;
        }

        setInterval(updateClock, 1000);
        updateClock();

        var musicBall = document.getElementById('musicBall');
        var audio = new Audio();
        var musicFiles = [
            'MP3/My Sunshine.mp3',
            'MP3/一个人的远方.mp3',
            'MP3/一半.mp3',
            'MP3/一直很安静 (Live).mp3',
            'MP3/一程山路 (Live).mp3',
            'MP3/一程山路.mp3',
            'MP3/一荤一素 (Live).mp3',
            'MP3/一路之下.mp3',
            'MP3/万物不如你.mp3',
            'MP3/三生三世.mp3',
            'MP3/不染 (Live).mp3',
            'MP3/不染.mp3',
            'MP3/不眠之夜.mp3',
            'MP3/丑八怪.mp3',
            'MP3/东北民谣.mp3',
            'MP3/为你写下这首情歌.mp3',
            'MP3/二零三 (Live).mp3',
            'MP3/云山.mp3',
            'MP3/五月天.mp3',
            'MP3/仓颉.mp3',
            'MP3/他不懂.mp3',
            'MP3/仰望星空.mp3',
            'MP3/伤心的人别听慢歌 (贯彻快乐).mp3',
            'MP3/你不是真正的快乐.mp3',
            'MP3/你还要我怎样.mp3',
            'MP3/倔强.mp3',
            'MP3/借 (Live).mp3',
            'MP3/像我这样的人 (Live).mp3',
            'MP3/像风一样.mp3',
            'MP3/光芒.mp3',
            'MP3/入阵曲.mp3',
            'MP3/全是爱.mp3',
            'MP3/其实.mp3',
            'MP3/冒险精神.mp3',
            'MP3/凤凰传奇《海底 (Live)》.mp3',
            'MP3/分分钟需要你 (Live).mp3',
            'MP3/刚刚好.mp3',
            'MP3/刻在我心底的名字.mp3',
            'MP3/剑心.mp3',
            'MP3/动物世界.mp3',
            'MP3/勿忘心安.mp3',
            'MP3/只在今夜.mp3',
            'MP3/只要平凡.mp3',
            'MP3/吉祥如意.mp3',
            'MP3/后来的我们.mp3',
            'MP3/听.mp3',
            'MP3/呓语 (Live).mp3',
            'MP3/呓语.mp3',
            'MP3/因为你 所以我.mp3',
            'MP3/大声唱.mp3',
            'MP3/天下.mp3',
            'MP3/天使.mp3',
            'MP3/天后 (Live).mp3',
            'MP3/天外来物.mp3',
            'MP3/天籁传奇.mp3',
            'MP3/天蓝蓝.mp3',
            'MP3/奢香夫人 (DJ版).mp3',
            'MP3/奢香夫人 (Live).mp3',
            'MP3/奢香夫人.mp3',
            'MP3/好好 (想把你写成一首歌).mp3',
            'MP3/如果我们不曾相遇.mp3',
            'MP3/如果有一天我变得很有钱 (Live).mp3',
            'MP3/守村人.mp3',
            'MP3/将军令.mp3',
            'MP3/小尖尖.mp3',
            'MP3/少年中国说.mp3',
            'MP3/山河图 (Live).mp3',
            'MP3/山河图.mp3',
            'MP3/岁月神偷 (Live).mp3',
            'MP3/崇拜.mp3',
            'MP3/干杯.mp3',
            'MP3/御龙归字谣.mp3',
            'MP3/心驰神往.mp3',
            'MP3/念.mp3',
            'MP3/怪咖.mp3',
            'MP3/恋爱ing.mp3',
            'MP3/想见你想见你想见你 (Live).mp3',
            'MP3/意外.mp3',
            'MP3/我不愿让你一个人.mp3',
            'MP3/我从草原来.mp3',
            'MP3/我们的歌谣.mp3',
            'MP3/我们都一样 (Live).mp3',
            'MP3/我和草原有个约定.mp3',
            'MP3/我好像在哪见过你.mp3',
            'MP3/我要你 (Live).mp3',
            'MP3/拜新年.mp3',
            'MP3/方圆几里.mp3',
            'MP3/无名的人.mp3',
            'MP3/无数.mp3',
            'MP3/无问.mp3',
            'MP3/明天过后.mp3',
            'MP3/星星 (Live).mp3',
            'MP3/星空.mp3',
            'MP3/是龙.mp3',
            'MP3/暧昧.mp3',
            'MP3/最炫民族风.mp3',
            'MP3/最熟悉的陌生人 (Live).mp3',
            'MP3/最美的太阳.mp3',
            'MP3/月亮之上.mp3',
            'MP3/有你无你.mp3',
            'MP3/木偶人.mp3',
            'MP3/梅香如故.mp3',
            'MP3/步步.mp3',
            'MP3/毛不易.mp3',
            'MP3/派对动物.mp3',
            'MP3/消愁 (Live).mp3',
            'MP3/温柔.mp3',
            'MP3/溜溜的情歌.mp3',
            'MP3/演员.mp3',
            'MP3/牧马城市.mp3',
            'MP3/玫瑰少年 (Mayday 2019 Just Rock It!!! 蓝｜BLUE-上海站).mp3',
            'MP3/玫瑰少年.mp3',
            'MP3/盛夏 (Live).mp3',
            'MP3/盛夏光年.mp3',
            'MP3/看得最远的地方.mp3',
            'MP3/看月亮爬上来 (Live).mp3',
            'MP3/着魔.mp3',
            'MP3/知足.mp3',
            'MP3/离开地球表面.mp3',
            'MP3/私奔到月球.mp3',
            'MP3/突然好想你.mp3',
            'MP3/笑忘歌.mp3',
            'MP3/等爱的玫瑰.mp3',
            'MP3/策马奔腾.mp3',
            'MP3/绅士.mp3',
            'MP3/绿旋风.mp3',
            'MP3/自由自在.mp3',
            'MP3/自由飞翔.mp3',
            'MP3/荷塘月色.mp3',
            'MP3/见信如晤.mp3',
            'MP3/解解闷.mp3',
            'MP/认真的雪.mp3',
            'MP3/让我欢喜让我忧 (Live).mp3',
            'MP3/谁 (Live).mp3',
            'MP3/身骑白马 (Live).mp3',
            'MP3/这么久没见.mp3',
            'MP3/这里是神奇的赛尔号.mp3',
            'MP3/这，就是爱.mp3',
            'MP3/违背的青春.mp3',
            'MP3/逆战.mp3',
            'MP3/那是你离开了北京的生活.mp3',
            'MP3/郎的诱惑.mp3',
            'MP3/醉美天下.mp3',
            'MP3/野心.mp3',
            'MP3/金色的屋檐.mp3',
            'MP3/陪你去流浪.mp3',
            'MP3/雪龙吟.mp3',
            'MP3/黑暗骑士 (feat. 五月天).mp3',
            'MP3/黑月光.mp3'
        ];


        function playRandomMusic() {
            var randomIndex = Math.floor(Math.random() * musicFiles.length);
            audio.src = musicFiles[randomIndex];
            audio.play();
            musicBall.classList.add('bounce');
            audio.onended = playRandomMusic; // 播放结束后自动播放下一首
        }

        document.getElementById('playPause').addEventListener('click', function() {
            if (audio.paused) {
                playRandomMusic();
                this.textContent = '⏸'; // 显示暂停图标
            } else {
                audio.pause();
                musicBall.classList.remove('bounce');
                this.textContent = '▶' ; // 显示播放图标
            }
        });

        document.getElementById('prevTrack').addEventListener('click', function() {
            var currentIndex = musicFiles.indexOf(audio.src.split('/').pop());
            var prevIndex = (currentIndex - 1 + musicFiles.length) % musicFiles.length;
            audio.src = musicFiles[prevIndex];
            audio.play();
        });

        document.getElementById('nextTrack').addEventListener('click', function() {
            var currentIndex = musicFiles.indexOf(audio.src.split('/').pop());
            var nextIndex = (currentIndex + 1) % musicFiles.length;
            audio.src = musicFiles[nextIndex];
            audio.play();
        });

        document.getElementById('fastForward').addEventListener('click', function() {
            audio.currentTime += 15; // 快进15秒
        });

        function makeDraggable(element) {
            let isDragging = false;
            let startX, startY, initialLeft, initialTop;

            element.addEventListener('mousedown', function(e) {
                isDragging = true;
                startX = e.clientX;
                startY = e.clientY;
                initialLeft = parseInt(window.getComputedStyle(element).left, 10);
                initialTop = parseInt(window.getComputedStyle(element).top, 10);
                element.classList.add('dragging');
            });

            document.addEventListener('mousemove', function(e) {
                if (isDragging) {
                    let deltaX = e.clientX - startX;
                    let deltaY = e.clientY - startY;
                    element.style.left = (initialLeft + deltaX) + 'px';
                    element.style.top = (initialTop + deltaY) + 'px';
                }
            });

            document.addEventListener('mouseup', function() {
                isDragging = false;
                element.classList.remove('dragging');
            });
        }

        makeDraggable(musicBall);
    });
});
