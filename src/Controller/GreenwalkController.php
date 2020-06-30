<?php

namespace App\Controller;

use App\Entity\Greenwalk;
use App\Entity\User;
use App\Form\AddGreenwalkType;
use App\Repository\GreenwalkRepository;
use App\Services\APIREST;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use phpDocumentor\Reflection\Types\Boolean;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GreenwalkController
 * @package App\Controller
 * @Route("/greenwalk", name="greenwalk_")
 */
class GreenwalkController extends AbstractFOSRestController
{

    /**
     * @Rest\Get("/getGreenwalk")
     * @Rest\View(serializerGroups={"greenWalk"})
     * @IsGranted("ROLE_USER")
     */
    public function getGreenwalksByUser(): View
    {
        return APIREST::onSuccess($this->getUser()->getRegisteredGreenWalks());
    }

    /**
     * @Rest\Post("", name="add")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return View
     */
    public function add(Request $request, EntityManagerInterface $entityManager)
    {
        $greenwalk = new Greenwalk();
        $form = $this->createForm(AddGreenwalkType::class, $greenwalk);
        $formError = APIREST::checkForm($form, $request);

        if ($formError) {
            return $formError;
        }

        $greenwalk->setAuthor($this->getUser());

        $entityManager->persist($greenwalk);
        $entityManager->flush();

        return APIREST::onSuccess(['id' => $greenwalk->getId()]);
    }

    /**
     * @Rest\Delete("/{id}", name="delete")
     * @IsGranted("ROLE_USER")
     * @param Greenwalk $greenwalk
     * @param EntityManagerInterface $entityManager
     * @return View
     */
    public function delete(Greenwalk $greenwalk, EntityManagerInterface $entityManager, SecurityController $securityController)
    {
        if ($securityController->isGranted('ROLE_ADMIN') || $this->getUser() === $greenwalk->getAuthor()) {
            $greenwalk->setState(false);
            $entityManager->persist($greenwalk);
            $entityManager->flush();
            return APIREST::onSuccess([true]);
        } else {
            return APIREST::onError('bad user to delete greenwalk');
        }
    }

    /**
     * @Rest\Get("/coordinate/{latitude}/{longitude}")
     * @Rest\View(serializerGroups={"greenWalk"})
     * @IsGranted("ROLE_USER")
     * @param float $latitude
     * @param float $longitude
     * @param GreenwalkRepository $greenwalkRepository
     * @return View
     */
    public function getAll(float $latitude, float $longitude, GreenwalkRepository $greenwalkRepository)
    {
        return APIREST::onSuccess($greenwalkRepository->findAllByCoordinate($latitude, $longitude));
    }

    /**
     * @Rest\Get("/{id}/{action}", name="registerUnregister")
     * @IsGranted("ROLE_USER")
     * @param Greenwalk $greenwalk
     * @param string $action
     * @param EntityManagerInterface $entityManager
     * @return View
     */
    public function registerUser(Greenwalk $greenwalk, string $action, EntityManagerInterface $entityManager, \Swift_Mailer $mailer)
    {
        if($greenwalk->getDatetime() < new \DateTime('now')){
            return APIREST::onError('This GreenWalk is not available anymore');
        }

        $user = $this->getUser();

        if ($action === "unsubscribe") {
            $greenwalk->removeParticipant($user);
            /* Envoie de mail dans le cas ou un utilisateur se désinscrit à une greenwalk */
            try {
                $message = (new \Swift_Message('Annulation de Greenwalk'))
                    ->setFrom('greenwalk.communication@gmail.com')
                    ->setTo($user->getEmail())
                    ->setBody($this->renderView('emails/cancelRegisterGreenwalk.html.twig', [
                        'greenwalk' => $greenwalk->getName(),
                        'date' => $greenwalk->getDatetime()->format('Y-m-d'),
                        'hour' => str_replace('-','h',$greenwalk->getDatetime()->format('H-i')).'min',
                        'street' => $greenwalk->getStreet(),
                        'city' => $greenwalk->getCity(),
                        'zipcode' => $greenwalk->getZipCode()
                    ]),'text/html');
                $mailer->send($message);
            } catch (\Exception $e) {
                var_dump($e->getMessage(), $e->getTraceAsString());
            }

        } else if ($action === "subscribe"){
            $greenwalk->addParticipant($user);
            /* Envoie de mail dans le cas ou un utilisateur s'inscrit à une greenwalk */
            try {
                $message = (new \Swift_Message('Participation à un GreenWalk'))
                    ->setFrom('greenwalk.communication@gmail.com')
                    ->setTo($user->getEmail())
                    ->setBody($this->renderView('emails/validationRegisterGreenwalk.html.twig', [
                        'greenwalk' => $greenwalk->getName(),
                        'date' => $greenwalk->getDatetime()->format('Y-m-d'),
                        'hour' => str_replace('-','h',$greenwalk->getDatetime()->format('H-i')).'min',
                        'street' => $greenwalk->getStreet(),
                        'city' => $greenwalk->getCity(),
                        'zipcode' => $greenwalk->getZipCode()
                    ]),
                        'text/html');

                $mailer->send($message);
            } catch (\Exception $e) {
                var_dump($e->getMessage(), $e->getTraceAsString());
            }
        }

        $entityManager->persist($greenwalk);
        $entityManager->flush();

        return APIREST::onSuccess(true);
    }

    /**
     * @Rest\Get("/{id}", name="getOne")
     * @IsGranted("ROLE_USER")
     * @Rest\View(serializerGroups={"greenWalk"})
     * @param Greenwalk $greenwalk
     * @return View
     */
    public function getOne(Greenwalk $greenwalk)
    {
        return APIREST::onSuccess($greenwalk);
    }
}
