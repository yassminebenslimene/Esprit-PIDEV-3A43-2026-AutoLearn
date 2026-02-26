# 🚀 AI Assistant Quick Reference

## Admin Commands

### User Management
```
créer étudiant Jean Dupont jean@test.com
modifier email étudiant test à nouveau@test.com
suspendre compte étudiant test
réactiver utilisateur test
chercher étudiants niveau débutant
utilisateurs inactifs depuis 7 jours
```

### Course Management
```
créer cours Python pour débutants
modifier cours 5 titre "Python Avancé"
ajouter chapitre au cours 5 titre "Variables"
montre-moi tous les cours
détails du cours 5
```

### Event Management
```
créer événement workshop Python le 2026-03-10 à 14h salle A
modifier événement 3 capacité 50
liste tous les événements
détails de l'événement 3
```

### Challenge Management
```
créer challenge algorithmes difficile 200 points
modifier challenge 2 difficulté facile
liste tous les challenges
détails du challenge 2
```

### Community Management
```
créer communauté développeurs Python
modifier communauté 1 nom "Python Experts"
liste toutes les communautés
détails de la communauté 1
```

### Quiz Management
```
créer quiz pour le cours 5 titre "Test Python"
détails du quiz 3
```

---

## Student Commands

### Course Discovery
```
quels cours pour débuter en Python?
montre-moi les cours disponibles
cours de niveau intermédiaire
détails du cours Python
mes cours inscrits
```

### Event Discovery
```
événements à venir
événements cette semaine
détails de l'événement workshop
mes événements inscrits
```

### Challenge Discovery
```
challenges disponibles
challenges faciles
challenges pour débutants
détails du challenge algorithmes
mes challenges complétés
```

### Community Discovery
```
communautés disponibles
communautés Python
détails de la communauté développeurs
mes communautés rejointes
```

### Progress Tracking
```
mes progrès d'apprentissage
mes statistiques
cours complétés
challenges réussis
mon classement
```

---

## Response Format

### Admin Actions
```
User: "créer cours Python"
AI: {"action": "create_course", "data": {...}}
    ✅ Cours créé
```

### Student Queries
```
User: "quels cours disponibles?"
AI: 📚 5 cours disponibles:
    - Python Basics (DEBUTANT)
    - Java Advanced (AVANCE)
    ...
```

---

## Error Messages

```
❌ Utilisateur introuvable
❌ Email déjà utilisé
❌ Cours introuvable
❌ Événement introuvable
❌ Permission refusée
❌ Champ requis: titre
```

---

## Tips

1. **Be Natural:** The AI understands natural language
2. **Be Concise:** Responses are ultra-short (3-5 words)
3. **Use French or English:** Both languages supported
4. **Check Data:** AI uses ONLY real database data
5. **Admin vs Student:** Different capabilities per role

---

## Available Actions

### Admin (21 actions)
- User: create, update, suspend, unsuspend, filter, get
- Course: create, update, get, list, add_chapter
- Event: create, update, get, list
- Challenge: create, update, get, list
- Community: create, update, get, list
- Quiz: create, get

### Student (8 actions)
- View: courses, events, challenges, communities, quizzes
- Enroll: in courses
- Join: communities
- Create: teams

---

**Need Help?** Check `AI_ASSISTANT_EXPANSION_COMPLETE.md` for full documentation.
