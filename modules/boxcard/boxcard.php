<?php
/**
 * Módulo: Expansivel
 * Descrição: Insere funcionalidade para mostra parte do texto após o clique
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}


function boxcard($atts = array())
  {
    $textolegenda_card    =  get_post_meta( get_the_ID(), 'box_info_card_poststexto-superior-do-box', true );
    $titulo_do_box_card   = get_post_meta( get_the_ID(), 'box_info_card_poststitulo-do-box', true );
    $caracteristicas_card = get_post_meta( get_the_ID(), 'box_info_card_postscaracteristicas-do-cartaoseparado-por-virgula', true );
    //$facilidades_cartao_texto_fixo = 'Accede a los beneficios:';
	$facilidades_cartao_texto_fixo = get_post_meta( get_the_ID(), 'box_info_card_titulo-caracteristicas', true );
    $texto_btn_card = get_post_meta( get_the_ID(), 'box_info_card_poststexto-do-botao', true );
    $link_btn_card  = get_post_meta( get_the_ID(), 'box_info_card_postslink-do-botao', true );
    $cor_do_botao  = get_post_meta( get_the_ID(), 'box_info_card_postscor-do-botao', true );
   
    $imagem_card               = get_post_meta( get_the_ID(), 'box_info_card_postsimagem-do-cartao', true );
    
    //$texto_btn_card = 'CONOCE MÁS';

    $caracteristicas_card = explode(',',$caracteristicas_card);
    $div_caracteristicas = '';
    foreach($caracteristicas_card as $caracteristica){
      $div_caracteristicas   .= '<div class="caracteristica-b-card">';
        $div_caracteristicas .= '<div>' . $caracteristica . '</div>';
      $div_caracteristicas   .= '</div>';
    }
	
	if(empty(get_post_meta( get_the_ID(), 'box_info_texto_redirect', true ))){		
   	   $texto_label_btn_card_fixo = 'Permanecerás en el sitio actual.';
	}else{
		$texto_label_btn_card_fixo  = get_post_meta( get_the_ID(), 'box_info_texto_redirect', true );
	}

    $html = '<style>';
    $html .= '
      .container-box-card{
        width:100%;
        height:auto;
        min-height:250px;
        overflow:hidden;
        position:relative;
        padding:2px;
        background-color:#efefef;
        border:solid 1px #eeeeee;
        border-radius:5px;
        display:grid;
        grid-template-columns: repeat(12,1fr);  
		margin-bottom:20px !important;
		margin-top:10px !important;
        box-shadow:0 2.8px 2.2px rgb(0 0 0 / 3%), 0 6.7px 5.3px rgb(0 0 0 / 5%), 0 12.5px 10px rgb(0 0 0 / 6%) !important;
      }
    ';

    $html .= '
      .bloco-img-card{
        grid-column: span 5;
        padding:8px 4px 4px 8px;
        overflow:hidden;  
        border-radius:4px;  
      }
      .bloco-img-card img{
        width:98%;
        position:relative;   
        border-radius:4px;
      }
    ';

    $html .= '
      .bloco-detalhes-card{
        grid-column: span 7;       
        padding:8px 4px 4px 8px;
      }
    ';

    $html .= '
      .texto-legenda-card{
        font-size:12px;
        font-weight:300;
      }
    ';

    $html .= '
      .titulo-b-card{
        font-size:18px;
        font-weight:600;
        margin-bottom:0px;
      }
    ';

    $html .= '
      .caracteristica-b-card{
        font-size:11px;
        font-weight:600;
        padding:2px 8px 1px 8px;
        background-color:#414141;
        color:#ffffff;
        display:inline-block;
        margin-right:4px;
      }
    ';

    $html .= '
    .facilidades_cartao_texto_fixo{
      font-size:16px;
      margin-top:8px;
    }
    ';

    $html .= '
      .bloco-bloco-btn-card{
        position:absolute;
        right:20px;
        margin-top:30px;
        margin-bottom:4px;
       }
      .bloco-bloco-btn-card a{    
        padding:10px;
        padding-right:40px;
        padding-left:40px;
        background-color:#333;
        color:#fff !important;
        text-decoration:none !important;
        border-radius:4px;
        border-bottom:none !important;
        box-shadow:0 2.8px 2.2px rgb(0 0 0 / 3%), 0 6.7px 5.3px rgb(0 0 0 / 5%), 0 12.5px 10px rgb(0 0 0 / 6%) !important;
        }
        .bloco-bloco-btn-card a:hover{
            background-color:#333 !important;
            color:#fff !important;
            text-decoration:none !important;
        }

    ';

    $html .= '
      .texto_label_btn_card_fixo{
        position:relative;
        margin-top:4px;
        text-align:center;
        font-size:10px;
        margin-bottom:10px;
        padding-bottom:10px;
        }
    ';

    $html .= '
        .bloco-btn-card{background-color: '.$cor_do_botao.' !important;}
    ';
    /**responsivo**/



    $html .= '
      @media (max-width: 600px){
        .bloco-bloco-btn-card{
          position:relative; 
          display:block;       
          text-align:center;      
          right:0px;
        }
        .bloco-detalhes-card{
          text-align:center;

        }
      }
    ';

    $html .= '
      @media (max-width: 600px){
          .bloco-img-card{
            grid-column: span 12;
            padding:8px 4px 4px 8px;
            overflow:hidden;  
            border-radius:4px;  
          }
          .bloco-img-card img{
            width:98%;
            position:relative;   
            border-radius:4px;
          }
        }
    ';

    $html .= '
      @media (max-width: 600px){
          .bloco-detalhes-card{
            grid-column: span 12;       
            padding:8px 4px 4px 8px;
          }
        }
    ';

    $html .= '
      @media (max-width: 600px){
          
            @keyframes ring {
            0% {
              padding:10px;
              padding-right:10px;
              padding-left:10px;
            }
            50% {
              padding:10px;
              padding-right:40px;
              padding-left:40px;
            }
            100% {
              padding:10px;
              padding-right:10px;
              padding-left:10px;
            }
            } 
        }
    ';
      /**responsivo**/


      $html .= '</style>';


      $html .= '<div class="container-box-card">';//inicio div central

      $html .= '<div class="bloco-img-card">';
            $html .= '<img src="'.$imagem_card.'" alt="">';
      $html .= '</div>';

      $html .= '<div class="bloco-detalhes-card">';

            $html .= '<div class="texto-legenda-card">';
              $html .= '<div>' . $textolegenda_card . '</div>';
            $html .= '</div>';

            $html .= '<div class="titulo-b-card">';
              $html .= '<div>' . $titulo_do_box_card . '</div>';
            $html .= '</div>';
            

            $html .= '<div class="facilidades_cartao_texto_fixo">';
                  $html .= '<div>' . $facilidades_cartao_texto_fixo . '</div>';
            $html .= '</div>';
    
           $html .= $div_caracteristicas;

            $html .= '<div class="bloco-bloco-btn-card">';
              $html .= '<a class="bloco-btn-card" href="'.$link_btn_card.'">' . $texto_btn_card . '</a>';
              $html .= '<div class="texto_label_btn_card_fixo">' . $texto_label_btn_card_fixo . '</div>';
            $html .= '</div>';

            


      $html .= '</div>';


      $html .= '</div>';//final div central

      if(!empty($textolegenda_card)){
        return $html;
      }

      
  }
  add_shortcode('boxcard', 'boxcard');

  /*metabox cartão*/ 
  class box_info_card_posts {
    private $config = '{"title":"Box Info Cart\u00e3o","prefix":"box_info_card_posts","domain":"box_info_card_posts","class_name":"box_info_card_posts","post-type":["post"],"context":"normal","priority":"default","fields":[{"type":"text","label":"Texto Superior do box","id":"box_info_card_poststexto-superior-do-box"},{"type":"text","label":"T\u00edtulo do box","id":"box_info_card_poststitulo-do-box"},{"type":"text","label":"Título Características","id":"box_info_card_titulo-caracteristicas"},{"type":"textarea","label":"Caracter\u00edsticas do cart\u00e3o(separado por virgula)","id":"box_info_card_postscaracteristicas-do-cartaoseparado-por-virgula"},{"type":"text","label":"Texto do Bot\u00e3o","id":"box_info_card_poststexto-do-botao"},{"type":"text","label":"Texto redirect","id":"box_info_texto_redirect"},{"type":"url","label":"Link do Bot\u00e3o","id":"box_info_card_postslink-do-botao"},{"type":"color","label":"Cor do Bot\u00e3o","default":"#292929","color-picker":"1","id":"box_info_card_postscor-do-botao"},{"type":"media","label":"Imagem do Cart\u00e3o","return":"url","modal-title":"Escolha a imagem","modal-button":"Selecionar essa imagem","id":"box_info_card_postsimagem-do-cartao"}]}';
  
    public function __construct() {
      $this->config = json_decode( $this->config, true );
      add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
      add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
      add_action( 'admin_head', [ $this, 'admin_head' ] );
      add_action( 'save_post', [ $this, 'save_post' ] );
    }
  
    public function add_meta_boxes() {
      foreach ( $this->config['post-type'] as $screen ) {
        add_meta_box(
          sanitize_title( $this->config['title'] ),
          $this->config['title'],
          [ $this, 'add_meta_box_callback' ],
          $screen,
          $this->config['context'],
          $this->config['priority']
        );
      }
    }
  
    public function admin_enqueue_scripts() {
      global $typenow;
      if ( in_array( $typenow, $this->config['post-type'] ) ) {
        wp_enqueue_media();
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_style( 'wp-color-picker' );
      }
    }
  
    public function admin_head() {
      global $typenow;
      if ( in_array( $typenow, $this->config['post-type'] ) ) {
        ?><script>
    jQuery.noConflict();
    (function ($) {
        $(function () {
            $('body').on('click', '.rwp-media-toggle', function (e) {
                e.preventDefault();
                let button = $(this);
                let rwpMediaUploader = null;
                rwpMediaUploader = wp.media({
                    title: button.data('modal-title'),
                    button: {
                        text: button.data('modal-button')
                    },
                    multiple: true
                }).on('select', function () {
                    let attachment = rwpMediaUploader.state().get('selection').first()
                        .toJSON();
                    button.prev().val(attachment[button.data('return')]);
                }).open();
            });
            $('.rwp-color-picker').wpColorPicker();
        });
    })(jQuery);
</script><?php
      }
    }
  
    public function save_post( $post_id ) {
      foreach ( $this->config['fields'] as $field ) {
        switch ( $field['type'] ) {
          case 'url':
            if ( isset( $_POST[ $field['id'] ] ) ) {
              $sanitized = esc_url_raw( $_POST[ $field['id'] ] );
              update_post_meta( $post_id, $field['id'], $sanitized );
            }
            break;
          default:
            if ( isset( $_POST[ $field['id'] ] ) ) {
              $sanitized = sanitize_text_field( $_POST[ $field['id'] ] );
              update_post_meta( $post_id, $field['id'], $sanitized );
            }
        }
      }
    }
  
    public function add_meta_box_callback() {
      $this->fields_table();
    }
  
    private function fields_table() {
      ?><table class="form-table" role="presentation">
    <tbody><?php
          foreach ( $this->config['fields'] as $field ) {
            ?><tr>
            <th scope="row"><?php $this->label( $field ); ?></th>
            <td><?php $this->field( $field ); ?></td>
        </tr><?php
          }
        ?></tbody>
</table><?php
    }
  
    private function label( $field ) {
      switch ( $field['type'] ) {
        case 'media':
          printf(
            '<label class="" for="%s_button">%s</label>',
            $field['id'], $field['label']
          );
          break;
        default:
          printf(
            '<label class="" for="%s">%s</label>',
            $field['id'], $field['label']
          );
      }
    }
  
    private function field( $field ) {
      switch ( $field['type'] ) {
        case 'media':
          $this->input( $field );
          $this->media_button( $field );
          break;
        case 'textarea':
          $this->textarea( $field );
          break;
        default:
          $this->input( $field );
      }
    }
  
    private function input( $field ) {
      if ( $field['type'] === 'media' ) {
        $field['type'] = 'text';
      }
      if ( isset( $field['color-picker'] ) ) {
        $field['class'] = 'rwp-color-picker';
      }
      printf(
        '<input class="regular-text %s" id="%s" name="%s" %s type="%s" value="%s">',
        isset( $field['class'] ) ? $field['class'] : '',
        $field['id'], $field['id'],
        isset( $field['pattern'] ) ? "pattern='{$field['pattern']}'" : '',
        $field['type'],
        $this->value( $field )
      );
    }
  
    private function media_button( $field ) {
      printf(
        ' <button class="button rwp-media-toggle" data-modal-button="%s" data-modal-title="%s" data-return="%s" id="%s_button" name="%s_button" type="button">%s</button>',
        isset( $field['modal-button'] ) ? $field['modal-button'] : __( 'Select this file', 'box_info_card_posts' ),
        isset( $field['modal-title'] ) ? $field['modal-title'] : __( 'Choose a file', 'box_info_card_posts' ),
        $field['return'],
        $field['id'], $field['id'],
        isset( $field['button-text'] ) ? $field['button-text'] : __( 'Upload', 'box_info_card_posts' )
      );
    }
  
    private function textarea( $field ) {
      printf(
        '<textarea class="regular-text" id="%s" name="%s" rows="%d">%s</textarea>',
        $field['id'], $field['id'],
        isset( $field['rows'] ) ? $field['rows'] : 5,
        $this->value( $field )
      );
    }
  
    private function value( $field ) {
      global $post;
      if ( metadata_exists( 'post', $post->ID, $field['id'] ) ) {
        $value = get_post_meta( $post->ID, $field['id'], true );
      } else if ( isset( $field['default'] ) ) {
        $value = $field['default'];
      } else {
        return '';
      }
      return str_replace( '\u0027', "'", $value );
    }
  
  }
  new box_info_card_posts;

/*final metabox cartão*/