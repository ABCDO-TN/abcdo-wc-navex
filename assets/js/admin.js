(function($) {
    'use strict';

    $(function() {

        // Logic for the parcels dashboard
        if ($('#navex-parcels-table').length) {
            loadParcels();
        }

        // Details modal management
        var modal = $('#navex-details-modal');
        var modalBody = $('#navex-modal-body');

        // Open modal
        $(document).on('click', '.navex-details-btn', function(e) {
            e.preventDefault();
            var trackingId = $(this).data('tracking-id');
            
            modal.show();
            modalBody.html('<span class="spinner is-active"></span>');

            var data = {
                action: 'abcd_wc_navex_get_parcel_details',
                nonce: abcd_wc_navex_ajax.nonce,
                tracking_id: trackingId
            };

            $.post(abcd_wc_navex_ajax.ajax_url, data, function(response) {
                if (response.success) {
                    modalBody.html(response.data.html);
                } else {
                    modalBody.html('<p>Error: ' + response.data.message + '</p>');
                }
            });
        });

        // Close modal
        $('#navex-modal-close, #navex-modal-backdrop').on('click', function() {
            modal.hide();
        });


        // Logic for the send button on the order page
        $('#abcd-wc-navex-send-btn').on('click', function() {
            var button = $(this);
            var spinner = button.next('.spinner');
            var orderId = button.data('order-id');

            button.prop('disabled', true);
            spinner.css('visibility', 'visible');

            var data = {
                action: 'abcd_wc_navex_send_parcel',
                order_id: orderId,
                nonce: abcd_wc_navex_ajax.nonce
            };

            $.post(abcd_wc_navex_ajax.ajax_url, data, function(response) {
                button.prop('disabled', false);
                spinner.css('visibility', 'hidden');

                if (response.success) {
                    alert(response.data.message);
                    button.closest('div').html('<p><strong>Navex Status:</strong> Sent</p>');
                } else {
                    alert('Error: ' + response.data.message);
                }
            });
        });

        /**
         * Load parcels via AJAX and populate the table.
         */
        function loadParcels() {
            var tableBody = $('#navex-parcels-table tbody');
            
            var data = {
                action: 'abcd_wc_navex_get_parcels',
                nonce: abcd_wc_navex_ajax.nonce
            };

            $.post(abcd_wc_navex_ajax.ajax_url, data, function(response) {
                tableBody.empty();

                if (response.success) {
                    if (response.data.length > 0) {
                        $.each(response.data, function(index, parcel) {
                            var row = '<tr>' +
                                '<td>' + parcel.order_id + '</td>' +
                                '<td>' + parcel.tracking_id + '</td>' +
                                '<td>' + parcel.status + '</td>' +
                                '<td>' + parcel.date + '</td>' +
                                '<td>' + parcel.actions + '</td>' +
                                '</tr>';
                            tableBody.append(row);
                        });
                    } else {
                        tableBody.append('<tr><td colspan="5">No parcels found.</td></tr>');
                    }
                } else {
                    tableBody.append('<tr><td colspan="5">Error loading parcels: ' + response.data.message + '</td></tr>');
                }
            });
        }

    });

})(jQuery);
