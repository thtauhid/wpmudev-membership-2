<?php
if(!class_exists('M_Rule')) {

	class M_Rule {

		var $data;
		var $name = 'none';
		var $label = 'None Set';

		// Is this an admin side rule?
		var $adminside = false;

		function __construct() {
			$this->on_creation();

		}

		function M_Rule() {
			$this->__construct();
		}

		function admin_sidebar($data) {
			?>
			<li class='level-draggable' id='<?php echo $this->name; ?>' <?php if($data === true) echo "style='display:none;'"; ?>>
				<div class='action action-draggable'>
					<div class='action-top'>
					<?php _e($this->label,'membership'); ?>
					</div>
				</div>
			</li>
			<?php
		}

		function admin_main($data) {

		}

		// Operations
		function on_creation() {

		}

		function on_positive($data) {
			$this->data = $data;
		}

		function on_negative($data) {
			$this->data = $data;
		}

		// Getters and Setters
		function is_adminside() {
			return $this->adminside;
		}


	}

}
?>