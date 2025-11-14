
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Test</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f4; }
        .login-container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 300px; }
        h1 { text-align: center; }
        .input-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; }
        input { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 0.75rem; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        button:hover { background-color: #0056b3; }
        #result { margin-top: 1rem; padding: 1rem; background-color: #e9ecef; border-radius: 4px; word-wrap: break-word; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="login-container">
    <h1>API Login Test</h1>
    <form id="loginForm">
        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
    <div id="result">Response will be shown here...</div>
</div>

<script>
    document.getElementById('loginForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const resultDiv = document.getElementById('result');

        resultDiv.textContent = 'Loading...';

        try {
            const loginUrl = '{{ route('login') }}';
            const response = await fetch(loginUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || `HTTP error! Status: ${response.status}`);
            }

            resultDiv.textContent = 'Success!\n' + JSON.stringify(result, null, 2);

        } catch (error) {
            resultDiv.textContent = 'Error: ' + error.message;
        }
    });
</script>

</body>
</html>
