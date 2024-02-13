<?php

// Incluir o arquivo de funções auxiliares
require_once 'functions.php';

/**
 * Organiza uploads de vídeos e áudios em diretórios baseados na data (ano/mês/dia).
 *
 * @param array $file Dados do arquivo enviado.
 * @return array Dados do arquivo modificados.
 */
function wpcm_organize_media_uploads($file) {
    // Determina se o arquivo é um vídeo ou áudio
    $file_type = wp_check_filetype($file['file']); // Retorna o tipo do arquivo
    $uploads_dir = wp_upload_dir();
    $date_path = date('Y/m/d');

    if (strpos($file_type['type'], 'video') !== false) {
        $target_dir = $uploads_dir['basedir'] . '/videos/' . $date_path;
        $target_url = $uploads_dir['baseurl'] . '/videos/' . $date_path;
    } elseif (strpos($file_type['type'], 'audio') !== false) {
        $target_dir = $uploads_dir['basedir'] . '/audios/' . $date_path;
        $target_url = $uploads_dir['baseurl'] . '/audios/' . $date_path;
    } else {
        // Se o arquivo não for vídeo nem áudio, retorna sem modificação
        return $file;
    }

    // Cria o diretório de destino, se não existir
    wpcm_create_directory($target_dir);

    // Gera um novo nome de arquivo para evitar sobreposição
    $new_filename = wpcm_generate_unique_filename() . '.' . pathinfo($file['file'], PATHINFO_EXTENSION);
    $new_file_path = $target_dir . '/' . $new_filename;

    // Move o arquivo para o novo diretório
    if (!@move_uploaded_file($file['tmp_name'], $new_file_path)) {
        // Se a movimentação falhar, retorna o arquivo sem modificação e possivelmente registra um erro
        return $file;
    }

    // Atualiza os dados do arquivo para refletir a nova localização
    $file['file'] = $new_file_path;
    $file['url'] = $target_url . '/' . $new_filename;

    return $file;
}

// Hook para organizar vídeos e áudios durante o upload
add_filter('wp_handle_upload', 'wpcm_organize_media_uploads');
