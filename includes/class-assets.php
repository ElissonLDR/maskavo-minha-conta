<?php
/**
 * Assets front (condicionais).
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Assets {

	/**
	 * @var bool
	 */
	private static $should_enqueue = false;

	/**
	 * Hooks.
	 */
	public static function register() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_handles' ), 5 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'maybe_enqueue' ), 30 );
	}

	/**
	 * Sempre registra handles (Elementor style/script_depends).
	 */
	public static function register_handles() {
		wp_register_style(
			'maskavo-mc-account',
			MASKAVO_MC_URL . 'assets/css/account.css',
			array(),
			MASKAVO_MC_VERSION
		);
		wp_register_script(
			'maskavo-mc-account',
			MASKAVO_MC_URL . 'assets/js/account.js',
			array(),
			MASKAVO_MC_VERSION,
			true
		);
	}

	/**
	 * Marca para enqueue (chamado pelo Renderer).
	 */
	public static function enqueue() {
		self::$should_enqueue = true;
		if ( did_action( 'wp_enqueue_scripts' ) ) {
			self::do_enqueue();
		}
	}

	/**
	 * wp_enqueue_scripts.
	 */
	public static function maybe_enqueue() {
		if ( self::$should_enqueue ) {
			self::do_enqueue();
		}
	}

	/**
	 * Enfileira CSS/JS + localize mínimo (sem dados sensíveis).
	 */
	private static function do_enqueue() {
		static $done = false;
		if ( $done ) {
			return;
		}
		$done = true;

		if ( ! wp_style_is( 'maskavo-mc-account', 'registered' ) ) {
			self::register_handles();
		}

		wp_enqueue_style( 'maskavo-mc-account' );
		wp_enqueue_script( 'maskavo-mc-account' );

		wp_localize_script(
			'maskavo-mc-account',
			'maskavoMc',
			array(
				'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
				'nonce'            => wp_create_nonce( Maskavo_MC_Guard::NONCE_ACTION ),
				'defaultAvatarUrl' => Maskavo_MC_Avatar_Service::default_url(),
				'i18n'             => array(
					'saving'           => __( 'Salvando…', 'maskavo-minha-conta' ),
					'saved'            => __( 'Salvo!', 'maskavo-minha-conta' ),
					'error'            => __( 'Não foi possível salvar. Tente de novo.', 'maskavo-minha-conta' ),
					'edit'             => __( 'Editar', 'maskavo-minha-conta' ),
					'cancel'           => __( 'Cancelar', 'maskavo-minha-conta' ),
					'save'             => __( 'Salvar', 'maskavo-minha-conta' ),
					'uploading'        => __( 'Enviando…', 'maskavo-minha-conta' ),
					'avatarOk'         => __( 'Foto atualizada.', 'maskavo-minha-conta' ),
					'avatarRemoved'    => __( 'Foto removida.', 'maskavo-minha-conta' ),
					'avatarLarge'      => __( 'A imagem deve ter no máximo 2 MB.', 'maskavo-minha-conta' ),
					'firstNameOneWord' => __( 'O nome deve ter apenas uma palavra.', 'maskavo-minha-conta' ),
					'firstNameNoNum'   => __( 'O nome não pode conter números.', 'maskavo-minha-conta' ),
					'lastNameNoNum'    => __( 'O sobrenome não pode conter números.', 'maskavo-minha-conta' ),
					'nameRequired'     => __( 'Preencha o campo antes de salvar.', 'maskavo-minha-conta' ),
				),
			)
		);
	}
}
