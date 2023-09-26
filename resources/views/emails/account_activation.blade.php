<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Activication</title>
</head>
<body>
    <h1>Hello, {{ $emailData['name'] }}</h1>
    <p>Your acount has been activated and this is your password:
        {{ $emailData['password'] }}
    </p>
</body>
</html>