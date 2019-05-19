<?php

namespace Core;

class Mailer
{
    /**
     * Email subject
     *
     * @var string
     */
    private $subject;

    /**
     * Email senders
     *
     * @var array
     */
    private $from;

    /**
     * Email receivers
     *
     * @var array
     */
    private $to;

    /**
     * Email message
     *
     * @var string|html
     */
    private $message;

    /**
     * Set default subject of the email, senders, receivers and message
     */
    public function __construct()
    {
        $this->subject("")
            ->from("")
            ->to("")
            ->message("");
    }

    /**
     * Set email subject
     *
     * @param  string $subject
     * @return Mailer
     */
    public function subject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Set email senders
     *
     * @param  array $from
     * @return Mailer
     */
    public function from($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * Set email receivers
     *
     * @param  array $to
     * @return Mailer
     */
    public function to($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * Set the email message
     * @param  string $message
     * @return Mailer
     */
    protected function message($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Set email message
     *
     * @param  string $twig_file
     * @param  array $params
     * @return Mailer
     */
    public function template($twig_file, $params=[])
    {
        $use_dist = filter_var(config("app.use_dist"), FILTER_VALIDATE_BOOLEAN);
        $path = resources_path($use_dist ? "dist-views" : "views");

        $loader = new \Twig_Loader_Filesystem("{$path}/emails");
        $twig = new \Twig_Environment($loader, config('mailer.settings'));
        $template = $twig->load($twig_file);

        $this->message = $template->render($params);
        return $this;
    }

    /**
     * Send the email
     *
     * @return int Number of recipient
     */
    public function send()
    {
        $config = config('mailer');

        $mail_host = $config['host'];
        $mail_port = $config['port'];
        $mail_username = $config['username'];
        $mail_password = $config['password'];

        $transport = (new \Swift_SmtpTransport($mail_host, $mail_port))
                        ->setUsername($mail_username)
                        ->setPassword($mail_password);

        $mail = new \Swift_Mailer($transport);

        $message = (new \Swift_Message($this->subject))
                    ->setFrom($this->from)
                    ->setTo($this->to)
                    ->setBody($this->message, "text/html");

        $recipient_number = $mail->send($message);

        return $recipient_number;
    }
}
