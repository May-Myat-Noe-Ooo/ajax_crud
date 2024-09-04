<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email with PHPMailer</title>
</head>
<body>
    <h2>Send Email</h2>
    <form action="send_mail.php" method="POST">
        <label for="email">Enter your email:</label>
        <input type="email" name="email" id="email" required>
        <button type="submit" name="send">Send</button>
    </form>
</body>
</html>
