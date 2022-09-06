<?php
namespace App\Http\Controllers;
class AESCipher extends Controller {

    private static $OPENSSL_CIPHER_NAME = "aes-128-cbc"; //Name of OpenSSL Cipher 
    private static $CIPHER_KEY_LEN = 16; //128 bits
    private static $key = "-=)(*&^%()!@#ffs";
    private static $iv = "!)@(#*f&%^123456";
    /**
     * Encrypt data using AES Cipher (CBC) with 128 bit key
     * 
     * @param type $key - key to use should be 16 bytes long (128 bits)
     * @param type $iv - initialization vector
     * @param type $data - data to encrypt
     * @return encrypted data in base64 encoding with iv attached at end after a :
     */
    

    
    static function encrypt($data) {
        if (strlen(AESCipher::$key) < AESCipher::$CIPHER_KEY_LEN) {
            AESCipher::$key = str_pad("AESCipher::$key", AESCipher::$CIPHER_KEY_LEN, "0"); //0 pad to len 16
        } else if (strlen(AESCipher::$key) > AESCipher::$CIPHER_KEY_LEN) {
            AESCipher::$key = substr(AESCipher::$key, 0, AESCipher::$CIPHER_KEY_LEN); //truncate to 16 bytes
        }
        
        $encodedEncryptedData = base64_encode(openssl_encrypt($data, AESCipher::$OPENSSL_CIPHER_NAME, AESCipher::$key, OPENSSL_RAW_DATA, AESCipher::$iv));
        $encodedIV = base64_encode(AESCipher::$iv);
        $encryptedPayload = $encodedEncryptedData.":".$encodedIV;
        
        return $encryptedPayload;
        
    }

    /**
     * Decrypt data using AES Cipher (CBC) with 128 bit key
     * 
     * @param type $key - key to use should be 16 bytes long (128 bits)
     * @param type $data - data to be decrypted in base64 encoding with iv attached at the end after a :
     * @return decrypted data
     */
    static function decrypt($data) {
        try {
            if (strlen(AESCipher::$key) < AESCipher::$CIPHER_KEY_LEN) {
                AESCipher::$key = str_pad("AESCipher::$key", AESCipher::$CIPHER_KEY_LEN, "0"); //0 pad to len 16
            } else if (strlen(AESCipher::$key) > AESCipher::$CIPHER_KEY_LEN) {
                AESCipher::$key = substr(AESCipher::$key, 0, AESCipher::$CIPHER_KEY_LEN); //truncate to 16 bytes
            }
            $parts = explode(':', $data); //Separate Encrypted data from iv.
            if (sizeof($parts) != 2){
                $decryptedData = "";
            }else{
                $decryptedData = openssl_decrypt(base64_decode($parts[0]), AESCipher::$OPENSSL_CIPHER_NAME, AESCipher::$key, OPENSSL_RAW_DATA, base64_decode($parts[1]));
            }
            return $decryptedData;
        } catch (\Throwable $th) {
           return '';
        }
        
    }

}

?>