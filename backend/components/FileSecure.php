<?php
namespace backend\components;
class FileSecure {
    public static $type = [
        0 => MCRYPT_3DES,
        1 => MCRYPT_RC2,
        2 => MCRYPT_SAFERPLUS,
        3 => MCRYPT_CAST_256,
        4 => MCRYPT_BLOWFISH,
        5 => MCRYPT_RIJNDAEL_192,
    ];

    public function createSecuredData($file, $type_index) {
        $data = file_get_contents($file);
        $iv = $this->createIv($type_index);
        $random = rand(10, 200);
        switch ($type_index) {
            case 0:
                $key = pack("H*", substr(sha1($random).md5($random), 0, 48));
                break;

            default:
                $key = pack("H*", substr(sha1($random).md5($random), 0, 64));
                break;
        }
        $encryptedData = $this->encrypt($key, $data, $iv, $type_index);
        $newFileData = md5($type_index) . "\n" . $key . "\n" . $iv . "\n" . $encryptedData;
        return $newFileData;
    }

    public function decryptSecuredFile($file) {
        $fileData = file_get_contents($file);
        $data_array = explode("\n", $fileData);
        $type_index = $this->getTypeIndex($data_array[0]);
        return $this->decrypt($data_array[1], $data_array[3], $data_array[2], $type_index);
    }

    private function getTypeIndex($hash) {
        for ($i=0; $i < sizeof(self::$type); $i++) { 
            if(md5($i) == $hash)
                return $i;
        }
    }

    private function createIv($type_index) {
        $size = mcrypt_get_iv_size(self::$type[$type_index], MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($size, MCRYPT_DEV_RANDOM);
        return $iv;
    }

    private function encrypt($key, $data, $iv, $type_index){
        $encrypted_data = mcrypt_encrypt(self::$type[$type_index], $key, $data, MCRYPT_MODE_CBC, $iv);
        return base64_encode($encrypted_data);
    }

    private function decrypt($key, $encryptedData, $iv, $type_index){
        $dec = base64_decode($encryptedData);
        $decrypt = mcrypt_decrypt(self::$type[$type_index], $key, $dec, MCRYPT_MODE_CBC, $iv);
        return trim($decrypt);
    }

}

?>   