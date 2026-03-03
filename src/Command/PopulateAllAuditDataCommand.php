<?php

namespace App\Command;

use App\Entity\GestionDeCours\Cours;
use App\Entity\GestionDeCours\Chapitre;
use App\Entity\Challenge;
use App\Entity\Evenement;
use App\Entity\Communaute;
use App\Entity\Exercice;
use App\Entity\Quiz;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:populate-all-audit-data',
    description: 'Populate audit data for all content types by making small updates',
)]
class PopulateAllAuditDataCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Populating Audit Data for All Content Types');

        $totalUpdates = 0;

        // 1. Cours
        $io->section('📚 Courses');
        $cours = $this->entityManager->getRepository(Cours::class)->findAll();
        foreach (array_slice($cours, 0, 3) as $c) {
            $desc = $c->getDescription();
            $c->setDescription($desc . ' ');
            $this->entityManager->flush();
            $c->setDescription(trim($desc));
            $this->entityManager->flush();
            $totalUpdates += 2;
        }
        $io->success("Updated " . min(3, count($cours)) . " courses");

        // 2. Chapitres
        $io->section('📖 Chapters');
        $chapitres = $this->entityManager->getRepository(Chapitre::class)->findAll();
        foreach (array_slice($chapitres, 0, 3) as $ch) {
            $contenu = $ch->getContenu();
            $ch->setContenu($contenu . ' ');
            $this->entityManager->flush();
            $ch->setContenu(trim($contenu));
            $this->entityManager->flush();
            $totalUpdates += 2;
        }
        $io->success("Updated " . min(3, count($chapitres)) . " chapters");

        // 3. Challenges
        $io->section('💪 Challenges');
        $challenges = $this->entityManager->getRepository(Challenge::class)->findAll();
        foreach (array_slice($challenges, 0, 3) as $ch) {
            $desc = $ch->getDescription();
            $ch->setDescription($desc . ' ');
            $this->entityManager->flush();
            $ch->setDescription(trim($desc));
            $this->entityManager->flush();
            $totalUpdates += 2;
        }
        $io->success("Updated " . min(3, count($challenges)) . " challenges");

        // 4. Exercices
        $io->section('✏️ Exercises');
        $exercices = $this->entityManager->getRepository(Exercice::class)->findAll();
        foreach (array_slice($exercices, 0, 3) as $ex) {
            $question = $ex->getQuestion();
            $ex->setQuestion($question . ' ');
            $this->entityManager->flush();
            $ex->setQuestion(trim($question));
            $this->entityManager->flush();
            $totalUpdates += 2;
        }
        $io->success("Updated " . min(3, count($exercices)) . " exercises");

        // 5. Quizzes
        $io->section('❓ Quizzes');
        $quizzes = $this->entityManager->getRepository(Quiz::class)->findAll();
        foreach (array_slice($quizzes, 0, 3) as $q) {
            $titre = $q->getTitre();
            $q->setTitre($titre . ' ');
            $this->entityManager->flush();
            $q->setTitre(trim($titre));
            $this->entityManager->flush();
            $totalUpdates += 2;
        }
        $io->success("Updated " . min(3, count($quizzes)) . " quizzes");

        // 6. Events
        $io->section('📅 Events');
        $evenements = $this->entityManager->getRepository(Evenement::class)->findAll();
        foreach (array_slice($evenements, 0, 3) as $ev) {
            $desc = $ev->getDescription();
            $ev->setDescription($desc . ' ');
            $this->entityManager->flush();
            $ev->setDescription(trim($desc));
            $this->entityManager->flush();
            $totalUpdates += 2;
        }
        $io->success("Updated " . min(3, count($evenements)) . " events");

        // 7. Communities
        $io->section('👥 Communities');
        $communautes = $this->entityManager->getRepository(Communaute::class)->findAll();
        foreach (array_slice($communautes, 0, 3) as $com) {
            $desc = $com->getDescription();
            $com->setDescription($desc . ' ');
            $this->entityManager->flush();
            $com->setDescription(trim($desc));
            $this->entityManager->flush();
            $totalUpdates += 2;
        }
        $io->success("Updated " . min(3, count($communautes)) . " communities");

        // 8. Posts
        $io->section('📝 Posts');
        $posts = $this->entityManager->getRepository(Post::class)->findAll();
        foreach (array_slice($posts, 0, 3) as $p) {
            $contenu = $p->getContenu();
            $p->setContenu($contenu . ' ');
            $this->entityManager->flush();
            $p->setContenu(trim($contenu));
            $this->entityManager->flush();
            $totalUpdates += 2;
        }
        $io->success("Updated " . min(3, count($posts)) . " posts");

        $io->newLine(2);
        $io->success("✅ Total audit entries created: $totalUpdates");
        $io->note('All content types now have audit data. Check /backoffice/audit to see all tracked actions!');

        return Command::SUCCESS;
    }
}
