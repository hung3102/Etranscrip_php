<?php
namespace backend\components;
class FileSecure {
    CONST KEY_LEN = 11;

    public function createSecuredData($file) {
        $data = file_get_contents($file);
        $iv = $this->createIv();
        $key = pack("H*", substr(sha1(self::KEY_LEN).md5(self::KEY_LEN), 0, 64));
        $encryptedData = $this->encrypt($key, $data, $iv);
        $newFileData = $iv . "\n" . $encryptedData;
        return $newFileData;
    }

    public function decryptSecuredFile($file) {
        $fileData = file_get_contents($file);
        $data_array = explode("\n", $fileData);
        $iv = $data_array[0];
        $encryptedData = $data_array[1];
        $key = pack("H*", substr(sha1(self::KEY_LEN).md5(self::KEY_LEN), 0, 64));
        return $this->decrypt($key, $encryptedData, $iv);
    }

    private function createIv() {
        $size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_192, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($size, MCRYPT_DEV_RANDOM);
        return $iv;
    }

    private function encrypt($key, $data, $iv){
        $encrypted_data = mcrypt_cbc(MCRYPT_RIJNDAEL_192, $key, $data, MCRYPT_ENCRYPT, $iv);
        return base64_encode($encrypted_data);
    }

    private function decrypt($key, $encryptedData, $iv){
        $dec = base64_decode($encryptedData);
        $decrypt = mcrypt_cbc(MCRYPT_RIJNDAEL_192, $key, $dec, MCRYPT_DECRYPT, $iv);
        return trim($decrypt);
    }

}

?>   