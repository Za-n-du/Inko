<?php
/**
 * Gallery lightbox (native <dialog>). Opened by [data-lightbox] links.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;
?>
<dialog class="lightbox" id="lightbox" aria-label="<?php esc_attr_e( 'Image viewer', 'inko-custom' ); ?>">
	<figure class="lightbox__figure">
		<img class="lightbox__img" src="data:image/gif;base64,R0lGODlhAQABAAAAACw=" alt="">
		<figcaption class="lightbox__caption"></figcaption>
	</figure>
	<button class="lightbox__btn lightbox__prev" type="button" aria-label="<?php esc_attr_e( 'Previous image', 'inko-custom' ); ?>"><?php echo inko_icon( 'prev' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
	<button class="lightbox__btn lightbox__next" type="button" aria-label="<?php esc_attr_e( 'Next image', 'inko-custom' ); ?>"><?php echo inko_icon( 'next' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
	<button class="lightbox__btn lightbox__close" type="button" aria-label="<?php esc_attr_e( 'Close', 'inko-custom' ); ?>"><?php echo inko_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
</dialog>
