# 🗑️ Explication : Suppression en Cascade

## ❌ Le Problème

### Erreur rencontrée
```
SQLSTATE[23000]: Integrity constraint violation: 1451 
Cannot delete or update a parent row: a foreign key constraint fails
```

### Qu'est-ce que ça signifie ?

Vous essayez de supprimer un **Cours** qui a des **Chapitres** associés.

La base de données refuse car :
- Les chapitres ont une clé étrangère `cours_id` qui pointe vers le cours
- Si on supprime le cours, les chapitres deviendraient "orphelins" (sans parent)
- MySQL protège l'intégrité des données en bloquant la suppression

### Schéma du problème

```
Cours (id=1)
  ├── Chapitre 1 (cours_id=1)
  │     ├── Quiz 1 (chapitre_id=1)
  │     └── Quiz 2 (chapitre_id=1)
  ├── Chapitre 2 (cours_id=1)
  └── Chapitre 3 (cours_id=1)

❌ Impossible de supprimer Cours (id=1) 
   car il a des chapitres qui dépendent de lui
```

---

## ✅ La Solution : Suppression en Cascade

### Qu'est-ce que la suppression en cascade ?

Quand vous supprimez un **parent**, tous ses **enfants** sont automatiquement supprimés.

### Configuration dans les Entités

#### 1. Entité Cours → Chapitres

**Avant** (❌ Erreur)
```php
#[ORM\OneToMany(targetEntity: Chapitre::class, mappedBy: 'cours')]
private Collection $chapitres;
```

**Après** (✅ Fonctionne)
```php
#[ORM\OneToMany(
    targetEntity: Chapitre::class, 
    mappedBy: 'cours', 
    orphanRemoval: true,           // ← Supprime les orphelins
    cascade: ['persist', 'remove']  // ← Cascade les opérations
)]
private Collection $chapitres;
```

#### 2. Entité Chapitre → Quiz

**Avant** (❌ Erreur)
```php
#[ORM\OneToMany(targetEntity: Quiz::class, mappedBy: 'chapitre')]
private Collection $quizzes;
```

**Après** (✅ Fonctionne)
```php
#[ORM\OneToMany(
    targetEntity: Quiz::class, 
    mappedBy: 'chapitre', 
    orphanRemoval: true,           // ← Supprime les orphelins
    cascade: ['persist', 'remove']  // ← Cascade les opérations
)]
private Collection $quizzes;
```

---

## 🔍 Explication des Options

### `orphanRemoval: true`

**Définition** : Supprime automatiquement les entités enfants qui n'ont plus de parent.

**Exemple** :
```php
$cours = $coursRepository->find(1);
$chapitre = $cours->getChapitres()->first();

// Retirer le chapitre de la collection
$cours->removeChapitre($chapitre);

// Sauvegarder
$entityManager->flush();

// ✅ Le chapitre est automatiquement supprimé de la base de données
```

### `cascade: ['persist', 'remove']`

**Définition** : Propage les opérations du parent vers les enfants.

**Options disponibles** :
- `persist` : Quand on sauvegarde le parent, les enfants sont aussi sauvegardés
- `remove` : Quand on supprime le parent, les enfants sont aussi supprimés
- `merge` : Quand on fusionne le parent, les enfants sont aussi fusionnés
- `detach` : Quand on détache le parent, les enfants sont aussi détachés
- `refresh` : Quand on rafraîchit le parent, les enfants sont aussi rafraîchis
- `all` : Toutes les opérations ci-dessus

**Exemple avec `remove`** :
```php
$cours = $coursRepository->find(1);

// Supprimer le cours
$entityManager->remove($cours);
$entityManager->flush();

// ✅ Tous les chapitres du cours sont automatiquement supprimés
// ✅ Tous les quiz des chapitres sont automatiquement supprimés
```

---

## 📊 Comportement avec la Cascade

### Avant (sans cascade)

```
Action: Supprimer Cours (id=1)

Cours (id=1) ❌ ERREUR
  ├── Chapitre 1 (cours_id=1) ⚠️ Bloque la suppression
  ├── Chapitre 2 (cours_id=1) ⚠️ Bloque la suppression
  └── Chapitre 3 (cours_id=1) ⚠️ Bloque la suppression

Résultat: Erreur de contrainte d'intégrité
```

### Après (avec cascade)

```
Action: Supprimer Cours (id=1)

Cours (id=1) ✅ Supprimé
  ├── Chapitre 1 (cours_id=1) ✅ Supprimé automatiquement
  │     ├── Quiz 1 (chapitre_id=1) ✅ Supprimé automatiquement
  │     └── Quiz 2 (chapitre_id=1) ✅ Supprimé automatiquement
  ├── Chapitre 2 (cours_id=1) ✅ Supprimé automatiquement
  └── Chapitre 3 (cours_id=1) ✅ Supprimé automatiquement

Résultat: Tout est supprimé proprement
```

---

## 🎯 Dans Votre Projet

### Hiérarchie de suppression

```
Cours
  └─ cascade → Chapitre
                └─ cascade → Quiz
                              └─ orphanRemoval → Question
                                                   └─ orphanRemoval → Option
```

Quand vous supprimez un **Cours** :
1. ✅ Tous les **Chapitres** sont supprimés (cascade)
2. ✅ Tous les **Quiz** des chapitres sont supprimés (cascade)
3. ✅ Toutes les **Questions** des quiz sont supprimées (orphanRemoval)
4. ✅ Toutes les **Options** des questions sont supprimées (orphanRemoval)

---

## ⚠️ Attention : Différence entre `orphanRemoval` et `cascade`

### `orphanRemoval: true`
- Supprime l'enfant quand il est **retiré de la collection**
- Fonctionne uniquement avec `OneToMany` et `OneToOne`
- Plus restrictif

**Exemple** :
```php
$cours->removeChapitre($chapitre);  // ← Retire de la collection
$entityManager->flush();             // ← Le chapitre est supprimé
```

### `cascade: ['remove']`
- Supprime l'enfant quand le **parent est supprimé**
- Fonctionne avec toutes les relations
- Plus général

**Exemple** :
```php
$entityManager->remove($cours);  // ← Supprime le parent
$entityManager->flush();         // ← Les chapitres sont supprimés
```

### Recommandation
Utilisez **les deux** pour une suppression complète :
```php
#[ORM\OneToMany(
    targetEntity: Chapitre::class, 
    mappedBy: 'cours', 
    orphanRemoval: true,           // ← Pour retrait de collection
    cascade: ['persist', 'remove']  // ← Pour suppression du parent
)]
```

---

## 🔧 Commandes Exécutées

```bash
# 1. Vider le cache après modification des entités
php bin/console cache:clear

# 2. Vérifier le schéma (optionnel)
php bin/console doctrine:schema:update --dump-sql

# 3. Mettre à jour le schéma si nécessaire
php bin/console doctrine:schema:update --force
```

---

## 📝 Code du Contrôleur (Inchangé)

Le code du contrôleur reste le même :

```php
#[Route('/{id}/delete', name: 'app_cours_delete', methods: ['POST'])]
public function delete(Request $request, Cours $cour, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete'.$cour->getId(), $request->getPayload()->getString('_token'))) {
        $entityManager->remove($cour);  // ← Supprime le cours
        $entityManager->flush();         // ← Cascade automatique
    }

    return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
}
```

Doctrine gère automatiquement la suppression en cascade grâce aux annotations dans les entités.

---

## ✅ Résultat

Maintenant, quand vous supprimez un cours :
- ✅ Le cours est supprimé
- ✅ Tous ses chapitres sont supprimés automatiquement
- ✅ Tous les quiz des chapitres sont supprimés automatiquement
- ✅ Aucune erreur de contrainte d'intégrité

---

## 🎓 Autres Entités à Vérifier

Vérifiez aussi ces relations dans votre projet :

| Parent | Enfant | Cascade nécessaire ? |
|--------|--------|---------------------|
| Quiz | Question | ✅ Oui (déjà fait avec `orphanRemoval`) |
| Question | Option | ✅ Oui (déjà fait avec `orphanRemoval`) |
| Challenge | Exercice | ⚠️ À vérifier |
| Communaute | Post | ⚠️ À vérifier |
| Post | Commentaire | ⚠️ À vérifier |

---

**Date**: 11 février 2026  
**Version**: 1.0
