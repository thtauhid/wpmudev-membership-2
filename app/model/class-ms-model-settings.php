<?php
/**
 * @copyright Incsub (http://incsub.com/)
 *
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,
 * MA 02110-1301 USA
 *
*/

/**
 * Settings model.
 *
 * Singleton. Persisted by parent class MS_Model_Option.
 *
 * @since 1.0.0
 *
 * @package Membership2
 * @subpackage Model
 */
class MS_Model_Settings extends MS_Model_Option {

	/**
	 * Singleton instance.
	 *
	 * @since 1.0.0
	 *
	 * @staticvar MS_Model_Settings
	 */
	public static $instance;

	/**
	 * Protection Message Type constants.
	 *
	 * User can set 3 different protection message defaults:
	 * - Whole page is protected
	 * - Shortcode content is protected
	 * - Read-more content is protected
	 *
	 * @since 1.0.0
	 */
	const PROTECTION_MSG_CONTENT = 'content';
	const PROTECTION_MSG_SHORTCODE = 'shortcode';
	const PROTECTION_MSG_MORE_TAG = 'more_tag';

	/**
	 * ID of the model object.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected $id = 'ms_plugin_settings';

	/**
	 * Model name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $name = 'Plugin settings';

	/**
	 * Current db version.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $version = '';

	/**
	 * Plugin enabled status indicator.
	 *
	 * @since 1.0.0
	 *
	 * @var boolean
	 */
	protected $plugin_enabled = false;

	/**
	 * Initial setup status indicator.
	 *
	 * Wizard mode.
	 *
	 * @since 1.0.0
	 *
	 * @var boolean
	 */
	protected $initial_setup = true;

	/**
	 * Is set to false when the first membership was created.
	 *
	 * @since 1.1.0
	 *
	 * @var boolean
	 */
	protected $is_first_membership = true;

	/**
	 * Is set to false when the first paid membership was created.
	 *
	 * @since 1.1.0
	 *
	 * @var boolean
	 */
	protected $is_first_paid_membership = true;

	/**
	 * Wizard step tracker.
	 *
	 * Indicate which step of the wizard.
	 *
	 * @since 1.0.0
	 *
	 * @var boolean
	 */
	protected $wizard_step = '';

	/**
	 * Hide Membership2 Menu pointer indicator.
	 *
	 * Wizard mode.
	 *
	 * @since 1.0.0
	 *
	 * @var boolean
	 */
	protected $hide_wizard_pointer = false;

	/**
	 * Hide Toolbar for non admin users indicator.
	 *
	 * Wizard mode.
	 *
	 * @since 1.0.0
	 *
	 * @var boolean
	 */
	protected $hide_admin_bar = true;

	/**
	 * The currency used in the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $currency = 'USD';

	/**
	 * The name used in the invoices.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $invoice_sender_name = '';

	/**
	 * Global payments already set indicator.
	 *
	 * @since 1.0.0
	 *
	 * @var boolean
	 */
	protected $is_global_payments_set = false;

	/**
	 * Settings data for extensions/integrations.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $custom = array();

	/**
	 * Protection Messages.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $protection_messages = array();

	/**
	 * How menu items are protected.
	 *
	 * @since 1.0.4.2
	 *
	 * @var string
	 */
	protected $menu_protection = 'item';

	/**
	 * Media / Downloads settings.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $downloads = array(
		'protection_type' => MS_Rule_Media_Model::PROTECTION_TYPE_COMPLETE,
		'masked_url' => 'downloads',
	);

	/**
	 * Import flags
	 *
	 * When data was imported a flag can be set here to remember that some
	 * members come from there.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $import = array();

	/**
	 * Special view.
	 *
	 * This defines a special view that is displayed when the plugin is loaded
	 * instead of the default plugin page that would be displayed.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $special_view = false;

	/**
	 * Get protection message types.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] The available protection message types.
	 */
	public static function get_protection_msg_types() {
		$types = array(
			self::PROTECTION_MSG_CONTENT,
			self::PROTECTION_MSG_SHORTCODE,
			self::PROTECTION_MSG_MORE_TAG,
		);

		return apply_filters( 'ms_model_settings_get_protection_msg_types', $types );
	}

	/**
	 * Validate protection message type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type The protection message type to validate.
	 * @return boolean True if valid.
	 */
	public static function is_valid_protection_msg_type( $type ) {
		$types = self::get_protection_msg_types();

		return apply_filters(
			'ms_model_settings_is_valid_protection_msg_type',
			in_array( $type, $types )
		);
	}

	/**
	 * Set protection message type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type The protection message type.
	 * @param string $msg The protection message.
	 */
	public function set_protection_message( $type, $msg ) {
		if ( self::is_valid_protection_msg_type( $type ) ) {
			$this->protection_messages[ $type ] = stripslashes( wp_kses_post( $msg ) );
		}

		do_action(
			'ms_model_settings_set_protection_message',
			$type,
			$msg,
			$this
		);
	}

	/**
	 * Get protection message type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type The protection message type.
	 * @return string $msg The protection message.
	 */
	public function get_protection_message( $type ) {
		$msg = '';
		if ( self::is_valid_protection_msg_type( $type ) ) {
			if ( isset( $this->protection_messages[ $type ] ) ) {
				$msg = $this->protection_messages[ $type ];
			} else {
				$msg = __( 'The content you are trying to access is only available to members. Sorry.', MS_TEXT_DOMAIN );
			}
		}

		return apply_filters(
			'ms_model_settings_get_protection_message',
			$msg,
			$type,
			$this
		);
	}

	/**
	 * Set custom setting.
	 *
	 * @since 1.0.0
	 *
	 * @param string $group The custom setting group.
	 * @param string $field The custom setting field.
	 * @param mixed $value The custom setting value.
	 */
	public function set_custom_setting( $group, $field, $value ) {
		$this->custom[ $group ][ $field ] = apply_filters(
			'ms_model_settings_set_custom_setting',
			$value,
			$group,
			$field,
			$this
		);
	}

	/**
	 * Get custom setting.
	 *
	 * @since 1.0.0
	 *
	 * @param string $group The custom setting group.
	 * @param string $field The custom setting field.
	 * @return mixed $value The custom setting value.
	 */
	public function get_custom_setting( $group, $field ) {
		$value = '';

		if ( ! empty( $this->custom[ $group ][ $field ] ) ) {
			$value = $this->custom[ $group ][ $field ];
		}

		return apply_filters(
			'ms_model_settings_get_custom_setting',
			$value,
			$group,
			$field,
			$this
		);
	}

	/**
	 * Activates a special view.
	 * Next time the plugin is loaded this special view is displayed.
	 *
	 * This should be set in MS_Model_Upgrade (or earlier) to ensure the special
	 * view is displayed on the current page request.
	 *
	 * @since  1.0.0
	 * @param  string $name Name of the view to display.
	 */
	static public function set_special_view( $name ) {
		$settings = MS_Factory::load( 'MS_Model_Settings' );
		$settings->special_view = $name;
		$settings->save();
	}

	/**
	 * Returns the currently set special view.
	 *
	 * @since  1.0.0
	 * @return string Name of the view to display.
	 */
	static public function get_special_view() {
		$settings = MS_Factory::load( 'MS_Model_Settings' );
		$view = $settings->special_view;
		return $view;
	}

	/**
	 * Deactivates the special view.
	 *
	 * @since  1.0.0
	 */
	static public function reset_special_view() {
		$settings = MS_Factory::load( 'MS_Model_Settings' );
		$settings->special_view = false;
		$settings->save();
	}

	/**
	 * Get available currencies.
	 *
	 * @since 1.0.0
	 *
	 * @return array {
	 *     @type string $currency The currency.
	 *     @type string $title The currency title.
	 * }
	 */
	public static function get_currencies() {
		static $Currencies = null;

		if ( null === $Currencies ) {
			$Currencies = apply_filters(
				'ms_model_settings_get_currencies',
				array(
					'AUD' => __( 'AUD - Australian Dollar', MS_TEXT_DOMAIN ),
					'BRL' => __( 'BRL - Brazilian Real', MS_TEXT_DOMAIN ),
					'CAD' => __( 'CAD - Canadian Dollar', MS_TEXT_DOMAIN ),
					'CHF' => __( 'CHF - Swiss Franc', MS_TEXT_DOMAIN ),
					'CZK' => __( 'CZK - Czech Koruna', MS_TEXT_DOMAIN ),
					'DKK' => __( 'DKK - Danish Krone', MS_TEXT_DOMAIN ),
					'EUR' => __( 'EUR - Euro', MS_TEXT_DOMAIN ),
					'GBP' => __( 'GBP - Pound Sterling', MS_TEXT_DOMAIN ),
					'HKD' => __( 'HKD - Hong Kong Dollar', MS_TEXT_DOMAIN ),
					'HUF' => __( 'HUF - Hungarian Forint', MS_TEXT_DOMAIN ),
					'ILS' => __( 'ILS - Israeli Shekel', MS_TEXT_DOMAIN ),
					'JPY' => __( 'JPY - Japanese Yen', MS_TEXT_DOMAIN ),
					'MYR' => __( 'MYR - Malaysian Ringgits', MS_TEXT_DOMAIN ),
					'MXN' => __( 'MXN - Mexican Peso', MS_TEXT_DOMAIN ),
					'NOK' => __( 'NOK - Norwegian Krone', MS_TEXT_DOMAIN ),
					'NZD' => __( 'NZD - New Zealand Dollar', MS_TEXT_DOMAIN ),
					'PHP' => __( 'PHP - Philippine Pesos', MS_TEXT_DOMAIN ),
					'PLN' => __( 'PLN - Polish Zloty', MS_TEXT_DOMAIN ),
					'RUB' => __( 'RUB - Russian Ruble', MS_TEXT_DOMAIN ),
					'SEK' => __( 'SEK - Swedish Krona', MS_TEXT_DOMAIN ),
					'SGD' => __( 'SGD - Singapore Dollar', MS_TEXT_DOMAIN ),
					'TWD' => __( 'TWD - Taiwan New Dollars', MS_TEXT_DOMAIN ),
					'THB' => __( 'THB - Thai Baht', MS_TEXT_DOMAIN ),
					'USD' => __( 'USD - U.S. Dollar', MS_TEXT_DOMAIN ),
					'ZAR' => __( 'ZAR - South African Rand', MS_TEXT_DOMAIN ),
				)
			);
		}

		return $Currencies;
	}

	/**
	 * Set specific property.
	 *
	 * @since 1.0.0
	 *
	 * @param string $property The name of a property to associate.
	 * @param mixed $value The value of a property.
	 */
	public function __set( $property, $value ) {
		if ( property_exists( $this, $property ) ) {
			switch ( $property ) {
				case 'currency':
					if ( array_key_exists( $value, self::get_currencies() ) ) {
						$this->$property = $value;
					}
					break;

				case 'invoice_sender_name':
					$this->$property = sanitize_text_field( $value );
					break;

				case 'plugin_enabled':
				case 'initial_setup':
				case 'is_first_membership':
				case 'hide_admin_bar':
					$this->$property = lib2()->is_true( $value );
					break;

				default:
					$this->$property = $value;
					break;
			}
		} else {
			switch ( $property ) {
				case 'protection_type':
					if ( MS_Rule_Media_Model::is_valid_protection_type( $value ) ) {
						$this->downloads['protection_type'] = $value;
					}
					break;

				case 'masked_url':
					$this->downloads['masked_url'] = sanitize_text_field( $value );
					break;
			}
		}
	}

	/**
	 * Returns a specific property.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $property The name of a property.
	 * @return mixed $value The value of a property.
	 */
	public function __get( $property ) {
		$value = null;

		switch ( $property ) {
			case 'menu_protection':
				if ( ! MS_Model_Addon::is_enabled( MS_Model_Addon::ADDON_ADV_MENUS ) ) {
					$value = 'item';
				} else {
					$value = $this->menu_protection;
				}
				break;

			default:
				if ( property_exists( $this, $property ) ) {
					$value = $this->$property;
				} else {
					switch ( $property ) {
						case 'currency_symbol':
							// Same translation table in:
							// -> ms-view-membership-setup-payment.js
							$symbol = $this->currency;
							switch ( $symbol ) {
								case 'USD': $symbol = '$'; break;
								case 'EUR': $symbol = '€'; break;
								case 'JPY': $symbol = '¥'; break;
							}
							$value = $symbol;
					}
				}
		}

		return apply_filters( 'ms_model_settings__get', $value, $property, $this );
	}
}
