<?php
/**
 * Troca de senha do usuário atual.
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Password_Service {

	/**
	 * Altera a senha do usuário autenticado.
	 *
	 * @param int    $user_id         User ID.
	 * @param string $current_password Senha atual.
	 * @param string $new_password     Nova senha.
	 * @param string $confirm_password Confirmação.
	 * @return true|WP_Error
	 */
	public static function change( $user_id, $current_password, $new_password, $confirm_password ) {
		$user_id = (int) $user_id;
		if ( $user_id <= 0 || $user_id !== Maskavo_MC_Guard::current_user_id() ) {
			return new WP_Error( 'forbidden', __( 'Não autorizado.', 'maskavo-minha-conta' ) );
		}

		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return new WP_Error( 'not_found', __( 'Usuário não encontrado.', 'maskavo-minha-conta' ) );
		}

		$current_password = (string) $current_password;
		$new_password     = (string) $new_password;
		$confirm_password = (string) $confirm_password;

		if ( '' === $current_password || '' === $new_password ) {
			return new WP_Error( 'empty', __( 'Preencha todos os campos de senha.', 'maskavo-minha-conta' ) );
		}

		if ( $new_password !== $confirm_password ) {
			return new WP_Error( 'mismatch', __( 'A confirmação da nova senha não confere.', 'maskavo-minha-conta' ) );
		}

		if ( strlen( $new_password ) < 8 ) {
			return new WP_Error( 'weak', __( 'A nova senha deve ter pelo menos 8 caracteres.', 'maskavo-minha-conta' ) );
		}

		if ( ! wp_check_password( $current_password, $user->user_pass, $user_id ) ) {
			return new WP_Error( 'wrong_password', __( 'Senha atual incorreta.', 'maskavo-minha-conta' ) );
		}

		if ( wp_check_password( $new_password, $user->user_pass, $user_id ) ) {
			return new WP_Error( 'same', __( 'A nova senha deve ser diferente da atual.', 'maskavo-minha-conta' ) );
		}

		wp_set_password( $new_password, $user_id );

		// Mantém a sessão atual (wp_set_password desloga em algumas versões).
		wp_set_auth_cookie( $user_id, true );
		wp_set_current_user( $user_id );

		return true;
	}
}
