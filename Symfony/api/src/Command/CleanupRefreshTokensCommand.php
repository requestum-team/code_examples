<?php declare(strict_types=1);


namespace App\Command;

use App\Entity\RefreshToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:cleanup-refresh-tokens',
    description: 'Deletes expired refresh tokens from the database.',
)]
class CleanupRefreshTokensCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $qb = $this->entityManager->createQueryBuilder();

        $deleted = $qb->delete(RefreshToken::class, 'rt')
            ->where('rt.expiresAt < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute();

        $output->writeln("Deleted $deleted expired refresh token(s).");

        return Command::SUCCESS;
    }
}

