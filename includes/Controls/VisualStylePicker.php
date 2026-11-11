<?php

namespace NovaElements\Controls;

use Elementor\Base_Data_Control;

defined( 'ABSPATH' ) || exit;

final class VisualStylePicker extends Base_Data_Control {
	public const TYPE = 'nova_visual_style';

	public function get_type(): string {
		return self::TYPE;
	}

	public function enqueue(): void {
		wp_enqueue_style(
			'nova-elements-style-picker',
			plugins_url( 'assets/css/style-picker.min.css', dirname( __DIR__, 2 ) . '/nova-elements.php' ),
			array(),
			\NovaElements\Core\Plugin::VERSION
		);
	}

	public function content_template(): void {
		?>
		<fieldset class="nova-style-picker">
			<legend class="elementor-control-title">{{ data.label }}</legend>
			<p class="nova-style-picker__description"><?php esc_html_e( 'Choose a layout. Your customized colors and typography are retained.', 'nova-elements' ); ?></p>
			<div class="nova-style-picker__grid">
				<# _.each( data.options, function( option, value ) { #>
					<div class="nova-style-picker__item">
						<input class="nova-style-picker__radio" type="radio"
							id="<?php $this->print_control_uid( '{{ value }}' ); ?>"
							name="nova-style-{{ data._cid }}" value="{{ value }}" data-setting="{{ data.name }}"
							aria-describedby="<?php $this->print_control_uid( '{{ value }}-description' ); ?>">
						<label class="nova-style-picker__card" for="<?php $this->print_control_uid( '{{ value }}' ); ?>">
							<img src="{{ option.thumbnail }}" alt="" width="320" height="200" loading="lazy" decoding="async">
							<span class="nova-style-picker__label">{{ option.label }}</span>
							<span class="nova-style-picker__check" aria-hidden="true">&#10003;</span>
						</label>
						<span class="nova-style-picker__sr" id="<?php $this->print_control_uid( '{{ value }}-description' ); ?>">{{ option.description }}</span>
					</div>
				<# } ); #>
			</div>
		</fieldset>
		<?php
	}
}
