<?php


namespace App\Controller;


use App\Entity\Token;
use App\Entity\User;
use App\Form\UserType;
use App\Services\REST;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as RestAnotation;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractFOSRestController
{
    /**
     * @RestAnotation\Post("/signup", name="signup")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     * @return View
     */
    public function signUp(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager): View
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $error = REST::checkForm($form, $request);

        if ($error) {
            return $error;
        }

        $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));

        $token = $user->addToken(new Token());

        $entityManager->persist($user);
        $entityManager->flush();

        return REST::onSuccess(['token' => (string)$token]);
    }
}
