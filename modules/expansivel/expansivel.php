<?php
/**
 * Módulo: Expansivel
 * Descrição: Insere funcionalidade para mostra parte do texto após o clique
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/*expansivel*/
function elemento_expansivel_shortcode($atts, $content = null) {
    // Extrair atributos
    $a = shortcode_atts(array(
        'titulo' => 'Clique para expandir',
        'cor_fundo' => '#f9f9f9',
        'cor_texto' => '#333333',
        'cor_borda' => '#dddddd',
		'cor_bg' => '#dddddd'
    ), $atts);

    // Gerar ID único
    $id = 'expansivel-' . uniqid();

    // Calcular cor de hover
    $cor_hover = adjustBrightness($a['cor_fundo'], -10);

    // HTML e CSS inline para o elemento expansível
    $output = '
    <style>
        .expansivel-container-' . $id . ' {
            border: 1px solid ' . esc_attr($a['cor_borda']) . ';
            margin-bottom: 10px;
            border-radius: 4px;
            overflow: hidden;
			box-shadow: 0px 10px 15px -3px rgba(0,0,0,0.1);
        }
        .expansivel-titulo-' . $id . ' {
            background-color: ' . esc_attr($a['cor_fundo']) . ';
            color: ' . esc_attr($a['cor_texto']) . ';
            padding: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: background-color 0.3s ease;
        }
        .expansivel-titulo-' . $id . ':hover {
            background-color: ' . $cor_hover . ';
        }
        .expansivel-titulo-' . $id . ' .icon {
            margin-right: 10px;
            font-weight: bold;
            font-size: 18px;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.5s ease;
            position: relative;
        }
        .expansivel-titulo-' . $id . ' .icon::before,
        .expansivel-titulo-' . $id . ' .icon::after {
            content: "";
            position: absolute;
            background-color: ' . esc_attr($a['cor_texto']) . ';
            transition: all 0.5s ease;
        }
        .expansivel-titulo-' . $id . ' .icon::before {
            width: 2px;
            height: 16px;
        }
        .expansivel-titulo-' . $id . ' .icon::after {
            width: 16px;
            height: 2px;
        }
        .expansivel-titulo-' . $id . '.ativo .icon {
            transform: rotate(180deg);
        }
        .expansivel-titulo-' . $id . '.ativo .icon::before {
            opacity: 0;
        }
        .expansivel-conteudo-' . $id . ' {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease;
            background-color: ' . esc_attr($a['cor_bg']) . ';
            color: ' . esc_attr($a['cor_texto']) . ';
        }
        .expansivel-conteudo-inner-' . $id . ' {
            padding: 15px;
        }
    </style>
    <div class="expansivel-container-' . $id . '">
        <div class="expansivel-titulo-' . $id . '" onclick="toggleConteudo(\'' . $id . '\')">
            <div class="icon"></div>
            ' . esc_html($a['titulo']) . '
        </div>
        <div id="' . $id . '" class="expansivel-conteudo-' . $id . '">
            <div class="expansivel-conteudo-inner-' . $id . '">' . do_shortcode($content) . '</div>
        </div>
    </div>
    <script>
    function toggleConteudo(id) {
        var conteudo = document.getElementById(id);
        var titulo = conteudo.previousElementSibling;
        if (conteudo.style.maxHeight) {
            conteudo.style.maxHeight = null;
            titulo.classList.remove("ativo");
        } else {
            conteudo.style.maxHeight = conteudo.scrollHeight + "px";
            titulo.classList.add("ativo");
        }
    }
    </script>
    ';

    return $output;
}
add_shortcode('expansivel', 'elemento_expansivel_shortcode');

// Função auxiliar para ajustar o brilho da cor
function adjustBrightness($hex, $steps) {
    $hex = ltrim($hex, '#');
    $r = max(0, min(255, hexdec(substr($hex, 0, 2)) + $steps));
    $g = max(0, min(255, hexdec(substr($hex, 2, 2)) + $steps));
    $b = max(0, min(255, hexdec(substr($hex, 4, 2)) + $steps));
    return sprintf("#%02x%02x%02x", $r, $g, $b);
}
/*expansivel*/