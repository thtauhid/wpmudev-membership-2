<?php
/**
 * This file defines the MS_Controller_Help class.
 *
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
 * Controller for Plugin documentation and help.
 *
 * @since 1.1.0
 *
 * @package Membership2
 * @subpackage Controller
 */
class MS_Controller_Help extends MS_Controller {

	/**
	 * Prepare the component.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Initialize the admin-side functions.
	 *
	 * @since 2.0.0
	 */
	public function admin_init() {
		$hook = MS_Controller_Plugin::admin_page_hook( 'help' );

		$this->run_action( 'admin_print_scripts-' . $hook, 'enqueue_scripts' );
	}

	/**
	 * Load and render the Documentation view.
	 *
	 * @since 1.1.0
	 */
	public function admin_help() {
		/**
		 * Create / Filter the view.
		 *
		 * @since 1.1.0
		 * @param object $this The MS_Controller_Help object.
		 */
		$view = MS_Factory::create( 'MS_View_Help' );
		$data = array();
		$data['tabs'] = $this->get_tabs();

		$view->data = apply_filters( 'ms_view_help_data', $data );
		$view->render();
	}

	/**
	 * Get available tabs.
	 *
	 * @since 1.1.0
	 *
	 * @return array The tabs configuration.
	 */
	public function get_tabs() {
		$tabs = array(
			'general' => array(
				'title' => __( 'General', MS_TEXT_DOMAIN ),
			),
			'shortcodes' => array(
				'title' => __( 'Shortcodes', MS_TEXT_DOMAIN ),
			),
			'network' => array(
				'title' => __( 'Network-Wide Protection', MS_TEXT_DOMAIN ),
			),
			'advanced' => array(
				'title' => __( 'Advanced Settings', MS_TEXT_DOMAIN ),
			),
		);

		if ( ! is_multisite() ) {
			unset( $tabs['network'] );
		}

		lib2()->array->equip_get( 'page' );
		$def_key = MS_Controller_Plugin::MENU_SLUG . '-help';
		$page = sanitize_html_class( $_GET['page'], $def_key );

		foreach ( $tabs as $key => $tab ) {
			$tabs[ $key ]['url'] = sprintf(
				'admin.php?page=%1$s&tab=%2$s',
				esc_attr( $page ),
				esc_attr( $key )
			);
		}

		return apply_filters(
			'ms_controller_help_get_tabs',
			$tabs,
			$this
		);
	}

	/**
	 * Load specific scripts.
	 *
	 * @since 1.1.0
	 */
	public function enqueue_scripts() {
		$data = array(
			'ms_init' => array( 'view_help' ),
		);

		lib2()->ui->data( 'ms_data', $data );
		wp_enqueue_script( 'ms-admin' );
	}

}