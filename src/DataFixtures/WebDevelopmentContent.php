<?php

namespace App\DataFixtures;

trait WebDevelopmentContent
{
    private function getChapter1Content(): string
    {
        return "Introduction au développement web moderne. HTML5, CSS3 et JavaScript sont les fondations du web.";
    }

    private function getChapter2Content(): string
    {
        return "HTML5 avancé: sémantique, formulaires, API multimédia et stockage local.";
    }

    private function getChapter3Content(): string
    {
        return "CSS3 moderne: Flexbox, Grid, animations et responsive design.";
    }

    private function getChapter4Content(): string
    {
        return "JavaScript ES6+: let/const, arrow functions, promises, async/await.";
    }

    private function getChapter5Content(): string
    {
        return "DOM et événements: manipulation du DOM, gestion des événements, AJAX.";
    }

    private function getChapter6Content(): string
    {
        return "Frameworks frontend: React, Vue.js, Angular - concepts et comparaison.";
    }

    private function getChapter7Content(): string
    {
        return "Backend avec Node.js: Express, API REST, authentification.";
    }

    private function getChapter8Content(): string
    {
        return "Bases de données: SQL vs NoSQL, MongoDB, MySQL, ORM.";
    }

    private function getChapter9Content(): string
    {
        return "DevOps et déploiement: Git, CI/CD, Docker, hébergement cloud.";
    }

    private function getChapter10Content(): string
    {
        return "Projet final: application web complète avec frontend et backend.";
    }
}
