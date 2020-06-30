<?php

namespace App\Controller;

use App\Entity\Greenwalk;
use App\Entity\User;
use App\Form\AddGreenwalkType;
use App\Repository\GreenwalkRepository;
use App\Services\APIREST;
use App\Services\MailService;
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
    public function delete(Greenwalk $greenwalk, EntityManagerInterface $entityManager, SecurityController $securityController, MailService $mailService, \Swift_Mailer $mailer)
    {
        if ($securityController->isGranted('ROLE_ADMIN') || $this->getUser() === $greenwalk->getAuthor()) {
            $listAllUser = $greenwalk->getParticipants();
            $allEmail = [];
            foreach ($listAllUser as $user){
                array_push($allEmail,$user->getEmail());
            }
            $greenwalk->setState(false);
            $entityManager->persist($greenwalk);
            $entityManager->flush();

            $content = [
                'greenwalk' => $greenwalk->getName(),
                'date' => $greenwalk->getDatetime()->format('Y-m-d'),
                'hour' => str_replace('-','h',$greenwalk->getDatetime()->format('H-i')).'min',
                'street' => $greenwalk->getStreet(),
                'city' => $greenwalk->getCity(),
                'zipcode' => $greenwalk->getZipCode()
            ];
            $mailService->mail('Annulation de GreenWalk',$allEmail,'emails/cancelRegisterGreenwalk.html.twig',$content,'cc');

            return APIREST::onSuccess(true);

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
    public function registerUser(Greenwalk $greenwalk, string $action, EntityManagerInterface $entityManager, MailService $mailService,\Swift_Mailer $mailer)
    {
        if($greenwalk->getDatetime() < new \DateTime('now')){
            return APIREST::onError('This GreenWalk is not available anymore');
        }

        if(!in_array($action, ['subscribe', 'unsubscribe'])){
            return APIREST::onError('You must choose an Action');
        }

        $user = $this->getUser();

        if ($action === "unsubscribe") {
            $greenwalk->removeParticipant($user);
            $template = 'emails/cancelRegisterGreenwalk.html.twig';
            $title = 'Annulation de GreenWalk';
        } else {
            $greenwalk->addParticipant($user);
           $template = 'emails/validationRegisterGreenwalk.html.twig';
           $title = 'Participation à une GreenWalk';
        }

        $content = [
            'greenwalk' => $greenwalk->getName(),
            'date' => $greenwalk->getDatetime()->format('Y-m-d'),
            'hour' => str_replace('-','h',$greenwalk->getDatetime()->format('H-i')).'min',
            'street' => $greenwalk->getStreet(),
            'city' => $greenwalk->getCity(),
            'zipcode' => $greenwalk->getZipCode()
        ];
        try {
            $mailService->mail( $title,$user->getEmail(),$template,$content);
            $entityManager->persist($greenwalk);
            $entityManager->flush();

            return APIREST::onSuccess(true);
        } catch (\Exception $e){
            return APIREST::onError($e->getMessage());
        }
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
