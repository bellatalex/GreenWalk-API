<?php

namespace App\Service;

use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment as Templating;

class MailService
{
    private $internalEmail = ['reservation.emotion@gmail.com' => 'GreenWalk'];

    /**
     * @var Swift_Mailer
     */
    private $mailer;
    /**
     * @var Templating
     */
    private $templating;

    /**
     * MailService constructor.
     * @param Swift_Mailer $mailer
     * @param Templating $templating
     */
    public function __construct(Swift_Mailer $mailer, Templating $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * ========================================================================
     *                        Prepared Mail Function
     * ========================================================================
     */

    public function sendMailAccountActivation(): bool
    {
        $subject = "";

        $body = $this->templating->render(
            'emails/accountActivation.html.twig',
            [
                'code' => uniqid()
            ]
        );

        $to = [];

        $mail = $this->prepareEmail($subject, $to, $body);

        return $this->sendEmail($mail);
    }

    /**
     * ========================================================================
     *                        General Mail Function
     * ========================================================================
     */

    public function prepareEmail(string $subject, array $to, string $body): Swift_Message
    {
        return (new Swift_Message())->setSubject($subject)
            ->setFrom($this->internalEmail)
            ->setTo($to)
            ->setBcc($this->internalEmail)
            ->setBody($body, 'text/html');
    }

    public function sendEmail(Swift_Message $mail): bool
    {
        return $this->mailer->send($mail);
    }

    public function sendEmailWithAttachment(Swift_Message $mail, array $attachments): bool
    {
        foreach ($attachments as $name => $path) {
            $mail->attach(
                Swift_Attachment::fromPath(
                    $path,
                    'application/pdf'
                )->setFilename($name)
            );
        }

        return $this->sendEmail($mail);
    }
}
