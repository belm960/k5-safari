<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './Exception.php';
require './PHPMailer.php';
require './SMTP.php';

// Get the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $to = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    $nameFrom = "K5-safari";
    $date = htmlspecialchars($data['date']);
    $phone = htmlspecialchars($data['phone']);
    $name = htmlspecialchars($data['name']);
    $message = htmlspecialchars($data['message']);

    $from = "sawerkit23@gmail.com";
    $subject = "=?UTF-8?B?" . base64_encode("お問い合わせありがとうございます") . "?=";
    $body = <<<EOT
        $name 様

        このたびはお問い合わせいただき、誠にありがとうございます。
        内容を確認のうえ、追ってご連絡させていただきます。

        以下がご入力いただいた内容です：
        • 日時 : $date
        • 電話番号: $phone
        • メッセージ: $message

        ご不明な点がございましたら、このメールに返信いただければ幸いです。

        よろしくお願いいたします。
        K5-safari
        EOT;

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Use your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'sawerkit23@gmail.com'; // Your email
        $mail->Password = 'rpco rcfn ykcl nsnt'; // Your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom($from, $nameFrom);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        echo "<h3>Email sent successfully to $to!</h3>";
    } catch (Exception $e) {
        echo "<h3>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</h3>";
    }
} else {
    echo "<h3>Invalid request method. Please use the form to submit.</h3>";
}
?>
