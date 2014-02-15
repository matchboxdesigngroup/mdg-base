<?php
/**
* MDG Ajax
*
* All AJAX callbacks should reside in this class for easier maintenance and testing
*
* @author Matchbox Design Group <info@matchboxdesigngroup.com>
*/
class MDG_Ajax extends MDG_Generic
{
	/**
	 * Class constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->_add_actions();
	} // __construct()


	/**
	 * Place all add_action calls here
	 *
	 * @return Void
	 */
	private function _add_actions() {
	} // _add_actions()
} // END Class MDG_Ajax()

global $mdg_ajax;
$mdg_ajax = new MDG_Ajax();