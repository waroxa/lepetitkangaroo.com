// Declaring as global variable for quick prototyping
let settings_data = {
	adminUrl: sbi_settings.admin_url,
	nonce: sbi_settings.nonce,
	ajaxHandler: sbi_settings.ajax_handler,
	model: sbi_settings.model,
	feeds: sbi_settings.feeds,
	links: sbi_settings.links,
	tooltipName: null,
	sourcesList: sbi_settings.sources,
	dialogBoxPopupScreen: sbi_settings.dialogBoxPopupScreen,
	selectSourceScreen: sbi_settings.selectSourceScreen,
	clickSocialScreen: sbi_settings.clickSocialScreen,
	clickSocialBtnStatus: 'normal',
	enableClickSocialSetup: sbi_settings.clickSocialScreen.enableSetupStep,
	clickSocialActive: sbi_settings.clickSocialActive,
	disableClickSocialBtn: false,
	socialWallActivated: sbi_settings.socialWallActivated,
	socialWallLinks: sbi_settings.socialWallLinks,
	stickyWidget: false,
	exportFeed: 'none',
	locales: sbi_settings.locales,
	timezones: sbi_settings.timezones,
	genericText: sbi_settings.genericText,
	generalTab: sbi_settings.generalTab,
	feedsTab: sbi_settings.feedsTab,
	translationTab: sbi_settings.translationTab,
	advancedTab: sbi_settings.advancedTab,
	upgradeUrl: sbi_settings.upgradeUrl,
	supportPageUrl: sbi_settings.supportPageUrl,
	licenseKey: sbi_settings.licenseKey,
	pluginItemName: sbi_settings.pluginItemName,
	licenseType: 'pro',
	licenseStatus: sbi_settings.licenseStatus,
	licenseErrorMsg: sbi_settings.licenseErrorMsg,
	cronNextCheck: sbi_settings.nextCheck,
	currentView: null,
	selected: null,
	current: 0,
	sections: ["General", "Feeds", "Advanced", "Snippets"],
	indicator_width: 0,
	indicator_pos: 0,
	forwards: true,
	currentTab: null,
	import_file: null,
	gdprInfoTooltip: null,
	loaderSVG: sbi_settings.loaderSVG,
	checkmarkSVG: sbi_settings.checkmarkSVG,
	checkmarCircleSVG: sbi_settings.checkmarCircleSVG,
	uploadSVG: sbi_settings.uploadSVG,
	exportSVG: sbi_settings.exportSVG,
	reloadSVG: sbi_settings.reloadSVG,
	timesSVG: sbi_settings.timesSVG,
	tooltipHelpSvg: sbi_settings.tooltipHelpSvg,
	resetSVG: sbi_settings.resetSVG,
	sbiLicenseNoticeActive: (sbi_settings.sbiLicenseNoticeActive === '1'),
	sbiLicenseInactiveState: (sbi_settings.sbiLicenseInactiveState === '1'),
	licenseBtnClicked: false,
	tooltip: {
		text: '',
		hover: false
	},

	cogSVG: sbi_settings.cogSVG,
	deleteSVG: sbi_settings.deleteSVG,
	svgIcons: sbi_svgs,

	testConnectionStatus: null,
	recheckLicenseStatus: null,
	btnStatus: null,
	uploadStatus: null,
	clearCacheStatus: null,
	clearErrorLogStatus: null,
	optimizeCacheStatus: null,
	dpaResetStatus: null,
	pressedBtnName: null,
	loading: false,
	hasError: sbi_settings.hasError,
	dialogBox: {
		active: false,
		type: null,
		heading: null,
		description: null,
		customButtons: undefined
	},
	sourceToDelete: {},
	viewsActive: {
		sourcePopup: false,
		sourcePopupScreen: 'redirect_1',
		sourcePopupType: 'creation',
		whyRenewLicense: false,
		licenseLearnMore: false,
		instanceSourceActive: null,
		clickSocialIntegrationModal: false,
	},
	//Add New Source
	newSourceData: sbi_settings.newSourceData ? sbi_settings.newSourceData : null,
	sourceConnectionURLs: sbi_settings.sourceConnectionURLs,
	manualSourcePopupInit: sbi_settings.manualSourcePopupInit,
	returnedApiSourcesList: [],
	addNewSource: {
		typeSelected: 'page',
		manualSourceID: null,
		manualSourceToken: null
	},
	selectedFeed: 'none',
	expandedFeedID: null,
	notificationElement: {
		type: 'success', // success, error, warning, message
		text: '',
		shown: null
	},
	selectedSourcesToConnect: [],

	//Loading Bar
	fullScreenLoader: false,
	appLoaded: false,
	previewLoaded: false,
	loadingBar: true,

	//Upgrade PRO to PRO
	upgradeNewVersion: false,
	upgradeNewVersionUrl: false,
	upgradeRemoteVersion: '',
	isLicenseUpgraded: sbi_settings.isLicenseUpgraded,
	licenseUpgradedInfo: sbi_settings.licenseUpgradedInfo,
	licenseUpgradedInfoTierName: null,

	// WPCode Snippets
	wpCodeSnippets: sbi_settings.wpCode?.snippets,
	wpCodeInstalled: sbi_settings.wpCode?.pluginInstalled,
	wpCodeActive: sbi_settings.wpCode?.pluginActive,
	wpCodeProInstalled: sbi_settings.wpCode?.isProInstalled,
	wpCodePageUrl: sbi_settings.wpCode?.pageUrl,
};

// The tab component
Vue.component("tab", {
	props: ["section", "index"],
	template: `
        <a class='tab' :id='section.toLowerCase().trim()' @click='emitWidth($el);changeComponent(index);activeTab(section)'>{{section}}</a>
    `,
	created: () => {
		let urlParams = new URLSearchParams(window.location.search);
		let view = urlParams.get('view');
		if (view === null) {
			view = 'general';
		}
		settings_data.currentView = view;
		settings_data.currentTab = settings_data.sections[0];
		settings_data.selected = "app-1";
	},
	methods: {
		emitWidth: function (el) {
			settings_data.indicator_width = jQuery(el).outerWidth();
			settings_data.indicator_pos = jQuery(el).position().left;
		},
		changeComponent: function (index) {
			let prev = settings_data.current;
			if (prev < index) {
				settings_data.forwards = false;
			} else if (prev > index) {
				settings_data.forwards = true;
			}
			settings_data.selected = "app-" + (index + 1);
			settings_data.current = index;
		},
		activeTab: function (section) {
			this.setView(section.toLowerCase().trim());
			settings_data.currentTab = section;
		},
		setView: function (section) {
			history.replaceState({}, null, settings_data.adminUrl + 'admin.php?page=sbi-settings&view=' + section);
		}
	}
});

let sbiSettings = new Vue({
	el: "#sbi-settings",
	http: {
		emulateJSON: true,
		emulateHTTP: true
	},
	data: settings_data,
	created: function () {
		this.$nextTick(function () {
			let tabEl = document.querySelector('.tab');
			settings_data.indicator_width = tabEl.offsetWidth;
		});
		setTimeout(function () {
			settings_data.appLoaded = true;
		}, 350);
	},
	mounted: function () {
		let self = this;
		// set the current view page on page load
		let activeEl = document.querySelector('a.tab#' + settings_data.currentView);
		// we have to uppercase the first letter
		let currentView = settings_data.currentView.charAt(0).toUpperCase() + settings_data.currentView.slice(1);
		let viewIndex = settings_data.sections.indexOf(currentView) + 1;
		settings_data.indicator_width = activeEl.offsetWidth;
		settings_data.indicator_pos = activeEl.offsetLeft;
		settings_data.selected = "app-" + viewIndex;
		settings_data.current = viewIndex;
		settings_data.currentTab = currentView;

		setTimeout(function () {
			settings_data.appLoaded = true;
		}, 350);

		if (this.licenseUpgradedInfo) {
			this.getUpgradedProTier()
		}

	},
	computed: {
		getStyle: function () {
			return {
				position: "absolute",
				bottom: "0px",
				left: settings_data.indicator_pos + "px",
				width: settings_data.indicator_width + "px",
				height: "2px"
			};
		},
		chooseDirection: function () {
			return "slide-fade";
		}
	},
	methods: {
		activateLicense: function () {
			let self = this;

			self.hasError = false;
			self.loading = true;
			self.pressedBtnName = 'sbi';
			self.licenseBtnClicked = true;

			let data = new FormData();
			data.append('action', 'sbi_activate_license');
			data.append('license_key', self.licenseKey);
			data.append('nonce', self.nonce);
			fetch(self.ajaxHandler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					self.licenseBtnClicked = false;

					if (data.success == false) {
						self.licenseStatus = 'inactive';
						self.hasError = true;
						self.loading = false;
						return;
					}
					if (data.success == true) {
						let licenseData = data.data.licenseData;
						self.licenseStatus = data.data.licenseStatus;
						self.loading = false;
						self.pressedBtnName = null;
						// if the activatation license request sent from the expired license modal
						if (self.viewsActive.licenseLearnMore) {
							if (data.data.licenseStatus != 'valid') {
								self.processNotification("licenseError");
								return;
							}
							if (data.data.licenseStatus == 'valid') {
								self.processNotification("licenseActivated");
							}
						}

						if (
							data.data.licenseStatus == 'inactive' ||
							data.data.licenseStatus == 'invalid' ||
							data.data.licenseStatus == 'expired'
						) {
							self.hasError = true;
							if (licenseData.error) {
								self.licenseErrorMsg = licenseData.errorMsg
							}
						}
					}

				});
		},
		deactivateLicense: function () {
			this.loading = true;
			this.pressedBtnName = 'sbi';
			let data = new FormData();
			data.append('action', 'sbi_deactivate_license');
			data.append('nonce', this.nonce);
			fetch(this.ajaxHandler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					if (data.success == true) {
						this.licenseStatus = data.data.licenseStatus;
						this.loading = false;
						this.pressedBtnName = null;
					}

				});
		},

		testConnection: function () {
			this.testConnectionStatus = 'loading';
			let data = new FormData();
			data.append('action', 'sbi_test_connection');
			data.append('nonce', this.nonce);
			fetch(this.ajaxHandler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					if (data.success == false) {
						this.testConnectionStatus = 'error';
					}
					if (data.success == true) {
						this.testConnectionStatus = 'success';

						setTimeout(function () {
							this.testConnectionStatus = null;
						}.bind(this), 3000);
					}

				});
		},
		recheckLicense: function (optionName = null) {
			console.log('clicked');
			this.recheckLicenseStatus = 'loading';
			this.pressedBtnName = optionName;
			let data = new FormData();
			data.append('action', 'sbi_recheck_connection');
			data.append('license_key', this.licenseKey);
			data.append('option_name', optionName);
			data.append('nonce', this.nonce);
			console.log(this.recheckLicenseStatus);
			fetch(this.ajaxHandler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					if (data.success == true) {
						if (data.data.license == 'valid') {
							this.recheckLicenseStatus = 'success';
						}
						if (data.data.license != 'valid') {
							this.recheckLicenseStatus = 'error';
						}

						if (data.data.isLicenseUpgraded !== undefined && data.data.isLicenseUpgraded !== false) {
							this.isLicenseUpgraded = true;
							this.licenseUpgradedInfo = data.data.licenseUpgradedInfo;
							this.getUpgradedProTier()
						}

						// if the api license status has changed from old stored license status
						// then reload the page to show proper error message and notices
						// or hide error messages and notices
						if (data.data.licenseChanged == true) {
							location.reload();
						}

						setTimeout(function () {
							this.pressedBtnName = null;
							this.recheckLicenseStatus = null;
						}.bind(this), 3000);
					}

				});
		},
		recheckLicenseIcon: function () {
			if (this.recheckLicenseStatus == null) {
				return this.generalTab.licenseBox.recheckLicense;
			} else if (this.recheckLicenseStatus == 'loading') {
				return this.loaderSVG;
			} else if (this.recheckLicenseStatus == 'success') {
				return this.checkmarCircleSVG + ' ' + this.generalTab.licenseBox.licenseValid;
			} else if (this.recheckLicenseStatus == 'error') {
				return this.timesSVG + this.generalTab.licenseBox.licenseExpired;
			}
		},
		recheckBtnText: function (btnName) {
			if (this.recheckLicenseStatus == null) {
				return this.generalTab.licenseBox.recheckLicense;
			} else if (this.recheckLicenseStatus == 'loading') {
				return this.loaderSVG;
			} else if (this.recheckLicenseStatus == 'success') {
				return this.checkmarCircleSVG + ' ' + this.generalTab.licenseBox.licenseValid;
			} else if (this.recheckLicenseStatus == 'error') {
				return this.timesSVG + ' ' + this.generalTab.licenseBox.licenseExpired;
			}
		},
		testConnectionIcon: function () {
			if (this.testConnectionStatus == 'loading') {
				return this.loaderSVG;
			} else if (this.testConnectionStatus == 'success') {
				return this.checkmarCircleSVG + this.generalTab.licenseBox.connectionSuccessful;
			} else if (this.testConnectionStatus == 'error') {
				return `${this.timesSVG} ${this.generalTab.licenseBox.connectionFailed} <a href="#">${this.generalTab.licenseBox.viewError}</a>`;
			}
		},
		importFile: function () {
			document.getElementById("import_file").click();
		},
		uploadFile: function (event) {
			this.uploadStatus = 'loading';
			let file = this.$refs.file.files[0];
			let data = new FormData();
			data.append('action', 'sbi_import_settings_json');
			data.append('file', file);
			data.append('nonce', this.nonce);
			fetch(this.ajaxHandler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					this.uploadStatus = null;
					this.$refs.file.files[0] = null;
					if (data.success == false) {
						this.notificationElement = {
							type: 'error',
							text: this.genericText.failedToImportFeed,
							shown: "shown"
						};
					}
					if (data.success == true) {
						this.feeds = data.data.feeds;
						this.notificationElement = {
							type: 'success',
							text: this.genericText.feedImported,
							shown: "shown"
						};
					}
					setTimeout(function () {
						this.notificationElement.shown = "hidden";
					}.bind(this), 3000);
				});
		},
		exportFeedSettings: function () {
			// return if no feed is selected
			if (this.exportFeed === 'none') {
				return;
			}

			let url = this.ajaxHandler + '?action=sbi_export_settings_json&nonce=' + this.nonce + '&feed_id=' + this.exportFeed;
			window.location = url;
		},
		saveSettings: function () {
			this.btnStatus = 'loading';
			this.pressedBtnName = 'saveChanges';
			let data = new FormData();
			data.append('action', 'sbi_save_settings');
			data.append('model', JSON.stringify(this.model));
			data.append('sbi_license_key', this.licenseKey);
			data.append('nonce', this.nonce);
			fetch(this.ajaxHandler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					if (data.success == false) {
						this.btnStatus = 'error';
						return;
					}

					this.cronNextCheck = data.data.cronNextCheck;
					this.btnStatus = 'success';
					setTimeout(function () {
						this.btnStatus = null;
						this.pressedBtnName = null;
					}.bind(this), 3000);
				});
		},
		clearCache: function () {
			this.clearCacheStatus = 'loading';
			let data = new FormData();
			data.append('action', 'sbi_clear_cache');
			data.append('model', JSON.stringify(this.model));
			data.append('nonce', this.nonce);
			fetch(this.ajaxHandler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					if (data.success == false) {
						this.clearCacheStatus = 'error';
						return;
					}

					this.cronNextCheck = data.data.cronNextCheck;
					this.clearCacheStatus = 'success';
					setTimeout(function () {
						this.clearCacheStatus = null;
					}.bind(this), 3000);
				});
		},
		installclickSocialPlugin: function (ispluginInstalled, isPluginActive, pluginDownloadPath, clickSocialPlugin) {
			let self = this;
			self.clickSocialBtnStatus = 'loading';
			self.disableClickSocialBtn = true;
			let data = new FormData();
			data.append('action', !ispluginInstalled ? 'sbi_install_addon' : 'sbi_activate_addon');
			data.append('nonce', self.nonce);
			data.append('type', 'plugin');
			data.append('plugin', !ispluginInstalled ? pluginDownloadPath : clickSocialPlugin);
			fetch(self.ajaxHandler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					if (data.success === true) {
						self.clickSocialBtnStatus = 'success';
						self.enableClickSocialSetup = true;
						self.setupclickSocialPlugin();
					} else {
						self.clickSocialBtnStatus = 'normal';
						self.disableClickSocialBtn = false;
					}
				});
		},
		dismissClickSocialNotice: function () {
			let self = this;

			// Remove the notice instantly from the UI for better user experience
			self.clickSocialScreen.shouldHideClickSocialNotice = true;

			let data = new FormData();
			data.append('action', 'sbi_dismiss_clicksocial_notice');
			data.append('nonce', self.nonce);
			fetch(self.ajaxHandler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
		},
		clickSocialInstallBtnIcon: function () {
			if (this.clickSocialBtnStatus == 'loading') {
				return this.loaderSVG;
			} else if (this.clickSocialBtnStatus == 'success') {
				return this.checkmarCircleSVG;
			} else if (this.clickSocialBtnStatus == 'error') {
				return this.timesSVG;
			}

			if (this.clickSocialScreen.isPluginInstalled && this.clickSocialScreen.isPluginActive) {
				return this.checkmarCircleSVG;
			}

			return this.clickSocialScreen.installSVG;
		},
		clickSocialInstallBtnText: function () {
			if (this.clickSocialBtnStatus == 'loading') {
				return 'Installing';
			} else if (this.clickSocialBtnStatus == 'success') {
				return 'Installed &amp; Activated Successfully';
			}

			if (this.clickSocialScreen.isPluginInstalled && !this.clickSocialScreen.isPluginActive) {
				return 'Activate Plugin';
			}
			if (this.clickSocialScreen.isPluginInstalled && this.clickSocialScreen.isPluginActive) {
				return 'Plugin Installed & Activated';
			}

			return 'Install Plugin';
		},
		setupclickSocialPlugin: function () {
			let self = this;
			let data = new FormData();
			data.append('action', 'sbi_clicksocial_setup_source');
			data.append('nonce', self.nonce);
			fetch(self.ajaxHandler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					if (data.success === true) {
						window.location.href = self.adminUrl + self.clickSocialScreen.setupPage;
					}
				});
		},
		showTooltip: function (tooltipName) {
			this.tooltipName = tooltipName;
		},
		hideTooltip: function () {
			this.tooltipName = null;
		},
		gdprOptions: function () {
			this.gdprInfoTooltip = null;
		},
		gdprLimited: function () {
			this.gdprInfoTooltip = this.gdprInfoTooltip == null ? true : null;
		},
		clearImageResizeCache: function () {
			this.optimizeCacheStatus = 'loading';
			let data = new FormData();
			data.append('action', 'sbi_clear_image_resize_cache');
			data.append('nonce', this.nonce);
			fetch(this.ajaxHandler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					if (data.success == false) {
						this.optimizeCacheStatus = 'error';
						return;
					}
					this.optimizeCacheStatus = 'success';
					setTimeout(function () {
						this.optimizeCacheStatus = null;
					}.bind(this), 3000);
				});
		},
		resetErrorLog: function () {
			this.clearErrorLogStatus = 'loading';
			let data = new FormData();
			data.append('action', 'sbi_clear_error_log');
			data.append('nonce', this.nonce);
			fetch(this.ajaxHandler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					if (!data.success) {
						this.clearErrorLogStatus = 'error';
						return;
					}
					this.clearErrorLogStatus = 'success';
					setTimeout(function () {
						this.clearErrorLogStatus = null;
					}.bind(this), 3000);
				});
		},
		dpaReset: function () {
			this.dpaResetStatus = 'loading';
			let data = new FormData();
			data.append('action', 'sbi_dpa_reset');
			data.append('nonce', this.nonce);
			fetch(this.ajaxHandler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					if (data.success == false) {
						this.dpaResetStatus = 'error';
						return;
					}
					this.dpaResetStatus = 'success';
					setTimeout(function () {
						this.dpaResetStatus = null;
					}.bind(this), 3000);
				});
		},
		resetErrorLogIcon: function () {
			if (this.clearErrorLogStatus === null) {
				return;
			}
			if (this.clearErrorLogStatus == 'loading') {
				return this.loaderSVG;
			} else if (this.clearErrorLogStatus == 'success') {
				return this.checkmarkSVG;
			} else if (this.clearErrorLogStatus == 'error') {
				return this.timesSVG;
			}
		},
		saveChangesIcon: function () {
			if (this.btnStatus == 'loading') {
				return this.loaderSVG;
			} else if (this.btnStatus == 'success') {
				return this.checkmarkSVG;
			} else if (this.btnStatus == 'error') {
				return this.timesSVG;
			}
		},
		importBtnIcon: function () {
			if (this.uploadStatus === null) {
				return this.uploadSVG;
			}
			if (this.uploadStatus == 'loading') {
				return this.loaderSVG;
			} else if (this.uploadStatus == 'success') {
				return this.checkmarkSVG;
			} else if (this.uploadStatus == 'error') {
				return this.timesSVG;
			}
		},
		clearCacheIcon: function () {
			if (this.clearCacheStatus === null) {
				return this.reloadSVG;
			}
			if (this.clearCacheStatus == 'loading') {
				return this.loaderSVG;
			} else if (this.clearCacheStatus == 'success') {
				return this.checkmarkSVG;
			} else if (this.clearCacheStatus == 'error') {
				return this.timesSVG;
			}
		},
		clearImageResizeCacheIcon: function () {
			if (this.optimizeCacheStatus === null) {
				return this.resetSVG;
			}
			if (this.optimizeCacheStatus == 'loading') {
				return this.loaderSVG;
			} else if (this.optimizeCacheStatus == 'success') {
				return this.checkmarkSVG;
			} else if (this.optimizeCacheStatus == 'error') {
				return this.timesSVG;
			}
		},
		dpaResetStatusIcon: function () {
			if (this.dpaResetStatus === null) {
				return;
			}
			if (this.dpaResetStatus == 'loading') {
				return this.loaderSVG;
			} else if (this.dpaResetStatus == 'success') {
				return this.checkmarkSVG;
			} else if (this.dpaResetStatus == 'error') {
				return this.timesSVG;
			}
		},

		/**
		 * Toggle Sticky Widget view
		 *
		 * @since 4.0
		 */
		toggleStickyWidget: function () {
			this.stickyWidget = !this.stickyWidget;
		},

		printUsedInText: function (usedInNumber) {
			if (usedInNumber == 0) {
				return this.genericText.sourceNotUsedYet;
			}
			return this.genericText.usedIn + ' ' + usedInNumber + ' ' + (usedInNumber == 1 ? this.genericText.feed : this.genericText.feeds);
		},

		/**
		 * Delete Source Ajax
		 *
		 * @since 4.0
		 */
		deleteSource: function (sourceToDelete) {
			let self = this;
			let data = new FormData();
			data.append('action', 'sbi_feed_saver_manager_delete_source');
			data.append('source_id', sourceToDelete.id);
			data.append('username', sourceToDelete.username);
			data.append('nonce', this.nonce);
			fetch(self.ajaxHandler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					if (sourceToDelete.just_added) {
						window.location.href = window.location.href.replace('sbi_access_token', 'sbi_null');
					}
					self.sourcesList = data;
				});
		},

		/**
		 * Check if Value is Empty
		 *
		 * @since 4.0
		 *
		 * @return boolean
		 */
		checkNotEmpty: function (value) {
			return value != null && value.replace(/ /gi, '') != '';
		},

		/**
		 * Activate View
		 *
		 * @since 4.0
		 */
		activateView: function (viewName, sourcePopupType = 'creation', ajaxAction = false) {
			let self = this;
			self.viewsActive[viewName] = (self.viewsActive[viewName] == false) ? true : false;
			if (viewName == 'sourcePopup' && sourcePopupType == 'creationRedirect') {
				setTimeout(function () {
					self.$refs.addSourceRef.processIFConnect()
				}, 3500);
			}
		},

		/**
		 * Switch & Change Feed Screens
		 *
		 * @since 4.0
		 */
		switchScreen: function (screenType, screenName) {
			this.viewsActive[screenType] = screenName;
		},

		/**
		 * Parse JSON
		 *
		 * @since 4.0
		 *
		 * @return jsonObject / Boolean
		 */
		jsonParse: function (jsonString) {
			try {
				return JSON.parse(jsonString);
			} catch (e) {
				return false;
			}
		},


		/**
		 * Ajax Post Action
		 *
		 * @since 4.0
		 */
		ajaxPost: function (data, callback) {
			let self = this;
			self.$http.post(self.ajaxHandler, data).then(callback);
		},

		/**
		 * Check if Object has Nested Property
		 *
		 * @since 4.0
		 *
		 * @return boolean
		 */
		hasOwnNestedProperty: function (obj, propertyPath) {
			if (!propertyPath) {
				return false;
			}
			let properties = propertyPath.split('.');
			for (var i = 0; i < properties.length; i++) {
				let prop = properties[i];
				if (!obj || !obj.hasOwnProperty(prop)) {
					return false;
				} else {
					obj = obj[prop];
				}
			}
			return true;
		},

		/**
		 * Show Tooltip on Hover
		 *
		 * @since 4.0
		 */
		toggleElementTooltip: function (tooltipText, type, align = 'center') {
			let self = this,
				target = window.event.currentTarget,
				tooltip = (target != undefined && target != null) ? document.querySelector('.sb-control-elem-tltp-content') : null;
			if (tooltip != null && type == 'show') {
				self.tooltip.text = tooltipText;
				let position = target.getBoundingClientRect(),
					left = position.left + 10,
					top = position.top - 10;
				tooltip.style.left = left + 'px';
				tooltip.style.top = top + 'px';
				tooltip.style.textAlign = align;
				self.tooltip.hover = true;
			}
			if (type == 'hide') {
				self.tooltip.hover = false;
			}
		},

		/**
		 * Hover Tooltip
		 *
		 * @since 4.0
		 */
		hoverTooltip: function (type) {
			this.tooltip.hover = type;
		},

		/**
		 * Open Dialog Box
		 *
		 * @since 4.0
		 */
		openDialogBox: function (type, args = []) {
			let self = this,
				heading = self.dialogBoxPopupScreen[type].heading,
				description = self.dialogBoxPopupScreen[type].description,
				customButtons = self.dialogBoxPopupScreen[type].customButtons;

			switch (type) {
				case "deleteSource":
					self.sourceToDelete = args;
					heading = heading.replace("#", self.sourceToDelete.username);
					break;
			}
			self.dialogBox = {
				active: true,
				type: type,
				heading: heading,
				description: description,
				customButtons: customButtons
			};
		},


		/**
		 * Confirm Dialog Box Actions
		 *
		 * @since 4.0
		 */
		confirmDialogAction: function () {
			let self = this;
			switch (self.dialogBox.type) {
				case 'deleteSource':
					self.deleteSource(self.sourceToDelete);
					break;
			}
		},

		/**
		 * Display Feed Sources Settings
		 *
		 * @since 4.0
		 *
		 * @param {object} source
		 * @param {int} sourceIndex
		 */
		displayFeedSettings: function (source, sourceIndex) {
			this.expandedFeedID = sourceIndex + 1;
		},

		/**
		 * Hide Feed Sources Settings
		 *
		 * @since 4.0
		 *
		 * @param {object} source
		 * @param {int} sourceIndex
		 */
		hideFeedSettings: function () {
			this.expandedFeedID = null;
		},

		/**
		 * Copy text to clipboard
		 *
		 * @since 4.0
		 */
		copyToClipBoard: function (value) {
			let self = this;
			const el = document.createElement('textarea');
			el.className = 'sbi-fb-cp-clpboard';
			el.value = value;
			document.body.appendChild(el);
			el.select();
			document.execCommand('copy');
			document.body.removeChild(el);
			self.notificationElement = {
				type: 'success',
				text: this.genericText.copiedClipboard,
				shown: "shown"
			};
			setTimeout(function () {
				self.notificationElement.shown = "hidden";
			}, 3000);
		},

		escapeHTML: function (text) {
			return text.replace(/&/g, "&amp;")
				.replace(/</g, "&lt;")
				.replace(/>/g, "&gt;")
				.replace(/"/g, "&quot;")
				.replace(/'/g, "&#039;");
		},

		decodeHTMLEntities: function (text) {
			if ('string' !== typeof text) {
				return text;
			}

			const charMap = {
				'&amp;': '&',
				'&lt;': '<',
				'&gt;': '>',
				'&quot;': '"'
			}

			Object.entries(charMap).forEach(([key, value]) => {
				text = text.replaceAll(key, value)
			})

			// remove script tags
			text = text.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');

			return text;
		},
		/**
		 * View Source Instances
		 *
		 * @since 4.0
		 */
		viewSourceInstances: function (source) {
			let self = this;
			self.viewsActive.instanceSourceActive = source;
		},

		/**
		 * Return Page/Group Avatar
		 *
		 * @since 4.0
		 *
		 * @return string
		 */
		returnAccountAvatar: function (source) {
			if (typeof source.local_avatar_url !== "undefined" && source.local_avatar_url !== '') {
				return source.local_avatar_url;
			}
			if (typeof source.avatar_url !== "undefined" && source.avatar_url !== '') {
				return source.avatar_url;
			}

			return false;
		},

		/**
		 * Trigger & Open Personal Account Info Dialog
		 *
		 * @since 6.1
		 *
		 * @return string
		 */
		openPersonalAccount: function (source) {
			let self = this;
			self.$refs.personalAccountRef.personalAccountInfo.id = source.account_id;
			self.$refs.personalAccountRef.personalAccountInfo.username = source.username;
			self.$refs.personalAccountRef.personalAccountInfo.bio = source?.header_data?.biography;
			self.$refs.personalAccountRef.personalAccountPopup = true;
			self.$refs.personalAccountRef.step = 2;
		},

		/**
		 * Cancel Personal Account
		 *
		 * @since 6.1
		 */
		cancelPersonalAccountUpdate: function () {
			let self = this;
		},

		successPersonalAccountUpdate: function () {
			let self = this;
			self.notificationElement = {
				type: 'success',
				text: self.genericText.personalAccountUpdated,
				shown: "shown"
			};
			setTimeout(function () {
				self.notificationElement.shown = "hidden";
			}, 3000);

			sbiSettings.$forceUpdate();
		},

		/**
		 * Loading Bar & Notification
		 *
		 * @since 6.2.0
		 */
		processNotification: function (notificationType) {
			let self = this,
				notification = self.genericText.notification[notificationType];
			self.loadingBar = false;
			self.notificationElement = {
				type: notification.type,
				text: notification.text,
				shown: "shown"
			};
			setTimeout(function () {
				self.notificationElement.shown = "hidden";
			}, 5000);
		},

		/**
		 * Upgrade Pro/Pro License
		 *
		 * @since 6.2.0
		 */
		upgradeProProLicense: function () {
			let self = this;

			self.hasError = false;
			self.loading = true;
			self.pressedBtnName = 'sbi-upgrade';
			self.licenseBtnClicked = true;
			self.upgradeNewVersion = false;
			self.upgradeNewVersionUrl = false;

			let data = new FormData();
			data.append('action', 'sbi_maybe_upgrade_redirect');
			data.append('license_key', self.licenseKey);
			data.append('nonce', self.nonce);
			fetch(self.ajaxHandler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					console.log(data)
					self.pressedBtnName = '';
					self.loading = false;
					self.licenseBtnClicked = false;

					if (data.success === false) {
						self.licenseStatus = 'invalid';
						self.hasError = true;

						if (typeof data.data !== 'undefined') {
							this.licenseErrorMsg = data.data.message
						}
						return;
					}
					if (data.success === true) {
						if (data.data.same_version === true) {
							window.location.href = data.data.url
						} else {
							self.upgradeNewVersion = true;
							self.upgradeNewVersionUrl = data.data.url;
							self.upgradeRemoteVersion = data.data.remote_version;
						}
					}

				});

		},

		cancelUpgrade: function () {
			this.upgradeNewVersion = false;
		},

		getUpgradedProTier: function () {
			if (this.licenseUpgradedInfo == undefined || this.licenseUpgradedInfo['item_name'] === undefined) {
				return false;
			}
			let licenseType = this.licenseUpgradedInfo['item_name'].toLowerCase(),
				removeString = [
					'instagram',
					'feed',
					'pro',
					' '
				];
			removeString.forEach(str => {
				licenseType = licenseType.replace(str, '')
			});
			this.licenseUpgradedInfoTierName = licenseType;
		},

		installActivateWPCodePlugin: function () {
			let self = this,
				ispluginInstalled = self.wpCodeInstalled,
				pluginDownloadPath = 'https://downloads.wordpress.org/plugin/insert-headers-and-footers.zip',
				pluginFilePath = self.wpCodeProInstalled ? 'wpcode-premium/wpcode.php' : 'insert-headers-and-footers/ihaf.php';

			self.loading = true;
			self.pressedBtnName = 'sbi';

			let data = new FormData();
			data.append('action', !ispluginInstalled ? 'sbi_install_addon' : 'sbi_activate_addon');
			data.append('nonce', self.nonce);
			data.append('type', 'plugin');
			data.append('plugin', !ispluginInstalled ? pluginDownloadPath : pluginFilePath);
			fetch(self.ajaxHandler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					self.loading = false;
					self.pressedBtnName = null;
					if (data.success === true) {
						setTimeout(function () {
							window.location.href = self.adminUrl + 'admin.php?page=sbi-settings&view=snippets';
						}, 1000);
					} else {
						console.log(data);
					}
				});
		},

		goToWPCodeDocumentation: function () {
			window.open('https://smashballoon.com/doc/how-does-the-wpcode-integration-work/?utm_content=learn-more', '_blank', 'noopener');
		},

		editWPCodeSnippet: function (index) {
			let self = this,
				snippet = self.wpCodeSnippets[index];

			window.open(this.decodeHTMLEntities(snippet.install), '_blank');
		},

		deprecateCustomSnippets: function () {
			let self = this,
				deprecateCustomSnippets = self.model.feeds.customCSS !== '' || self.model.feeds.customJS !== '';

			return deprecateCustomSnippets;
		},

		installMigrateSnippets: function () {
			let self = this,
				ispluginInstalled = self.wpCodeInstalled,
				isPluginActive = self.wpCodeActive,
				pluginDownloadPath = 'https://downloads.wordpress.org/plugin/insert-headers-and-footers.zip',
				pluginFilePath = self.wpCodeProInstalled ? 'wpcode-premium/wpcode.php' : 'insert-headers-and-footers/ihaf.php';

			self.loading = true;
			self.pressedBtnName = 'sbi';

			// if plugin is installed and active, migrate snippets
			if (ispluginInstalled && isPluginActive) {
				self.migrateSnippets();
				return;
			}

			let data = new FormData();
			data.append('action', !ispluginInstalled ? 'sbi_install_addon' : 'sbi_activate_addon');
			data.append('nonce', self.nonce);
			data.append('type', 'plugin');
			data.append('plugin', !ispluginInstalled ? pluginDownloadPath : pluginFilePath);
			fetch(self.ajaxHandler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					if (data.success === true) {
						self.migrateSnippets();
					} else {
						console.log(data);
					}
				});
		},

		migrateSnippets: function () {
			let self = this;

			let data = new FormData();
			data.append('action', 'sbi_migrate_snippets');
			data.append('nonce', this.nonce);
			fetch(this.ajaxHandler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					self.loading = false;
					self.pressedBtnName = null;
					if (data.success === true) {
						setTimeout(function () {
							window.location.href = self.wpCodePageUrl
						}, 1000);
					} else {
						console.log(data);
					}
				});
		},

	}
});

