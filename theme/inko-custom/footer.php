<?php
/**
 * Site footer + document close.
 *
 * On the front page the footer is part of slide 11 (template-parts/sections/11-contact.php),
 * so it is only printed here for every other view.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_front_page() ) {
	get_template_part( 'template-parts/site-footer' );
}

wp_footer();
?>
</body>
</html>
