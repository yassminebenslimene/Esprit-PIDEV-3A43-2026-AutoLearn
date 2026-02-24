# 🔄 Automatisation des Rappels d'Inactivité

## ✅ Ce Qui Fonctionne Déjà

Quand tu exécutes:
```cmd
php bin/console app:send-inactivity-reminders
```

Le système:
1. ✅ Détecte les étudiants inactifs (3+ jours)
2. ✅ Crée des notifications internes automatiquement
3. ✅ Affiche le badge rouge avec le nombre
4. ✅ Les étudiants voient les notifications dans leur interface

## 🎯 Rendre le Système Automatique

Pour que la commande s'exécute **automatiquement tous les jours** sans intervention manuelle:

### Option 1: Planificateur de Tâches Windows (Recommandé)

**Étape 1:** Exécute le fichier `setup_cron_windows.bat` en tant qu'administrateur
- Clique droit sur le fichier
- "Exécuter en tant qu'administrateur"

**Étape 2:** La tâche sera créée et s'exécutera automatiquement tous les jours à 9h00

**Étape 3:** Pour vérifier
- Ouvre "Planificateur de tâches" Windows
- Cherche "AutoLearn_Rappel_Inactivite"
- Tu verras la tâche configurée

### Option 2: Configuration Manuelle

1. Ouvre "Planificateur de tâches" (Task Scheduler)
2. Clique sur "Créer une tâche de base"
3. Nom: "AutoLearn Rappel Inactivité"
4. Déclencheur: "Quotidien" à 9h00
5. Action: "Démarrer un programme"
6. Programme: `php`
7. Arguments: `C:\chemin\vers\autolearn\bin\console app:send-inactivity-reminders`
8. Répertoire: `C:\chemin\vers\autolearn`

### Option 3: Serveur de Production (Linux)

Si tu déploies sur un serveur Linux, ajoute dans le crontab:

```bash
# Exécuter tous les jours à 9h00
0 9 * * * cd /var/www/autolearn && php bin/console app:send-inactivity-reminders
```

## 📊 Fréquence Recommandée

**Quotidien à 9h00** - Les étudiants reçoivent leur notification le matin

Tu peux aussi configurer:
- **Deux fois par jour**: 9h00 et 18h00
- **Tous les 2 jours**: Pour moins de notifications
- **Personnalisé**: Selon tes besoins

## 🧪 Test Immédiat

Pour tester que tout fonctionne:

```cmd
php bin/console app:send-inactivity-reminders
```

Puis connecte-toi en tant qu'étudiant inactif et vérifie:
1. Le badge rouge apparaît avec le nombre
2. Les notifications sont dans la liste
3. Tu peux les marquer comme lues

## ✅ Résumé

**Actuellement:**
- ✅ Notifications internes: Fonctionnent
- ✅ Détection d'inactivité: Fonctionne
- ✅ Badge + Liste: Fonctionnent
- ⏳ Automatisation: À configurer (fichier .bat fourni)

**Après configuration:**
- ✅ Tout s'exécute automatiquement chaque jour
- ✅ Les étudiants reçoivent leurs notifications
- ✅ Aucune intervention manuelle nécessaire

## 🎉 Conclusion

Ton système de rappel d'inactivité est **complet et fonctionnel**. Il suffit de configurer la tâche planifiée pour qu'il devienne entièrement automatique!
