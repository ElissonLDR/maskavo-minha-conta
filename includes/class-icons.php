<?php
/**
 * Ícones SVG da navegação (currentColor = contraste via CSS).
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Icons {

	/**
	 * SVG por chave de seção / atalho.
	 *
	 * @param string $key Icon key.
	 * @return string HTML seguro (SVG fixo).
	 */
	public static function get( $key ) {
		$icons = array(
			'profile'      => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8" r="3.25" stroke="currentColor" stroke-width="1.75"/><path d="M5.5 19.25c1.6-3.2 4-4.75 6.5-4.75s4.9 1.55 6.5 4.75" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>',
			'subscription' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="6" width="17" height="12" rx="2.5" stroke="currentColor" stroke-width="1.75"/><path d="M3.5 10.5h17" stroke="currentColor" stroke-width="1.75"/><path d="M7 15h3.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>',
			'certificates' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 4.75h8A2.25 2.25 0 0 1 18.25 7v7.5A2.25 2.25 0 0 1 16 16.75H8A2.25 2.25 0 0 1 5.75 14.5V7A2.25 2.25 0 0 1 8 4.75z" stroke="currentColor" stroke-width="1.75"/><path d="M9 9h6M9 12.5h4" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><path d="M14.5 16.5l1.5 4 1.75-1.25L19.5 20.5l.25-3.75" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/></svg>',
			'reviews'      => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3.75l2.35 4.76 5.25.76-3.8 3.7.9 5.24L12 15.9l-4.7 2.47.9-5.24-3.8-3.7 5.25-.76L12 3.75z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/></svg>',
			'security'     => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5.5" y="10.5" width="13" height="9" rx="2" stroke="currentColor" stroke-width="1.75"/><path d="M8.5 10.5V8a3.5 3.5 0 0 1 7 0v2.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><circle cx="12" cy="15" r="1.25" fill="currentColor"/></svg>',
			'courses'      => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4.5 7.5L12 4.75 19.5 7.5 12 10.25 4.5 7.5z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/><path d="M6.5 9.25v5.5c0 .5 2.5 2.5 5.5 2.5s5.5-2 5.5-2.5v-5.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><path d="M19.5 7.5v7" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>',
			'logout'       => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10 5.75H7.5A2.75 2.75 0 0 0 4.75 8.5v7A2.75 2.75 0 0 0 7.5 18.25H10" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><path d="M13.5 12H20M17.25 8.75L20.5 12l-3.25 3.25" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		);

		return isset( $icons[ $key ] ) ? $icons[ $key ] : '';
	}
}
