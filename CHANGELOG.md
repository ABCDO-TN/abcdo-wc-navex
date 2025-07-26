# Changelog

## [1.1.0] - 2025-07-26
### Added
- **Security:** Complete refactoring of API key management. Keys are now stored encrypted in a dedicated database table (`wp_abcdo_wc_navex_tokens`) instead of `wp_options`.
- **Security:** The settings UI is now secure. The API key is never displayed in the form, preventing its exposure in the HTML source code.
- **Feature:** Simplified configuration with a single API key field instead of three.
- **Feature:** Added a button to easily delete the saved API key.
- **Improvement:** Added a migration routine to clean up old API keys from the `wp_options` table on update.
- **Improvement:** The code structure has been refactored into separate classes (Database_Manager, Token_Manager, Settings, Migration) for better maintainability and separation of concerns.

## [1.0.17] - 2024-07-25
### Added
- Feature: The "Details" button is now functional and opens a modal with parcel information.
- Feature: Added encryption for API tokens stored in the database.
### Fixed
- The parcel list is now synchronized with WooCommerce orders; parcels linked to deleted orders no longer appear.
- Translation files are now loaded on the correct hook to avoid PHP notices.
²
... (previous versions)
