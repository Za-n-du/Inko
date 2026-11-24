<?php
/**
 * Footer. The editorial homepage supplies its own footer; ordinary pages use the compact theme footer.
 *
 * @package inko-melbourne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! inko_is_stack_page() && ! inko_is_editorial_home() ) {
	get_template_part( 'template-parts/footer/simple-footer' );
}
wp_footer();
?>
</body>
</html>
