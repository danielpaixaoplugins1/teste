<?php

// Incluir o arquivo de funções auxiliares para usar funções como wpcm_create_directory
require_once 'functions.php';

/**
 * Otimiza e converte imagens para WebP durante o upload.
 *
 * @param array $file Dados do arquivo enviado.
 * @return array Dados do arquivo modificado.
 */
function wpcm_optimize_and_convert_image($file) {
    // Verifica se o arquivo é uma imagem e permite a conversão para WebP.
    if (!wpcm_is_image($file['tmp_name']) || wpcm_is_gif_or_banner($file['name'])) {
        return $file; // Retorna o arquivo sem modificação se não for uma imagem ou for um GIF/banner.
    }

    $original_path = $file['tmp_name'];
    $webp_path = $original_path . '.webp';

    // Tenta converter a imagem para WebP.
    if (function_exists('imagewebp')) {
        $image = imagecreatefromstring(file_get_contents($original_path));
        if ($image === false) {
            return $file; // Retorna o arquivo sem modificação se a conversão falhar.
        }
        imagewebp($image, $webp_path, 80); // 80 é a qualidade de compressão.
        imagedestroy($image);

        // Redimensiona a imagem se necessário.
        list($width, $height) = getimagesize($webp_path);
        $max_size = 1200; // Define o tamanho máximo para redimensionamento.
        if ($width > $max_size) {
            wpcm_resize_image($webp_path, $max_size);
        }

        // Renomeia o arquivo WebP.
        $new_name = 'img' . date('i') . date('s') . '.webp';
        $new_path = dirname($webp_path) . '/' . $new_name;
        rename($webp_path, $new_path);

        // Atualiza os dados do arquivo para refletir a nova imagem WebP.
        $file['name'] = $new_name;
        $file['type'] = 'image/webp';
        $file['tmp_name'] = $new_path;

        // Exclui o arquivo original se especificado nas configurações.
        if (true) { // Substituir por uma verificação de configuração real.
            unlink($original_path);
        }
    }

    return $file;
}

/**
 * Redimensiona uma imagem para uma largura máxima, mantendo a proporção.
 *
 * @param string $file_path Caminho do arquivo da imagem.
 * @param int $max_width Largura máxima desejada.
 */
function wpcm_resize_image($file_path, $max_width) {
    $info = getimagesize($file_path);

    if ($info === false) {
        return; // Falha ao obter informações da imagem, aborta o redimensionamento.
    }

    $width = $info[0];
    $height = $info[1];
    $ratio = $height / $width;
    $new_width = $max_width;
    $new_height = $max_width * $ratio;

    // Cria uma nova imagem com as dimensões desejadas.
    $new_image = imagecreatetruecolor($new_width, $new_height);
    $source = imagecreatefromwebp($file_path);
    imagecopyresampled($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    // Salva a imagem redimensionada.
    imagewebp($new_image, $file_path, 80); // Mantém a qualidade em 80.

    // Libera memória.
    imagedestroy($source);
    imagedestroy($new_image);
}

// Hook para otimizar e converter imagens durante o upload.
add_filter('wp_handle_upload_prefilter', 'wpcm_optimize_and_convert_image');
