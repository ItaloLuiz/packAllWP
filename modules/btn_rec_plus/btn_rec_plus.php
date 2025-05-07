<?php
/*
Plugin Name: Gerenciar Botões WP
Description: Gerenciador de botões personalizados com shortcode
Author:ítalo
Version: 1.0
*/

if (!defined('ABSPATH')) exit;

// Registrar Post Type
function register_button_post_type() {
    register_post_type('custom_button', array(
        'labels' => array(
            'name' => 'Botões',
            'singular_name' => 'Botão',
            'add_new' => 'Adicionar Novo',
            'add_new_item' => 'Adicionar Novo Botão',
            'edit_item' => 'Editar Botão',
            'new_item' => 'Novo Botão',
            'view_item' => 'Ver Botão',
            'search_items' => 'Buscar Botões',
            'not_found' => 'Nenhum botão encontrado',
            'not_found_in_trash' => 'Nenhum botão encontrado na lixeira'
        ),
        'public' => true,
        'show_in_nav_menus' => false,
        'show_in_admin_bar' => false,
        'publicly_queryable' => false,
        'exclude_from_search' => true,
        'supports' => array('title'),
        'menu_icon' => 'dashicons-button',
        'rewrite' => false
    ));
}
add_action('init', 'register_button_post_type');

// Adicionar colunas na listagem
function add_button_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['shortcode'] = 'Shortcode';
    $new_columns['label'] = 'Label';
    return $new_columns;
}
add_filter('manage_custom_button_posts_columns', 'add_button_columns');

// Preencher conteúdo das colunas
function fill_button_columns($column, $post_id) {
    switch ($column) {
        case 'shortcode':
            echo '<code>[btnRecPlusPlus id="' . $post_id . '"]</code>';
            echo '<button class="button button-small copy-shortcode" data-shortcode="[btnRecPlusPlus id=&quot;' . $post_id . '&quot;]">Copiar</button>';
            break;
        case 'label':
            echo esc_html(get_post_meta($post_id, '_label', true));
            break;
    }
}
add_action('manage_custom_button_posts_custom_column', 'fill_button_columns', 10, 2);

// Adicionar JavaScript para copiar shortcode
function add_copy_shortcode_script() {
    $screen = get_current_screen();
    if ($screen->post_type === 'custom_button') {
        ?>
        <script>
        jQuery(document).ready(function($) {
            $('.copy-shortcode').click(function() {
                var shortcode = $(this).data('shortcode');
                navigator.clipboard.writeText(shortcode);
                $(this).text('Copiado!');
                setTimeout(() => $(this).text('Copiar'), 1000);
            });
        });
        </script>
        <style>
        .copy-shortcode {
            margin-left: 5px !important;
        }
        .column-shortcode {
            width: 200px;
        }
        .column-label {
            width: 150px;
        }
        </style>
        <?php
    }
}
add_action('admin_footer', 'add_copy_shortcode_script');

// Adicionar Meta Box
function add_button_meta_box() {
    add_meta_box(
        'button_settings',
        'Configurações do Botão',
        'render_button_settings',
        'custom_button',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_button_meta_box');

// Opções predefinidas de texto
function get_predefined_texts() {
    return array(
        'VÁ PARA O SITE OFICIAL' => 'VÁ PARA O SITE OFICIAL',
        'GO TO THE OFFICIAL WEBSITE' => 'GO TO THE OFFICIAL WEBSITE',
        'VAYAS AL SITIO OFICIAL' => 'VAYAS AL SITIO OFICIAL',
        'VAI AL SITO UFFICIALE' => 'VAI AL SITO UFFICIALE',        
        'VER ANÁLISE COMPLETA' => 'VER ANÁLISE COMPLETA',
        'SEE FULL ANALYSIS' => 'SEE FULL ANALYSIS',
        'VER ANÁLISIS COMPLETO' => 'VER ANÁLISIS COMPLETO',
        'VEDI ANALISI COMPLETA' => 'VEDI ANALISI COMPLETA',
        'personalizado' => 'Texto Personalizado'
    );
}


function get_predefined_label() {
    return array(
        // Português
        'Você saira do site' => 'Você saira do site',
        'Você permanecera nesse site' => 'Você permanecera nesse site',
    
        // Inglês
        'You will leave the site' => 'You will leave the site',
        'You will stay on this site' => 'You will stay on this site',
    
        // Espanhol
        'Salirás del sitio' => 'Salirás del sitio',
        'Permanecerás en este sitio' => 'Permanecerás en este sitio',
    
        // Italiano
        'Lascerai il sito' => 'Lascerai il sito',
        'Rimarrai su questo sito' => 'Rimarrai su questo sito',
        
    );
}

// Renderizar configurações
function render_button_settings($post) {
    wp_nonce_field('button_settings_nonce', 'button_settings_nonce');
    
    $texto_btn = get_post_meta($post->ID, '_texto_btn', true);
    $url = get_post_meta($post->ID, '_url', true);
    $texto_tipo = get_post_meta($post->ID, '_texto_tipo', true);
    $width = get_post_meta($post->ID, '_width', true) ?: '100%';
    $height = get_post_meta($post->ID, '_height', true) ?: '40px';
    $bgbtn = get_post_meta($post->ID, '_bgbtn', true) ?: '#44bd32';
    $corLink = get_post_meta($post->ID, '_corLink', true) ?: '#ffffff';
    $hover_bg = get_post_meta($post->ID, '_hover_bg', true) ?: '#4cd137';
    $hover_color = get_post_meta($post->ID, '_hover_color', true) ?: '#ffffff';
    $fonte = get_post_meta($post->ID, '_fonte', true) ?: '20px';
    $label = get_post_meta($post->ID, '_label', true);
    $border = get_post_meta($post->ID, '_border', true);
    
    ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <div class="button-settings-container">
        <style>
            .button-settings-container {
                max-width: 800px;
                padding: 20px;
            }
            .settings-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin-bottom: 20px;
            }
            .field-group {
                margin-bottom: 15px;
            }
            .field-group label {
                display: block;
                margin-bottom: 5px;
                font-weight: 600;
            }
            .shortcode-display {
                background: #f0f0f1;
                padding: 15px;
                margin-top: 20px;
                border-radius: 5px;
            }
            .wp-picker-container {
                margin-top: 5px;
            }
            .button-preview {
                margin-top: 20px;
                padding: 20px;
                background: #f9f9f9;
                border-radius: 5px;
            }
        </style>

        <div class="settings-grid">
            <div class="field-group">
                <label>Texto do Botão:</label>
                <select name="texto_tipo" class="widefat" id="texto_tipo">
                    <?php foreach (get_predefined_texts() as $key => $value): ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($texto_tipo, $key); ?>>
                            <?php echo esc_html($value); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field-group" id="texto_personalizado_group" style="<?php echo $texto_tipo === 'personalizado' ? 'display:block' : 'display:none'; ?>">
                <label>Texto Personalizado para o Botão:</label>
                <input type="text" name="texto_btn" class="widefat" value="<?php echo esc_attr($texto_btn); ?>">
            </div>

            <div class="field-group">
                <label>URL:</label>
                <input type="url" name="url" class="widefat" value="<?php echo esc_url($url); ?>">
            </div>

            <div class="field-group">
                <label>Label (texto explicativo abaixo do botão):</label>
                <!--<input type="text" name="label" class="widefat" value="<?php echo esc_attr($label); ?>">-->

                <select name="label" class="widefat" id="label">
                    <?php foreach (get_predefined_label() as $key => $value): ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($label, $key); ?>>
                            <?php echo esc_html($value); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field-group" id="label_personalizado_group" style="<?php echo $label === 'personalizado' ? 'display:block' : 'display:none'; ?>">
                <label>Texto Personalizado para o Label:</label>
                <input type="text" name="label_personalizado" class="widefat" value="<?php echo esc_attr($label); ?>">
            </div>
            

            <div class="field-group">
                <label>Largura:</label>
                <input type="text" name="width" class="widefat" value="<?php echo esc_attr($width); ?>">
            </div>

            <div class="field-group">
                <label>Altura:</label>
                <input type="text" name="height" class="widefat" value="<?php echo esc_attr($height); ?>">
            </div>
          

            <div class="field-group">
                <label>Cor de Fundo:</label>
                <input type="text" name="bgbtn" class="color-picker" value="<?php echo esc_attr($bgbtn); ?>">
            </div>

            <div class="field-group">
                <label>Cor do Texto:</label>
                <input type="text" name="corLink" class="color-picker" value="<?php echo esc_attr($corLink); ?>">
            </div>

            <div class="field-group">
                <label>Cor de Fundo (Hover):</label>
                <input type="text" name="hover_bg" class="color-picker" value="<?php echo esc_attr($hover_bg); ?>">
            </div>

            <div class="field-group">
                <label>Cor do Texto (Hover):</label>
                <input type="text" name="hover_color" class="color-picker" value="<?php echo esc_attr($hover_color); ?>">
            </div>
            <hr>

            <div class="field-group">
                <label>Tamanho da Fonte:</label>
                <input type="text" name="fonte" class="widefat" value="<?php echo esc_attr($fonte); ?>">
            </div>
            <div class="field-group">
                <label>Borda:</label>
                <input type="text" name="border" class="widefat" value="<?php echo esc_attr($border); ?>">
            </div>
        </div>

        <div class="shortcode-display">
            <strong>Shortcode:</strong>
            <code>[btnRecPlusPlus id="<?php echo $post->ID; ?>"]</code>
            <button class="button button-small copy-shortcode" data-shortcode="[btnRecPlusPlus id=&quot;<?php echo $post->ID; ?>&quot;]">Copiar</button>
        </div>

        <div class="button-preview">
            <h3>Preview do Botão</h3>
            <div id="button-preview-area"></div>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('.color-picker').wpColorPicker({
            change: updatePreview
        });
        
        $('#texto_tipo').change(function() {
            if ($(this).val() === 'personalizado') {
                $('#texto_personalizado_group').show();
            } else {
                $('#texto_personalizado_group').hide();
                updatePreview();
            }
        });

        /*$('#label').change(function() {
            if ($(this).val() === 'personalizado') {
                $('#label_personalizado_group').show();
            } else {
                $('#label_personalizado_group').hide();
                updatePreview();
            }
        });*/

        $('input, select').on('change keyup', updatePreview);

        function updatePreview() {
            var textoTipo = $('#texto_tipo').val();
            var textoBotao = textoTipo === 'personalizado' ? 
                $('input[name="texto_btn"]').val() :
                $('#texto_tipo option:selected').text();


            var label = $('#label').val();
            var textoLabel = label === 'personalizado' ? 
                $('input[name="label"]').val() :
                $('#label_personalizado option:selected').text();

            var preview = `
                <style>
                    .btn-rec-preview {
                        display: inline-flex;
                        justify-content: center;
                        align-items: center;
                        text-decoration: none;
                        background-color: ${$('input[name="bgbtn"]').val()};
                        color: ${$('input[name="corLink"]').val()} !important;
                        border: none;
                        border-radius: 4px;
                        padding: 10px 20px;
                        width: ${$('input[name="width"]').val()};
                        height: ${$('input[name="height"]').val()};
                        font-family: Arial, sans-serif;
                        font-size: ${$('input[name="fonte"]').val()};
                        transition: all 0.3s ease;
                        text-align: center;
                        border-radius: ${$('input[name="border"]').val()};
                    }
                    .btn-rec-preview:hover {
                        background-color: ${$('input[name="hover_bg"]').val()};
                        color: ${$('input[name="hover_color"]').val()} !important;
                        text-decoration: none !important;
                    }
                    .btn-rec-container {
                        text-align: center;
                    }
                    .btn-rec-label-preview {
                        font-size: 14px;
                        color: #666;
                        margin-top: 5px;
                        display: block;
                        text-align: center;
                    }
                </style>
                <div class="btn-rec-container">
                    <a href="${$('input[name="url"]').val()}" class="btn-rec-preview">
                        ${textoBotao}
                    </a>
                    ${$('select[name="label"]').val() ? 
                        '<span class="btn-rec-label-preview">' + $('select[name="label"]').val() + '</span>' : 
                        ''}
                </div>
            `;
            
            $('#button-preview-area').html(preview);
        }

        updatePreview();
    });
    </script>
    <?php
}

function save_button_meta_box($post_id) {
    if (!isset($_POST['button_settings_nonce']) || !wp_verify_nonce($_POST['button_settings_nonce'], 'button_settings_nonce')) {
        return;
    }

    $fields = ['_texto_btn', '_url', '_texto_tipo', '_width', '_height', '_bgbtn', '_corLink', '_hover_bg', '_hover_color', '_fonte', '_label','_border'];
    foreach ($fields as $field) {
        if (isset($_POST[str_replace('_', '', $field)])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[str_replace('_', '', $field)]));
        }
    }
}
add_action('save_post', 'save_button_meta_box');


// Salvar Meta Box
function save_button_settings($post_id) {
    if (!isset($_POST['button_settings_nonce']) || 
        !wp_verify_nonce($_POST['button_settings_nonce'], 'button_settings_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    $fields = array(
        'texto_tipo', 'texto_btn', 'url', 'label', 'width', 'height',
        'bgbtn', 'corLink', 'hover_bg', 'hover_color', 'fonte','border'
    );

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post_custom_button', 'save_button_settings');

// Registrar Shortcode
function custom_button_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => 0
    ), $atts, 'btnRecPlusPlus');

    if (empty($atts['id'])) {
        return '';
    }

    $post_id = intval($atts['id']);
    
    // Recupera o tipo de texto e texto personalizado
    $texto_tipo = get_post_meta($post_id, '_texto_tipo', true);
    $texto_btn = get_post_meta($post_id, '_texto_btn', true);
    
    // Define o texto final do botão
    if ($texto_tipo === 'personalizado') {
        $texto_final = $texto_btn;
    } else {
        $predefined_texts = get_predefined_texts();
        $texto_final = $predefined_texts[$texto_tipo];
    }

 

    // Recupera demais dados
    $button_data = array(
        'url' => get_post_meta($post_id, '_url', true),
        'label' => get_post_meta($post_id, '_label', true),
        'width' => get_post_meta($post_id, '_width', true) ?: '100%',
        'height' => get_post_meta($post_id, '_height', true) ?: '40px',
        'bgbtn' => get_post_meta($post_id, '_bgbtn', true) ?: '#44bd32',
        'corLink' => get_post_meta($post_id, '_corLink', true) ?: '#ffffff',
        'hover_bg' => get_post_meta($post_id, '_hover_bg', true) ?: '#4cd137',
        'hover_color' => get_post_meta($post_id, '_hover_color', true) ?: '#ffffff',
        'fonte' => get_post_meta($post_id, '_fonte', true) ?: '20px',
        'border' => get_post_meta($post_id, '_border', true) ?: '10px'
    );

    $style = "<style>
        .btn-rec-container-{$post_id} {
            text-align: center;
            margin: 10px 0;
        }
        .btn-rec-{$post_id} {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            background-color: {$button_data['bgbtn']};
            color: {$button_data['corLink']} !important;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            width: {$button_data['width']};
            height: {$button_data['height']};
            font-family: Arial, sans-serif;
            font-size: {$button_data['fonte']};
            transition: all 0.3s ease;
            text-align: center;
            border-radius: {$button_data['border']};
            cursor: pointer;
        }
        .btn-rec-{$post_id}:hover {
            background-color: {$button_data['hover_bg']};
            color: {$button_data['hover_color']} !important;
            text-decoration: none !important;
        }
        .btn-rec-label-{$post_id} {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
            display: block;
            text-align: center;
        }
    </style>";

    $button = "<div class='btn-rec-container-{$post_id}'>";
	
  $button .= "<a href='" . esc_url($button_data['url']) . "' class='btn-rec-{$post_id}'>"
         . "<i style='margin-right:5px' aria-hidden='true' class='fas fa-check-circle'></i> "
         . esc_html($texto_final) 
         . "</a>";


    
    if (!empty($button_data['label'])) {
        $button .= "<span class='btn-rec-label-{$post_id}'>" . esc_html($button_data['label']) . "</span>";
    }
    
    $button .= "</div>";

    return $style . $button;
}
add_shortcode('btnRecPlusPlus', 'custom_button_shortcode');

// Adicionar scripts e estilos no admin
function enqueue_button_admin_scripts($hook) {
    $screen = get_current_screen();
    if ($screen->post_type === 'custom_button') {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }
}
add_action('admin_enqueue_scripts', 'enqueue_button_admin_scripts');

// Adiciona o shortcode e o CSS ao WordPress
function custom_checkbox_shortcode($atts, $content = null) {
    // Adiciona o CSS inline ao rodapé
    add_action('wp_footer', function () {
        echo '<style>
            .custom-checkbox-item {
                display: flex;
                align-items: center;
                margin-bottom: 8px; /* Espaço entre os itens */
                padding-bottom: 8px; /* Margem inferior interna */
                border-bottom: 1px solid #e0e0e0; /* Linha fina para separar os itens */
            }

            .checkbox-icon {
                color: #28a745; /* Verde mais moderno */
                font-size: 18px; /* Tamanho do ícone ajustado */
                margin-right: 8px; /* Espaço menor entre o ícone e o texto */
                flex-shrink: 0;
            }

            .checkbox-text {
                font-size: 15px; /* Texto um pouco menor para equilíbrio visual */
                color: #444; /* Cinza escuro para contraste suave */
                line-height: 1.4; /* Altura de linha ajustada */
            }

            @media (max-width: 768px) {
                .custom-checkbox-item {
                    flex-direction: row;
                    align-items: flex-start;
                    margin-bottom: 6px; /* Menor margem entre os itens em telas menores */
                    padding-bottom: 6px;
                }

                .checkbox-icon {
                    font-size: 16px; /* Ícone levemente menor para mobile */
                    margin-right: 6px; /* Ajuste do espaçamento no mobile */
                }

                .checkbox-text {
                    font-size: 14px; /* Texto menor em dispositivos móveis */
                }
            }
        </style>';
    });

    // Retorna o HTML do shortcode
    return '<div class="custom-checkbox-item">
                <span class="checkbox-icon">✔</span>
                <span class="checkbox-text">' . do_shortcode($content) . '</span>
            </div>';
}
add_shortcode('checkbox', 'custom_checkbox_shortcode');

/*modelo anterior, mas precisamos poius temos muitos posts nele*/ 

