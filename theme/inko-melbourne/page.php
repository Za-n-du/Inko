<?php
/**
 * Pages. A page that contains INKO slides is printed full-screen; any other page gets a plain readable layout.
 *
 * @package inko-melbourne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
$ik_stack = inko_is_stack_page();
?>
<main id="ik-main"<?php echo $ik_stack ? '' : ' class="ik-page"'; ?>>
	<?php
	while ( have_posts() ) {
		the_post();
		if ( $ik_stack ) {
			the_content();
		} else {
			echo '<div class="ik-page__in"><h1>' . esc_html( get_the_title() ) . '</h1>';
			the_content();
			echo '</div>';
		}
	}
	?>
</main>
<?php
get_footer();
