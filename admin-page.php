<?php

function wpcm_admin_page() {
    // Verifica se o usuário atual tem permissão para acessar esta página
    if (!current_user_can('manage_options')) {
        wp_die(__('Você não tem permissões suficientes para acessar esta página.', 'wpcm-media-manager'));
    }

    // Processa o formulário de configurações, se necessário
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wpcm_max_image_size'])) {
        // Aqui você pode validar e salvar a opção de tamanho máximo de imagem
        update_option('wpcm_max_image_size', sanitize_text_field($_POST['wpcm_max_image_size']));
        echo '<div class="notice notice-success"><p>' . __('Configurações atualizadas.', 'wpcm-media-manager') . '</p></div>';
    }

    // Obtém o valor atual da configuração de tamanho máximo de imagem
    $max_image_size = get_option('wpcm_max_image_size', 1200); // 1200 é o valor padrão

    ?>
    <div class="wrap">
        <h1><?php _e('Gerenciador de Banners', 'wpcm-media-manager'); ?></h1>

        <form method="post" action="">
            <h2><?php _e('Configurações de Mídia', 'wpcm-media-manager'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="wpcm_max_image_size"><?php _e('Tamanho máximo da imagem (px)', 'wpcm-media-manager'); ?></label></th>
                    <td><input name="wpcm_max_image_size" type="number" id="wpcm_max_image_size" value="<?php echo esc_attr($max_image_size); ?>" class="small-text"></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>

        <!-- Início da seção de listagem de banners -->
        <h2><?php _e('Banners', 'wpcm-media-manager'); ?></h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('ID', 'wpcm-media-manager'); ?></th>
                    <th><?php _e('Preview', 'wpcm-media-manager'); ?></th>
                    <th><?php _e('Shortcode', 'wpcm-media-manager'); ?></th>
                    <th><?php _e('Actions', 'wpcm-media-manager'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $upload_dir = wp_upload_dir();
                $banners_dir = $upload_dir['basedir'] . '/banners';
                $banners_url = $upload_dir['baseurl'] . '/banners';

                if (file_exists($banners_dir)) {
                    $banners = scandir($banners_dir);
                    foreach ($banners as $banner) {
                        if ($banner == '.' || $banner == '..') continue; // Pula os diretórios '.' e '..'

                        $banner_path = $banners_dir . '/' . $banner;
                        $banner_url = $banners_url . '/' . $banner;
                        $shortcode = '[bn id="' . esc_attr($banner) . '"]';
                        ?>
                        <tr>
                            <td><?php echo esc_html($banner); ?></td>
                            <td><img src="<?php echo esc_url($banner_url); ?>" style="max-width: 100px; max-height: 100px;" /></td>
                            <td><input type="text" readonly="readonly" value="<?php echo esc_attr($shortcode); ?>" class="large-text code"></td>
                            <td>
                                <button class="button wpcm-copy-shortcode" data-shortcode="<?php echo esc_attr($shortcode); ?>"><?php _e('Copy Shortcode', 'wpcm-media-manager'); ?></button>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('.wpcm-copy-shortcode').click(function() {
            var shortcode = $(this).data('shortcode');
            navigator.clipboard.writeText(shortcode).then(function() {
                alert('<?php _e('Shortcode copied to clipboard', 'wpcm-media-manager'); ?>');
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        });
    });
    </script>
    <?php
}

// Adiciona a função wpcm_admin_page ao gancho admin_menu para exibir a página no admin
add_action('admin_menu', 'wpcm_register_admin_page');

function wpcm_register_admin_page() {
    add_menu_page(
        __('Gerenciador de Banners WPCM', 'wpcm-media-manager'),
        __('WPCM Banners', 'wpcm-media-manager'),
        'manage_options',
        'wpcm-media-manager',
        'wpcm_admin_page',
        'dashicons-images-alt2'
    );
}
