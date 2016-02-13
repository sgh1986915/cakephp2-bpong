<?php
class EncryptionComponent extends Component
{
      private function hex2bin($hexdata) 
      {
        $bindata = '';

        for ($i = 0; $i < strlen($hexdata); $i += 2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }

        return $bindata;
      }
        function aes_decrypt($code)  
        {
            
            $code = $this->hex2bin($code);
            $iv = AES_ENCRYPT_IV;
            $key128 =AES_ENCRYPT_KEY;

            //$td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);
            $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
          
            //mcrypt_generic_init($td, $this->key, $iv);
            mcrypt_generic_init($cipher, $key128, $iv);   
          
            $decrypted = mdecrypt_generic($cipher, $code);

            mcrypt_generic_deinit($cipher);
            mcrypt_module_close($cipher);

            return utf8_encode(trim($decrypted));
        }    
}
?>
