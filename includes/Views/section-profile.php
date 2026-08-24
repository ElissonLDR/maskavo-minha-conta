<?php
/**
 * Seção Meus dados — edição por campo + tooltip no e-mail.
 *
 * @var array $profile
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$email_tip = __( 'O e-mail não pode ser alterado por aqui. Fale com o suporte se precisar trocar.', 'maskavo-minha-conta' );
$edit_svg  = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20h4.5L19 9.5 14.5 5 4 15.5V20z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/><path d="M13 6.5l4.5 4.5" stroke="currentColor" stroke-width="1.75"/></svg>';
$warn_svg  = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.75"/><path d="M12 8v5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><circle cx="12" cy="16.25" r="1" fill="currentColor"/></svg>';
$check_svg = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$close_svg = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 7l10 10M17 7L7 17" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>';
?>
<header class="maskavo-mc-panel__head">
	<h2 class="maskavo-mc-panel__title"><?php esc_html_e( 'Meus dados', 'maskavo-minha-conta' ); ?></h2>
	<p class="maskavo-mc-panel__lead"><?php esc_html_e( 'Suas informações pessoais na Comunidade.', 'maskavo-minha-conta' ); ?></p>
</header>

<article class="maskavo-mc-card maskavo-mc-card--profile" data-mc-profile-card>
	<div class="maskavo-mc-profile__top">
		<div class="maskavo-mc-avatar" data-mc-avatar>
			<img
				src="<?php echo esc_url( $profile['avatar_url'] ); ?>"
				alt="<?php echo esc_attr( $profile['display_name'] ); ?>"
				width="176"
				height="176"
				loading="lazy"
				data-mc-avatar-img
			/>
			<button type="button" class="maskavo-mc-avatar__btn" data-mc-avatar-pick title="<?php esc_attr_e( 'Trocar foto', 'maskavo-minha-conta' ); ?>">
				<span class="screen-reader-text"><?php esc_html_e( 'Trocar foto', 'maskavo-minha-conta' ); ?></span>
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M14.5 5.5l4 4L8 20H4v-4L14.5 5.5z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
			</button>
			<input type="file" accept="image/jpeg,image/png,image/webp,image/gif" hidden data-mc-avatar-input />
		</div>
		<div class="maskavo-mc-profile__intro">
			<div class="maskavo-mc-profile__identity">
				<h3 class="maskavo-mc-profile__name" data-mc-display-name><?php echo esc_html( $profile['display_name'] ); ?></h3>
				<p class="maskavo-mc-profile__meta"><?php esc_html_e( 'Membro desde', 'maskavo-minha-conta' ); ?>
					<strong data-mc-member-since><?php echo esc_html( $profile['member_since'] ); ?></strong>
				</p>
			</div>
			<div class="maskavo-mc-avatar__actions">
				<button type="button" class="maskavo-mc-btn maskavo-mc-btn--ghost maskavo-mc-btn--sm" data-mc-avatar-pick>
					<?php esc_html_e( 'Trocar foto', 'maskavo-minha-conta' ); ?>
				</button>
				<button
					type="button"
					class="maskavo-mc-btn maskavo-mc-btn--ghost maskavo-mc-btn--sm"
					data-mc-avatar-remove
					<?php echo empty( $profile['has_avatar'] ) ? 'hidden' : ''; ?>
				>
					<?php esc_html_e( 'Remover', 'maskavo-minha-conta' ); ?>
				</button>
			</div>
			<p class="maskavo-mc-feedback maskavo-mc-feedback--avatar" data-mc-avatar-feedback hidden></p>
		</div>
	</div>

	<div class="maskavo-mc-fields" data-mc-profile-fields>
		<!-- E-mail (somente leitura + tooltip) -->
		<div class="maskavo-mc-field" data-mc-field="email" data-mc-locked="1">
			<div class="maskavo-mc-field__head">
				<span class="maskavo-mc-field__label"><?php esc_html_e( 'E-mail', 'maskavo-minha-conta' ); ?></span>
				<span class="maskavo-mc-tooltip" data-mc-tooltip>
					<button
						type="button"
						class="maskavo-mc-field__warn"
						aria-expanded="false"
						aria-controls="maskavo-mc-tip-email"
						title="<?php esc_attr_e( 'Por que não posso editar?', 'maskavo-minha-conta' ); ?>"
					>
						<span class="screen-reader-text"><?php esc_html_e( 'Por que não posso editar?', 'maskavo-minha-conta' ); ?></span>
						<?php echo $warn_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
					<span class="maskavo-mc-tooltip__bubble" id="maskavo-mc-tip-email" role="tooltip" hidden>
						<?php echo esc_html( $email_tip ); ?>
					</span>
				</span>
			</div>
			<div class="maskavo-mc-field__value" data-mc-email><?php echo esc_html( $profile['email'] ); ?></div>
		</div>

		<!-- Nome -->
		<div class="maskavo-mc-field" data-mc-field="first_name">
			<div class="maskavo-mc-field__head">
				<span class="maskavo-mc-field__label"><?php esc_html_e( 'Nome', 'maskavo-minha-conta' ); ?></span>
				<button type="button" class="maskavo-mc-field__edit" data-mc-field-edit title="<?php esc_attr_e( 'Editar', 'maskavo-minha-conta' ); ?>">
					<span class="screen-reader-text"><?php esc_html_e( 'Editar nome', 'maskavo-minha-conta' ); ?></span>
					<?php echo $edit_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>
			<div class="maskavo-mc-field__view">
				<span class="maskavo-mc-field__value" data-mc-first-name><?php echo esc_html( $profile['first_name'] !== '' ? $profile['first_name'] : '—' ); ?></span>
			</div>
			<div class="maskavo-mc-field__edit-wrap" hidden>
				<input type="text" class="maskavo-mc-field__input" name="first_name" value="<?php echo esc_attr( $profile['first_name'] ); ?>" autocomplete="given-name" />
				<div class="maskavo-mc-field__actions">
					<button type="button" class="maskavo-mc-field__action maskavo-mc-field__action--cancel" data-mc-field-cancel title="<?php esc_attr_e( 'Cancelar', 'maskavo-minha-conta' ); ?>">
						<span class="screen-reader-text"><?php esc_html_e( 'Cancelar', 'maskavo-minha-conta' ); ?></span>
						<?php echo $close_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
					<button type="button" class="maskavo-mc-field__action maskavo-mc-field__action--save" data-mc-field-save title="<?php esc_attr_e( 'Salvar', 'maskavo-minha-conta' ); ?>">
						<span class="screen-reader-text"><?php esc_html_e( 'Salvar', 'maskavo-minha-conta' ); ?></span>
						<?php echo $check_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</div>
			</div>
		</div>

		<!-- Sobrenome -->
		<div class="maskavo-mc-field" data-mc-field="last_name">
			<div class="maskavo-mc-field__head">
				<span class="maskavo-mc-field__label"><?php esc_html_e( 'Sobrenome', 'maskavo-minha-conta' ); ?></span>
				<button type="button" class="maskavo-mc-field__edit" data-mc-field-edit title="<?php esc_attr_e( 'Editar', 'maskavo-minha-conta' ); ?>">
					<span class="screen-reader-text"><?php esc_html_e( 'Editar sobrenome', 'maskavo-minha-conta' ); ?></span>
					<?php echo $edit_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>
			<div class="maskavo-mc-field__view">
				<span class="maskavo-mc-field__value" data-mc-last-name><?php echo esc_html( $profile['last_name'] !== '' ? $profile['last_name'] : '—' ); ?></span>
			</div>
			<div class="maskavo-mc-field__edit-wrap" hidden>
				<input type="text" class="maskavo-mc-field__input" name="last_name" value="<?php echo esc_attr( $profile['last_name'] ); ?>" autocomplete="family-name" />
				<div class="maskavo-mc-field__actions">
					<button type="button" class="maskavo-mc-field__action maskavo-mc-field__action--cancel" data-mc-field-cancel title="<?php esc_attr_e( 'Cancelar', 'maskavo-minha-conta' ); ?>">
						<span class="screen-reader-text"><?php esc_html_e( 'Cancelar', 'maskavo-minha-conta' ); ?></span>
						<?php echo $close_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
					<button type="button" class="maskavo-mc-field__action maskavo-mc-field__action--save" data-mc-field-save title="<?php esc_attr_e( 'Salvar', 'maskavo-minha-conta' ); ?>">
						<span class="screen-reader-text"><?php esc_html_e( 'Salvar', 'maskavo-minha-conta' ); ?></span>
						<?php echo $check_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</div>
			</div>
		</div>
	</div>

	<p class="maskavo-mc-feedback" data-mc-feedback hidden></p>
</article>
