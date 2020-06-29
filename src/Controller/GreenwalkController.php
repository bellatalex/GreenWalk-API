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
    public function delete(Greenwalk $greenwalk, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($greenwalk);
        $entityManager->flush();
        return APIREST::onSuccess([true]);
    }

    /**
     * @Rest\Get("/{latitude}/{longitude}")
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


    //fonction qui inscrit l'utilisateur à une greenwalk / désinscrire l'utilisateur à une greenwalk
    //fonction qui inscrit l'utilisateur à une greenwalk qui n'est pas encore passé. (historique de greenwalk)


    /**
     * @Rest\Get("/{id}/{action}", name="registerUnregister")
     * @param Greenwalk $greenwalk
     * @param Boolean $action
     * @param EntityManagerInterface $entityManager
     * @return View
     */
    public function registerUser(Greenwalk $greenwalk, Boolean $action, EntityManagerInterface $entityManager)
    {
        if($greenwalk->getDatetime() > date()){
            return APIREST::onError('Cette GreenWalk est déjà passé');
        }

        $user = $this->getUser();

        if ($action) {
            $greenwalk->addParticipant($user);
            //Envoyer un mail au user pour l'informer qu'il est bien inscrit à la greenwalk
        } else {
            $greenwalk->removeParticipant($user);
            //Envoie un mail pour informer à l'utilisateur qu'il s'est désinscrit
        }

        $entityManager->persist($greenwalk);
        $entityManager->flush();

        return APIREST::onSuccess([true]);
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
