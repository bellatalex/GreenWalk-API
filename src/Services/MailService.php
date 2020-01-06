<?php

namespace App\Service;

use App\Entity\Rental;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment as Templating;

class MailService
{
    private $eMotionMail = ['reservation.emotion@gmail.com' => 'eMotion'];

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
            ->setFrom($this->eMotionMail)
            ->setTo($to)
            ->setBcc($this->eMotionMail)
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
