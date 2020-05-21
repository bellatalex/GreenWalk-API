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
        
        return APIREST::onSuccess('testo');
    }
}