<?php
/**
 * Fichier pour la gestion du chiffrement.
 *
 * @package Abcdo_Wc_Navex
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Classe pour gérer le chiffrement et le déchiffrement des données.
 */
class Abcdo_Wc_Navex_Crypto {

    /**
     * L'algorithme de chiffrement.
     *
     * @var string
     */
    private const CIPHER_ALGO = 'aes-256-cbc';

    /**
     * Chiffrer une chaîne de caractères.
     *
     * @param string $data La chaîne à chiffrer.
     * @return string|false La chaîne chiffrée ou false en cas d'erreur.
     */
    public static function encrypt( $data ) {
        $key = self::get_encryption_key();
        $iv_length = openssl_cipher_iv_length( self::CIPHER_ALGO );
        $iv = openssl_random_pseudo_bytes( $iv_length );

        $encrypted = openssl_encrypt( $data, self::CIPHER_ALGO, $key, 0, $iv );

        if ( false === $encrypted ) {
            return false;
        }

        return base64_encode( $iv . $encrypted );
    }

    /**
     * Déchiffrer une chaîne de caractères.
     *
     * @param string $data La chaîne chiffrée.
     * @return string|false La chaîne déchiffrée ou false en cas d'erreur.
     */
    public static function decrypt( $data ) {
        $key = self::get_encryption_key();
        $data = base64_decode( $data, true );

        if ( false === $data ) {
            return false;
        }

        $iv_length = openssl_cipher_iv_length( self::CIPHER_ALGO );
        $iv = substr( $data, 0, $iv_length );
        $encrypted_data = substr( $data, $iv_length );

        return openssl_decrypt( $encrypted_data, self::CIPHER_ALGO, $key, 0, $iv );
    }

    /**
     * Générer une clé de chiffrement à partir des sels WordPress.
     *
     * @return string La clé de chiffrement.
     */
    private static function get_encryption_key() {
        $key = '';
        if ( defined( 'AUTH_KEY' ) ) {
            $key .= AUTH_KEY;
        }
        if ( defined( 'SECURE_AUTH_KEY' ) ) {
            $key .= SECURE_AUTH_KEY;
        }
        if ( defined( 'LOGGED_IN_KEY' ) ) {
            $key .= LOGGED_IN_KEY;
        }

        // S'assurer que la clé a la bonne longueur pour AES-256
        return substr( hash( 'sha256', $key ), 0, 32 );
    }
}
