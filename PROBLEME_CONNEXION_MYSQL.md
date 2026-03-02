# 🔌 PROBLÈME DE CONNEXION MYSQL

## ❌ Erreur
```
SQLSTATE[HY000] [2002] Aucune connexion n'a pu être établie car l'ordinateur cible l'a expressément refusée
```

## 🎯 CAUSE
**MySQL n'est pas démarré !**

---

## ✅ SOLUTION RAPIDE

### Script automatique :
```bash
.\DEMARRER_MYSQL.bat
```

---

## 📋 SOLUTIONS MANUELLES

### 1️⃣ Si tu utilises XAMPP :

1. Ouvrir **XAMPP Control Panel**
2. Cliquer sur **"Start"** à côté de **MySQL**
3. Attendre que le statut devienne vert
4. Vérifier que le port est **3306**

### 2️⃣ Si tu utilises WAMP :

1. Ouvrir **WAMP**
2. Cliquer sur l'icône WAMP dans la barre des tâches
3. Cliquer sur **"Start All Services"**
4. Attendre que l'icône devienne verte

### 3️⃣ Via Services Windows :

1. Appuyer sur **Win + R**
2. Taper : `services.msc`
3. Chercher **"MySQL"** ou **"MySQL80"**
4. Clic droit → **Démarrer**

### 4️⃣ Via ligne de commande :

```bash
# MySQL standard
net start MySQL

# MySQL 8.0
net start MySQL80

# Via XAMPP
C:\xampp\mysql_start.bat
```

---

## 🔍 VÉRIFIER QUE MYSQL EST DÉMARRÉ

### Méthode 1 : XAMPP Control Panel
- MySQL doit avoir un fond **vert**
- Port doit afficher **3306**

### Méthode 2 : Ligne de commande
```bash
# Tester la connexion
mysql -u root -p

# Ou via PHP
php -r "new PDO('mysql:host=127.0.0.1', 'root', '');"
```

### Méthode 3 : phpMyAdmin
Ouvrir : http://localhost/phpmyadmin

Si ça s'ouvre, MySQL fonctionne ! ✅

---

## ⚙️ CONFIGURATION DANS .env

Vérifie que ta configuration est correcte :

```env
# .env
DATABASE_URL="mysql://root:@127.0.0.1:3306/autolearn_db?serverVersion=8.0.32&charset=utf8mb4"
```

**Paramètres :**
- **Utilisateur :** root
- **Mot de passe :** (vide par défaut avec XAMPP)
- **Host :** 127.0.0.1
- **Port :** 3306
- **Base :** autolearn_db

---

## 🚨 PROBLÈMES COURANTS

### Problème 1 : Port 3306 déjà utilisé

**Symptôme :** MySQL ne démarre pas, erreur "Port already in use"

**Solution :**
1. Ouvrir XAMPP Config (bouton Config à côté de MySQL)
2. Changer le port (ex: 3307)
3. Mettre à jour `.env` :
```env
DATABASE_URL="mysql://root:@127.0.0.1:3307/autolearn_db?..."
```

### Problème 2 : MySQL ne démarre pas du tout

**Solutions :**
1. Vérifier les logs : `C:\xampp\mysql\data\mysql_error.log`
2. Réinstaller MySQL via XAMPP
3. Vérifier qu'aucun autre MySQL n'est installé

### Problème 3 : Mot de passe incorrect

**Symptôme :** "Access denied for user 'root'@'localhost'"

**Solution :**
1. Réinitialiser le mot de passe MySQL
2. Ou mettre à jour `.env` avec le bon mot de passe :
```env
DATABASE_URL="mysql://root:ton_mot_de_passe@127.0.0.1:3306/..."
```

---

## 🔄 WORKFLOW COMPLET

```bash
# 1. Démarrer MySQL
.\DEMARRER_MYSQL.bat

# 2. Vérifier la connexion
php -r "new PDO('mysql:host=127.0.0.1', 'root', '');"

# 3. Créer la base si elle n'existe pas
php bin/console doctrine:database:create

# 4. Mettre à jour le schéma
php bin/console doctrine:schema:update --force

# 5. Vider le cache
php bin/console cache:clear

# 6. Démarrer le serveur
symfony serve
```

---

## 📊 CHECKLIST DE VÉRIFICATION

- [ ] XAMPP Control Panel ouvert
- [ ] MySQL démarré (fond vert)
- [ ] Port 3306 affiché
- [ ] phpMyAdmin accessible (http://localhost/phpmyadmin)
- [ ] Base `autolearn_db` existe
- [ ] `.env` correctement configuré
- [ ] Connexion testée avec succès

---

## 🎯 APRÈS LE DÉMARRAGE DE MYSQL

```bash
# Mettre à jour la base de données
php bin/console doctrine:schema:update --force

# Vider le cache
php bin/console cache:clear

# Démarrer l'application
symfony serve
```

Ouvrir : http://127.0.0.1:8000

---

## 💡 ASTUCE

Pour que MySQL démarre automatiquement au démarrage de Windows :

1. Ouvrir **services.msc**
2. Chercher **MySQL** ou **MySQL80**
3. Clic droit → **Propriétés**
4. Type de démarrage : **Automatique**
5. Cliquer **OK**

---

## 📞 SI ÇA NE FONCTIONNE TOUJOURS PAS

1. Redémarrer l'ordinateur
2. Réinstaller XAMPP
3. Vérifier les logs : `C:\xampp\mysql\data\mysql_error.log`
4. Vérifier qu'aucun antivirus ne bloque MySQL

---

**MySQL doit être démarré AVANT de lancer Symfony !** ⚠️
