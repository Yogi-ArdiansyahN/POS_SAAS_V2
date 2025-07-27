<?php

function datenow()
{
    return date('Y-m-d');
}

function get_device($ag = false)
{
    $agent = (($ag == false) ? \Config\Services::request()->getUserAgent() : $ag);
    if ($agent->isMobile()) {
        $device = $agent->getMobile();
        $browsers = $agent->getBrowser();
    } else if ($agent->isBrowser()) {
        $device = $agent->getPlatform();
        $browsers = $agent->getBrowser();
    }

    return $browsers . "(" . $device . ")";
}

function send_email($data)
{
    $email_smtp = \Config\Services::email();

    $config["protocol"] = "smtp";
    //isi sesuai nama domain/mail server
    $config["SMTPHost"]  = "smtp.mailketing.id";
    //alamat email SMTP
    $config["SMTPUser"]  = "mailketing5964";
    //password email SMTP
    $config["SMTPPass"]  = "Aigd2q09";
    $config["mailtype"] = "html";
    $config["SMTPPort"]  = 587;
    $config["SMTPCrypto"] = "tls";

    $email_smtp->initialize($config);
    $email_smtp->setFrom('no-reply@project-ci4.co.id', 'ProjectCI4');
    $email_smtp->setTo($data['to']);
    $email_smtp->setSubject($data['subject']);
    $email_smtp->setMessage($data['message']);
    return $email_smtp->send();
}
