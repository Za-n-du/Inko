<?php
/**
 * Front page: shows the page chosen in Settings > Reading (the V3 homepage built with Elementor).
 *
 * @package inko-melbourne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main id="ik-main"<?php echo inko_is_stack_page() ? '' : ' class="ik-page"'; ?>>
	<?php if ( is_page() ) : ?>
		<?php
		while ( have_posts() ) {
			the_post();
			if ( inko_is_stack_page() ) {
				the_content();
			} else {
				echo '<div class="ik-page__in"><h1>' . esc_html( get_the_title() ) . '</h1>';
				the_content();
				echo '</div>';
			}
		}
		?>
	<?php else : ?>
		<div class="ik-page__in">
			<h1><?php echo esc_html( inko_contact( 'name' ) ); ?></h1>
			<?php if ( current_user_can( 'manage_options' ) ) : ?>
				<p><?php esc_html_e( 'The homepage is not set up yet.', 'inko-melbourne' ); ?> <a href="<?php echo esc_url( admin_url( 'themes.php?page=inko-setup' ) ); ?>"><?php esc_html_e( 'Appearance > INKO Setup: import the V3 homepage.', 'inko-melbourne' ); ?></a></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</main>
<?php
get_footer();
