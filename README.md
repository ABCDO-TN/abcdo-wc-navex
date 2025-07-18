# ABCDO Navex Integration for WooCommerce

**Auteur:** ABCDO
**Nom du plugin:** ABCDO Navex Integration for WooCommerce
**Version:** 1.0.0
**Dépôt GitHub:** `https://github.com/abcd-wc-navex/abcdo-wc-navex`

## Description

Ce plugin intègre le service de livraison Navex directement dans WooCommerce. Il permet d'envoyer automatiquement les détails des commandes à l'API Navex pour créer des colis et de suivre leur statut depuis le tableau de bord WordPress.

Le plugin inclut également un mécanisme de mise à jour automatique via GitHub pour simplifier la maintenance et le déploiement des nouvelles versions.

## Feuille de Route (RDP)

### Phase 1 : Initialisation et Structure du Plugin

-   [x] **Créer le fichier `README.md`** : Documentation initiale du projet.
-   [ ] **Créer la structure des fichiers** :
    -   `abcdo-wc-navex.php` (Fichier principal du plugin)
    -   `readme.txt` (Fichier standard pour le répertoire de plugins WordPress)
    -   `LICENSE` (Licence du projet)
    -   `.gitignore` (Pour exclure les fichiers non nécessaires)
    -   `includes/` (Répertoire pour les classes et la logique métier)
        -   `class-abcd-wc-navex-admin.php` (Gestion de l'interface d'administration)
        -   `class-abcd-wc-navex-api.php` (Client pour l'API Navex)
        -   `class-abcd-wc-navex-integration.php` (Logique d'intégration avec WooCommerce)
        -   `class-abcd-wc-navex-updater.php` (Gestionnaire des mises à jour via GitHub)
    -   `assets/` (Pour les fichiers CSS et JavaScript)

### Phase 2 : Développement du Cœur du Plugin

-   [ ] **Développer le fichier principal (`abcdo-wc-navex.php`)** :
    -   Ajouter l'en-tête du plugin (nom, version, auteur, etc.).
    -   Définir les constantes (`ABCDO_WC_NAVEX_VERSION`, `ABCDO_WC_NAVEX_PATH`).
    -   Inclure les fichiers de classes du répertoire `includes/`.
    -   Instancier les classes principales.

-   [ ] **Créer la page de configuration (`class-abcd-wc-navex-admin.php`)** :
    -   Ajouter un onglet "ABCDO Navex" dans `WooCommerce > Réglages > Intégration`.
    -   Créer un champ pour que l'utilisateur puisse saisir et sauvegarder sa clé d'API Navex.
    -   Sécuriser la sauvegarde et la récupération de la clé.

-   [ ] **Implémenter le client API (`class-abcd-wc-navex-api.php`)** :
    -   Créer une méthode `send_parcel()` qui prend les détails de la commande en paramètre.
    -   Construire la requête POST vers `https://app.navex.tn/api/{TOKEN}/v1/post.php` en utilisant `wp_remote_post()`.
    -   Gérer les réponses de l'API (succès, erreurs) et retourner un résultat structuré.

-   [ ] **Intégrer avec WooCommerce (`class-abcd-wc-navex-integration.php`)** :
    -   Utiliser le hook `woocommerce_order_status_changed` pour déclencher l'envoi à l'API Navex lorsque le statut d'une commande passe à "En cours de traitement" (ou un autre statut configurable).
    -   Ajouter un "meta box" sur la page de détail de la commande pour afficher le statut de l'envoi Navex.
    -   Ajouter un bouton dans ce "meta box" pour permettre un envoi manuel de la commande à Navex.

### Phase 3 : Mises à Jour Automatiques

-   [ ] **Développer le gestionnaire de mises à jour (`class-abcd-wc-navex-updater.php`)** :
    -   Utiliser les filtres `pre_set_site_transient_update_plugins` et `plugins_api`.
    -   Interroger l'API GitHub (`https://api.github.com/repos/{user}/{repo}/releases/latest`) pour vérifier l'existence d'une nouvelle version.
    -   Si une nouvelle version est disponible, afficher une notification de mise à jour dans l'interface d'administration de WordPress.
    -   Fournir l'URL du fichier `.zip` de la release pour permettre la mise à jour.

### Phase 4 : Automatisation du Déploiement (CI/CD)

-   [ ] **Créer le répertoire `.github/workflows/`**.
-   [ ] **Développer le workflow `release.yml`** :
    -   Déclencher le workflow lors de la création d'une nouvelle "release" sur GitHub.
    -   Utiliser une action pour archiver les fichiers du plugin dans un fichier `abcdo-wc-navex.zip`.
    -   Exclure les fichiers et répertoires de développement (`.git`, `.github`, `README.md`, etc.) de l'archive `.zip` finale.
    -   Attacher l'archive `.zip` en tant qu'artefact à la release GitHub.

## Spécifications de l'API Navex

-   **Endpoint :** `https://app.navex.tn/api/{TOKEN}/v1/post.php`
-   **Méthode :** `POST`
-   **Authentification :** La clé d'API est incluse dans l'URL.
-   **Corps de la requête :** `application/x-www-form-urlencoded`

### Champs de la requête

| Champ         | Description                      | Obligatoire | Type   |
|---------------|----------------------------------|-------------|--------|
| `prix`        | Prix total de la commande        | Oui         | string |
| `nom`         | Nom complet du client            | Oui         | string |
| `gouvernerat` | Gouvernorat de livraison         | Oui         | string |
| `ville`       | Ville de livraison               | Oui         | string |
| `adresse`     | Adresse de livraison             | Oui         | string |
| `tel`         | Numéro de téléphone du client    | Oui         | string |
| `tel2`        | Deuxième numéro de téléphone     | Non         | string |
| `designation` | Description des articles         | Oui         | string |
| `nb_article`  | Nombre total d'articles          | Oui         | number |
| `msg`         | Message/note pour le livreur     | Non         | string |
| `echange`     | Indique si c'est un échange      | Non         | string |
| `article`     | Article à échanger               | Non         | string |
| `nb_echange`  | Nombre d'articles à échanger     | Non         | string |
| `ouvrir`      | Autorisation d'ouvrir le colis   | Oui         | string |
