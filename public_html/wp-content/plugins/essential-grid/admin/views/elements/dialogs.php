<!-- DECISION MODAL -->
<div class="_ESG_AM_ esg-modal-wrapper" data-modal="esg_m_decisionModal">
	<div class="esg-modal-inner">
		<div class="esg-modal-content">
			<div id="esg_m_decisionModal" class="esg_modal form_inner">
				<div class="esg_m_header">
					<i id="esg_m_icon" class="esg_m_symbol material-icons">info</i>
					<span id="esg_m_title" class="esg_m_title"><?php esc_html_e('Decision Modal Title', 'essential-grid');?></span>
				</div>
				<div class="esg_m_content">
					<div id="esg_m_maintxt" class="esg_m_main_txt"></div>
					<div id="esg_m_subtxt" class="esg_m_sub_txt"></div>
					<div class="div75"></div>
					<div id="esg_m_do_btn" class="esg_m_darkhalfbutton mr10">
						<i id="esg_m_do_icon" class="material-icons">add_circle_outline</i>
						<span id="esg_m_do_txt"><?php esc_html_e('Do It', 'essential-grid');?></span>
					</div>
					<div id="esg_m_dont_btn" class="esg_m_darkhalfbutton">
						<i id="esg_m_dont_icon" class="material-icons">add_circle_outline</i>
						<span id="esg_m_dont_txt"><?php esc_html_e('Dont Do It', 'essential-grid');?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- PREMIUM BENEFITS MODAL -->
<div id="esg-premium-benefits-dialog" class="esg-display-none">
	<div class="esg-premium-benefits-dialogtitles" id="esg-wrong-purchase-code-title">
		<span class="material-icons">error</span>
		<span class="benefits-title-right">
			<span class="esg-premium-benefits-dialogtitle"><?php esc_html_e('Ooops... Wrong Purchase Code!', 'essential-grid'); ?></span>
			<span class="esg-premium-benefits-dialogsubtitle"><?php 
				printf(
					/* translators: 1:open link tag to ESG FAQ 2:close link tag */
					esc_html__('Maybe just a typo? (Click %1$shere%2$s to find out how to locate your Essential Grid purchase code.', 'essential-grid'),
					'<a target="_blank" href="https://www.essential-grid.com/manual/installing-activating-and-registering-essential-grid/">',
					'</a>'
				);
				?></span>
		</span>
	</div>
	<div class="esg-premium-benefits-dialogtitles esg-display-none" id="esg-plugin-update-feedback-title">
		<span class="material-icons">error</span>
		<span class="benefits-title-right">
			<span class="esg-premium-benefits-dialogtitle"><?php esc_html_e('Plugin Activation Required', 'essential-grid'); ?></span>
			<span class="esg-premium-benefits-dialogsubtitle"><?php
				printf(
					/* translators: 1:open link tag to ESG pricing 2:close link tag */
					esc_html__('In order to download the %1$slatest update%2$s instantly', 'essential-grid'),
					'<a target="_blank" href="https://account.essential-grid.com/licenses/pricing/">',
					'</a>'
				);
				?></span>
		</span>
	</div>
	<div class="esg-premium-benefits-dialogtitles esg-display-none" id="esg-plugin-download-template-feedback-title">
		<span class="material-icons">error</span>
		<span class="benefits-title-right">
			<span class="esg-premium-benefits-dialogtitle"><?php esc_html_e('Plugin Activation Required', 'essential-grid'); ?></span>
			<span class="esg-premium-benefits-dialogsubtitle"><?php
				printf(
					/* translators: 1:open link tag to Grid Library 2:close link tag */
					esc_html__('In order to gain instant access to the entire %1$sGrid Library%2$s', 'essential-grid'),
					'<a target="_blank" href="https://www.essential-grid.com/grids">',
					'</a>'
				);
				?></span>
		</span>
	</div>

	<div id="basic_premium_benefits_block">
		<div class="esg-premium-benefits-block">
			<h3><?php esc_html_e('If you purchased a theme that bundled Essential Grid', 'essential-grid'); ?></h3>
			<ul>
				<li><?php esc_html_e('No activation needed to use / create grids with Essential Grid', 'essential-grid'); ?></li>
				<li><?php esc_html_e('Update manually through your theme', 'essential-grid'); ?></li>
				<li><?php
					printf(
						/* translators: 1:open link tag to FAQ database 2:close link tag 3:open link tag to video tutorials */
						esc_html__('Access our %1$sFAQ database%2$s and %3$svideo tutorials%2$s for help', 'essential-grid'),
						'<a target="_blank" href="https://www.essential-grid.com/help-center">',
						'</a>',
						'<a target="_blank" class="rspb_darklink" href="https://www.essential-grid.com/video-tutorials">'
					);
					?></li>
			</ul>
		</div>
		<div class="esg-premium-benefits-block esg-premium-benefits-block-instant-access">
			<h3><?php esc_html_e('Activate Essential Grid for', 'essential-grid'); ?> <span class="instant_access"><?php esc_html_e('instant access', 'essential-grid'); ?></span> <?php esc_html_e('to', 'essential-grid'); ?></h3>
			<div class="instant-access-wrapper instant-access-update">
				<span class="material-icons">check_circle</span>
				<?php esc_html_e('Update to the latest version directly from your dashboard', 'essential-grid'); ?>
				<a target="_blank" class="instant-access-btn" href="https://www.essential-grid.com/manual/installing-activating-and-registering-essential-grid/"><?php esc_html_e('Update', 'essential-grid'); ?></a>
			</div>
			<div class="instant-access-wrapper instant-access-support">
				<span class="material-icons">support</span>
				<?php esc_html_e('Support ticket desk', 'essential-grid'); ?>
				<a target="_blank" class="instant-access-btn" href="https://support.essential-grid.com/"><?php esc_html_e('Support', 'essential-grid'); ?></a>
			</div>
			<div class="instant-access-wrapper instant-access-library">
				<span class="material-icons">photo_library</span>
				<?php esc_html_e('Library with tons of premium grids & addons', 'essential-grid'); ?>
				<a target="_blank" class="instant-access-btn" href="https://www.essential-grid.com/grids/"><?php esc_html_e('Library', 'essential-grid'); ?></a>
			</div>
		</div>
		<a target="_blank" class="get_purchase_code" href="https://account.essential-grid.com/licenses/pricing/"><?php esc_html_e('GET A PURCHASE CODE', 'essential-grid'); ?></a>
	</div>
</div>
