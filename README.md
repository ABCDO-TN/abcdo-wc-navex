# ABCDO Navex Integration for WooCommerce

This plugin seamlessly integrates your WooCommerce store with the Navex shipping service, automating the process of creating shipping labels and synchronizing order statuses.

This enhanced version introduces robust multi-token API management for improved reliability and load balancing, along with automatic order status synchronization to streamline your fulfillment workflow.

## Features

*   **Direct Shipment Creation:** Create Navex shipping labels directly from the WooCommerce order details page with a single click.
*   **Multi-Token API Management:** Configure multiple Navex API tokens. The plugin will rotate through the tokens for each request, providing load balancing and failover capabilities. If one token fails, the next one is automatically used.
*   **Automatic Order Sync:** Automatically create a Navex shipment when a WooCommerce order is updated to a specific status (e.g., 'Processing').
*   **Customizable Status Mapping:** Define which WooCommerce order status should trigger the automatic shipment creation and what status the order should be set to upon successful creation.
*   **Detailed Logging:** Keep track of all API interactions. Successful requests and errors are logged within WooCommerce for easy debugging and monitoring.
*   **Order Notes Integration:** Automatically adds the Navex tracking number and a tracking link to the corresponding WooCommerce order notes upon successful shipment creation.

## Installation

### From your WordPress Dashboard

1.  Navigate to **Plugins > Add New** in your WordPress admin dashboard.
2.  Click on the **Upload Plugin** button at the top of the page.
3.  Select the `abcd-navex-integration.zip` file from your computer.
4.  Click **Install Now**.
5.  Once the installation is complete, click **Activate Plugin**.

### Manual Installation via FTP

1.  Unzip the `abcd-navex-integration.zip` file.
2.  Upload the extracted `abcd-navex-integration` folder to the `/wp-content/plugins/` directory on your web server.
3.  Navigate to **Plugins > Installed Plugins** in your WordPress admin dashboard.
4.  Find the "ABCDO Navex Integration for WooCommerce" plugin in the list and click **Activate**.

## Configuration and Usage

After activating the plugin, you must configure it with your Navex API credentials and automation preferences.

### Configuration

1.  Navigate to **WooCommerce > Settings** in your WordPress admin dashboard.
2.  Click on the **Integration** tab.
3.  Select the **ABCDO Navex** option to access the settings page.

You will see the following options:

*   **Enable/Disable:** Check this box to enable the Navex integration.
*   **Navex API Tokens:** Enter your Navex API tokens here. **Enter one token per line.** The plugin will use these tokens in rotation for each API request.
*   **Enable Auto Sync:** Check this box to automatically create a Navex shipment when an order's status is updated.
*   **Trigger Status for Auto Sync:** Select the WooCommerce order status that will trigger the automatic shipment creation (e.g., `Processing`). When an order is updated to this status, the plugin will attempt to create a Navex shipment.
*   **Status After Success:** Select the order status to be applied after a Navex shipment is successfully created automatically (e.g., `Completed`).
*   **Enable Logging:** It is highly recommended to keep this enabled. It logs all API requests and responses for troubleshooting. Logs can be viewed under **WooCommerce > Status > Logs**. Select the appropriate `abcd-navex-integration` log file from the dropdown.

### Usage Guide

#### 1. Automatic Shipment Creation

If you have enabled **Auto Sync** in the settings, the plugin will handle shipment creation automatically.

1.  When an order is paid for, its status will typically change to `Processing` (or the custom status you selected as the trigger).
2.  The plugin detects this status change and automatically sends a shipment creation request to the Navex API.
3.  Upon success, the order status is updated to your chosen **Status After Success**, and the Navex tracking number is added to the order notes.
4.  If the API call fails, an error message is added to the order notes, and the order status remains unchanged.

#### 2. Manual Shipment Creation

You can manually create a shipment for any order, regardless of the auto-sync settings.

1.  Navigate to **WooCommerce > Orders** and open the order you wish to ship.
2.  On the right-hand side, you will find a meta box titled **Navex Shipment**.
3.  This box displays the current shipment status for the order.
4.  Click the **Create Navex Shipment** button.
5.  The plugin will send the request to the Navex API. Upon success, the page will refresh, and the tracking number will appear in the meta box and in the order notes. If it fails, an error message will be displayed.

#### 3. Viewing Tracking Information and Logs

*   **Tracking Number:** The Navex tracking number and a direct tracking link are added to the private order notes for easy access by store administrators.
*   **Logs:** To troubleshoot any issues, navigate to **WooCommerce > Status > Logs**. Select the log file starting with `abcd-navex-integration` from the dropdown menu to view detailed records of API communication.
