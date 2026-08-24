<?php
/**
 * Seção Segurança (senha).
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<header class="maskavo-mc-panel__head">
	<h2 class="maskavo-mc-panel__title"><?php esc_html_e( 'Segurança', 'maskavo-minha-conta' ); ?></h2>
	<p class="maskavo-mc-panel__lead"><?php esc_html_e( 'Altere sua senha de acesso.', 'maskavo-minha-conta' ); ?></p>
</header>

<article class="maskavo-mc-card">
	<form class="maskavo-mc-form" data-mc-password-form autocomplete="off">
		<label>
			<span><?php esc_html_e( 'Senha atual', 'maskavo-minha-conta' ); ?></span>
			<input type="password" name="current_password" required autocomplete="current-password" />
		</label>
		<label>
			<span><?php esc_html_e( 'Nova senha', 'maskavo-minha-conta' ); ?></span>
			<input type="password" name="new_password" required minlength="8" autocomplete="new-password" />
		</label>
		<label>
			<span><?php esc_html_e( 'Confirmar nova senha', 'maskavo-minha-conta' ); ?></span>
			<input type="password" name="confirm_password" required minlength="8" autocomplete="new-password" />
		</label>
		<p class="maskavo-mc-hint"><?php esc_html_e( 'Mínimo de 8 caracteres. Não compartilhe sua senha.', 'maskavo-minha-conta' ); ?></p>
		<div class="maskavo-mc-form__actions">
			<button type="submit" class="maskavo-mc-btn maskavo-mc-btn--primary"><?php esc_html_e( 'Atualizar senha', 'maskavo-minha-conta' ); ?></button>
		</div>
		<p class="maskavo-mc-feedback" data-mc-feedback hidden></p>
	</form>
</article>
