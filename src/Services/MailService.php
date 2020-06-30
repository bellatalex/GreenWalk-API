<?php


namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Templating\EngineInterface;

class MailService
{
    private $templating;

    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    function mail(EntityManagerInterface $entityManager, $object, \Swift_Mailer $mailer, $title, $to, $view, $content = 0, $ToOrCc = 'to')
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
                dd('test');
                $message->setBody($this->templating->render($view, $content), 'text/html');
            } else {
                $message->setBody($this->templating->render($view), 'text/html');
            }

            $mailer->send($message);

            $entityManager->persist($object);
            $entityManager->flush();

            return APIREST::onSuccess(true);
        } catch (\Exception $e) {
            return APIREST::onError($e->getMessage());
        }
    }
}