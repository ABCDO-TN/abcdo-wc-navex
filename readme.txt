=== ABCDO Navex Integration for WooCommerce ===
Contributors: ABCDO
Tags: woocommerce, shipping, delivery, navex, integration
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.0.19
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Intègre l'API de livraison Navex avec WooCommerce pour automatiser la création de colis et le suivi.

== Description ==

Ce plugin connecte votre boutique WooCommerce au service de livraison tunisien Navex. Il permet de :
*   **Créer des colis** automatiquement ou manuellement depuis les commandes WooCommerce.
*   **Configurer votre clé d'API** Navex en toute sécurité.
*   **Suivre le statut** de l'envoi directement depuis la page de la commande.
*   **Recevoir des mises à jour** du plugin directement depuis GitHub.

== Installation ==

1.  Téléchargez le fichier `.zip` du plugin.
2.  Allez dans `WordPress Admin > Extensions > Ajouter` et cliquez sur `Téléverser une extension`.
3.  Choisissez le fichier `.zip` et activez le plugin.
4.  Allez dans `Navex Delivery > Settings` et entrez vos clés d'API Navex (ajout, récupération, suppression).

== Changelog ==

= 1.0.19 =
*   Correction : La requête pour récupérer les commandes est maintenant compatible avec le stockage de commandes haute performance (HPOS), résolvant une erreur fatale et la désynchronisation des colis.

= 1.0.18 =
*   Correction : L'appel API pour les détails d'un colis gère maintenant correctement les réponses non-JSON, corrigeant l'erreur "Réponse JSON invalide".
*   Correction : Suppression du nettoyage agressif du cache des mises à jour, ce qui résout les incohérences dans la page des mises à jour de WordPress.

= 1.0.17 =
*   Fonctionnalité : Le bouton "Détails" est maintenant fonctionnel et ouvre une fenêtre modale avec les informations du colis.
*   Fonctionnalité : Ajout du chiffrement pour les tokens d'API stockés en base de données.
*   Correction : La liste des colis est maintenant synchronisée avec les commandes WooCommerce, les colis liés à des commandes supprimées n'apparaissent plus.
*   Correction : Le chargement des fichiers de traduction se fait maintenant sur le bon hook pour éviter les notices PHP.

= 1.0.16 =
*   Correction : Résolution d'une erreur fatale dans le système de mise à jour qui provoquait la disparition des plugins et des notifications de mise à jour.
*   Amélioration : La classe de mise à jour est maintenant plus robuste et évite les conditions de course à l'initialisation.

= 1.0.15 =
*   Fonctionnalité : Ajout d'un tableau de bord de suivi des colis (`Navex Delivery`).
*   Fonctionnalité : Refonte de la page de réglages avec des champs dédiés pour les tokens d'ajout, de récupération et de suppression.
*   Amélioration : Ajout d'une icône de menu personnalisée pour une meilleure identification.
*   Amélioration : La logique AJAX est maintenant gérée par un script dédié et sécurisée par un nonce unifié.

= 1.0.14 =
*   Correction : Résolution d'une erreur fatale dans le système de mise à jour.
*   Correction : Amélioration de la stabilité lors de la vérification des mises à jour.
*   Correction : Le plugin vérifie maintenant correctement l'existence des données avant de les utiliser.

= 1.0.13 =
*   Amélioration : La vérification des mises à jour est maintenant forcée lors de la visite de la page des mises à jour de WordPress.

= 1.0.12 =
*   Correction : Amélioration de la compatibilité HPOS pour l'affichage de la boîte d'envoi Navex.

= 1.0.11 =
*   Correction : Résolution d'une erreur fatale de compatibilité HPOS avec les anciennes versions de WooCommerce.

= 1.0.10 =
*   Correction : Amélioration de la compatibilité HPOS (High-Performance Order Storage).
*   Correction : Optimisation du chargement des traductions pour éviter les avertissements.
*   Correction : Suppression du constructeur en double dans la classe Admin.

= 1.0.8 =
*   Fonctionnalité : Automatisation complète du processus de release. Le déploiement se fait maintenant automatiquement lors d'un push sur la branche main, en utilisant la version et le changelog des fichiers du plugin.

= 1.0.7 =
*   Correction : Amélioration majeure du système de mise à jour automatique pour résoudre l'erreur "cURL error 52". La structure de l'archive et la logique de post-installation ont été corrigées.

= 1.0.6 =
*   Correction : Résolution d'une erreur fatale sur la page de commande lors de l'utilisation du stockage des commandes haute performance (HPOS).
*   Correction : Suppression d'un avertissement "deprecated" de PHP 8.

= 1.0.5 =
*   Correction : Assure la compatibilité de la boîte d'envoi Navex avec le stockage des commandes haute performance (HPOS).

= 1.0.4 =
*   Fonctionnalité : Finalisation du système de mise à jour automatique via GitHub. Le plugin détecte maintenant les nouvelles versions.

= 1.0.3 =
*   Fonctionnalité : Implémentation de la logique d'envoi de colis à Navex depuis la page de commande.
*   Fonctionnalité : Ajout du script AJAX pour l'envoi manuel.

= 1.0.2 =
*   Correction : Ajout de la déclaration de compatibilité avec le stockage des commandes haute performance (HPOS) de WooCommerce.

= 1.0.1 =
*   Correction : Résolution des erreurs du workflow GitHub Actions pour la création des releases.

= 1.0.0 =
*   Version initiale.
*   Création de la structure du plugin.
*   Intégration de l'API Navex pour la création de colis.
*   Page de configuration pour la clé d'API.
*   Mise en place du système de mise à jour via GitHub.
