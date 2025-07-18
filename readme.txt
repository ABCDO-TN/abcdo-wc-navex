=== ABCDO Navex Integration for WooCommerce ===
Contributors: ABCDO
Tags: woocommerce, shipping, delivery, navex, integration
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.0.2
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
4.  Allez dans `WooCommerce > Réglages > Intégration > ABCDO Navex` et entrez votre clé d'API Navex.

== Changelog ==

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
