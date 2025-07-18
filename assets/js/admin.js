jQuery(document).ready(function($) {
    $('#abcd-wc-navex-send-btn').on('click', function(e) {
        e.preventDefault();

        var button = $(this);
        var spinner = button.next('.spinner');
        var orderId = button.data('order-id');
        var nonce = $('#abcd_wc_navex_nonce').val();

        // Afficher le spinner et désactiver le bouton
        spinner.addClass('is-active');
        button.prop('disabled', true);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'abcd_wc_navex_send_parcel',
                order_id: orderId,
                nonce: nonce
            },
            success: function(response) {
                spinner.removeClass('is-active');

                if (response.success) {
                    // Remplacer le bouton par un message de succès
                    button.siblings('p').html('<strong>Statut Navex :</strong> Envoyé');
                    button.remove();
                } else {
                    alert('Erreur : ' + response.data.message);
                    button.prop('disabled', false); // Réactiver le bouton seulement en cas d'erreur
                }
            },
            error: function(xhr, status, error) {
                spinner.removeClass('is-active');
                button.prop('disabled', false);
                alert('Une erreur AJAX est survenue : ' + error);
            }
        });
    });
});
