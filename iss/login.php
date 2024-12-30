<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f9;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .hidden {
            display: none;
        }
        .message {
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Step 1: Enter Email -->
        <div id="email-step">
            <h2>Email Login</h2>
            <div class="form-group">
                <label for="email">Enter Your Email</label>
                <input type="email" id="email" placeholder="example@example.com" required>
            </div>
            <button onclick="sendLoginCode()">Send Login Code</button>
            <div id="email-message" class="message"></div>
        </div>

        <!-- Step 2: Enter Code -->
        <div id="code-step" class="hidden">
            <h2>Enter Login Code</h2>
            <div class="form-group">
                <label for="code">Enter the Code</label>
                <input type="text" id="code" placeholder="Enter the code you received" required>
            </div>
            <button onclick="verifyCode()">Verify Code</button>
            <div id="code-message" class="message"></div>
        </div>
    </div>

    <script>
        const API_URL = 'http://localhost/iss/api/send_login.php';

        function sendLoginCode() {
            const email = document.getElementById('email').value.trim();
            const emailMessage = document.getElementById('email-message');

            if (!email) {
                emailMessage.textContent = "Please enter your email.";
                emailMessage.style.color = "red";
                return;
            }

            fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                })
    .then(response => response.text())
    .then(text => {
        console.log("Raw Response:", text);
        try {
            const data = JSON.parse(text);
            if (data.success) {
                document.getElementById('email-message').textContent = "Login code sent to your email.";
                sessionStorage.setItem('loginCode', data.code);
                document.getElementById('email-step').classList.add('hidden');
                document.getElementById('code-step').classList.remove('hidden');
            } else {
                document.getElementById('email-message').textContent = data.message;
            }
        } catch (err) {
            console.error("JSON Parsing Error:", err);
            document.getElementById('email-message').textContent = "Unexpected server response.";
        }
    })
    .catch(err => {
        console.error("Fetch Error:", err);
        document.getElementById('email-message').textContent = "An error occurred. Please try again.";
    });
    }

        function verifyCode() {
            const code = document.getElementById('code').value;
            const codeMessage = document.getElementById('code-message');
            

            if (code === sessionStorage.getItem('loginCode')) {
                codeMessage.textContent = "Login successful!";
                codeMessage.style.color = "green";

                // Clear the code from local storage after successful login
                sessionStorage.removeItem('loginCode');

                // Redirect or perform other actions
                setTimeout(() => {
                    window.location.href = 'dashboard.html'; // Update to your actual dashboard URL
                }, 2000);
            } else {
                codeMessage.textContent = "Invalid code. Please try again.";
                codeMessage.style.color = "red";
            }
        }
    </script>
</body>
</html>
