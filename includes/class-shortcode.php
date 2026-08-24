<?php
/**
 * Shortcode [maskavo_minha_conta].
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Shortcode {

	/**
	 * Register.
	 */
	public static function register() {
		add_shortcode( 'maskavo_minha_conta', array( __CLASS__, 'render' ) );
	}

	/**
	 * @param array|string $atts Atts.
	 * @return string
	 */
	public static function render( $atts = array() ) {
		$atts = shortcode_atts(
			array(
				'title'             => __( 'Minha conta', 'maskavo-minha-conta' ),
				'show_profile'      => 'yes',
				'show_subscription' => 'yes',
				'show_certificates' => 'yes',
				'show_reviews'      => 'yes',
				'show_security'     => 'yes',
				'show_meus_cursos'  => 'yes',
			),
			$atts,
			'maskavo_minha_conta'
		);

		return Maskavo_MC_Renderer::render(
			array(
				'title'             => (string) $atts['title'],
				'show_profile'      => 'yes' === $atts['show_profile'],
				'show_subscription' => 'yes' === $atts['show_subscription'],
				'show_certificates' => 'yes' === $atts['show_certificates'],
				'show_reviews'      => 'yes' === $atts['show_reviews'],
				'show_security'     => 'yes' === $atts['show_security'],
				'show_meus_cursos'  => 'yes' === $atts['show_meus_cursos'],
			)
		);
	}
}
