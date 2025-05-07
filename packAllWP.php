<?php
/**
 * Plugin Name: packAllWP
 * Plugin URI: https://github.com/ItaloLuiz/packAllWP
 * Description: Plugin modular com múltiplas funcionalidades
 * Version: 1.0.0
 * Author: ítalo
 * Author URI: https://github.com/ItaloLuiz/packAllWP
 * Text Domain: pack-all-wp
 * License: GPL v2 or later
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes
define('PACK_ALL_WP_VERSION', '1.0.0');
define('PACK_ALL_WP_DIR', plugin_dir_path(__FILE__));
define('PACK_ALL_WP_URL', plugin_dir_url(__FILE__));
define('PACK_ALL_WP_GITHUB_UPDATER', true); // Ative para suporte a atualizações via GitHub

/**
 * Classe principal do plugin
 */
class PackAllWP {
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
        // Inicializar ações e filtros principais
        add_action('plugins_loaded', array($this, 'load_modules'));
        add_action('admin_menu', array($this, 'register_admin_menu'));
    }

    /**
     * Carrega todos os módulos do plugin
     */
    public function load_modules() {
        // Carregar GitHub Updater
        if (file_exists(PACK_ALL_WP_DIR . 'includes/github-updater.php')) {
            require_once PACK_ALL_WP_DIR . 'includes/github-updater.php';
        }
        
        // Carrega o módulo Quiz Hub
        if (file_exists(PACK_ALL_WP_DIR . 'modules/quiz-hub/quiz-hub.php')) {
            require_once PACK_ALL_WP_DIR . 'modules/quiz-hub/quiz-hub.php';
        }
        
        // Carrega o módulo Button
        if (file_exists(PACK_ALL_WP_DIR . 'modules/button/button.php')) {
            require_once PACK_ALL_WP_DIR . 'modules/button/button.php';
        }

        // Carrega o módulo Expansivel
        if (file_exists(PACK_ALL_WP_DIR . 'modules/expansivel/expansivel.php')) {
            require_once PACK_ALL_WP_DIR . 'modules/expansivel/expansivel.php';
        }

        // Carrega o módulo Expansivel
        if (file_exists(PACK_ALL_WP_DIR . 'modules/boxcard/boxcard.php')) {
            require_once PACK_ALL_WP_DIR . 'modules/boxcard/boxcard.php';
        }

         // Carrega o módulo slides
         if (file_exists(PACK_ALL_WP_DIR . 'modules/slides/slides.php')) {
            require_once PACK_ALL_WP_DIR . 'modules/slides/slides.php';
        }

        // Carrega o módulo btn_rec_plus
        if (file_exists(PACK_ALL_WP_DIR . 'modules/btn_rec_plus/btn_rec_plus.php')) {
            require_once PACK_ALL_WP_DIR . 'modules/btn_rec_plus/btn_rec_plus.php';
        }

        // Carrega o módulo btn_rec
        if (file_exists(PACK_ALL_WP_DIR . 'modules/btn_rec/btn_rec.php')) {
            require_once PACK_ALL_WP_DIR . 'modules/btn_rec/btn_rec.php';
        }
        
        // Outros módulos serão carregados aqui no futuro
    }

    /**
     * Registra o menu de administração
     */
    public function register_admin_menu() {
        add_menu_page(
            'packAllWP', 
            'packAllWP', 
            'manage_options', 
            'pack-all-wp', 
            array($this, 'admin_page'),
            'dashicons-admin-plugins',
            99
        );
    }

    /**
     * Página de administração
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <p><?php _e('Bem-vindo ao packAllWP! Plugin modular com múltiplas funcionalidades.', 'pack-all-wp'); ?></p>
            
            <div class="modules-list">
                <h2><?php _e('Módulos Disponíveis', 'pack-all-wp'); ?></h2>
                
                <div class="module-item">
                    <h3><?php _e('Quiz Hub', 'pack-all-wp'); ?></h3>
                    <p><?php _e('Permite renderizar quiz externo através de shortcode.', 'pack-all-wp'); ?></p>
                    <code>[quizHub url="https://url-que-sera-pega.com"]</code>
                </div>
                
                <div class="module-item">
                    <h3><?php _e('Buttons', 'pack-all-wp'); ?></h3>
                    <p><?php _e('Cria botões personalizados usando shortcode.', 'pack-all-wp'); ?></p>
                    <code>[button url="" texto="" modelo=""]</code>
                </div>
            </div>
        </div>
        <?php
    }
}

// Inicializar o plugin
function pack_all_wp_init() {
    return PackAllWP::instance();
}

// Começar!
pack_all_wp_init();