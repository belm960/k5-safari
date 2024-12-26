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
        if (!$to) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid email address.']);
            exit;
        }

        $nameFrom = "K5-safari";
        $date = htmlspecialchars($data['date']);
        $phone = htmlspecialchars($data['phone']);
        $name = htmlspecialchars($data['name']);
        $message = htmlspecialchars($data['message']);

        $from = "info@k5-safari1.com";
        $subject = "=?UTF-8?B?" . base64_encode("お問い合わせありがとうございます") . "?=";
        $adminSubject = "=?UTF-8?B?" . base64_encode("新しいフォーム登録通知") . "?=";
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
        $adminBody = <<<EOT
        【管理者様】

        以下のユーザーがフォームに登録しました：

        • 名前: $name
        • 日時: $date
        • 電話番号: $phone
        • メッセージ: $message

        内容を確認し、必要に応じて対応をお願いします。

        ----------------------------------------

        よろしくお願いいたします。
        K5-safari
        EOT;
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'sv16274.xserver.jp';
            $mail->SMTPAuth = true;
            $mail->Username= 'info@k5-safari1.com';
            $mail->Password = 'K5-Safari2024'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            // Recipients
            $mail->setFrom($from, $nameFrom);
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = nl2br($body); // Convert newlines to HTML breaks

            $mail->send();
            $mail->clearAddresses();
            $mail->addAddress($from);
            $mail->Subject = $subject;
            $mail->Body = nl2br($adminBody);
            $mail->send();
            echo json_encode(['status' => 'success', 'message' => "Email sent successfully to $to!"]);
        } catch (Exception $e) {
            echo "Mailer Error: {$mail->ErrorInfo} {$e}"; // Log error instead of exposing it
            echo json_encode(['status' => 'error', 'message' =>  $e]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Please use the form to submit.']);
    }
