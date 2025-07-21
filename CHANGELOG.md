# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.21] - 2025-07-21

### Added
- **Automated Release Workflow:** Implemented a GitHub Actions workflow that automatically builds the plugin `.zip` archive, creates a GitHub Release, and populates the changelog from the `CHANGELOG.md` file when a new version tag is pushed.

### Changed
- **Performance:** Verified that asset loading is optimized and follows WordPress best practices to ensure minimal impact on admin performance.

## [1.0.20] - 2025-07-21

### Changed
- **Full Internationalization:** The entire plugin (UI, code comments, documentation) has been translated into English, which is now the default source language.
- **Code Refactoring:** Performed a full code review. All identified PHP errors, warnings, and notices have been resolved. The code now fully complies with WordPress standards.

## [1.0.19] - 2025-07-21

### Fixed
- **HPOS Compatibility:** The `wc_get_orders` query in the `ajax_get_parcels` function has been rewritten to be fully compatible with High-Performance Order Storage (HPOS). This resolves the fatal error `WC_Order_Data_Store_CPT::query was called incorrectly` and fixes parcel desynchronization.

## [1.0.18] - 2025-07-19

### Fixed
- **API Response Handling:** The API call for parcel details now correctly handles raw text responses, resolving the "Invalid JSON response" error.
- **Update System Stability:** Removed aggressive and unnecessary clearing of the update cache (`update_plugins` transient), which stabilizes the WordPress update system and resolves display inconsistencies.

## [1.0.17] - 2025-07-19

### Added
- **Token Encryption:** API tokens are now encrypted in the database using WordPress security salts for enhanced security.
- **"Parcel Details" Feature:** The "Details" button on the dashboard now opens a modal window displaying the full parcel information retrieved via a new API request.

### Fixed
- **Parcel Synchronization:** The parcel list displayed on the dashboard is now directly linked to existing WooCommerce orders. Parcels whose orders have been deleted no longer appear.
- **Translation Loading:** The textdomain is now loaded on the `init` hook to comply with WordPress standards and eliminate PHP notices.

### Changed
- **API Logic:** The API class has been updated to handle token decryption and includes a new method to retrieve details for a specific parcel.
- **Admin Logic:** The Admin class now handles the saving of encrypted tokens, the new data source for the parcel list, and the AJAX endpoint for the details modal.

## [1.0.16] - 2025-07-19

### Fixed
- **Critical Update System Bug:** Resolved a fatal error (`Trying to access array offset on null`) that prevented the loading of the plugin list and update notifications. The `ABCD_WC_Navex_Updater` class now loads its dependencies securely to avoid race conditions.

### Changed
- **Updater Class Reliability:** Added security guards and improved the logic for handling GitHub API responses to make the update process more robust.

## [1.0.15] - 2025-07-19

### Added
- **Tracking Dashboard:** Added a new "Navex Delivery" admin page to display the real-time status of parcels via AJAX.
- **Advanced Token Management:** Created a dedicated settings page (`Navex Delivery > Settings`) with separate fields for the add, get, and delete API tokens.
- **Custom Menu Icon:** Integrated a custom SVG icon for the main plugin menu.

### Changed
- **Admin Architecture:** The `ABCD_WC_Navex_Admin` class has been completely restructured to support the new menu hierarchy and the WordPress Settings API.
- **API Architecture:** The `ABCD_WC_Navex_API` class now handles multiple tokens and has been prepared for future methods (get, delete).
- **AJAX Security:** All AJAX actions are now secured by a unified and verified WordPress nonce, improving protection against CSRF vulnerabilities.
- **Admin Scripts:** The `admin.js` file has been updated to handle the logic for the new tracking dashboard.
