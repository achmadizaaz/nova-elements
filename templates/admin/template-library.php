<?php

defined( 'ABSPATH' ) || exit;
?>
<main class="wrap nova-library">
	<header class="nova-library__header">
		<div>
			<p><?php esc_html_e( 'Nova Elements', 'nova-elements' ); ?></p>
			<h1><?php esc_html_e( 'Template Library', 'nova-elements' ); ?></h1>
			<p><?php esc_html_e( 'Preview a design and import it to Elementor Site Templates.', 'nova-elements' ); ?></p>
		</div>
		<?php if ( $native ) : ?>
			<a class="button" href="<?php echo esc_url( $native ); ?>"><?php esc_html_e( 'My Templates', 'nova-elements' ); ?></a>
		<?php endif; ?>
	</header>

	<?php if ( $notice ) : ?>
		<div class="notice <?php echo 'imported' === $status ? 'notice-success' : 'notice-error'; ?>"><p><?php echo esc_html( $notice ); ?></p></div>
	<?php endif; ?>
	<?php if ( $native && ! $can_import ) : ?>
		<div class="notice notice-info"><p><?php esc_html_e( 'Your account needs permission to publish Elementor templates to import these designs.', 'nova-elements' ); ?></p></div>
	<?php endif; ?>

	<form class="nova-library__filters" method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
		<input type="hidden" name="page" value="nova-elements-library">
		<label><?php esc_html_e( 'Search templates', 'nova-elements' ); ?><input type="search" name="q" value="<?php echo esc_attr( $search ); ?>" maxlength="100"></label>
		<div class="nova-library__filter">
			<label for="nova-library-type"><?php esc_html_e( 'Template type', 'nova-elements' ); ?></label>
			<select id="nova-library-type" name="type">
				<option value=""><?php esc_html_e( 'All types', 'nova-elements' ); ?></option>
				<?php foreach ( $types as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $type, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="nova-library__filter">
			<label for="nova-library-category"><?php esc_html_e( 'Category', 'nova-elements' ); ?></label>
			<select id="nova-library-category" name="category">
				<option value=""><?php esc_html_e( 'All categories', 'nova-elements' ); ?></option>
				<?php foreach ( $categories as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $category, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<button class="button"><?php esc_html_e( 'Filter', 'nova-elements' ); ?></button>
	</form>

	<p class="nova-library__count"><?php
		/* translators: 1: number of matches, 2: total bundled templates. */
		printf( esc_html__( '%1$s of %2$s templates', 'nova-elements' ), esc_html( number_format_i18n( count( $matches ) ) ), esc_html( number_format_i18n( count( $this->registry->all() ) ) ) );
	?></p>
	<?php if ( ! $cards ) : ?>
		<p class="nova-library__empty"><?php esc_html_e( 'No templates match these filters.', 'nova-elements' ); ?> <a href="<?php echo esc_url( self::url() ); ?>"><?php esc_html_e( 'Clear filters', 'nova-elements' ); ?></a></p>
	<?php else : ?>
		<div class="nova-library__grid">
			<?php foreach ( $cards as $id => $card ) : ?>
				<?php
				$preview = $this->registry->file( $id, 'preview.webp' ) ? plugins_url( 'library/' . $id . '/preview.webp', $this->plugin_file ) : '';
				$tablet = $this->registry->file( $id, 'preview-tablet.webp' ) ? plugins_url( 'library/' . $id . '/preview-tablet.webp', $this->plugin_file ) : '';
				$mobile = $this->registry->file( $id, 'preview-mobile.webp' ) ? plugins_url( 'library/' . $id . '/preview-mobile.webp', $this->plugin_file ) : '';
				$meta = implode( ' · ', array( $categories[ $card['category'] ], $types[ $card['type'] ], $styles[ $card['style'] ] ) );
				$required = implode( ', ', array_map( static fn ( string $key ): string => $widgets[ $key ]['title'], $card['required_widgets'] ) );
				$problem = $this->importer->compatibility( $card );
				?>
				<article class="nova-library__card" data-nova-card>
					<?php if ( $preview ) : ?>
						<div class="nova-library__thumbnail">
							<img src="<?php echo esc_url( $preview ); ?>" alt="" width="1000" loading="lazy" decoding="async">
						</div>
					<?php endif; ?>
					<div class="nova-library__body">
						<p class="nova-library__meta" data-nova-meta><?php echo esc_html( $meta ); ?></p>
						<h2 data-nova-title><?php echo esc_html( $card['name'] ); ?></h2>
						<p data-nova-description><?php echo esc_html( $card['description'] ); ?></p>
						<p data-nova-requirements><?php
							/* translators: %s: comma-separated widget names. */
							printf( esc_html__( 'Requires %s', 'nova-elements' ), esc_html( $required ) );
						?></p>
						<?php if ( $problem ) : ?>
							<p data-nova-problem><?php echo esc_html( $problem->get_error_message() ); ?></p>
						<?php endif; ?>
						<div class="nova-library__actions">
							<?php if ( $preview ) : ?>
								<button type="button" class="button" hidden data-nova-preview="<?php echo esc_url( $preview ); ?>" data-nova-tablet="<?php echo esc_url( $tablet ); ?>" data-nova-mobile="<?php echo esc_url( $mobile ); ?>"><?php esc_html_e( 'Preview', 'nova-elements' ); ?></button>
							<?php endif; ?>
							<form data-nova-import method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
								<input type="hidden" name="action" value="<?php echo esc_attr( self::ACTION ); ?>">
								<input type="hidden" name="nova_template" value="<?php echo esc_attr( $id ); ?>">
								<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( self::ACTION . '_' . $id ) ); ?>">
								<button class="button button-primary" <?php disabled( ! $can_import || (bool) $problem ); ?>><?php esc_html_e( 'Import to Site Templates', 'nova-elements' ); ?></button>
							</form>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if ( $page_count > 1 ) : ?>
		<nav class="nova-library__pagination" aria-label="<?php esc_attr_e( 'Template library pages', 'nova-elements' ); ?>">
			<?php echo wp_kses_post( paginate_links( array( 'base' => add_query_arg( array( 'q' => $search, 'type' => $type, 'category' => $category, 'library_page' => '%#%' ), self::url() ), 'format' => '', 'current' => $page, 'total' => $page_count ) ) ); ?>
		</nav>
	<?php endif; ?>

	<dialog class="nova-library__dialog" aria-labelledby="nova-library-preview-title">
		<div class="nova-library__dialog-head">
			<div>
				<p data-nova-dialog-meta></p>
				<h2 id="nova-library-preview-title"></h2>
			</div>
			<form method="dialog"><button class="button"><?php esc_html_e( 'Close', 'nova-elements' ); ?></button></form>
		</div>
		<p data-nova-dialog-description></p>
		<p data-nova-dialog-requirements></p>
		<p data-nova-dialog-problem hidden></p>
		<div class="nova-library__actions" data-nova-devices>
			<button type="button" class="button" data-nova-device="desktop" aria-pressed="true"><?php esc_html_e( 'Desktop', 'nova-elements' ); ?></button>
			<button type="button" class="button" data-nova-device="tablet" aria-pressed="false"><?php esc_html_e( 'Tablet', 'nova-elements' ); ?></button>
			<button type="button" class="button" data-nova-device="mobile" aria-pressed="false"><?php esc_html_e( 'Mobile', 'nova-elements' ); ?></button>
		</div>
		<img data-nova-dialog-image alt="">
		<div data-nova-dialog-import></div>
	</dialog>
</main>
