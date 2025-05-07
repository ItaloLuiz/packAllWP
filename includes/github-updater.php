<?php
/**
 * GitHub Updater
 * 
 * Permite atualização automática do plugin diretamente do GitHub
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

class PackAllWP_GitHub_Updater {
    /**
     * Instância única
     */
    private static $instance = null;
    
    /**
     * Dados do plugin
     */
    private $plugin_slug;
    private $plugin_name;
    private $version;
    private $github_repo;
    private $github_username;
    private $github_api_url;
    private $plugin_file;

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
        // Configurações do plugin
        $this->plugin_slug = 'pack-all-wp';
        $this->plugin_name = 'packAllWP';
        $this->version = PACK_ALL_WP_VERSION;
        $this->github_username = 'seu-usuario-github'; // Altere para seu usuário GitHub
        $this->github_repo = 'packAllWP'; // Altere para o nome do seu repositório
        $this->github_api_url = 'https://api.github.com/repos/' . $this->github_username . '/' . $this->github_repo . '/releases/latest';
        $this->plugin_file = plugin_basename(PACK_ALL_WP_DIR . 'packAllWP.php');

        // Hooks para verificar atualizações
        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_updates'));
        add_filter('plugins_api', array($this, 'plugin_info'), 20, 3);
        add_filter('upgrader_post_install', array($this, 'post_install'), 10, 3);
    }

    /**
     * Verifica se há atualizações disponíveis
     */
    public function check_for_updates($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        // Obter informações do GitHub
        $remote_info = $this->get_remote_info();
        
        // Verificar se há uma nova versão
        if ($remote_info && version_compare($this->version, $remote_info->tag_name, '<')) {
            $obj = new stdClass();
            $obj->slug = $this->plugin_slug;
            $obj->new_version = $remote_info->tag_name;
            $obj->url = $remote_info->html_url;
            $obj->package = $remote_info->zipball_url;
            $obj->plugin = $this->plugin_file;
            $transient->response[$this->plugin_file] = $obj;
        }

        return $transient;
    }

    /**
     * Recupera informações do repositório GitHub
     */
    private function get_remote_info() {
        // Verificar se já temos informações em cache
        $info = get_transient('packallwp_github_info');
        
        if (false === $info) {
            // Fazer requisição para a API do GitHub
            $response = wp_remote_get($this->github_api_url, array(
                'headers' => array(
                    'Accept' => 'application/vnd.github.v3+json',
                    'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url')
                )
            ));

            // Verificar erro
            if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
                return false;
            }

            // Obter corpo da resposta e decodificar JSON
            $info = json_decode(wp_remote_retrieve_body($response));
            
            // Salvar em cache por 6 horas
            set_transient('packallwp_github_info', $info, 6 * HOUR_IN_SECONDS);
        }

        return $info;
    }

    /**
     * Informações do plugin para tela de atualização
     */
    public function plugin_info($result, $action, $args) {
        // Verificar se estamos requisitando informações sobre nosso plugin
        if ('plugin_information' !== $action || $args->slug !== $this->plugin_slug) {
            return $result;
        }

        // Obter informações do GitHub
        $remote_info = $this->get_remote_info();
        
        if (!$remote_info) {
            return $result;
        }

        // Montar informações para a tela de atualização
        $plugin_info = new stdClass();
        $plugin_info->name = $this->plugin_name;
        $plugin_info->slug = $this->plugin_slug;
        $plugin_info->version = $remote_info->tag_name;
        $plugin_info->author = $this->github_username;
        $plugin_info->homepage = $remote_info->html_url;
        $plugin_info->requires = '5.0'; // Versão mínima do WordPress
        $plugin_info->tested = '6.5'; // Versão testada do WordPress
        $plugin_info->downloaded = 0;
        $plugin_info->last_updated = $remote_info->published_at;
        $plugin_info->sections = array(
            'description' => $remote_info->body,
            'changelog' => '<h4>Changelog</h4><pre>' . $remote_info->body . '</pre>'
        );
        $plugin_info->download_link = $remote_info->zipball_url;

        return $plugin_info;
    }

    /**
     * Ações após a instalação
     */
    public function post_install($true, $hook_extra, $result) {
        // Verificar se é nosso plugin
        if (isset($hook_extra['plugin']) && $hook_extra['plugin'] === $this->plugin_file) {
            // Garantir que o diretório correto está em uso
            global $wp_filesystem;
            $plugin_folder = WP_PLUGIN_DIR . '/' . dirname($this->plugin_file);
            $wp_filesystem->move($result['destination'], $plugin_folder);
            $result['destination'] = $plugin_folder;
        }

        return $result;
    }
}

// Inicializar o GitHub Updater se estiver configurado
if (defined('PACK_ALL_WP_GITHUB_UPDATER') && PACK_ALL_WP_GITHUB_UPDATER) {
    function pack_all_wp_github_updater_init() {
        return PackAllWP_GitHub_Updater::instance();
    }
    
    pack_all_wp_github_updater_init();
}