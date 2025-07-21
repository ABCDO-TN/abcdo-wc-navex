<?php
/**
 * Encryption management file.
 *
 * @package Abcdo_Wc_Navex
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class to handle data encryption and decryption.
 */
class ABCD_WC_Navex_Crypto {

    /**
     * The encryption algorithm.
     *
     * @var string
     */
    private const CIPHER_ALGO = 'aes-256-cbc';

    /**
     * Encrypt a string.
     *
     * @param string $data The string to encrypt.
     * @return string|false The encrypted string or false on error.
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
     * Decrypt a string.
     *
     * @param string $data The encrypted string.
     * @return string|false The decrypted string or false on error.
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
     * Generate an encryption key from WordPress salts.
     *
     * @return string The encryption key.
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

        // Ensure the key is the correct length for AES-256
        return substr( hash( 'sha256', $key ), 0, 32 );
    }
}
