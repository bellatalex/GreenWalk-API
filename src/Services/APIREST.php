<?php


namespace App\Services;


use FOS\RestBundle\View\View;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class APIREST
{

    /**
     * @param $message
     * @param int $httpErrorCode
     * @return View
     */
    public static function onSuccessMessage(String $message, int $httpErrorCode = Response::HTTP_OK): View
    {
        return View::create([
            'message' => $message
        ], $httpErrorCode);
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     * @return View|null
     */
    public static function checkForm(FormInterface $form, Request $request): ?View
    {
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return APIREST::errorForm($form);
        }

        return null;
    }

    /**
     * @param Form $form
     * @return View
     */
    public static function errorForm(FormInterface $form)
    {
        $errors = [];

        foreach ($form as $child) {
            if (!($child->isSubmitted() && $child->isValid())) {
                $iterator = $child->getErrors(true, false);

                foreach ($iterator as $error) {
                    $errors[$child->getName()][] = $error->getMessage();
                }
            }
        }

        return $errors ? self::onError($errors) : null;
    }

    /**
     * @param $messages
     * @param int $httpErrorCode
     * @return View
     */
    public static function onError($messages, $httpErrorCode = Response::HTTP_BAD_REQUEST): View
    {
        return self::onSuccess([
            'messages' => $messages
        ], $httpErrorCode);
    }

    /**
     * @param $data
     * @param int $httpErrorCode
     * @return View
     */
    public static function onSuccess($data, int $httpErrorCode = Response::HTTP_OK): View
    {
        return View::create($data, $httpErrorCode);
    }

}
