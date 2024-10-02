from flask import Flask, render_template

app = Flask(__name__)

# 定义不同电子邮箱服务的信息
email_services = [
    {
        'name': 'Gmail',
        'url': 'https://mail.google.com/',
        'description': 'Google\'s email service.',
        'logo': 'https://th.bing.com/th?id=OIP.RRjVVzmiPNX7KRpwen-aHQHaEK&w=333&h=187&c=8&rs=1&qlt=90&o=6&cb=13&pid=3.1&rm=2'
    },
    {
        'name': 'Outlook',
        'url': 'https://outlook.live.com/',
        'description': 'Microsoft\'s email service.',
        'logo': 'https://th.bing.com/th?id=OIP.r8eyE3ihLgzNR-7t32ZZIwHaE8&w=306&h=204&c=8&rs=1&qlt=90&o=6&cb=13&pid=3.1&rm=2'
    },
    {
        'name': 'Yahoo Mail',
        'url': 'https://mail.yahoo.com/',
        'description': 'Yahoo\'s email service.',
        'logo': 'https://th.bing.com/th?id=OIP.0P7qKYp7e18MkWd8bbR6sQAAAA&w=183&h=183&c=8&rs=1&qlt=90&o=6&cb=13&pid=3.1&rm=2'
    },
    {
        'name': 'ProtonMail',
        'url': 'https://protonmail.com/',
        'description': 'A secure and encrypted email service.',
        'logo': 'https://th.bing.com/th?id=OIP.ggORq9aiTJmoU9jJighAFwHaEK&w=333&h=187&c=8&rs=1&qlt=90&o=6&cb=13&pid=3.1&rm=2'
    },
    {
        'name': 'QQ Mail',
        'url': 'https://mail.qq.com/',
        'description': 'Tencent\'s email service, popular in China.',
        'logo': 'https://th.bing.com/th?id=OIP.X-JG_KocR1n0MA7X30o8pwHaHa&w=250&h=250&c=8&rs=1&qlt=90&o=6&cb=13&pid=3.1&rm=2'
    },
    {
        'name': '163 Mail',
        'url': 'https://mail.163.com/',
        'description': 'NetEase\'s email service.',
        'logo': 'https://th.bing.com/th?id=OIP.eCEYoNcbKyCm5e2D5cVx_QAAAA&w=215&h=215&c=8&rs=1&qlt=90&o=6&cb=13&pid=3.1&rm=2'
    },
    {
        'name': '126 Mail',
        'url': 'https://mail.126.com/',
        'description': 'Another popular email service by NetEase.',
        'logo': 'https://th.bing.com/th?id=OIP.ShhwXbRnIOZpsJXiapUapgAAAA&w=189&h=189&c=8&rs=1&qlt=90&o=6&cb=13&pid=3.1&rm=2'
    },
    {
        'name': 'Sohu Mail',
        'url': 'https://mail.sohu.com/',
        'description': 'Sohu\'s email service.',
        'logo': 'https://th.bing.com/th/id/OIP.PPS1d1vcoGZ-QXH2YReA5QHaFj?w=228&h=180&c=7&r=0&o=5&pid=1.7'
    },
    {
        'name': 'Sina Mail',
        'url': 'https://mail.sina.com.cn/',
        'description': 'Sina\'s email service.',
        'logo': 'https://th.bing.com/th/id/OIP.mN3AmB82Vb9C1iIy00EHjQAAAA?w=251&h=141&c=7&r=0&o=5&pid=1.7'
    },
    {
        'name': '139 Mail',
        'url': 'https://mail.10086.cn/',
        'description': 'China Mobile\'s email service, 139 Mail.',
        'logo': 'https://th.bing.com/th/id/OIP.pYHgKb6ev1mv55tqkzHGbAHaEa?w=331&h=200&c=7&r=0&o=5&pid=1.7'  # 139邮箱logo链接
    }
]

@app.route('/')
def home():
    return render_template('emails.html', email_services=email_services)

if __name__ == '__main__':
    app.run(debug=True)
