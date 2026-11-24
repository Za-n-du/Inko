<?php
/**
 * Document head + site header.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

$inko_studio = inko_content( 'studio' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<script>document.documentElement.classList.add('js');</script>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'inko-custom' ); ?></a>

<header class="site-header" id="site-header">
	<div class="site-header__inner">

		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( $inko_studio['brand'] . ' — ' . __( 'home', 'inko-custom' ) ); ?>">
			<?php
			if ( has_custom_logo() ) {
				$inko_logo_id = get_theme_mod( 'custom_logo' );
				echo wp_get_attachment_image( $inko_logo_id, 'full', false, array( 'class' => 'brand__logo', 'alt' => $inko_studio['brand'], 'loading' => 'eager' ) );
			} else {
				printf(
					'<img class="brand__logo" src="%1$s" width="892" height="630" alt="%2$s" loading="eager" decoding="async">',
					esc_url( inko_asset( 'images/logo-inko.png' ) ),
					esc_attr( $inko_studio['brand'] )
				);
			}
			?>
		</a>

		<nav class="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'inko-custom' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav-list',
					'depth'          => 1,
					'fallback_cb'    => 'inko_menu_fallback',
				)
			);
			?>
		</nav>

		<div class="site-header__actions">
			<a class="btn btn--outline btn--sm site-header__cta" href="<?php echo esc_url( inko_section_url( 'quote' ) ); ?>">
				<span><?php echo esc_html( inko_content( 'quote' )['cta'] ); ?></span><?php echo inko_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
			<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="menu-overlay">
				<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'inko-custom' ); ?></span>
				<span class="menu-toggle__text" aria-hidden="true">
					<span class="menu-toggle__open"><?php esc_html_e( 'Menu', 'inko-custom' ); ?></span>
					<span class="menu-toggle__close"><?php esc_html_e( 'Close', 'inko-custom' ); ?></span>
				</span>
				<span class="menu-toggle__icon" aria-hidden="true"><span class="menu-toggle__bars"><i></i><i></i></span></span>
			</button>
		</div>

	</div>
</header>

<div class="menu-overlay" id="menu-overlay" hidden>
	<div class="menu-overlay__inner">
		<?php $inko_menu_slides = inko_slides(); ?>
		<nav class="menu-overlay__nav" aria-label="<?php esc_attr_e( 'Site sections', 'inko-custom' ); ?>">
			<ol class="menu-overlay__list" style="--rows:<?php echo esc_attr( (int) ceil( count( $inko_menu_slides ) / 2 ) ); ?>">
				<?php
				$inko_n = 0;
				foreach ( $inko_menu_slides as $inko_id => $inko_slide ) :
					++$inko_n;
					?>
					<li>
						<a class="menu-overlay__link" href="<?php echo esc_url( inko_section_url( $inko_id ) ); ?>" data-menu="<?php echo esc_attr( $inko_id ); ?>">
							<span class="menu-overlay__num"><?php echo esc_html( sprintf( '%02d', $inko_n ) ); ?></span>
							<span class="menu-overlay__label"><?php echo esc_html( $inko_slide[1] ); ?></span>
							<?php echo inko_icon( 'arrow-ur' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ol>
		</nav>

		<div class="menu-overlay__aside">
			<div class="menu-overlay__block">
				<p class="menu-overlay__title"><?php esc_html_e( 'Visit', 'inko-custom' ); ?></p>
				<p><?php echo esc_html( $inko_studio['address_1'] ); ?>, <?php echo esc_html( $inko_studio['address_2'] ); ?></p>
			</div>
			<div class="menu-overlay__block">
				<p class="menu-overlay__title"><?php esc_html_e( 'Contact', 'inko-custom' ); ?></p>
				<p>
					<a href="mailto:<?php echo esc_attr( antispambot( $inko_studio['email'] ) ); ?>"><?php echo esc_html( antispambot( $inko_studio['email'] ) ); ?></a>
					<a href="tel:<?php echo esc_attr( $inko_studio['phone_tel'] ); ?>"><?php echo esc_html( $inko_studio['phone_display'] ); ?></a>
				</p>
			</div>
			<div class="menu-overlay__block">
				<p class="menu-overlay__title"><?php esc_html_e( 'Follow', 'inko-custom' ); ?></p>
				<?php inko_social_links( 'social social--row', false, inko_content( 'hero' )['social'] ); ?>
			</div>
		</div>
	</div>
</div>
