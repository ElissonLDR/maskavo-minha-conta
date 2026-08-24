<?php
/**
 * Seção Assinatura.
 *
 * @var array $subscriptions
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items      = isset( $subscriptions['items'] ) ? (array) $subscriptions['items'] : array();
$manage_url = isset( $subscriptions['manage_url'] ) ? (string) $subscriptions['manage_url'] : '';
?>
<header class="maskavo-mc-panel__head">
	<h2 class="maskavo-mc-panel__title"><?php esc_html_e( 'Assinatura', 'maskavo-minha-conta' ); ?></h2>
	<p class="maskavo-mc-panel__lead"><?php esc_html_e( 'Planos e tempo de acesso na plataforma.', 'maskavo-minha-conta' ); ?></p>
</header>

<?php if ( empty( $items ) ) : ?>
	<article class="maskavo-mc-card maskavo-mc-empty">
		<p><?php esc_html_e( 'Nenhuma assinatura ativa encontrada nesta conta.', 'maskavo-minha-conta' ); ?></p>
		<?php if ( $manage_url ) : ?>
			<a class="maskavo-mc-btn maskavo-mc-btn--primary" href="<?php echo esc_url( home_url( '/assine/' ) ); ?>">
				<?php esc_html_e( 'Conhecer planos', 'maskavo-minha-conta' ); ?>
			</a>
		<?php endif; ?>
	</article>
<?php else : ?>
	<div class="maskavo-mc-stack">
		<?php foreach ( $items as $item ) : ?>
			<article class="maskavo-mc-card maskavo-mc-card--row maskavo-mc-card--curriculum">
				<div class="maskavo-mc-card__main">
					<div class="maskavo-mc-card__topline">
						<h3 class="maskavo-mc-card__title"><?php echo esc_html( $item['label'] ); ?></h3>
						<span class="maskavo-mc-badge maskavo-mc-badge--<?php echo esc_attr( $item['status_key'] ); ?>">
							<?php echo esc_html( $item['status_label'] ); ?>
						</span>
					</div>
					<?php if ( ! empty( $item['since_label'] ) ) : ?>
						<p class="maskavo-mc-card__meta">
							<?php
							printf(
								/* translators: %s: date */
								esc_html__( 'Desde %s', 'maskavo-minha-conta' ),
								esc_html( $item['since_label'] )
							);
							?>
						</p>
					<?php endif; ?>
					<?php if ( ! empty( $item['duration_label'] ) ) : ?>
						<p class="maskavo-mc-card__meta">
							<?php
							printf(
								/* translators: %s: duration */
								esc_html__( 'Tempo de assinatura: %s', 'maskavo-minha-conta' ),
								esc_html( $item['duration_label'] )
							);
							?>
						</p>
					<?php endif; ?>
					<?php if ( ! empty( $item['next_label'] ) ) : ?>
						<p class="maskavo-mc-card__meta">
							<?php
							printf(
								/* translators: %s: date */
								esc_html__( 'Próxima renovação: %s', 'maskavo-minha-conta' ),
								esc_html( $item['next_label'] )
							);
							?>
						</p>
					<?php endif; ?>
				</div>
			</article>
		<?php endforeach; ?>

		<?php if ( $manage_url ) : ?>
			<article class="maskavo-mc-card maskavo-mc-card--row maskavo-mc-card--curriculum">
				<div class="maskavo-mc-card__main">
					<h3 class="maskavo-mc-card__title"><?php esc_html_e( 'Gerenciar assinatura', 'maskavo-minha-conta' ); ?></h3>
					<p class="maskavo-mc-card__meta"><?php esc_html_e( 'Cobranças e cancelamento são gerenciados na Hotmart.', 'maskavo-minha-conta' ); ?></p>
				</div>
				<a class="maskavo-mc-btn maskavo-mc-btn--primary" href="<?php echo esc_url( $manage_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Abrir Hotmart', 'maskavo-minha-conta' ); ?>
				</a>
			</article>
		<?php endif; ?>
	</div>
<?php endif; ?>
