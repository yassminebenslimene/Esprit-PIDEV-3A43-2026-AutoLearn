# 🔧 Résolution : Problème de Cache sur Windows

## ❌ Le Problème

### Erreur rencontrée
```
Unable to write in the "cache" directory 
(C:\Users\yassm\OneDrive\Desktop\PI - Copie (2)\autolearn/var/cache/dev)
```

ou

```
Unable to write in the "logs" directory 
(C:\Users\yassm\OneDrive\Desktop\PI - Copie (2)\autolearn/var/log)
```

### Pourquoi cette erreur ?

Sur Windows, surtout avec **OneDrive**, les permissions d'écriture peuvent être bloquées pour les dossiers `var/cache` et `var/log`.

**Causes possibles** :
1. OneDrive synchronise le dossier et bloque l'accès
2. Permissions Windows insuffisantes
3. Cache corrompu ou verrouillé
4. Processus PHP en cours qui bloque les fichiers

---

## ✅ Solution Rapide (Commandes PowerShell)

### Étape 1 : Supprimer les dossiers cache et log

```powershell
Remove-Item -Recurse -Force var/log -ErrorAction SilentlyContinue
Remove-Item -Recurse -Force var/cache -ErrorAction SilentlyContinue
```

### Étape 2 : Recréer les dossiers

```powershell
New-Item -ItemType Directory -Force -Path var/cache, var/log
```

### Étape 3 : Donner les permissions complètes

```powershell
icacls var /grant yassm:F /T
```

**Note** : Remplacez `yassm` par votre nom d'utilisateur Windows.

Pour connaître votre nom d'utilisateur :
```powershell
$env:USERNAME
```

### Étape 4 : Vider le cache Symfony

```bash
php bin/console cache:clear
```

---

## 🔄 Solution Alternative (Si la première ne fonctionne pas)

### Méthode 1 : Utiliser cache:warmup

```bash
php bin/console cache:warmup
```

### Méthode 2 : Supprimer manuellement

1. Fermez tous les processus PHP (serveur Symfony, etc.)
2. Ouvrez l'Explorateur Windows
3. Naviguez vers `var/cache` et `var/log`
4. Supprimez les dossiers manuellement
5. Relancez `php bin/console cache:clear`

### Méthode 3 : Désactiver OneDrive temporairement

Si votre projet est dans OneDrive :

1. Clic droit sur l'icône OneDrive dans la barre des tâches
2. Paramètres → Compte → Choisir les dossiers
3. Décochez le dossier du projet
4. Attendez la fin de la synchronisation
5. Relancez les commandes

---

## 🚀 Solution Permanente

### Option 1 : Déplacer le projet hors de OneDrive

**Recommandé** : Déplacez votre projet dans un dossier local non synchronisé.

```
❌ Mauvais : C:\Users\yassm\OneDrive\Desktop\PI - Copie (2)\autolearn
✅ Bon : C:\xampp\htdocs\autolearn
✅ Bon : C:\projets\autolearn
✅ Bon : D:\dev\autolearn
```

**Avantages** :
- Pas de conflit avec OneDrive
- Meilleures performances
- Moins de problèmes de permissions

### Option 2 : Exclure var/ de OneDrive

Si vous devez garder le projet dans OneDrive :

1. Créez un fichier `.gitignore` (déjà fait normalement)
2. Ajoutez dans OneDrive les exclusions :
   - Paramètres OneDrive → Sauvegarde → Gérer la sauvegarde
   - Excluez le dossier `var/`

### Option 3 : Modifier les permissions Windows

Donnez les permissions complètes au dossier `var/` :

```powershell
# Permissions complètes pour votre utilisateur
icacls var /grant %USERNAME%:F /T

# Ou permissions pour tous les utilisateurs
icacls var /grant *S-1-1-0:F /T
```

---

## 📝 Script PowerShell Automatique

Créez un fichier `fix-cache.ps1` à la racine du projet :

```powershell
# fix-cache.ps1
Write-Host "🔧 Nettoyage du cache Symfony..." -ForegroundColor Cyan

# Supprimer les dossiers
Remove-Item -Recurse -Force var/log -ErrorAction SilentlyContinue
Remove-Item -Recurse -Force var/cache -ErrorAction SilentlyContinue

# Recréer les dossiers
New-Item -ItemType Directory -Force -Path var/cache, var/log | Out-Null

# Donner les permissions
icacls var /grant "$env:USERNAME`:F" /T | Out-Null

# Vider le cache
php bin/console cache:clear

Write-Host "✅ Cache nettoyé avec succès!" -ForegroundColor Green
```

**Utilisation** :
```powershell
.\fix-cache.ps1
```

---

## 🛠️ Commandes Utiles

### Vérifier les permissions

```powershell
icacls var
```

### Voir les processus PHP en cours

```powershell
Get-Process php
```

### Tuer tous les processus PHP

```powershell
Get-Process php | Stop-Process -Force
```

### Vérifier l'espace disque

```powershell
Get-PSDrive C
```

---

## ⚠️ Problèmes Courants

### Problème 1 : "Accès refusé"

**Solution** : Exécutez PowerShell en tant qu'administrateur
- Clic droit sur PowerShell → "Exécuter en tant qu'administrateur"

### Problème 2 : "Le fichier est utilisé par un autre processus"

**Solution** : Fermez tous les processus PHP
```powershell
Get-Process php | Stop-Process -Force
```

### Problème 3 : OneDrive synchronise en continu

**Solution** : Mettez le projet en pause dans OneDrive
- Clic droit sur OneDrive → Suspendre la synchronisation → 2 heures

### Problème 4 : Cache se remplit rapidement

**Solution** : Videz régulièrement le cache
```bash
# Vider le cache
php bin/console cache:clear

# Vider le cache de production
php bin/console cache:clear --env=prod
```

---

## 📊 Vérification Post-Résolution

Après avoir appliqué la solution, vérifiez :

```bash
# 1. Vider le cache
php bin/console cache:clear

# 2. Démarrer le serveur
symfony serve

# Ou avec PHP
php -S localhost:8000 -t public
```

Si le serveur démarre sans erreur, le problème est résolu! ✅

---

## 🔍 Diagnostic Avancé

### Vérifier les permissions détaillées

```powershell
Get-Acl var | Format-List
```

### Voir la taille du cache

```powershell
Get-ChildItem var/cache -Recurse | Measure-Object -Property Length -Sum
```

### Lister les fichiers verrouillés

```powershell
Get-ChildItem var -Recurse | Where-Object {$_.IsReadOnly}
```

---

## 💡 Bonnes Pratiques

1. **Ne jamais** mettre un projet Symfony dans OneDrive/Dropbox/Google Drive
2. **Toujours** ajouter `var/` dans `.gitignore`
3. **Régulièrement** vider le cache en développement
4. **Utiliser** `symfony serve` plutôt que `php -S` (meilleure gestion du cache)
5. **Fermer** les processus PHP avant de supprimer le cache

---

## 📚 Commandes de Maintenance

### Nettoyage complet

```bash
# Supprimer le cache
php bin/console cache:clear

# Supprimer les logs
Remove-Item var/log/*.log

# Optimiser l'autoloader
composer dump-autoload --optimize
```

### Vérification de l'environnement

```bash
# Vérifier la configuration Symfony
php bin/console about

# Vérifier les services
php bin/console debug:container

# Vérifier les routes
php bin/console debug:router
```

---

## ✅ Checklist de Résolution

- [ ] Fermer tous les processus PHP
- [ ] Supprimer `var/cache` et `var/log`
- [ ] Recréer les dossiers
- [ ] Donner les permissions complètes
- [ ] Vider le cache Symfony
- [ ] Démarrer le serveur
- [ ] Vérifier que tout fonctionne

---

**Date**: 14 février 2026  
**Version**: 1.0  
**Système**: Windows 11 avec OneDrive
