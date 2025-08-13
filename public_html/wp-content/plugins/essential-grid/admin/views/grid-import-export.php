<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

$item_skin = new Essential_Grid_Item_Skin();
$nav_skin = new Essential_Grid_Navigation();
$metas = new Essential_Grid_Meta();
$import = new Essential_Grid_Import();
$export = new Essential_Grid_Export();

$grids = Essential_Grid_Db::get_entity('grids')->get_grids();
$skins = $item_skin->get_essential_item_skins();
$elements = Essential_Grid_Item_Element::get_essential_item_elements();
$navigation_skins = $nav_skin->get_essential_navigation_skins();
$custom_metas = $metas->get_all_meta();
$global_css = Essential_Grid_Global_Css::get_global_css_styles();

$token = wp_create_nonce("Essential_Grid_actions");

$add_cpt = Essential_Grid_Base::getCpt();

$pg = 'false';
$allGrids    = esg_sort_grids_alphabetical( $grids );
$allSkins    = esg_sort_grids_alphabetical( $skins );
$allElements = esg_sort_grids_alphabetical( $elements );
$allNavs     = esg_sort_grids_alphabetical( $navigation_skins );
$allMetas    = esg_sort_grids_alphabetical( $custom_metas );

$import_data = false;

if (
	isset($_FILES['import_file'])
	&& isset($_POST['token'])
	&& wp_verify_nonce( sanitize_key( $_POST['token'] ), 'Essential_Grid_actions' )
) {
	if (!empty($_FILES['import_file']['error'])) {
		echo '<div class="error"><p>'.esc_attr__('Import File Error: Invalid file or file size too big.', 'essential-grid').'</p></div>';
	} else {
		$file_name = !empty($_FILES['import_file']['name']) ? sanitize_file_name($_FILES['import_file']['name']) : '';
		$file_tmp_name = !empty($_FILES['import_file']['tmp_name']) ? sanitize_text_field($_FILES['import_file']['tmp_name']) : '';
		$ext = explode(".", $file_name);
		$file_ext = strtolower(end($ext));
		if ($file_ext == "json") {
			$wp_filesystem = Essential_Grid_Base::get_filesystem();
			$import_data = $wp_filesystem->get_contents( $file_tmp_name );
			if (!$import_data) {
				echo '<div class="error"><p>' . esc_html__('Import File Error:', 'essential-grid') .  esc_html__('File is empty or cannot be read', 'essential-grid') . '</p></div>';
			} else {
				$import_data = json_decode(esg_remove_utf8_bom($import_data), true);
				if (is_null($import_data)) {
					$last_err = json_last_error();
					$last_err_msg = json_last_error_msg();
					echo '<div class="error"><p>' . esc_html__('Import File Error:', 'essential-grid') . esc_html(' ( '.$last_err .' ) '. $last_err_msg).'</p></div>';
				}
			}
		}else {
			echo '<div class="error"><p>'.esc_html__('Import File Error:', 'essential-grid') . esc_html__('Only .json extension supported.', 'essential-grid').'</p></div>';
		}
	}

	if (!empty($import_data)) {
		//check import keys for data we cant process
		$import_keys = $import->get_import_keys();
		$import_data_keys = array_keys($import_data);
		$diff = array_diff($import_data_keys, $import_keys);
		if (!empty($diff)) {
			echo '<div class="error"><p>'.esc_attr__('Import File contain data that cannot be processed!', 'essential-grid').' ( '.implode(',', array_map('esc_html', $diff)) .' ) </p></div>';

			$esg_addons = Essential_Grid_Addons::instance();
			$missing_addons = $esg_addons->check_import_keys($diff);
			foreach ($missing_addons as $handle => $keys) {
				$title = $esg_addons->get_addon_attr($handle, 'title', $handle);
				echo '<div class="error"><p>'
					. sprintf(
						/* translators: 1: addon title, 2: import keys */
						esc_html__( 'Please install "%1$s" addon to process "%2$s"', 'essential-grid' ),
						esc_html($title),
						esc_html($keys)
					)
					. '</p></div>';
			}
			
			foreach ($diff as $key) {
				unset($import_data[$key]);
			}
		}
	}
	
}

function esg_sort_grids_alphabetical_callback($a, $b)
{
	if (is_object($a) && is_object($b)) {
		$a_name = $a->name;
		$b_name = $b->name;
	} else if (is_array($a) && is_array($b)) {
		$a_name = $a['name'];
		$b_name = $b['name'];
	} else {
		return 0;
	}
	$a_name = strtolower(strval($a_name));
	$b_name = strtolower(strval($b_name));
	if ($a_name > $b_name) {
		return 1;
	} else if ($a_name < $b_name) {
		return -1;
	}
	return 0;
}

function esg_sort_grids_alphabetical($ar)
{
	if (!is_array($ar) || empty($ar)) {
		return $ar;
	}
	$arr = $ar;
	usort($arr, 'esg_sort_grids_alphabetical_callback'); // anonomous functions not backwards compatible
	return $arr;
}

function esg_remove_utf8_bom($text)
{
	$bom = pack('H*','EFBBBF');
	return preg_replace("/^$bom/", '', $text);
}

?>
	<h2 class="topheader"><?php echo esc_html(get_admin_page_title()); ?></h2>
	<div id="eg-global-settings-menu">
		<ul>
			<li class="selected-esg-setting" data-toshow="esg-import-settings"><i class="material-icons">publish</i><p><?php esc_html_e('Import', 'essential-grid'); ?></p></li><!--
			--><li data-toshow="esg-export-settings"><i class="material-icons">get_app</i><p><?php esc_html_e('Export', 'essential-grid'); ?></p></li><!--
			--><li data-toshow="esg-demo-datas"><i class="material-icons">style</i><p><?php esc_html_e('Demo Datas', 'essential-grid'); ?></p></li>
		</ul>
	</div>
	<div id="eg-grid-export-import-wrapper" class="esg-box">
		<div id="esg-demo-datas" class="esg-settings-container">
			<?php if($add_cpt == 'true' || $add_cpt === true){ ?>
			<div>
				<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Full Demo ', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc eg-cs-tbc-padding">
					<div class="esg-btn esg-green" id="esg-import-demo-posts"><?php esc_html_e('Import Full Demo Data', 'essential-grid'); ?></div>
				</div>
			</div>
			<?php } ?>
			<div>
				<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Skins ', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc eg-cs-tbc-padding">
					<a href="<?php echo esc_url(admin_url('admin.php')); ?>?page=essential-grid-item-skin" class="esg-btn esg-purple" id="esg-download-skins"><?php esc_html_e('Download Fresh Skins', 'essential-grid'); ?></a>
				</div>
			</div>
		</div>
		<form id="esg-export-settings" class="esg-settings-container" method="POST" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>?action=Essential_Grid_request_ajax">
			<input type="hidden" name="client_action" value="export_data">
			<input type="hidden" name="token" value="<?php echo esc_attr($token); ?>">
			
			<div class="esg-export-toggle-mode">
				<!-- EXPORT MODE -->
				<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Mode ', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc eg-cs-tbc-padding">

					<div class="esg-togglebutton">
						<input type="checkbox" class="checkbox" />
						<div class="knobs">
							<div class="before"><?php esc_html_e('Export Single Grid', 'essential-grid'); ?></div>
							<div class="after"><?php esc_html_e('Export All Items ', 'essential-grid'); ?></div>
							<span></span>
						</div>
						<div class="layer"></div>
					</div>

					<div class="esg-export-single-grid-wrapper">
						<input type=text name="esg_export_grid_name" id="esg_export_grid_name" value="" placeholder="<?php esc_attr_e('Type grid name or handle', 'essential-grid'); ?>" />
					</div>
					
				</div>
			</div>
			
			<?php if(!empty($grids)) { ?>
				<div class="esg-export-section esg-export-grids">
					<!-- BASIC SETTINGS -->
					<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Grids', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc eg-cs-tbc-padding">
						<ul>
							<li><div class="eg-li-intern-wrap"><input class="esg-switch-checkboxes-trigger" type="checkbox" name="export-grids" checked="checked" /><span><?php esc_html_e('All', 'essential-grid'); ?></span><span class="eg-amount-of-lis"></span></div>
								<ul class="eg-ie-sub-ul">
									<?php
									foreach($allGrids as $grid) {
										if (!Essential_Grid_Base::isValid()) {
											$params = json_decode($grid->params, true);
											$pg = Essential_Grid_Base::getVar($params, 'pg', 'false');
										}
										$resources = $export->getGridResources(
											$import, 
											[
												'grid' => $grid,
												'skins' => $skins,
												'navigation_skins' => $navigation_skins,
												'global_css' => $global_css
											]
										);
									?>
										<li>
											<div class="eg-li-intern-wrap">
												<input type="checkbox" name="export-grids-id[]" value="<?php echo esc_attr($grid->id); ?>" <?php echo ($pg != 'false' ? 'disabled' : 'checked="checked"'); ?>
													data-handle="<?php echo esc_attr($grid->handle); ?>"
													data-resources="<?php echo esc_attr(wp_json_encode($resources)); ?>"
												/>
												<span class="eg-tooltip-wrap <?php echo ($pg != 'false' ? 'disabled' : ''); ?>" 
														<?php echo ($pg != 'false' ? 'title="' . esc_attr__('Activate Essential Grid to export premium templates', 'essential-grid') . '"' : ''); ?>>
													<?php echo esc_html($grid->name . ' [ ' . $grid->handle . ' ]'); ?>
												</span>
											</div>
										</li>
									<?php } ?>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			<?php } ?>
			
			<?php if(!empty($skins)){ ?>
				<div class="esg-export-section esg-export-skins">
					<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Skins ', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc eg-cs-tbc-padding">
						<ul>
							<li><div class="eg-li-intern-wrap"><input class="esg-switch-checkboxes-trigger" type="checkbox" name="export-skins" checked="checked" /><span><?php esc_html_e('All', 'essential-grid'); ?></span><span class="eg-amount-of-lis"></span></div>
								<ul class="eg-ie-sub-ul">
									<?php foreach($allSkins as $skin){ ?>
										<li><div class="eg-li-intern-wrap"><input type="checkbox" name="export-skins-id[]" checked="checked" value="<?php echo esc_attr($skin['id']); ?>" /><?php echo esc_html($skin['name']); ?></div></li>
									<?php } ?>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			<?php } ?>
			
			<?php if(!empty($elements)){ ?>
				<div class="esg-export-section esg-export-elements">
					<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Elements', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc eg-cs-tbc-padding">
						<ul>
							<li><div class="eg-li-intern-wrap"><input class="esg-switch-checkboxes-trigger" type="checkbox" name="export-elements" checked="checked" /><span><?php esc_html_e('All', 'essential-grid'); ?></span><span class="eg-amount-of-lis"></span></div>
								<ul class="eg-ie-sub-ul">
									<?php foreach($allElements as $element){ ?>
										<li><div class="eg-li-intern-wrap"><input type="checkbox" name="export-elements-id[]" checked="checked" value="<?php echo esc_attr($element['id']); ?>" /><?php echo esc_html($element['name']); ?></div></li>
									<?php } ?>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			<?php } ?>
			
			<?php if(!empty($navigation_skins)){ ?>
				<div class="esg-export-section esg-export-navigation-skins">
					<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Navigation Skins', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc eg-cs-tbc-padding">
						<ul>
							<li><div class="eg-li-intern-wrap"><input class="esg-switch-checkboxes-trigger" type="checkbox" name="export-navigation-skins" checked="checked" /><span><?php esc_html_e('All', 'essential-grid'); ?></span><span class="eg-amount-of-lis"></span></div>
								<ul class="eg-ie-sub-ul">
								<?php foreach($allNavs as $skin){ ?>
									<li><div class="eg-li-intern-wrap"><input type="checkbox" name="export-navigation-skins-id[]" checked="checked" value="<?php echo esc_attr($skin['id']); ?>" /><?php echo esc_html($skin['name']); ?></div></li>
								<?php } ?>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			<?php } ?>
			
			<?php if(!empty($custom_metas)){ ?>
				<div class="esg-export-section esg-export-custom-meta">
					<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Custom Metas', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc eg-cs-tbc-padding">
						<ul>
							<li><div class="eg-li-intern-wrap"><input class="esg-switch-checkboxes-trigger" type="checkbox" name="export-custom-meta" checked="checked" /><span><?php esc_html_e('All', 'essential-grid'); ?></span><span class="eg-amount-of-lis"></span></div>
								<ul class="eg-ie-sub-ul">
									<?php foreach($allMetas as $meta){ $type = ($meta['m_type'] == 'link') ? 'egl-' : 'eg-'; ?>
										<li><div class="eg-li-intern-wrap"><input type="checkbox" name="export-custom-meta-handle[]" checked="checked" value="<?php echo esc_attr($meta['handle']); ?>" /><?php echo esc_html($type . $meta['handle']); ?></div></li>
									<?php } ?>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			<?php } ?>

				<div class="esg-export-section esg-export-global-css">
					<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Others', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc eg-cs-tbc-padding">
						<ul>
							<li><div class="eg-li-intern-wrap"><input type="checkbox" name="export-global-styles" value="on" checked="checked" /><span><?php esc_html_e('Global Styles', 'essential-grid'); ?></span></div></li>
						</ul>
					</div>
				</div>

				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo apply_filters('essgrid_export_form_output', '');
				?>
			
				<div>
					<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Export', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc eg-cs-tbc-padding">
						<input type="submit" id="eg-export-selected-settings" class="esg-btn esg-purple" value="<?php esc_attr_e('Export Selected', 'essential-grid'); ?>" />
					</div>
				</div>
		</form>

		<form id="esg-import-settings" class="esg-settings-container active-esc" method="post" enctype="multipart/form-data">
		<input type="hidden" name="token" value="<?php echo esc_attr($token); ?>">
		<?php
		$is_open = 'closed';
		$is_vis = 'display:none;';
		if(!empty($import_data)){
			
			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			
			$is_open = 'open';
			$is_vis = '';

			if(!empty($import_data['grids']) && is_array($import_data['grids']))
				foreach($import_data['grids'] as $d_grid)
					echo '<input type="hidden" name="data-grids[]" value="' . base64_encode(wp_json_encode($d_grid, true)) . '" />';

			if(!empty($import_data['skins']) && is_array($import_data['skins']))
				foreach($import_data['skins'] as $d_skin)
					echo '<input type="hidden" name="data-skins[]" value="' . base64_encode(wp_json_encode($d_skin, true)) . '" />';

			if(!empty($import_data['elements']) && is_array($import_data['elements']))
				foreach($import_data['elements'] as $d_elements)
					echo '<input type="hidden" name="data-elements[]" value="' . base64_encode(wp_json_encode($d_elements, true)) . '" />';

			if(!empty($import_data['navigation-skins']) && is_array($import_data['navigation-skins']))
				foreach($import_data['navigation-skins'] as $d_navigation_skins)
					echo '<input type="hidden" name="data-navigation-skins[]" value="' . base64_encode(wp_json_encode($d_navigation_skins, true)) . '" />';

			if(!empty($import_data['custom-meta']) && is_array($import_data['custom-meta']))
				foreach($import_data['custom-meta'] as $d_custom_meta)
					echo '<input type="hidden" name="data-custom-meta[]" value="' . base64_encode(wp_json_encode($d_custom_meta, true)) . '" />';

			if(isset($import_data['global-css']))
				echo '<input type="hidden" name="data-global-css" value="' . base64_encode($import_data['global-css']) . '" />';

			echo apply_filters('essgrid_import_form_hidden_inputs', '', $import_data);

			// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

		}
		
		if(!empty($import_data)){
			
			if(!empty($import_data['grids'])){ ?>
				<div> 
					<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Grids ', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc eg-cs-tbc-padding">
						<ul>
							<li>
								<div class="eg-li-intern-wrap">
									<input class="esg-switch-checkboxes-trigger" type="checkbox" name="import-grids" checked="checked" />
									<span class="eg-import-checkbox-action"><?php esc_html_e('Unselect ', 'essential-grid');?></span>
									<span><?php esc_html_e('Grids', 'essential-grid'); ?></span>
									<span class="eg-amount-of-lis"></span>
									<span class="esg-f-right">
										<span class="esg-radio-overwrite"><input type="radio" class="esg-overwrite-all" name="grid-overwrite-all" checked="checked" value="append" /> <?php esc_html_e('Append as New', 'essential-grid'); ?></span><span class="space18"></span><!--
										--><span><input type="radio" class="esg-overwrite-all" name="grid-overwrite-all" value="overwrite" /> <?php esc_html_e('Overwrite Existing', 'essential-grid'); ?></span>
									</span>
									<div class="esg-clearfix"></div>
								</div>
								<ul class="eg-ie-sub-ul">
									<?php foreach($import_data['grids'] as $grid_values){ ?>
										<li>
											<div class="eg-li-intern-wrap">
												<input class="eg-get-val" type="checkbox" name="import-grids-id[]" value="<?php echo esc_attr($grid_values['id']); ?>" checked="checked" />
												<?php echo esc_html($grid_values['name']); ?>
												<?php
												if(!empty($grids)){
													foreach($grids as $grid){
														if($grid->handle == $grid_values['handle']){
															//already exists in database, ask to append or overwrite
															?>
															<span class="esg-f-right">
																<span class="esg-radio-overwrite"><input type="radio" name="grid-overwrite-<?php echo esc_attr($grid_values['id']); ?>" checked="checked" value="append" /> <?php esc_html_e('Append as New', 'essential-grid'); ?></span><span class="space18"></span><!--
																--><span><input type="radio" name="grid-overwrite-<?php echo esc_attr($grid_values['id']); ?>" value="overwrite" /> <?php esc_html_e('Overwrite Existing', 'essential-grid'); ?></span>
															</span>
															<div class="esg-clearfix"></div>
															<?php
															break;
														}
													}
												}
												?>
											</div>
										</li>
									<?php } ?>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			<?php } ?>

			<?php if(!empty($import_data['skins'])){ ?>
				<div> 
					<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Skins ', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc eg-cs-tbc-padding">
						<ul>
							<li>
								<div class="eg-li-intern-wrap">
									<input class="esg-switch-checkboxes-trigger" type="checkbox" name="import-skins" checked="checked" />
									<span class="eg-import-checkbox-action"><?php esc_html_e('Unselect ', 'essential-grid');?></span>
									<span><?php esc_html_e('Skins', 'essential-grid'); ?></span>
									<span class="eg-amount-of-lis"></span>
									<span class="esg-f-right">
										<span class="esg-radio-overwrite"><input type="radio" class="esg-overwrite-all" name="skin-overwrite-all" checked="checked" value="append" /> <?php esc_html_e('Append as New', 'essential-grid'); ?></span><span class="space18"></span><!--
										--><span><input type="radio" class="esg-overwrite-all" name="skin-overwrite-all" value="overwrite" /> <?php esc_html_e('Overwrite Existing', 'essential-grid'); ?></span>
									</span>
									<div class="esg-clearfix"></div>
								</div>
								<ul class="eg-ie-sub-ul">
									<?php foreach($import_data['skins'] as $skin){ ?>
										<li>
											<div class="eg-li-intern-wrap">
												<input class="eg-get-val" type="checkbox" name="import-skins-id[]" checked="checked" value="<?php echo esc_attr($skin['id']); ?>" />
												<?php echo esc_html($skin['name']); ?>
												<?php
												if(!empty($skins)){
													foreach($skins as $e_skin){
														if($skin['handle'] == $e_skin['handle']){
															//already exists in database, ask to append or overwrite
															?>
															<span class="esg-f-right">
																<span class="esg-radio-overwrite"><input type="radio" name="skin-overwrite-<?php echo esc_attr($skin['id']); ?>" checked="checked" value="append" /> <?php esc_html_e('Append as New', 'essential-grid'); ?></span><span class="space18"></span><!--
																--><span><input type="radio" name="skin-overwrite-<?php echo esc_attr($skin['id']); ?>" value="overwrite" /> <?php esc_html_e('Overwrite Existing', 'essential-grid'); ?></span>
															</span>
															<div class="esg-clearfix"></div>
															<?php
															break;
														}
													}
												}
												?>
											</div>
										</li>
									<?php } ?>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			<?php } ?>

			<?php if(!empty($import_data['elements'])){ ?>
				<div> 
					<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Elements ', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc eg-cs-tbc-padding">
						<ul>
							<li>
								<div class="eg-li-intern-wrap">
									<input class="esg-switch-checkboxes-trigger" type="checkbox" name="import-elements" checked="checked" />
									<span class="eg-import-checkbox-action"><?php esc_html_e('Unselect ', 'essential-grid');?></span>
									<span><?php esc_html_e('Elements', 'essential-grid'); ?></span>
									<span class="eg-amount-of-lis"></span>
									<span class="esg-f-right">
										<span class="esg-radio-overwrite"><input type="radio" class="esg-overwrite-all" name="element-overwrite-all" checked="checked" value="append" /> <?php esc_html_e('Append as New', 'essential-grid'); ?></span><span class="space18"></span><!--
										--><span><input type="radio" class="esg-overwrite-all" name="element-overwrite-all" value="overwrite" /> <?php esc_html_e('Overwrite Existing', 'essential-grid'); ?></span>
									</span>
									<div class="esg-clearfix"></div>
								</div>
								<ul class="eg-ie-sub-ul">
									<?php foreach($import_data['elements'] as $element){ ?>
										<li>
											<div class="eg-li-intern-wrap">
												<input class="eg-get-val" type="checkbox" name="import-elements-id[]" checked="checked" value="<?php echo esc_attr($element['id']); ?>" />
												<?php echo esc_html($element['name']); ?>
												<?php
												if(!empty($elements)){
													foreach($elements as $e_element){
														if($element['handle'] == $e_element['handle']){
															//already exists in database, ask to append or overwrite
															?>
															<span class="esg-f-right">
																<span class="esg-radio-overwrite"><input type="radio" name="element-overwrite-<?php echo esc_attr($element['id']); ?>" checked="checked" value="append" /> <?php esc_html_e('Append as New', 'essential-grid'); ?></span><span class="space18"></span><!--
																--><span><input type="radio" name="element-overwrite-<?php echo esc_attr($element['id']); ?>" value="overwrite" /> <?php esc_html_e('Overwrite Existing', 'essential-grid'); ?></span>
															</span>
															<div class="esg-clearfix"></div>
															<?php
															break;
														}
													}
												}
												?>
											</div>
										</li>
									<?php } ?>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			<?php } ?>

			<?php if(!empty($import_data['navigation-skins'])){ ?>
				<div> 
					<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Navigation Skins ', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc eg-cs-tbc-padding">
						<ul>
							<li>
								<div class="eg-li-intern-wrap">
									<input class="esg-switch-checkboxes-trigger" type="checkbox" name="import-navigation-skins" checked="checked" />
									<span class="eg-import-checkbox-action"><?php esc_html_e('Unselect ', 'essential-grid');?></span>
									<span><?php esc_html_e('Navigation Skins', 'essential-grid'); ?></span>
									<span class="eg-amount-of-lis"></span>
									<span class="esg-f-right">
										<span class="esg-radio-overwrite"><input type="radio" class="esg-overwrite-all" name="nav-skin-overwrite-all" checked="checked" value="append" /> <?php esc_html_e('Append as New', 'essential-grid'); ?></span><span class="space18"></span><!--
										--><span><input type="radio" class="esg-overwrite-all" name="nav-skin-overwrite-all" value="overwrite" /> <?php esc_html_e('Overwrite Existing', 'essential-grid'); ?></span>
									</span>
									<div class="esg-clearfix"></div>
								</div>
								<ul class="eg-ie-sub-ul">
									<?php foreach($import_data['navigation-skins'] as $skin){ ?>
										<li>
											<div class="eg-li-intern-wrap">
												<input class="eg-get-val" type="checkbox" name="import-navigation-skins-id[]" checked="checked" value="<?php echo esc_attr($skin['id']); ?>" />
												<?php echo esc_html($skin['name']); ?>
												<?php
												if(!empty($navigation_skins)){
													foreach($navigation_skins as $e_nav_skins){
														if($skin['handle'] == $e_nav_skins['handle']){
															//already exists in database, ask to append or overwrite
															?>
															<span class="esg-f-right">
																<span class="esg-radio-overwrite"><input type="radio" name="nav-skin-overwrite-<?php echo esc_attr($skin['id']); ?>" checked="checked" value="append" /> <?php esc_html_e('Append as New', 'essential-grid'); ?></span><span class="space18"></span><!--
																--><span><input type="radio" name="nav-skin-overwrite-<?php echo esc_attr($skin['id']); ?>" value="overwrite" /> <?php esc_html_e('Overwrite Existing', 'essential-grid'); ?></span>
															</span>
															<div class="esg-clearfix"></div>
															<?php
															break;
														}
													}
												}
												?>
											</div>
										</li>
									<?php } ?>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			<?php } ?>

			<?php if(!empty($import_data['custom-meta'])){ ?>
				<div> 
					<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Custom Meta ', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc eg-cs-tbc-padding">
						<ul>
							<li>
								<div class="eg-li-intern-wrap">
									<input class="esg-switch-checkboxes-trigger" type="checkbox" name="import-custom-meta" checked="checked" />
									<span class="eg-import-checkbox-action"><?php esc_html_e('Unselect ', 'essential-grid');?></span>
									<span><?php esc_html_e('Custom Meta', 'essential-grid'); ?></span>
									<span class="eg-amount-of-lis"></span>
									<span class="esg-f-right">
										<span class="esg-radio-overwrite"><input type="radio" class="esg-overwrite-all" name="custom-meta-overwrite-all" checked="checked" value="append" /> <?php esc_html_e('Append as New', 'essential-grid'); ?></span><span class="space18"></span><!--
										--><span><input type="radio" class="esg-overwrite-all" name="custom-meta-overwrite-all" value="overwrite" /> <?php esc_html_e('Overwrite Existing', 'essential-grid'); ?></span>
									</span>
									<div class="esg-clearfix"></div>
								</div>
								<ul class="eg-ie-sub-ul">
									<?php foreach($import_data['custom-meta'] as $custom_meta){ ?>
										<li>
											<div class="eg-li-intern-wrap">
												<input class="eg-get-val" type="checkbox" name="import-custom-meta-handle[]" checked="checked" value="<?php echo esc_attr($custom_meta['handle']); ?>" />
												<?php echo esc_html($custom_meta['handle']); ?>
												<?php
												if(!empty($custom_metas)){
													foreach($custom_metas as $e_custom_meta){
														if($custom_meta['handle'] == $e_custom_meta['handle']){
															//already exists in database, ask to append or overwrite
															?>
															<span class="esg-f-right">
																<span class="esg-radio-overwrite"><input type="radio" name="custom-meta-overwrite-<?php echo esc_attr($custom_meta['handle']); ?>" checked="checked" value="append" /> <?php esc_html_e('Append as New', 'essential-grid'); ?></span><span class="space18"></span><!--
																--><span><input type="radio" name="custom-meta-overwrite-<?php echo esc_attr($custom_meta['handle']); ?>" value="overwrite" /> <?php esc_html_e('Overwrite Existing', 'essential-grid'); ?></span>
															</span>
															<div class="esg-clearfix"></div>
															<?php
															break;
														}
													}
												}
												?>
											</div>
										</li>
									<?php } ?>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			<?php } ?>

			<?php if(!empty($import_data['global-css'])){ ?>
				<div> 
					<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Global CSS ', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc eg-cs-tbc-padding">
						<ul>
							<li>
								<div class="eg-li-intern-wrap">
									<input class="eg-get-val" type="checkbox" name="import-global-styles" checked="checked"/><!--
									--><span><?php esc_html_e('Global Styles', 'essential-grid'); ?></span>
									<span class="esg-f-right">
										<span class="esg-radio-overwrite"><input type="radio" name="global-styles-overwrite" checked="checked" value="append" /> <?php esc_html_e('Append as New', 'essential-grid'); ?></span><span class="space18"></span><!--
										--><span><input type="radio" name="global-styles-overwrite" value="overwrite" /> <?php esc_html_e('Overwrite Existing', 'essential-grid'); ?></span>
									</span>
									<div class="esg-clearfix"></div>
								</div>
							</li>
						</ul>
					</div>
				</div>
			<?php } ?>

			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo apply_filters('essgrid_import_form_output', '', $import_data);
			?>

			<div>
				<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Additional', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc eg-cs-tbc-padding">
					<ul>
						<li>
							<div class="eg-li-intern-wrap">
								<input class="eg-get-val" type="checkbox" name="import-create-taxonomies" /><!--
									--><span><?php esc_html_e('Create Missing Taxonomies', 'essential-grid'); ?></span>
								<div><?php esc_html_e('Create missing categories and tags for post-based grid.', 'essential-grid'); ?> 
									<?php esc_html_e('(New categories and tags will have no assigned posts; you will need to assign them.)', 'essential-grid'); ?></div>
								<div class="esg-clearfix"></div>
							</div>
						</li>
					</ul>
				</div>
			</div>

			<div>
				<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Import ', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc eg-cs-tbc-padding">
					<div id="esg-import-data" class="esg-btn esg-purple"><?php esc_html_e('Import Selected Data', 'essential-grid'); ?></div>
				</div>
			</div>
		<?php
		} else { ?>
			<div>
				<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Select File', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc eg-cs-tbc-padding">
					<input type="file" name="import_file" />
					<div class="div13"></div>
					<input type="submit" class="esg-btn esg-purple" id="esg-read-file-import" value="<?php esc_attr_e('Read Selected File', 'essential-grid'); ?>" />
				</div>
			</div>
		<?php } ?>
		</form>
	</div>

<script type="text/javascript">
	window.ESG ??={};
	ESG.F ??= {};
	ESG.E ??= {};
	ESG.V ??= {};
	ESG.S ??= {};
	ESG.C ??= {};
	ESG.LIB = ESG.LIB===undefined ? { nav_skins:[], item_skins:{}, nav_originals:{}} : ESG.LIB;
	ESG.CM = ESG.CM===undefined ? {apiJS:null, ajaxCSS:null, navCSS:null} : ESG.CM;
	ESG.WIN ??=jQuery(window);
	ESG.DOC ??=jQuery(document);

	ESG.E.waitTptFunc ??= [];
	ESG.E.waitTptFunc.push(function(){
		jQuery(function(){ AdminEssentials.initImportExport(); });
	});
</script>
