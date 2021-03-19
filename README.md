# Projet Info 4 Mura Loïc

Ce projet a eu pour but de découvrir les fonctionnalités de Symfony

### Le site est composé de plusieurs pages :

| Type | Nom | Lien |
|:---- |:--- |:---- |
|Static|Accueil|/|
|Static|A propos|/notre-histoire|
|Static|Mentions légales|/mentions-legales|
|Static|CGV|/conditions-generales-de-vente|
|Dynamique|Connexion|/connexion|
|Dynamique|Déconnexion|/deconnexion|
|Dynamique|Inscription|/inscription|
|Dynamique|Contact|/nous-contacter|
|Dynamique|Panneau d'administration|/admin|
|Dynamique|Panier|/panier|
|Dynamique|Liste des produits|/nos-t-shirts|

### Les fonctionnalitées suivantes ont été développés :

- Requêtes de base CRUD
- Requêtes ajax delete / update
- Panier avec une gestion de la session
- Authentification des utilisateurs avec roles
- Envoie de mail sur la page contact
- Upload / suprression de fichier image
- Production de données fictives avec orm-fixtures et faker
- Validation des formulaires côté serveur
- Filtre twig pour l'affichage du prix formaté en €
- Pagination de la page /nos-t-shirts avec le bundle KnpPaginator

### Les autres outils utilisés :

- Maildev

### Bundle ajoutés :

- orm-fixtures
- fzaninotto/faker
- twig/string-extra
- knplabs/knp-paginator-bundle
