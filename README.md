# Dar Anawat – Site Web de Riad

## 1. Présentation du projet

Dar Anawat est un site web destiné à présenter un riad et ses chambres aux visiteurs. L’objectif est de proposer une interface simple, élégante et facile à utiliser, permettant aux utilisateurs de découvrir le riad, consulter les chambres disponibles et accéder aux détails de chaque chambre.

Ce projet est développé avec Laravel et Blade, en utilisant HTML, CSS et JavaScript pour la structure et la présentation des pages.

## 2. Technologies utilisées

- **Laravel :** framework PHP utilisé pour organiser le projet et gérer les routes.
- **PHP :** langage utilisé pour la partie serveur.
- **Blade :** moteur de templates Laravel pour créer les pages dynamiques.
- **HTML5 :** structure des pages web.
- **CSS3 :** mise en forme et design de l’interface.
- **JavaScript :** ajout éventuel d’interactions côté client.
- **Git et GitHub :** gestion des versions et partage du code source.

## 3. Page d’accueil (Home Page)

### Objectif
La page d’accueil est la première page visible par les visiteurs. Elle permet de présenter le riad et de donner une première impression de son identité et de son ambiance.

### Travail réalisé
- Création de la structure générale de la page d’accueil.
- Organisation des différentes sections pour faciliter la navigation.
- Mise en place d’une interface visuelle adaptée à un site de riad.
- Intégration de la barre de navigation (Navbar).
- Intégration du pied de page (Footer).
- Préparation de la navigation vers les autres pages du site.
- Utilisation de données fictives lorsque cela est nécessaire pour la présentation.

### Résultat attendu
Une page d’accueil claire et accueillante qui permet aux visiteurs de découvrir le riad et d’accéder facilement à la page des chambres.

## 4. Page Chambres

### Objectif
La page Chambres permet aux visiteurs de consulter les différentes chambres proposées par le riad.

### Travail réalisé
- Création de la vue `chambres.blade.php`.
- Organisation des chambres sous forme de cartes.
- Réutilisation du composant Blade `components/room-card.blade.php` pour afficher les cartes de chambres.
- Présentation des informations principales de chaque chambre selon les données disponibles.
- Mise en place d’un accès à la page de détail depuis chaque carte.
- Utilisation de données fictives pour faciliter le développement et la présentation de l’interface.
- Intégration de la navigation commune avec la Navbar et le Footer.

### Résultat attendu
Une page qui permet aux visiteurs de parcourir les chambres et de sélectionner celle qu’ils souhaitent découvrir plus en détail.

## 5. Page Détail d’une chambre

### Objectif
La page Détail permet de consulter les informations relatives à une chambre sélectionnée depuis la page Chambres.

### Travail réalisé
- Création de la vue `chambre-details.blade.php`.
- Préparation d’une page dédiée à la présentation détaillée d’une chambre.
- Organisation des informations pour faciliter la lecture.
- Préparation de la navigation entre la liste des chambres et leurs pages de détail.
- Mise en place de la route permettant d’accéder au détail d’une chambre, selon son type.
- Réutilisation des éléments communs du site pour conserver une présentation cohérente.

### Résultat attendu
Une page permettant au visiteur d’obtenir davantage d’informations sur une chambre avant de poursuivre éventuellement vers une réservation.

## 6. Organisation des fichiers

Les principaux fichiers concernés par le développement sont :

```text
app/
└── Http/
    └── Controllers/
        └── Controller.php

resources/
└── views/
    ├── components/
    │   ├── navbar.blade.php
    │   ├── footer.blade.php
    │   └── room-card.blade.php
    ├── layouts/
    │   └── app.blade.php
    ├── chambres.blade.php
    └── chambre-details.blade.php

routes/
└── web.php

tests/
└── Feature/
    └── ChambresTest.php
```

## 7. Gestion des routes

Le fichier `routes/web.php` est utilisé pour définir les routes permettant d’accéder aux différentes pages du site.

Les routes concernées comprennent notamment :
- La page d’accueil.
- La page listant les chambres.
- La page affichant les détails d’une chambre à partir de son type.

Les URL exactes et les noms des routes dépendent de leur définition dans le fichier `web.php`.

## 8. Composants et layout communs

### Navbar
Le composant `navbar.blade.php` contient la barre de navigation commune, qui facilite l’accès aux différentes sections du site.

### Footer
Le composant `footer.blade.php` contient le pied de page et les informations communes affichées en bas des pages.

### Layout principal
Le fichier `layouts/app.blade.php` permet de définir une structure commune aux pages qui l’utilisent, afin de conserver une présentation homogène et de limiter la répétition du code.

### Carte de chambre
Le composant `room-card.blade.php` permet de réutiliser la même structure visuelle pour présenter les différentes chambres sur les pages du site.

## 9. Tests

Les tests fonctionnels couvrent les pages de chambres et les demandes de réservation. Ils vérifient les champs, les dates, la capacité et l’enregistrement des demandes en attente.

Pour exécuter tous les tests :

```bash
php artisan test
```

## 10. État d’avancement

Le projet comprend désormais :

- Page d’accueil.
- Page Chambres.
- Page Détail d’une chambre.
- Formulaire de demande de réservation relié à Laravel.
- Enregistrement en base des demandes en attente de vérification.
- Validation serveur des coordonnées, des dates et de la capacité des chambres.
- Interface de réservation traduite en français, anglais, espagnol et arabe.
- Composants Navbar, Footer et carte de chambre.

Les demandes ne confirment pas la disponibilité : le projet ne contient pas encore d’inventaire des chambres ni d’interface d’administration pour confirmer ou refuser une demande.

## 11. Base de données locale

SQLite est configuré par défaut dans `.env.example`, donc XAMPP/MySQL n’est pas obligatoire. Sous PowerShell, après l’installation des dépendances, préparez votre environnement et appliquez les migrations :

```powershell
composer install
npm ci
npm run build
Copy-Item .env.example .env
php artisan key:generate
if (-not (Test-Path database\database.sqlite)) { New-Item database\database.sqlite -ItemType File }
php artisan migrate
```

Les tests utilisent SQLite en mémoire et ne modifient pas votre base locale.

## Conclusion

Le site permet aux visiteurs d’envoyer une demande de séjour enregistrée en base. L’équipe doit vérifier la disponibilité avant toute confirmation ; aucun paiement en ligne n’est proposé.
