<?php
/**
 * Datas no padrão do locale do usuário (ex.: BR = dia/mês/ano).
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Date {

	/**
	 * Formata timestamp no padrão do locale do usuário.
	 *
	 * @param int $timestamp Unix timestamp.
	 * @return string
	 */
	public static function format( $timestamp ) {
		$timestamp = (int) $timestamp;
		if ( $timestamp <= 0 ) {
			return '';
		}

		$format = self::format_string();
		return date_i18n( $format, $timestamp );
	}

	/**
	 * Formata datetime GMT/MySQL.
	 *
	 * @param string $datetime Datetime string.
	 * @param bool   $as_gmt   Se a string está em GMT/UTC.
	 * @return string
	 */
	public static function format_datetime( $datetime, $as_gmt = true ) {
		$datetime = trim( (string) $datetime );
		if ( '' === $datetime || '0000-00-00 00:00:00' === $datetime ) {
			return '';
		}

		$ts = $as_gmt ? strtotime( $datetime . ' UTC' ) : strtotime( $datetime );
		if ( ! $ts ) {
			return '';
		}

		return self::format( $ts );
	}

	/**
	 * String de formato PHP conforme locale.
	 *
	 * @return string
	 */
	public static function format_string() {
		$locale = function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = strtolower( str_replace( '-', '_', (string) $locale ) );

		/**
		 * Permite sobrescrever o formato de data da Minha Conta.
		 *
		 * @param string $format Formato PHP date.
		 * @param string $locale Locale atual.
		 */
		$format = apply_filters( 'maskavo_mc_date_format', '', $locale );
		if ( is_string( $format ) && '' !== $format ) {
			return $format;
		}

		// Português (Brasil/Portugal): 2 de setembro de 2025
		if ( 0 === strpos( $locale, 'pt' ) ) {
			return 'j \d\e F \d\e Y';
		}

		// Espanhol: 2 de septiembre de 2025
		if ( 0 === strpos( $locale, 'es' ) ) {
			return 'j \d\e F \d\e Y';
		}

		// Francês: 2 septembre 2025
		if ( 0 === strpos( $locale, 'fr' ) ) {
			return 'j F Y';
		}

		// Alemão: 2. September 2025
		if ( 0 === strpos( $locale, 'de' ) ) {
			return 'j. F Y';
		}

		// Inglês e demais: usa o formato do site, ou padrão US
		$site = (string) get_option( 'date_format' );
		return $site !== '' ? $site : 'F j, Y';
	}
}
