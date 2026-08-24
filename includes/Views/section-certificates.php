<?php
/**
 * Seção Certificados — grid de cards 16:9.
 *
 * @var array $certificates
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<header class="maskavo-mc-panel__head">
	<h2 class="maskavo-mc-panel__title"><?php esc_html_e( 'Certificados', 'maskavo-minha-conta' ); ?></h2>
	<p class="maskavo-mc-panel__lead"><?php esc_html_e( 'Cursos concluídos com certificado disponível.', 'maskavo-minha-conta' ); ?></p>
</header>

<?php if ( empty( $certificates ) ) : ?>
	<article class="maskavo-mc-card maskavo-mc-empty">
		<p><?php esc_html_e( 'Você ainda não tem certificados. Conclua um curso para liberar o seu.', 'maskavo-minha-conta' ); ?></p>
	</article>
<?php else : ?>
	<div class="maskavo-mc-cert-grid">
		<?php foreach ( $certificates as $cert ) : ?>
			<?php
			$has_url   = ! empty( $cert['url'] );
			$image_url = ! empty( $cert['image_url'] ) ? $cert['image_url'] : ( MASKAVO_MC_URL . 'assets/img/curriculum-bg.svg' );
			$tag       = $has_url ? 'a' : 'article';
			$attrs     = $has_url
				? 'href="' . esc_url( $cert['url'] ) . '" target="_blank" rel="noopener noreferrer"'
				: '';
			?>
			<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				class="maskavo-mc-cert-card"
				<?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				style="--mc-cert-bg: url('<?php echo esc_url( $image_url ); ?>')"
			>
				<span class="maskavo-mc-cert-card__media" aria-hidden="true"></span>
				<span class="maskavo-mc-cert-card__shade" aria-hidden="true"></span>
				<span class="maskavo-mc-cert-card__body">
					<span class="maskavo-mc-cert-card__title"><?php echo esc_html( $cert['title'] ); ?></span>
					<?php if ( ! empty( $cert['completed_label'] ) ) : ?>
						<span class="maskavo-mc-cert-card__meta">
							<?php
							printf(
								/* translators: %s: date */
								esc_html__( 'Concluído em %s', 'maskavo-minha-conta' ),
								esc_html( $cert['completed_label'] )
							);
							?>
						</span>
					<?php endif; ?>
					<?php if ( $has_url ) : ?>
						<span class="maskavo-mc-cert-card__cta"><?php esc_html_e( 'Ver certificado', 'maskavo-minha-conta' ); ?></span>
					<?php endif; ?>
				</span>
			</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
