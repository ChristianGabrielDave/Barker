<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="stylesheet" href="design/indexStyle.css">
        <link rel="shortcut icon" type="x-icon" href="assets/logo.png">
        <script src="https://kit.fontawesome.com/2960bf0645.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="container">
            <div class="Form loginForm">
                <h2>Login</h2>
                <form action="auth.php" method="post">
                    <div class="inputBox">
                        <i class="fa-solid fa-user"></i>
                        <label for="#">Username</label>
                        <input type="text" name="username" placeholder="Enter Your Username" required>
                    </div>
                    <div class="inputBox">
                        <i class="fa-solid fa-lock"></i>
                        <label for="#">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter Your Password" required>
                        <span class="eye" onclick="togglePasswordVisibility('password', 'hide1', 'hide2')">
                            <i id="hide1" class="fa-solid fa-eye"></i>
                            <i id="hide2" class="fa-solid fa-eye-slash"></i>
                        </span>
                    </div>
                    <button class="button" type="submit" name="login">Login</button>
                </form>
                <p class="rgstrBttn">Don't have an account? <a href="#">Register!</a></p>
            </div>
            <div class="Form registerForm">
                <h2>Register</h2>
                <form action="auth.php" method="post" onsubmit="return validatePass()">
                    <div class="inputBox">
                        <i class="fa-solid fa-user"></i>
                        <label for="#">Username</label>
                        <input type="text" name="username" placeholder="Enter Your Username" required>
                    </div>
                    <div class="inputBox">
                        <i class="fa-solid fa-envelope"></i>
                        <label for="#">E-mail</label>
                        <input type="text" name="email" placeholder="Enter Your E-mail" required>
                    </div>
                    <div class="inputBox">
                        <i class="fa-solid fa-lock"></i>
                        <label for="password">Password</label>
                        <input type="password" id="registerPassword" name="password" placeholder="Enter Your Password" required>
                        <span class="eye" onclick="togglePasswordVisibility('registerPassword', 'hide3', 'hide4')">
                            <i id="hide3" class="fa-solid fa-eye"></i>
                            <i id="hide4" class="fa-solid fa-eye-slash"></i>
                        </span>
                    </div>
                    <div class="inputBox">
                        <i class="fa-solid fa-lock"></i>
                        <label for="confirmPass">Confirm Password</label>
                        <input type="password" id="confirmPass" placeholder="Re-Enter Your Password" required>
                        <span class="eye" onclick="togglePasswordVisibility('confirmPass', 'hide5', 'hide6')">
                            <i id="hide5" class="fa-solid fa-eye"></i>
                            <i id="hide6" class="fa-solid fa-eye-slash"></i>
                        </span>
                    </div>
                    <p id="message" style="color: rgba(168,147,120,255);"></p>
                    <button class="button" type="submit" name="register">Register</button>
                </form>
                <p class="lgnBttn">Already have an account? <a href="#">Login!</a></p>
                <script>
                    const container = document.querySelector(".container");
                    const loginForm = document.querySelector('.loginForm');
                    const registerForm = document.querySelector('.registerForm');
                    const rgstrBttn = document.querySelector('.rgstrBttn');
                    const lgnBttn = document.querySelector('.lgnBttn');
                    const currentHeight = parseInt(window.getComputedStyle(container).height);

                    rgstrBttn.addEventListener('click', () => {
                        registerForm.classList.add('active');
                        loginForm.classList.add('active');
                        document.title = "Register";
                        container.style.height = (currentHeight + 125) + 'px'; 
                    });

                    lgnBttn.addEventListener('click', () => {
                        registerForm.classList.remove('active');
                        loginForm.classList.remove('active');
                        document.title = "Login";
                        container.style.height = (currentHeight - 50) + 'px';
                    });

                    function togglePasswordVisibility(inputId, eyeId, slashId) {
                        var passwordField = document.getElementById(inputId);
                        var visibleEye = document.getElementById(eyeId);
                        var invisibleEye = document.getElementById(slashId);

                        if (passwordField.type === "password") {
                            passwordField.type = "text";
                            visibleEye.style.display = "block";
                            invisibleEye.style.display = "none";
                        } else {
                            passwordField.type = "password";
                            visibleEye.style.display = "none";
                            invisibleEye.style.display = "block";
                        }
                    }

                    function validatePass() {
                        var password = document.getElementById("registerPassword").value;
                        var confirmPass = document.getElementById("confirmPass").value;
                        var messageElement = document.getElementById("message");

                        if (password !== confirmPass) {
                            messageElement.textContent = "Passwords do not match.";
                            return false;
                        } else {
                            messageElement.textContent = "";
                            return true;
                        }
                    }
                </script>
            </div>
        </div>
    </body>
</html>