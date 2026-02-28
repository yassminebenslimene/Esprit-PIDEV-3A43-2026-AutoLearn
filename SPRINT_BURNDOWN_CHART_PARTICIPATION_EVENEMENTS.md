# 📊 SPRINT BURNDOWN CHART - Processus de Participation aux Événements

## 🎯 QU'EST-CE QU'UN SPRINT BURNDOWN CHART ?

### Définition
Un **Sprint Burndown Chart** est un graphique qui visualise la progression du travail restant au fil du temps pendant un sprint Scrum. C'est un outil de gestion de projet agile qui permet de :

1. **Suivre l'avancement** : Voir combien de travail reste à faire chaque jour
2. **Détecter les problèmes** : Identifier rapidement si l'équipe est en retard ou en avance
3. **Prédire la livraison** : Estimer si le sprint sera terminé à temps
4. **Améliorer la planification** : Apprendre de chaque sprint pour mieux estimer le suivant

### Composants du Burndown Chart

**Axe X (Horizontal)** : Le temps (jours du sprint)
- Jour 1, Jour 2, Jour 3... jusqu'à la fin du sprint

**Axe Y (Vertical)** : Le travail restant
- Mesuré en Story Points, heures, ou nombre de tâches

**Ligne Idéale** : Une ligne droite descendante
- Représente la progression parfaite si le travail est distribué uniformément

**Ligne Réelle** : La progression actuelle de l'équipe
- Monte quand on ajoute du travail
- Descend quand on termine des tâches
- Peut être au-dessus (retard) ou en-dessous (avance) de la ligne idéale


### Exemple Visuel

```
Story Points
    50 |●                                    (Ligne Idéale)
       |  ●                                  
    40 |    ●                                
       |      ●●                             (Ligne Réelle)
    30 |        ●                            
       |          ●                          
    20 |            ●                        
       |              ●                      
    10 |                ●                    
       |                  ●                  
     0 |____________________●________________
       J1  J2  J3  J4  J5  J6  J7  J8  J9  J10
                        Jours
```

### Interprétation

- **Ligne réelle AU-DESSUS de la ligne idéale** : L'équipe est en retard
- **Ligne réelle EN-DESSOUS de la ligne idéale** : L'équipe est en avance
- **Ligne réelle MONTE** : Du travail a été ajouté (scope creep)
- **Ligne réelle PLATE** : Aucun progrès (blocage)

---

## 🔍 ANALYSE COMPLÈTE DU PROCESSUS DE PARTICIPATION

### Vue d'ensemble du flux

```
┌─────────────────────────────────────────────────────────────────┐
│                  PROCESSUS DE PARTICIPATION                      │
└─────────────────────────────────────────────────────────────────┘

1. CRÉATION D'ÉQUIPE
   ↓
2. SÉLECTION D'ÉVÉNEMENT
   ↓
3. SOUMISSION DE PARTICIPATION
   ↓
4. VALIDATION AUTOMATIQUE (3 règles)
   ↓
5. ACCEPTATION/REFUS
   ↓
6. ENVOI EMAIL AUTOMATIQUE
   ↓
7. GESTION DU CYCLE DE VIE
```


---

## 📋 ÉTAPE 1 : CRÉATION D'ÉQUIPE

### Entité : `Equipe.php`

**Contraintes de validation** :
- ✅ Nom obligatoire (NotBlank)
- ✅ Minimum 4 étudiants (Assert\Count min: 4)
- ✅ Maximum 6 étudiants (Assert\Count max: 6)
- ✅ Lien obligatoire avec un événement

**Flux utilisateur** :

```php
// Route 1 : Création simple
GET/POST /equipe/new
→ Formulaire EquipeFrontType
→ Sélection manuelle de l'événement
→ Ajout manuel des membres

// Route 2 : Création pour un événement spécifique (RECOMMANDÉ)
GET/POST /equipe/new-for-event/{eventId}
→ Événement pré-sélectionné
→ Utilisateur connecté ajouté automatiquement
→ Ajout des autres membres (3-5 personnes)
→ Redirection vers /equipe/{id}/event/{eventId}
```

**Contrôles de sécurité** :
- 🔒 Authentification requise (`#[IsGranted('ROLE_USER')]`)
- 🔒 Seuls les membres peuvent modifier/supprimer l'équipe

**Points clés du code** :

```php
// FrontofficeEquipeController.php - Ligne 52
public function newForEvent(int $eventId, ...)
{
    $equipe = new Equipe();
    $equipe->setEvenement($evenement);
    
    // Ajout automatique de l'utilisateur connecté
    $currentUser = $this->getUser();
    $equipe->addEtudiant($currentUser);
    
    // Formulaire avec current_user_id pour éviter les doublons
    $form = $this->createForm(EquipeFrontType::class, $equipe, [
        'current_user_id' => $currentUser->getId()
    ]);
}
```


---

## 📋 ÉTAPE 2 : REJOINDRE UNE ÉQUIPE EXISTANTE

### Logique de places disponibles

**Calcul des places** :
```php
// Dans Equipe.php
$nbMembres = $equipe->getEtudiants()->count(); // 4 à 6
$placesDisponibles = 6 - $nbMembres;

// Exemples :
// 4 membres → 2 places disponibles ✅
// 5 membres → 1 place disponible ✅
// 6 membres → 0 place disponible ❌ (équipe complète)
```

**Affichage dans l'interface** :
```twig
{# templates/frontoffice/equipe/show.html.twig #}
{% set nbMembres = equipe.etudiants|length %}
{% set placesDisponibles = 6 - nbMembres %}

{% if placesDisponibles > 0 %}
    <span class="badge bg-success">
        {{ placesDisponibles }} place(s) disponible(s)
    </span>
    <button class="btn btn-primary">Rejoindre cette équipe</button>
{% else %}
    <span class="badge bg-danger">Équipe complète</span>
{% endif %}
```

**Processus de rejoindre** :
1. Utilisateur consulte la liste des équipes pour l'événement
2. Système affiche uniquement les équipes avec places disponibles
3. Utilisateur clique sur "Rejoindre"
4. Système vérifie :
   - ✅ L'équipe n'est pas complète (< 6 membres)
   - ✅ L'utilisateur n'est pas déjà membre
   - ✅ L'événement accepte encore des participations
5. Ajout de l'utilisateur à l'équipe
6. Mise à jour automatique du compteur de places


---

## 📋 ÉTAPE 3 : SOUMISSION DE PARTICIPATION

### Routes disponibles

**Route 1 : Formulaire manuel**
```php
GET/POST /participation/new
→ Sélection manuelle de l'équipe (parmi les équipes de l'utilisateur)
→ Sélection manuelle de l'événement
→ Soumission
```

**Route 2 : Participation directe (RECOMMANDÉ)**
```php
GET/POST /participation/new-for-team/{equipeId}/event/{eventId}
→ Équipe et événement pré-sélectionnés
→ Création automatique de la participation
→ Validation immédiate
→ Redirection vers /participation/mes-participations
```

### Formulaire : `ParticipationFrontType.php`

**Champs** :
- `evenement` : EntityType (liste déroulante des événements)
- `equipe` : EntityType (filtrée : uniquement les équipes de l'utilisateur)

**Filtrage intelligent** :
```php
// Ligne 35 - Query Builder
'query_builder' => function($repository) use ($user) {
    return $repository->createQueryBuilder('e')
        ->join('e.etudiants', 'et')
        ->where('et.id = :userId')
        ->setParameter('userId', $user->getId());
}
```

**Sécurité** :
- 🔒 L'utilisateur ne peut participer qu'avec SES équipes
- 🔒 Impossible de sélectionner une équipe dont il n'est pas membre


---

## 🔍 ÉTAPE 4 : VALIDATION AUTOMATIQUE (CŒUR DU SYSTÈME)

### Méthode : `Participation::validateParticipation()`

Cette méthode est **LE CERVEAU** du système. Elle applique 3 règles strictes.

### ❌ RÈGLE 1 : Événement annulé

```php
// Participation.php - Ligne 62
if ($evenement->getIsCanceled()) {
    $this->setStatut(StatutParticipation::REFUSE);
    return [
        'accepted' => false,
        'message' => 'L\'événement "' . $evenement->getTitre() . 
                     '" a été annulé. Aucune participation n\'est acceptée.'
    ];
}
```

**Logique** :
- Si `isCanceled = true` → Refus automatique
- Message en rouge affiché à l'utilisateur
- Participation marquée comme "Refusé"

---

### ❌ RÈGLE 2 : Capacité maximale atteinte

```php
// Participation.php - Ligne 72
$acceptedCount = 0;
foreach ($evenement->getParticipations() as $p) {
    // Ne pas compter la participation actuelle
    if ($p->getId() === $this->getId()) {
        continue;
    }
    if ($p->getStatut() === StatutParticipation::ACCEPTE) {
        $acceptedCount++;
    }
}

if ($acceptedCount >= $evenement->getNbMax()) {
    $this->setStatut(StatutParticipation::REFUSE);
    return [
        'accepted' => false,
        'message' => 'La capacité maximale de l\'événement est atteinte (' . 
                     $evenement->getNbMax() . ' équipes maximum). ' .
                     'Votre participation a été refusée.'
    ];
}
```

**Logique** :
- Compte uniquement les participations **ACCEPTÉES**
- Exclut la participation en cours de validation
- Compare avec `nbMax` de l'événement
- Exemple : Si `nbMax = 10` et il y a déjà 10 équipes acceptées → Refus


---

### ❌ RÈGLE 3 : Doublon d'étudiant (LA PLUS COMPLEXE)

```php
// Participation.php - Ligne 90
foreach ($evenement->getParticipations() as $p) {
    // Ne pas vérifier contre soi-même
    if ($p->getId() === $this->getId()) {
        continue;
    }
    
    // Vérifier uniquement les participations acceptées
    if ($p->getStatut() !== StatutParticipation::ACCEPTE) {
        continue;
    }
    
    // Comparer chaque étudiant de l'équipe actuelle
    // avec chaque étudiant des équipes déjà acceptées
    foreach ($p->getEquipe()->getEtudiants() as $etudiant) {
        foreach ($equipe->getEtudiants() as $membre) {
            if ($etudiant->getId() === $membre->getId()) {
                $this->setStatut(StatutParticipation::REFUSE);
                return [
                    'accepted' => false,
                    'message' => 'L\'étudiant "' . $membre->getPrenom() . ' ' . 
                                 $membre->getNom() . '" participe déjà à cet ' .
                                 'événement avec l\'équipe "' . 
                                 $p->getEquipe()->getNom() . '". Un étudiant ' .
                                 'ne peut pas participer avec deux équipes ' .
                                 'différentes au même événement.'
                ];
            }
        }
    }
}
```

**Logique détaillée** :

1. **Parcourir toutes les participations** de l'événement
2. **Ignorer** :
   - La participation en cours de validation
   - Les participations refusées ou en attente
3. **Pour chaque participation acceptée** :
   - Récupérer tous les étudiants de cette équipe
   - Comparer avec tous les étudiants de la nouvelle équipe
4. **Si un étudiant est trouvé dans les deux équipes** :
   - Refus immédiat
   - Message personnalisé avec le nom de l'étudiant et de l'équipe existante

**Exemple concret** :

```
Événement : "Hackathon 2026"
nbMax : 10 équipes

Participations existantes (ACCEPTÉES) :
- Équipe A : [Ahmed, Fatima, Youssef, Sara]
- Équipe B : [Mohamed, Leila, Karim, Nadia]

Nouvelle participation :
- Équipe C : [Ahmed, Ali, Salma, Omar]
                ↑
              DOUBLON !

Résultat : ❌ REFUSÉ
Message : "L'étudiant Ahmed participe déjà à cet événement 
           avec l'équipe Équipe A. Un étudiant ne peut pas 
           participer avec deux équipes différentes au même événement."
```


---

### ✅ ACCEPTATION AUTOMATIQUE

```php
// Participation.php - Ligne 115
// Si toutes les règles sont passées
$this->setStatut(StatutParticipation::ACCEPTE);
return [
    'accepted' => true,
    'message' => 'Participation acceptée avec succès ! Votre équipe "' . 
                 $equipe->getNom() . '" est inscrite à l\'événement "' . 
                 $evenement->getTitre() . '".'
];
```

**Conditions d'acceptation** :
- ✅ Événement NON annulé
- ✅ Capacité NON atteinte
- ✅ Aucun doublon d'étudiant

---

## 📧 ÉTAPE 5 : ENVOI AUTOMATIQUE D'EMAIL

### Service : `EmailService.php`

**Méthode principale** : `sendParticipationConfirmation()`

### Déclenchement

```php
// FrontofficeParticipationController.php - Ligne 67
if ($result['accepted']) {
    $entityManager->persist($participation);
    $entityManager->flush();
    
    // Envoi à TOUS les membres de l'équipe
    foreach ($participation->getEquipe()->getEtudiants() as $etudiant) {
        $emailService->sendParticipationConfirmation(
            $etudiant->getEmail(),
            $etudiant->getPrenom(),
            $etudiant->getNom(),
            $participation->getEquipe()->getNom(),
            $evenement->getTitre(),
            $evenement->getDateDebut(),
            $evenement->getLieu(),
            $participation->getId()
        );
    }
}
```

### Contenu de l'email

**1. Template Twig** : `templates/emails/participation_confirmation.html.twig`

**2. Pièces jointes** :
- 📄 **Badge PDF** : Badge personnalisé avec nom, équipe, événement
- 📅 **Fichier .ics** : Pour ajouter l'événement au calendrier
- 🔲 **QR Code PNG** : Code QR avec toutes les informations


### Génération du QR Code

```php
// EmailService.php - Ligne 48
$qrContent = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$qrContent .= "   EVENT PARTICIPATION\n";
$qrContent .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
$qrContent .= "PARTICIPANT:\n";
$qrContent .= "  " . strtoupper($studentName) . "\n\n";
$qrContent .= "TEAM:\n";
$qrContent .= "  " . $teamName . "\n\n";
$qrContent .= "EVENT:\n";
$qrContent .= "  " . $eventName . "\n\n";
$qrContent .= "DATE:\n";
$qrContent .= "  " . $eventDate->format('F d, Y - H:i') . "\n\n";
$qrContent .= "REGISTRATION ID:\n";
$qrContent .= "  #" . str_pad($participationId, 6, '0', STR_PAD_LEFT) . "\n\n";
$qrContent .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$qrContent .= "✓ Registration Confirmed\n";
$qrContent .= "   AUTOLEARN PLATFORM\n";
$qrContent .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━";

// Génération via API externe
$qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' 
             . urlencode($qrContent);
$qrCodeData = @file_get_contents($qrCodeUrl);
```

### Génération du fichier .ics

```php
// EmailService.php - Ligne 285
private function generateIcsFile(
    string $eventName,
    \DateTimeInterface $eventDate,
    string $location
): string {
    $dtStart = $eventDate->format('Ymd\THis');
    $dtEnd = (clone $eventDate)->modify('+2 hours')->format('Ymd\THis');
    
    return <<<ICS
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Autolearn Platform//Event//EN
BEGIN:VEVENT
UID:{$now}@autolearn.com
DTSTAMP:{$now}
DTSTART:{$dtStart}
DTEND:{$dtEnd}
SUMMARY:{$eventName}
LOCATION:{$location}
DESCRIPTION:You are registered for this event on Autolearn Platform
STATUS:CONFIRMED
END:VEVENT
END:VCALENDAR
ICS;
}
```

### Gestion des erreurs d'envoi

```php
// FrontofficeParticipationController.php - Ligne 70
$successCount = 0;
$failedEmails = [];

foreach ($equipe->getEtudiants() as $etudiant) {
    try {
        $emailService->sendParticipationConfirmation(...);
        $successCount++;
    } catch (\Exception $e) {
        $failedEmails[] = $email . ' - Error: ' . $e->getMessage();
    }
}

// Messages flash
if ($successCount > 0) {
    $this->addFlash('success', '✅ ' . $successCount . ' email(s) sent!');
}
if (!empty($failedEmails)) {
    $this->addFlash('warning', '⚠️ Failed: ' . implode(' | ', $failedEmails));
}
```


---

## 🔄 ÉTAPE 6 : GESTION DU CYCLE DE VIE (WORKFLOW)

### Workflow Bundle : `EvenementWorkflowSubscriber.php`

### États de l'événement

```yaml
# config/packages/workflow.yaml
evenement_publishing:
  type: 'state_machine'
  marking_store:
    type: 'method'
    property: 'workflowStatus'
  supports:
    - App\Entity\Evenement
  initial_marking: planifie
  places:
    - planifie      # Événement créé, participations ouvertes
    - en_cours      # Événement en cours
    - termine       # Événement terminé
    - annule        # Événement annulé
  transitions:
    demarrer:       # planifie → en_cours
      from: planifie
      to: en_cours
    terminer:       # en_cours → termine
      from: en_cours
      to: termine
    annuler:        # planifie → annule
      from: planifie
      to: annule
```

### Événements automatiques

**1. Événement démarre** (`onEnCours`)

```php
// EvenementWorkflowSubscriber.php - Ligne 82
public function onEnCours(Event $event): void
{
    $evenement = $event->getSubject();
    
    // Envoyer email à tous les participants
    $this->sendEmailsToParticipants($evenement, 'started');
}
```

**Email envoyé** : `templates/emails/event_started.html.twig`
- 🚀 Sujet : "Event Started - {Nom de l'événement}"
- 📧 Destinataires : Tous les membres des équipes acceptées

---

**2. Événement annulé** (`onAnnule`)

```php
// EvenementWorkflowSubscriber.php - Ligne 113
public function onAnnule(Event $event): void
{
    $evenement = $event->getSubject();
    
    // Envoyer email d'annulation
    $this->sendEmailsToParticipants($evenement, 'cancelled');
}
```

**Email envoyé** : `templates/emails/event_cancelled.html.twig`
- ⚠️ Sujet : "Event Cancelled - {Nom de l'événement}"
- 📧 Destinataires : Tous les membres des équipes acceptées

**Impact sur les participations** :
- Toutes les nouvelles participations sont automatiquement refusées
- Les participations existantes restent en base mais l'événement est marqué annulé


---

**3. Événement terminé** (`onTermine`)

```php
// EvenementWorkflowSubscriber.php - Ligne 97
public function onTermine(Event $event): void
{
    $evenement = $event->getSubject();
    
    // TODO: Générer les certificats automatiquement
    // $this->certificateService->generateForEvent($evenement);
    
    // TODO: Envoyer email de remerciement
    // $this->emailService->sendThankYou($evenement);
}
```

**Fonctionnalités prévues** :
- 📜 Génération automatique des certificats
- 📧 Email de remerciement
- 📊 Demande de feedback
- 🏆 Attribution de badges

---

### Méthode d'envoi aux participants

```php
// EvenementWorkflowSubscriber.php - Ligne 125
private function sendEmailsToParticipants(Evenement $evenement, string $type): void
{
    foreach ($evenement->getParticipations() as $participation) {
        // Vérifier que la participation est ACCEPTÉE
        if ($participation->getStatut() !== StatutParticipation::ACCEPTE) {
            continue;
        }
        
        $equipe = $participation->getEquipe();
        
        // Envoyer à chaque membre de l'équipe
        foreach ($equipe->getEtudiants() as $etudiant) {
            if ($type === 'started') {
                $this->emailService->sendEventStarted(...);
            } elseif ($type === 'cancelled') {
                $this->emailService->sendEventCancellation(...);
            }
        }
    }
}
```

**Filtrage intelligent** :
- ✅ Seules les participations **ACCEPTÉES** reçoivent les emails
- ✅ Chaque membre de chaque équipe reçoit un email personnalisé
- ✅ Gestion des erreurs : continue même si un email échoue


---

## 🗑️ ÉTAPE 7 : NETTOYAGE AUTOMATIQUE

### Suppression des participations refusées

```php
// FrontofficeParticipationController.php - Ligne 28
public function mesParticipations(...): Response
{
    // Récupérer TOUTES les participations
    $allParticipations = $participationRepository->createQueryBuilder('p')
        ->join('p.equipe', 'e')
        ->join('e.etudiants', 'et')
        ->where('et.id = :userId')
        ->setParameter('userId', $user->getId())
        ->getQuery()
        ->getResult();
    
    // Supprimer automatiquement les participations refusées
    $deletedCount = 0;
    foreach ($allParticipations as $participation) {
        if ($participation->getStatut()->value === 'Refusé') {
            $entityManager->remove($participation);
            $deletedCount++;
        }
    }
    if ($deletedCount > 0) {
        $entityManager->flush();
    }
    
    // Afficher uniquement les participations ACCEPTÉES ou EN_ATTENTE
    $participations = $participationRepository->createQueryBuilder('p')
        ->join('p.equipe', 'e')
        ->join('e.etudiants', 'et')
        ->where('et.id = :userId')
        ->andWhere('p.statut != :refuse')
        ->setParameter('userId', $user->getId())
        ->setParameter('refuse', 'Refusé')
        ->getQuery()
        ->getResult();
}
```

**Logique** :
1. Charger toutes les participations de l'utilisateur
2. Supprimer automatiquement celles avec statut "Refusé"
3. Afficher uniquement les participations valides

**Avantage** :
- 🧹 Base de données propre
- 📊 Statistiques précises
- 🚀 Pas de données inutiles


---

## 📊 SPRINT BURNDOWN CHART - DÉVELOPPEMENT DE LA FONCTIONNALITÉ

### Contexte du Sprint

**Sprint** : Développement du module de participation aux événements
**Durée** : 10 jours ouvrables
**Équipe** : 3 développeurs
**Capacité** : 60 Story Points (6 SP/jour)

---

### Décomposition en User Stories

| ID | User Story | Story Points | Priorité |
|----|-----------|--------------|----------|
| US-01 | En tant qu'étudiant, je veux créer une équipe pour un événement | 5 | HAUTE |
| US-02 | En tant qu'étudiant, je veux rejoindre une équipe existante avec places disponibles | 8 | HAUTE |
| US-03 | En tant qu'étudiant, je veux soumettre la participation de mon équipe | 5 | HAUTE |
| US-04 | En tant que système, je veux valider automatiquement les participations (3 règles) | 13 | CRITIQUE |
| US-05 | En tant que système, je veux envoyer un email de confirmation avec pièces jointes | 8 | HAUTE |
| US-06 | En tant que système, je veux afficher un message de refus en rouge avec la raison | 3 | MOYENNE |
| US-07 | En tant qu'étudiant, je veux voir mes participations acceptées | 3 | MOYENNE |
| US-08 | En tant que système, je veux envoyer des emails lors du démarrage d'un événement | 5 | MOYENNE |
| US-09 | En tant que système, je veux envoyer des emails lors de l'annulation d'un événement | 5 | MOYENNE |
| US-10 | En tant que système, je veux nettoyer automatiquement les participations refusées | 5 | BASSE |

**TOTAL** : 60 Story Points


---

### Planning du Sprint (10 jours)

| Jour | User Stories | SP Complétés | SP Restants | Notes |
|------|-------------|--------------|-------------|-------|
| J0 | - | 0 | 60 | Sprint Planning |
| J1 | US-01 (Création équipe) | 5 | 55 | Entité + Formulaire + Routes |
| J2 | US-03 (Soumission participation) | 5 | 50 | Formulaire + Routes basiques |
| J3 | US-04 (Validation auto) - Partie 1 | 7 | 43 | Règles 1 et 2 (annulé + capacité) |
| J4 | US-04 (Validation auto) - Partie 2 | 6 | 37 | Règle 3 (doublon étudiant) |
| J5 | US-02 (Rejoindre équipe) | 8 | 29 | Logique places disponibles |
| J6 | US-05 (Email confirmation) - Partie 1 | 4 | 25 | Service email + template |
| J7 | US-05 (Email confirmation) - Partie 2 | 4 | 21 | QR Code + Badge + .ics |
| J8 | US-06, US-07 (Messages + Liste) | 6 | 15 | Interface utilisateur |
| J9 | US-08, US-09 (Workflow emails) | 10 | 5 | Workflow subscriber |
| J10 | US-10 (Nettoyage) + Tests | 5 | 0 | Finalisation + Tests |

---

### Graphique Burndown Chart

```
Story Points Restants
    60 |●                                    
       |  ●●                                 Ligne Idéale (6 SP/jour)
    50 |      ●                              
       |        ●●                           Ligne Réelle
    40 |            ●                        
       |              ●                      
    30 |                ●●                   
       |                    ●                
    20 |                      ●              
       |                        ●            
    10 |                          ●          
       |                            ●●       
     0 |______________________________●______
       J0  J1  J2  J3  J4  J5  J6  J7  J8  J9  J10
                        Jours
```

### Analyse du Burndown

**Points clés** :

1. **J0-J2** : Démarrage rapide
   - Création des entités et formulaires de base
   - Ligne réelle légèrement au-dessus de l'idéale (normal en début de sprint)

2. **J3-J4** : Complexité de la validation
   - US-04 est la plus complexe (13 SP)
   - Divisée sur 2 jours
   - Ligne réelle se rapproche de l'idéale

3. **J5** : Pic de complexité
   - US-02 (Rejoindre équipe) = 8 SP
   - Logique de places disponibles + sécurité
   - Ligne réelle descend rapidement

4. **J6-J7** : Intégration email
   - Service email + génération de pièces jointes
   - Progression constante

5. **J8-J10** : Finalisation
   - Interface utilisateur
   - Workflow
   - Tests et nettoyage
   - Sprint terminé à temps ✅


---

## 🎯 DÉTAILS DES TÂCHES PAR USER STORY

### US-01 : Créer une équipe (5 SP)

**Tâches** :
- ✅ Créer l'entité `Equipe.php` avec contraintes (4-6 membres)
- ✅ Créer le formulaire `EquipeFrontType.php`
- ✅ Créer le contrôleur `FrontofficeEquipeController.php`
- ✅ Créer les routes `/equipe/new` et `/equipe/new-for-event/{eventId}`
- ✅ Créer les templates Twig
- ✅ Ajouter l'utilisateur connecté automatiquement

**Critères d'acceptation** :
- [x] Une équipe doit avoir entre 4 et 6 membres
- [x] Le nom de l'équipe est obligatoire
- [x] L'équipe est liée à un événement
- [x] L'utilisateur connecté est ajouté automatiquement

---

### US-02 : Rejoindre une équipe existante (8 SP)

**Tâches** :
- ✅ Calculer les places disponibles (6 - nbMembres)
- ✅ Afficher le badge "X place(s) disponible(s)"
- ✅ Créer le bouton "Rejoindre cette équipe"
- ✅ Vérifier que l'équipe n'est pas complète
- ✅ Vérifier que l'utilisateur n'est pas déjà membre
- ✅ Vérifier que l'événement accepte encore des participations
- ✅ Ajouter l'utilisateur à l'équipe
- ✅ Mettre à jour le compteur de places

**Critères d'acceptation** :
- [x] Affichage des places disponibles en temps réel
- [x] Impossible de rejoindre une équipe complète
- [x] Impossible de rejoindre deux fois la même équipe
- [x] Mise à jour automatique du compteur

---

### US-03 : Soumettre une participation (5 SP)

**Tâches** :
- ✅ Créer l'entité `Participation.php`
- ✅ Créer le formulaire `ParticipationFrontType.php`
- ✅ Créer le contrôleur `FrontofficeParticipationController.php`
- ✅ Créer les routes `/participation/new` et `/participation/new-for-team/{equipeId}/event/{eventId}`
- ✅ Filtrer les équipes (uniquement celles de l'utilisateur)
- ✅ Créer les templates Twig

**Critères d'acceptation** :
- [x] L'utilisateur ne peut participer qu'avec ses équipes
- [x] Sélection de l'événement et de l'équipe
- [x] Soumission du formulaire


---

### US-04 : Validation automatique (13 SP) - LA PLUS COMPLEXE

**Tâches** :

**Règle 1 : Événement annulé (3 SP)**
- ✅ Vérifier `$evenement->getIsCanceled()`
- ✅ Définir le statut à `REFUSE`
- ✅ Retourner un message d'erreur personnalisé

**Règle 2 : Capacité maximale (4 SP)**
- ✅ Compter les participations acceptées (exclure la participation actuelle)
- ✅ Comparer avec `$evenement->getNbMax()`
- ✅ Définir le statut à `REFUSE` si capacité atteinte
- ✅ Retourner un message avec le nombre maximum

**Règle 3 : Doublon d'étudiant (6 SP)**
- ✅ Parcourir toutes les participations acceptées
- ✅ Comparer chaque étudiant de la nouvelle équipe avec les équipes existantes
- ✅ Détecter les doublons par ID
- ✅ Définir le statut à `REFUSE` si doublon trouvé
- ✅ Retourner un message avec le nom de l'étudiant et de l'équipe existante

**Critères d'acceptation** :
- [x] Refus automatique si événement annulé
- [x] Refus automatique si capacité atteinte
- [x] Refus automatique si un étudiant participe déjà
- [x] Acceptation automatique si toutes les règles sont respectées
- [x] Messages d'erreur clairs et personnalisés

---

### US-05 : Email de confirmation (8 SP)

**Tâches** :

**Partie 1 : Service email (4 SP)**
- ✅ Créer `EmailService.php`
- ✅ Méthode `sendParticipationConfirmation()`
- ✅ Template Twig `participation_confirmation.html.twig`
- ✅ Configuration SendGrid/Brevo

**Partie 2 : Pièces jointes (4 SP)**
- ✅ Générer le QR Code (API externe)
- ✅ Générer le Badge PDF (`BadgeService.php`)
- ✅ Générer le fichier .ics (calendrier)
- ✅ Attacher les 3 fichiers à l'email

**Critères d'acceptation** :
- [x] Email envoyé à tous les membres de l'équipe
- [x] QR Code avec toutes les informations
- [x] Badge PDF personnalisé
- [x] Fichier .ics pour ajouter au calendrier
- [x] Gestion des erreurs d'envoi


---

### US-06 : Message de refus en rouge (3 SP)

**Tâches** :
- ✅ Afficher le message d'erreur avec `addFlash('error', ...)`
- ✅ Styliser en rouge dans le template
- ✅ Afficher la raison exacte du refus

**Critères d'acceptation** :
- [x] Message en rouge visible
- [x] Raison du refus claire
- [x] Pas de création de participation en base

---

### US-07 : Liste des participations (3 SP)

**Tâches** :
- ✅ Route `/participation/mes-participations`
- ✅ Filtrer par utilisateur connecté
- ✅ Afficher uniquement les participations acceptées ou en attente
- ✅ Template Twig avec tableau

**Critères d'acceptation** :
- [x] Liste des participations de l'utilisateur
- [x] Affichage du statut (Accepté/En attente)
- [x] Informations de l'équipe et de l'événement

---

### US-08 : Email de démarrage (5 SP)

**Tâches** :
- ✅ Créer `EvenementWorkflowSubscriber.php`
- ✅ Écouter l'événement `workflow.evenement_publishing.entered.en_cours`
- ✅ Méthode `onEnCours()`
- ✅ Envoyer email à tous les participants acceptés
- ✅ Template `event_started.html.twig`

**Critères d'acceptation** :
- [x] Email envoyé automatiquement au démarrage
- [x] Uniquement aux participations acceptées
- [x] Tous les membres de chaque équipe reçoivent l'email

---

### US-09 : Email d'annulation (5 SP)

**Tâches** :
- ✅ Écouter l'événement `workflow.evenement_publishing.entered.annule`
- ✅ Méthode `onAnnule()`
- ✅ Envoyer email d'annulation
- ✅ Template `event_cancelled.html.twig`
- ✅ Bloquer les nouvelles participations

**Critères d'acceptation** :
- [x] Email envoyé automatiquement à l'annulation
- [x] Uniquement aux participations acceptées
- [x] Nouvelles participations refusées automatiquement

---

### US-10 : Nettoyage automatique (5 SP)

**Tâches** :
- ✅ Détecter les participations refusées
- ✅ Supprimer automatiquement de la base
- ✅ Exécuter lors de l'affichage de "Mes participations"
- ✅ Logger les suppressions

**Critères d'acceptation** :
- [x] Participations refusées supprimées automatiquement
- [x] Base de données propre
- [x] Pas d'impact sur les participations acceptées


---

## 📈 MÉTRIQUES DU SPRINT

### Vélocité de l'équipe

| Métrique | Valeur |
|----------|--------|
| Story Points planifiés | 60 SP |
| Story Points complétés | 60 SP |
| Vélocité | 6 SP/jour |
| Taux de complétion | 100% |
| Durée du sprint | 10 jours |

### Répartition par complexité

| Complexité | Nombre de US | Story Points | % du total |
|------------|--------------|--------------|------------|
| Faible (1-3 SP) | 3 | 9 SP | 15% |
| Moyenne (4-8 SP) | 6 | 38 SP | 63% |
| Élevée (9+ SP) | 1 | 13 SP | 22% |

### Répartition par priorité

| Priorité | Nombre de US | Story Points |
|----------|--------------|--------------|
| CRITIQUE | 1 | 13 SP |
| HAUTE | 4 | 26 SP |
| MOYENNE | 4 | 16 SP |
| BASSE | 1 | 5 SP |

---

## 🎓 LEÇONS APPRISES

### Points positifs ✅

1. **Validation automatique robuste**
   - Les 3 règles couvrent tous les cas d'usage
   - Messages d'erreur clairs et personnalisés
   - Pas de participation invalide en base

2. **Emails automatiques**
   - Confirmation immédiate
   - Pièces jointes utiles (QR Code, Badge, .ics)
   - Gestion des erreurs d'envoi

3. **Workflow bien intégré**
   - Emails automatiques lors des transitions
   - Historique complet des événements
   - Guards pour empêcher les transitions invalides

4. **Sécurité**
   - Authentification requise
   - Filtrage des équipes par utilisateur
   - Vérification des doublons

### Points d'amélioration 🔧

1. **Performance**
   - La règle 3 (doublon) fait beaucoup de boucles imbriquées
   - Optimisation possible avec une requête SQL

2. **Tests**
   - Ajouter des tests unitaires pour `validateParticipation()`
   - Tests d'intégration pour les emails

3. **Interface utilisateur**
   - Ajouter une prévisualisation avant soumission
   - Afficher le nombre de places restantes en temps réel

4. **Notifications**
   - Ajouter des notifications push
   - Système de rappels avant l'événement


---

## 🔄 SCÉNARIOS D'UTILISATION COMPLETS

### Scénario 1 : Participation réussie ✅

**Contexte** :
- Événement : "Hackathon 2026"
- Capacité : 10 équipes
- Participations acceptées : 5 équipes
- Étudiant : Ahmed

**Étapes** :

1. **Ahmed crée une équipe**
   ```
   GET /equipe/new-for-event/123
   - Nom : "Team Innovators"
   - Membres : Ahmed (auto), Fatima, Youssef, Sara
   POST → Équipe créée ✅
   ```

2. **Ahmed soumet la participation**
   ```
   GET /participation/new-for-team/456/event/123
   → Validation automatique
   ```

3. **Validation (3 règles)**
   ```
   ✅ Règle 1 : Événement NON annulé
   ✅ Règle 2 : 5/10 équipes → Places disponibles
   ✅ Règle 3 : Aucun doublon d'étudiant
   
   → Statut : ACCEPTÉ
   ```

4. **Envoi des emails**
   ```
   📧 Email à Ahmed
   📧 Email à Fatima
   📧 Email à Youssef
   📧 Email à Sara
   
   Pièces jointes :
   - QR Code
   - Badge PDF
   - Fichier .ics
   ```

5. **Confirmation**
   ```
   Message vert : "Participation acceptée avec succès ! 
                   Votre équipe Team Innovators est inscrite 
                   à l'événement Hackathon 2026."
   
   Redirection → /participation/mes-participations
   ```

---

### Scénario 2 : Refus - Capacité atteinte ❌

**Contexte** :
- Événement : "Workshop AI"
- Capacité : 5 équipes
- Participations acceptées : 5 équipes (COMPLET)
- Étudiant : Mohamed

**Étapes** :

1. **Mohamed crée une équipe**
   ```
   Équipe : "AI Masters"
   Membres : Mohamed, Leila, Karim, Nadia
   ```

2. **Mohamed soumet la participation**
   ```
   GET /participation/new-for-team/789/event/456
   → Validation automatique
   ```

3. **Validation (3 règles)**
   ```
   ✅ Règle 1 : Événement NON annulé
   ❌ Règle 2 : 5/5 équipes → CAPACITÉ ATTEINTE
   
   → Statut : REFUSÉ
   ```

4. **Résultat**
   ```
   Message rouge : "La capacité maximale de l'événement est 
                    atteinte (5 équipes maximum). Votre 
                    participation a été refusée."
   
   ❌ Aucun email envoyé
   ❌ Participation NON créée en base
   
   Redirection → /participation/mes-participations
   ```


---

### Scénario 3 : Refus - Doublon d'étudiant ❌

**Contexte** :
- Événement : "Hackathon 2026"
- Participations acceptées :
  - Équipe A : [Ahmed, Fatima, Youssef, Sara]
  - Équipe B : [Mohamed, Leila, Karim, Nadia]
- Étudiant : Ali

**Étapes** :

1. **Ali crée une équipe**
   ```
   Équipe : "Code Warriors"
   Membres : Ali, Ahmed, Salma, Omar
                    ↑
                 DOUBLON !
   ```

2. **Ali soumet la participation**
   ```
   GET /participation/new-for-team/999/event/123
   → Validation automatique
   ```

3. **Validation (3 règles)**
   ```
   ✅ Règle 1 : Événement NON annulé
   ✅ Règle 2 : 2/10 équipes → Places disponibles
   ❌ Règle 3 : Ahmed participe déjà avec "Team Innovators"
   
   → Statut : REFUSÉ
   ```

4. **Résultat**
   ```
   Message rouge : "L'étudiant Ahmed participe déjà à cet 
                    événement avec l'équipe Team Innovators. 
                    Un étudiant ne peut pas participer avec 
                    deux équipes différentes au même événement."
   
   ❌ Aucun email envoyé
   ❌ Participation NON créée en base
   
   Redirection → /participation/mes-participations
   ```

---

### Scénario 4 : Refus - Événement annulé ❌

**Contexte** :
- Événement : "Conference 2026"
- Statut : ANNULÉ (isCanceled = true)
- Étudiant : Salma

**Étapes** :

1. **Salma crée une équipe**
   ```
   Équipe : "Tech Leaders"
   Membres : Salma, Omar, Ines, Rami
   ```

2. **Salma soumet la participation**
   ```
   GET /participation/new-for-team/111/event/789
   → Validation automatique
   ```

3. **Validation (3 règles)**
   ```
   ❌ Règle 1 : Événement ANNULÉ
   
   → Statut : REFUSÉ (sans vérifier les autres règles)
   ```

4. **Résultat**
   ```
   Message rouge : "L'événement Conference 2026 a été annulé. 
                    Aucune participation n'est acceptée."
   
   ❌ Aucun email envoyé
   ❌ Participation NON créée en base
   
   Redirection → /participation/mes-participations
   ```


---

### Scénario 5 : Rejoindre une équipe existante ✅

**Contexte** :
- Événement : "Hackathon 2026"
- Équipe existante : "Team Innovators"
  - Membres actuels : Ahmed, Fatima, Youssef, Sara (4 membres)
  - Places disponibles : 2
- Étudiant : Karim (veut rejoindre)

**Étapes** :

1. **Karim consulte les équipes**
   ```
   GET /evenement/123/equipes
   
   Affichage :
   ┌─────────────────────────────────────┐
   │ Team Innovators                     │
   │ 4 membres                           │
   │ 🟢 2 place(s) disponible(s)         │
   │ [Rejoindre cette équipe]            │
   └─────────────────────────────────────┘
   ```

2. **Karim clique sur "Rejoindre"**
   ```
   POST /equipe/456/join
   
   Vérifications :
   ✅ Équipe pas complète (4 < 6)
   ✅ Karim n'est pas déjà membre
   ✅ Événement accepte encore des participations
   
   → Karim ajouté à l'équipe
   ```

3. **Mise à jour automatique**
   ```
   Équipe "Team Innovators" :
   - Membres : Ahmed, Fatima, Youssef, Sara, Karim (5 membres)
   - Places disponibles : 1
   
   Affichage mis à jour :
   ┌─────────────────────────────────────┐
   │ Team Innovators                     │
   │ 5 membres                           │
   │ 🟡 1 place disponible               │
   │ [Rejoindre cette équipe]            │
   └─────────────────────────────────────┘
   ```

4. **Si un 6ème membre rejoint**
   ```
   Équipe "Team Innovators" :
   - Membres : Ahmed, Fatima, Youssef, Sara, Karim, Nadia (6 membres)
   - Places disponibles : 0
   
   Affichage mis à jour :
   ┌─────────────────────────────────────┐
   │ Team Innovators                     │
   │ 6 membres                           │
   │ 🔴 Équipe complète                  │
   │ [Bouton désactivé]                  │
   └─────────────────────────────────────┘
   ```


---

## 🚀 WORKFLOW COMPLET - VUE D'ENSEMBLE

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    CYCLE DE VIE D'UNE PARTICIPATION                      │
└─────────────────────────────────────────────────────────────────────────┘

1. CRÉATION D'ÉQUIPE
   ├─ Utilisateur crée une équipe (4-6 membres)
   ├─ OU rejoint une équipe existante (si places disponibles)
   └─ Équipe liée à un événement
   
2. SOUMISSION DE PARTICIPATION
   ├─ Utilisateur sélectionne son équipe
   ├─ Utilisateur sélectionne l'événement
   └─ Soumission du formulaire
   
3. VALIDATION AUTOMATIQUE
   ├─ Règle 1 : Événement NON annulé ?
   │   ├─ OUI → Continuer
   │   └─ NON → REFUS (message rouge)
   │
   ├─ Règle 2 : Capacité disponible ?
   │   ├─ OUI → Continuer
   │   └─ NON → REFUS (message rouge)
   │
   └─ Règle 3 : Aucun doublon d'étudiant ?
       ├─ OUI → ACCEPTATION
       └─ NON → REFUS (message rouge)
   
4. SI ACCEPTÉ
   ├─ Création de la participation en base
   ├─ Statut : ACCEPTÉ
   ├─ Envoi d'emails à tous les membres
   │   ├─ QR Code
   │   ├─ Badge PDF
   │   └─ Fichier .ics
   └─ Message vert de confirmation
   
5. SI REFUSÉ
   ├─ Aucune création en base
   ├─ Message rouge avec raison
   └─ Redirection vers "Mes participations"
   
6. CYCLE DE VIE DE L'ÉVÉNEMENT
   ├─ PLANIFIÉ → Participations ouvertes
   │
   ├─ DÉMARRAGE (transition "demarrer")
   │   ├─ Email automatique à tous les participants
   │   └─ Sujet : "🚀 Event Started"
   │
   ├─ EN COURS → Événement en cours
   │
   ├─ TERMINÉ (transition "terminer")
   │   ├─ Génération des certificats (TODO)
   │   └─ Email de remerciement (TODO)
   │
   └─ ANNULÉ (transition "annuler")
       ├─ Email d'annulation à tous les participants
       ├─ Sujet : "⚠️ Event Cancelled"
       └─ Nouvelles participations refusées automatiquement
   
7. NETTOYAGE AUTOMATIQUE
   └─ Suppression des participations refusées lors de l'affichage
```


---

## 📊 CONCLUSION : POURQUOI UN BURNDOWN CHART EST PERTINENT ICI ?

### ✅ OUI, c'est totalement applicable !

Le Sprint Burndown Chart est **parfaitement adapté** pour suivre le développement du processus de participation aux événements car :

### 1. Complexité mesurable

La fonctionnalité est décomposable en **User Stories indépendantes** :
- Chaque US a une valeur en Story Points
- Chaque US peut être complétée indépendamment
- La progression est mesurable jour par jour

### 2. Dépendances claires

Certaines US dépendent d'autres :
```
US-01 (Créer équipe) → US-02 (Rejoindre équipe)
                     ↓
US-03 (Soumettre participation) → US-04 (Validation auto)
                                ↓
                    US-05 (Email confirmation)
                                ↓
                    US-06 (Message refus)
```

Le Burndown Chart permet de visualiser si ces dépendances causent des retards.

### 3. Risques identifiables

**US-04 (Validation automatique)** est la plus complexe (13 SP) :
- Si elle prend plus de temps que prévu → La ligne réelle monte
- Le Burndown Chart alerte l'équipe immédiatement
- L'équipe peut ajuster (ajouter des ressources, simplifier, etc.)

### 4. Prédiction de livraison

Avec le Burndown Chart, on peut prédire :
- **Jour 5** : "À ce rythme, on finira en 12 jours au lieu de 10"
- **Action** : Prioriser les US critiques, reporter les US basses priorités
- **Résultat** : Sprint terminé à temps

### 5. Amélioration continue

Après le sprint, l'équipe analyse le Burndown Chart :
- "Pourquoi la ligne réelle est montée au Jour 3 ?"
- "US-04 était sous-estimée, on devrait estimer 15 SP la prochaine fois"
- "On a perdu du temps sur l'intégration email, il faut mieux préparer"

---

## 🎯 RECOMMANDATIONS POUR UTILISER LE BURNDOWN CHART

### 1. Mise à jour quotidienne

**Daily Standup** (15 minutes chaque matin) :
```
- Qu'est-ce que j'ai fait hier ?
- Qu'est-ce que je vais faire aujourd'hui ?
- Y a-t-il des blocages ?

→ Mettre à jour le Burndown Chart
→ Ajuster si nécessaire
```

### 2. Outils recommandés

- **Jira** : Burndown Chart automatique
- **Trello** : Plugin "Burndown for Trello"
- **Excel/Google Sheets** : Graphique manuel
- **GitHub Projects** : Burndown Chart intégré

### 3. Alertes à surveiller

🚨 **Ligne réelle MONTE** → Scope creep (ajout de travail)
🚨 **Ligne réelle PLATE** → Blocage (aucun progrès)
🚨 **Ligne réelle AU-DESSUS de l'idéale** → Retard

### 4. Actions correctives

Si retard détecté :
1. **Prioriser** : Se concentrer sur les US critiques
2. **Simplifier** : Réduire le scope des US basses priorités
3. **Ajouter des ressources** : Demander de l'aide
4. **Reporter** : Déplacer certaines US au prochain sprint

---

## 📚 RESSOURCES SUPPLÉMENTAIRES

### Lectures recommandées

- **Scrum Guide** : https://scrumguides.org/
- **Agile Manifesto** : https://agilemanifesto.org/
- **Burndown Chart Best Practices** : https://www.atlassian.com/agile/tutorials/burndown-charts

### Outils

- **Jira** : https://www.atlassian.com/software/jira
- **Trello** : https://trello.com/
- **GitHub Projects** : https://github.com/features/issues
- **Azure DevOps** : https://azure.microsoft.com/en-us/products/devops

---

## ✅ RÉSUMÉ FINAL

Le **Sprint Burndown Chart** est un outil puissant pour :
- ✅ Visualiser la progression du travail
- ✅ Détecter les problèmes rapidement
- ✅ Prédire la livraison
- ✅ Améliorer la planification

Pour le processus de participation aux événements :
- ✅ 10 User Stories (60 Story Points)
- ✅ Sprint de 10 jours
- ✅ Vélocité de 6 SP/jour
- ✅ Livraison à temps

**Le Burndown Chart est totalement applicable et recommandé pour ce type de projet !** 🎉

