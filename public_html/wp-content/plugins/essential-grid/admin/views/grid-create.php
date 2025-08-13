<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

$base     = new Essential_Grid_Base();
$nav_skin = new Essential_Grid_Navigation();
$wa       = new Essential_Grid_Widget_Areas();
$meta     = new Essential_Grid_Meta();

$isCreate  = $base->getGetVar( 'create', 'true' );
$editAlias = $base->getGetVar('alias');

$grid     = false;
$layers   = false;
$settings = [ 'bg' => '' ];
$title    = esc_attr__( 'Create New Grid', 'essential-grid' );
$save     = esc_attr__( 'Save Grid', 'essential-grid');

if (intval($isCreate) > 0) {
	$grid = Essential_Grid_Db::get_entity('grids')->get_grid_by_id( $isCreate );
} elseif ($editAlias) {
	$grid = Essential_Grid_Db::get_entity('grids')->get_grid_by_handle($editAlias);
}
if (!empty($grid)) {
	$title = esc_attr__('Settings', 'essential-grid');
	$layers = $grid['layers'];
	$settings = !is_null($grid['settings']) ? $grid['settings'] : $settings;
}

$postTypesWithCats = $base->getPostTypesWithCatsForClient();

$base = new Essential_Grid_Base();

$pages = get_pages(['sort_column' => 'post_name']);

$post_elements = $base->getPostTypesAssoc();

$postTypes = $base->getVar($grid, ['postparams', 'post_types'], 'post');
$categories = $base->setCategoryByPostTypes($postTypes, $postTypesWithCats);

$selected_pages = explode(',', $base->getVar($grid, ['postparams', 'selected_pages'], '-1', 's'));

$columns = $base->getVar($grid, ['params', 'columns']);
$columns = $base->set_basic_colums($columns);

$mascontent_height = $base->getVar($grid, ['params', 'mascontent-height']);
$mascontent_height = $base->set_basic_mascontent_height($mascontent_height);

$columns_width = $base->getVar($grid, ['params', 'columns-width']);
$columns_width = $base->set_basic_colums_width($columns_width);

$columns_height = $base->getVar($grid, ['params', 'columns-height']);
$columns_height = $base->set_basic_colums_height($columns_height);

$columns_advanced = [];
if (!empty($grid)) $columns_advanced = $base->get_advanced_colums($grid['params']);

$nav_skin_choosen = $base->getVar($grid, ['params', 'navigation-skin'], 'minimal-light');
$navigation_skins = $nav_skin->get_essential_navigation_skins();

$entry_skins = Essential_Grid_Item_Skin::get_essential_item_skins();
$entry_skin_choosen = $base->getVar($grid, ['params', 'entry-skin'], '0');

$grid_animations = $base->get_grid_animations();
$start_animations = $base->get_start_animations();
$grid_item_animations = $base->get_grid_item_animations();
$hover_animations = $base->get_hover_animations();
$grid_animation_choosen = $base->getVar($grid, ['params', 'grid-animation'], 'fade');
$grid_start_animation_choosen = $base->getVar($grid, ['params', 'grid-start-animation'], 'reveal');
$grid_item_animation_choosen = $base->getVar($grid, ['params', 'grid-item-animation'], 'none');
$grid_item_animation_other = $base->getVar($grid, ['params', 'grid-item-animation-other'], 'none');
$hover_animation_choosen = $base->getVar($grid, ['params', 'hover-animation'], 'fade');

if(intval($isCreate) > 0) //currently editing, so default can be empty
	$media_source_order = $base->getVar($grid, ['postparams', 'media-source-order']);
else
	$media_source_order = $base->getVar($grid, ['postparams', 'media-source-order'], ['featured-image']);

$media_source_list = $base->get_media_source_order();

$all_image_sizes = $base->get_all_image_sizes();

$meta_keys = $meta->get_all_meta_handle();

// INIT POSTER IMAGE SOURCE ORDERS
if (intval($isCreate) > 0) {
	//currently editing, so default can be empty
	$poster_source_order = $base->getVar($grid, ['params', 'poster-source-order']);
	if ($poster_source_order == '') { //since 2.1.0
		$poster_source_order = $base->getVar($grid, ['postparams', 'poster-source-order']);
	}
} else {
	$poster_source_order = $base->getVar($grid, ['postparams', 'poster-source-order'], ['featured-image']);
}

$poster_source_list = $base->get_poster_source_order();
$esg_default_skins = $nav_skin->get_default_navigation_skins();
$esg_js_elements = $base->get_custom_elements_for_javascript();

$esg_addons = Essential_Grid_Addons::instance();
$new_addon_counter = $esg_addons->get_addons_counter();
$grid_addons = [];
if ($grid !== false) {
	$grid_addons = $esg_addons->get_grid_addons_list($grid['id'], $base->getVar($grid, ['params', 'addons'], []));
}
?>

<!-- LEFT SETTINGS -->
<h2 class="topheader">
	<?php echo esc_html($title); ?>
	<span class="topheader-buttons esg-f-right">
		<?php if (!intval($isCreate)) { ?>
		<span class="eg-tooltip-wrap esg-display-inline-block" title="<?php esc_attr_e('Save new grid to get access to addons!', 'essential-grid'); ?>">
		<?php } ?>
		<a class="esg-btn esg-blue esg-btn-addons <?php if (!intval($isCreate)) { echo 'notavailable'; } ?>" id="esg-addons-open" href="javascript:void(0);">
			<i class="material-icons">extension</i><?php esc_html_e('AddOns', 'essential-grid'); ?>
				<?php if ( $new_addon_counter ) : ?>
					<span id="esg-new-addons-counter" class="esg-new-addons-counter"><?php echo esc_html($new_addon_counter); ?></span>
				<?php endif; ?>
		</a>
		<?php if (!intval($isCreate)) { ?>
		</span>
		<?php } ?>
		<a target="_blank" class="esg-btn esg-red" href="https://www.essential-grid.com/help-center">
			<i class="material-icons">help</i>
			<?php esc_html_e('Help Center', 'essential-grid'); ?>
		</a>
	</span>
	<div class="esg-clearfix"></div>
</h2>
<div class="eg-pbox esg-box esg-box-min-width">
	<div class="esg-box-inside esg-box-inside-layout">
		
		<!-- MENU -->
		<div id="eg-create-settings-menu">
			<ul>
				<li id="esg-naming-tab" class="selected-esg-setting" data-toshow="eg-create-settings"><i class="eg-icon-cog"></i><p><?php esc_html_e('Naming', 'essential-grid'); ?></p></li>
				<li id="esg-source-tab" class="selected-source-setting" data-toshow="esg-settings-posts-settings"><i class="eg-icon-folder"></i><p><?php esc_html_e('Source', 'essential-grid'); ?></p></li>
				<li id="esg-grid-settings-tab" data-toshow="esg-settings-grid-settings"><i class="eg-icon-menu"></i><p><?php esc_html_e('Grid Settings', 'essential-grid'); ?></p></li>
				<li id="esg-filterandco-tab" data-toshow="esg-settings-filterandco-settings"><i class="eg-icon-shuffle"></i><p><?php esc_html_e('Nav-Filter-Sort', 'essential-grid'); ?></p></li>
				<li id="esg-skins-tab" data-toshow="esg-settings-skins-settings"><i class="eg-icon-droplet"></i><p><?php esc_html_e('Skins', 'essential-grid'); ?></p></li>
				<li data-toshow="esg-settings-animations-settings"><i class="eg-icon-tools"></i><p><?php esc_html_e('Animations', 'essential-grid'); ?></p></li>
				<li data-toshow="esg-settings-lightbox-settings"><i class="eg-icon-search"></i><p><?php esc_html_e('Lightbox', 'essential-grid'); ?></p></li>
				<li data-toshow="esg-settings-ajax-settings"><i class="eg-icon-ccw-1"></i><p><?php esc_html_e('Ajax', 'essential-grid'); ?></p></li>
				<li data-toshow="esg-settings-spinner-settings"><i class="eg-icon-back-in-time"></i><p><?php esc_html_e('Spinner', 'essential-grid'); ?></p></li>
				<li data-toshow="esg-settings-api-settings"><i class="eg-icon-magic"></i><p><?php esc_html_e('API/JavaScript', 'essential-grid'); ?></p></li>
				<li data-toshow="esg-settings-cookie-settings"><i class="eg-icon-eye"></i><p><?php esc_html_e('Cookies', 'essential-grid'); ?></p></li>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo apply_filters('essgrid_grid_create_menu', '', $grid); 
				?>
			</ul>
		 </div>

		<!-- NAMING -->
		<div id="eg-create-settings" class="esg-settings-container active-esc">
			<div>
				<div class="eg-cs-tbc-left">
					<esg-llabel><span><?php esc_html_e('Naming', 'essential-grid'); ?></span></esg-llabel>
				</div>
				<div class="eg-cs-tbc">
					<input type="hidden" name="eg-settings" value='<?php echo wp_json_encode($settings); ?>' />
					
					<?php $pg = 'false';?>
					<?php if ($grid !== false) { 
						$pg = $base->getVar($grid, ['params', 'pg'], 'false');
						?>
						<input type="hidden" name="eg-id" value="<?php echo esc_attr($grid['id']); ?>" />
					<?php } ?>
					
					<input type="hidden" name="eg-pg" value="<?php echo esc_attr($pg); ?>" />
					<?php if ($pg === 'true') { ?>
						<div class="esg-pg-wrap">
							<div class="esg-pg-title"><?php esc_html_e('PREMIUM TEMPLATE', 'essential-grid'); ?></div>
							<div class="esg-pg-content">
								<?php 
								echo sprintf(
									/* translators: link to ESG templates library */
									esc_html__( 'This is a Premium template from the Essential Grid %s.', 'essential-grid' ),
									'<a target="_blank" rel="noopener" href="https://www.essential-grid.com/grids/">'
									. esc_html__('template library', 'essential-grid')
									. '</a>'
								); 
								
								echo sprintf(
									/* translators: link to ESG manual */
									esc_html__( 'It can only be used on this website with a %s.', 'essential-grid' ),
									'<a target="_blank" rel="noopener" href="https://www.essential-grid.com/manual/installing-activating-and-registering-essential-grid/">'
									. esc_html__('registered license key', 'essential-grid')
									. '</a>'
								); 
								?>
							</div>
						</div>
					<?php } ?>
					
					<div>
						<label for="name" class="eg-tooltip-wrap" title="<?php esc_attr_e('Name of the grid', 'essential-grid'); ?>">
							<?php esc_html_e('Title', 'essential-grid'); ?> *
						</label>
						<input type="text" name="name" value="<?php echo esc_attr($base->getVar($grid, 'name', '', 's')); ?>" />
					</div>
					<div class="div13"></div>
					<div>
						<label for="handle" class="eg-tooltip-wrap" title="<?php esc_attr_e('Technical alias without special chars and white spaces', 'essential-grid'); ?>">
							<?php esc_html_e('Alias', 'essential-grid'); ?> *
						</label>
						<input type="text" name="handle" value="<?php echo esc_attr($base->getVar($grid, 'handle', '', 's')); ?>" />
					</div>
					<div class="div13"></div>
					<div>
						<label for="shortcode" class="eg-tooltip-wrap" title="<?php esc_attr_e('Copy this shortcode to paste it to your pages or posts content', 'essential-grid'); ?>" >
							<?php esc_html_e('Shortcode', 'essential-grid'); ?>
						</label>
						<input type="text" id="esg_shortcode" name="shortcode" value="" readonly="readonly" />
						<div id="esg_copy_shortcode" class="esg-copy-btn esg-margin-l-10">
							<i class="material-icons">content_copy</i>
						</div>
					</div>
					<div class="div13"></div>
					<div>
						<label for="id" class="eg-tooltip-wrap" title="<?php esc_attr_e('Add a unique ID to be able to add CSS to certain Grids', 'essential-grid'); ?>">
							<?php esc_html_e('CSS ID', 'essential-grid'); ?>
						</label>
						<input type="text" name="css-id" id="esg-id-value" value="<?php echo esc_attr($base->getVar($grid, ['params', 'css-id'], '', 's')); ?>" />
					</div>
				</div>
			</div>
		</div>

		<!-- SOURCE -->
		<div id="esg-settings-posts-settings" class="esg-settings-container">
			<div>
				<form id="eg-form-create-posts">
					<div id="esg-source-type-wrap">
						<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Source', 'essential-grid'); ?></span></esg-llabel></div>
						<div class="eg-cs-tbc ">
							<label for="shortcode" class="eg-tooltip-wrap" title="<?php esc_attr_e('Choose source of grid items', 'essential-grid'); ?>"><?php esc_html_e('Based on', 'essential-grid'); ?></label><!--
							--><div class="esg-staytog"><input type="radio" name="source-type" value="post" class="esg-source-choose-wrapper" <?php checked($base->getVar($grid, ['postparams', 'source-type'], 'post'), 'post'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Items from Posts, Custom Posts', 'essential-grid'); ?>"><?php esc_html_e('Post, Pages, Custom Posts', 'essential-grid'); ?></span><div class="space18"></div></div><!--
							--><div class="esg-staytog"><input type="radio" name="source-type" value="custom" class="esg-source-choose-wrapper" <?php echo checked($base->getVar($grid, ['postparams', 'source-type'], 'post'), 'custom'); ?> ><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Items from the Media Gallery (Bulk Selection, Upload Possible)', 'essential-grid'); ?>"><?php esc_html_e('Custom Grid (Editor Below)', 'essential-grid'); ?></span><div class="space18"></div></div>
							<?php do_action('essgrid_grid_source', $base, $grid); ?>
						</div>
					</div>

					<div id="custom-sorting-wrap" class="esg-display-none">
						<ul id="esg-custom-li-sorter" class="esg-margin-0">
						</ul>
					</div>
					<div id="post-pages-wrap">
						<div>
							<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Type and Category', 'essential-grid'); ?></span></esg-llabel></div>
							<div class="eg-cs-tbc">
								<label for="post_types" class="eg-tooltip-wrap" title="<?php esc_attr_e('Select Post Types (multiple selection possible)', 'essential-grid'); ?>"><?php esc_html_e('Post Types', 'essential-grid'); ?></label><!--
								--><select name="post_types" size="5" multiple="multiple">
									<?php
									$selectedPostTypes = [];
									$post_types = $base->getVar($grid, ['postparams', 'post_types'], 'post');
									if(!empty($post_types))
										$selectedPostTypes = explode(',',$post_types);
									else
										$selectedPostTypes = ['post'];

									if(!empty($post_elements)){
										// 3.0.12
										$foundOne = false;
										foreach($post_elements as $handle => $name){
											if(!$foundOne && in_array($handle, $selectedPostTypes)) {
												$foundOne = true;
											}
										}
										$postTypeCount = 0;
										foreach($post_elements as $handle => $name){
											if($postTypeCount === 0 && !$foundOne) {
												$selected = ' selected';
											} else {
												$selected = in_array($handle, $selectedPostTypes) ? ' selected' : '';
											}
											?>
											<option value="<?php echo esc_attr($handle); ?>"<?php echo esc_attr($selected); ?>><?php echo esc_html($name); ?></option>
											<?php
											$postTypeCount++;
										}
									}
									?>
								</select>

								<div id="eg-post-cat-wrap">
									<div class="div13"></div>
									<label for="post_category" class="eg-tooltip-wrap" title="<?php esc_attr_e('Select Categories and Tags (multiple selection possible)', 'essential-grid'); ?>"><?php esc_html_e('Post Categories', 'essential-grid'); ?></label><!--
									--><select id="post_category" name="post_category" size="7" multiple="multiple" >
										<?php
										$selectedCats = [];
										$post_cats = $base->getVar($grid, ['postparams', 'post_category']);
										if(!empty($post_cats))
											$selectedCats = explode(',',$post_cats);
										else
											$selectedCats = [];
										
										foreach ($categories as $handle => $cat) {
											$selected = false;
											$isDisabled = strpos($handle, 'option_disabled_') !== false;
											if(!$isDisabled) {
												$selected = in_array($handle, $selectedCats);
											}
											?>
											<option value="<?php echo esc_attr($handle); ?>"<?php echo $selected ? ' data-selected="true"' : ''; ?><?php echo $isDisabled ? ' disabled="disabled"' : ''; ?>><?php echo esc_html($cat); ?></option>
											<?php
										}
										?>
									</select>

									<div class="div15"></div>
									<label>&nbsp;</label>
									<a class="esg-btn esg-purple eg-clear-taxonomies" href="javascript:void(0);"><?php esc_html_e('Clear', 'essential-grid'); ?></a>
									<a class="esg-btn esg-purple eg-categories-taxonomies" href="javascript:void(0);"><?php esc_html_e('All Categories', 'essential-grid'); ?></a>
									<a class="esg-btn esg-purple eg-tags-taxonomies" href="javascript:void(0);"><?php esc_html_e('All Tags', 'essential-grid'); ?></a>
									<a class="esg-btn esg-purple eg-all-taxonomies" href="javascript:void(0);"><?php esc_html_e('All Items', 'essential-grid'); ?></a>
									
									<div class="div5"></div>
									<label for="category-relation"><?php esc_html_e('Category Relation', 'essential-grid'); ?></label><!--
								--><span class="esg-display-inline-block"><input type="radio" value="OR" name="category-relation" <?php checked($base->getVar($grid, ['postparams', 'category-relation'], 'OR'), 'OR'); ?> ><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Post need to be in one of the selected categories/tags', 'essential-grid'); ?>"><?php esc_html_e('OR', 'essential-grid'); ?></span></span><div class="space18"></div><!--
								--><span><input type="radio" value="AND" name="category-relation" <?php checked($base->getVar($grid, ['postparams', 'category-relation'], 'OR'), 'AND'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Post need to be in all categories/tags selected', 'essential-grid'); ?>"><?php esc_html_e('AND', 'essential-grid'); ?></span></span>
									
								</div>
								
								<div id="eg-additional-post">
									<div class="div13"></div>
									<label for="additional-query" class="eg-tooltip-wrap" title="<?php esc_attr_e('Please use it like \'year=2012&monthnum=12\'', 'essential-grid'); ?>"><?php esc_html_e('Additional Parameters', 'essential-grid'); ?></label><!--
									--><input type="text" name="additional-query" class="eg-additional-parameters esg-w-305" value="<?php echo esc_attr($base->getVar($grid, ['postparams', 'additional-query'])); ?>" />
									<div>
										<label></label>
										<?php esc_html_e('Please use it like \'year=2012&monthnum=12\' or \'post__in=array(1,2,5)\'', 'essential-grid'); ?>&nbsp;-&nbsp;
										<?php
										printf(
											/* translators: 1:open link tag to Wordpress Codex 2:close link tag */
											esc_html__('For a full list of parameters, please visit  %1$sWordpress Codex%2$s', 'essential-grid'),
											'<a href="https://codex.wordpress.org/Class_Reference/WP_Query" target="_blank">',
											'</a>'
										);
										?>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div id="set-pages-wrap">
						<div>
							<div class="eg-cs-tbc-left">
								<esg-llabel><span><?php esc_html_e('Pages', 'essential-grid'); ?></span></esg-llabel>
							</div>
							<div class="eg-cs-tbc">
								<label for="pages" class="eg-tooltip-wrap" title="<?php esc_attr_e('Additional filtering on pages,Start to type a page title for pre selection', 'essential-grid'); ?>"><?php esc_html_e('Select Pages', 'essential-grid'); ?></label><!--
								--><input type="text" id="pages" value="" name="search_pages"> <a class="esg-btn esg-purple" id="button-add-pages" href="javascript:void(0);"><i class="material-icons">add</i></a>
								<div id="pages-wrap">
									<?php
									if(!empty($pages)){
										foreach($pages as $page){
											if(in_array($page->ID, $selected_pages)){
												?>
												<div class="esg-page-list-element-wrap"><div class="esg-page-list-element" data-id="<?php echo esc_attr($page->ID); ?>"><?php echo esc_html(str_replace('"', '', $page->post_title).' (ID: '.$page->ID.')'); ?></div><div class="esg-btn esg-red del-page-entry"><i class="eg-icon-trash"></i></div></div>
												<?php
											}
										}
									}
									?>
								</div>
								<select name="selected_pages" multiple="multiple" class="esg-display-none">
									<?php
									if (!empty($pages)) {
										foreach ($pages as $page) { ?>
											<option value="<?php echo esc_attr($page->ID); ?>"<?php echo (in_array($page->ID, $selected_pages)) ? ' selected' : ''; ?>><?php echo esc_html(str_replace('"', '', $page->post_title).' (ID: '.$page->ID.')'); ?></option>
										<?php
										}
									}
									?>
								</select>
							</div>
						</div>

					</div>

					<div id="aditional-pages-wrap">
						<div>
							<div class="eg-cs-tbc-left">
								<esg-llabel><span><?php esc_html_e('Options', 'essential-grid'); ?></span></esg-llabel>
							</div>
							<div class="eg-cs-tbc">
								<?php
								$max_entries = intval($base->getVar($grid, ['postparams', 'max_entries'], '-1'));
								?>
								<label for="pages" class="eg-tooltip-wrap" title="<?php esc_attr_e('Defines a posts limit, use only numbers, -1 will disable this option, use only numbers', 'essential-grid'); ?>"><?php esc_html_e('Maximum Posts', 'essential-grid'); ?></label><!--
								--><input type="number" value="<?php echo esc_attr($max_entries); ?>" name="max_entries">
								<div class="div13"></div>
								<?php
								$max_entries_preview = intval($base->getVar($grid, ['postparams', 'max_entries_preview'], '20'));
								?>

								<label for="pages" class="eg-tooltip-wrap" title="<?php esc_attr_e('Defines a posts limit, use only numbers, -1 will disable this option, use only numbers', 'essential-grid'); ?>"><?php esc_html_e('Maximum Posts Preview', 'essential-grid'); ?></label><!--
								--><input type="number" value="<?php echo esc_attr($max_entries_preview); ?>" name="max_entries_preview">

							</div>
						</div>

					</div>

					<div id="all-stream-wrap">
						<div id="external-stream-wrap">
							<div>
								<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Service', 'essential-grid'); ?></span></esg-llabel></div>
								<div class="eg-cs-tbc esg-stream-source-type-container">
									<label for="shortcode" class="eg-tooltip-wrap" title="<?php esc_attr_e('Choose source of grid items', 'essential-grid'); ?>"><?php esc_html_e('Provider', 'essential-grid'); ?></label>
								</div>
							</div>
						</div>
					</div>
					<?php do_action('essgrid_grid_source_options',$base,$grid); ?>
					
					<div id="media-source-order-wrap">
						<div>
							<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Media Source', 'essential-grid'); ?></span></esg-llabel></div>
							<div class="eg-cs-tbc">
								<div class="esg-msow-inner">
									<div class="esg-msow-inner-container">
										<div class="eg-tooltip-wrap" title="<?php esc_attr_e('Set default order of used media', 'essential-grid'); ?>"><?php esc_html_e('Item Media Source Order', 'essential-grid'); ?></div>
										<div id="imso-list" class="eg-media-source-order-wrap eg-media-source-order-wrap-additional">
											<?php
											if(!empty($media_source_order)){
												foreach($media_source_order as $media_handle){
													if(!isset($media_source_list[$media_handle])) continue;
													?>
													<div id="imso-<?php echo esc_attr($media_handle); ?>" class="eg-media-source-order esg-blue esg-btn"><i class="eg-icon-<?php echo esc_attr($media_source_list[$media_handle]['type']); ?>"></i><span><?php echo esc_html($media_source_list[$media_handle]['name']); ?></span><input class="eg-get-val" type="checkbox" name="media-source-order[]" checked="checked" value="<?php echo esc_attr($media_handle); ?>" /></div>
													<?php
													unset($media_source_list[$media_handle]);
												}
											}
	
											if(!empty($media_source_list)){
												foreach($media_source_list as $media_handle => $media_set){
													?>
													<div id="imso-<?php echo esc_attr($media_handle); ?>" class="eg-media-source-order esg-purple esg-btn"><i class="eg-icon-<?php echo esc_attr($media_set['type']); ?>"></i><span><?php echo esc_html($media_set['name']); ?></span><input class="eg-get-val" type="checkbox" name="media-source-order[]" value="<?php echo esc_attr($media_handle); ?>" /></div>
													<?php
												}
											}
											?>
										</div>
									</div>
									<div id="poster-media-source-container" class="eg-poster-media-source-container">
										<div class="eg-tooltip-wrap" title="<?php esc_attr_e('Set the default order of Poster Image Source', 'essential-grid'); ?>"><?php esc_html_e('Optional Audio/Video Image Order', 'essential-grid'); ?></div>
										<div id="pso-list" class="eg-media-source-order-wrap eg-media-source-order-wrap-additional">
											<?php
											if(!empty($poster_source_order)){
												foreach($poster_source_order as $poster_handle){
													if(!isset($poster_source_list[$poster_handle])) continue;
													?>
													<div id="pso-<?php echo esc_attr($poster_handle); ?>" class="eg-media-source-order esg-purple esg-btn"><i class="eg-icon-<?php echo esc_attr($poster_source_list[$poster_handle]['type']); ?>"></i><span><?php echo esc_html($poster_source_list[$poster_handle]['name']); ?></span><input class="eg-get-val" type="checkbox" name="poster-source-order[]" checked="checked" value="<?php echo esc_attr($poster_handle); ?>" /></div>
													<?php
													unset($poster_source_list[$poster_handle]);
												}
											}
	
											if(!empty($poster_source_list)){
												foreach($poster_source_list as $poster_handle => $poster_set){
													?>
													<div id="pso-<?php echo esc_attr($poster_handle); ?>" class="eg-media-source-order esg-purple esg-btn"><i class="eg-icon-<?php echo esc_attr($poster_set['type']); ?>"></i><span><?php echo esc_html($poster_set['name']); ?></span><input class="eg-get-val" type="checkbox" name="poster-source-order[]" value="<?php echo esc_attr($poster_handle); ?>" /></div>
													<?php
												}
											}
											?>
										</div>
									</div>
									<div><?php esc_html_e('First Media Source will be loaded as default. In case one source does not exist, next available media source in this order will be used', 'essential-grid'); ?></div>
								</div>
							</div>
						</div>
					</div>

				<div id="media-source-sizes">
					<div>
						<div class="eg-cs-tbc-left">
							<esg-llabel><span><?php esc_html_e('Source Size', 'essential-grid'); ?></span></esg-llabel>
						</div>
						<div class="eg-cs-tbc eg-cs-tbc-padding-top">
							
							<?php $image_source_smart = $base->getVar($grid, ['postparams', 'image-source-smart'], 'off');?>
							<label for="image-source-smart" class="eg-tooltip-wrap" title="<?php esc_attr_e('Grid will try to detect user device and use optimized image sizes', 'essential-grid'); ?>"><?php esc_html_e('Enable Smart Image Size', 'essential-grid'); ?></label><!--
							--><span><input type="radio" name="image-source-smart" value="on" <?php checked($image_source_smart, 'on'); ?> /><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
							--><span><input type="radio" name="image-source-smart" value="off" <?php checked($image_source_smart, 'off'); ?> /><?php esc_html_e('Off', 'essential-grid'); ?></span>
							<div class="div13"></div>

							<div>
								<!-- DEFAULT IMAGE SOURCE -->
								<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Desktop Grid Image Source Size', 'essential-grid'); ?>"><?php esc_html_e('Desktop Image Source Type', 'essential-grid'); ?></label><!--
								--><?php $image_source_type = $base->getVar($grid, ['postparams', 'image-source-type'], 'full');?><select name="image-source-type">
									<?php
									foreach($all_image_sizes as $handle => $name){
										?>
										<option <?php selected($image_source_type, $handle); ?> value="<?php echo esc_attr($handle); ?>"><?php echo esc_html($name); ?></option>
										<?php
									}
									?>
								</select>
							</div>
							<div class="div13"></div>

							<!-- DEFAULT IMAGE SOURCE -->
							<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Mobile Grid Image Source Size', 'essential-grid'); ?>"><?php esc_html_e('Mobile Image Source Type', 'essential-grid'); ?></label><!--
							--><?php $image_source_type = $base->getVar($grid, ['postparams', 'image-source-type-mobile'], $image_source_type);?><select name="image-source-type-mobile">
								<?php
								foreach($all_image_sizes as $handle => $name){
									?>
									<option <?php selected($image_source_type, $handle); ?> value="<?php echo esc_attr($handle); ?>"><?php echo esc_html($name); ?></option>
									<?php
								}
								?>
							</select>

						</div>

					</div>
				</div>
				
				<div id="media-source-default-templates">
					<div>
						<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Default Source', 'essential-grid'); ?></span></esg-llabel></div>
						<?php
						$default_img = $base->getVar($grid, ['postparams', 'default-image'], 0, 'i');
						$var_src = '';
						if($default_img > 0){
							$img = wp_get_attachment_image_src($default_img, 'full');
							if($img !== false){
								$var_src = $img[0];
							}
						}
						?>
						<div class="eg-cs-tbc">
							<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Image will be used if no criteria are matching so a default image will be shown', 'essential-grid'); ?>"><?php esc_html_e('Default Image', 'essential-grid'); ?></label><!--
							--><div class="esg-btn esg-purple eg-default-image-add" data-setto="eg-default-image"><?php esc_html_e('Choose Image', 'essential-grid'); ?></div><!--
							--><div class="esg-btn  esg-red  eg-default-image-clear" data-setto="eg-default-image"><?php esc_html_e('Remove Image', 'essential-grid'); ?></div><!--
							--><input type="hidden" name="default-image" value="<?php echo !empty($default_img) ? esc_attr($default_img) : ""; ?>" id="eg-default-image" /><!--
							--><div class="eg-default-image-container"><img id="eg-default-image-img" class="image-holder-wrap-div<?php echo ($var_src == '') ? ' esg-display-none' : ''; ?>" src="<?php echo esc_url($var_src); ?>"  alt=""/></div>
						</div>
					</div>
				</div>

				<div class=" default-posters notavailable" id="eg-youtube-default-poster">
					<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('YouTube Poster', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc">
						<?php
						$youtube_default_img = $base->getVar($grid, ['postparams', 'youtube-default-image'], 0, 'i');
						$var_src = '';
						if($youtube_default_img > 0){
							$youtube_img = wp_get_attachment_image_src($youtube_default_img, 'full');
							if($youtube_img !== false){
								$var_src = $youtube_img[0];
							}
						}
						?>
						<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Set the default posters for the different video sources', 'essential-grid'); ?>"><?php esc_html_e('Default Poster', 'essential-grid'); ?></label><!--
						--><div class="esg-btn esg-purple eg-youtube-default-image-add" data-setto="eg-youtube-default-image"><?php esc_html_e('Choose Image', 'essential-grid'); ?></div><!--
						--><div class="esg-btn esg-red eg-youtube-default-image-clear" data-setto="eg-youtube-default-image"><?php esc_html_e('Remove Image', 'essential-grid'); ?></div>
						<input type="hidden" name="youtube-default-image" value="<?php echo !empty($youtube_default_img) ? esc_attr($youtube_default_img) : '' ; ?>" id="eg-youtube-default-image" /><!--
						--><div class="eg-default-image-container"><img id="eg-youtube-default-image-img" class="image-holder-wrap-div<?php echo ($var_src == '') ? ' esg-display-none' : ''; ?>" src="<?php echo esc_url($var_src); ?>"  alt=""/></div>
					</div>
				</div>

				<div class=" default-posters notavailable" id="eg-vimeo-default-poster">
					<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Vimeo Poster', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc">
						<?php
						$vimeo_default_img = $base->getVar($grid, ['postparams', 'vimeo-default-image'], 0, 'i');
						$var_src = '';
						if($vimeo_default_img > 0){
							$vimeo_img = wp_get_attachment_image_src($vimeo_default_img, 'full');
							if($vimeo_img !== false){
								$var_src = $vimeo_img[0];
							}
						}
						?>
						<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Set the default posters for the different video sources', 'essential-grid'); ?>"><?php esc_html_e('Default Poster', 'essential-grid'); ?></label><!--
						--><div class="esg-btn esg-purple eg-vimeo-default-image-add"  data-setto="eg-vimeo-default-image"><?php esc_html_e('Choose Image', 'essential-grid'); ?></div><!--
						--><div class="esg-btn esg-red eg-vimeo-default-image-clear"  data-setto="eg-vimeo-default-image"><?php esc_html_e('Remove Image', 'essential-grid'); ?></div>
						<input type="hidden" name="vimeo-default-image" value="<?php echo !empty($vimeo_default_img) ? esc_attr($vimeo_default_img) : ''; ?>" id="eg-vimeo-default-image" /><!--
						--><div class="eg-default-image-container"><img id="eg-vimeo-default-image-img" class="image-holder-wrap-div<?php echo ($var_src == '') ? ' esg-display-none' : ''; ?>" src="<?php echo esc_url($var_src); ?>"  alt=""/></div>
					</div>
				</div>

				<div class=" default-posters notavailable" id="eg-html5-default-poster">

					<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('HTML5 Poster', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc">
						<?php
						$html_default_img = $base->getVar($grid, ['postparams', 'html-default-image'], 0, 'i');
						$var_src = '';
						if($html_default_img > 0){
							$html_img = wp_get_attachment_image_src($html_default_img, 'full');
							if($html_img !== false){
								$var_src = $html_img[0];
							}
						}
						?>
						<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Set the default posters for the different video sources', 'essential-grid'); ?>"><?php esc_html_e('Default Poster', 'essential-grid'); ?></label><!--
						--><div class="esg-btn esg-purple eg-html-default-image-add"  data-setto="eg-html-default-image"><?php esc_html_e('Choose Image', 'essential-grid'); ?></div><!--
						--><div class="esg-btn esg-red eg-html-default-image-clear"  data-setto="eg-html-default-image"><?php esc_html_e('Remove Image', 'essential-grid'); ?></div>
						<input type="hidden" name="html-default-image" value="<?php echo !empty($html_default_img) ? esc_attr($html_default_img) : ''; ?>" id="eg-html-default-image" /><!--
						--><div class="eg-default-image-container"><img id="eg-html-default-image-img" class="image-holder-wrap-div<?php echo ($var_src == '') ? ' esg-display-none' : ''; ?>" src="<?php echo esc_url($var_src); ?>"  alt=""/></div>
					</div>
				</div>
				<div id="gallery-wrap"></div>

				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo apply_filters('essgrid_grid_form_create_posts', '', $grid);
				?>
					
				</form>
			</div>
		</div>
		
		<?php require_once('elements/grid-settings.php'); ?>

		<div id="custom-element-add-elements-wrapper">
			<div>
				<div class="eg-cs-tbc-left">
					<esg-llabel><span><?php esc_html_e('Add Items', 'essential-grid'); ?></span></esg-llabel>
				</div>
				<div class="eg-cs-tbc">
					<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Add element to Custom Grid', 'essential-grid'); ?>"><?php esc_html_e('Add', 'essential-grid'); ?></label><!--
					--><div class="esg-btn esg-purple esg-open-edit-dialog" id="esg-add-new-custom-youtube-top"><i class="eg-icon-youtube-squared"></i><?php esc_html_e('You Tube', 'essential-grid'); ?></div><!--
					--><div class="esg-btn esg-purple esg-open-edit-dialog" id="esg-add-new-custom-vimeo-top"><i class="eg-icon-vimeo-squared"></i><?php esc_html_e('Vimeo', 'essential-grid'); ?></div><!--
					--><div class="esg-btn esg-purple esg-open-edit-dialog" id="esg-add-new-custom-html5-top"><i class="eg-icon-video"></i><?php esc_html_e('Self Hosted Media', 'essential-grid'); ?></div><!--
					--><div class="esg-btn esg-purple esg-open-edit-dialog" id="esg-add-new-custom-image-top"><i class="eg-icon-picture-1"></i><?php esc_html_e('Image(s)', 'essential-grid'); ?></div><!--
					--><div class="esg-btn esg-purple esg-open-edit-dialog" id="esg-add-new-custom-soundcloud-top"><i class="eg-icon-soundcloud"></i><?php esc_html_e('Sound Cloud', 'essential-grid'); ?></div><!--
					--><div class="esg-btn esg-purple esg-open-edit-dialog" id="esg-add-new-custom-text-top"><i class="eg-icon-font"></i><?php esc_html_e('Simple Content', 'essential-grid'); ?></div><!--
					--><div class="esg-btn esg-purple esg-open-edit-dialog" id="esg-add-new-custom-blank-top"><i class="eg-icon-cancel"></i><?php esc_html_e('Blank Item', 'essential-grid'); ?></div>
				</div>
			</div>

		</div>

		<div class="save-wrap-settings">
			<div class="sws-toolbar-button"><a class="esg-btn esg-green" href="javascript:void(0);" id="eg-btn-save-grid"><i class="rs-icon-save-light"></i><?php echo esc_html($save); ?></a></div>
			<div class="sws-toolbar-button"><a class="esg-btn esg-purple esg-refresh-preview-button"><i class="eg-icon-arrows-ccw"></i><?php esc_html_e('Refresh Preview', 'essential-grid'); ?></a></div>
			<div class="sws-toolbar-button"><a class="esg-btn esg-blue" href="<?php echo esc_url(Essential_Grid_Base::getViewUrl(Essential_Grid_Admin::VIEW_OVERVIEW)); ?>"><i class="eg-icon-cancel"></i><?php esc_html_e('Close', 'essential-grid'); ?></a></div>
			<div class="sws-toolbar-button"><?php if($grid !== false){ ?> <a class="esg-btn esg-red" href="javascript:void(0);" id="eg-btn-delete-grid"><i class="eg-icon-trash"></i><?php esc_html_e('Delete Grid', 'essential-grid'); ?></a><?php } ?></div>
		</div>
	</div>
</div>

<div class="clear"></div>

<?php
if(intval($isCreate) == 0){ 
	//currently editing
	echo '<div id="eg-create-step-3">';
}
?>

<div class="esg-editor-space"></div>
<h2><?php esc_html_e('Editor / Preview', 'essential-grid'); ?></h2>
<form id="eg-custom-elements-form-wrap">
	<div id="eg-live-preview-wrap">
		<?php
		Essential_Grid_Global_Css::output_global_css_styles_wrapped();
		?>
		<div id="esg-preview-wrapping-wrapper">
			<?php
			if($base->getVar($grid, ['postparams', 'source-type'], 'post') == 'custom'){
				$layers = @$grid['layers']; //no stripslashes used here

				if(!empty($layers)){
					foreach($layers as $layer){
						?>
						<input class="eg-remove-on-reload" type="hidden" name="layers[]" value="<?php echo esc_attr($layer); ?>" />
						<?php
					}
				}
			}
			?>
		</div>
	</div>
</form>
<?php
if(intval($isCreate) == 0){ 
	//currently editing
	echo '</div>';
}

Essential_Grid_Dialogs::post_meta_dialog(); // to change post meta information
Essential_Grid_Dialogs::edit_custom_element_dialog(); // to change post meta information
Essential_Grid_Dialogs::custom_element_image_dialog(); // to change post meta information

require_once('elements/grid-addons.php');
?>
<script type="text/javascript">
	try{
		jQuery('.mce-notification-error').remove();
		jQuery('#wpbody-content >.notice').remove();
	} catch(e) {}

	window.ESG ??={};
	ESG.F ??= {};
	ESG.E ??= {};
	ESG.V ??= {};
	ESG.S ??= {};
	ESG.SC ??= {};
	ESG.C ??= {};
	ESG.LIB ??= {nav_skins: [], item_skins: {}, nav_originals: {}};
	ESG.CM ??={apiJS: null, ajaxCSS: null, navCSS: null};
	ESG.WIN ??=jQuery(window);
	ESG.DOC ??=jQuery(document);
	window.ESG.E.plugin_url = "<?php echo esc_js(ESG_PLUGIN_URL); ?>";
	ESG.LIB.COLOR_PRESETS = <?php echo wp_json_encode(ESGColorpicker::get_color_presets()); ?>;
	ESG.E.overviewMode = false;
	ESG.E.newAddonsCounter = document.getElementById('esg-new-addons-counter');
	ESG.E.newAddonsAmount = <?php echo esc_js($new_addon_counter); ?>;
	ESG.E.grid = {
		id: <?php echo ($grid !== false ? esc_js($grid['id']) : 'false'); ?>,
		addons: <?php echo (!empty($grid['params']['addons']) ? wp_json_encode($grid['params']['addons']) : '{}'); ?>,
		params : <?php echo (!empty($grid['params']) ? wp_json_encode($grid['params']) : '{}'); ?>,
		postparams : <?php echo (!empty($grid['postparams']) ? wp_json_encode($grid['postparams']) : '{}'); ?>,
	};

	// EARLY ACCESS TO SELECTED SOURE TYPE
	ESG.C.sourceType = jQuery('input[name="source-type"]');
	ESG.S.STYPE = jQuery('input[name="source-type"]:checked').val();

	var eg_jsonTaxWithCats = <?php echo (!empty($postTypesWithCats) ? wp_json_encode($postTypesWithCats) : '{}'); ?>;
	var pages = [
		<?php
		if(!empty($pages)){
			$first = true;
			foreach($pages as $page){
				echo (!$first) ? ",\n" : "\n";
				echo '{ value: '.esc_attr($page->ID).', label: "'.esc_attr(wp_strip_all_tags($page->post_title, true)).' (ID: '.esc_attr($page->ID).')" }';
				$first = false;
			}
		}
		?>
	];

	function esg_grid_create_ready_function() {
		jQuery('#eg-create-settings-menu ul').esgScrollTabs();
		
		AdminEssentials.set_basic_columns(<?php echo wp_json_encode($base->set_basic_colums([])); ?>);
		AdminEssentials.set_basic_columns_width(<?php echo wp_json_encode($base->set_basic_colums_width([])); ?>);
		AdminEssentials.set_basic_masonry_content_height(<?php echo wp_json_encode($base->set_basic_masonry_content_height([])); ?>);
		AdminEssentials.setInitMetaKeysJson(<?php echo (!empty($meta_keys) ? wp_json_encode($meta_keys) : '{}'); ?>);
		
		AdminEssentials.Addons.setAddons(<?php echo (!empty($grid_addons) ? wp_json_encode($grid_addons) : '{}'); ?>);
		AdminEssentials.Addons.init({
			afterInit: function() {
				AdminEssentials.initCreateGrid(<?php echo ($grid !== false) ? '"update_grid"' : ''; ?>);
				AdminEssentials.set_default_nav_skin(<?php echo (!empty($navigation_skins) ? wp_json_encode($navigation_skins) : '{}'); ?>);
				AdminEssentials.set_default_nav_originals(<?php echo (!empty($esg_default_skins) ? wp_json_encode($esg_default_skins) : '{}'); ?>);
				AdminEssentials.initSlider();
				AdminEssentials.initAutocomplete();
				AdminEssentials.initTabSizes();
				AdminEssentials.set_navigation_layout();
				AdminEssentials.checkDepricatedSkins();

				AdminEssentials.initSpinnerAdmin();
				AdminEssentials.setInitCustomJson(<?php echo (!empty($esg_js_elements) ? wp_json_encode($esg_js_elements) : '{}'); ?>);
				
				AdminEssentials.createPreviewGrid();

				<?php if (!intval($isCreate)) { ?>
				AdminEssentials.Tip.create('new_grid', '<?php esc_attr_e('Add-ons cannot be assigned to the grid without a valid ID. Please fill the grid title and alias fields, and save the new grid to gain access to the add-on functionality!', 'essential-grid'); ?>');
				<?php } ?>
			}
		});

		ESG.DOC.trigger('esggrid_init_create_form');
	}

	ESG.E.waitTptFunc ??= [];
	ESG.E.waitTptFunc.push(function(){
		if (document.readyState === "loading") {
			document.addEventListener("DOMContentLoaded", esg_grid_create_ready_function);
		} else {
			// `DOMContentLoaded` has already fired
			esg_grid_create_ready_function();
		}
	});
</script>

<div id="navigation-styling-css-wrapper">
<?php
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo Essential_Grid_Navigation::output_navigation_skins();
?>
</div>
<div id="esg-template-wrapper" class="esg-display-none"></div>
