<?php

namespace App\Service;

use App\Entity\Rental;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment as Templating;

class MailService
{
    private $greenwalkMail = ['contact@greenwalk.com' => 'greenwalk'];

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
     *                        General Mail Function
     * ========================================================================
     */

    public function prepareEmail(string $subject, array $to, string $body): Swift_Message
    {
        return (new Swift_Message())->setSubject($subject)
            ->setFrom($this->greenwalkMail)
            ->setTo($to)
            ->setBcc($this->greenwalkMail)
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


    /**
     * ========================================================================
     *                        Prepared Mail Function
     * ========================================================================
     */

    public function sendMailContrat(Rental $rental): bool
    {
        $subject = 'Facture de votre réservation du '.$rental->getStartRentalDate()->format('d/m/Y');

        $body = $this->templating->render(
            'emails/contract.html.twig',
            [
                'rental' => $rental,
            ]
        );

        $to = [
            $rental->getClient()->getEmail() => $rental->getClient()->getLastname().' '.$rental->getClient(
                )->getFirstname(),
        ];

        $mail = $this->prepareEmail($subject, $to, $body);

        $contracts = $rental->getPdf()['contract'];

        $attachment = [
            'Contrat de location n°'.$rental->getId()
            => $contracts[count($contracts) - 1],
        ];

        return $this->sendEmailWithAttachment($mail, $attachment);
    }

    public function sendMailContact(string $firstname, string $lastname, $email, $message)
    {
        $subject = 'Demande de contact';

        $body = $this->templating->render(
            'emails/contact.html.twig',
            [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'mail' => $email,
                'message' => $message,
            ]
        );

        $mail = $this->prepareEmail(
            $subject,
            array_merge([$email], $this->greenwalkMail),
            $body
        );

        return $this->sendEmail($mail);

    }
}
