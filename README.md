# ABCDO Navex Integration for WooCommerce

**Auteur :** ABCDO
**Version :** 1.0.15
**Dépôt GitHub :** `https://github.com/ABCDO-TN/abcdo-wc-navex`

## Description

Ce plugin intègre le service de livraison Navex directement dans WooCommerce. Il permet d'envoyer les détails des commandes à l'API Navex pour créer des colis et de suivre leur statut depuis le tableau de bord WordPress.

Le plugin inclut un mécanisme de mise à jour automatique via GitHub pour simplifier la maintenance et le déploiement des nouvelles versions.

## Fonctionnalités

*   **Tableau de Bord de Suivi :** Un tableau de bord centralisé pour visualiser en temps réel le statut de tous vos colis expédiés avec Navex.
*   **Configuration Facile :** Une page de réglages dédiée pour configurer les clés d'API nécessaires à la communication avec Navex.
*   **Envoi Manuel :** Un bouton sur chaque page de commande WooCommerce pour envoyer manuellement les informations du colis à Navex.
*   **Mises à Jour Automatiques :** Recevez les notifications de nouvelles versions et mettez à jour le plugin en un clic depuis votre tableau de bord WordPress.
*   **Compatibilité HPOS :** Entièrement compatible avec le système de stockage de commandes haute performance de WooCommerce.

## Installation

1.  Téléchargez la dernière version du plugin depuis la page [Releases](https://github.com/ABCDO-TN/abcdo-wc-navex/releases) du dépôt GitHub.
2.  Dans votre tableau de bord WordPress, allez dans `Extensions > Ajouter`.
3.  Cliquez sur `Téléverser une extension` et sélectionnez le fichier `.zip` que vous avez téléchargé.
4.  Activez l'extension.

## Configuration

Une fois le plugin activé, vous devez configurer vos clés d'API Navex pour permettre la communication avec leurs services.

1.  Dans le menu de gauche de WordPress, naviguez vers `Navex Delivery > Settings`.
2.  Remplissez les champs suivants avec les informations fournies par Navex :
    *   **Token d'ajout :** Nécessaire pour créer de nouveaux colis.
    *   **Token de récupération :** Nécessaire pour suivre le statut des colis existants.
    *   **Token de suppression :** Nécessaire pour annuler un colis.
3.  Cliquez sur `Save Settings`.

## Utilisation

### Suivi des Colis

Pour voir le statut de tous vos colis, allez dans `Navex Delivery` dans le menu principal de WordPress. Le tableau de bord se chargera et affichera la liste de vos envois.

*Note : Cette fonctionnalité dépend du "Token de récupération". Assurez-vous qu'il est correctement configuré.*

### Envoyer un Colis Manuellement

1.  Allez sur la page d'une commande WooCommerce (`WooCommerce > Commandes` et cliquez sur une commande).
2.  Sur la droite, vous trouverez une boîte "ABCDO Navex Shipping".
3.  Cliquez sur le bouton `Send to Navex` pour transmettre les informations de la commande à l'API Navex et créer le colis.
4.  Le statut de l'envoi sera alors mis à jour dans la boîte.

*Note : Cette fonctionnalité dépend du "Token d'ajout".*
