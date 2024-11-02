<?php
/*
Plugin Name: O.S.S Bulk Categories Loader
Plugin URI: http://www.onestepsolutions.biz
Description: This control allows your to upload multiple categories.
Version: The Plugin's Version Number, e.g.: 1.0
Author: Kamran Shahid Butt
Author URI: http://www.onestepsolutions.biz
License: A "Slug" license name e.g. GPL2
*/
?>
<?php
/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php
add_action('admin_menu', 'oss_category_menu');

function oss_category_menu()
{
	add_submenu_page( "edit.php", 'OSS Bulk Category Uploader', "Import Categories", "manage_options", 'oss-bulk-categories-import', 'oss_category_plugin_html');
}

function oss_category_plugin_html()
{

  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }
	echo '<div class="wrap">';
	echo '<div class="icon32" id="icon-edit"><br></div><h2>Import Categories</h2>';
	echo '<div class="col-container">';
	echo '<div class="form-wrap">';
		echo '<form id="frm" name="frm" enctype="multipart/form-data" method="post">';
			echo '<label for="parent">'. _ex('Parent Categories', 'Taxonomy Parent'). '</label>';
				wp_dropdown_categories(
					array(
							'hide_empty' => 0, 
							'hide_if_empty' => false, 
							'taxonomy' => "category", 
							'name' => 'parent', 
							'orderby' => 'name', 
							'hierarchical' => true, 
							'depth'        => 0,
							'order'              => 'ASC',
							'show_option_none' => __('None')
							)
						);
			echo '<label>File: </label>';
			echo '<input type="file" name="import" id="import" />';
			echo '<div><input type="submit" name="btnImport" id="btnImport" value="Start Import" /></div>';
		echo '</form>';
	echo '</div>';
	echo '</div>';
	echo '</div>';

}

if(isset($_POST['btnImport']))
{
	global $wpdb;
	$term_table = $wpdb->prefix . "terms";
	$taxonomy_table = $wpdb->prefix . "term_taxonomy";
	
	if(isset($_FILES['import']['tmp_name']))
	{
		$handle = fopen($_FILES['import']['tmp_name'], "r");
		
		while (($data = fgetcsv($handle, 1000, ",")))
		{
			$wpdb->insert( $term_table, array( 'name' => $data[0], 'slug' => $data[0], 'term_group' => '0' ) );
			$wpdb->insert( $taxonomy_table, array( 'term_id' => $wpdb->insert_id, 'taxonomy' => 'category', 'description' => $data[1], 'parent' => $_POST['parent'], 'count'=>'0' ));
		}
	}
}
?>