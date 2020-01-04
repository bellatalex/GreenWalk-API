<?php


namespace App\Controller;


use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View;

class SecurityController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/signin", name="signIn")
     * @View()
     */
    public function signIn()
    {

        return $this->view(['test']);
    }
}
