<?php
/**
 * Módulo: slides
 * Descrição: Insere slides
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

class slides_post_recomendador_leadPress {
	private $config = '{"title":"slides posts recomendador","prefix":"slides_post_recomendador_leadPress","domain":"leadPress","class_name":"slides_post_recomendador_leadPress","post-type":["post"],"context":"normal","priority":"default","fields":[{"type":"text","label":"Bloco 1","id":"slides_post_recomendador_leadPressbloco-1"},{"type":"text","label":"Bloco 2","id":"slides_post_recomendador_leadPressbloco-2"},{"type":"text","label":"Bloco 3","id":"slides_post_recomendador_leadPressbloco-3"},{"type":"text","label":"Bloco 4","id":"slides_post_recomendador_leadPressbloco-4"},{"type":"text","label":"Bloco 5","id":"slides_post_recomendador_leadPressbloco-5"}]}';

	public function __construct() {
		$this->config = json_decode( $this->config, true );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
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

	public function save_post( $post_id ) {
		foreach ( $this->config['fields'] as $field ) {
			switch ( $field['type'] ) {
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
			default:
				printf(
					'<label class="" for="%s">%s</label>',
					$field['id'], $field['label']
				);
		}
	}

	private function field( $field ) {
		switch ( $field['type'] ) {
			default:
				$this->input( $field );
		}
	}

	private function input( $field ) {
		printf(
			'<input class="regular-text %s" id="%s" name="%s" %s type="%s" value="%s">',
			isset( $field['class'] ) ? $field['class'] : '',
			$field['id'], $field['id'],
			isset( $field['pattern'] ) ? "pattern='{$field['pattern']}'" : '',
			$field['type'],
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
new slides_post_recomendador_leadPress;



// Função para renderizar o shortcode do slider
function leadPress_slider_shortcode($atts) {
    // Atributos do shortcode
    $atts = shortcode_atts(
        array(
            'post_id' => get_the_ID(),
            'autoplay' => 'true',
            'speed' => '5000',
            'animation' => 'fade'
        ),
        $atts,
        'leadPress_slider'
    );
    
    // Obter ID do post
    $post_id = intval($atts['post_id']);
    
    // Obter dados dos campos meta
    $slides = array();
    for ($i = 1; $i <= 5; $i++) {
        $slide_content = get_post_meta($post_id, "slides_post_recomendador_leadPressbloco-{$i}", true);
        if (!empty($slide_content)) {
            $slides[] = $slide_content;
        }
    }
    
    // Se não houver conteúdo, retornar vazio
    if (empty($slides)) {
        return '';
    }
    
    // Gerar ID único para o slider
    $slider_id = 'leadPress-slider-' . uniqid();
    
    // Iniciar buffer de saída
    ob_start();
    
    // HTML do slider
    ?>
    <div id="<?php echo esc_attr($slider_id); ?>" class="leadPress-slider-container" 
         data-autoplay="<?php echo esc_attr($atts['autoplay']); ?>"
         data-speed="<?php echo esc_attr($atts['speed']); ?>"
         data-animation="<?php echo esc_attr($atts['animation']); ?>">
        <div class="leadPress-slider">
            <?php foreach ($slides as $index => $slide): ?>
                <div class="leadPress-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                    <img src ="<?php echo do_shortcode($slide); ?>">
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($slides) > 1): ?>
            <div class="leadPress-slider-controls">
                <button class="leadPress-slider-prev" aria-label="Slide anterior">
                    <svg viewBox="0 0 24 24" width="24" height="24">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path>
                    </svg>
                </button>
                
                <div class="leadPress-slider-dots">
                    <?php foreach ($slides as $index => $slide): ?>
                        <button class="leadPress-slider-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                                data-slide="<?php echo $index; ?>" 
                                aria-label="Ir para slide <?php echo $index + 1; ?>"></button>
                    <?php endforeach; ?>
                </div>
                
                <button class="leadPress-slider-next" aria-label="Próximo slide">
                    <svg viewBox="0 0 24 24" width="24" height="24">
                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path>
                    </svg>
                </button>
            </div>
        <?php endif; ?>
    </div>
    <?php
    
    // Retornar conteúdo do buffer
    return ob_get_clean();
}

// Registrar o shortcode
add_shortcode('leadPress_slider', 'leadPress_slider_shortcode');

// Função para adicionar estilos e scripts
function leadPress_slider_scripts_styles() {
    // Verificar se o shortcode está presente no conteúdo
    global $post;
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'leadPress_slider')) {
        ?>
        <style>
        .leadPress-slider-container {
    position: relative;
    width: 100%;
    margin: 30px 0;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    /* Altura ajustável de acordo com o conteúdo */
    height: auto;
}

.leadPress-slider {
    position: relative;
    width: 100%;
    /* Aumentar altura mínima para acomodar imagens maiores */
    min-height: 400px;
    height: auto;
    aspect-ratio: 16/9; /* Proporção widescreen para imagens */
}

.leadPress-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
    padding: 0; /* Remover padding para maximizar espaço da imagem */
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Tratamento de imagens dentro dos slides */
.leadPress-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Cobrir todo o espaço disponível */
    object-position: center; /* Centralizar imagem */
}

/* Opção alternativa para preservar proporção da imagem inteira */
.leadPress-slide.preserve-ratio img {
    object-fit: contain;
    background-color: #f5f5f5; /* Fundo leve para imagens com transparência */
}

.leadPress-slide.active {
    opacity: 1;
    z-index: 1;
}

.leadPress-slider-controls {
    position: absolute;
    bottom: 20px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2;
}

.leadPress-slider-prev,
.leadPress-slider-next {
    background: rgba(0, 0, 0, 0.5); /* Controles mais escuros para destacar sobre imagens claras */
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 10px;
    transition: background 0.3s ease;
}

.leadPress-slider-prev:hover,
.leadPress-slider-next:hover {
    background: rgba(0, 0, 0, 0.8);
}

.leadPress-slider-prev svg,
.leadPress-slider-next svg {
    fill: #fff; /* Ícones brancos para contraste */
}

.leadPress-slider-dots {
    display: flex;
    justify-content: center;
    align-items: center;
}

.leadPress-slider-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    border: none;
    margin: 0 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.leadPress-slider-dot.active {
    background: #fff;
}

/* Animação de fade */
.leadPress-slider-container[data-animation="fade"] .leadPress-slide {
    opacity: 0;
    transition: opacity 0.8s ease; /* Transição mais suave */
}

.leadPress-slider-container[data-animation="fade"] .leadPress-slide.active {
    opacity: 1;
}

/* Animação de slide */
.leadPress-slider-container[data-animation="slide"] .leadPress-slide {
    transform: translateX(100%);
    opacity: 1;
    transition: transform 0.8s ease;
}

.leadPress-slider-container[data-animation="slide"] .leadPress-slide.active {
    transform: translateX(0);
}

.leadPress-slider-container[data-animation="slide"] .leadPress-slide.prev {
    transform: translateX(-100%);
}

/* Responsividade */
@media (max-width: 768px) {
    .leadPress-slider-container {
        margin: 15px 0;
    }
    
    .leadPress-slider {
        min-height: 250px; /* Altura mínima para mobile */
        aspect-ratio: 4/3; /* Proporção mais adequada para mobile */
    }
    
    .leadPress-slider-prev,
    .leadPress-slider-next {
        width: 30px;
        height: 30px;
    }
    
    .leadPress-slider-dot {
        width: 8px;
        height: 8px;
    }
}

/* Para telas muito grandes, limitar tamanho máximo */
@media (min-width: 1200px) {
    .leadPress-slider {
        max-height: 600px;
    }
}
        </style>

        <script>
        /**
         * leadPress Slider JavaScript
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar todos os sliders na página
            const sliders = document.querySelectorAll('.leadPress-slider-container');
            
            sliders.forEach(function(sliderContainer) {
                const slider = sliderContainer.querySelector('.leadPress-slider');
                const slides = slider.querySelectorAll('.leadPress-slide');
                const prevButton = sliderContainer.querySelector('.leadPress-slider-prev');
                const nextButton = sliderContainer.querySelector('.leadPress-slider-next');
                const dots = sliderContainer.querySelectorAll('.leadPress-slider-dot');
                
                // Obter configurações do slider
                const autoplay = sliderContainer.getAttribute('data-autoplay') === 'true';
                const speed = parseInt(sliderContainer.getAttribute('data-speed') || '5000');
                const animation = sliderContainer.getAttribute('data-animation') || 'fade';
                
                // Variáveis do slider
                let currentSlide = 0;
                let slideInterval;
                
                // Função para mostrar um slide específico
                function showSlide(index) {
                    // Remover classes ativas
                    slides.forEach(slide => {
                        slide.classList.remove('active');
                        slide.classList.remove('prev');
                    });
                    
                    dots.forEach(dot => {
                        dot.classList.remove('active');
                    });
                    
                    // Adicionar classe ativa ao slide atual
                    slides[index].classList.add('active');
                    
                    // Adicionar classe prev ao slide anterior (para animação de slide)
                    if (index > 0) {
                        slides[index - 1].classList.add('prev');
                    } else {
                        slides[slides.length - 1].classList.add('prev');
                    }
                    
                    // Atualizar dot ativo
                    if (dots[index]) {
                        dots[index].classList.add('active');
                    }
                    
                    // Atualizar índice atual
                    currentSlide = index;
                }
                
                // Função para mostrar o próximo slide
                function nextSlide() {
                    let newIndex = currentSlide + 1;
                    if (newIndex >= slides.length) {
                        newIndex = 0;
                    }
                    showSlide(newIndex);
                }
                
                // Função para mostrar o slide anterior
                function prevSlide() {
                    let newIndex = currentSlide - 1;
                    if (newIndex < 0) {
                        newIndex = slides.length - 1;
                    }
                    showSlide(newIndex);
                }
                
                // Iniciar autoplay se necessário
                if (autoplay && slides.length > 1) {
                    slideInterval = setInterval(nextSlide, speed);
                    
                    // Pausar o autoplay quando o mouse estiver sobre o slider
                    sliderContainer.addEventListener('mouseenter', function() {
                        clearInterval(slideInterval);
                    });
                    
                    // Retomar o autoplay quando o mouse sair do slider
                    sliderContainer.addEventListener('mouseleave', function() {
                        slideInterval = setInterval(nextSlide, speed);
                    });
                }
                
                // Adicionar event listeners para os botões
                if (prevButton) {
                    prevButton.addEventListener('click', function() {
                        prevSlide();
                        
                        // Resetar o autoplay se necessário
                        if (autoplay) {
                            clearInterval(slideInterval);
                            slideInterval = setInterval(nextSlide, speed);
                        }
                    });
                }
                
                if (nextButton) {
                    nextButton.addEventListener('click', function() {
                        nextSlide();
                        
                        // Resetar o autoplay se necessário
                        if (autoplay) {
                            clearInterval(slideInterval);
                            slideInterval = setInterval(nextSlide, speed);
                        }
                    });
                }
                
                // Adicionar event listeners para os dots
                dots.forEach(function(dot, index) {
                    dot.addEventListener('click', function() {
                        showSlide(index);
                        
                        // Resetar o autoplay se necessário
                        if (autoplay) {
                            clearInterval(slideInterval);
                            slideInterval = setInterval(nextSlide, speed);
                        }
                    });
                });
                
                // Adicionar suporte para swipe em dispositivos móveis
                let touchStartX = 0;
                let touchEndX = 0;
                
                sliderContainer.addEventListener('touchstart', function(e) {
                    touchStartX = e.changedTouches[0].screenX;
                }, { passive: true });
                
                sliderContainer.addEventListener('touchend', function(e) {
                    touchEndX = e.changedTouches[0].screenX;
                    handleSwipe();
                }, { passive: true });
                
                function handleSwipe() {
                    const swipeThreshold = 50;
                    if (touchEndX < touchStartX - swipeThreshold) {
                        // Swipe para a esquerda
                        nextSlide();
                    } else if (touchEndX > touchStartX + swipeThreshold) {
                        // Swipe para a direita
                        prevSlide();
                    }
                    
                    // Resetar o autoplay se necessário
                    if (autoplay) {
                        clearInterval(slideInterval);
                        slideInterval = setInterval(nextSlide, speed);
                    }
                }
            });
        });
        </script>
        <?php
    }
}

// Adicionar hook para carregar scripts e estilos
add_action('wp_footer', 'leadPress_slider_scripts_styles');