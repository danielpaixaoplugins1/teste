<?php

/**
 * Sanitiza strings para serem seguras para uso em URLs, nomes de arquivos, etc.
 *
 * @param string $string A string a ser sanitizada.
 * @return string A string sanitizada.
 */
function wpcm_sanitize_string($string) {
    return sanitize_text_field($string);
}

/**
 * Verifica se um determinado arquivo é uma imagem com base na extensão.
 *
 * @param string $file_path Caminho do arquivo.
 * @return bool Verdadeiro se o arquivo for uma imagem, falso caso contrário.
 */
function wpcm_is_image($file_path) {
    $image_extensions = ['jpg', 'jpeg', 'png', 'webp'];
    $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

    return in_array($extension, $image_extensions);
}

/**
 * Verifica se um determinado arquivo é um GIF ou um banner.
 *
 * @param string $file_name Nome do arquivo.
 * @return bool Verdadeiro se o arquivo for um GIF ou começar com "banner-", falso caso contrário.
 */
function wpcm_is_gif_or_banner($file_name) {
    return preg_match('/\.gif$/i', $file_name) || strpos($file_name, 'banner-') === 0;
}

/**
 * Cria diretórios recursivamente se eles não existirem.
 *
 * @param string $path Caminho do diretório a ser criado.
 * @return bool Verdadeiro se o diretório foi criado ou já existe, falso caso contrário.
 */
function wpcm_create_directory($path) {
    if (!file_exists($path)) {
        return wp_mkdir_p($path);
    }

    return true;
}

/**
 * Gera um nome único para um arquivo baseado na hora atual para evitar conflitos.
 *
 * @param string $prefix Prefixo para o nome do arquivo.
 * @return string Nome de arquivo único.
 */
function wpcm_generate_unique_filename($prefix = '') {
    $time_suffix = date('YmdHis');
    return $prefix . $time_suffix;
}

/**
 * Exibe uma mensagem de erro administrativo.
 *
 * @param string $message Mensagem de erro a ser exibida.
 */
function wpcm_admin_notice_error($message) {
    add_action('admin_notices', function() use ($message) {
        echo '<div class="notice notice-error"><p>' . esc_html($message) . '</p></div>';
    });
}
