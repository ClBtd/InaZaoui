# Ina Zaoui Photography

## 📷 Présentation

Ce site web présente les œuvres et le portfolio de la photographe Ina Zaoui. La plateforme offre une interface d'administration pour la gestion des albums et des accès invités, ainsi qu'un espace public permettant aux visiteurs de découvrir l'ensemble des œuvres photographiques.

## Fonctionnalités

- **Galerie photographique** : Présentation des œuvres par catégories et collections
- **Espace administrateur** : Gestion complète du contenu et des albums
- **Espace invités** : Accès sécurisé pour les clients à leurs albums privés

## Prérequis techniques

Avant d'installer le projet, assurez-vous d'avoir les éléments suivants sur votre environnement de développement :

- PHP 8.0 ou supérieur
- Composer (gestionnaire de dépendances PHP)
- Postgres ou un autre SGBD compatible
- Symfony CLI (optionnel, mais recommandé pour le développement)
- PHPUnit et Xdebug (pour les tests et la couverture de code)

## 🚀 Installation

### 1. Récupération du projet

```bash
# Cloner le dépôt Git
git clone https://github.com/clbtd/ina-zaoui.git

# Accéder au répertoire du projet
cd ina-zaoui
```

### 2. Installation des dépendances

```bash
# Installer les dépendances PHP
composer install

# Installer les dépendances front-end
npm install

# Compiler les assets (si nécessaire)
npm run build
```

### 3. Configuration de la base de données

Créez un fichier `.env.local` à la racine du projet et configurez vos variables d'environnement :

```
DATABASE_URL="mysql://user:password@127.0.0.1:3306/ina_zaoui_db?serverVersion=8.0"
```

Puis exécutez les commandes suivantes :

```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Charger les fixtures (données de démonstration)
php bin/console doctrine:fixtures:load
```

### 4. Démarrage du serveur

```bash
# Avec Symfony CLI
symfony serve

# Alternative avec le serveur PHP intégré
php -S localhost:8000 -t public/
```

## 🖥️ Utilisation

Une fois le serveur démarré, accédez à l'application via :

- 🌐 **Interface publique** : [http://localhost:8000](http://localhost:8000)
- ⚙️ **Administration** : [http://localhost:8000/admin](http://localhost:8000/admin)
  - Identifiants par défaut (si fixtures chargées) :
  - Email : admin@example.com
  - Mot de passe : password

## 🧪 Tests et qualité

### Configuration de l'environnement de test

```bash
# Créer la base de données de test
php bin/console doctrine:database:create --env=test

# Exécuter les migrations dans l'environnement de test
php bin/console doctrine:migrations:migrate --env=test

# Charger les fixtures de test
php bin/console doctrine:fixtures:load --env=test
```

### Exécution des tests

```bash
# Lancer tous les tests
./vendor/bin/phpunit

# Générer un rapport de couverture HTML
./vendor/bin/phpunit --coverage-html var/coverage
```
Le rapport de couverture sera disponible dans le dossier `var/coverage`.
