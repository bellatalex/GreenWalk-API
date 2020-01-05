<?php


namespace App\Services;


use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormErrorIterator;
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
            return APIREST::errorForm($form->getErrors(true, true));
        }

        return null;
    }

    /**
     * @param FormErrorIterator $errorIterator
     * @return View
     */
    public static function errorForm(FormErrorIterator $errorIterator): View
    {
        $errors = [];

        foreach ($errorIterator as $error) {
            $errors[] = $error->getMessage();
        }

        return self::onError($errors);
    }

    /**
     * @param null $message
     * @param $errors
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
