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

$tooltips = get_option('tp_eg_tooltips', 'true');
?>
</div>

<script type="text/javascript">
	window.ESG ??={};
	ESG.F ??= {};
	ESG.E ??= {};
	ESG.E.slug = '<?php echo esc_js(str_replace(["\n", "\r"], '', ESG_PLUGIN_SLUG)); ?>';
	ESG.E.slug_path = '<?php echo esc_js(str_replace(["\n", "\r"], '', ESG_PLUGIN_SLUG_PATH)); ?>';
	ESG.E.activated = <?php echo esc_js(Essential_Grid_Base::getValid()); ?>;
	ESG.E.revision = '<?php echo esc_js(ESG_REVISION); ?>';
	ESG.E.latest_version = '<?php echo esc_js( Essential_Grid_Base::getLatestVersion() ); ?>';
	ESG.E.tips_dont_show = <?php $tips = get_option('tp_eg_tips_dont_show', []); echo (!empty($tips) ? wp_json_encode($tips) : '[]'); ?>;
	ESG.E.plugin_url = '<?php echo esc_js(str_replace(["\n", "\r"], '', ESG_PLUGIN_URL)); ?>';
	ESG.E.ajaxurl = '<?php echo esc_js(admin_url('admin-ajax.php')); ?>';
	ESG.E.nonce = '<?php echo esc_js(wp_create_nonce("Essential_Grid_actions")); ?>';

	// Check for Resources ( jQuery / tptools / tpgsap )
	new Promise(
		(resolve) => { const t = setInterval(() => { if (ESG.F.waitTpt) { clearInterval(t); resolve(); } }, 19); }
	).then(
		() => { ESG.F.waitTpt(); }
	);
	
	var token = '<?php echo esc_js(wp_create_nonce("Essential_Grid_actions")); ?>';
	var es_do_tooltipser = <?php echo esc_js($tooltips); ?>;
	<?php
	if($tooltips == 'true'){
	?>
	var initToolTipser_once = false;
	if (document.readyState === "loading") 
		document.addEventListener('readystatechange',function(){
			if ((document.readyState === "interactive" || document.readyState === "complete") && !initToolTipser_once) {
				initToolTipser_once = true;
				AdminEssentials.initToolTipser();
			}
		});
	else {
		initToolTipser_once = true;
		AdminEssentials.initToolTipser();
	}
	
	<?php
	}
	?>
	
</script>

<div id="waitaminute">
	<div class="waitaminute-message"><i class="eg-icon-coffee"></i><br><?php esc_html_e("Please Wait...", 'essential-grid'); ?></div>
</div>

<div id="eg-error-box"></div>

<?php include(ESG_PLUGIN_ADMIN_PATH . '/views/elements/dialogs.php'); ?>
