# ✅ Fix: Erreur getCapaciteMax()

## 🐛 Problème

Erreur lors de la demande "Montre-moi les événements à venir":
```
Call to undefined method App\Entity\Evenement::getCapaciteMax()
```

## 🔍 Cause

Dans `RAGService.php`, ligne 194, le code appelait `$event->getCapaciteMax()` mais l'entité `Evenement` utilise `getNbMax()` au lieu de `getCapaciteMax()`.

## ✅ Solution

Changé dans `src/Service/RAGService.php`:
```php
// Avant (incorrect)
'places_disponibles' => $event->getCapaciteMax() - $event->getParticipations()->count(),

// Après (correct)
'places_disponibles' => $event->getNbMax() - $event->getParticipations()->count(),
```

## 🧪 Test

L'assistant peut maintenant répondre correctement à:
- "Montre-moi les événements à venir"
- "Quels événements cette semaine?"
- "Liste les événements"
- "Show me upcoming events"

## ✅ Statut

Problème résolu! L'assistant IA fonctionne maintenant correctement pour les requêtes d'événements.

---

**Fixé**: 23 Février 2026  
**Fichier modifié**: `src/Service/RAGService.php`  
**Ligne**: 194
