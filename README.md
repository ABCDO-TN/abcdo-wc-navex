# ABCDO Navex Integration for WooCommerce

**Author:** ABCDO
**Version:** 1.0.20
**GitHub Repository:** `https://github.com/ABCDO-TN/abcdo-wc-navex`

## Description

This plugin integrates the Navex delivery service directly into WooCommerce. It allows sending order details to the Navex API to create parcels and track their status from the WordPress dashboard.

The plugin also includes an automatic update mechanism via GitHub to simplify the maintenance and deployment of new versions.

## Features

*   **Tracking Dashboard:** A centralized dashboard to view the real-time status of all your parcels shipped with Navex.
*   **Easy Configuration:** A dedicated settings page to configure the necessary API keys for communication with Navex.
*   **Manual Sending:** A button on each WooCommerce order page to manually send the parcel information to Navex.
*   **Automatic Updates:** Receive notifications of new versions and update the plugin with one click from your WordPress dashboard.
*   **HPOS Compatibility:** Fully compatible with WooCommerce's High-Performance Order Storage system.

## Installation

1.  Download the latest version of the plugin from the [Releases](https://github.com/ABCDO-TN/abcdo-wc-navex/releases) page of the GitHub repository.
2.  In your WordPress dashboard, go to `Plugins > Add New`.
3.  Click on `Upload Plugin` and select the `.zip` file you downloaded.
4.  Activate the plugin.

## Configuration

Once the plugin is activated, you must configure your Navex API keys to allow communication with their services.

1.  In the left menu of WordPress, navigate to `Navex Delivery > Settings`.
2.  Fill in the following fields with the information provided by Navex:
    *   **Add Token:** Required to create new parcels.
    *   **Get Token:** Required to track the status of existing parcels.
    *   **Delete Token:** Required to cancel a parcel.
3.  Click `Save Settings`. The tokens will be encrypted and stored securely in the database.

## Usage

### Parcel Tracking

To see the status of all your parcels, go to `Navex Delivery` in the main WordPress menu. The dashboard will load and display the list of your shipments.

*Note: This feature depends on the "Get Token". Make sure it is configured correctly.*

### Sending a Parcel Manually

1.  Go to a WooCommerce order page (`WooCommerce > Orders` and click on an order).
2.  On the right, you will find a "ABCDO Navex Shipping" box.
3.  Click the `Send to Navex` button to transmit the order information to the Navex API and create the parcel.
4.  The shipping status will then be updated in the box.

*Note: This feature depends on the "Add Token".*
