# Changelog

Toutes les modifications notables apportées à ce projet seront documentées dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
et ce projet adhère à [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.16] - 2025-07-19

### Corrigé
- **Bug Critique du Système de Mise à Jour :** Résolution d'une erreur fatale (`Trying to access array offset on null`) qui empêchait le chargement de la liste des plugins et des notifications de mise à jour. La classe `ABCD_WC_Navex_Updater` charge maintenant ses dépendances de manière sécurisée pour éviter les conditions de course.

### Modifié
- **Fiabilité de la Classe Updater :** Ajout de gardes de sécurité et amélioration de la logique de gestion des réponses de l'API GitHub pour rendre le processus de mise à jour plus robuste.

## [1.0.15] - 2025-07-19

### Ajouté
- **Tableau de Bord de Suivi :** Ajout d'une nouvelle page d'administration "Navex Delivery" pour afficher en temps réel le statut des colis via AJAX.
- **Gestion Avancée des Tokens :** Création d'une page de réglages dédiée (`Navex Delivery > Settings`) avec des champs séparés pour les tokens d'API d'ajout, de récupération et de suppression.
- **Icône de Menu Personnalisée :** Intégration d'une icône SVG personnalisée pour le menu principal du plugin.

### Modifié
- **Architecture Admin :** La classe `ABCD_WC_Navex_Admin` a été entièrement restructurée pour prendre en charge la nouvelle hiérarchie des menus et la Settings API de WordPress.
- **Architecture API :** La classe `ABCD_WC_Navex_API` gère maintenant plusieurs tokens et a été préparée pour de futures méthodes (récupération, suppression).
- **Sécurité AJAX :** Toutes les actions AJAX sont désormais sécurisées par un nonce WordPress unifié et vérifié, améliorant la protection contre les failles CSRF.
- **Scripts Admin :** Le fichier `admin.js` a été mis à jour pour gérer la logique du nouveau tableau de bord de suivi.
