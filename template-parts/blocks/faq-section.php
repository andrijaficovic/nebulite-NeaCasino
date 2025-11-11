<?php
/**
 * Template: FAQ Section Block
 *
 * Expects vars: $block_id, $title, $faq_items, $margin_bottom
 *
 * @package Nebulite
 */

if ( ! isset( $block_id ) ) {
	return;
}

$margin_bottom_value = isset( $margin_bottom ) ? $margin_bottom : '2rem';

// Build JSON-LD schema markup for FAQPage
$schema = array(
	'@context' => 'https://schema.org',
	'@type'    => 'FAQPage',
	'mainEntity' => array(),
);

if ( ! empty( $faq_items ) && is_array( $faq_items ) ) {
	foreach ( $faq_items as $item ) {
		if ( ! empty( $item['question'] ) && ! empty( $item['answer'] ) ) {
			$schema['mainEntity'][] = array(
				'@type'          => 'Question',
				'name'           => wp_strip_all_tags( $item['question'] ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => wp_strip_all_tags( $item['answer'] ),
				),
			);
		}
	}
}
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="faq-section"<?php echo $margin_bottom_value ? ' style="margin-bottom: ' . esc_attr( $margin_bottom_value ) . ';"' : ''; ?>>
	<div class="container">
		<?php if ( ! empty( $title ) ) : ?>
			<h2 class="faq-section__title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<?php if ( ! empty( $description ) ) : ?>
			<div class="faq-section__description"><?php echo wp_kses_post( $description ); ?></div>
		<?php endif; ?>

		<?php if ( ! empty( $faq_items ) && is_array( $faq_items ) ) : ?>
			<div class="faq-section__items">
				<?php foreach ( $faq_items as $index => $item ) : ?>
					<?php
					if ( empty( $item['question'] ) || empty( $item['answer'] ) ) {
						continue;
					}
					$item_id = $block_id . '-item-' . $index;
					?>
					<div class="faq-section__item">
						<button 
							class="faq-section__question" 
							type="button" 
							aria-expanded="false" 
							aria-controls="<?php echo esc_attr( $item_id ); ?>"
							id="<?php echo esc_attr( $item_id ); ?>-question"
						>
							<h3 class="faq-section__question-text"><?php echo esc_html( $item['question'] ); ?></h3>
							<span class="faq-section__icon" aria-hidden="true">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M12 8V16M8 12H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
								</svg>
							</span>
						</button>
						<div 
							class="faq-section__answer" 
							id="<?php echo esc_attr( $item_id ); ?>"
							role="region"
							aria-labelledby="<?php echo esc_attr( $item_id ); ?>-question"
							hidden
						>
							<div class="faq-section__answer-content">
								<?php echo wp_kses_post( $item['answer'] ); ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $schema['mainEntity'] ) ) : ?>
		<script type="application/ld+json">
			<?php echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ); ?>
		</script>
	<?php endif; ?>
</section>

