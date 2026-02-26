# -*- coding: utf-8 -*-
"""
Script pour générer le Sprint Backlog Utilisateurs (Admin + Étudiant)
Basé sur le Product Backlog Utilisateurs (19 User Stories)
"""

# Données des User Stories avec leurs tâches
user_stories = [
    {
        'id': 'US-5.1',
        'titre': "En tant qu'Admin, je souhaite créer un événement",
        'priorite': 95,
        'taches': [
            ('T-5.1.1', "Créer l'entité Evenement avec tous les attributs", 'src/Entity/Evenement.php', 2, True),
            ('T-5.1.2', 'Créer les enums pour Type et Statut', 'src/Enum/TypeEvenement.php<br>src/Enum/StatutEvenement.php', 1, True),
            ('T-5.1.3', 'Générer et exécuter la migration', 'migrations/VersionXXX.php', 0.5, True),
            ('T-5.1.4', 'Créer le formulaire de création', 'src/Form/EvenementType.php', 1.5, True),
            ('T-5.1.5', 'Créer le contrôleur avec action new', 'src/Controller/EvenementController.php', 1, True),
            ('T-5.1.6', 'Créer le template de création', 'templates/backoffice/evenement/new.html.twig', 2, True),
            ('T-5.1.7', "Ajouter les validations dans l'entité", 'src/Entity/Evenement.php', 1, True),
            ('T-5.1.TEST', 'Tests pour US-5.1', 'Tests manuels et automatiques', 1, True),
        ]
    },
    {
        'id': 'US-5.2',
        'titre': "En tant qu'Admin, je souhaite modifier un événement",
        'priorite': 90,
        'taches': [
            ('T-5.2.1', "Créer l'action edit dans le contrôleur", 'src/Controller/EvenementController.php', 1, True),
            ('T-5.2.2', 'Créer le template de modification', 'templates/backoffice/evenement/edit.html.twig', 1.5, True),
            ('T-5.2.3', 'Ajouter les vérifications de sécurité', 'src/Controller/EvenementController.php', 0.5, True),
            ('T-5.2.TEST', 'Tests pour US-5.2', 'Tests manuels et automatiques', 1, True),
        ]
    },
