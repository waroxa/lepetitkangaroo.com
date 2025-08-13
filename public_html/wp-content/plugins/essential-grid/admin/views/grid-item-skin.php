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

//force the js file to be included
global $esg_dev_mode;
if($esg_dev_mode) {
	wp_enqueue_script('esg-item-editor-script', ESG_PLUGIN_URL.'admin/assets/js/modules/dev/grid-editor.js', ['jquery'], ESG_REVISION, [ 'in_footer' => false ] );
} else {
	wp_enqueue_script('esg-item-editor-script', ESG_PLUGIN_URL.'admin/assets/js/modules/grid-editor.min.js', ['jquery'], ESG_REVISION, [ 'in_footer' => false ] );
}

?>
<h2 class="topheader"><?php esc_html_e('Skin Overview', 'essential-grid'); ?></h2>

<div id="eg-grid-even-item-skin-wrapper">

	<?php
	$skins_c = new Essential_Grid_Item_Skin();
	$navigation_c = new Essential_Grid_Navigation('1');
	$grid_c = new Essential_Grid();

	Essential_Grid_Item_Skin::propagate_default_item_skins();

	$grid['id'] = '1';
	$grid['name'] = esc_attr__('Overview', 'essential-grid');
	$grid['handle'] = 'overview';
	$grid['postparams'] = [];
	$grid['layers'] = [];
	$grid['params'] = [
		'layout' => 'masonry',
		'navigation-skin' => 'backend-flat',
		'filter-arrows' => 'single',
		'navigation-padding' => '0 0 0 0',
		'rows-unlimited' => 'off',
		'rows' => 3,
		'columns' => [3,3,3,2,2,2,1],
		'columns-width' => [1400,1170,1024,960,778,640,480],
		'spacings' => 15,
		'grid-animation' => 'fade',
		'grid-animation-speed' => 800,
		'grid-animation-delay' => 5,
		'grid-start-animation' => 'reveal',
		'grid-start-animation-speed' => '800',
		'grid-start-animation-delay' => 0,
		'grid-start-animation-type' => 'item',
		'grid-animation-type' => 'item',
		'x-ratio' => 4,
		'y-ratio' => 4,
		'wait-for-viewport' => 'off',
	];

	$skins_html = '';
	$skins_css = '';
	$filters = [];

	$skins = $skins_c->get_essential_item_skins();

	$demo_img = [];
	for($i=1; $i<=10; $i++){
		$demo_img[] = 'demoimage'.$i.'.jpg';
	}

	if(!empty($skins) && is_array($skins)){
		$src = [];

		foreach($skins as $skin){

			// 2.2.6
			if(is_array($skin) && array_key_exists('handle', $skin) && $skin['handle'] === 'esgblankskin') continue;

			if(empty($src)) $src = $demo_img;

			$item_skin = new Essential_Grid_Item_Skin();
			$item_skin->init_by_data($skin);

			//set filters
			$item_skin->set_demo_filter();

			//add skin specific css
			$item_skin->register_skin_css();

			//set demo image
			$img_key = array_rand($src);
			$item_skin->set_image($src[$img_key]);
			unset($src[$img_key]);

			$item_filter = $item_skin->get_filter_array();

			$filters = array_merge($item_filter, $filters);

			ob_start();
			$item_skin->output_item_skin('overview');
			$current_skin_html = ob_get_clean();
			
			//2.3.7 display html of item skin preview
			$skins_html .= htmlspecialchars_decode($current_skin_html);

			//2.3.7 replace placeholders with demo data
			$skins_html = str_replace(
				['%favorites%' , '%author_name%' , '%likes_short%' , '%date%' , '%retweets%' , '%likes%' , '%views_short%' , '%dislikes_short%' , '%duration%' , '%num_comments%','Likes (Facebook,YouTube,Vimeo,Instagram)','Likes Short (Facebook,YouTube,Vimeo,Instagram)' , 'Date Modified', 'Views (flickr,YouTube, Vimeo)' , 'Views Short (flickr,YouTube, Vimeo)', 'Cat. List' , 'Excerpt'],
				['314' , 'Author' , '1.2K' , '2020-06-28' , '35' , '123' , '54' , '13' , '9:32' , '12' , '231' , '1.2K' , '2020-06-28', '231' , '1.2K' , 'News, Journey, Company', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt.'],
				$skins_html
			);

			ob_start();
			$item_skin->generate_element_css('overview');
			$skins_css.= ob_get_clean();
		}
	}

	$grid_c->init_by_data($grid);
	?>
	<div class="eg-pbox esg-box eg-transbackground">
			<?php
			$grid_c->output_wrapper_pre();
			$filters = array_map("unserialize", array_unique(array_map("serialize", $filters))); //filter to unique elements

			$navigation_c->set_special_class('esg-fgc-' . $grid['id']);
			$navigation_c->set_filter($filters);
			$navigation_c->set_style('padding', $grid['params']['navigation-padding']);
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
			echo $navigation_c->output_filter(true);

			$grid_c->output_grid_pre();

			//output elements
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- var contain escaped HTML
			echo $skins_html;

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
			$grid_c->output_grid_post();
			echo '<div class="esg-text-center">';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
			echo $navigation_c->output_pagination(true);
			echo '</div>';

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
			$grid_c->output_wrapper_post();
			?>
	</div>

	<?php
	$grid_c->output_grid_javascript(false, true);
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- var contain escaped HTML
	echo $skins_css;
	Essential_Grid_Global_Css::output_global_css_styles_wrapped();
	if(empty($skins)){
		esc_html_e('No Item Skins found!', 'essential-grid');
	}
	?>
</div><!--
--><div id="create_import_grid_wrap">
	<a class="esg-btn-big esg-purple" href="<?php echo esc_url($this->getViewUrl(Essential_Grid_Admin::VIEW_ITEM_SKIN_EDITOR, 'create=true')); ?>"><i class="material-icons">style</i><?php esc_html_e('Create New Item Skin', 'essential-grid'); ?></a>
	<a class="esg-btn-big esg-red"  href="<?php echo esc_url($this->getViewUrl(Essential_Grid_Admin::VIEW_OVERVIEW, 'open_library')); ?>"><i class="material-icons">get_app</i><?php esc_html_e('Import from Grid Templates', 'essential-grid'); ?></a>
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
		jQuery('.mce-notification-error').remove();
		jQuery('#wpbody-content >.notice').remove();
		jQuery(function(){ GridEditorEssentials.initOverviewItemSkin(); });
	});
</script>
