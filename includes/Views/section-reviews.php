<?php
/**
 * Seção Avaliações.
 *
 * @var array $reviews
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<header class="maskavo-mc-panel__head">
	<h2 class="maskavo-mc-panel__title"><?php esc_html_e( 'Avaliações', 'maskavo-minha-conta' ); ?></h2>
	<p class="maskavo-mc-panel__lead"><?php esc_html_e( 'Notas e comentários que você deixou nos cursos.', 'maskavo-minha-conta' ); ?></p>
</header>

<?php if ( empty( $reviews ) ) : ?>
	<article class="maskavo-mc-card maskavo-mc-empty">
		<p><?php esc_html_e( 'Você ainda não avaliou nenhum curso.', 'maskavo-minha-conta' ); ?></p>
	</article>
<?php else : ?>
	<div class="maskavo-mc-stack">
		<?php foreach ( $reviews as $review ) : ?>
			<article class="maskavo-mc-card maskavo-mc-card--row maskavo-mc-card--curriculum">
				<div class="maskavo-mc-card__main">
					<div class="maskavo-mc-card__topline">
						<h3 class="maskavo-mc-card__title">
							<?php if ( ! empty( $review['url'] ) ) : ?>
								<a href="<?php echo esc_url( $review['url'] ); ?>"><?php echo esc_html( $review['title'] ); ?></a>
							<?php else : ?>
								<?php echo esc_html( $review['title'] ); ?>
							<?php endif; ?>
						</h3>
						<span class="maskavo-mc-badge maskavo-mc-badge--<?php echo esc_attr( $review['status_key'] ); ?>">
							<?php echo esc_html( $review['status_label'] ); ?>
						</span>
					</div>
					<div class="maskavo-mc-stars" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: stars */ __( '%d de 5 estrelas', 'maskavo-minha-conta' ), (int) $review['rating'] ) ); ?>">
						<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
							<span class="maskavo-mc-star<?php echo $i <= (int) $review['rating'] ? ' is-on' : ''; ?>" aria-hidden="true">★</span>
						<?php endfor; ?>
					</div>
					<?php if ( ! empty( $review['date_label'] ) ) : ?>
						<p class="maskavo-mc-card__meta"><?php echo esc_html( $review['date_label'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $review['content'] ) ) : ?>
						<p class="maskavo-mc-review__content"><?php echo esc_html( $review['content'] ); ?></p>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $review['url'] ) ) : ?>
					<a class="maskavo-mc-btn maskavo-mc-btn--primary" href="<?php echo esc_url( $review['url'] ); ?>">
						<?php esc_html_e( 'Ver curso', 'maskavo-minha-conta' ); ?>
					</a>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
