<div id="sb-footer-banner" class="sbi-fb-fs sbi-bld-footer"
	 :class="(viewsActive.pageScreen == 'welcome' && feedsList != null && feedsList.length != 0) ? 'sbi-fb-full-wrapper' : 'sbi-fb-wrapper'"
	 v-if="(!viewsActive.footerDiabledScreens.includes(viewsActive.pageScreen) || (viewsActive.pageScreen == 'welcome' && feedsList != null && feedsList.length != 0)) && !iscustomizerScreen">
	<div class="sb-box-shadow">
		<div class="sbi-bld-ft-content">
			<div class="sbi-bld-ft-img"><img :src="mainFooterScreen.image" alt=""></div>
			<div class="sbi-txt-btn-wrapper">
				<div class="sbi-bld-ft-txt">
					<h3 class="sbi-bld-ft-title" v-html="mainFooterScreen.heading"></h3>
					<div class="sbi-bld-ft-info sb-small-p" v-html="mainFooterScreen.description"></div>
				</div>
				<div class="sbi-bld-ft-action">
					<a :href="mainFooterScreen.link ? mainFooterScreen.link : links.allAccessBundle" target="_blank"
					   class="sb-button sbi-btn-green">{{mainFooterScreen.learnMore}}
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path
								d="M3.3335 11.7266L10.3935 4.66659H6.00016V3.33325H12.6668V9.99992H11.3335V5.60659L4.2735 12.6666L3.3335 11.7266Z"
								fill="white"/>
						</svg>
					</a>
				</div>
			</div>
		</div>
		<div v-if="pluginType !== 'pro' && mainFooterScreen.promo !== ''" class="sbi-bld-ft-btm"
			 v-html="mainFooterScreen.promo"></div>
	</div>
</div>
<div class="sbi-stck-wdg" v-if="viewsActive.pageScreen !== 'selectFeed' && ! iscustomizerScreen"
	 :data-active="checkActiveView('footerWidget')">
	<?php $smashballoon_info = InstagramFeed\Builder\SBI_Feed_Builder::get_smashballoon_info(); ?>
	<div class="sbi-stck-pop">

		<div class="sbi-stck-el sbi-stck-el-upgrd sbi-fb-fs sb-btn-orange">
			<div class="sbi-stck-el-icon"><?php echo $icons($smashballoon_info['upgrade']['icon']) ?></div>
			<div class="sbi-stck-el-txt sb-small-p sb-bold"
				 style="color: #fff;"><?php esc_html_e('Get All Access Bundle', 'instagram-feed') ?></div>
			<div class="sbi-chevron">
				<svg width="7" height="10" viewBox="0 0 7 10" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M1.3332 0L0.158203 1.175L3.97487 5L0.158203 8.825L1.3332 10L6.3332 5L1.3332 0Z"
						  fill="white"/>
				</svg>
			</div>
			<a :href="links.popup.allAccessBundle" target="_blank" class="sbi-fs-a"></a>
		</div>

		<div
			class="sbi-stck-title sbi-fb-fs sb-small-p sb-bold sb-dark-text"><?php esc_html_e('Our Feeds for other platforms', 'instagram-feed') ?></div>

		<div class="sbi-stck-el-list sbi-fb-fs">
			<?php foreach ($smashballoon_info['platforms'] as $platform) : ?>
				<div class="sbi-stck-el sbi-fb-fs">

					<div class="sbi-stck-el-icon"
						 style="color:<?php echo $smashballoon_info['colorSchemes'][$platform['icon']] ?>;"><?php echo $icons($platform['icon']) ?></div>
					<div
						class="sbi-stck-el-txt sb-small-text sb-small-p sb-dark-text"><?php echo esc_attr($platform['name']) ?></div>
					<div class="sbi-chevron">
						<svg width="7" height="10" viewBox="0 0 7 10" fill="#8C8F9A" xmlns="http://www.w3.org/2000/svg">
							<path d="M1.3332 0L0.158203 1.175L3.97487 5L0.158203 8.825L1.3332 10L6.3332 5L1.3332 0Z"
								  fill="#8C8F9A"></path>
						</svg>
					</div>
					<a href="<?php echo esc_url($platform['link']) ?>" target="_blank" class="sbi-fs-a"></a>
				</div>
			<?php endforeach ?>
		</div>
		<div class="sbi-stck-follow sbi-fb-fs">
			<span><?php esc_html_e('Follow Us', 'instagram-feed') ?></span>
			<div class="sbi-stck-flw-links">
				<?php foreach ($smashballoon_info['socialProfiles'] as $social_key => $social) : ?>
					<a href="<?php echo esc_url($social); ?>" target="_blank" rel="noopener noreferrer"
					   style="color:<?php echo $smashballoon_info['colorSchemes'][$social_key] ?>;"><?php echo $icons($social_key) ?></a>
				<?php endforeach ?>
			</div>
		</div>
	</div>
	<div class="sbi-stck-wdg-btn" @click.prevent.default="activateView('footerWidget')">
		<?php echo $icons('smash'); ?>
		<div class="sbi-stck-wdg-btn-cls">
			<svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path
					d="M14.501 1.77279L13.091 0.362793L7.50098 5.95279L1.91098 0.362793L0.500977 1.77279L6.09098 7.36279L0.500977 12.9528L1.91098 14.3628L7.50098 8.77279L13.091 14.3628L14.501 12.9528L8.91098 7.36279L14.501 1.77279Z"
					fill="#141B38"/>
			</svg>
		</div>
	</div>
</div>
<?php
include_once SBI_BUILDER_DIR . 'templates/sections/popup/add-source-popup.php';
include_once SBI_BUILDER_DIR . 'templates/sections/popup/license-learn-more.php';
include_once SBI_BUILDER_DIR . 'templates/sections/popup/why-renew-license-popup.php';
include_once SBI_BUILDER_DIR . 'templates/sections/popup/personal-account-popup.php';
include_once SBI_BUILDER_DIR . 'templates/sections/popup/sources-list-popup.php';
include_once SBI_BUILDER_DIR . 'templates/sections/popup/extensions-popup.php';
include_once SBI_BUILDER_DIR . 'templates/sections/popup/feedtemplates-popup.php';
include_once SBI_BUILDER_DIR . 'templates/sections/popup/feedtypes-popup.php';
include_once SBI_BUILDER_DIR . 'templates/sections/popup/feedtypes-customizer-popup.php';
include_once SBI_BUILDER_DIR . 'templates/sections/popup/confirm-dialog-popup.php';
include_once SBI_BUILDER_DIR . 'templates/sections/popup/embed-popup.php';
include_once SBI_BUILDER_DIR . 'templates/sections/popup/onboarding-popup.php';
include_once SBI_BUILDER_DIR . 'templates/sections/popup/onboarding-customizer-popup.php';
include_once SBI_BUILDER_DIR . 'templates/sections/popup/install-plugin-popup.php';
?>
<div class="sb-notification-ctn" :data-active="notificationElement.shown" :data-type="notificationElement.type">
	<div class="sb-notification-icon" v-html="svgIcons[notificationElement.type+'Notification']"></div>
	<span class="sb-notification-text" v-html="notificationElement.text"></span>
</div>

<div class="sb-full-screen-loader" :data-show="fullScreenLoader ? 'shown' :  'hidden'">
	<div class="sb-full-screen-loader-logo">
		<div class="sb-full-screen-loader-spinner"></div>
		<div class="sb-full-screen-loader-img" v-html="svgIcons['smash']"></div>
	</div>
	<div class="sb-full-screen-loader-txt">
		Loading...
	</div>
</div>

<sb-personal-account-component
	:generic-text="genericText"
	:svg-icons="svgIcons"
	ref="personalAccountRef">
</sb-personal-account-component>


<sb-confirm-dialog-component
	:dialog-box.sync="dialogBox"
	:source-to-delete="sourceToDelete"
	:svg-icons="svgIcons"
	:parent-type="'builder'"
	:generic-text="genericText"></sb-confirm-dialog-component>

<sb-add-source-component
	:sources-list="sourcesList"
	:select-source-screen="selectSourceScreen"
	:views-active="viewsActive"
	:generic-text="genericText"
	:selected-feed="selectedFeed"
	:svg-icons="svgIcons"
	:links="links"
	ref="addSourceRef">
</sb-add-source-component>

<install-plugin-popup
	:views-active="viewsActive"
	:generic-text="genericText"
	:svg-icons="svgIcons"
	:plugins="plugins[viewsActive.installPluginModal]">
</install-plugin-popup>
