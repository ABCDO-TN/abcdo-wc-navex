=== ABCDO Navex Integration for WooCommerce ===
Contributors: ABCDO
Tags: woocommerce, shipping, delivery, navex, integration
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.0.22
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Integrates the Navex delivery API with WooCommerce to automate parcel creation and tracking.

== Description ==

This plugin connects your WooCommerce store to the Tunisian delivery service Navex. It allows you to:
*   **Create parcels** automatically or manually from WooCommerce orders.
*   **Securely configure** your Navex API key.
*   **Track the shipping status** directly from the order page.
*   **Receive plugin updates** directly from GitHub.

== Installation ==

1.  Download the `.zip` file of the plugin.
2.  Go to `WordPress Admin > Plugins > Add New` and click `Upload Plugin`.
3.  Choose the `.zip` file and activate the plugin.
4.  Go to `Navex Delivery > Settings` and enter your Navex API keys (add, get, delete).

== Changelog ==

= 1.0.22 =
*   Fix: Corrected and simplified the GitHub Actions workflow to ensure the plugin `.zip` is created correctly.
*   Enhancement: The release process is now more robust and automatically generates release notes.

= 1.0.21 =
*   Feature: Implemented a GitHub Actions workflow to automate the creation of release packages.
*   Enhancement: Verified and confirmed performance best practices, such as conditional loading of assets.

= 1.0.20 =
*   Enhancement: Full internationalization of the plugin into English (UI, code comments, documentation).
*   Fix: All identified PHP errors, warnings, and notices have been resolved.

= 1.0.19 =
*   Fix: The query to retrieve orders is now compatible with High-Performance Order Storage (HPOS), resolving a fatal error and parcel desynchronization.

= 1.0.18 =
*   Fix: The API call for parcel details now correctly handles non-JSON responses, fixing the "Invalid JSON response" error.
*   Fix: Removed aggressive update cache clearing, which resolves inconsistencies on the WordPress Updates page.

= 1.0.17 =
*   Feature: The "Details" button is now functional and opens a modal window with the parcel information.
*   Feature: Added encryption for API tokens stored in the database.
*   Fix: The list of parcels is now synchronized with WooCommerce orders; parcels linked to deleted orders no longer appear.
*   Fix: Translation files are now loaded on the correct hook to prevent PHP notices.

= 1.0.16 =
*   Fix: Resolved a fatal error in the update system that caused plugins and update notifications to disappear.
*   Enhancement: The updater class is now more robust and avoids race conditions on initialization.

= 1.0.15 =
*   Feature: Added a parcel tracking dashboard (`Navex Delivery`).
*   Feature: Redesigned the settings page with dedicated fields for add, get, and delete tokens.
*   Enhancement: Added a custom menu icon for better identification.
*   Enhancement: AJAX logic is now handled by a dedicated script and secured by a unified nonce.

= 1.0.14 =
*   Fix: Resolved a fatal error in the update system.
*   Fix: Improved stability when checking for updates.
*   Fix: The plugin now correctly checks for the existence of data before using it.

= 1.0.13 =
*   Enhancement: The update check is now forced when visiting the WordPress update page.

= 1.0.12 =
*   Fix: Improved HPOS compatibility for the display of the Navex shipping box.

= 1.0.11 =
*   Fix: Resolved a fatal HPOS compatibility error with older versions of WooCommerce.

= 1.0.10 =
*   Fix: Improved HPOS (High-Performance Order Storage) compatibility.
*   Fix: Optimized translation loading to avoid warnings.
*   Fix: Removed duplicate constructor in the Admin class.

= 1.0.8 =
*   Feature: Full automation of the release process. Deployment is now done automatically on a push to the main branch, using the version and changelog from the plugin files.

= 1.0.7 =
*   Fix: Major improvement of the automatic update system to resolve the "cURL error 52". The archive structure and post-installation logic have been corrected.

= 1.0.6 =
*   Fix: Resolved a fatal error on the order page when using High-Performance Order Storage (HPOS).
*   Fix: Removed a "deprecated" warning from PHP 8.

= 1.0.5 =
*   Fix: Ensures compatibility of the Navex shipping box with High-Performance Order Storage (HPOS).

= 1.0.4 =
*   Feature: Finalization of the automatic update system via GitHub. The plugin now detects new versions.

= 1.0.3 =
*   Feature: Implementation of the logic for sending parcels to Navex from the order page.
*   Feature: Added AJAX script for manual sending.

= 1.0.2 =
*   Fix: Added compatibility declaration with WooCommerce's High-Performance Order Storage (HPOS).

= 1.0.1 =
*   Fix: Resolved errors in the GitHub Actions workflow for creating releases.

= 1.0.0 =
*   Initial version.
*   Creation of the plugin structure.
*   Integration of the Navex API for parcel creation.
*   Configuration page for the API key.
*   Implementation of the update system via GitHub.
