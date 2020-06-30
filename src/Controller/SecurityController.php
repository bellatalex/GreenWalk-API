<?php

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Form\UserSignUpType;
use App\Repository\UserRepository;
use App\Services\APIREST;
use App\Services\MailService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class SecurityController
 * @package App\Controller
 * @Route("/auth", name="auth_")
 */
class SecurityController extends AbstractFOSRestController
{
    /**
     *  Create an user account
     *
     * Return a token associated to the new user
     *
     * @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="email",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="password",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="firstname",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="birthdate",
     *                  type="string"
     *              )
     *          )
     *     )
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns a token",
     *     @SWG\Schema(type="string[]",
     *         @SWG\Property(property="id", ref=@Model(type=Token::class))
     *     )
     * )
     * @SWG\Tag(name="Security")
     *
     * @Rest\Post("/signup", name="signup")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     * @return View
     */
    public function signUp(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, \Swift_Mailer $mailer, MailService $mailService): View
    {
        $user = new User();
        $form = $this->createForm(UserSignUpType::class, $user);
        $formError = APIREST::checkForm($form, $request);

        if ($formError) {
            return $formError;
        }

        $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));

        $token = $user->addToken(new Token());

        $mailService->mail('Création d\'un compte',$user->getEmail(),'emails/accountActivation.html.twig');

        $entityManager->persist($user);
        $entityManager->flush();

        return APIREST::onSuccess(['token' => (string)$token]);
    }

    /**
     * Sign in
     *
     * Return a token associated to the user if provided credential are good
     *
     * @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="email",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="password",
     *                  type="string"
     *              )
     *          )
     *     )
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a token",
     *     @SWG\Schema(type="string[]",
     *         @SWG\Property(property="id", ref=@Model(type=Token::class))
     *     )
     * )
     * @SWG\Tag(name="Security")
     *
     * @Rest\Post("/signin", name="signin")
     * @param Request $request
     * @param UserRepository $userRepository
     * @return View|null
     */
    public function signIn(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $form = $this->createFormBuilder()
            ->add('email', null, [
                'constraints' => [
                    new NotBlank(),
                    new EmailConstraint()
                ]
            ])
            ->add('password', null, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->getForm();

        $formError = APIREST::checkForm($form, $request);
        if ($formError) {
            return $formError;
        }

        ["email" => $email, "password" => $password] = $form->getData();

        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user || !$passwordEncoder->isPasswordValid($user, $password)) {
            return APIREST::onError('bad credential', Response::HTTP_UNAUTHORIZED);
        }

        if (!$user->getState()) {
            return APIREST::onError('You\'re account have been deactivated', Response::HTTP_FORBIDDEN);
        }

        $token = $user->addToken(new Token());
        $entityManager->persist($user);

        $entityManager->flush();

        return APIREST::onSuccess(["token" => $token]);
    }

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Return current user information",
     * )
     * @SWG\Tag(name="Security")
     *
     * @Rest\Get("/info", name="info")
     * @Rest\View(serializerGroups={"user"})
     * @IsGranted("ROLE_USER")
     */
    public function info()
    {
        return APIREST::onSuccess($this->getUser());
    }
}
