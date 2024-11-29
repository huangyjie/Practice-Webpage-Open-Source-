from flask import Flask, request, jsonify, render_template
import subprocess
import sys
import os
import shutil
import tempfile

app = Flask(__name__)

# 修改临时文件目录配置
TEMP_DIR = '/www/wwwroot/web/Xboke/zx/temp'

@app.route('/')
def home():
    return render_template('index.html')

@app.route('/compile', methods=['POST'])
def compile_code():
    # 确保临时目录存在
    os.makedirs(TEMP_DIR, exist_ok=True)
    
    data = request.json
    language = data.get('language')
    code = data.get('code')
    user_input = data.get('input', "")

    try:
        # Python 代码执行
        if language == 'python':
            try:
                # 使用项目目录下的临时目录
                temp_dir = os.path.join(TEMP_DIR, 'temp_python')
                os.makedirs(temp_dir, exist_ok=True)
                
                temp_file = os.path.join(temp_dir, "temp.py")
                with open(temp_file, "w") as f:
                    f.write(code)
                
                os.chmod(temp_file, 0o755)
                result = subprocess.run(['python3', temp_file], 
                                     input=user_input,
                                     capture_output=True,
                                     text=True,
                                     cwd=temp_dir)
                
                # 清理临时文件
                shutil.rmtree(temp_dir)
            
            except Exception as e:
                return jsonify({"output": f"Python执行错误: {str(e)}"}), 500

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

        # Ruby 代码执行
        elif language == 'ruby':
            # 使用which命令找到ruby解释器路径
            ruby_path = shutil.which('ruby')
            if not ruby_path:
                return jsonify({"output": "系统未安装Ruby解释器"}), 400
                
            with open("temp.rb", "w") as f:
                f.write(code)
            # 给予执行权限    
            os.chmod("temp.rb", 0o755)
            result = subprocess.run([ruby_path, 'temp.rb'], 
                                 input=user_input, 
                                 capture_output=True, 
                                 text=True)
            os.remove("temp.rb")

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

        # Go 代码编译和执行
        elif language == 'go':
            try:
                temp_go_path = os.path.join(TEMP_DIR, 'temp_go_path')
                os.makedirs(temp_go_path, exist_ok=True)
                
                src_dir = os.path.join(temp_go_path, 'src')
                os.makedirs(src_dir, exist_ok=True)
                
                go_env = os.environ.copy()
                go_env['GOPATH'] = temp_go_path
                go_env['GOCACHE'] = os.path.join(TEMP_DIR, 'go-cache')
                go_env['HOME'] = temp_go_path
                
                temp_file = os.path.join(src_dir, "temp.go")
                with open(temp_file, "w") as f:
                    f.write(code)
                
                # 编译Go代码
                compile_result = subprocess.run(
                    ["go", "build", "-o", "temp_go", temp_file],
                    env=go_env,
                    capture_output=True,
                    text=True,
                    cwd=src_dir
                )
                
                if compile_result.returncode != 0:
                    return jsonify({"output": "Go 编译失败: " + compile_result.stderr}), 400
                
                # 执行编译后的程序
                result = subprocess.run(
                    ["./temp_go"],
                    input=user_input,
                    capture_output=True,
                    text=True,
                    cwd=src_dir
                )
                
                # 清理临时文件和目录
                shutil.rmtree(temp_go_path)
                
                output = result.stdout if result.returncode == 0 else result.stderr
                return jsonify({"output": output})
            
            except Exception as e:
                return jsonify({"output": f"Go执行错误: {str(e)}"}), 500

        # Rust 代码编译和执行
        elif language == 'rust':
            try:
                # 创建临时Rust文件
                with open("temp.rs", "w") as f:
                    f.write(code)
                
                # 编译Rust代码
                compile_result = subprocess.run(["rustc", "temp.rs", "-o", "temp_rust"], 
                                             capture_output=True, 
                                             text=True)
                if compile_result.returncode != 0:
                    return jsonify({"output": "Rust 编译失败: " + compile_result.stderr}), 400
                
                # 执行编译后的程序
                result = subprocess.run(["./temp_rust"], 
                                      input=user_input, 
                                      capture_output=True, 
                                      text=True)
                
                # 清理临时文件
                os.remove("temp.rs")
                os.remove("temp_rust")
            
            except Exception as e:
                return jsonify({"output": f"Rust执行错误: {str(e)}"}), 500

        # PHP 代码执行
        elif language == 'php':
            try:
                # 创建临时PHP文件
                with open("temp.php", "w") as f:
                    f.write(code)
                
                # 设置执行权限
                os.chmod("temp.php", 0o755)
                
                # 执行PHP代码
                result = subprocess.run(["php", "temp.php"], 
                                      input=user_input, 
                                      capture_output=True, 
                                      text=True)
                
                # 清理临时文件
                os.remove("temp.php")
            
            except Exception as e:
                return jsonify({"output": f"PHP执行错误: {str(e)}"}), 500

        else:
            return jsonify({"output": "不支持的编程语言"}), 400

        output = result.stdout if result.returncode == 0 else result.stderr
        return jsonify({"output": output})

    except Exception as e:
        return jsonify({"output": str(e)}), 500

if __name__ == '__main__':
    # 确保临时目录存在并设置权限
    os.makedirs(TEMP_DIR, exist_ok=True)
    os.chmod(TEMP_DIR, 0o755)
    app.run(host='127.0.0.1', debug=True)