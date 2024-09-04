<?php
require 'vendor/autoload.php'; // Ensure you have included the autoload file from Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['send'])) {
    $email = $_POST['email'];

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';                     // Specify SMTP server (use your SMTP server)
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'oomaymyatnoe21@gmail.com';           // SMTP username
        $mail->Password = 'xuxjzhbzihpnpaly';              // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption, `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        // Recipients
        $mail->setFrom('oomaymyatnoe21@gmail.com', 'May Myat Noe Oo'); // From email and name
        $mail->addAddress($email);                            // Add recipient email

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Welcome to PHPMailer Test';
        $mail->Body    = "Welcome to $email, Happy Friday!";
        $mail->AltBody = "Welcome to $email, Happy Friday!";  // Plain text alternative

        // Send email
        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo 'Invalid request';
}
?>
