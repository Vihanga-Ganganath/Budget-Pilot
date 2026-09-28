<?php
/**
 * Mailer.php - socket-based SMTP email client for sending verification codes.
 * No external libraries needed (uses PHP's built-in socket stream functions).
 */

if (!function_exists('make_code')) {
    /** Generates a random 6-digit verification code, e.g. "048213" */
    function make_code(): string {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('smtp_read')) {
    /** Reads one full SMTP reply line-by-line. */
    function smtp_read($fp): array {
        $text = '';
        while (($line = fgets($fp, 515)) !== false) {
            $text .= $line;
            if (strlen($line) < 4 || $line[3] === ' ') {
                break;
            }
        }
        return [(int) substr($text, 0, 3), trim($text)];
    }
}

if (!function_exists('smtp_cmd')) {
    /** Sends an SMTP command and checks the response code. */
    function smtp_cmd($fp, string $command, array $okCodes): bool {
        fwrite($fp, $command . "\r\n");
        [$code, $text] = smtp_read($fp);
        if (!in_array($code, $okCodes, true)) {
            error_log('SMTP error: ' . $text);
            return false;
        }
        return true;
    }
}

if (!function_exists('send_verification_email')) {
    /**
     * Sends a 6-digit verification code to the specified user email address.
     */
    function send_verification_email(string $to, string $name, string $code): bool {
        $to   = str_replace(["\r", "\n"], '', $to);
        $name = trim(preg_replace('/[\r\n]+/', ' ', $name));

        $subject = 'Your Budget Pilot Verification Code';
        $body =
            "Hi $name,\n\n" .
            "Your verification code is: $code\n\n" .
            "It expires in " . CODE_MINUTES . " minutes. " .
            "If you didn't create an account, you can safely ignore this email.\n\n" .
            "Budget Pilot - Personal & Family Finance";

        $headers = [
            'Date: ' . date('r'),
            'From: =?UTF-8?B?' . base64_encode(MAIL_FROM_NAME) . '?= <' . MAIL_USER . '>',
            'To: <' . $to . '>',
            'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=',
            'Message-ID: <' . bin2hex(random_bytes(8)) . '@' . MAIL_HOST . '>',
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: base64',
        ];

        $message = implode("\r\n", $headers) . "\r\n\r\n" . chunk_split(base64_encode($body), 76, "\r\n");

        $errno = 0;
        $errstr = '';
        $fp = @stream_socket_client('ssl://' . MAIL_HOST . ':' . MAIL_PORT, $errno, $errstr, 15);

        if (!$fp) {
            error_log("SMTP connect failed: $errstr ($errno)");
            return false;
        }
        stream_set_timeout($fp, 15);

        [$greeting] = smtp_read($fp);

        $ok = $greeting === 220
            && smtp_cmd($fp, 'EHLO localhost', [250])
            && smtp_cmd($fp, 'AUTH LOGIN', [334])
            && smtp_cmd($fp, base64_encode(MAIL_USER), [334])
            && smtp_cmd($fp, base64_encode(MAIL_PASS), [235])
            && smtp_cmd($fp, 'MAIL FROM:<' . MAIL_USER . '>', [250])
            && smtp_cmd($fp, 'RCPT TO:<' . $to . '>', [250, 251])
            && smtp_cmd($fp, 'DATA', [354]);

        if ($ok) {
            fwrite($fp, $message . ".\r\n");
            [$code2, $text] = smtp_read($fp);
            $ok = ($code2 === 250);
            if (!$ok) {
                error_log('SMTP error after message: ' . $text);
            }
        }

        fwrite($fp, "QUIT\r\n");
        fclose($fp);

        return $ok;
    }
}
