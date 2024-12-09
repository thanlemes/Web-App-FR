<?php
    function criptografar_id($id, $chave) {
        $iv = openssl_random_pseudo_bytes(16);
        $dados_criptografados = openssl_encrypt($id, 'aes-128-cbc', $chave, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $dados_criptografados);
    }
?>