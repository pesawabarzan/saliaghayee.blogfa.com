<?php
/** lib/SimpleMailer.php
 * لایه ساده SMTP با پشتیبانی AUTH LOGIN/PLAIN (بدون وابستگی خارجی).
 * توجه: برای محیط‌های production پیشنهاد می‌شود از PHPMailer استفاده کنید.
 * این کلاس برای سازگاری هاست اشتراکی طراحی شده است.
 */
class SimpleMailer {
  private array $cfg;
  public function __construct(array $cfg) { $this->cfg = $cfg; }

  public function send(string $to, string $subject, string $body, string $fromEmail = null, string $fromName = null): bool {
    if (!($this->cfg['smtp_enabled'] ?? false)) {
      // fallback به mail()
      $headers = "MIME-Version: 1.0\r\nContent-type:text/html; charset=UTF-8\r\n";
      if ($fromEmail) $headers .= "From: " . ($fromName ? "$fromName <{$fromEmail}>" : $fromEmail) . "\r\n";
      return mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $body, $headers);
    }
    $host = $this->cfg['smtp_host']; $port = (int)$this->cfg['smtp_port'];
    $user = $this->cfg['smtp_user']; $pass = $this->cfg['smtp_pass'];
    $secure = $this->cfg['smtp_secure']; // tls | ssl | none

    $remote = ($secure === 'ssl' ? "ssl://" : "") . $host . ":" . $port;
    $fp = stream_socket_client($remote, $errno, $errstr, 15, STREAM_CLIENT_CONNECT);
    if (!$fp) return false;
    stream_set_timeout($fp, 15);

    $read = function() use ($fp) { return fgets($fp, 515); };
    $write = function($cmd) use ($fp) { fwrite($fp, $cmd."\r\n"); };

    $read();
    $write("EHLO localhost");
    $ehlo = $read();

    if ($secure === 'tls') {
      $write("STARTTLS");
      $read();
      if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        fclose($fp); return false;
      }
      $write("EHLO localhost"); $read();
    }

    if ($user && $pass) {
      $write("AUTH LOGIN");
      $read();
      $write(base64_encode($user)); $read();
      $write(base64_encode($pass)); $read();
    }

    $from = $fromEmail ?: $user;
    $write("MAIL FROM:<{$from}>"); $read();
    $write("RCPT TO:<{$to}>"); $read();
    $write("DATA"); $read();

    $headers = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . ($fromName ? "$fromName <{$from}>" : $from) . "\r\n";
    $headers .= "To: {$to}\r\n";
    $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
    $headers .= "Date: " . date('r') . "\r\n";
    $msg = $headers . "\r\n" . $body . "\r\n.";

    $write($msg); $read();
    $write("QUIT");
    fclose($fp);
    return true;
  }
}
