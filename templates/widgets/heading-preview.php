<?php

defined( 'ABSPATH' ) || exit;
?>
<#
const tags = <?php echo wp_json_encode( $tags ); ?>;
const presets = <?php echo wp_json_encode( $presets ); ?>;
const tag = tags.includes( settings.heading_tag ) ? settings.heading_tag : 'h2';
const preset = presets.includes( settings.style_preset ) ? settings.style_preset : '<?php echo esc_js( $default ); ?>';
const title = typeof settings.title === 'string' ? settings.title.trim() : '';
const subtitle = typeof settings.subtitle === 'string' ? settings.subtitle.trim() : '';
const fillClass = settings.color_mode === 'gradient' ? ' nova-heading--gradient-text' : '';

if ( title || subtitle ) { #>
	<div class="nova-widget nova-heading nova-heading--{{ preset }}{{ fillClass }}">
		<# if ( title ) { #>
			<{{ tag }} class="nova-heading__title">{{ title }}</{{ tag }}>
		<# } #>
		<# if ( subtitle ) { #>
			<p class="nova-heading__subtitle">{{ subtitle }}</p>
		<# } #>
	</div>
<# } #>
