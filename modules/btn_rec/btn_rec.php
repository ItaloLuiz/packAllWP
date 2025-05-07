<?php
/**
 * Módulo: btn rec
 * Descrição: shortcode de botão
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

function btn_rec($atts, $content = null) {
    // Definir atributos padrão
    $atts = shortcode_atts(array(
        'texto_btn' => 'Clique Aqui',
        'url' => '#',
        'label' => '',
        'estilo' => 'padrao', // Novo: seleciona o estilo do botão
    ), $atts, 'btnRec');
    
    // Escapar valores para segurança
    $url = !empty($atts['url']) ? esc_url($atts['url']) : '#';
    $texto = !empty($atts['texto_btn']) ? esc_html($atts['texto_btn']) : 'Clique Aqui';
    $label = !empty($atts['label']) ? esc_html($atts['label']) : '';
    $estilo = $atts['estilo'];
    
    // Definir estilos pré-configurados em um array
    $estilos = array(
        // Estilo padrão (azul)
        'padrao' => array(
            'bg' => '#3366FF',
            'color' => '#ffffff',
            'hover_bg' => '#2855CC',
            'border_radius' => '4px',
            'padding' => '12px 24px',
            'width' => 'auto',
        ),
        // Estilo para botão de destaque (verde)
        'destaque' => array(
            'bg' => '#4CAF50',
            'color' => '#ffffff',
            'hover_bg' => '#3E8E41',
            'border_radius' => '4px',
            'padding' => '14px 28px',
            'width' => 'auto',
        ),
        // Estilo para botão largo (ocupando toda a largura)
        'largo' => array(
            'bg' => '#3366FF',
            'color' => '#ffffff',
            'hover_bg' => '#2855CC',
            'border_radius' => '4px',
            'padding' => '16px 32px',
            'width' => '100%',
        ),
        // Estilo minimalista
        'minimalista' => array(
            'bg' => '#ffffff',
            'color' => '#333333',
            'hover_bg' => '#f0f0f0',
            'border_radius' => '0',
            'padding' => '10px 20px',
            'width' => 'auto',
            'border' => '1px solid #333333',
        ),
        // Estilo vermelho (call to action forte)
        'alerta' => array(
            'bg' => '#FF5733',
            'color' => '#ffffff',
            'hover_bg' => '#E64A2E',
            'border_radius' => '4px',
            'padding' => '12px 24px',
            'width' => 'auto',
        ),
    );
    
    // Obter o estilo selecionado ou usar o padrão se não existir
    $estilo_aplicar = isset($estilos[$estilo]) ? $estilos[$estilo] : $estilos['padrao'];
    
    // ID único para o botão
    $unique_id = 'btn_' . rand(100, 999);
    
    // Montar o CSS inline
    $css = '<style>
        #' . $unique_id . ' {
            display: inline-block;
            background-color: ' . $estilo_aplicar['bg'] . ';
            color: ' . $estilo_aplicar['color'] . ' !important;
            border-radius: ' . $estilo_aplicar['border_radius'] . ';
            padding: ' . $estilo_aplicar['padding'] . ';
            width: ' . $estilo_aplicar['width'] . ';
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s;
            font-size: 16px;
            cursor: pointer;
            ' . (isset($estilo_aplicar['border']) ? 'border: ' . $estilo_aplicar['border'] . ';' : 'border: none;') . '
        }
        
        #' . $unique_id . ':hover {
            background-color: ' . $estilo_aplicar['hover_bg'] . ';
        }
        
        .btn-container-' . $unique_id . ' {
            text-align: center;
            margin: 15px 0;
            width: 100%;
        }
        
        .btn-label-' . $unique_id . ' {
            text-align: center;
            margin-top: 5px;
            font-size: 14px;
            color: #666;
        }
    </style>';
    
    // Montar o HTML do botão
    $html = '<div class="btn-container-' . $unique_id . '">
        <a id="' . $unique_id . '" href="' . $url . '" class="btn">' . $texto . '</a>';
    
    // Adicionar o label se existir
    if (!empty($label)) {
        $html .= '<div class="btn-label-' . $unique_id . '">' . $label . '</div>';
    }
    
    $html .= '</div>';
    
    // Retornar o CSS + HTML
    return $css . $html;
}

add_shortcode('btnRec', 'btn_rec');