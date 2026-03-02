# 📝 Comment Faire les 8 Tests - Guide Étape par Étape

## 🎯 Objectif

Apprendre à créer et exécuter les 8 tests unitaires pour valider l'entité Chapitre.

---

## 📋 Prérequis

Avant de commencer, assurez-vous d'avoir:
- ✅ Symfony installé
- ✅ PHPUnit installé (normalement inclus avec Symfony)
- ✅ L'entité Chapitre qui existe déjà

---

## 🚀 ÉTAPE 1: Créer le Service ChapitreManager

### Où créer le fichier?
📁 `src/Service/ChapitreManager.php`

### Que mettre dedans?

```php
<?php

namespace App\Service;

use App\Entity\GestionDeCours\Chapitre;
use InvalidArgumentException;

class ChapitreManager
{
    /**
     * Valide un chapitre selon les règles métier
     */
    public function validate(Chapitre $chapitre): bool
    {
        // Règle 1: Le titre est obligatoire
        if (empty($chapitre->getTitre())) {
            throw new InvalidArgumentException('Le titre du chapitre est obligatoire');
        }

        // Règle 2: Le contenu ne doit pas être vide
        if (empty($chapitre->getContenu())) {
            throw new InvalidArgumentException('Le contenu du chapitre ne peut pas être vide');
        }

        // Règle 3: L'ordre doit être un nombre positif
        if ($chapitre->getOrdre() === null || $chapitre->getOrdre() <= 0) {
            throw new InvalidArgumentException('L\'ordre du chapitre doit être un nombre positif');
        }

        return true;
    }
}
```

### 💡 Explication du code

- `empty()` vérifie si une valeur est vide (null, "", 0, false)
- `throw new InvalidArgumentException()` lance une erreur avec un message
- Si tout est OK, on retourne `true`

---

## 🚀 ÉTAPE 2: Créer le Fichier de Tests

### Où créer le fichier?
📁 `tests/Service/ChapitreManagerTest.php`

### Structure de base

```php
<?php

namespace App\Tests\Service;

use App\Entity\GestionDeCours\Chapitre;
use App\Service\ChapitreManager;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ChapitreManagerTest extends TestCase
{
    private ChapitreManager $manager;

    // Cette fonction s'exécute AVANT chaque test
    protected function setUp(): void
    {
        $this->manager = new ChapitreManager();
    }

    // Ici on va mettre nos 8 tests
}
```

---

## 🧪 ÉTAPE 3: Écrire les 8 Tests

### ✅ TEST 1: Chapitre Valide

**Ce qu'on teste:** Un chapitre avec toutes les informations correctes doit être accepté.

```php
public function testValidChapitre(): void
{
    // 1. ARRANGE: Préparer les données
    $chapitre = new Chapitre();
    $chapitre->setTitre('Introduction aux bases de données');
    $chapitre->setContenu('Les bases de données sont des systèmes qui permettent...');
    $chapitre->setOrdre(1);

    // 2. ACT & ASSERT: Exécuter et vérifier
    $this->assertTrue($this->manager->validate($chapitre));
}
```

**Résultat attendu:** ✅ Le test passe (pas d'erreur)

---

### ❌ TEST 2: Chapitre Sans Titre

**Ce qu'on teste:** Un chapitre sans titre doit être rejeté.

```php
public function testChapitreWithoutTitre(): void
{
    // 1. ARRANGE: Créer un chapitre SANS titre
    $chapitre = new Chapitre();
    // PAS DE setTitre() ici!
    $chapitre->setContenu('Contenu du chapitre');
    $chapitre->setOrdre(1);

    // 2. ASSERT: On s'attend à une erreur
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Le titre du chapitre est obligatoire');

    // 3. ACT: Tenter de valider
    $this->manager->validate($chapitre);
}
```

**Résultat attendu:** ✅ Le test passe (l'erreur est bien levée)

---

### ❌ TEST 3: Chapitre Avec Titre Vide

**Ce qu'on teste:** Un chapitre avec un titre vide ("") doit être rejeté.

```php
public function testChapitreWithEmptyTitre(): void
{
    // 1. ARRANGE: Créer un chapitre avec titre VIDE
    $chapitre = new Chapitre();
    $chapitre->setTitre(''); // Chaîne vide
    $chapitre->setContenu('Contenu du chapitre');
    $chapitre->setOrdre(1);

    // 2. ASSERT: On s'attend à une erreur
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Le titre du chapitre est obligatoire');

    // 3. ACT: Tenter de valider
    $this->manager->validate($chapitre);
}
```

**Résultat attendu:** ✅ Le test passe (l'erreur est bien levée)

---

### ❌ TEST 4: Chapitre Sans Contenu

**Ce qu'on teste:** Un chapitre sans contenu doit être rejeté.

```php
public function testChapitreWithoutContenu(): void
{
    // 1. ARRANGE: Créer un chapitre SANS contenu
    $chapitre = new Chapitre();
    $chapitre->setTitre('Titre du chapitre');
    // PAS DE setContenu() ici!
    $chapitre->setOrdre(1);

    // 2. ASSERT: On s'attend à une erreur
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Le contenu du chapitre ne peut pas être vide');

    // 3. ACT: Tenter de valider
    $this->manager->validate($chapitre);
}
```

**Résultat attendu:** ✅ Le test passe (l'erreur est bien levée)

---

### ❌ TEST 5: Chapitre Avec Contenu Vide

**Ce qu'on teste:** Un chapitre avec un contenu vide ("") doit être rejeté.

```php
public function testChapitreWithEmptyContenu(): void
{
    // 1. ARRANGE: Créer un chapitre avec contenu VIDE
    $chapitre = new Chapitre();
    $chapitre->setTitre('Titre du chapitre');
    $chapitre->setContenu(''); // Chaîne vide
    $chapitre->setOrdre(1);

    // 2. ASSERT: On s'attend à une erreur
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Le contenu du chapitre ne peut pas être vide');

    // 3. ACT: Tenter de valider
    $this->manager->validate($chapitre);
}
```

**Résultat attendu:** ✅ Le test passe (l'erreur est bien levée)

---

### ❌ TEST 6: Chapitre Avec Ordre Négatif

**Ce qu'on teste:** Un chapitre avec un ordre négatif (-1) doit être rejeté.

```php
public function testChapitreWithNegativeOrdre(): void
{
    // 1. ARRANGE: Créer un chapitre avec ordre NÉGATIF
    $chapitre = new Chapitre();
    $chapitre->setTitre('Titre du chapitre');
    $chapitre->setContenu('Contenu du chapitre');
    $chapitre->setOrdre(-1); // Ordre négatif

    // 2. ASSERT: On s'attend à une erreur
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('L\'ordre du chapitre doit être un nombre positif');

    // 3. ACT: Tenter de valider
    $this->manager->validate($chapitre);
}
```

**Résultat attendu:** ✅ Le test passe (l'erreur est bien levée)

---

### ❌ TEST 7: Chapitre Avec Ordre Zéro

**Ce qu'on teste:** Un chapitre avec un ordre zéro (0) doit être rejeté.

```php
public function testChapitreWithZeroOrdre(): void
{
    // 1. ARRANGE: Créer un chapitre avec ordre ZÉRO
    $chapitre = new Chapitre();
    $chapitre->setTitre('Titre du chapitre');
    $chapitre->setContenu('Contenu du chapitre');
    $chapitre->setOrdre(0); // Ordre zéro

    // 2. ASSERT: On s'attend à une erreur
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('L\'ordre du chapitre doit être un nombre positif');

    // 3. ACT: Tenter de valider
    $this->manager->validate($chapitre);
}
```

**Résultat attendu:** ✅ Le test passe (l'erreur est bien levée)

---

### ❌ TEST 8: Chapitre Sans Ordre

**Ce qu'on teste:** Un chapitre sans ordre (null) doit être rejeté.

```php
public function testChapitreWithoutOrdre(): void
{
    // 1. ARRANGE: Créer un chapitre SANS ordre
    $chapitre = new Chapitre();
    $chapitre->setTitre('Titre du chapitre');
    $chapitre->setContenu('Contenu du chapitre');
    // PAS DE setOrdre() ici!

    // 2. ASSERT: On s'attend à une erreur
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('L\'ordre du chapitre doit être un nombre positif');

    // 3. ACT: Tenter de valider
    $this->manager->validate($chapitre);
}
```

**Résultat attendu:** ✅ Le test passe (l'erreur est bien levée)

---

## ▶️ ÉTAPE 4: Exécuter les Tests

### Commande à lancer

Ouvrez votre terminal PowerShell dans le dossier du projet et tapez:

```bash
.\vendor\bin\phpunit tests/Service/ChapitreManagerTest.php
```

### Résultat attendu

```
PHPUnit 9.6.34 by Sebastian Bergmann and contributors.

Testing App\Tests\Service\ChapitreManagerTest
........                                                            8 / 8 (100%)

Time: 00:00.048, Memory: 10.00 MB

OK (8 tests, 15 assertions)
```

### 📊 Comprendre le résultat

- `........` → 8 points = 8 tests réussis
- `8 / 8 (100%)` → 100% de réussite
- `OK` → Tous les tests passent
- `15 assertions` → 15 vérifications au total

---

## 🔍 ÉTAPE 5: Comprendre le Pattern AAA

Chaque test suit le pattern **AAA**:

### 1️⃣ ARRANGE (Préparer)
```php
$chapitre = new Chapitre();
$chapitre->setTitre('Mon titre');
// ... préparer les données
```

### 2️⃣ ACT (Agir)
```php
$this->manager->validate($chapitre);
```

### 3️⃣ ASSERT (Vérifier)
```php
$this->assertTrue(...);
// ou
$this->expectException(...);
```

---

## 🎨 Schéma Visuel des 8 Tests

```
┌─────────────────────────────────────────────────────────┐
│                    8 TESTS                              │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ✅ Test 1: Chapitre VALIDE                            │
│     → Titre: "Introduction..."                         │
│     → Contenu: "Les bases..."                          │
│     → Ordre: 1                                         │
│     → Résultat: SUCCÈS                                 │
│                                                         │
│  ❌ Test 2: Chapitre SANS titre                        │
│     → Titre: (rien)                                    │
│     → Résultat: ERREUR "titre obligatoire"            │
│                                                         │
│  ❌ Test 3: Chapitre avec titre VIDE                   │
│     → Titre: ""                                        │
│     → Résultat: ERREUR "titre obligatoire"            │
│                                                         │
│  ❌ Test 4: Chapitre SANS contenu                      │
│     → Contenu: (rien)                                  │
│     → Résultat: ERREUR "contenu vide"                 │
│                                                         │
│  ❌ Test 5: Chapitre avec contenu VIDE                 │
│     → Contenu: ""                                      │
│     → Résultat: ERREUR "contenu vide"                 │
│                                                         │
│  ❌ Test 6: Chapitre avec ordre NÉGATIF                │
│     → Ordre: -1                                        │
│     → Résultat: ERREUR "ordre positif"                │
│                                                         │
│  ❌ Test 7: Chapitre avec ordre ZÉRO                   │
│     → Ordre: 0                                         │
│     → Résultat: ERREUR "ordre positif"                │
│                                                         │
│  ❌ Test 8: Chapitre SANS ordre                        │
│     → Ordre: (rien)                                    │
│     → Résultat: ERREUR "ordre positif"                │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

## 📝 Checklist Complète

Cochez au fur et à mesure:

- [ ] 1. Créer `src/Service/ChapitreManager.php`
- [ ] 2. Créer `tests/Service/ChapitreManagerTest.php`
- [ ] 3. Écrire le test 1 (chapitre valide)
- [ ] 4. Écrire le test 2 (sans titre)
- [ ] 5. Écrire le test 3 (titre vide)
- [ ] 6. Écrire le test 4 (sans contenu)
- [ ] 7. Écrire le test 5 (contenu vide)
- [ ] 8. Écrire le test 6 (ordre négatif)
- [ ] 9. Écrire le test 7 (ordre zéro)
- [ ] 10. Écrire le test 8 (sans ordre)
- [ ] 11. Exécuter les tests avec `.\vendor\bin\phpunit`
- [ ] 12. Vérifier que tous les tests passent (8/8)

---

## 🎓 Concepts Importants

### `expectException()`
Indique qu'on s'attend à ce qu'une exception soit levée.

```php
$this->expectException(InvalidArgumentException::class);
```

### `expectExceptionMessage()`
Vérifie le message exact de l'erreur.

```php
$this->expectExceptionMessage('Le titre du chapitre est obligatoire');
```

### `assertTrue()`
Vérifie qu'une valeur est `true`.

```php
$this->assertTrue($this->manager->validate($chapitre));
```

---

## ❓ Questions Fréquentes

### Q: Pourquoi 8 tests?
**R:** Pour couvrir tous les cas possibles:
- 1 cas valide
- 7 cas invalides (3 règles × différentes façons d'être invalide)

### Q: Que faire si un test échoue?
**R:** 
1. Lire le message d'erreur
2. Vérifier le code du service ChapitreManager
3. Vérifier le code du test
4. Corriger et relancer

### Q: Puis-je ajouter d'autres tests?
**R:** Oui! Par exemple:
- Titre trop long
- Contenu trop court
- Ordre trop grand

---

## 🎯 Résumé en 4 Points

1. **Créer le service** qui valide les chapitres
2. **Créer les tests** qui vérifient toutes les situations
3. **Exécuter les tests** avec PHPUnit
4. **Vérifier** que tous les tests passent (8/8)

---

## ✅ Vous avez terminé!

Félicitations! Vous savez maintenant:
- ✅ Créer un service de validation
- ✅ Écrire des tests unitaires
- ✅ Exécuter les tests avec PHPUnit
- ✅ Interpréter les résultats

Vous pouvez présenter ce travail à votre enseignant!
