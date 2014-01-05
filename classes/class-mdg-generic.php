<?php
/**
 * This generic class is really just designed to hold random functions/methods.
 * By putting them in this generic class, we will avoid collisions and
 * make these functions easier to find.  Since you will be forced to instantiate
 * this class before you can use these functions, that instantiation will
 * tell you (and others) that the function lives here.
 *
 * @author Matchbox Design Group <info@matchboxdesigngroup.com>
 */
class MDG_Generic {
	/**
	 * Class Constructor
	 *
	 * @return Void
	 */
	public function __construct() {
	} // __construct()


	/**
	 * Checks if the current host is localhost.
	 *
	 * @return boolean If the current host is localhost.
	 */
	public function is_localhost() {
		$localhost = array( 'localhost', '127.0.0.1' );
		$host      = $_SERVER['HTTP_HOST'];

		if ( in_array( $host, $localhost ) ) {
			return true;
		} // if()

		return false;
	} // is_localhost()



	/**
	 * Checks if the current host is a staging site.
	 *
	 * @return boolean If the current host is a staging site.
	 */
	public function is_staging() {
		$staging = array( 'staging.', 'dev.' );
		$host    = $_SERVER['HTTP_HOST'];

		foreach ( $staging as $site ) {
			if ( strpos( $host, $site ) !== false ) {
				return true;
			} // if()
		} // foreach()

		return false;
	} // is_staging()



	/**
	 * Retrieves a page/post/custom post type ID when provided a slug.
	 *
	 * @param string  $slug The slug of the page/post/custom post type you want an ID for.
	 *
	 * @return integer      The ID of the page/post/custom post type
	 */
	public function get_id_by_slug( $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ){
			return $page->ID;
		}

		return null;
	} // get_id_by_slug()



	/**
	 * Adds testing post content to the supplied post type.
	 *
	 * Sample use: $mdg_generic->make_dummy_content( 'project', 'Sample Project' 20 );
	 *
	 * @param string  $post_type Required. Name of the post type to create content for.
	 * @param string  $title     Required. The title base you want to use without a trailing space, the post count will be appended to the end.
	 * @param integer $count     Required. The amount of posts you want to be added.
	 * @param string[] $options{} Optional.
	 *
	 * @return [type]            [description]
	 */
	public function make_dummy_content( $post_type, $title, $count, $options = array() ) {
		global $user_ID;

		for ( $i = 1; $i <= $count; $i++ ) {

			$text = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";

			// add an extra paragraph here and there
			$text = $i % 3 ? $text . '<br/><br/>' . $text : $text;

			// By adding one to the time the publish date increments
			$current_time = time() + $i;

			$new_post = array(
				'post_title'    => "{$title} {$i}",
				'post_content'  => $text,
				'post_status'   => 'publish',
				'post_date'     => date( 'Y-m-d H:i:s', $current_time ),
				'post_author'   => $user_ID,
				'post_type'     => $post_type,
				'post_category' => array( 0 )
			);

			$post_id = wp_insert_post( $new_post );
		} // for()
	} // make_dummy_content()



	/**
	 * Truncates a string with the supplied information
	 *
	 * Example:
	 * global $mdg_generic;
	 * $mdg_generic->truncate( $string, 30, " " )
	 *
	 * @param string  $string The string to be truncated
	 * @param integer $limit  The length of the truncated string
	 * @param string  $break  The break point
	 * @param string  $pad    The string padding to use
	 *
	 * @return string          The truncated string if $string <= $limit or the input $string
	 */
	public function truncate_string( $string, $limit, $break = '.', $pad = '...' ) {
		// return with no change if string is shorter than $limit
		if ( strlen( $string ) <= $limit ){
			return $string;
		}

		// our first test
		$test1 = strpos( $string, $break, $limit );

		// second test to make sure we didn't land on a break (won't truncate)
		$test2 = strpos( $string, $break, $limit -1 );

		// is $break present between $limit and the end of the string?
		if ( false !== ( $breakpoint = $test1 ) || false !== ( $breakpoint = $test2 ) ) {
			if ( $breakpoint < strlen( $string ) - 1 ) {
				$string = substr( $string, 0, $breakpoint ) . $pad;
			} // if()
		} // if()

		return $string;
	} // truncate_string()



	/**
	 * Creates and optionally outputs pagination
	 *
	 * @param string  $max_num_pages Optional. The amount of pages to be paginated through, defaults to the global $wp_query->max_num_pages.
	 * @param integer $range         Optional. The minimum amount of items to show
	 * @param boolean $output        Optional. Output the content
	 *
	 * @return string                    The pagination HTML
	 */
	public function pagination( $max_num_pages = null, $range = 2, $output = true ) {
		$showitems  = ( $range * 2 ) + 1;
		$pagination = '';

		global $paged;
		if ( empty( $paged ) ){
			$paged = 1;
		}

		if ( is_null( $max_num_pages ) ) {
			global $wp_query;
			$max_num_pages = $wp_query->max_num_pages;
			if ( ! $max_num_pages )
				$max_num_pages = 1;
		} // if()

		if ( 1 != $max_num_pages ) {
			$pagination .= "<div class='pagination'>";
			if ( $paged > 2 && $paged > $range + 1 && $showitems < $max_num_pages )
				$pagination .= "<a href='".get_pagenum_link( 1 )."'>&laquo;</a>";
			if ( $paged > 1 && $showitems < $max_num_pages )
				$pagination .= "<a href='".get_pagenum_link( $paged - 1 )."'>&lsaquo;</a>";

			for ( $i = 1; $i <= $max_num_pages; $i++ ) {
				if ( 1 != $max_num_pages &&( ! ( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $max_num_pages <= $showitems ) ) {
					$pagination .= ( $paged == $i )? "<span class='current'>".$i.'</span>':'<a href="'.get_pagenum_link( $i ).'" class="inactive" >'.$i.'</a>';
				} // if()
			} // for()

			if ( $paged < $max_num_pages && $showitems < $max_num_pages ) {
				$pagination .= "<a href='".get_pagenum_link( $paged + 1 )."'>&rsaquo;</a>";
			}

			if ( $paged < $max_num_pages - 1 &&  $paged + $range - 1 < $max_num_pages && $showitems < $max_num_pages ) {
				$pagination .= "<a href='".get_pagenum_link( $max_num_pages )."'>&raquo;</a>";
			}

			$pagination .= "</div>\n";
		} // if()

		if ( $output ) {
			$allowed_html = array(
				'div' => array(
					'class' => array(),
					'id'    => array(),
				),
				'a' => array(
					'href'  => array(),
					'class' => array(),
					'id'    => array(),
				),
				'span'    => array(
					'class' => array(),
					'id'    => array(),
				),
			);
			echo wp_kses( $pagination, $allowed_html );
		} // if()

		return $pagination;
	} // pagination()



	/**
	 * Retrieves the YouTube video ID from the supplied embed code
	 *
	 * @param string  $embed YouTube embed code
	 *
	 * @return [type]        [description]
	 */
	public function get_youtube_id( $embed ) {

		// pass me a link or an embed code and I'll return the youtube id for the video
		preg_match( '#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})#', $embed, $matches );
		if ( isset( $matches[2] ) && $matches[2] != '' ) {
			$youtube_id = $matches[2];
		}

		return $youtube_id;
	} // get_youtube_id()



	/**
	 *
	 *
	 * @todo Audit and document this method
	 */
	public function clean_multi_input( $multi_input = '' ) {
		// this converts the multi_input from it's saved state (fake sorta json object thingy)
		// to a php array

		// make it a valid json object
		$multi_input = str_replace( '|', '"', $multi_input );

		// decode to get make it php friendly array
		$multi_input = json_decode( $multi_input );

		return $multi_input;
	} // clean_multi_input()



	/**
	 *
	 *
	 * @todo Audit and document this method
	 */
	public function group_multi_input( $multi_input = '' ) {

		// this method will get the multi_input, clean them via this->clean_multi_input, and return them in a grouped array

		// clean/format multi_input
		$multi_input = $this->clean_multi_input( $multi_input );

		$i = 1;
		$multi_input_fields_count = 3; // this is the number of fields for each group of rewards
		$tracker = 1;
		$grouped_array  = array();

		foreach ( $multi_input as $award ) {

			// iterate through multi_input, building an award (item) with each field
			if ( $tracker == 1 ) {
				//first in group
				$item = array();
			}

			array_push( $item, $award );

			if ( $tracker == $multi_input_fields_count ) {
				// last in group

				array_push( $grouped_array, $item );

				$tracker = 1; // reset tracker

			} else {
				$tracker++;
			}

			$i++;
		}

		return $grouped_array;
	} // group_multi_input()



	/**
	 * Get attachment ID from src url
	 *
	 * @param string  $attachment_url Absolute URI to an attachment
	 *
	 * @return integer Post ID
	 */
	public function get_attachment_id_from_src( $attachment_url ) {
		global $wpdb;
		$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$attachment_url'";
		$id    = $wpdb->get_var( $query );
		return $id;
	} // get_attachment_id_from_src()
} // END Class MDG_Generic()

global $mdg_generic;
$mdg_generic = new MDG_Generic();
