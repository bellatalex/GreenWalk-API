<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Services\APIREST;
use App\Form\UserSignUpType;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Greenwalk;
use App\Form\AddGreenwalkType;

/**
 * Class GreenwalkController
 * @package App\Controller
 * @Route("/greenwalk", name="greenwalk_")
 */
class GreenwalkController extends AbstractFOSRestController
{
    /**
     * @Rest\Post("/add", name="add")
     */
    public function add (Request $request, EntityManagerInterface $entityManager)
    {
        dump(date('m/d/Y h:i:s', time()));

        $greenwalk = new Greenwalk();
        $form = $this->createForm(AddGreenwalkType::class, $greenwalk);
        $formError = APIREST::checkForm($form, $request);

         if ($formError) {
             return $formError;
         }

        $entityManager->persist($greenwalk);
        $entityManager->flush();


        return APIREST::onSuccess(['id' => $greenwalk->getId()]);
    }
}
