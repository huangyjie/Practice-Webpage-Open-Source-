from flask import Flask, request, jsonify, render_template
import subprocess
import sys
import os

app = Flask(__name__)

@app.route('/')
def home():
    return render_template('index.html')

@app.route('/compile', methods=['POST'])
def compile_code():
    data = request.json
    language = data.get('language')
    code = data.get('code')
    user_input = data.get('input', "")

    try:
        # Python 代码执行
        if language == 'python':
            result = subprocess.run([sys.executable, '-c', code], input=user_input, capture_output=True, text=True)

        # JavaScript 代码执行
        elif language == 'javascript':
            result = subprocess.run(['node', '-e', code], input=user_input, capture_output=True, text=True)

        # C++ 代码编译和执行
        elif language == 'cpp':
            with open("temp.cpp", "w") as f:
                f.write(code)
            compile_result = subprocess.run(["g++", "temp.cpp", "-o", "temp.exe"], capture_output=True, text=True)
            if compile_result.returncode != 0:
                return jsonify({"output": "C++ 编译失败: " + compile_result.stderr}), 400
            result = subprocess.run(["./temp.exe"], input=user_input, capture_output=True, text=True)
            os.remove("temp.cpp")
            os.remove("temp.exe")

        # Java 代码编译和执行
        elif language == 'java':
            with open("Main.java", "w") as f:
                f.write(code)
            compile_result = subprocess.run(["javac", "Main.java"], capture_output=True, text=True)
            if not os.path.exists("Main.class"):
                return jsonify({"output": "Java 编译失败: " + compile_result.stderr}), 400
            result = subprocess.run(["java", "Main"], input=user_input, capture_output=True, text=True)
            output = result.stdout if result.returncode == 0 else result.stderr
            os.remove("Main.java")
            os.remove("Main.class")
            return jsonify({"output": output})

        # Ruby 代码执行（使用完整路径）
        elif language == 'ruby':
            result = subprocess.run([r'C:\Ruby27-x64\bin\ruby.exe', '-e', code], input=user_input, capture_output=True, text=True)

        # C 代码编译和执行
        elif language == 'c':
            with open("temp.c", "w") as f:
                f.write(code)
            compile_result = subprocess.run(["gcc", "temp.c", "-o", "temp.exe"], capture_output=True, text=True)
            if compile_result.returncode != 0:
                return jsonify({"output": "C 编译失败: " + compile_result.stderr}), 400
            result = subprocess.run(["./temp.exe"], input=user_input, capture_output=True, text=True)
            os.remove("temp.c")
            os.remove("temp.exe")

        else:
            return jsonify({"output": "不支持的编程语言"}), 400

        output = result.stdout if result.returncode == 0 else result.stderr
        return jsonify({"output": output})

    except Exception as e:
        return jsonify({"output": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)
