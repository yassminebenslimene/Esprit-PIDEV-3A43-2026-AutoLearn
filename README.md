# AutoLearn - Plateforme d'Apprentissage

## C'est quoi AutoLearn ?
AutoLearn est une plateforme web pour les étudiants qui veulent apprendre en ligne. On peut suivre des cours, faire des quiz, participer à des événements comme des hackathons, et rejoindre des communautés d'étudiants.

## Les fonctionnalités

### Ce que tu peux faire en tant qu'étudiant :
- Suivre des cours avec des chapitres bien organisés
- Faire des quiz pour tester tes connaissances (avec correction automatique par IA)
- Relever des challenges de programmation
- Participer à des événements (hackathons, workshops, conférences)
- Créer ou rejoindre une équipe pour les événements
- Rejoindre des communautés et partager des posts
- Voir ton profil et suivre ta progression
- Recevoir des notifications pour les nouvelles activités

### Ce que les admins peuvent faire :
- Gérer les comptes étudiants
- Créer et organiser les cours
- Organiser des événements
- Voir les statistiques de la plateforme
- Consulter l'historique des modifications (audit)

## Comment ça marche ?

### Installation
1. Installer XAMPP (pour avoir PHP et MySQL)
2. Cloner le projet dans `C:\xampp\htdocs\`
3. Ouvrir un terminal dans le dossier du projet
4. Installer les dépendances : `composer install`
5. Créer la base de données : `php bin/console doctrine:database:create`
6. Créer les tables : `php bin/console doctrine:migrations:migrate`
7. Charger des données de test : `php bin/console doctrine:fixtures:load`

### Démarrer le serveur
```bash
php -S 127.0.0.1:8000 -t public
```

Puis ouvrir dans le navigateur : http://127.0.0.1:8000/

### Se connecter
Après avoir chargé les fixtures, tu peux te connecter avec :
- **Admin** : admin@autolearn.com / password
- **Étudiant** : etudiant@autolearn.com / password

## Technologies utilisées
- **PHP** : Symfony 7.2
- **Base de données** : MariaDB
- **Frontend** : Bootstrap + JavaScript
- **IA** : Groq (pour générer du contenu et corriger les quiz)
- **Emails** : Brevo (pour envoyer les confirmations)

## Configuration importante

Dans le fichier `.env`, il faut configurer :

```env
# Base de données
DATABASE_URL="mysql://root:@127.0.0.1:3306/autolearn_db"

# Email (Brevo)
BREVO_API_KEY=ta_clé_api
MAIL_FROM_EMAIL=autolearn66@gmail.com

# IA (Groq)
GROQ_API_KEY=ta_clé_api
GROQ_MODEL=llama-3.3-70b-versatile
```

## Structure du projet
```
autolearn/
├── src/                 # Code PHP (Controllers, Services, Entities)
├── templates/           # Pages HTML (Twig)
├── public/              # CSS, JS, images
├── migrations/          # Migrations base de données
└── config/              # Configuration Symfony
```

## Besoin d'aide ?
Contacte-nous : autolearn66@gmail.com

