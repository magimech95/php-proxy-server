<?php

/*
 * Usage:
    $c = new App_Sandbox_Cipher('Key'); // key is optional if you have 'APP_SANDBOX_ENCRYPTION_KEY' constant defined.
    $encrypted = $c->encrypt('secret message');
    $decrypted = $c->decrypt($encrypted);
*/

/**
* @see http://stackoverflow.com/questions/1788150/how-to-encrypt-string-in-php
*/
class App_Sandbox_Cipher {
    private $iv;
    private $iv_size;
    private $secure_key;
    private $method;

    function __construct($key = '', $method = 'blowfish') {
        if (empty($key)) {
            $key = APP_SANDBOX_ENCRYPTION_KEY;
        }

        if (!method_exists($this, 'encrypt_' . $method)) {
            throw new Exception(__METHOD__ . " Invalid encryption method selected.");
        }

        /*
        *  Notice about performance:
            By default mcrypt_create_iv() function uses /dev/random as a source of random values. If server has low entropy this source could be very slow. In this case you can use /dev/urandom.
            Here is a good explanation what is the difference between them http://www.onkarjoshi.com/blog/191/device-dev-random-vs-urandom/
            So, if you are not using this encryption for something critical (i hope you don't) then you can use /dev/urandom to improve encryption performance. Just change this line:
            $iv = mcrypt_create_iv($this->iv_size, MCRYPT_DEV_URANDOM);
        */
        $this->iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $this->iv = mcrypt_create_iv($this->iv_size, MCRYPT_DEV_URANDOM); // should be faster than when not specifying the 2nd param.
        $this->secure_key = hash('sha256', $key, TRUE);
        $this->method = $method;
    }

    /**
     * This calls the encryption method based on the method
     * @param str $input
     * @return str
     */
    function encrypt($input) {
        $method = 'encrypt_' . $this->method;
        $str = $this->$method($input);

        return $str;
    }

    /**
     * This calls the decryption method based on the method
     * @param str $input
     * @return str
     */
    function decrypt($input) {
        $method = 'decrypt_' . $this->method;
        $str = $this->$method($input);

        return $str;
    }

    // For some reason the string can't be decrypted properly on my live site!?!
    function encrypt_rijndael($input) {
        $iv = $this->iv;
        $str = $iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->secure_key, $input, MCRYPT_MODE_CBC, $iv); // base64_encode
        $str = $this->pack($str);

        return $str;
    }

    // For some reason the string can't be decrypted properly on my live site!?!
    function decrypt_rijndael($input) {
        $input = $this->unpack($input);
        $iv = $this->iv;
        $cipher = substr($input, $this->iv_size);
        $str = @trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->secure_key, $cipher, MCRYPT_MODE_CBC, $this->iv));

        return $str;
    }

    /**
     *
     * @param type $pure_string
     * @return type
     * @see http://stackoverflow.com/questions/16600708/how-do-you-encrypt-and-decrypt-a-php-string
     */
    function encrypt_blowfish($pure_string) {
        $iv_size = $this->iv_size;
        $iv = $this->iv;
        $encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, $this->secure_key, utf8_encode($pure_string), MCRYPT_MODE_ECB, $iv);
        $encrypted_string = $this->pack($encrypted_string);

        return $encrypted_string;
    }

    /**
     * Returns decrypted original string
     */
    function decrypt_blowfish($encrypted_string) {
        $encrypted_string = $this->unpack($encrypted_string);

        //$iv_size = $this->iv_size;
        $iv = $this->iv;
        $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $this->secure_key, $encrypted_string, MCRYPT_MODE_ECB, $iv);
        $decrypted_string = trim($decrypted_string);

        return $decrypted_string;
    }

    /**
     * Package for easy transportation
     * @param str $str
     * @return str
     */
    function pack($str) {
        $str = bin2hex($str);
        return $str;
    }

    /**
     * Unpackage for easy processing
     * @param str $str
     * @return str
     */
    function unpack($str) {
        $str = hex2bin($str);
        return $str;
    }
}

?>