<?php


namespace App\Services;


use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;

class REST
{

    /**
     * @param $data
     * @param int $httpErrorCode
     * @return View
     */
    public static function onSuccess($data, int $httpErrorCode = Response::HTTP_OK): View
    {
        return View::create($data, $httpErrorCode);
    }

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
     * @param ConstraintViolationList $violation
     * @return View
     */
    public static function errorValidation(ConstraintViolationList $violation): View
    {
        $errors = [];
        foreach ($violation as $error) {
            $errors[$error->getPropertyPath()] = $error->getMessage();
        }

        return self::onError(null, $errors);
    }

    /**
     * @param null $message
     * @param $errors
     * @param int $httpErrorCode
     * @return View
     */
    public static function onError($message = null, $errors = null, $httpErrorCode = Response::HTTP_BAD_REQUEST): View
    {
        return self::onSuccess([
            'errors' => $errors,
            'message' => $message
        ], $httpErrorCode);
    }

}
