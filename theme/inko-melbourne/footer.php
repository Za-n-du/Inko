<?php
/**
 * Footer. On slide pages the footer is slide 12 (editable in Elementor), so nothing is printed twice.
 *
 * @package inko-melbourne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! inko_is_stack_page() ) {
	get_template_part( 'template-parts/footer/simple-footer' );
}
wp_footer();
?>
</body>
</html>
