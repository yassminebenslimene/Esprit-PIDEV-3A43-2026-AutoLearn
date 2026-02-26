<?php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'test' => true],
    Symfony\UX\StimulusBundle\StimulusBundle::class => ['all' => true],
    Symfony\UX\Turbo\TurboBundle::class => ['all' => true],
    Twig\Extra\TwigExtraBundle\TwigExtraBundle::class => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
    SymfonyCasts\Bundle\ResetPassword\SymfonyCastsResetPasswordBundle::class => ['all' => true],
<<<<<<< HEAD
    CalendarBundle\CalendarBundle::class => ['all' => true],
    App\Bundle\UserActivityBundle\UserActivityBundle::class => ['all' => true],
    Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class => ['dev' => true, 'test' => true],
    SimpleThings\EntityAudit\SimpleThingsEntityAuditBundle::class => ['all' => true],
    Vich\UploaderBundle\VichUploaderBundle::class => ['all' => true],
    Knp\Bundle\PaginatorBundle\KnpPaginatorBundle::class => ['all' => true],
=======
    Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class => ['dev' => true, 'test' => true],
    CalendarBundle\CalendarBundle::class => ['all' => true],
    OpenAI\Symfony\OpenAIBundle::class => ['all' => true],
    Sbyaute\StarRatingBundle\StarRatingBundle::class => ['all' => true],
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
];
