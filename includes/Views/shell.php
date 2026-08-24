<?php
/**
 * Shell: nav + seções.
 *
 * Variáveis: $args, $profile, $subscriptions, $certificates, $reviews, $meus_cursos, $logout_url
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sections = array();
if ( ! empty( $args['show_profile'] ) ) {
	$sections['profile'] = __( 'Meus dados', 'maskavo-minha-conta' );
}
if ( ! empty( $args['show_subscription'] ) ) {
	$sections['subscription'] = __( 'Assinatura', 'maskavo-minha-conta' );
}
if ( ! empty( $args['show_certificates'] ) ) {
	$sections['certificates'] = __( 'Certificados', 'maskavo-minha-conta' );
}
if ( ! empty( $args['show_reviews'] ) ) {
	$sections['reviews'] = __( 'Avaliações', 'maskavo-minha-conta' );
}
if ( ! empty( $args['show_security'] ) ) {
	$sections['security'] = __( 'Segurança', 'maskavo-minha-conta' );
}

$first_key = '';
foreach ( array_keys( $sections ) as $k ) {
	$first_key = $k;
	break;
}

$chevron_svg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$back_svg    = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/></svg>';
?>
<div class="maskavo-mc" data-maskavo-mc>
	<aside class="maskavo-mc-nav" aria-label="<?php esc_attr_e( 'Menu da conta', 'maskavo-minha-conta' ); ?>">
		<div class="maskavo-mc-nav__brand">
			<span class="maskavo-mc-nav__eyebrow"><?php esc_html_e( 'Conta', 'maskavo-minha-conta' ); ?></span>
			<strong class="maskavo-mc-nav__name"><?php echo esc_html( $profile['display_name'] ?: __( 'Aluno', 'maskavo-minha-conta' ) ); ?></strong>
		</div>

		<nav class="maskavo-mc-nav__list" role="tablist">
			<?php foreach ( $sections as $key => $label ) : ?>
				<button
					type="button"
					class="maskavo-mc-nav__item<?php echo $key === $first_key ? ' is-active' : ''; ?>"
					role="tab"
					aria-selected="<?php echo $key === $first_key ? 'true' : 'false'; ?>"
					data-mc-tab="<?php echo esc_attr( $key ); ?>"
				>
					<span class="maskavo-mc-nav__icon" aria-hidden="true"><?php echo Maskavo_MC_Icons::get( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG estático. ?></span>
					<span class="maskavo-mc-nav__label"><?php echo esc_html( $label ); ?></span>
				</button>
			<?php endforeach; ?>
		</nav>

		<div class="maskavo-mc-nav__footer">
			<?php if ( ! empty( $args['show_meus_cursos'] ) ) : ?>
				<a class="maskavo-mc-nav__link" href="<?php echo esc_url( $meus_cursos ); ?>">
					<span class="maskavo-mc-nav__icon" aria-hidden="true"><?php echo Maskavo_MC_Icons::get( 'courses' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span><?php esc_html_e( 'Meus cursos', 'maskavo-minha-conta' ); ?></span>
				</a>
			<?php endif; ?>
			<a class="maskavo-mc-nav__link maskavo-mc-nav__link--sair maskavo-logout bt-sair" href="<?php echo esc_url( $logout_url ); ?>">
				<span class="maskavo-mc-nav__icon" aria-hidden="true"><?php echo Maskavo_MC_Icons::get( 'logout' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span><?php esc_html_e( 'Sair', 'maskavo-minha-conta' ); ?></span>
			</a>
		</div>
	</aside>

	<div class="maskavo-mc-main">
		<div class="maskavo-mc-mobile-home" data-mc-mobile-home>
			<header class="maskavo-mc-mobile-home__head">
				<span class="maskavo-mc-mobile-home__eyebrow"><?php esc_html_e( 'Conta', 'maskavo-minha-conta' ); ?></span>
				<strong class="maskavo-mc-mobile-home__name"><?php echo esc_html( $profile['display_name'] ?: __( 'Aluno', 'maskavo-minha-conta' ) ); ?></strong>
			</header>

			<nav class="maskavo-mc-mobile-menu" aria-label="<?php esc_attr_e( 'Seções da conta', 'maskavo-minha-conta' ); ?>">
				<?php foreach ( $sections as $key => $label ) : ?>
					<button
						type="button"
						class="maskavo-mc-mobile-menu__item"
						data-mc-open-section="<?php echo esc_attr( $key ); ?>"
					>
						<span class="maskavo-mc-mobile-menu__icon" aria-hidden="true"><?php echo Maskavo_MC_Icons::get( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="maskavo-mc-mobile-menu__label"><?php echo esc_html( $label ); ?></span>
						<span class="maskavo-mc-mobile-menu__chevron" aria-hidden="true"><?php echo $chevron_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</button>
				<?php endforeach; ?>
			</nav>

			<div class="maskavo-mc-mobile-home__footer">
				<?php if ( ! empty( $args['show_meus_cursos'] ) ) : ?>
					<a class="maskavo-mc-mobile-menu__link" href="<?php echo esc_url( $meus_cursos ); ?>">
						<span class="maskavo-mc-mobile-menu__icon" aria-hidden="true"><?php echo Maskavo_MC_Icons::get( 'courses' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span><?php esc_html_e( 'Meus cursos', 'maskavo-minha-conta' ); ?></span>
					</a>
				<?php endif; ?>
				<a class="maskavo-mc-mobile-menu__link maskavo-mc-mobile-menu__link--sair maskavo-logout bt-sair" href="<?php echo esc_url( $logout_url ); ?>">
					<span class="maskavo-mc-mobile-menu__icon" aria-hidden="true"><?php echo Maskavo_MC_Icons::get( 'logout' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span><?php esc_html_e( 'Sair', 'maskavo-minha-conta' ); ?></span>
				</a>
			</div>
		</div>

		<button type="button" class="maskavo-mc-back" data-mc-back hidden>
			<span class="maskavo-mc-back__icon" aria-hidden="true"><?php echo $back_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<span><?php esc_html_e( 'Voltar', 'maskavo-minha-conta' ); ?></span>
		</button>

		<?php if ( ! empty( $args['show_profile'] ) ) : ?>
			<section
				class="maskavo-mc-panel<?php echo 'profile' === $first_key ? ' is-active' : ''; ?>"
				data-mc-panel="profile"
				role="tabpanel"
				<?php echo 'profile' !== $first_key ? 'hidden' : ''; ?>
			>
				<?php Maskavo_MC_Renderer::view( 'section-profile', array( 'profile' => $profile ) ); ?>
			</section>
		<?php endif; ?>

		<?php if ( ! empty( $args['show_subscription'] ) ) : ?>
			<section
				class="maskavo-mc-panel<?php echo 'subscription' === $first_key ? ' is-active' : ''; ?>"
				data-mc-panel="subscription"
				role="tabpanel"
				<?php echo 'subscription' !== $first_key ? 'hidden' : ''; ?>
			>
				<?php Maskavo_MC_Renderer::view( 'section-subscription', array( 'subscriptions' => $subscriptions ) ); ?>
			</section>
		<?php endif; ?>

		<?php if ( ! empty( $args['show_certificates'] ) ) : ?>
			<section
				class="maskavo-mc-panel<?php echo 'certificates' === $first_key ? ' is-active' : ''; ?>"
				data-mc-panel="certificates"
				role="tabpanel"
				<?php echo 'certificates' !== $first_key ? 'hidden' : ''; ?>
			>
				<?php Maskavo_MC_Renderer::view( 'section-certificates', array( 'certificates' => $certificates ) ); ?>
			</section>
		<?php endif; ?>

		<?php if ( ! empty( $args['show_reviews'] ) ) : ?>
			<section
				class="maskavo-mc-panel<?php echo 'reviews' === $first_key ? ' is-active' : ''; ?>"
				data-mc-panel="reviews"
				role="tabpanel"
				<?php echo 'reviews' !== $first_key ? 'hidden' : ''; ?>
			>
				<?php Maskavo_MC_Renderer::view( 'section-reviews', array( 'reviews' => $reviews ) ); ?>
			</section>
		<?php endif; ?>

		<?php if ( ! empty( $args['show_security'] ) ) : ?>
			<section
				class="maskavo-mc-panel<?php echo 'security' === $first_key ? ' is-active' : ''; ?>"
				data-mc-panel="security"
				role="tabpanel"
				<?php echo 'security' !== $first_key ? 'hidden' : ''; ?>
			>
				<?php Maskavo_MC_Renderer::view( 'section-security', array() ); ?>
			</section>
		<?php endif; ?>
	</div>
</div>
