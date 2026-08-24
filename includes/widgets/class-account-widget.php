<?php
/**
 * Widget Elementor — Minha Conta.
 *
 * @package Maskavo_Minha_Conta
 */

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Account_Widget extends Widget_Base {

	public function get_name() {
		return 'maskavo_minha_conta';
	}

	public function get_title() {
		return __( 'Maskavo Minha Conta', 'maskavo-minha-conta' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return array( 'maskavo', 'general' );
	}

	public function get_keywords() {
		return array( 'conta', 'perfil', 'assinatura', 'senha', 'maskavo' );
	}

	public function get_style_depends() {
		return array( 'maskavo-mc-account' );
	}

	public function get_script_depends() {
		return array( 'maskavo-mc-account' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => __( 'Conteúdo', 'maskavo-minha-conta' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => __( 'Título', 'maskavo-minha-conta' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Minha conta', 'maskavo-minha-conta' ),
			)
		);

		$this->add_control(
			'show_profile',
			array(
				'label'        => __( 'Meus dados', 'maskavo-minha-conta' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_subscription',
			array(
				'label'        => __( 'Assinatura', 'maskavo-minha-conta' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_certificates',
			array(
				'label'        => __( 'Certificados', 'maskavo-minha-conta' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_reviews',
			array(
				'label'        => __( 'Avaliações', 'maskavo-minha-conta' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_security',
			array(
				'label'        => __( 'Segurança', 'maskavo-minha-conta' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_meus_cursos',
			array(
				'label'        => __( 'Link Meus cursos', 'maskavo-minha-conta' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Conteúdo dinâmico (não cachear HTML de conta).
	 *
	 * @return bool
	 */
	/**
	 * Evita cache Elementor do HTML da conta.
	 *
	 * @return bool
	 */
	public function is_dynamic_content(): bool {
		return true;
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo Maskavo_MC_Renderer::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML escapado nas views.
			array(
				'title'             => isset( $s['title'] ) ? (string) $s['title'] : __( 'Minha conta', 'maskavo-minha-conta' ),
				'show_profile'      => ! empty( $s['show_profile'] ) && 'yes' === $s['show_profile'],
				'show_subscription' => ! empty( $s['show_subscription'] ) && 'yes' === $s['show_subscription'],
				'show_certificates' => ! empty( $s['show_certificates'] ) && 'yes' === $s['show_certificates'],
				'show_reviews'      => ! empty( $s['show_reviews'] ) && 'yes' === $s['show_reviews'],
				'show_security'     => ! empty( $s['show_security'] ) && 'yes' === $s['show_security'],
				'show_meus_cursos'  => ! empty( $s['show_meus_cursos'] ) && 'yes' === $s['show_meus_cursos'],
			)
		);
	}
}
