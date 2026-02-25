# ⚡ CONFIGURATION RAPIDE DES APIs

**Pour l'équipe qui a pullé la branche Amira**

---

## 🚨 IMPORTANT

Les APIs ne fonctionneront PAS tant que tu n'auras pas configuré tes propres clés!

---

## 📋 ÉTAPES RAPIDES (5 minutes)

### 1️⃣ Créer `.env.local`

```bash
copy .env.local.example .env.local
```

### 2️⃣ Obtenir clé SendGrid

1. Crée compte: https://signup.sendgrid.com/
2. Va dans: Settings → API Keys → Create API Key
3. Copie la clé (commence par `SG.`)

### 3️⃣ Obtenir clé OpenWeatherMap

1. Crée compte: https://home.openweathermap.org/users/sign_up
2. Va dans: API keys
3. Copie la clé (32 caractères)

### 4️⃣ Configurer `.env.local`

Ouvre `.env.local` et remplace:

```env
MAILER_DSN=sendgrid+api://SG.ta_cle_sendgrid@default
WEATHER_API_KEY=ta_cle_openweathermap
```

### 5️⃣ Redémarrer le serveur

```bash
# Arrête (Ctrl+C) puis redémarre
symfony server:start
```

---

## ✅ Tester

- **Météo:** Va sur http://localhost:8000/events
- **Email:** Participe à un événement

---

## 📖 Guide Complet

Pour plus de détails, voir: **GUIDE_CONFIGURATION_API_EQUIPE.md**

---

## 🆘 Problèmes?

1. Vérifie que `.env.local` existe
2. Vérifie que les clés sont correctes
3. Attends 15 min après création compte OpenWeatherMap
4. Contacte Amira

---

**C'est tout!** Les APIs devraient maintenant fonctionner. 🎉
