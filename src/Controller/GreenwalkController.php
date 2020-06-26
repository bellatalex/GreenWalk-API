<?php

namespace App\Controller;

use App\Entity\Greenwalk;
use App\Form\AddGreenwalkType;
use App\Form\SearchGreenwalkType;
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
     * @Rest\Post("/", name="add")
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
     * @Rest\Get("/{id}", name="getOne")
     * @param Greenwalk $greenwalk
     * @return View
     */
    public function getOne(Greenwalk $greenwalk)
    {
        return APIREST::onSuccess($greenwalk);
    }

    /**
     * @Rest\Get("/", name="get")
     * @param GreenwalkRepository $greenwalkRepository
     * @return View
     */
    public function getAll(Request $request, GreenwalkRepository $greenwalkRepository)
    {
        $data = null;
        $form = $this->createForm(SearchGreenwalkType::class, $data);
        $form->submit($request->query, false);
        dd($form->getData());


        return APIREST::onSuccess($greenwalkRepository->findBy(['state' => true]));
    }


    //fonction qui inscrit l'utilisateur à une greenwalk / désinscrire l'utilisateur à une greenwalk
    //fonction qui inscrit l'utilisateur à une greenwalk qui n'est pas encore passé. (historique de greenwalk)


    /**
     * @Rest\Get("/{id}/{action}", name="registerUnregister")
     * @IsGranted("ROLE_USER")
     * @param Greenwalk $greenwalk
     * @param Boolean $action
     * @param EntityManagerInterface $entityManager
     * @return View
     */
    public function registerUser(Greenwalk $greenwalk, Boolean $action, EntityManagerInterface $entityManager)
    {
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
}
