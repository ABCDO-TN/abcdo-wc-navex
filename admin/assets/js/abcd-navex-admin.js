(function($) {
    'use strict';

    $(function() {

        // Logique pour le tableau de bord des colis
        if ($('#navex-parcels-table').length) {
            loadParcels();
        }

        // Gestion de la modale de détails
        var modal = $('#navex-details-modal');
        var modalBody = $('#navex-modal-body');

        // Ouvrir la modale
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
                    modalBody.html('<p>Erreur: ' + response.data.message + '</p>');
                }
            });
        });

        // Fermer la modale
        $('#navex-modal-close, #navex-modal-backdrop').on('click', function() {
            modal.hide();
        });

        // Gérer la suppression de colis
        $(document).on('click', '.navex-delete-btn', function(e) {
            e.preventDefault();

            if ( ! confirm( 'Êtes-vous sûr de vouloir supprimer ce colis ?' ) ) {
                return;
            }

            var trackingId = $(this).data('tracking-id');
            var row = $(this).closest('tr');

            var data = {
                action: 'abcd_wc_navex_delete_parcel',
                nonce: abcd_wc_navex_ajax.nonce,
                tracking_id: trackingId
            };

            $.post(abcd_wc_navex_ajax.ajax_url, data, function(response) {
                if (response.success) {
                    row.fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    alert('Erreur: ' + response.data.message);
                }
            });
        });


        // Logique pour le bouton d'envoi sur la page de commande
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
                    button.closest('div').html('<p><strong>Navex Status:</strong> Envoyé</p>');
                } else {
                    alert('Erreur: ' + response.data.message);
                }
            });
        });

        /**
         * Charge les colis via AJAX et remplit le tableau.
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
                        tableBody.append('<tr><td colspan="5">Aucun colis trouvé.</td></tr>');
                    }
                } else {
                    tableBody.append('<tr><td colspan="5">Erreur lors de la récupération des colis: ' + response.data.message + '</td></tr>');
                }
            });
        }

        // Handle token deletion
        $(document).on('click', '.navex-delete-token-btn', function(e) {
            e.preventDefault();

            if ( ! confirm( 'Are you sure you want to delete this token?' ) ) {
                return;
            }

            var button = $(this);
            var tokenKey = button.data('token-key');
            var statusSpan = button.closest('.navex-token-status');

            var data = {
                action: 'abcdo_wc_navex_delete_token',
                nonce: abcd_wc_navex_ajax.nonce,
                token_key: tokenKey
            };

            button.prop('disabled', true);

            $.post(abcd_wc_navex_ajax.ajax_url, data, function(response) {
                if (response.success) {
                    statusSpan.html('<span style="color: red;">Status: Not Set</span>');
                } else {
                    alert('Error: ' + response.data.message);
                    button.prop('disabled', false);
                }
            });
        });

    });

})(jQuery);
