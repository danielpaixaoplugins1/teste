<?php

// Incluir o arquivo de funções auxiliares
require_once 'functions.php';

/**
 * Função para tratar o upload de GIFs e banners, aplicando regras específicas,
 * e organizando os arquivos em diretórios baseados na data de upload.
 *
 * @param array $file Dados do arquivo enviado.
 * @return array Dados do arquivo modificado.
 */
function wpcm_handle_gifs_and_banners($file) {
    // Verifica se o arquivo é um GIF ou um banner
    if (wpcm_is_gif_or_banner($file['name'])) {
        // Define o limite de tamanho para 5MB para GIFs e banners
        $file_size_limit = 5 * 1024 * 1024; // 5MB em bytes

        if ($file['size'] > $file_size_limit) {
            // Adiciona uma mensagem de erro se o arquivo exceder o limite de tamanho
            $file['error'] = 'O arquivo excede o limite de tamanho de 5MB para GIFs e banners.';
            return $file;
        }

        // Determina o diretório de destino para banners, organizado por data
        $uploads_dir = wp_upload_dir();
        $date_path = date('Y/m/d');
        $banners_dir = $uploads_dir['basedir'] . '/banners/' . $date_path;
        wpcm_create_directory($banners_dir); // Cria o diretório se não existir

        // Constrói o novo caminho do arquivo no diretório de banners
        $new_file_path = $banners_dir . '/' . basename($file['name']);

        // Move o arquivo para o novo diretório
        if (!@move_uploaded_file($file['tmp_name'], $new_file_path)) {
            $file['error'] = 'Falha ao mover o arquivo GIF/banner para o diretório específico.';
            return $file;
        }

        // Atualiza o caminho temporário do arquivo para refletir o novo diretório
        $file['tmp_name'] = $new_file_path;

        // Atualiza a URL para refletir a nova localização do arquivo
        $file['url'] = $uploads_dir['baseurl'] . '/banners/' . $date_path . '/' . basename($file['name']);
    }

    return $file;
}

// Adiciona o filtro para tratar GIFs e banners durante o upload
add_filter('wp_handle_upload_prefilter', 'wpcm_handle_gifs_and_banners');
