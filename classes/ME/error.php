<?php

function createFolderLogsMelhorEnvio() {

    $pathUploads = wp_upload_dir();
    $path = $pathUploads['basedir'] . '/logs_melhor_envio';

    if(!is_dir($path)) {
        mkdir($path);
    }

    return $path;
}

function getContentFileOrCreate($path) {
    $file = $path . '/error' . date('m') . '.txt';
    if (!file_exists($file)) {
        fopen($file, "w");
    }

    return [
        'file'    => $file,
        'content' =>  file_get_contents($file),
    ];
}

function insertLogErrorMelhorEnvio($data) {

    $path = createFolderLogsMelhorEnvio();
    $content = getContentFileOrCreate($path);
   
    $line = date('Y-m-d h:i:s') . ' - Error: (' . $data->id . ')  ' . $data->company->name . '. Motivo: ' . $data->error . "\r\n";
    $txt = $content['content'] . $line;

    $file = fopen($content['file'], "w");
    fwrite($file, tirarAcentos($txt));
    fclose($file);    
}

function insertLogErrorMelhorEnvioGeneric($data) {

    $path = createFolderLogsMelhorEnvio();
    $content = getContentFileOrCreate($path);
   
    $line = date('Y-m-d h:i:s') . ' - COTACAO TELA DO PRODUTO: (' . $data . ') ' . "\r\n";
    $txt = $content['content'] . $line;

    $file = fopen($content['file'], "w");
    fwrite($file, tirarAcentos($txt));
    fclose($file);    
}

function tirarAcentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
}