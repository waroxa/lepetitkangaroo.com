<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

$validated = Essential_Grid_Base::getValid();
$code = Essential_Grid_Base::getCode();
$latest_version = Essential_Grid_Base::getLatestVersion();
?>

<div class="flex-grid">
	
	<div class="col">
		<div id="esg-version-information" class="esg_info_box">
			<div class="esg-blue esg_info_box_decor"><i class="eg-icon-th-large"></i></div>
			<div class="view_title"><?php esc_html_e("Version Information", 'essential-grid'); ?></div>
			<div><?php esc_html_e("Installed Version", 'essential-grid'); ?>: <span id="esg-vi-cv"><?php echo esc_html(ESG_REVISION); ?></span></div>
			<div><?php esc_html_e("Available Version", 'essential-grid'); ?>: <span id="esg-vi-lv"><?php echo esc_html($latest_version); ?></span></div>
			<div class="div10"></div>
			<a id="esg-updates-check" class="esg-btn esg-purple" href="javascript:void(0);">
				<i class="material-icons">refresh</i><?php esc_html_e('Check Version', 'essential-grid'); ?>
			</a>
			<a id="esg-updates-run" class="esg-btn esg-blue esg-display-none" href="javascript:void(0);">
				<i class="material-icons">extension</i><?php esc_html_e('UPDATE', 'essential-grid'); ?>
			</a>
		</div>
	</div>

	<div class="col">
		<div class="esg_info_box">
			<div class="esg-purple esg_info_box_decor"><i class="eg-icon-info-circled"></i></div>
			<div class="view_title"><?php esc_html_e("How To Use Essential Grid", 'essential-grid'); ?></div>
			<div><?php
				printf(
					/* translators: 1:open link tag to ESG manual 2:close link tag */
					esc_html__('%1$sRegister%2$s your Essential Grid for the full premium power!', 'essential-grid'),
					'<a href="https://www.essential-grid.com/manual/installing-activating-and-registering-essential-grid/#register" target="_blank">',
					'</a>'
				);
				?></div>
			<div><?php
				printf(
					/* translators: 1:open link tag to ESG manual 2:close link tag */
					esc_html__('Read the %1$smanual%2$s for the fundamentals of how to create a grid.', 'essential-grid'),
					'<a href="https://www.essential-grid.com/manual/grid-demo-in-under-3-minutes/" target="_blank">',
					'</a>'
				);
				?></div>
			<div><?php
				printf(
					/* translators: 1:open link tag to grid templates 2:close link tag */
					esc_html__('Check out the premium %1$sgrid templates%2$s available for registered plugins.', 'essential-grid'),
					'<a href="https://www.essential-grid.com/grids/" target="_blank">',
					'</a>'
				);
				?></div>
		</div>
	</div>
	
	<div class="col esg-w-100p esg-display-<?php echo ($validated === 'true' ? 'none' : 'block'); ?>">
		<div id="benefitscontent" class="esg_info_box">
			<div class="esg-blue esg_info_box_decor" ><i class="eg-icon-doc"></i></div>
			<div class="view_title"><?php esc_html_e("Registration Benefits", 'essential-grid'); ?>:</div>
			<div><?php
				printf(
					/* translators: 1:open link tag to grid templates 2:close link tag */
					esc_html__('%1$sPremium Grid Templates%2$s - Select from dozens of plug and play grid designs to kickstart your project', 'essential-grid'),
					'<strong><a href="https://www.essential-grid.com/grids/" target="_blank">',
					'</a></strong>'
				);
				?></div>
			<div><?php
				printf(
					/* translators: 1:open link tag to ESG pricing 2:close link tag */
					esc_html__('%1$sPremium AddOns%2$s - Get access to Addons with any of our Essential Grid license plans', 'essential-grid'),
					'<strong><a href="https://account.essential-grid.com/licenses/pricing/" target="_blank">',
					'</a></strong>'
				);
				?></div>
			<div><?php
				printf(
					/* translators: 1:open link tag to ESG Support 2:close link tag */
					esc_html__('%1$sGet Premium 1on1 Support%2$s - We help you in case of issues, installation problems and conflicts with other plugins or themes', 'essential-grid'),
					'<strong><a href="https://support.essential-grid.com/" target="_blank">',
					'</a></strong>'
				);
				?></div>
			<div><?php
				printf(
					/* translators: 1:open link tag to ESG pricing 2:close link tag */
					esc_html__('%1$sAuto Updates%2$s - Always receive the latest version of our plugin.  New features and bug fixes are available regularly', 'essential-grid'),
					'<strong><a href="https://account.essential-grid.com/licenses/pricing/" target="_blank">',
					'</a></strong>'
				);
				?></div>
		</div>
	</div>
	
	<div class="col">
		<!-- ACTIVATE THIS PRODUCT -->
		<a id="activateplugin"></a>
		<div id="esg-validation-box" class="esg_info_box">
			<?php if ($validated === 'true') { ?>
				<div class="esg-green esg_info_box_decor"><i class="eg-icon-check"></i></div>
			<?php } else { ?>
				<div class="esg-red esg_info_box_decor"><i class="eg-icon-cancel"></i></div>
			<?php } ?>
			<div id="esg-validation-wrapper">
				<div class="view_title"><?php esc_html_e('Purchase code:', 'essential-grid'); ?></div>
				<div class="validation-input">
					<input class="esg-w-250 esg-margin-r-10 blur-on-lose-focus" type="text" name="eg-validation-token" value="<?php echo esc_attr($code); ?>" <?php echo ($validated === 'true') ? ' readonly="readonly"' : ''; ?> />
					<a href="javascript:void(0);" id="eg-validation-activate" class="esg-btn esg-green esg-margin-r-10 <?php echo ($validated !== 'true') ? '' : 'esg-display-none'; ?>"><?php esc_html_e('Activate', 'essential-grid'); ?></a><a href="javascript:void(0);" id="eg-validation-deactivate" class="esg-btn esg-red <?php echo ($validated === 'true') ? '' : 'esg-display-none'; ?>"><?php esc_html_e('Deactivate', 'essential-grid'); ?></a>
					<div class="validation-description"><?php
						printf(
							/* translators: 1:open strong tag  2:close strong tag */
							esc_html__('Please enter your %1$sEssential Grid purchase code / license key.%2$s', 'essential-grid'),
							'<strong class="esg-color-black">',
							'</strong><br/>'
						);
						printf(
							/* translators: 1:open link tag to ESG manual 2:close link tag */
							esc_html__('You can find your key by following the instructions on %1$sthis page.%2$s', 'essential-grid'),
							'<a target="_blank" href="https://www.essential-grid.com/manual/installing-activating-and-registering-essential-grid/">',
							'</a><br>'
						);
						printf(
							/* translators: 1:open link tag to ESG pricing 2:close link tag */
							esc_html__('Have no regular license for this installation? %1$sGrab a fresh one%2$s!', 'essential-grid'),
							'<a target="_blank" href="https://account.essential-grid.com/licenses/pricing/">',
							'</a>'
						);
						?></div>
				</div>
				<div class="clear"></div>
			</div>

			<?php if($validated === 'true') { ?>
				<div class="validation-label"> <?php esc_html_e("How to get Support ?", 'essential-grid'); ?></div>
				<div><?php
					printf(
						/* translators: 1:open link tag to ESG help center 2:close link tag */
						esc_html__('Visit our %1$sHelp Center%2$s for the latest FAQs, Documentation and Ticket Support.', 'essential-grid'),
						'<a href="https://www.essential-grid.com/help-center" target="_blank">',
						'</a>'
					);
					?></div>
			<?php } else { ?>
				<div id="esg-before-validation"><?php
					printf(
						/* translators: 1:open link tag to ESG pricing 2:close link tag 3:open strong tag 4:close strong tag */
						esc_html__('%1$sClick here to get%2$s %3$sPremium Support, Templates, AddOns and Auto Updates%4$s', 'essential-grid'),
						'<a href="https://account.essential-grid.com/licenses/pricing/" target="_blank">',
						'</a>',
						'<strong>',
						'</strong>'
					);
					?></div>
			<?php } ?>
		</div>
	</div>
	
	<div class="col">
		<!-- NEWSLETTER PART -->
		<div id="eg-newsletter-wrapper" class="esg_info_box">
			<div class="esg-red esg_info_box_decor" ><i class="eg-icon-mail"></i></div>
			<div class="view_title"><?php esc_html_e('Newsletter', 'essential-grid'); ?></div>
			<input type="text" value="" placeholder="<?php esc_attr_e('Enter your E-Mail here', 'essential-grid'); ?>" name="eg-email" class="esg-w-250 esg-margin-r-10" />
			<span class="subscribe-newsletter-wrap"><a href="javascript:void(0);" class="esg-btn esg-purple" id="subscribe-to-newsletter"><?php esc_html_e('Subscribe', 'essential-grid'); ?></a></span>
			<span class="unsubscribe-newsletter-wrap esg-display-none">
				<a href="javascript:void(0);" class="esg-btn esg-red" id="unsubscribe-to-newsletter"><?php esc_html_e('Unsubscribe', 'essential-grid'); ?></a>
				<a href="javascript:void(0);" class="esg-btn esg-green" id="cancel-unsubscribe"><?php esc_html_e('Cancel', 'essential-grid'); ?></a>
			</span>
			<div><a href="javascript:void(0);" id="activate-unsubscribe" class="esg-info-box-unsubscribe"><?php esc_html_e('unsubscibe from newsletter', 'essential-grid'); ?></a></div>
			<div id="why-subscribe-wrapper">
				<div class="star_red"><strong class="esg-font-w-700"><?php esc_html_e('Perks of subscribing to our Newsletter', 'essential-grid'); ?></strong></div>
				<ul>
					<li><?php esc_html_e('Receive info on the latest ThemePunch product updates', 'essential-grid'); ?></li>
					<li><?php esc_html_e('Be the first to know about new products by ThemePunch and their partners', 'essential-grid'); ?></li>
					<li><?php esc_html_e('Participate in polls and customer surveys that help us increase the quality of our products and services', 'essential-grid'); ?></li>
				</ul>
			</div>
		</div>
	</div>
	
</div>

<script type="text/javascript">
if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", AdminEssentials.initInfoSection);
} else {
	// `DOMContentLoaded` has already fired
	AdminEssentials.initInfoSection();
}
</script>
