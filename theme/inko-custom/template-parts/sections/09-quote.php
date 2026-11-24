<?php
/**
 * Slide 09 — Booking / consultation: "Get a Quotation."
 * Content: original heading + text; form fields mirror the original
 * Contact_us form (Name, Email, Phone, File Upload, Details).
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

$inko_q      = inko_content( 'quote' );
$inko_s      = inko_content( 'studio' );
$inko_status = inko_quote_status();
?>
<section class="slide slide--quote" id="<?php echo esc_attr( $args['id'] ); ?>" data-slide aria-labelledby="quote-title">
	<div class="quote__bg" aria-hidden="true">
		<?php echo inko_img( 'studio-07', '', '60vw', array( 'class' => 'quote__bg-img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>

	<div class="slide__inner quote__grid">
		<div class="quote__copy">
			<?php inko_eyebrow( $args['num'], __( 'Quotation', 'inko-custom' ) ); ?>
			<?php $inko_tt = inko_split_last( $inko_q['title'] ); ?>
			<h2 class="display" id="quote-title" data-reveal="mask"><span><?php echo inko_heading( $inko_tt[0], $inko_tt[1] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?></span></h2>
			<p class="lede" data-reveal style="--d:.1s"><?php echo esc_html( $inko_q['text'] ); ?></p>

			<div class="quote__contact" data-reveal style="--d:.2s">
				<p class="quote__contact-label"><?php echo esc_html( $inko_q['contact'] ); ?></p>
				<a class="contact-line" href="mailto:<?php echo esc_attr( antispambot( $inko_s['email'] ) ); ?>"><?php echo inko_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( antispambot( $inko_s['email'] ) ); ?></span></a>
				<a class="contact-line" href="tel:<?php echo esc_attr( $inko_s['phone_tel'] ); ?>"><?php echo inko_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $inko_s['phone_display'] ); ?></span></a>
			</div>
		</div>

		<form class="quote-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data" data-reveal style="--d:.15s" novalidate>
			<input type="hidden" name="action" value="inko_quote">
			<?php wp_nonce_field( 'inko_quote', 'inko_quote_nonce' ); ?>

			<div class="quote-form__status <?php echo $inko_status ? 'is-' . esc_attr( $inko_status['type'] ) : ''; ?>" role="status" aria-live="polite" tabindex="-1"><?php echo $inko_status ? esc_html( $inko_status['text'] ) : ''; ?></div>

			<p class="quote-form__req"><span aria-hidden="true">*</span> <?php esc_html_e( 'Required field', 'inko-custom' ); ?></p>

			<fieldset class="field-group">
				<legend><?php esc_html_e( 'Name', 'inko-custom' ); ?> <span aria-hidden="true">*</span></legend>
				<div class="field">
					<label for="q-first"><?php esc_html_e( 'First Name', 'inko-custom' ); ?></label>
					<input id="q-first" name="first_name" type="text" autocomplete="given-name" required maxlength="80">
				</div>
				<div class="field">
					<label for="q-last"><?php esc_html_e( 'Last Name', 'inko-custom' ); ?></label>
					<input id="q-last" name="last_name" type="text" autocomplete="family-name" required maxlength="80">
				</div>
			</fieldset>

			<div class="field-row">
				<div class="field">
					<label for="q-email"><?php esc_html_e( 'Email', 'inko-custom' ); ?> <span aria-hidden="true">*</span></label>
					<input id="q-email" name="email" type="email" autocomplete="email" required maxlength="160">
				</div>
				<div class="field">
					<label for="q-phone"><?php esc_html_e( 'Phone', 'inko-custom' ); ?></label>
					<input id="q-phone" name="phone" type="tel" autocomplete="tel" maxlength="40">
				</div>
			</div>

			<div class="field field--file">
				<span class="field__label" id="q-file-label"><?php esc_html_e( 'File Upload', 'inko-custom' ); ?></span>
				<label class="file-drop" for="q-file">
					<?php echo inko_icon( 'upload' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="file-drop__text" data-file-name><?php esc_html_e( 'Choose a file', 'inko-custom' ); ?></span>
				</label>
				<input class="file-input" id="q-file" name="reference" type="file" accept=".jpg,.jpeg,.png,.webp,.heic,.pdf,image/jpeg,image/png,image/webp,image/heic,application/pdf" aria-describedby="q-file-help" aria-labelledby="q-file-label" data-max="<?php echo esc_attr( INKO_QUOTE_MAX_BYTES ); ?>">
				<p class="field__help" id="q-file-help"><?php esc_html_e( 'Your tattoo reference or design: JPG, PNG, WEBP, HEIC or PDF, up to 4 MB.', 'inko-custom' ); ?></p>
			</div>

			<div class="field">
				<label for="q-details"><?php esc_html_e( 'Details', 'inko-custom' ); ?> <span aria-hidden="true">*</span></label>
				<p class="field__help" id="q-details-help">
					<?php
					$inko_help = array();
					foreach ( $inko_q['details_help'] as $inko_i => $inko_h ) {
						$inko_help[] = ( $inko_i + 1 ) . '. ' . $inko_h;
					}
					echo esc_html( implode( '  ', $inko_help ) );
					?>
				</p>
				<textarea id="q-details" name="details" rows="4" required maxlength="4000" aria-describedby="q-details-help"></textarea>
			</div>

			<div class="hp" aria-hidden="true">
				<label for="q-website">Website</label>
				<input id="q-website" name="website" type="text" tabindex="-1" autocomplete="off">
			</div>

			<button class="btn btn--primary btn--block" type="submit"><span><?php esc_html_e( 'Send', 'inko-custom' ); ?></span><?php echo inko_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
		</form>
	</div>
</section>
