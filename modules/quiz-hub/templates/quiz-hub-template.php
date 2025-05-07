<?php
/**
 * Template Name: Quiz Hub Template
 * 
 * Template para páginas com Quiz Hub - versão totalmente limpa
 */

if (!defined('ABSPATH')) exit;

// Desabilitar todas as saídas buffer
while (ob_get_level()) {
    ob_end_clean();
}

// Desativar qualquer output do tema
remove_all_actions('wp_head');
remove_all_actions('wp_footer');

// Iniciar novo buffer de saída
ob_start();

if (have_posts()) {
    while (have_posts()) {
        the_post();
        echo do_shortcode(get_the_content());
    }
}

// Obter o conteúdo e limpar o buffer
$content = ob_get_clean();

// Enviar o conteúdo diretamente ao navegador
echo $content;

// Terminar a execução
exit;