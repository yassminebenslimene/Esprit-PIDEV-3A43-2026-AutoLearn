 # 📚 CONCEPTS SYMFONY EXPLIQUÉS EN DÉTAIL

## 🎯 TABLE DES MATIÈRES

1. [Qu'est-ce que Symfony ?](#1-quest-ce-que-symfony)
2. [Architecture MVC](#2-architecture-mvc)
3. [Doctrine ORM](#3-doctrine-orm)
4. [QueryBuilder vs DQL](#4-querybuilder-vs-dql)
5. [Requêtes HTTP GET vs POST](#5-requêtes-http-get-vs-post)
6. [Formulaires Symfony](#6-formulaires-symfony)
7. [Bundles](#7-bundles)
8. [Services et Injection de Dépendances](#8-services-et-injection-de-dépendances)
9. [EventSubscribers](#9-eventsubscribers)
10. [Workflow Component](#10-workflow-component)

---

## 1️⃣ QU'EST-CE QUE SYMFONY ?

### Définition

**Symfony** est un **framework PHP** open-source créé en 2005 par Fabien Potencier.

Un **framework** est un ensemble d'outils et de composants réutilisables qui facilitent le développement d'applications web.

### Analogie

Imagine que tu construis une maison :
- **Sans framework** : Tu dois fabriquer toi-même les briques, le ciment, les fenêtres, etc.
- **Avec framework** : Tu as déjà des briques, des fenêtres, des portes prêtes à l'emploi. Tu n'as qu'à les assembler.

### Avantages de Symfony

1. **Structure claire** : Architecture MVC (Model-View-Controller)
2. **Composants réutilisables** : Mailer, Form, Security, Workflow, etc.
3. **ORM Doctrine** : Manipulation de la base de données sans écrire de SQL
4. **Communauté active** : Beaucoup de bundles et de documentation
5. **Performance** : Cache intégré, optimisations automatiques
6. **Sécurité** : Protection contre les failles courantes (XSS, CSRF, SQL Injection)

### Composants utilisés dans le module événement

| Composant | Rôle |
|-----------|------|
| **HttpFoundation** | Gestion des requêtes HTTP (GET, POST) |
| **Routing** | Mapping URL → Contrôleur |
| **Form** | Création et validation de formulaires |
| **Mailer** | Envoi d'emails |
| **Workflow** | Gestion des états et transitions |
| **Security** | Authentification et autorisation |
| **Validator** | Validation des données |
| **Twig** | Moteur de templates HTML |

---

## 2️⃣ ARCHITECTURE MVC

### Définition

**MVC** = Model-View-Controller

C'est un **pattern architectural** qui sépare l'application en 3 couches :

```
┌─────────────────────────────────────────┐
│              USER (Browser)             │
└────────────────┬────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────┐
│         CONTROLLER (Logique)            │
│  - Reçoit la requête HTTP               │
│  - Appelle le Model                     │
│  - Prépare les données pour la View     │
└────────┬────────────────────────┬───────┘
         │                        │
         ▼                        ▼
┌────────────────┐      ┌────────────────┐
│     MODEL      │      │      VIEW      │
│  (Entités)     │      │  (Templates)   │
│  - Evenement   │      │  - Twig HTML   │
│  - Participation│      │  - CSS/JS      │
│  - Equipe      │      │                │
└────────┬───────┘      └────────────────┘
         │
         ▼
┌────────────────────────┐
│   DATABASE (MySQL)     │
└────────────────────────┘
```

### Exemple concret : Afficher la liste des événements

#### 1. USER fait une requête
```
GET /backoffice/evenement
```

#### 2. ROUTING trouve le contrôleur
```php
#[Route('/backoffice/evenement', name: 'backoffice_evenements')]
public function index() { ... }
```

#### 3. CONTROLLER récupère les données
```php
public function index(EvenementRepository $repo): Response
{
    // Appel au MODEL
    $evenements = $repo->findAll();
    
    // Prépare les données pour la VIEW
    return $this->render('backoffice/evenement/index.html.twig', [
        'evenements' => $evenements
    ]);
}
```

#### 4. MODEL interroge la base de données
```php
// Doctrine ORM transforme ceci en SQL
$evenements = $repo->findAll();

// SQL généré automatiquement:
// SELECT * FROM evenement
```

#### 5. VIEW affiche les données
```twig
{% for evenement in evenements %}
    <tr>
        <td>{{ evenement.titre }}</td>
        <td>{{ evenement.lieu }}</td>
        <td>{{ evenement.dateDebut|date('d/m/Y') }}</td>
    </tr>
{% endfor %}
```

#### 6. RESPONSE est envoyée au navigateur
```html
<tr>
    <td>Hackathon 2026</td>
    <td>ESPRIT</td>
    <td>15/03/2026</td>
</tr>
```

---

## 3️⃣ DOCTRINE ORM

### Qu'est-ce qu'un ORM ?

**ORM** = Object-Relational Mapping

C'est un outil qui fait le **pont entre les objets PHP et les tables SQL**.

### Sans ORM (SQL brut)

```php
// Créer un événement
$sql = "INSERT INTO evenement (titre, lieu, date_debut) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute(['Hackathon', 'ESPRIT', '2026-03-15']);

// Récupérer un événement
$sql = "SELECT * FROM evenement WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([1]);
$row = $stmt->fetch();

// Accéder aux données
echo $row['titre'];  // Tableau associatif
```

**Problèmes** :
- Code verbeux
- Risque d'erreurs SQL
- Pas de typage
- Pas d'autocomplete dans l'IDE

### Avec ORM (Doctrine)

```php
// Créer un événement
$evenement = new Evenement();
$evenement->setTitre('Hackathon');
$evenement->setLieu('ESPRIT');
$evenement->setDateDebut(new \DateTime('2026-03-15'));
$entityManager->persist($evenement);
$entityManager->flush();

// Récupérer un événement
$evenement = $evenementRepository->find(1);

// Accéder aux données
echo $evenement->getTitre();  // Objet typé
```

**Avantages** :
- Code orienté objet
- Typage fort
- Autocomplete dans l'IDE
- Validation automatique
- Relations gérées automatiquement

### EntityManager : Le chef d'orchestre

L'**EntityManager** est l'outil principal de Doctrine.

**Opérations CRUD** :

#### CREATE - Créer
```php
$evenement = new Evenement();
$evenement->setTitre("Conférence AI");
$evenement->setLieu("ESPRIT");

$entityManager->persist($evenement);  // Préparer l'insertion (en mémoire)
$entityManager->flush();              // Exécuter l'insertion (en base de données)

// SQL généré:
// INSERT INTO evenement (titre, lieu) VALUES ('Conférence AI', 'ESPRIT')
```

#### READ - Lire
```php
// Récupérer tous les événements
$evenements = $evenementRepository->findAll();

// Récupérer un événement par ID
$evenement = $evenementRepository->find(1);

// Récupérer par critère
$evenements = $evenementRepository->findBy(['lieu' => 'ESPRIT']);

// Récupérer un seul résultat
$evenement = $evenementRepository->findOneBy(['titre' => 'Hackathon']);

// SQL généré:
// SELECT * FROM evenement WHERE lieu = 'ESPRIT'
```

#### UPDATE - Modifier
```php
$evenement = $evenementRepository->find(1);
$evenement->setTitre("Nouveau titre");

$entityManager->flush();  // Pas besoin de persist() pour une modification

// SQL généré:
// UPDATE evenement SET titre = 'Nouveau titre' WHERE id = 1
```

#### DELETE - Supprimer
```php
$evenement = $evenementRepository->find(1);

$entityManager->remove($evenement);
$entityManager->flush();

// SQL généré:
// DELETE FROM evenement WHERE id = 1
```

### Relations entre entités

Doctrine gère automatiquement les relations.

#### OneToMany : Un événement a plusieurs participations

```php
// Dans Evenement.php
#[ORM\OneToMany(mappedBy: "evenement", targetEntity: Participation::class)]
private Collection $participations;

// Utilisation
$evenement = $evenementRepository->find(1);
foreach ($evenement->getParticipations() as $participation) {
    echo $participation->getEquipe()->getNom();
}

// SQL généré automatiquement:
// SELECT * FROM participation WHERE evenement_id = 1
```

#### ManyToOne : Une participation appartient à un événement

```php
// Dans Participation.php
#[ORM\ManyToOne(targetEntity: Evenement::class, inversedBy: "participations")]
private Evenement $evenement;

// Utilisation
$participation = $participationRepository->find(1);
echo $participation->getEvenement()->getTitre();

// SQL généré automatiquement:
// SELECT * FROM evenement WHERE id = (SELECT evenement_id FROM participation WHERE id = 1)
```

#### ManyToMany : Une équipe a plusieurs étudiants

```php
// Dans Equipe.php
#[ORM\ManyToMany(targetEntity: Etudiant::class)]
#[ORM\JoinTable(name: "equipe_etudiant")]
private Collection $etudiants;

// Utilisation
$equipe = $equipeRepository->find(1);
foreach ($equipe->getEtudiants() as $etudiant) {
    echo $etudiant->getNom();
}

// SQL généré automatiquement:
// SELECT e.* FROM etudiant e
// INNER JOIN equipe_etudiant ee ON e.id = ee.etudiant_id
// WHERE ee.equipe_id = 1
```

---

## 4️⃣ QUERYBUILDER VS DQL

### QueryBuilder (Recommandé)

Le **QueryBuilder** est une API orientée objet pour construire des requêtes.

**Avantages** :
- Autocomplete dans l'IDE
- Typage fort
- Facile à débugger
- Réutilisable

**Exemple** :
```php
$qb = $entityManager->createQueryBuilder();
$qb->select('e')
   ->from(Evenement::class, 'e')
   ->where('e.status = :status')
   ->andWhere('e.dateDebut > :now')
   ->setParameter('status', StatutEvenement::PLANIFIE)
   ->setParameter('now', new \DateTime())
   ->orderBy('e.dateDebut', 'DESC')
   ->setMaxResults(10);

$evenements = $qb->getQuery()->getResult();

// SQL généré:
// SELECT * FROM evenement e
// WHERE e.status = 'Planifié'
// AND e.date_debut > '2026-02-25 10:00:00'
// ORDER BY e.date_debut DESC
// LIMIT 10
```

**Méthodes disponibles** :

| Méthode | Description | Exemple |
|---------|-------------|---------|
| `select()` | Colonnes à sélectionner | `select('e')` |
| `from()` | Table source | `from(Evenement::class, 'e')` |
| `where()` | Condition WHERE | `where('e.status = :status')` |
| `andWhere()` | Condition AND | `andWhere('e.lieu = :lieu')` |
| `orWhere()` | Condition OR | `orWhere('e.type = :type')` |
| `orderBy()` | Tri | `orderBy('e.dateDebut', 'DESC')` |
| `setParameter()` | Paramètre sécurisé | `setParameter('status', 'Planifié')` |
| `setMaxResults()` | LIMIT | `setMaxResults(10)` |
| `setFirstResult()` | OFFSET | `setFirstResult(20)` |
| `join()` | JOIN | `join('e.participations', 'p')` |
| `leftJoin()` | LEFT JOIN | `leftJoin('e.equipes', 'eq')` |

### DQL (Doctrine Query Language)

Le **DQL** ressemble à SQL mais utilise des noms de classes au lieu de tables.

**Exemple** :
```php
$dql = "SELECT e FROM App\Entity\Evenement e 
        WHERE e.status = :status 
        AND e.dateDebut > :now
        ORDER BY e.dateDebut DESC";

$query = $entityManager->createQuery($dql);
$query->setParameter('status', StatutEvenement::PLANIFIE);
$query->setParameter('now', new \DateTime());
$query->setMaxResults(10);

$evenements = $query->getResult();
```

**Différences avec SQL** :

| SQL | DQL |
|-----|-----|
| `SELECT * FROM evenement` | `SELECT e FROM App\Entity\Evenement e` |
| `WHERE evenement.status = 'Planifié'` | `WHERE e.status = :status` |
| `INNER JOIN participation ON ...` | `JOIN e.participations p` |

### Quand utiliser quoi ?

| Situation | Outil recommandé |
|-----------|------------------|
| Requête simple | `findBy()`, `findOneBy()` |
| Requête avec conditions | QueryBuilder |
| Requête complexe avec plusieurs JOIN | DQL |
| Requête dynamique (filtres optionnels) | QueryBuilder |
| Requête SQL native nécessaire | SQL brut avec `$entityManager->getConnection()` |

---

## 5️⃣ REQUÊTES HTTP GET VS POST

### Qu'est-ce qu'une requête HTTP ?

Une **requête HTTP** est un message envoyé par le navigateur au serveur.

**Structure** :
```
GET /backoffice/evenement HTTP/1.1
Host: localhost:8000
User-Agent: Mozilla/5.0
Accept: text/html
```

### GET - Récupérer des données

**Caractéristiques** :
- Paramètres dans l'URL
- Idempotent (peut être appelé plusieurs fois sans effet de bord)
- Peut être mis en cache
- Peut être bookmarké
- Limité en taille (environ 2000 caractères)

**Exemple** :
```
URL: /evenements?status=planifie&type=hackathon

Paramètres:
- status = planifie
- type = hackathon
```

**Dans le contrôleur** :
```php
#[Route('/evenements', methods: ['GET'])]
public function index(Request $request): Response
{
    // Récupérer les paramètres GET
    $status = $request->query->get('status');
    $type = $request->query->get('type');
    
    // Filtrer les événements
    $qb = $this->evenementRepository->createQueryBuilder('e');
    
    if ($status) {
        $qb->andWhere('e.status = :status')
           ->setParameter('status', $status);
    }
    
    if ($type) {
        $qb->andWhere('e.type = :type')
           ->setParameter('type', $type);
    }
    
    $evenements = $qb->getQuery()->getResult();
    
    return $this->render('evenement/index.html.twig', [
        'evenements' => $evenements
    ]);
}
```

**Utilisations** :
- Afficher une liste
- Rechercher
- Filtrer
- Paginer

### POST - Modifier des données

**Caractéristiques** :
- Paramètres dans le corps de la requête (non visibles dans l'URL)
- Non idempotent (chaque appel peut créer/modifier des données)
- Ne peut pas être mis en cache
- Ne peut pas être bookmarké
- Pas de limite de taille

**Exemple** :
```
URL: /evenement/new

Corps de la requête:
titre=Hackathon&lieu=ESPRIT&dateDebut=2026-03-15
```

**Dans le contrôleur** :
```php
#[Route('/evenement/new', methods: ['GET', 'POST'])]
public function new(Request $request): Response
{
    $evenement = new Evenement();
    $form = $this->createForm(EvenementType::class, $evenement);
    
    // Récupère les données POST et les lie au formulaire
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        // Les données POST sont maintenant dans l'objet $evenement
        $this->entityManager->persist($evenement);
        $this->entityManager->flush();
        
        return $this->redirectToRoute('evenements');
    }
    
    // GET: Afficher le formulaire vide
    return $this->render('evenement/new.html.twig', [
        'form' => $form
    ]);
}
```

**Utilisations** :
- Créer une ressource
- Modifier une ressource
- Supprimer une ressource
- Soumettre un formulaire

### Autres méthodes HTTP

| Méthode | Rôle | Idempotent |
|---------|------|------------|
| **GET** | Récupérer | ✅ Oui |
| **POST** | Créer | ❌ Non |
| **PUT** | Remplacer complètement | ✅ Oui |
| **PATCH** | Modifier partiellement | ❌ Non |
| **DELETE** | Supprimer | ✅ Oui |

---


## 6️⃣ FORMULAIRES SYMFONY

### Qu'est-ce qu'un formulaire Symfony ?

Un **formulaire Symfony** lie un objet PHP à un formulaire HTML.

**Avantages** :
- Génération automatique du HTML
- Validation automatique
- Protection CSRF intégrée
- Gestion des erreurs
- Transformation des données

### Création d'un formulaire

#### 1. Créer la classe de formulaire

**Fichier** : `src/Form/EvenementType.php`

```php
namespace App\Form;

use App\Entity\Evenement;
use App\Enum\TypeEvenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de l\'événement',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Hackathon 2026'
                ]
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 5]
            ])
            ->add('type', EnumType::class, [
                'class' => TypeEvenement::class,
                'label' => 'Type d\'événement',
                'attr' => ['class' => 'form-select']
            ])
            ->add('dateDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de début',
                'attr' => ['class' => 'form-control']
            ])
            ->add('dateFin', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
                'attr' => ['class' => 'form-control']
            ])
            ->add('nbMax', IntegerType::class, [
                'label' => 'Nombre maximum d\'équipes',
                'attr' => ['class' => 'form-control', 'min' => 1, 'max' => 100]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
```

#### 2. Utiliser le formulaire dans le contrôleur

```php
#[Route('/evenement/new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    // 1. Créer un nouvel objet Evenement
    $evenement = new Evenement();
    
    // 2. Créer le formulaire lié à l'objet
    $form = $this->createForm(EvenementType::class, $evenement);
    
    // 3. Récupérer les données POST et les lier au formulaire
    $form->handleRequest($request);
    
    // 4. Vérifier si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Les données du formulaire sont maintenant dans $evenement
        
        // 5. Sauvegarder en base de données
        $entityManager->persist($evenement);
        $entityManager->flush();
        
        // 6. Message flash et redirection
        $this->addFlash('success', 'Événement créé avec succès');
        return $this->redirectToRoute('backoffice_evenements');
    }
    
    // 7. Afficher le formulaire (GET ou formulaire invalide)
    return $this->render('backoffice/evenement/new.html.twig', [
        'form' => $form,
        'evenement' => $evenement
    ]);
}
```

#### 3. Afficher le formulaire dans le template

**Fichier** : `templates/backoffice/evenement/new.html.twig`

```twig
{{ form_start(form) }}
    <div class="row">
        <div class="col-md-6">
            {{ form_row(form.titre) }}
        </div>
        <div class="col-md-6">
            {{ form_row(form.lieu) }}
        </div>
    </div>
    
    {{ form_row(form.description) }}
    
    <div class="row">
        <div class="col-md-4">
            {{ form_row(form.type) }}
        </div>
        <div class="col-md-4">
            {{ form_row(form.dateDebut) }}
        </div>
        <div class="col-md-4">
            {{ form_row(form.dateFin) }}
        </div>
    </div>
    
    {{ form_row(form.nbMax) }}
    
    <button type="submit" class="btn btn-primary">Créer</button>
{{ form_end(form) }}
```

**HTML généré** :
```html
<form method="post">
    <div class="row">
        <div class="col-md-6">
            <label for="evenement_titre">Titre de l'événement</label>
            <input type="text" id="evenement_titre" name="evenement[titre]" 
                   class="form-control" placeholder="Ex: Hackathon 2026">
        </div>
        <div class="col-md-6">
            <label for="evenement_lieu">Lieu</label>
            <input type="text" id="evenement_lieu" name="evenement[lieu]" 
                   class="form-control">
        </div>
    </div>
    
    <!-- ... autres champs ... -->
    
    <input type="hidden" name="_token" value="abc123...">
    <button type="submit" class="btn btn-primary">Créer</button>
</form>
```

### Types de champs disponibles

| Type | Description | Exemple |
|------|-------------|---------|
| `TextType` | Champ texte simple | `<input type="text">` |
| `TextareaType` | Zone de texte multiligne | `<textarea>` |
| `EmailType` | Email | `<input type="email">` |
| `PasswordType` | Mot de passe | `<input type="password">` |
| `IntegerType` | Nombre entier | `<input type="number">` |
| `DateType` | Date | `<input type="date">` |
| `DateTimeType` | Date et heure | `<input type="datetime-local">` |
| `ChoiceType` | Liste déroulante | `<select>` |
| `EnumType` | Enum PHP 8.1+ | `<select>` avec valeurs enum |
| `EntityType` | Entité Doctrine | `<select>` avec données de la BDD |
| `FileType` | Upload de fichier | `<input type="file">` |
| `CheckboxType` | Case à cocher | `<input type="checkbox">` |
| `RadioType` | Bouton radio | `<input type="radio">` |

### Validation

La validation se fait automatiquement avec les annotations dans l'entité.

**Dans l'entité** :
```php
#[ORM\Column(length:255)]
#[Assert\NotBlank(message: "Le titre est obligatoire")]
#[Assert\Length(
    min: 3,
    max: 255,
    minMessage: "Le titre doit contenir au moins {{ limit }} caractères"
)]
private string $titre;
```

**Si le formulaire est invalide** :
```php
if ($form->isSubmitted() && $form->isValid()) {
    // Formulaire valide
} else {
    // Formulaire invalide
    // Les erreurs sont automatiquement affichées dans le template
}
```

**Affichage des erreurs dans le template** :
```twig
{{ form_row(form.titre) }}
{# Affiche automatiquement les erreurs sous le champ #}
```

**HTML généré avec erreur** :
```html
<div class="form-group">
    <label for="evenement_titre">Titre de l'événement</label>
    <input type="text" id="evenement_titre" name="evenement[titre]" 
           class="form-control is-invalid" value="">
    <div class="invalid-feedback">
        Le titre est obligatoire
    </div>
</div>
```

---

## 7️⃣ BUNDLES

### Qu'est-ce qu'un Bundle ?

Un **Bundle** est un plugin Symfony qui ajoute des fonctionnalités.

**Analogie** : C'est comme une extension Chrome ou un plugin WordPress.

### Bundles utilisés dans le module événement

#### 1. WorkflowBundle

**Rôle** : Gestion des états et transitions

**Installation** :
```bash
composer require symfony/workflow
```

**Configuration** : `config/packages/workflow.yaml`

**Utilisation** :
```php
// Vérifier si une transition est possible
if ($workflow->can($evenement, 'demarrer')) {
    // Appliquer la transition
    $workflow->apply($evenement, 'demarrer');
}
```

---

#### 2. CalendarBundle (tatarbj/calendar-bundle)

**Rôle** : Affichage calendrier des événements

**Installation** :
```bash
composer require tatarbj/calendar-bundle
```

**Configuration** : `config/packages/calendar.yaml`

**Utilisation** :
- Créer un `CalendarSubscriber` qui écoute `CalendarEvents::SET_DATA`
- Ajouter les événements au calendrier
- Afficher le calendrier dans un template avec FullCalendar.js

---

#### 3. MailerBundle

**Rôle** : Envoi d'emails

**Installation** :
```bash
composer require symfony/mailer
```

**Configuration** : `config/packages/mailer.yaml`

**Utilisation** :
```php
$email = (new Email())
    ->from('autolearn@example.com')
    ->to('student@example.com')
    ->subject('Participation confirmée')
    ->html('<h1>Félicitations!</h1>');

$mailer->send($email);
```

---

#### 4. TwigBundle

**Rôle** : Moteur de templates HTML

**Installation** : Inclus par défaut

**Configuration** : `config/packages/twig.yaml`

**Utilisation** :
```php
return $this->render('evenement/index.html.twig', [
    'evenements' => $evenements
]);
```

---

#### 5. DoctrineBundle

**Rôle** : ORM pour la base de données

**Installation** :
```bash
composer require symfony/orm-pack
```

**Configuration** : `config/packages/doctrine.yaml`

**Utilisation** :
```php
$evenement = new Evenement();
$entityManager->persist($evenement);
$entityManager->flush();
```

---

#### 6. SimpleThingsEntityAuditBundle

**Rôle** : Historique des modifications des entités

**Installation** :
```bash
composer require simplethings/entity-audit-bundle
```

**Configuration** : `config/packages/simple_things_entity_audit.yaml`

**Utilisation** : Automatique, enregistre toutes les modifications dans `user_audit` et `revisions`

---

### Comment installer un Bundle ?

1. **Installer via Composer**
   ```bash
   composer require vendor/bundle-name
   ```

2. **Symfony Flex configure automatiquement**
   - Crée le fichier de configuration dans `config/packages/`
   - Enregistre le bundle dans `config/bundles.php`

3. **Utiliser le bundle**
   - Lire la documentation
   - Configurer selon les besoins
   - Utiliser dans le code

---

## 8️⃣ SERVICES ET INJECTION DE DÉPENDANCES

### Qu'est-ce qu'un Service ?

Un **Service** est une classe réutilisable qui effectue une tâche spécifique.

**Exemples** :
- `EmailService` : Envoie des emails
- `CertificateService` : Génère des certificats PDF
- `FeedbackAnalyticsService` : Analyse les feedbacks

### Pourquoi utiliser des Services ?

**Sans Service** :
```php
// Dans le contrôleur
public function new(Request $request): Response
{
    // ... code de création d'événement ...
    
    // Envoi d'email directement dans le contrôleur
    $email = (new Email())
        ->from('autolearn@example.com')
        ->to($student->getEmail())
        ->subject('Participation confirmée')
        ->html('<h1>Félicitations!</h1>');
    $mailer->send($email);
    
    // Problème: Code dupliqué si on envoie des emails ailleurs
}
```

**Avec Service** :
```php
// Dans EmailService.php
class EmailService
{
    public function sendParticipationConfirmation(string $email, ...): void
    {
        $email = (new Email())
            ->from('autolearn@example.com')
            ->to($email)
            ->subject('Participation confirmée')
            ->html($this->twig->render('emails/participation.html.twig'));
        $this->mailer->send($email);
    }
}

// Dans le contrôleur
public function new(Request $request, EmailService $emailService): Response
{
    // ... code de création d'événement ...
    
    // Envoi d'email via le service
    $emailService->sendParticipationConfirmation($student->getEmail(), ...);
    
    // Avantage: Code réutilisable, testable, maintenable
}
```

### Injection de Dépendances

L'**Injection de Dépendances** (DI) est un pattern où Symfony fournit automatiquement les dépendances.

**Exemple** :
```php
class EmailService
{
    // Symfony injecte automatiquement ces dépendances
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        private CertificateService $certificateService
    ) {}
    
    public function sendCertificate(...): void
    {
        // Utiliser les dépendances injectées
        $pdf = $this->certificateService->generateCertificate(...);
        $html = $this->twig->render('emails/certificate.html.twig');
        
        $email = (new Email())
            ->html($html)
            ->addPart(new DataPart($pdf, 'certificate.pdf'));
        
        $this->mailer->send($email);
    }
}
```

**Comment ça marche ?**

1. Symfony scanne toutes les classes dans `src/`
2. Il enregistre automatiquement les services
3. Quand tu demandes un service, Symfony le crée et injecte ses dépendances

**Dans le contrôleur** :
```php
public function new(
    Request $request,
    EmailService $emailService,  // ← Symfony injecte automatiquement
    EntityManagerInterface $entityManager  // ← Symfony injecte automatiquement
): Response {
    // Utiliser les services injectés
    $emailService->sendEmail(...);
    $entityManager->flush();
}
```

---

## 9️⃣ EVENTSUBSCRIBERS

### Qu'est-ce qu'un EventSubscriber ?

Un **EventSubscriber** est une classe qui écoute des événements et exécute du code automatiquement.

**Analogie** : C'est comme un détecteur de mouvement qui allume la lumière automatiquement.

### Événements disponibles

Symfony déclenche des événements à différents moments :

| Événement | Quand | Exemple d'utilisation |
|-----------|-------|----------------------|
| `kernel.request` | Avant chaque requête | Vérifier l'authentification |
| `kernel.response` | Avant chaque réponse | Ajouter des headers |
| `kernel.exception` | Quand une exception est levée | Logger les erreurs |
| `workflow.transition` | Quand une transition workflow | Envoyer des emails |
| `doctrine.postPersist` | Après insertion en BDD | Envoyer une notification |
| `doctrine.postUpdate` | Après modification en BDD | Logger les changements |

### Exemple : EvenementWorkflowSubscriber

**Fichier** : `src/EventSubscriber/EvenementWorkflowSubscriber.php`

```php
class EvenementWorkflowSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private EmailService $emailService
    ) {}
    
    // Déclarer les événements écoutés
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.evenement_publishing.entered.en_cours' => 'onEnCours',
            'workflow.evenement_publishing.entered.termine' => 'onTermine',
            'workflow.evenement_publishing.entered.annule' => 'onAnnule',
        ];
    }
    
    // Méthode appelée automatiquement quand l'événement démarre
    public function onEnCours(Event $event): void
    {
        $evenement = $event->getSubject();
        
        $this->logger->info('🚀 Événement démarré', [
            'evenement_id' => $evenement->getId(),
        ]);
        
        // Envoyer automatiquement des emails à tous les participants
        $this->sendEmailsToParticipants($evenement, 'started');
    }
    
    // Méthode appelée automatiquement quand l'événement se termine
    public function onTermine(Event $event): void
    {
        $evenement = $event->getSubject();
        
        $this->logger->info('✅ Événement terminé', [
            'evenement_id' => $evenement->getId(),
        ]);
        
        // Envoyer automatiquement les certificats
        $this->sendCertificatesToParticipants($evenement);
    }
}
```

**Comment ça marche ?**

1. **Symfony enregistre automatiquement** le subscriber
2. **Quand un événement est déclenché**, Symfony appelle la méthode correspondante
3. **Le code s'exécute automatiquement** sans intervention manuelle

**Exemple de flux** :
```
1. Admin clique sur "Démarrer l'événement"
   ↓
2. Contrôleur: $workflow->apply($evenement, 'demarrer')
   ↓
3. Workflow change l'état: planifie → en_cours
   ↓
4. Workflow déclenche l'événement: workflow.evenement_publishing.entered.en_cours
   ↓
5. Symfony appelle automatiquement: EvenementWorkflowSubscriber::onEnCours()
   ↓
6. Méthode envoie automatiquement les emails à tous les participants
```

---

## 🔟 WORKFLOW COMPONENT

### Qu'est-ce qu'un Workflow ?

Un **Workflow** est une machine à états qui gère les transitions entre différents états.

**Analogie** : C'est comme un feu tricolore :
- États : Vert, Orange, Rouge
- Transitions : Vert → Orange → Rouge → Vert

### Exemple : Workflow d'un événement

**États possibles** :
- Planifié
- En cours
- Terminé
- Annulé

**Transitions possibles** :
```
Planifié ──demarrer──> En cours ──terminer──> Terminé
    │                      │
    └──────annuler─────────┴──────────────────> Annulé
```

### Configuration

**Fichier** : `config/packages/workflow.yaml`

```yaml
framework:
    workflows:
        evenement_publishing:
            type: 'state_machine'           # Un seul état à la fois
            audit_trail:
                enabled: true               # Historique des transitions
            marking_store:
                type: 'method'
                property: 'workflowStatus'  # Propriété qui stocke l'état
            supports:
                - App\Entity\Evenement      # Entité concernée
            initial_marking: planifie       # État initial
            places:                         # États possibles
                - planifie
                - en_cours
                - termine
                - annule
            transitions:                    # Transitions possibles
                demarrer:
                    from: planifie
                    to: en_cours
                terminer:
                    from: en_cours
                    to: termine
                annuler:
                    from: [planifie, en_cours]
                    to: annule
```

### Utilisation dans le code

#### Vérifier si une transition est possible

```php
if ($workflow->can($evenement, 'demarrer')) {
    echo "On peut démarrer l'événement";
} else {
    echo "Impossible de démarrer l'événement";
}
```

#### Appliquer une transition

```php
try {
    $workflow->apply($evenement, 'demarrer');
    echo "Événement démarré avec succès";
} catch (TransitionException $e) {
    echo "Erreur: " . $e->getMessage();
}
```

#### Obtenir les transitions disponibles

```php
$transitions = $workflow->getEnabledTransitions($evenement);

foreach ($transitions as $transition) {
    echo $transition->getName();  // "demarrer", "annuler", etc.
}
```

#### Obtenir l'état actuel

```php
$marking = $workflow->getMarking($evenement);
$places = $marking->getPlaces();  // ['en_cours' => 1]
```

### Guards : Conditions pour bloquer une transition

Tu peux ajouter des conditions pour autoriser ou bloquer une transition.

**Dans l'EventSubscriber** :
```php
public static function getSubscribedEvents(): array
{
    return [
        'workflow.evenement_publishing.guard' => 'onGuard',
    ];
}

public function onGuard(GuardEvent $event): void
{
    $evenement = $event->getSubject();
    $transition = $event->getTransition()->getName();
    
    // Empêcher de démarrer si la date n'est pas encore arrivée
    if ($transition === 'demarrer') {
        $now = new \DateTime();
        if ($evenement->getDateDebut() > $now) {
            $event->setBlocked(true, 'La date de début n\'est pas encore arrivée');
        }
    }
}
```

---

## 📊 RÉSUMÉ DES CONCEPTS

| Concept | Définition | Exemple |
|---------|------------|---------|
| **Symfony** | Framework PHP | Structure MVC, composants réutilisables |
| **MVC** | Model-View-Controller | Séparation en 3 couches |
| **Doctrine ORM** | Mapping objet-relationnel | `$evenement->getTitre()` au lieu de `$row['titre']` |
| **QueryBuilder** | API pour construire des requêtes | `$qb->where('e.status = :status')` |
| **DQL** | Doctrine Query Language | `SELECT e FROM Evenement e WHERE ...` |
| **GET** | Récupérer des données | `/evenements?status=planifie` |
| **POST** | Modifier des données | Soumettre un formulaire |
| **Formulaire** | Lie un objet PHP à un formulaire HTML | `$form = $this->createForm(EvenementType::class)` |
| **Bundle** | Plugin Symfony | WorkflowBundle, MailerBundle, etc. |
| **Service** | Classe réutilisable | EmailService, CertificateService |
| **Injection de Dépendances** | Symfony fournit automatiquement les dépendances | `__construct(private MailerInterface $mailer)` |
| **EventSubscriber** | Écoute des événements | Envoyer des emails automatiquement |
| **Workflow** | Machine à états | Planifié → En cours → Terminé |

---

FIN DU GUIDE DES CONCEPTS SYMFONY
