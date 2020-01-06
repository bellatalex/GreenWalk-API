<?php


namespace App\Services;


use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class UserActivationService
{
    public const STATE_PENDING = 'pending';
    public const STATE_ACTIVATED = 'activated';
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function initActivation(): array
    {
        return [
            'state' => self::STATE_PENDING,
            'code' => self::generateActivationCode(),
            'date' => new DateTime()
        ];
    }

    public static function generateActivationCode(): int
    {
        return rand(1000, 9999);
    }

    public function checkActivationCode(User $user, int $code): bool
    {
        $activation = $user->getActivation();
        if (self::STATE_ACTIVATED === $activation['state']) {
            return true;
        }

        if ($code !== $activation['code']) {
            return false;
        }

        $activation['state'] = self::STATE_ACTIVATED;
        $user->setActivation($activation);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return true;
    }
}
