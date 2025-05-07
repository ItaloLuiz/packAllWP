<?php
/**
 * Módulo: Quiz Hub
 * Descrição: Renderiza quiz externo através de shortcode e cria modelo de página
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe responsável pelo módulo Quiz Hub
 */
class PackAllWP_QuizHub {
    
    /**
     * Instância única
     */
    private static $instance = null;
    
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
        // Garantir que os diretórios necessários existam
        $this->ensure_directories();
        
        // Registrar ações e filtros
        add_action('init', array($this, 'register_page_template'));
        add_filter('template_include', array($this, 'load_quiz_hub_template'));
        add_shortcode('quizHub', array($this, 'quiz_hub_shortcode'));
    }
    
    /**
     * Garantir que os diretórios necessários existam
     */
    private function ensure_directories() {
        // Criar diretório para templates se não existir
        $template_dir = PACK_ALL_WP_DIR . 'modules/quiz-hub/templates/';
        if (!file_exists($template_dir)) {
            wp_mkdir_p($template_dir);
            
            // Criar o arquivo de template se não existir
            $template_file = $template_dir . 'quiz-hub-template.php';
            if (!file_exists($template_file)) {
                $template_content = $this->get_default_template_content();
                file_put_contents($template_file, $template_content);
            }
        }
    }
    
    /**
     * Conteúdo padrão para o template (versão clean que apenas exibe o conteúdo)
     */
    private function get_default_template_content() {
        return '<?php
/**
 * Template Name: Quiz Hub Template
 * 
 * Template para páginas com Quiz Hub - versão totalmente limpa
 */

if (!defined(\'ABSPATH\')) exit;

// Desabilitar todas as saídas buffer
while (ob_get_level()) {
    ob_end_clean();
}

// Desativar qualquer output do tema
remove_all_actions(\'wp_head\');
remove_all_actions(\'wp_footer\');

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
';
    }
    
    /**
     * Registra modelo de página
     */
    public function register_page_template() {
        // Adicionar modelo ao tema atual
        add_filter('theme_page_templates', array($this, 'add_quiz_hub_template'));
    }
    
    /**
     * Adiciona template ao dropdown de modelos de página
     */
    public function add_quiz_hub_template($templates) {
        $templates['quiz-hub-template.php'] = __('Quiz Hub Template (Primeiro HUB)', 'pack-all-wp');
        return $templates;
    }
    
    /**
     * Carrega o template para o modelo de página selecionado
     */
    public function load_quiz_hub_template($template) {
        if (is_page()) {
            $page_template = get_post_meta(get_the_ID(), '_wp_page_template', true);
            
            if ('quiz-hub-template.php' === $page_template) {
                $template_path = PACK_ALL_WP_DIR . 'modules/quiz-hub/templates/quiz-hub-template.php';
                
                if (file_exists($template_path)) {
                    return $template_path;
                } else {
                    // Log para depuração
                    error_log('Quiz Hub: Template não encontrado em: ' . $template_path);
                }
            }
        }
        
        return $template;
    }
    
    /**
     * Implementação do shortcode [quizHub]
     * Carrega conteúdo externo diretamente, sem modificações
     */
    public function quiz_hub_shortcode($atts) {
        $atts = shortcode_atts(array(
            'url' => '',
        ), $atts, 'quizHub');
        
        // Validar URL
        if (empty($atts['url']) || !filter_var($atts['url'], FILTER_VALIDATE_URL)) {
            return '<p class="error">' . __('URL inválida ou não fornecida.', 'pack-all-wp') . '</p>';
        }
        
        // Buscar o conteúdo do URL remoto
        $response = wp_remote_get($atts['url'], array(
            'timeout' => 30, // Aumentar timeout para URLs que podem demorar
            'sslverify' => false // Se necessário para ambientes de desenvolvimento
        ));
        
        // Verificar se houve erro na requisição
        if (is_wp_error($response)) {
            return '<p class="error">' . sprintf(
                __('Erro ao carregar o conteúdo: %s', 'pack-all-wp'),
                $response->get_error_message()
            ) . '</p>';
        }
        
        // Verificar código de status HTTP
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            return '<p class="error">' . sprintf(
                __('Erro ao carregar o conteúdo. Código de status: %s', 'pack-all-wp'),
                $status_code
            ) . '</p>';
        }
        
        // Obter o corpo da resposta
        $body = wp_remote_retrieve_body($response);
        
        // Se o corpo estiver vazio, retornar mensagem de erro
        if (empty($body)) {
            return '<p class="error">' . __('Conteúdo vazio retornado do servidor.', 'pack-all-wp') . '</p>';
        }
        
        // Retornar o conteúdo exato como recebido, sem modificações
        return $body;
    }
}

// Iniciar o módulo
function pack_all_wp_quiz_hub_init() {
    return PackAllWP_QuizHub::instance();
}

// Inicializar o módulo Quiz Hub
pack_all_wp_quiz_hub_init();