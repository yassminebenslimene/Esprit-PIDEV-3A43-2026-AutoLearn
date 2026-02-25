# ⚡ Après `git pull` - À FAIRE

## 🎯 Tu viens de faire `git pull` ?

Exécute cette commande pour charger les cours dans ta base :

```bash
php bin/console doctrine:fixtures:load --append
```

**C'est tout ! Les 3 cours (Python, Java, Web) sont maintenant dans ta base. ✅**

---

## 🧪 Vérifier

```
http://localhost:8000/
```

Tu devrais voir **3 cours** avec **25 chapitres** au total.

---

## 🐛 Si ça ne marche pas

### Erreur : "Database does not exist"
```bash
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
php bin/console doctrine:fixtures:load --append
```

### Erreur : "Composer dependencies"
```bash
composer install
php bin/console doctrine:fixtures:load --append
```

### MySQL pas démarré
- Ouvre XAMPP Control Panel
- Démarre MySQL

---

## 📚 Plus d'Infos

Voir : `GUIDE_INSTALLATION_COLLEGUES.md`

---

**Bienvenue ! 🚀**
