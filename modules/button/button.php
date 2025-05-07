<?php
/**
 * Módulo: Button
 * Descrição: Insere botões personalizados usando shortcode
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe responsável pelo módulo Button
 */
class PackAllWP_Button {
    
    /**
     * Instância única
     */
    private static $instance = null;
    
    /**
     * Modelos de botões disponíveis
     */
    private $button_models = array();
    
    /**
     * Retorna instância única da classe (Singleton)
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Construtor
     */
    private function __construct() {
        // Registrar shortcode
        add_shortcode('button', array($this, 'button_shortcode'));
        
        // Registrar scripts e estilos
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Inicializar modelos de botões
        $this->init_button_models();
    }
    
    /**
     * Inicializa os modelos de botões disponíveis
     */
    private function init_button_models() {
        $this->button_models = array(
            'padrao' => array(
                'class' => 'btn-padrao',
                'description' => __('Botão padrão com estilo básico', 'pack-all-wp')
            ),
            'primario' => array(
                'class' => 'btn-primario',
                'description' => __('Botão de destaque primário', 'pack-all-wp')
            ),
            'secundario' => array(
                'class' => 'btn-secundario',
                'description' => __('Botão de destaque secundário', 'pack-all-wp')
            ),
            'sucesso' => array(
                'class' => 'btn-sucesso',
                'description' => __('Botão verde para ações de sucesso', 'pack-all-wp')
            ),
            'alerta' => array(
                'class' => 'btn-alerta',
                'description' => __('Botão amarelo para alertas', 'pack-all-wp')
            ),
            'perigo' => array(
                'class' => 'btn-perigo',
                'description' => __('Botão vermelho para ações críticas', 'pack-all-wp')
            ),
            'info' => array(
                'class' => 'btn-info',
                'description' => __('Botão azul para informações', 'pack-all-wp')
            ),
            'fantasma' => array(
                'class' => 'btn-fantasma',
                'description' => __('Botão transparente com borda', 'pack-all-wp')
            ),
            'link' => array(
                'class' => 'btn-link',
                'description' => __('Botão com aparência de link', 'pack-all-wp')
            ),
        );
    }
    
    /**
     * Implementação do shortcode [button]
     */
    public function button_shortcode($atts) {
        $atts = shortcode_atts(array(
            'url' => '#',
            'texto' => __('Clique Aqui', 'pack-all-wp'),
            'modelo' => 'padrao',
            'id' => '',
            'class' => '',
            'target' => '_self',
            'rel' => '',
            'label' => ''
        ), $atts, 'button');
        
        // Verificar se o modelo existe, caso contrário usar o padrão
        $modelo = isset($this->button_models[$atts['modelo']]) ? $atts['modelo'] : 'padrao';
        
        // Preparar classes do botão
        $classes = array('pack-all-wp-btn', $this->button_models[$modelo]['class']);
        
        // Adicionar classes personalizadas se fornecidas
        if (!empty($atts['class'])) {
            $classes[] = esc_attr($atts['class']);
        }
        
        // Montar atributos do botão
        $button_attrs = array(
            'href' => esc_url($atts['url']),
            'class' => esc_attr(implode(' ', $classes)),
            'target' => esc_attr($atts['target']),
        );
        
        // Adicionar ID se fornecido
        if (!empty($atts['id'])) {
            $button_attrs['id'] = esc_attr($atts['id']);
        }
        
        // Adicionar rel se fornecido
        if (!empty($atts['rel'])) {
            $button_attrs['rel'] = esc_attr($atts['rel']);
        }
        
        // Adicionar noopener noreferrer para links externos
        if ($atts['target'] == '_blank') {
            $button_attrs['rel'] = !empty($button_attrs['rel']) ? $button_attrs['rel'] . ' noopener noreferrer' : 'noopener noreferrer';
        }
        
        // Construir string de atributos
        $attributes = '';
        foreach ($button_attrs as $key => $value) {
            $attributes .= ' ' . $key . '="' . $value . '"';
        }
        
        // Construir HTML do botão com label abaixo se fornecido
        $button_html = sprintf(
            '<div class="pack-all-wp-btn-container">
                <a%s>%s</a>
                %s
            </div>',
            $attributes,
            esc_html($atts['texto']),
            !empty($atts['label']) ? '<span class="pack-all-wp-btn-label">' . esc_html($atts['label']) . '</span>' : ''
        );
        
        return $button_html;
    }
    
    /**
     * Registra scripts e estilos
     */
    public function enqueue_scripts() {
        // Registrar e enfileirar CSS
        wp_register_style(
            'pack-all-wp-button',
            PACK_ALL_WP_URL . 'modules/button/assets/css/button.css',
            array(),
            PACK_ALL_WP_VERSION
        );
        
        // Carregamos o CSS apenas quando necessário
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'button')) {
            wp_enqueue_style('pack-all-wp-button');
        }
    }
    
    /**
     * Obtém os modelos de botões disponíveis
     */
    public function get_button_models() {
        return $this->button_models;
    }
}

// Iniciar o módulo
function pack_all_wp_button_init() {
    return PackAllWP_Button::instance();
}

// Inicializar o módulo Button
pack_all_wp_button_init();

// Criar diretório para assets se não existir
if (!file_exists(PACK_ALL_WP_DIR . 'modules/button/assets/css/')) {
    mkdir(PACK_ALL_WP_DIR . 'modules/button/assets/css/', 0755, true);
}

// Adicionar CSS para o label do botão
function pack_all_wp_button_add_label_css() {
    $custom_css = "
    .pack-all-wp-btn-container {
        display: inline-block;
        text-align: center;
        margin-bottom: 10px;
    }
    .pack-all-wp-btn-label {
        display: block;
        font-size: 0.8em;
        margin-top: 5px;
        color: #666;
        line-height: 1.2;
    }
    ";
    
    // Verificar se existe algum arquivo CSS para o botão
    $css_file = PACK_ALL_WP_DIR . 'modules/button/assets/css/button.css';
    
    // Se o arquivo existir, adicionar o CSS personalizado
    if (file_exists($css_file)) {
        file_put_contents($css_file, $custom_css, FILE_APPEND);
    } else {
        // Caso contrário, criar o arquivo
        file_put_contents($css_file, $custom_css);
    }
}

// Executar ao ativar o plugin
register_activation_hook(__FILE__, 'pack_all_wp_button_add_label_css');