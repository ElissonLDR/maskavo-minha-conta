<?php
/**
 * Assinaturas / planos do usuário (somente leitura).
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Subscription_Service {

	/**
	 * Planos do usuário (dados internos — Presenter filtra).
	 *
	 * @param int $user_id User ID.
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_for_user( $user_id ) {
		$user_id = (int) $user_id;
		if ( $user_id <= 0 ) {
			return array();
		}

		$cache_key = 'maskavo_mc_subs_' . $user_id;
		$cached    = wp_cache_get( $cache_key, 'maskavo_mc' );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$items = array();

		// Preferir mappings do plugin de matrícula (rótulos oficiais).
		$mappings = self::get_role_mappings();
		$user     = get_userdata( $user_id );
		$roles    = $user ? (array) $user->roles : array();

		foreach ( $mappings as $map ) {
			$role = isset( $map['role'] ) ? (string) $map['role'] : '';
			if ( '' === $role || ! in_array( $role, $roles, true ) ) {
				continue;
			}

			$plan_id = isset( $map['plan_id'] ) ? (int) $map['plan_id'] : 0;
			$sub     = $plan_id > 0 ? self::fetch_tutor_subscription( $user_id, $plan_id ) : null;

			$status = 'active';
			$since  = '';
			$next   = '';

			if ( $sub ) {
				$status = isset( $sub->status ) ? (string) $sub->status : 'active';
				$since  = ! empty( $sub->start_date_gmt ) ? (string) $sub->start_date_gmt : ( ! empty( $sub->created_at_gmt ) ? (string) $sub->created_at_gmt : '' );
				$next   = ! empty( $sub->next_payment_date_gmt ) ? (string) $sub->next_payment_date_gmt : '';
			}

			$items[] = array(
				'label'       => isset( $map['label'] ) ? (string) $map['label'] : $role,
				'type'        => isset( $map['type'] ) ? (string) $map['type'] : 'subscription',
				'status_raw'  => $status,
				'since_gmt'   => $since,
				'next_gmt'    => $next,
				'has_role'    => true,
			);
		}

		// Fallback: assinaturas Tutor ativas sem role mapeada.
		if ( empty( $items ) && class_exists( 'TutorPro\Subscription\Models\SubscriptionModel' ) ) {
			$model = new \TutorPro\Subscription\Models\SubscriptionModel();
			$list  = $model->get_user_active_subscriptions( $user_id );
			foreach ( (array) $list as $row ) {
				$plan_name = '';
				if ( ! empty( $row->plan ) && ! empty( $row->plan->plan_name ) ) {
					$plan_name = (string) $row->plan->plan_name;
				} elseif ( ! empty( $row->plan_name ) ) {
					$plan_name = (string) $row->plan_name;
				} else {
					$plan_name = __( 'Assinatura', 'maskavo-minha-conta' );
				}

				$items[] = array(
					'label'      => $plan_name,
					'type'       => 'subscription',
					'status_raw' => isset( $row->status ) ? (string) $row->status : 'active',
					'since_gmt'  => ! empty( $row->start_date_gmt ) ? (string) $row->start_date_gmt : ( ! empty( $row->created_at_gmt ) ? (string) $row->created_at_gmt : '' ),
					'next_gmt'   => ! empty( $row->next_payment_date_gmt ) ? (string) $row->next_payment_date_gmt : '',
					'has_role'   => false,
				);
			}
		}

		wp_cache_set( $cache_key, $items, 'maskavo_mc', 5 * MINUTE_IN_SECONDS );

		return $items;
	}

	/**
	 * Link externo para gerenciar (Hotmart). Sem IDs internos.
	 *
	 * @return string
	 */
	public static function manage_url() {
		return (string) apply_filters(
			'maskavo_mc_manage_subscription_url',
			'https://consumer.hotmart.com/'
		);
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	private static function get_role_mappings() {
		if ( class_exists( 'Maskavo_Matricula_Config' ) && method_exists( 'Maskavo_Matricula_Config', 'get_enabled' ) ) {
			return (array) Maskavo_Matricula_Config::get_enabled();
		}
		if ( class_exists( 'Maskavo_Matricula_Config' ) && method_exists( 'Maskavo_Matricula_Config', 'get_all' ) ) {
			return (array) Maskavo_Matricula_Config::get_all();
		}

		return array(
			array(
				'label'   => 'Comunidade Maskavo',
				'role'    => 'assinante_comunidade',
				'type'    => 'subscription',
				'plan_id' => 14,
			),
			array(
				'label'   => 'Combo Cookies',
				'role'    => 'assinante_combo_cookies',
				'type'    => 'subscription',
				'plan_id' => 13,
			),
			array(
				'label'   => 'Combo Tortas',
				'role'    => 'assinante_combo_tortas',
				'type'    => 'subscription',
				'plan_id' => 16,
			),
		);
	}

	/**
	 * @param int $user_id User ID.
	 * @param int $plan_id Plan ID.
	 * @return object|null
	 */
	private static function fetch_tutor_subscription( $user_id, $plan_id ) {
		if ( ! class_exists( 'TutorPro\Subscription\Models\SubscriptionModel' ) ) {
			return null;
		}
		$model = new \TutorPro\Subscription\Models\SubscriptionModel();
		$row   = $model->get_user_subscription_by_plan( $plan_id, $user_id );
		return $row ? $row : null;
	}
}
