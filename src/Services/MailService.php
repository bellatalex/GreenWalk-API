<?php


namespace App\Services;

use Symfony\Component\Templating\EngineInterface;

class MailService
{
    private $templating;
    private $mailer;

    public function __construct(EngineInterface $templating, \Swift_Mailer $mailer)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
    }

    function mail($title, $to, $view, $content = 0, $ToOrCc = 'to')
    {
        try {
            $message = (new \Swift_Message($title))
                ->setFrom('greenwalk.communication@gmail.com');
            if ($ToOrCc == 'to') {
                $message->setTo($to);
            } elseif ($ToOrCc == 'cc') {
                $message->setCc($to);
                }
            if ($content != 0) {
                $message->setBody($this->templating->render($view, $content), 'text/html');
            } else {
                $message->setBody($this->templating->render($view), 'text/html');
            }

            $this->mailer->send($message);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}