# 🚨 Guide de Résolution - Erreur 503 sur Railway

## Problème identifié
Le site se déploie mais retourne une erreur 503 lors de la connexion car MySQL n'est pas correctement lié.

## Solution étape par étape

### 1. Vérifier la liaison MySQL dans Railway

1. **Allez dans votre Dashboard Railway**
2. **Cliquez sur votre Web Service**
3. **Onglet "Variables"**
4. **Cliquez sur "Add Reference Variable"**
5. **Sélectionnez votre service MySQL**

Railway va automatiquement ajouter ces variables :
- `MYSQL_URL`
- `MYSQLHOST`
- `MYSQLPORT`
- `MYSQLUSER`
- `MYSQLPASSWORD`
- `MYSQLDATABASE`

### 2. Vérifier la connexion

Accédez à : `https://ias-b3-g7-paris.up.railway.app/debug-railway.php`

Cela affichera :
- Les variables MySQL disponibles
- Le statut de connexion
- Les erreurs éventuelles

### 3. Initialiser la base de données

#### Option A : Via le navigateur
Accédez à : `https://ias-b3-g7-paris.up.railway.app/init-db-simple.php`

#### Option B : Via Railway Shell
```bash
railway run php init-db-simple.php
```

### 4. Tester la connexion

Utilisez ces identifiants de test :
- **Email :** test@culture-radar.fr
- **Mot de passe :** password123

Ou :
- **Email :** demo@culture-radar.fr
- **Mot de passe :** demo123

## Fichiers de debug disponibles

1. **`/debug-railway.php`** - Affiche toutes les variables et teste la connexion
2. **`/test-railway-connection.php`** - Interface visuelle de test
3. **`/health.php`** - Endpoint de health check
4. **`/init-db-simple.php`** - Initialise la base de données

## Checklist de déploiement

- [ ] MySQL créé sur Railway
- [ ] Services liés (Web ↔ MySQL)
- [ ] Variables MySQL visibles dans le Web Service
- [ ] Base de données initialisée
- [ ] Tables créées
- [ ] Utilisateurs de test créés
- [ ] Login fonctionnel

## Variables d'environnement supplémentaires

Ajoutez ces variables dans Railway si nécessaire :

```
SERP_API_KEY=b56aa6ec92f9f569f50f671e5133d46d5131c74c260086c37f5222bf489f2d4d
OPENAGENDA_API_KEY=votre_cle
PARIS_OPEN_DATA_KEY=votre_cle
```

## Si le problème persiste

1. **Vérifiez les logs Railway :**
   - Dashboard → Service → Logs

2. **Redéployez :**
   ```bash
   git add .
   git commit -m "Fix MySQL connection"
   git push
   ```

3. **Recréez le service MySQL :**
   - Supprimez l'ancien MySQL
   - Créez un nouveau MySQL
   - Reliez-le au Web Service

## Contact Support

Si vous avez encore des problèmes :
- Railway Support : https://help.railway.app
- Railway Discord : https://discord.gg/railway

---

Dernière mise à jour : 18/08/2025