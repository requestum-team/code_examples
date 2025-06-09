<?php declare(strict_types=1);

namespace App\State\Password;

use ApiPlatform\State\ProcessorInterface;
use App\Dto\Password\ForgotPasswordDto;
use App\Entity\PasswordResetToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Metadata\Operation;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Uid\Uuid;

class ForgotPasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private MailerInterface $mailer,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof ForgotPasswordDto) {
            return null;
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $data->email]);

        if ($user) {
            $token = Uuid::v4()->toRfc4122();
            $expiresAt = new \DateTimeImmutable('+1 hour');

            $reset = new PasswordResetToken();
            $reset->setUser($user);
            $reset->setToken($token);
            $reset->setExpiresAt($expiresAt);
            $reset->setUsed(false);

            $this->em->persist($reset);
            $this->em->flush();

            $email = (new Email())
                ->from('no-reply@example.com')
                ->to($user->getEmail())
                ->subject('Reset your password')
                ->text("Use this token to reset your password: $token");

            $this->mailer->send($email);
        }

        return ['message' => 'If your email is registered, you’ll receive instructions shortly.'];
    }
}
