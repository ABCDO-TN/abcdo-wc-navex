(function($) {
    'use strict';

    $(function() {

        // Logique pour le tableau de bord des colis
        if ($('#navex-parcels-table').length) {
            loadParcels();
        }

        // Logique pour le bouton d'envoi sur la page de commande
        $('#abcd-wc-navex-send-btn').on('click', function() {
            var button = $(this);
            var spinner = button.next('.spinner');
            var orderId = button.data('order-id');
            var nonce = $('#abcd_wc_navex_nonce').val();

            button.prop('disabled', true);
            spinner.css('visibility', 'visible');

            var data = {
                action: 'abcd_wc_navex_send_parcel',
                order_id: orderId,
                nonce: nonce
            };

            $.post(ajaxurl, data, function(response) {
                button.prop('disabled', false);
                spinner.css('visibility', 'hidden');

                if (response.success) {
                    alert(response.data.message);
                    // Mettre à jour l'affichage du statut sans recharger la page
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
                tableBody.empty(); // Vider le message de chargement

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

    });

})(jQuery);
