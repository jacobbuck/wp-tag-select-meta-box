<?php
/*
Plugin Name: Tag Select Metabox
Plugin URI: https://github.com/jacobbuck/wp-tag-select-metabox
Description: An alternative post tag and non-hierarchal taxonomy meta box.
Version: 1.0
Author: Jacob Buck
Author URI: http://jacobbuck.co.nz/
*/

class TagSelectMetabox {
	
	private $taxonomies;
	
	function __construct () {
		add_action("admin_init", array($this, "admin_init"));
		add_action("add_meta_boxes", array($this, "add_meta_boxes"));
		add_action("init", array($this, "init"), 9999);
		add_action("save_post", array($this, "save_post"));
	}
	
	function admin_init () {
		wp_register_script("chosen.jquery", plugins_url("assets/chosen/chosen.jquery.min.js", __FILE__), array("jquery"), "0.9.8");
		wp_register_script("tagselect", plugins_url("assets/scripts.min.js", __FILE__), array("jquery", "chosen.jquery"), "1");
		wp_register_style("chosen", plugins_url("assets/chosen/chosen.min.css", __FILE__), false, "0.9.8");
		wp_register_style("tagselect", plugins_url("assets/styles.min.css", __FILE__), array("chosen"), "1");
	}
	
	function add_meta_boxes ($post_type) {
		foreach ($this->taxonomies as $tax) {
			if (is_object_in_taxonomy($post_type, $tax->name)) {
				add_action("admin_enqueue_scripts", array($this, "admin_enqueue_scripts"));
				add_meta_box( 
					"tagselect-" . $tax->name,
					__(empty($tax->tagselect["singular"]) ? $tax->label : $tax->labels->singular_name),
					array($this, "tagselect_meta_box_callback"),
					null,
					"side",
					"default",
					array("taxonomy" => $tax)
				);
				remove_meta_box("tagsdiv-" . $tax->name, $post_type, "side");
			}
		}
	}
	
	function init () {
		$this->taxonomies = get_taxonomies(array("hierarchical" => false, "show_ui" => true), "object");
	}
	
	function save_post ($post_id) {
		if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
			return;
		//if (! wp_verify_nonce($_POST["myplugin_noncename"], plugin_basename( __FILE__ )))
		//	return;
		foreach ($this->taxonomies as $tax) {
			if (isset($_POST["tagselect-" . $tax->name . "-select"])) {
				wp_set_object_terms($post_id, $_POST["tagselect-" . $tax->name . "-select"], $tax->name, false);
			}
		}
	}
		
	function admin_enqueue_scripts () {
		wp_enqueue_script("tagselect");
		wp_enqueue_style("tagselect");
	}
	
	function tagselect_meta_box_callback ($post, $box) {
		$tax = $box["args"]["taxonomy"];	
		extract(wp_parse_args(
			empty($tax->tagselect) ? array() : $tax->tagselect,
			array(
				"singular" => false,
				"hide_add" => false
			)
		));
		$disabled = ! current_user_can($tax->cap->assign_terms);
		$terms = get_terms(
			$tax->name,
			array("hide_empty" => false)
		);
		$post_terms = wp_get_object_terms(
			$post->ID,
			$tax->name,
			array("fields" => "slugs")
		);
		?>
		<div class="tagselect-wrap">
			<div class="tagselect-select-wrap">
				<?php 
				echo "<select class=\"tagselect-select\" name=\"".$box["id"]."-select[]\"".($disabled ? " disabled" : "").($singular ? "" : " multiple")." data-placeholder=\"".__("Choose a " . $tax->labels->singular_name)."&hellip;\">\n";
				if ($singular) {
					echo "<option></option>\n";
				}
				foreach ($terms as $term) {
					echo "<option value=\"$term->slug\"".(in_array($term->slug, $post_terms) ? " selected=\"selected\"" : "").">".__($term->name)."</option>\n";
				}
				echo "</select>\n";
				?>
			</div>
			<?php if (! ($hide_add || $disabled)) { ?>
				<div class="tagselect-add-wrap hide-if-no-js">
					<p><input type="text" class="tagselect-add-text" name="<?php echo $box["id"]; ?>-add-text" value="" placeholder="<?php _e($tax->label); ?>"> <input type="button" class="button tagselect-add-button" name="<?php echo $box_id; ?>-add-button" value="Add"></p>
				</div>
			<?php } ?>
		</div>
		<?php
	}
	
}

$tagselectmetabox = new TagSelectMetabox;