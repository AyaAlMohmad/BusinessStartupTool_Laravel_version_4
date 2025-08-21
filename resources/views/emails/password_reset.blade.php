<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body>
    <h1>Password Reset</h1>
    <p>To reset your password, click the link below:</p>
    <a href="{{ url('password/reset', $token) }}">Reset Password</a>
</body>
</html>