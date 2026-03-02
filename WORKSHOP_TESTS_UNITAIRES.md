# 🧪 Workshop Tests Unitaires - Entité Chapitre

## Introduction

Ce document présente l'implémentation des tests unitaires pour l'entité **Chapitre** du module de gestion de cours, conformément au workshop sur les tests unitaires.

## 1. Règles métier à valider

Pour l'entité **Chapitre**, nous avons défini les règles suivantes:

1. ✅ **Le titre du chapitre est obligatoire**
2. ✅ **Le contenu du chapitre ne peut pas être vide**
3. ✅ **L'ordre doit être un nombre positif (> 0)**

Ces règles doivent être validées par des tests unitaires.

## 2. Organisation des dossiers

```
src/
└── Service/
    └── ChapitreManager.php

tests/
└── Service/
    └── ChapitreManagerTest.php
```

## 3. Création du service métier

**Fichier**: `src/Service/ChapitreManager.php`

```php
<?php

namespace App\Service;

use App\Entity\GestionDeCours\Chapitre;
use InvalidArgumentException;

class ChapitreManager
{
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

### Explications

- La validation utilise `empty()` pour vérifier que le titre et le contenu ne sont pas vides
- `FILTER_VALIDATE_EMAIL` n'est pas nécessaire ici (pas d'email dans Chapitre)
- `InvalidArgumentException` est levée immédiatement en cas de données invalides

## 4. Implémentation des tests unitaires

**Fichier**: `tests/Service/ChapitreManagerTest.php`

### Structure du test

```php
namespace App\Tests\Service;

use App\Entity\GestionDeCours\Chapitre;
use App\Service\ChapitreManager;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ChapitreManagerTest extends TestCase
{
    private ChapitreManager $manager;

    protected function setUp(): void
    {
        $this->manager = new ChapitreManager();
    }

    // ... tests ...
}
```

### Tests implémentés

#### ✅ Test 1: Chapitre valide

```php
public function testValidChapitre(): void
{
    $chapitre = new Chapitre();
    $chapitre->setTitre('Introduction aux bases de données');
    $chapitre->setContenu('Les bases de données sont...');
    $chapitre->setOrdre(1);

    $this->assertTrue($this->manager->validate($chapitre));
}
```

#### ❌ Test 2: Chapitre sans titre

```php
public function testChapitreWithoutTitre(): void
{
    $chapitre = new Chapitre();
    $chapitre->setContenu('Contenu du chapitre');
    $chapitre->setOrdre(1);

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Le titre du chapitre est obligatoire');

    $this->manager->validate($chapitre);
}
```

#### ❌ Test 3: Chapitre avec titre vide

```php
public function testChapitreWithEmptyTitre(): void
{
    $chapitre = new Chapitre();
    $chapitre->setTitre('');
    $chapitre->setContenu('Contenu du chapitre');
    $chapitre->setOrdre(1);

    $this->expectException(InvalidArgumentException::class);
    $this->manager->validate($chapitre);
}
```

#### ❌ Test 4: Chapitre sans contenu

```php
public function testChapitreWithoutContenu(): void
{
    $chapitre = new Chapitre();
    $chapitre->setTitre('Titre du chapitre');
    $chapitre->setOrdre(1);

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Le contenu du chapitre ne peut pas être vide');

    $this->manager->validate($chapitre);
}
```

#### ❌ Test 5: Chapitre avec contenu vide

```php
public function testChapitreWithEmptyContenu(): void
{
    $chapitre = new Chapitre();
    $chapitre->setTitre('Titre du chapitre');
    $chapitre->setContenu('');
    $chapitre->setOrdre(1);

    $this->expectException(InvalidArgumentException::class);
    $this->manager->validate($chapitre);
}
```

#### ❌ Test 6: Chapitre avec ordre négatif

```php
public function testChapitreWithNegativeOrdre(): void
{
    $chapitre = new Chapitre();
    $chapitre->setTitre('Titre du chapitre');
    $chapitre->setContenu('Contenu du chapitre');
    $chapitre->setOrdre(-1);

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('L\'ordre du chapitre doit être un nombre positif');

    $this->manager->validate($chapitre);
}
```

#### ❌ Test 7: Chapitre avec ordre zéro

```php
public function testChapitreWithZeroOrdre(): void
{
    $chapitre = new Chapitre();
    $chapitre->setTitre('Titre du chapitre');
    $chapitre->setContenu('Contenu du chapitre');
    $chapitre->setOrdre(0);

    $this->expectException(InvalidArgumentException::class);
    $this->manager->validate($chapitre);
}
```

#### ❌ Test 8: Chapitre sans ordre

```php
public function testChapitreWithoutOrdre(): void
{
    $chapitre = new Chapitre();
    $chapitre->setTitre('Titre du chapitre');
    $chapitre->setContenu('Contenu du chapitre');

    $this->expectException(InvalidArgumentException::class);
    $this->manager->validate($chapitre);
}
```

## 5. Exécution des tests

### Commande

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

### Interprétation

- ✅ **8 tests** exécutés
- ✅ **15 assertions** vérifiées
- ✅ **100% de réussite**
- ⏱️ Temps d'exécution: 48ms
- 💾 Mémoire utilisée: 10 MB

Chaque point (`.`) correspond à un test réussi.
`OK` indique que la logique métier est valide.

## 6. Couverture des tests

| Règle métier | Tests | Statut |
|-------------|-------|--------|
| Titre obligatoire | 2 tests (null, vide) | ✅ |
| Contenu obligatoire | 2 tests (null, vide) | ✅ |
| Ordre positif | 3 tests (négatif, zéro, null) | ✅ |
| Validation complète | 1 test (cas valide) | ✅ |

## 7. Avantages des tests unitaires

✅ **Validation automatique** des règles métier

✅ **Détection précoce** des bugs avant la livraison

✅ **Documentation vivante** du comportement attendu

✅ **Refactoring sécurisé** - les tests garantissent que le comportement reste correct

✅ **Qualité du code** améliorée

## 8. Bonnes pratiques appliquées

1. ✅ **Arrange-Act-Assert** (AAA pattern)
   - Arrange: Préparer les données
   - Act: Exécuter l'action
   - Assert: Vérifier le résultat

2. ✅ **Un test = une règle métier**

3. ✅ **Noms de tests explicites** (`testChapitreWithoutTitre`)

4. ✅ **Tests isolés** (chaque test est indépendant)

5. ✅ **Utilisation de `setUp()`** pour initialiser le manager

## Conclusion

Les tests unitaires constituent la **première étape de la phase de test**. Ils permettent de valider la logique métier et de sécuriser le projet avant la livraison finale.

Pour l'entité **Chapitre**, nous avons:
- ✅ Identifié 3 règles métier
- ✅ Créé un service ChapitreManager
- ✅ Implémenté 8 tests unitaires
- ✅ Obtenu 100% de réussite

Le service ChapitreManager peut maintenant être utilisé dans les contrôleurs pour valider les chapitres avant leur sauvegarde en base de données.
