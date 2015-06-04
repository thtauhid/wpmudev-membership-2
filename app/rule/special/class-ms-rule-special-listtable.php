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
 * Membership List Table
 *
 *
 * @since 4.0.0
 *
 */
class MS_Rule_Special_ListTable extends MS_Helper_ListTable_Rule {

	protected $id = MS_Rule_Special::RULE_ID;

	public function __construct( $model ) {
		parent::__construct( $model );
		$this->name['singular'] = __( 'Special Page', MS_TEXT_DOMAIN );
		$this->name['plural'] = __( 'Special Pages', MS_TEXT_DOMAIN );
	}

	public function get_columns() {
		$columns = array(
			'cb' => true,
			'name' => __( 'Page title', MS_TEXT_DOMAIN ),
			'url' => __( 'Example', MS_TEXT_DOMAIN ),
			'access' => true,
		);

		if ( MS_Model_Membership::TYPE_DRIPPED !== $this->membership->type ) {
			unset( $columns['dripped'] );
		}

		return apply_filters(
			"ms_helper_listtable_{$this->id}_columns",
			$columns
		);
	}

	public function column_name( $item ) {
		return $item->post_title;
	}

	public function column_url( $item ) {
		return $item->url;
	}

}