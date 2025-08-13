let sbiBuilder,
	sketch = VueColor.Sketch,
	dummyLightBoxComponent = 'sbi-dummy-lightbox-component';
	sbiStorage = window.localStorage;


Vue.component(dummyLightBoxComponent, {
	template: '#' + dummyLightBoxComponent,
	props: ['customizerFeedData', 'parent', 'dummyLightBoxScreen']
});

/**
 * VueJS Global App Builder
 *
 * @since 4.0
 */
sbiBuilder = new Vue({
	el: '#sbi-builder-app',
	http: {
		emulateJSON: true,
		emulateHTTP: true
	},
	components: {
		'sketch-picker': sketch,
	},
	mixins: [VueClickaway.mixin],
	data: {
		nonce: sbi_builder.nonce,
		template: sbi_builder.feedInitOutput,
		templateRender: false,
		updatedTimeStamp: new Date().getTime(),
		feedSettingsDomOptions: null,

		$parent: this,
		plugins: sbi_builder.installPluginsPopup,
		supportPageUrl: sbi_builder.supportPageUrl,
		pluginURL: sbi_builder.pluginURL,
		builderUrl: sbi_builder.builderUrl,
		pluginType: sbi_builder.pluginType,
		genericText: sbi_builder.genericText,
		ajaxHandler: sbi_builder.ajax_handler,
		adminPostURL: sbi_builder.adminPostURL,
		widgetsPageURL: sbi_builder.widgetsPageURL,
		themeSupportsWidgets: sbi_builder.themeSupportsWidgets,
		translatedText: sbi_builder.translatedText,
		socialShareLink: sbi_builder.socialShareLink,
		shouldDisableProFeatures: sbi_builder.shouldDisableProFeatures,
		legacyCSSEnabled: sbi_builder.legacyCSSEnabled,
		recheckLicenseStatus: null,
		welcomeScreen: sbi_builder.welcomeScreen,
		allFeedsScreen: sbi_builder.allFeedsScreen,
		extensionsPopup: sbi_builder.extensionsPopup,
		mainFooterScreen: sbi_builder.mainFooterScreen,
		embedPopupScreen: sbi_builder.embedPopupScreen,

		selectSourceScreen: sbi_builder.selectSourceScreen,
		customizeScreensText: sbi_builder.customizeScreens,
		dialogBoxPopupScreen: sbi_builder.dialogBoxPopupScreen,
		selectFeedTypeScreen: sbi_builder.selectFeedTypeScreen,
		selectFeedTemplateScreen: sbi_builder.selectFeedTemplateScreen,
		selectFeedThemeScreen: sbi_builder.selectFeedThemeScreen,
		addFeaturedPostScreen: sbi_builder.addFeaturedPostScreen,
		addFeaturedAlbumScreen: sbi_builder.addFeaturedAlbumScreen,
		addVideosPostScreen: sbi_builder.addVideosPostScreen,
		dummyLightBoxScreen: false,
		licenseTierFeatures: sbi_builder.licenseTierFeatures,

		svgIcons: sbi_svgs,
		feedsList: sbi_builder.feeds,
		feedTypes: sbi_builder.feedTypes,
		feedTemplates: sbi_builder.feedTemplates,
		feedThemes: sbi_builder.feedThemes,
		previewTheme: '',
		socialInfo: sbi_builder.socialInfo,
		sourcesList: sbi_builder.sources,
		links: sbi_builder.links,
		legacyFeedsList: sbi_builder.legacyFeeds,
		activeExtensions: sbi_builder.activeExtensions,
		advancedFeedTypes: sbi_builder.advancedFeedTypes,

		//Selected Feed type => User Hashtag Tagged
		selectedFeed: ['user'],
		// Selected Feed Template
		selectedFeedTemplate: 'ft_default',
		selectedFeedTheme: 'default_theme',
		selectedFeedPopup: [],

		selectedSources: [],
		selectedSourcesPopup: [],
		selectedSourcesTagged: [],
		selectedSourcesTaggedPopup: [],
		selectedSourcesUser: [],
		selectedSourcesUserPopup: [],
		selectedHastags: [],
		selectedHastagsPopup: [],
		hashtagInputText: '',
		hashtagOrderBy: 'recent',
		licenseKey: sbi_builder.licenseKey,
		licenseBtnClicked: false,
		viewsActive: {
			//Screens where the footer widget is disabled
			footerDiabledScreens: [
				'welcome',
				'selectFeed'
			],
			footerWidget: false,

			feedthemePopup: false,
			feedThemeElement: null,
			feedThemeDropdown: false,

			// welcome, selectFeed
			pageScreen: 'welcome',

			// feedsType, selectSource, selectTemplate, feedsTypeGetProcess
			selectedFeedSection: 'feedsType',
			manualSourcePopupInit: sbi_builder.manualSourcePopupInit,
			sourcePopup: false,
			feedtypesPopup: false,
			feedtemplatesPopup: false,
			feedtypesCustomizerPopup: false,
			sourcesListPopup: false,
			// step_1 [Add New Source] , step_2 [Connect to a user pages/groups], step_3 [Add Manually]
			sourcePopupScreen: 'redirect_1',
			whyRenewLicense: false,
			licenseLearnMore: false,
			// creation or customizer
			sourcePopupType: 'creation',
			extensionsPopupElement: false,
			feedTypeElement: null,
			instanceFeedActive: null,
			clipboardCopiedNotif: false,
			legacyFeedsShown: false,
			editName: false,
			embedPopup: false,
			embedPopupScreen: 'step_1',
			embedPopupSelectedPage: null,

			moderationMode: false,

			// onboarding
			onboardingPopup: sbi_builder.allFeedsScreen.onboarding.active,
			onboardingStep: 1,

			// customizer onboarding
			onboardingCustomizerPopup: sbi_builder.customizeScreens.onboarding.active,

			// plugin install popup
			installPluginPopup: false,
			installPluginModal: 'facebook'
		},

		//Feeds Pagination
		feedPagination: {
			feedsCount: sbi_builder.feedsCount != undefined ? sbi_builder.feedsCount : null,
			pagesNumber: 1,
			currentPage: 1,
			itemsPerPage: sbi_builder.itemsPerPage != undefined ? sbi_builder.itemsPerPage : null,
		},

		//Add New Source
		newSourceData: sbi_builder.newSourceData ? sbi_builder.newSourceData : null,
		sourceConnectionURLs: sbi_builder.sourceConnectionURLs,
		returnedApiSourcesList: [],
		addNewSource: {
			typeSelected: 'page',
			manualSourceID: null,
			manualSourceToken: null
		},
		selectedSourcesToConnect: [],

		//Feeds Types Get Info
		extraProcessFeedsTypes: [
			//'events',
			'singlealbum',
			'featuredpost',
			'videos'
		],
		isCreateProcessGood: false,
		feedCreationInfoUrl: null,
		feedTypeOnSourcePopup: 'user',

		feedsSelected: [],
		selectedBulkAction: false,
		singleAlbumFeedInfo: {
			url: '',
			info: {},
			success: false,
			isError: false
		},
		featuredPostFeedInfo: {
			url: '',
			info: {},
			success: false,
			isError: false
		},
		videosTypeInfo: {
			type: 'all',
			info: {},
			playListUrl: null,
			success: false,
			playListUrlError: false
		},

		customizerFeedDataInitial: null,
		customizerFeedData: sbi_builder.customizerFeedData,
		wordpressPageLists: sbi_builder.wordpressPageLists,
		iscustomizerScreen: (sbi_builder.customizerFeedData != undefined && sbi_builder.customizerFeedData != false),

		customizerSidebarBuilder: sbi_builder.customizerSidebarBuilder,
		sbiLicenseNoticeActive: (sbi_builder.sbiLicenseNoticeActive === '1'),
		sbiLicenseInactiveState: (sbi_builder.sbiLicenseInactiveState === '1'),
		customizerScreens: {
			activeTab: 'customize',
			printedType: {},
			printedTemplate: {},
			activeSection: null,
			previewScreen: 'desktop',
			sourceExpanded: null,
			sourcesChoosed: [],
			inputNameWidth: '0px',
			activeSectionData: null,
			parentActiveSection: null, //For nested Setions
			parentActiveSectionData: null, //For nested Setions
			activeColorPicker: null,
			printedTheme: {},
		},
		previewScreens: [
			'desktop',
			'tablet',
			'mobile'
		],

		nestedStylingSection: [],
		expandedCaptions: [],

		sourceToDelete: {},
		feedToDelete: {},
		dialogBox: {
			active: false,
			type: null, //deleteSourceCustomizer
			heading: null,
			description: null,
			customButtons: undefined
		},

		feedStyle: '',
		expandedPostText: [],
		showedSocialShareTooltip: null,
		showedCommentSection: [],

		//LightBox Object
		lightBox: {
			visibility: 'hidden',
			type: null,
			post: null,
			activeImage: null,
			albumIndex: 0,
			videoSource: null
		},
		highLightedSection: 'all',

		shoppableFeed: {
			postId: null,
			postMedia: null,
			postCaption: null,
			postShoppableUrl: ''
		},

		moderationSettings: {
			list_type_selected: null,
			allow_list: [],
			block_list: []
		},
		customBlockModerationlistTemp: '',
		tooltip: {
			text: '',
			hover: false,
			hoverType: 'outside'
		},
		//Loading Bar
		fullScreenLoader: false,
		appLoaded: false,
		previewLoaded: false,
		loadingBar: true,
		notificationElement: {
			type: 'success', // success, error, warning, message
			text: '',
			shown: null
		},

		//Moderation & Shoppable Mode
		shouldPaginateNext: true,
		moderationShoppableMode: false,
		moderationShoppableModeAjaxDone: false,
		moderationShoppableModeOffset: 0,
		moderationShoppableModeOffsetLast: 0,
		moderationShoppableShowSelected: 0,
		moderationShoppableisLoading: false,

		sw_feed: false,
		sw_feed_id: false
	},
	watch: {
		feedPreviewOutput: function () {
			return this.feedPreviewMaker()
		},
	},
	computed: {

		feedStyleOutput: function () {
			return this.customizerStyleMaker();
		},
		singleHolderData: function () {
			return this.singleHolderParams();
		},
		getModerationShoppableMode: function () {
			if (this.viewsActive.moderationMode || this.customizerScreens.activeSection == 'settings_shoppable_feed') {
				this.moderationShoppableMode = true;
			} else {
				this.moderationShoppableMode = false;
			}
			return this.moderationShoppableMode;
		},
		getModerationShoppableModeOffset: function () {
			return this.moderationShoppableModeOffset > 0;
		},
		getModerationShoppableisLoading: function () {
			return this.moderationShoppableisLoading;
		}

	},
	updated: function () {
		if (this.customizerFeedData) {
			this.setShortcodeGlobalSettings(true);
		}
	},
	created: function () {
		let self = this;
		const urlParams = new URLSearchParams(window.location.search);
		// get the socail wall link feed url params
		self.sw_feed = urlParams.get('sw-feed');
		self.sw_feed_id = urlParams.get('sw-feed-id');
		this.$parent = self;
		if (self.customizerFeedData) {
			self.template = String("<div>" + this.decodeVueHTML(self.template) + "</div>");
			self.setShortcodeGlobalSettings(true);

			self.feedSettingsDomOptions = self.jsonParse(jQuery("html").find("#sb_instagram").attr('data-options'));

			self.selectedSources = self.customizerFeedData.settings.id;
			self.selectedSourcesUser = self.customizerFeedData.settings.id;
			self.selectedSourcesTagged = self.customizerFeedData.settings.tagged;
			self.selectedHastags = self.customizerFeedData.settings.hashtag;
			self.selectedFeed = self.getCustomizerSelectedFeedsType();
			self.selectedFeedPopup = self.getCustomizerSelectedFeedsType();
			self.customizerFeedData.settings.shoppablelist = self.jsonParse(self.customizerFeedData.settings.shoppablelist) ? self.jsonParse(self.customizerFeedData.settings.shoppablelist) : {};
			self.customizerFeedData.settings.moderationlist = self.jsonParse(self.customizerFeedData.settings.moderationlist) ? self.jsonParse(self.customizerFeedData.settings.moderationlist) : self.moderationSettings;
			Object.assign(self.moderationSettings, self.customizerFeedData.settings.moderationlist);
			self.customBlockModerationlistTemp = `${self.customizerFeedData.settings.customBlockModerationlist}`;

			self.customizerFeedDataInitial = JSON.parse(JSON.stringify(self.customizerFeedData));

			self.updatedTimeStamp = new Date().getTime();
		}

		if (self.customizerFeedData == undefined) {
			self.feedPagination.pagesNumber = self.feedPagination.feedsCount != null ? Math.ceil(self.feedPagination.feedsCount / self.feedPagination.itemsPerPage) : 1
		}
		window.addEventListener('beforeunload', (event) => {
			if (self.customizerFeedData) {
				self.leaveWindowHandler(event);
			}
		});


		self.loadingBar = false;
		/* Onboarding - move elements so the position is in context */
		self.positionOnboarding();
		setTimeout(function () {
			self.positionOnboarding();
		}, 500);

		setTimeout(function () {
			self.appLoaded = true;
		}, 350);
	},
	methods: {
		hasFeature: function (feature_name) {
			let self = this;
			return self.licenseTierFeatures.includes(feature_name);
		},

		updateColorValue: function (id) {
			let self = this;
			self.customizerFeedData.settings[id] = (self.customizerFeedData.settings[id].a == 1) ? self.customizerFeedData.settings[id].hex : self.customizerFeedData.settings[id].hex8;
		},

		recheckLicense: function (optionName = null) {
			let self = this;
			let licenseNoticeWrapper = document.querySelector('.sb-license-notice');
				self.recheckLicenseStatus = 'loading';
			let recheckLicenseData = {
				action: 'sbi_recheck_connection',
				license_key: self.licenseKey,
			};
			self.ajaxPost(recheckLicenseData, function (_ref) {
				let data = _ref.data;
				if (data.success == true) {
					if (data.data.license == 'valid') {
						self.recheckLicenseStatus = 'success';
					}
					if (data.data.license != 'valid') {
						self.recheckLicenseStatus = 'error';
					}

					setTimeout(function () {
						self.recheckLicenseStatus = null;
						if (data.data.license == 'valid') {
							licenseNoticeWrapper.remove();
						}
					}.bind(this), 3000);
				}

			});
		},
		recheckBtnText: function (btnName) {
			let self = this;
			if (self.recheckLicenseStatus == null) {
				return self.genericText.recheckLicense;
			} else if (self.recheckLicenseStatus == 'loading') {
				return self.svgIcons.loaderSVG + ' ' + self.genericText.recheckLicense;
			} else if (self.recheckLicenseStatus == 'success') {
				return self.svgIcons.checkmarkSVG + ' ' + self.genericText.licenseValid;
			} else if (self.recheckLicenseStatus == 'error') {
				return self.svgIcons.times2SVG + ' ' + self.genericText.licenseExpired;
			}
		},

		/**
		 * Leave Window Handler
		 *
		 * @since 6.0
		 */
		leaveWindowHandler: function (ev) {
			let self = this,
				updateFeedData = {
					action: 'sbi_feed_saver_manager_recache_feed',
					feedID: self.customizerFeedData.feed_info.id,
				};
			self.ajaxPost(updateFeedData, function (_ref) {
				let data = _ref.data;
			});
		},

		/**
		 * Activate license key from license error post grace period header notice
		 *
		 * @since 6.2.0
		 */
		activateLicense: function () {
			let self = this;

			self.licenseBtnClicked = true;

			if (self.licenseKey == null) {
				self.licenseBtnClicked = false;
				return;
			}
			let licenseData = {
				action: 'sbi_license_activation',
				nonce: sbi_admin.nonce,
				license_key: self.licenseKey
			};
			self.ajaxPost(licenseData, function (_ref) {
				self.licenseBtnClicked = false;
				let data = _ref.data;

				if (data && data.success == false) {
					self.processNotification("licenseError");
					return;
				}
				if (data !== false) {
					self.processNotification("licenseActivated");
				}
			})
		},

		/**
		 * Show & Hide View
		 *
		 * @since 6.0
		 */
		activateView: function (viewName, sourcePopupType = 'creation', ajaxAction = false) {
			let self = this;
			if (viewName === 'extensionsPopupElement' && self.customizerFeedData !== undefined && (self.viewsActive.extensionsPopupElement == 'tagged' || self.viewsActive.extensionsPopupElement == 'hashtag' || self.viewsActive.extensionsPopupElement == 'socialwall')) {
				self.activateView('feedtypesPopup');
			}

			self.viewsActive[viewName] = (self.viewsActive[viewName] == false) ? true : false;
			if (viewName === 'sourcePopup') {
				self.viewsActive.sourcePopupType = sourcePopupType;
				if (self.customizerFeedData != undefined && sourcePopupType != 'updateCustomizer') {
					Object.assign(self.customizerScreens.sourcesChoosed, self.customizerFeedData.settings.sources);
				}
				if (self.customizerFeedData != undefined && sourcePopupType == 'updateCustomizer') {
					//self.viewsActive.sourcesListPopup = true;
					//self.viewsActive.sourcePopupType = 'customizer';
					//self.viewsActive.sourcePopup = true;

					//self.customizerFeedData.settings.sources = self.customizerScreens.sourcesChoosed;
				}

				if (ajaxAction !== false) {
					self.customizerControlAjaxAction(ajaxAction);
				}
			}
			if (viewName === 'feedtypesPopup') {
				self.viewsActive.feedTypeElement = null;
			}
			if (viewName === 'feedtemplatesPopup') {
				self.viewsActive.feedTemplatesElement = null;
			}
			if (viewName == 'editName') {
				document.getElementById("sbi-csz-hd-input").focus();
			}
			if (viewName == 'embedPopup' && ajaxAction == true) {
				self.saveFeedSettings();
			}

			if ((viewName == 'sourcePopup' || viewName == 'sourcePopupType') && sourcePopupType == 'creationRedirect') {
				self.viewsActive.sourcePopupScreen = 'redirect_1';
				setTimeout(function () {
					self.$refs.addSourceRef.processIFConnect()
				}, 3500);
			}
			if (viewName == 'moderationMode') {
				self.customizerControlAjaxAction('feedFlyPreview');
			}

			if (viewName === 'feedthemePopup') {
				if (self.shouldDisableProFeatures || !self.hasFeature('feed_themes')) {
					self.viewsActive.extensionsPopupElement = 'feedthemeTemplate';
				} else {
					self.viewsActive.feedThemeElement = null;
					self.viewsActive.feedThemeDropdown = self.viewsActive.feedThemeDropdown ? false : true;
				}
			}

			sbiBuilder.$forceUpdate();
			self.movePopUp();
		},

		/**
		 * Show/Hide View or Redirect to plugin dashboard page
		 *
		 * @since 4.0
		 */
		activateViewOrRedirect: function (viewName, pluginName, plugin) {
			let self = this;
			if (plugin.installed && plugin.activated) {
				window.location = plugin.dashboard_permalink;
				return;
			}

			self.viewsActive[viewName] = (self.viewsActive[viewName] == false) ? true : false;

			if (viewName == 'installPluginPopup') {
				self.viewsActive.installPluginModal = pluginName;
			}

			self.movePopUp();
			sbiBuilder.$forceUpdate();
		},

		movePopUp: function () {
			let overlay = document.querySelectorAll("sb-fs-boss");
			if (overlay.length > 0) {
				document.getElementById("wpbody-content").prepend(overlay[0]);
			}
		},

		/**
		 * Check if View is Active
		 *
		 * @since 4.0
		 *
		 * @return boolean
		 */
		checkActiveView: function (viewName) {
			return this.viewsActive[viewName];
		},

		/**
		 * Switch & Change Feed Screens
		 *
		 * @since 4.0
		 */
		switchScreen: function (screenType, screenName) {
			this.viewsActive[screenType] = screenName;
			sbiBuilder.$forceUpdate();
		},

		/**
		 * Check if Value is Empty
		 *
		 * @since 4.0
		 *
		 * @return boolean
		 */
		checkNotEmpty: function (value) {
			return value != null && value != undefined && value.replace(/ /gi, '') != '';
		},

		/**
		 * Check if Value exists in Array Object
		 *
		 * @since 4.0
		 *
		 * @return boolean
		 */
		checkObjectArrayElement: function (objectArray, object, byWhat) {
			let objectResult = objectArray.filter(function (elem) {
				return elem[byWhat] == object[byWhat];
			});
			return (objectResult.length > 0) ? true : false;
		},

		/**
		 * Check if Data Setting is Enabled
		 *
		 * @since 4.0
		 *
		 * @return boolean
		 */
		valueIsEnabled: function (value) {
			return value == 1 || value == true || value == 'true' || value == 'on';
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
			data['nonce'] = this.nonce;
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
			for (let i = 0; i < properties.length; i++) {
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
		 * Feed List Pagination
		 *
		 * @since 4.0
		 */
		feedListPagination: function (type) {
			let self = this,
				currentPage = self.feedPagination.currentPage,
				pagesNumber = self.feedPagination.pagesNumber;
			self.loadingBar = true;
			if ((currentPage != 1 && type == 'prev') || (currentPage < pagesNumber && type == 'next')) {
				self.feedPagination.currentPage = (type == 'next') ?
					(currentPage < pagesNumber ? (parseInt(currentPage) + 1) : pagesNumber) :
					(currentPage > 1 ? (parseInt(currentPage) - 1) : 1);

				let postData = {
					action: 'sbi_feed_saver_manager_get_feed_list_page',
					page: self.feedPagination.currentPage
				};
				self.ajaxPost(postData, function (_ref) {
					let data = _ref.data;
					if (data) {
						self.feedsList = data;
					}
					self.loadingBar = false;
				});
				sbiBuilder.$forceUpdate();
			}
		},

		/**
		 * Choose Feed Type
		 *
		 * @since 6.0
		 */
		chooseFeedType: function (feedTypeEl, iscustomizerPopup = false) {
			let self = this;
			let isFeedAvailable = self.hasFeature(feedTypeEl.type + '_feeds');
			if (feedTypeEl.type != 'socialwall') {
				if ((self.shouldDisableProFeatures || !isFeedAvailable) && feedTypeEl.type !== 'user') {
					self.viewsActive.extensionsPopupElement = feedTypeEl.type;
					return;
				}
				if (self.selectedFeed.includes(feedTypeEl.type)) {
					if (self.selectedFeed.length != 1) {
						self.selectedFeed.splice(self.selectedFeed.indexOf(feedTypeEl.type), 1);
					}
				} else {
					self.selectedFeed.push(feedTypeEl.type);
				}
			} else if (feedTypeEl.type == 'socialwall') {
				self.viewsActive.extensionsPopupElement = 'socialwall';
			}
			sbiBuilder.$forceUpdate();
		},

		chooseFeedTemplate: function (feedTemplate, iscustomizerPopup = false) {
			let self = this;
			self.selectedFeedTemplate = feedTemplate.type;
			if (iscustomizerPopup) {
				if (self.shouldDisableProFeatures || !self.hasFeature('feed_templates')) {
					self.activateView('feedtemplatesPopup');
					self.viewsActive.extensionsPopupElement = 'feedTemplate';
					return;
				}
				self.viewsActive.feedTemplateElement = feedTemplate.type;
			}
			sbiBuilder.$forceUpdate();
		},

		chooseFeedTheme: function (feedTheme, iscustomizerPopup = false) {
			let self = this;
			self.selectedFeedTheme = feedTheme.type;
			if (iscustomizerPopup) {
				self.viewsActive.feedThemeElement = feedTheme.type;
			}
			if ((feedTheme.type == 'showcase_carousel' || feedTheme.type == 'simple_carousel') && !self.activeExtensions.carousel) {
				self.viewsActive.extensionsPopupElement = 'carousel';
				self.selectedFeedTemplate = 'default';
				self.viewsActive.feedTemplateElement = null;
				self.viewsActive.feedtemplatesPopup = null;
				self.viewsActive.feedthemePopup = null;
			}
			sbiBuilder.$forceUpdate();
		},

		/**
		 * Get Feed Template Element Title
		 *
		 * @since 4.2.0
		 */
		getFeedTemplateElTitle: function ($el, isCustomizer = false) {
			return $el.title;
		},

		/**
		 * Choose Feed Type
		 *
		 * @since 6.0
		 */
		selectFeedTypePopup: function (feedTypeEl) {
			let self = this;
			let type = feedTypeEl.type,
				isFeedAvailable = self.hasFeature(type + '_feeds');

			if (type != 'socialwall') {
				if ((self.shouldDisableProFeatures || !isFeedAvailable) && type !== 'user') {
					self.viewsActive.extensionsPopupElement = type;
					if (self.customizerFeedData !== undefined) {
						self.viewsActive['feedtypesPopup'] = false;
					}
					return;
				}

				if (!self.selectedFeedPopup.includes(type) && !self.selectedFeed.includes(type)) {
					self.selectedFeedPopup.push(type);
				} else if (self.selectedFeedPopup.includes(type) && self.selectedFeedPopup.length != 1) {
					self.selectedFeedPopup.splice(self.selectedFeedPopup.indexOf(type), 1);
				}
			}

			if (type == 'socialwall') {
				self.viewsActive.extensionsPopupElement = 'socialwall';
				if (self.customizerFeedData !== undefined) {
					self.viewsActive['feedtypesPopup'] = false;
				}
			}
		},

		/**
		 * Check Selected Feed Type
		 *
		 * @since 6.0
		 */
		checkFeedTypeSelect: function (feedTypeEl) {
			let self = this;
			if (self.customizerFeedData) {
				return self.selectedFeedPopup.includes(feedTypeEl.type) && feedTypeEl.type != 'socialwall'
			}
			return self.selectedFeed.includes(feedTypeEl.type) && feedTypeEl.type != 'socialwall'
		},

		/**
		 * Confirm Add Feed Type Poup
		 *
		 * @since 6.0
		 */
		addFeedTypePopup: function () {
			let self = this;
			let selectedFeed = self.selectedFeedPopup.concat(self.selectedFeed);
			selectedFeed = selectedFeed.filter(function (item, pos) {
				return selectedFeed.indexOf(item) == pos;
			});
			self.selectedFeed = selectedFeed;
			self.selectedFeedPopup = self.selectedFeed;

			self.activateView('feedtypesPopup');
			if (self.customizerFeedData) {
				self.activateView('feedtypesCustomizerPopup');
			}
		},

		/**
		 * Returns The Selected Feeds Type
		 * For Customizer PopUp
		 *
		 * @since 6.0
		 */
		getCustomizerSelectedFeedsType: function () {
			let self = this,
				customizerSettings = self.customizerFeedData.settings;

			switch (customizerSettings.type) {
				case 'user':
					return ['user'];
				case 'hashtag':
					return ['hashtag'];
				case 'tagged':
					return ['tagged'];
				case 'mixed': {
					let feedTypes = [];
					if (customizerSettings.id.length > 0) {
						feedTypes.push('user');
					}
					if (customizerSettings.hashtag.length > 0) {
						feedTypes.push('hashtag');
					}
					if (customizerSettings.tagged.length > 0) {
						feedTypes.push('tagged');
					}
					return feedTypes;
				}
			}

		},

		/**
		 * Choose Feed Type
		 *
		 * @since 6.0
		 */
		checkMultipleFeedType: function () {
			return this.selectedFeed.length > 1;
		},

		/**
		 * Check if Feed Type Source is Active
		 *
		 * @since 6.0
		 */
		checkMultipleFeedTypeActive: function (feedTypeID) {
			return this.selectedFeed.length >= 1 && this.selectedFeed.includes(feedTypeID);
		},

		/**
		 * Customizer
		 * Check if Feed Type Source is Active
		 *
		 * @since 6.0
		 */
		checkMultipleFeedTypeActiveCustomizer: function (feedTypeID) {
			return this.customizerFeedData.settings.type == feedTypeID || (this.customizerFeedData.settings.type == 'mixed' && this.checkFeedTypeHasSources(feedTypeID));
		},

		/**
		 * Customizer
		 * Check if Feed Type Has Sources
		 *
		 * @since 6.0
		 */
		checkFeedTypeHasSources: function (feedTypeID) {
			let self = this;
			switch (feedTypeID) {
				case 'user':
					return self.createSourcesArray(self.customizerFeedData.settings.id).length > 0;
				case 'hashtag':
					return self.createSourcesArray(self.customizerFeedData.settings.hashtag).length > 0;
				case 'tagged':
					return self.createSourcesArray(self.customizerFeedData.settings.tagged).length > 0;
			}
			return false;
		},

		/**
		 * Customizer
		 * Toggle the Feed Types in Popup
		 *
		 * @since 6.0
		 */
		openFeedTypesPopupCustomizer: function () {
			let self = this;
			self.selectedSourcesUserPopup = self.createSourcesArray(self.selectedSourcesUser);
			self.selectedSourcesTaggedPopup = self.createSourcesArray(self.selectedSourcesTagged);
			self.selectedHastagsPopup = self.createSourcesArray(self.selectedHastags);
			self.activateView('feedtypesCustomizerPopup')
		},

		/**
		 * Customizer
		 * Toggle the Feed Types in Popup
		 *
		 * @since 6.0
		 */
		toggleFeedTypesChooserPopup: function () {
			let self = this;
			self.activateView('feedtypesCustomizerPopup');
			self.activateView('feedtypesPopup');
		},

		/**
		 * Customizer
		 * Toggle the Feed Types With Sources Popup
		 *
		 * @since 6.0
		 */
		toggleFeedTypesSourcesPopup: function () {
			let self = this;

			self.activateView('sourcesListPopup');
			if (self.customizerFeedData) {
				self.activateView('feedtypesCustomizerPopup');
			}
		},

		/**
		 * Customizer
		 * Update Feed Type
		 * & Sources/Hashtags
		 * @since 6.0
		 */
		updateFeedTypeAndSourcesCustomizer: function () {
			let self = this;
			self.selectedSourcesUser = self.createSourcesArray(self.selectedSourcesUserPopup);
			self.selectedSourcesTagged = self.createSourcesArray(self.selectedSourcesTaggedPopup);

			self.selectedHastags = self.createSourcesArray(self.getFeedHashtagsSaverPopup());

			self.customizerFeedData.settings.type = self.getFeedTypeSaver();
			self.customizerFeedData.settings.id = self.getFeedIdSourcesSaver();
			self.customizerFeedData.settings.tagged = self.getFeedIdSourcesTaggedSaver();
			self.customizerFeedData.settings.hashtag = self.getFeedHashtagsSaver();
			self.customizerControlAjaxAction('feedFlyPreview');
			self.activateView('feedtypesCustomizerPopup');

		},

		/**
		 * Customizer
		 * Cancel Feed Types
		 * & Sources/Hashtags
		 * @since 6.0
		 */
		cancelFeedTypeAndSourcesCustomizer: function () {
			let self = this;
			if (
				JSON.stringify(self.createSourcesArray(self.selectedSourcesUser)) === JSON.stringify(self.createSourcesArray(self.selectedSourcesUserPopup)) &&
				JSON.stringify(self.createSourcesArray(self.selectedSourcesTagged)) === JSON.stringify(self.createSourcesArray(self.selectedSourcesTaggedPopup)) &&
				JSON.stringify(self.createSourcesArray(self.selectedHastags)) === JSON.stringify(self.createSourcesArray(self.getFeedHashtagsSaverPopup())) &&
				JSON.stringify(self.selectedFeedPopup) === JSON.stringify(self.selectedFeed)
			) {
				self.viewsActive['feedtypesPopup'] = false;
				self.viewsActive['feedtypesCustomizerPopup'] = false;
			} else {
				self.openDialogBox('unsavedFeedSources');
			}

		},


		/**
		 * Customizer
		 * Update Feed Type
		 * & Sources/Hashtags
		 * @since 6.0
		 */
		getFeedHashtagsSaverPopup: function () {
			let self = this;
			if (self.checkNotEmpty(self.hashtagInputText)) {
				self.hashtagWriteDetectPopup(true);
			}
			return self.selectedHastagsPopup;
		},


		/**
		 * If max number of source types are added (3)
		 *
		 * @since 6.0
		 */
		maxTypesAdded: function () {
			return this.selectedFeed.length >= 3;
		},

		/**
		 * Check if Feed Type Source is Active
		 *
		 * @since 6.0
		 */
		removeFeedTypeSource: function (feedTypeID) {
			let self = this;
			self.selectedFeed.splice(self.selectedFeed.indexOf(feedTypeID), 1);
			self.selectedFeedPopup.splice(self.selectedFeedPopup.indexOf(feedTypeID), 1);
			if (feedTypeID == 'user') {
				self.selectedSourcesUser = [];
			} else if (feedTypeID == 'tagged') {
				self.selectedSourcesTagged = [];
			} else if (feedTypeID == 'hashtag') {
				self.selectedHastags = [];
			}
		},

		/**
		 * Choose Feed Type
		 *
		 * @since 6.0
		 */
		checkSingleFeedType: function (feedType) {
			return this.selectedFeed.length == 1 && this.selectedFeed[0] == feedType;
		},


		//Check Feed Creation Process Sources & Hashtags
		creationProcessCheckSourcesHashtags: function () {
			let self = this;
			if (self.selectedFeed.length > 1) {
				let number = 0;
				if (self.selectedFeed.includes('user') && self.selectedSourcesUser.length >= 1) {
					number += 1;
				}
				if (self.selectedFeed.includes('tagged') && self.selectedSourcesTagged.length >= 1) {
					number += 1;
				}
				if (self.selectedFeed.includes('hashtag') && self.selectedHastags.length >= 1) {
					number += 1;
				}
				return (number > 0);
			} else {
				if (self.selectedFeed.length == 1 && self.selectedFeed[0] == 'hashtag') {
					return (self.selectedHastags.length >= 1 || self.checkNotEmpty(self.hashtagInputText))
				}
			}
			return self.selectedSources.length > 0 ? true : false;
		},

		/*
			Feed Creation Process
		*/
		creationProcessCheckAction: function () {
			let self = this, checkBtnNext = false;
			switch (self.viewsActive.selectedFeedSection) {
				case 'feedsType':
					checkBtnNext = self.selectedFeed != null ? true : false;
					window.sbiSelectedFeed = self.selectedFeed;
					break;
				case 'selectSource':
					checkBtnNext = self.creationProcessCheckSourcesHashtags();
					break;
				case 'selectTheme':
					checkBtnNext = self.selectedSources.length > 0 || self.creationProcessCheckSourcesHashtags() ? true : false;
					break;
				case 'selectTemplate':
					checkBtnNext = self.selectedSources.length > 0 || self.creationProcessCheckSourcesHashtags() ? true : false;
					break;
				case 'feedsTypeGetProcess':

					break;
			}
			return checkBtnNext;
		},
		//Next Click in the Creation Process
		creationProcessNext: function () {
			let self = this;

			switch (self.viewsActive.selectedFeedSection) {
				case 'feedsType':
					if (self.selectedFeed !== null) {
						if (self.selectedFeed === 'socialwall') {
							window.location.href = sbi_builder.pluginsInfo.social_wall.settingsPage;
							return;
						}
						self.switchScreen('selectedFeedSection', 'selectSource');
					}
					break;
				case 'selectSource':
					if (self.selectedSources.length > 0 || self.creationProcessCheckSourcesHashtags()) {
						if (self.checkPeronalAccount()) {
							if (self.shouldDisableProFeatures || !self.hasFeature('feed_templates')) {
								self.isCreateProcessGood = true;
							} else {
								if (self.hasFeature('feed_themes')) {
									self.switchScreen('selectedFeedSection', 'selectTheme');
								} else {
									self.switchScreen('selectedFeedSection', 'selectTemplate');
								}
							}
						} else {
							self.$refs.personalAccountRef.personalAccountPopup = true;
						}
					}
					break;
				case 'selectTheme':
					if (self.selectedSources.length > 0 || self.creationProcessCheckSourcesHashtags()) {
						self.switchScreen('selectedFeedSection', 'selectTemplate');
					} else {
						self.processNotification("selectSourceError");
					}
					break;
				case 'selectTemplate':
					self.isCreateProcessGood = self.creationProcessCheckSourcesHashtags();
					if (self.selectedSources.length > 0) {
						self.switchScreen('selectedFeedSection', 'feedsTypeGetProcess');
						if (!self.extraProcessFeedsTypes.includes(self.selectedFeed))
							self.isCreateProcessGood = true;
					}
					self.hashtagWriteDetect(true);
					break;
				case 'feedsTypeGetProcess':
					break;
			}
			if (self.isCreateProcessGood) {
				self.submitNewFeed();
			}
		},
		changeVideoSource: function (videoSource) {
			this.videosTypeInfo.type = videoSource;
			sbiBuilder.$forceUpdate();
		},

		//Next Click in the Onboarding Process
		onboardingNext: function () {
			this.viewsActive.onboardingStep++;
			this.onboardingHideShow();
			sbiBuilder.$forceUpdate();
		},
		//Previous Click in the Onboarding Process
		onboardingPrev: function () {
			this.viewsActive.onboardingStep--;
			this.onboardingHideShow();
			sbiBuilder.$forceUpdate();
		},
		onboardingHideShow: function () {
			let tooltips = document.querySelectorAll(".sb-onboarding-tooltip");
			for (let i = 0; i < tooltips.length; i++) {
				tooltips[i].style.display = "none";
			}
			document.querySelectorAll(".sb-onboarding-tooltip-" + this.viewsActive.onboardingStep)[0].style.display = "block";

			if (this.viewsActive.onboardingCustomizerPopup) {
				if (this.viewsActive.onboardingStep === 2) {
					this.switchCustomizerTab('customize');
				} else if (this.viewsActive.onboardingStep === 3) {
					this.switchCustomizerTab('settings');
				}
			}

		},
		//Close Click in the Onboarding Process
		onboardingClose: function () {
			let self = this,
				wasActive = self.viewsActive.onboardingPopup ? 'newuser' : 'customizer';

			document.getElementById("sbi-builder-app").classList.remove('sb-onboarding-active');

			self.viewsActive.onboardingPopup = false;
			self.viewsActive.onboardingCustomizerPopup = false;

			self.viewsActive.onboardingStep = 0;
			let postData = {
				action: 'sbi_dismiss_onboarding',
				was_active: wasActive
			};
			self.ajaxPost(postData, function (_ref) {
				let data = _ref.data;
			});
			sbiBuilder.$forceUpdate();
		},
		positionOnboarding: function () {
			let self = this,
				onboardingElem = document.querySelectorAll(".sb-onboarding-overlay")[0],
				wrapElem = document.getElementById("sbi-builder-app");

			if (onboardingElem === null || typeof onboardingElem === 'undefined') {
				return;
			}

			if (self.viewsActive.onboardingCustomizerPopup && self.iscustomizerScreen) {
				if (document.getElementById("sb-onboarding-tooltip-customizer-1") !== null) {
					wrapElem.classList.add('sb-onboarding-active');

					let step1El = document.querySelectorAll(".sbi-csz-header")[0];
					if (step1El !== undefined) {
						step1El.appendChild(document.getElementById("sb-onboarding-tooltip-customizer-1"));
					}

					let step2El = document.querySelectorAll(".sb-customizer-sidebar-sec1")[0];
					if (step2El !== undefined) {
						step2El.appendChild(document.getElementById("sb-onboarding-tooltip-customizer-2"));
					}

					let step3El = document.querySelectorAll(".sb-customizer-sidebar-sec1")[0];
					if (step3El !== undefined) {
						step3El.appendChild(document.getElementById("sb-onboarding-tooltip-customizer-3"));
					}

					self.onboardingHideShow();
				}
			} else if (self.viewsActive.onboardingPopup && !self.iscustomizerScreen) {
				if (sbi_builder.allFeedsScreen.onboarding.type === 'single') {
					if (document.getElementById("sb-onboarding-tooltip-single-1") !== null) {
						wrapElem.classList.add('sb-onboarding-active');

						let step1El = document.querySelectorAll(".sbi-fb-wlcm-header .sb-positioning-wrap")[0];
						if (step1El !== undefined) {
							step1El.appendChild(document.getElementById("sb-onboarding-tooltip-single-1"));
						}

						let step2El = document.querySelectorAll(".sbi-table-wrap")[0];
						if (step2El !== undefined) {
							step2El.appendChild(document.getElementById("sb-onboarding-tooltip-single-2"));
						}
						self.onboardingHideShow();
					}
				} else {
					if (document.getElementById("sb-onboarding-tooltip-multiple-1") !== null) {
						wrapElem.classList.add('sb-onboarding-active');

						let step1El = document.querySelectorAll(".sbi-fb-wlcm-header .sb-positioning-wrap")[0];
						if (step1El !== undefined) {
							step1El.appendChild(document.getElementById("sb-onboarding-tooltip-multiple-1"));
						}

						let step2El = document.querySelectorAll(".sbi-fb-lgc-ctn")[0];
						if (step2El !== undefined) {
							step2El.appendChild(document.getElementById("sb-onboarding-tooltip-multiple-2"));
						}

						let step3El = document.querySelectorAll(".sbi-legacy-table-wrap")[0];
						if (step3El !== undefined) {
							step3El.appendChild(document.getElementById("sb-onboarding-tooltip-multiple-3"));
						}

						self.activateView('legacyFeedsShown');
						self.onboardingHideShow();
					}
				}

			}
		},
		//Back Click in the Creation Process
		creationProcessBack: function () {
			let self = this;
			switch (self.viewsActive.selectedFeedSection) {
				case 'feedsType':
					self.switchScreen('pageScreen', 'welcome');
					break;
				case 'selectSource':
					self.switchScreen('selectedFeedSection', 'feedsType');
					break;
				case 'selectTheme':
					self.switchScreen('selectedFeedSection', 'selectSource');
					break;
				case 'selectTemplate':
					self.switchScreen('selectedFeedSection', 'selectTheme');
					break;
				case 'feedsTypeGetProcess':
					self.switchScreen('selectedFeedSection', 'selectSource');
					break;
			}
			sbiBuilder.$forceUpdate();
		},
		getSelectedSourceName: function (sourceID) {
			let self = this;
			let sourceInfo = self.sourcesList.filter(function (source) {
				return source.account_id == sourceID;
			});
			return (sourceInfo.length > 0) ? sourceInfo[0].username : '';
		},

		getSourceIdSelected: function () {
			let self = this;
			if (self.selectedFeed.length == 1 && self.selectedFeed[0] != 'hashtag') {
				return self.selectedSources[0];
			} else if (self.selectedSourcesUser.length >= 1 && self.selectedFeed.length > 1 && self.selectedFeed.includes('user')) {
				return self.selectedSourcesUser[0];
			} else if (self.selectedSourcesTagged.length >= 1 && self.selectedFeed.length > 1 && self.selectedFeed.includes('tagged')) {
				return self.selectedSourcesTagged[0];
			}
			return 'Instagram Feed';

		},

		//Check Business Account Sources

		checkBusinessAccount: function () {
			let self = this;
			let sourceInfo = self.sourcesList.filter(function (source) {
				return source.account_type == 'business' || source.type == 'business';
			});
			return sourceInfo.length > 0;
		},

		//Return Feed Type
		getFeedTypeSaver: function () {
			let self = this;
			if (self.selectedFeed.length > 1) {
				return 'mixed';
			}
			return self.selectedFeed[0];
		},

		//Return Sources ID,
		getFeedIdSourcesSaver: function () {
			let self = this;
			if ((self.selectedFeed.length > 1 && self.selectedFeed.includes('user')) || self.customizerFeedData) {
				return self.selectedSourcesUser;
			}
			return (self.selectedFeed.length == 1 && self.selectedFeed.includes('user')) ? self.selectedSources : "";
		},

		//Return Sources ID
		getFeedIdSourcesTaggedSaver: function () {
			let self = this;
			if ((self.selectedFeed.length > 1 && self.selectedFeed.includes('tagged')) || self.customizerFeedData) {
				return self.selectedSourcesTagged;
			}
			return (self.selectedFeed.length == 1 && self.selectedFeed.includes('tagged')) ? self.selectedSources : "";
		},

		//Return Hashtag Saver
		getFeedHashtagsSaver: function () {
			let self = this;
			if (self.selectedFeed.length == 1 && self.selectedFeed[0] == 'hashtag' && self.checkNotEmpty(self.hashtagInputText)) {
				self.hashtagWriteDetect(true);
			}
			if ((self.selectedFeed.length > 1 && self.selectedFeed.includes('hashtag')) || (self.selectedFeed.length == 1 && self.selectedFeed[0] == 'hashtag')) {
				return self.selectedHastags;
			}
			return [];
		},

		//Create & Submit New Feed
		submitNewFeed: function () {
			let self = this,
				newFeedData = {
					action: 'sbi_feed_saver_manager_builder_update',
					sources: self.getFeedIdSourcesSaver(),
					tagged: self.getFeedIdSourcesTaggedSaver(),
					hashtag: self.getFeedHashtagsSaver(),
					order: self.hashtagOrderBy,
					new_insert: 'true',
					sourcename: self.getSelectedSourceName(self.getSourceIdSelected()),
					//feedtype : self.selectedFeed,
					feedtemplate: self.selectedFeedTemplate,
					feedtheme: self.selectedFeedTheme,
					type: self.getFeedTypeSaver()
				};

			self.fullScreenLoader = true;
			self.ajaxPost(newFeedData, function (_ref) {
				let data = _ref.data;
				if (data.feed_id && data.success) {
					window.location = self.builderUrl + '&feed_id=' + data.feed_id + self.sw_feed_params();
				}
			});
		},

		sw_feed_params: function () {
			let sw_feed_param = '';
			if (this.sw_feed) {
				sw_feed_param += '&sw-feed=true';
			}
			if (this.sw_feed_id) {
				sw_feed_param += '&sw-feed-id=' + this.sw_feed_id;
			}
			return sw_feed_param;
		},

		swfeedReturnUrl: function () {
			let self = this;
			if (self.sw_feed) {
				sw_return_url = 'admin.php?page=sbsw#/create-feed'
			}
			if (self.sw_feed_id) {
				sw_return_url = 'admin.php?page=sbsw&feed_id=' + self.sw_feed_id
			}
			return sw_return_url;
		},

		//Select Sources
		selectSource: function (source) {
			let self = this;
			if ((source.account_type != 'personal' && self.selectedFeed[0] == 'tagged') || self.selectedFeed[0] == 'user') {
				if (self.selectedSources.includes(source.account_id)) {
					self.selectedSources.splice(self.selectedSources.indexOf(source.account_id), 1);
				} else {
					self.selectedSources.push(source.account_id);
				}
			}
		},

		//Source Ative
		isSourceSelectActive: function (source) {
			let self = this;
			if (self.selectedSources.includes(source.account_id)) {
				return (source.account_type != 'personal' && self.selectedFeed[0] == 'tagged') || self.selectedFeed[0] == 'user';
			}
			return false;
		},

		//Check if source is Disabled
		checkSourceDisabled: function (source) {
			let self = this;
			return (source.account_type == 'personal' && self.selectedFeed[0] == 'tagged');
		},


		//Open Add Source List Popup
		openSourceListPopup: function (feedTypeID) {
			let self = this;
			self.feedTypeOnSourcePopup = feedTypeID;
			if (self.feedTypeOnSourcePopup == 'tagged') {
				self.selectedSourcesPopup = self.createSourcesArray(self.selectedSourcesTagged);
			} else if (self.feedTypeOnSourcePopup == 'user') {
				self.selectedSourcesPopup = self.createSourcesArray(self.selectedSourcesUser);
			}
			self.activateView('sourcesListPopup');
			if (self.customizerFeedData) {
				self.activateView('feedtypesCustomizerPopup');
			}
		},

		//Check if source is Disabled POPUP
		checkSourceDisabledPopup: function (source) {
			let self = this;
			return (source.account_type == 'personal' && self.feedTypeOnSourcePopup == 'tagged');
		},

		//Source Active POPUP
		isSourceSelectActivePopup: function (source) {
			let self = this;
			if (self.selectedSourcesPopup.includes(source.account_id)) {
				return (source.account_type != 'personal' && self.feedTypeOnSourcePopup == 'tagged') || self.feedTypeOnSourcePopup == 'user';
			}
			return false;
		},

		//Select Sources POPUP
		selectSourcePopup: function (source) {
			let self = this;
			if ((source.account_type != 'personal' && self.feedTypeOnSourcePopup == 'tagged') || self.feedTypeOnSourcePopup == 'user') {
				if (self.selectedSourcesPopup.includes(source.account_id)) {
					self.selectedSourcesPopup.splice(self.selectedSourcesPopup.indexOf(source.account_id), 1);
				} else {
					self.selectedSourcesPopup.push(source.account_id);
				}
			}
		},

		//Return Choosed Feed Type
		returnSelectedSourcesByType: function (feedType) {
			let self = this,
				sourcesListByType = [];
			if (feedType == 'user') {
				sourcesListByType = self.sourcesList.filter(function (source) {
					return (self.customizerFeedData) ? self.selectedSourcesUserPopup.includes(source.account_id) : self.selectedSourcesUser.includes(source.account_id);
				});
			} else if (feedType == 'tagged') {
				sourcesListByType = self.sourcesList.filter(function (source) {
					return (self.customizerFeedData) ? self.selectedSourcesTaggedPopup.includes(source.account_id) : self.selectedSourcesTagged.includes(source.account_id);
				});
			}
			return sourcesListByType;
		},

		//Remove Source From Feed Type
		removeSourceFromFeedType: function (source, feedType) {
			let self = this;
			if (feedType == 'user') {
				if (self.customizerFeedData) {
					self.selectedSourcesUserPopup.splice(self.selectedSourcesUserPopup.indexOf(source.account_id), 1)
				} else {
					self.selectedSourcesUser.splice(self.selectedSourcesUser.indexOf(source.account_id), 1)
				}
			} else if (feedType == 'tagged') {
				if (self.customizerFeedData) {
					self.selectedSourcesTaggedPopup.splice(self.selectedSourcesTaggedPopup.indexOf(source.account_id), 1)
				} else {
					self.selectedSourcesTagged.splice(self.selectedSourcesTagged.indexOf(source.account_id), 1)
				}
			}
		},

		/*
			Return Selected Sources / Hashtags
			on The Customizer Control
		*/
		returnSelectedSourcesByTypeCustomizer: function (feedType) {
			let self = this,
				sourcesListNameByType = [];
			if (feedType == 'user') {
				sourcesListNameByType = self.sourcesList.filter(function (source) {
					return self.customizerFeedData.settings.id.includes(source.account_id);

				});
			}
			if (feedType == 'tagged') {
				sourcesListNameByType = self.sourcesList.filter(function (source) {
					return self.customizerFeedData.settings.tagged.includes(source.account_id);
				});
			}
			if (feedType == 'hashtag') {
				sourcesListNameByType = Array.isArray(self.customizerFeedData.settings.hashtag) ? self.customizerFeedData.settings.hashtag : self.customizerFeedData.settings.hashtag.split(',');
			}
			return sourcesListNameByType;
		},

		//Check if source are Array
		createSourcesArray: function (element) {
			if (Array.isArray(element) && element.length == 1 && !this.checkNotEmpty(element[0])) {
				return [];
			}
			return Array.isArray(element) ? Array.from(element) : Array.from(element.split(','));
		},

		// Add Source to Feed Type
		addSourceToFeedType: function () {
			let self = this;
			if (self.feedTypeOnSourcePopup == 'tagged') {
				if (!self.customizerFeedData) {
					self.selectedSourcesTagged = self.createSourcesArray(self.selectedSourcesPopup);
					self.selectedSourcesTaggedPopup = self.createSourcesArray(self.selectedSourcesTagged);
				} else {
					self.selectedSourcesTaggedPopup = self.createSourcesArray(self.selectedSourcesPopup);
				}
			} else if (self.feedTypeOnSourcePopup == 'user') {
				if (!self.customizerFeedData) {
					self.selectedSourcesUser = self.createSourcesArray(self.selectedSourcesPopup);
					self.selectedSourcesUserPopup = self.createSourcesArray(self.selectedSourcesUser);
				} else {
					self.selectedSourcesUserPopup = self.createSourcesArray(self.selectedSourcesPopup);
				}
			}
			self.activateView('sourcesListPopup');
			if (self.customizerFeedData) {
				self.activateView('feedtypesCustomizerPopup');
			}
		},

		//Detect Hashtag Writing
		hashtagWriteDetectPopup: function (isProcess = false) {
			let self = this,
				target = window.event;
			if (target.keyCode == 188 || isProcess == true) {
				self.hashtagInputText = self.hashtagInputText.replace(',', '');
				if (self.checkNotEmpty(self.hashtagInputText)) {
					if (self.hashtagInputText[0] !== '#') {
						self.hashtagInputText = '#' + self.hashtagInputText;
					}
					self.selectedHastagsPopup = self.createSourcesArray(self.selectedHastagsPopup);
					self.selectedHastagsPopup.push(self.hashtagInputText);
				}
				self.hashtagInputText = '';
			}
		},

		//Detect Hashtag Writing
		hashtagWriteDetect: function (isProcess = false) {
			let self = this,
				target = window.event;
			if (target.keyCode == 188 || isProcess == true) {
				self.hashtagInputText = self.hashtagInputText.replace(',', '');
				if (self.checkNotEmpty(self.hashtagInputText)) {
					if (self.hashtagInputText[0] !== '#') {
						self.hashtagInputText = '#' + self.hashtagInputText;
					}
					self.selectedHastags = self.createSourcesArray(self.selectedHastags);
					self.selectedHastags.push(self.hashtagInputText);
					self.selectedHastagsPopup = self.createSourcesArray(self.selectedHastags);
				}
				self.hashtagInputText = '';
			}
		},

		//Remove Hashtag from List
		removeHashtag: function (hashtag) {
			let self = this;
			if (self.customizerFeedData) {
				self.selectedHastagsPopup.splice(self.selectedHastagsPopup.indexOf(hashtag), 1);
			} else {
				self.selectedHastags.splice(self.selectedHastags.indexOf(hashtag), 1);
			}
		},


		processDomList: function (selector, attributes) {
			document.querySelectorAll(selector).forEach(function (element) {
				attributes.map(function (attrName) {
					element.setAttribute(attrName[0], attrName[1]);
				});
			});
		},
		openTooltipBig: function () {
			let self = this, elem = window.event.currentTarget;
			self.processDomList('.sbi-fb-onbrd-tltp-elem', [['data-active', 'false']]);
			elem.querySelector('.sbi-fb-onbrd-tltp-elem').setAttribute('data-active', 'true');
			sbiBuilder.$forceUpdate();
		},
		closeTooltipBig: function () {
			let self = this;
			self.processDomList('.sbi-fb-onbrd-tltp-elem', [['data-active', 'false']]);
			window.event.stopPropagation();
			sbiBuilder.$forceUpdate();
		},

		/*
			FEEDS List Actions
		*/

		/**
		 * Switch Bulk Action
		 *
		 * @since 4.0
		 */
		bulkActionClick: function () {
			let self = this;
			switch (self.selectedBulkAction) {
				case 'delete':
					if (self.feedsSelected.length > 0) {
						self.openDialogBox('deleteMultipleFeeds')
					}
					break;
			}
			sbiBuilder.$forceUpdate();
		},

		/**
		 * Duplicate Feed
		 *
		 * @since 4.0
		 */
		feedActionDuplicate: function (feed) {
			let self = this,
				feedsDuplicateData = {
					action: 'sbi_feed_saver_manager_duplicate_feed',
					feed_id: feed.id
				};
			self.ajaxPost(feedsDuplicateData, function (_ref) {
				let data = _ref.data;
				self.feedsList = Object.values(Object.assign({}, data));
				//self.feedsList = data;
			});
			sbiBuilder.$forceUpdate();
		},

		/**
		 * Delete Feed
		 *
		 * @since 4.0
		 */
		feedActionDelete: function (feeds_ids) {
			let self = this,
				feedsDeleteData = {
					action: 'sbi_feed_saver_manager_delete_feeds',
					feeds_ids: feeds_ids
				};
			self.ajaxPost(feedsDeleteData, function (_ref) {
				let data = _ref.data;
				self.feedsList = Object.values(Object.assign({}, data));
				self.feedsSelected = [];
			});
		},

		/**
		 * View Feed Instances
		 *
		 * @since 4.0
		 */
		viewFeedInstances: function (feed) {
			let self = this;
			self.viewsActive.instanceFeedActive = feed;
			self.movePopUp();
			sbiBuilder.$forceUpdate();
		},

		/**
		 * Select All Feeds in List
		 *
		 * @since 4.0
		 */
		selectAllFeedCheckBox: function () {
			let self = this;
			if (!self.checkAllFeedsActive()) {
				self.feedsSelected = [];
				self.feedsList.forEach(function (feed) {
					self.feedsSelected.push(feed.id);
				});
			} else {
				self.feedsSelected = [];
			}

		},

		/**
		 * Select Single Feed in List
		 *
		 * @since 4.0
		 */
		selectFeedCheckBox: function (feedID) {
			if (this.feedsSelected.includes(feedID)) {
				this.feedsSelected.splice(this.feedsSelected.indexOf(feedID), 1);
			} else {
				this.feedsSelected.push(feedID);
			}
			sbiBuilder.$forceUpdate();
		},

		/**
		 * Check if All Feeds are Selected
		 *
		 * @since 4.0
		 */
		checkAllFeedsActive: function () {
			let self = this,
				result = true;
			self.feedsList.forEach(function (feed) {
				if (!self.feedsSelected.includes(feed.id)) {
					result = false;
				}
			});

			return result;
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
			sbiBuilder.$forceUpdate();
		},

		/*-------------------------------------------
			CUSTOMIZER FUNCTIONS
		-------------------------------------------*/
		/**
		 * HighLight Section
		 *
		 * @since 4.0
		 */
		isSectionHighLighted: function (sectionName) {
			let self = this;
			return (self.highLightedSection === sectionName || self.highLightedSection === 'all')
		},

		/**
		 * Enable Highlight Section
		 *
		 * @since 4.0
		 */
		enableHighLightSection: function (sectionId) {
			let self = this,
				listPostSection = ['customize_feedlayout', 'customize_colorscheme', 'customize_posts', 'post_style', 'individual_elements'],
				headerSection = ['customize_header'],
				followButtonSection = ['customize_followbutton'],
				loadeMoreSection = ['customize_loadmorebutton'],
				lightBoxSection = ['customize_lightbox'],
				domBody = document.getElementsByTagName("body")[0];

			self.dummyLightBoxScreen = false;
			domBody.classList.remove("no-overflow");

			if (listPostSection.includes(sectionId)) {
				self.highLightedSection = 'postList';
				self.scrollToHighLightedSection("sbi_images");
			} else if (headerSection.includes(sectionId)) {
				self.highLightedSection = 'header';
				self.scrollToHighLightedSection("sb_instagram_header");
			} else if (followButtonSection.includes(sectionId)) {
				self.highLightedSection = 'followButton';
				self.scrollToHighLightedSection("sbi_load");
			} else if (loadeMoreSection.includes(sectionId)) {
				self.highLightedSection = 'loadMore';
				self.scrollToHighLightedSection("sbi_load");
			} else if (lightBoxSection.includes(sectionId)) {
				self.highLightedSection = 'lightBox';
				self.dummyLightBoxScreen = true;
				document.body.scrollTop = 0;
				document.documentElement.scrollTop = 0;
				domBody.classList.add("no-overflow");
			} else {
				self.highLightedSection = 'all';
				self.dummyLightBoxScreen = false;
				domBody.classList.remove("no-overflow");
			}
		},


		/**
		 * Scroll to Highlighted Section
		 *
		 * @since 4.0
		 */
		scrollToHighLightedSection: function (sectionId) {
			const element = document.getElementById(sectionId) !== undefined && document.getElementById(sectionId) !== null ?
				document.getElementById(sectionId) :
				(document.getElementsByClassName(sectionId)[0] !== undefined && document.getElementsByClassName(sectionId)[0] !== null ? document.getElementsByClassName(sectionId)[0] : null);


			if (element != undefined && element != null) {
				const y = element.getBoundingClientRect().top - 120 + window.pageYOffset - 10;
				window.scrollTo({top: y, behavior: 'smooth'});
			}
		},

		/**
		 * Enable & Show Color Picker
		 *
		 * @since 4.0
		 */
		showColorPickerPospup: function (controlId) {
			this.customizerScreens.activeColorPicker = controlId;
		},

		/**
		 * Hide Color Picker
		 *
		 * @since 4.0
		 */
		hideColorPickerPospup: function () {
			this.customizerScreens.activeColorPicker = null;
		},

		switchCustomizerPreviewDevice: function (previewScreen) {
			let self = this;
			self.customizerScreens.previewScreen = previewScreen;
			self.loadingBar = true;
			window.sbi_preview_device = previewScreen;
			setTimeout(function () {
				self.setShortcodeGlobalSettings(false);
				self.loadingBar = false;
			}, 400);
			sbiBuilder.$forceUpdate();
		},
		switchCustomizerTab: function (tabId) {
			let self = this,
				domBody = document.getElementsByTagName("body")[0];

			if (self.customizerScreens.activeSection == 'settings_filters_moderation' && self.viewsActive['moderationMode'] == true) {
				self.saveModerationSettings();
			}
			self.customizerScreens.activeTab = tabId;
			self.customizerScreens.activeSection = null;
			self.customizerScreens.activeSectionData = null;
			self.highLightedSection = 'all';

			self.dummyLightBoxScreen = false;
			domBody.classList.remove("no-overflow");

			if (self.moderationShoppableModeAjaxDone && self.getModerationShoppableMode == false) {
				self.customizerControlAjaxAction('feedFlyPreview');
			}

			sbiBuilder.$forceUpdate();
		},
		switchCustomizerSection: function (sectionId, section, isNested = false, isBackElements) {
			let self = this;
			self.customizerScreens.parentActiveSection = null;
			self.customizerScreens.parentActiveSectionData = null;
			if (isNested) {
				self.customizerScreens.parentActiveSection = self.customizerScreens.activeSection;
				self.customizerScreens.parentActiveSectionData = self.customizerScreens.activeSectionData;
			}
			self.customizerScreens.activeSection = sectionId;
			self.customizerScreens.activeSectionData = section;
			self.enableHighLightSection(sectionId);
			if (sectionId === 'settings_filters_moderation') {
				self.viewsActive['moderationMode'] = false;
			}
			if (sectionId === 'settings_shoppable_feed') {
				self.customizerControlAjaxAction('feedFlyPreview');
			}

			if (self.moderationShoppableModeAjaxDone && self.getModerationShoppableMode == false) {
				self.customizerControlAjaxAction('feedFlyPreview');
			}

			sbiBuilder.$forceUpdate();
		},
		switchNestedSection: function (sectionId, section) {
			let self = this;
			if (section !== null) {
				self.customizerScreens.activeSection = sectionId;
				self.customizerScreens.activeSectionData = section;
			} else {
				let sectionArray = sectionId['sections'];
				let elementSectionData = self.customizerSidebarBuilder;

				sectionArray.map(function (elm, index) {
					elementSectionData = (elementSectionData[elm] != undefined && elementSectionData[elm] != null) ? elementSectionData[elm] : null;
				});
				if (elementSectionData != null) {
					self.customizerScreens.activeSection = sectionId['id'];
					self.customizerScreens.activeSectionData = elementSectionData;
				}
			}
			sbiBuilder.$forceUpdate();
		},
		backToPostElements: function () {
			let self = this,
				individual_elements = self.customizerSidebarBuilder['customize'].sections.customize_posts.nested_sections.individual_elements;
			self.customizerScreens.activeSection = 'customize_posts';
			self.customizerScreens.activeSectionData = self.customizerSidebarBuilder['customize'].sections.customize_posts;
			self.switchCustomizerSection('individual_elements', individual_elements, true, true);
			sbiBuilder.$forceUpdate();
		},

		/**
		 * Should show toggleset type cover
		 *
		 * @since 2.0
		 */
		shouldShowTogglesetCover: function (toggle) {
			let self = this;
			if (self.shouldDisableProFeatures) {
				return toggle.checkExtension != undefined && !self.checkExtensionActive(toggle.checkExtension)
			} else {
				return false
			}
		},

		/**
		 * Open extension popup from toggleset cover
		 *
		 * @since 2.0
		 */
		togglesetExtPopup: function (toggle, controlID) {
			let self = this;
			if (controlID == 'sortby' && toggle.value == 'likes') {
				window.open(toggle.utmLink, '_blank');
				return;
			}
			self.viewsActive.extensionsPopupElement = toggle.checkExtension;
		},

		changeSettingValue: function (settingID, value, doProcess = true, ajaxAction = false) {
			let self = this;
			if (doProcess) {
				self.customizerFeedData.settings[settingID] = value;
			}
			if (ajaxAction !== false) {
				self.customizerControlAjaxAction(ajaxAction, settingID);
			}
			self.regenerateLayout(settingID);
		},

		//Shortcode Global Layout Settings
		regenerateLayout: function (settingID) {
			let self = this,
				regenerateFeedHTML = [
					'layout'
				],
				relayoutFeed = [
					'layout',
					'carouselarrows',
					'carouselpag',
					'carouselautoplay',
					'carouseltime',
					'carouselloop',
					'carouselrows',
					'cols',
					'colstablet',
					'colsmobile',
					'highlighttype',
					'highlightoffset',
					'highlightpattern',
					'highlightids',
					'highlighthashtag',
					'imagepadding'
				];
			if (relayoutFeed.includes(settingID)) {
				setTimeout(function () {
					self.setShortcodeGlobalSettings(true);
				}, 200)
			}

		},


		//Get Number of Columns depending on the Preview Screen
		getColsPreviewScreen: function () {
			let self = this;
			if (self.getModerationShoppableMode) {
				return 4;
			}
			switch (self.customizerScreens.previewScreen) {
				case 'mobile':
					return self.customizerFeedData.settings.colsmobile
				case 'tablet':
					return self.customizerFeedData.settings.colstablet
				default:
					return self.customizerFeedData.settings.cols
			}
		},

		//Get Customizer Additional CSS Classes
		getAdditionalCustomizerClasses: function () {
			let self = this,
				additionalCssClasses = '';
			if (self.getModerationShoppableMode) {
				additionalCssClasses += ' sbi-customizer-ms-modes ';
			}
			return additionalCssClasses;
		},
		//Shortcode Global Layout Settings
		setShortcodeGlobalSettings: function (flyPreview = false) {
			let self = this,
				instagramFeed = jQuery("html").find("#sb_instagram"),
				feedSettings = self.jsonParse(instagramFeed.attr('data-options')),
				customizerSettings = self.customizerFeedData.settings;
			self.alterLightboxBehavior();

			if (JSON.stringify(self.feedSettingsDomOptions) !== JSON.stringify(feedSettings) || flyPreview == true) {

				if (customizerSettings.layout == 'grid' || self.getModerationShoppableMode) {
					feedSettings = self.gridShortcodeSettings(feedSettings, instagramFeed);
				} else if (customizerSettings.layout == 'carousel') {
					feedSettings = self.carouselShortcodeSettings(feedSettings, instagramFeed, customizerSettings);
				} else if (customizerSettings.layout == 'masonry') {
					feedSettings = self.masonryShortcodeSettings(feedSettings, instagramFeed, customizerSettings);
				} else if (customizerSettings.layout == 'highlight') {
					feedSettings = self.highlightShortcodeSettings(feedSettings, instagramFeed, customizerSettings);
				}

				if (flyPreview === true) {
					if (typeof feedSettings.avatars == 'object' && Object.keys(feedSettings.avatars)[0] != undefined) {
						let headerSourceName = Object.keys(feedSettings.avatars)[0],
							newHeaderData = null;
						let newHeaderDataMap = self.sourcesList.map(function (source) {
							if (source.username === headerSourceName) {
								newHeaderData = source != undefined ? source : null;
							}
						});
						if (newHeaderData !== null && newHeaderData?.header_data !== undefined) {
							self.customizerFeedData.header = newHeaderData;
							self.customizerFeedData.headerData = newHeaderData.header_data;
						}
					}
				}
				instagramFeed.attr("data-options", JSON.stringify(feedSettings));
				//setTimeout(function(){
				window.sbi_init()
				//},200)
				self.feedSettingsDomOptions = feedSettings;
			}
			jQuery('body').find('#sbi_load .sbi_load_btn').unbind('click')

		},

		//Grid Shortcode Settings
		gridShortcodeSettings: function (feedSettings, instagramFeed) {
			let self = this;
			feedSettings['grid'] = true;
			self.destroyHighlightLayout(instagramFeed);
			self.destroyMasonryLayout(instagramFeed);
			self.destoryOwl(instagramFeed);
			delete feedSettings['carousel'];
			delete feedSettings['masonry'];
			delete feedSettings['highlight'];
			return feedSettings;
		},

		//Masonry Shortcode Settings
		masonryShortcodeSettings: function (feedSettings, instagramFeed) {
			let self = this;
			feedSettings['masonry'] = true;
			self.destroyHighlightLayout(instagramFeed);
			self.destoryOwl(instagramFeed);
			delete feedSettings['grid'];
			delete feedSettings['carousel'];
			delete feedSettings['highlight'];
			jQuery('.sbi_photo img').show();
			return feedSettings;
		},

		//Carousel Shortcode Settings
		carouselShortcodeSettings: function (feedSettings, instagramFeed, customizerSettings) {
			let self = this,
				arrows = self.valueIsEnabled(customizerSettings['carouselarrows']),
				pag = self.valueIsEnabled(customizerSettings['carouselpag']),
				autoplay = self.valueIsEnabled(customizerSettings['carouselautoplay']),
				time = autoplay ? parseInt(customizerSettings['carouseltime']) : false,
				loop = self.checkNotEmpty(customizerSettings['carouselloop']) && customizerSettings['carouselloop'] !== 'rewind' ? false : true,
				rows = customizerSettings['carouselrows'] ? Math.min(parseInt(customizerSettings['carouselrows']), 2) : 1;

			feedSettings['carousel'] = [arrows, pag, autoplay, time, loop, rows];
			self.destoryOwl(instagramFeed);
			self.destroyHighlightLayout(instagramFeed);
			self.destroyMasonryLayout(instagramFeed);
			delete feedSettings['grid'];
			delete feedSettings['masonry'];
			delete feedSettings['highlight'];
			return feedSettings;
		},

		//Highlight Shortcode Settings
		highlightShortcodeSettings: function (feedSettings, instagramFeed, customizerSettings) {
			let self = this,
				type = customizerSettings['highlighttype'].trim(),
				pattern = customizerSettings['highlightpattern'].trim(),
				offset = parseInt(customizerSettings['highlightoffset']),
				hashtag = customizerSettings['highlighthashtag'].replace(',', '|').replace('#', '').replace(' ', '').trim(),
				ids = customizerSettings['highlightids'].replace(',', '|').replace('sbi_', '').replace(' ', '').trim();
			feedSettings['highlight'] = [type, pattern, offset, hashtag, ids];

			self.destroyHighlightLayout(instagramFeed);
			self.destroyMasonryLayout(instagramFeed);
			self.destoryOwl(instagramFeed);
			delete feedSettings['carousel'];
			delete feedSettings['masonry'];
			delete feedSettings['grid'];
			return feedSettings;
		},


		//destroy Owl
		destoryOwl: function (instagramFeed) {
			let self = this;
			let owlCarouselCtn = instagramFeed.find('.sbi_carousel');
			if (instagramFeed.find('#sbi_images').hasClass('sbi_carousel')) {
			}
		},

		//Destroy Masonry Layout
		destroyMasonryLayout: function (instagramFeed) {
			let self = this;
			if (instagramFeed.hasClass('sbi_masonry')) {
				instagramFeed.find('#sbi_images').css({'height': 'unset'});
				instagramFeed.find('.sbi_item').each(function () {
					jQuery(this).attr({'style': ''});
				});
				jQuery("#sbi_images").smashotope('destroy');
				instagramFeed.removeClass('sbi_masonry')
			}
		},


		//Destroy Highlight Layout
		destroyHighlightLayout: function (instagramFeed) {
			let self = this;
			if (instagramFeed.hasClass('sbi_highlight')) {
				instagramFeed.find('#sbi_images').css({'height': 'unset'});
				instagramFeed.find('.sbi_item').each(function () {
					jQuery(this).attr({'style': ''});
				});
				jQuery("#sbi_images").smashotope('destroy');
				instagramFeed.removeClass('sbi_highlight')
			}
		},

		//Tablet Cols Classes
		getTabletColsClass: function () {
			let self = this,
				customizerSettings = self.customizerFeedData.settings;

			return ' sbi_tab_col_' + parseInt(customizerSettings.colstablet);
		},

		//Mobile Cols Classes
		getMobileColsClass: function () {
			let self = this,
				customizerSettings = self.customizerFeedData.settings,
				disableMobile = self.valueIsEnabled(customizerSettings.disablemobile);

			if (!disableMobile && customizerSettings.colsmobile !== 'same') {
				let colsmobile = parseInt(customizerSettings.colsmobile) > 0 ? parseInt(customizerSettings.colsmobile) : 'auto';
				return ' sbi_mob_col_' + colsmobile;
			} else {
				let colsmobile = parseInt(customizerSettings.cols) > 0 ? parseInt(customizerSettings.cols) : 4;
				return ' sbi_disable_mobile sbi_mob_col_' + parseInt(customizerSettings.cols);

			}
		},

		getHeaderClass: function (headerType) {
			let self = this,
				customizerSettings = self.customizerFeedData.settings,
				headerClasses = 'sb_instagram_header ';

			headerClasses += 'sbi_feed_type_' + customizerSettings['type'];
			headerClasses += customizerSettings['headerstyle'] === 'centered' && headerType === 'normal' ? ' sbi_centered' : '';
			headerClasses += ['medium', 'large'].includes(customizerSettings['headersize']) ? ' sbi_' + customizerSettings['headersize'] : '';
			headerClasses += headerType === 'boxed' ? ' sbi_header_style_boxed' : '';
			headerClasses += self.getHeaderAvatar() === false ? ' sbi_no_avatar' : '';
			headerClasses += self.getPaletteClass('_header');
			if (self.customizerScreens.previewScreen == 'mobile') {
				headerClasses += ' sbi-mobile-preview ';
			}
			let shouldShowBio = self.checkNotEmpty(self.getHeaderBio()) ? self.valueIsEnabled(customizerSettings['showbio']) : false;
			headerClasses += !shouldShowBio ? ' sbi_no_bio ' : '';
			return headerClasses;
		},

		//Header Name
		getHeaderName: function () {
			let self = this,
				headerData = self.customizerFeedData.headerData;
			if (self.hasOwnNestedProperty(headerData, 'name') && self.checkNotEmpty(headerData['name'])) {
				return headerData['name'];
			} else if (self.hasOwnNestedProperty(headerData, 'data.full_name')) {
				return headerData['data']['full_name'];
			}
			return self.getHeaderUserName();
		},

		//Header User Name
		getHeaderUserName: function () {
			let self = this,
				headerData = self.customizerFeedData.headerData;
			if (self.hasOwnNestedProperty(headerData, 'username') && self.checkNotEmpty(headerData['username'])) {
				return headerData['username'];
			} else if (self.hasOwnNestedProperty(headerData, 'user.username')) {
				return headerData['user']['username'];
			} else if (self.hasOwnNestedProperty(headerData, 'data.username')) {
				return headerData['data']['username'];
			}
			return '';
		},

		getHeaderUserNameTitle: function () {
			let username = this.getHeaderUserName();
			return username !== '' ? '@' + username : '';
		},

		//Header Media Count
		getHeaderMediaCount: function () {
			let self = this,
				headerData = self.customizerFeedData.headerData;
			if (self.hasOwnNestedProperty(headerData, 'data.counts.media')) {
				return headerData['data']['counts']['media'];
			} else if (self.hasOwnNestedProperty(headerData, 'counts.media')) {
				return headerData['counts']['media'];
			} else if (self.hasOwnNestedProperty(headerData, 'media_count')) {
				return headerData['media_count'];
			}
			return '';
		},

		//Header Followers Count
		getHeaderFollowersCount: function () {
			let self = this,
				headerData = self.customizerFeedData.headerData;
			if (self.hasOwnNestedProperty(headerData, 'data.counts.followed_by')) {
				return headerData['data']['counts']['followed_by'];
			} else if (self.hasOwnNestedProperty(headerData, 'counts.followed_by')) {
				return headerData['counts']['followed_by'];
			} else if (self.hasOwnNestedProperty(headerData, 'followers_count')) {
				return headerData['followers_count'];
			}
			return '';
		},

		//Header Avatar
		getHeaderAvatar: function () {
			let self = this,
				customizerSettings = self.customizerFeedData.settings,
				headerData = self.customizerFeedData.headerData,
				header = self.customizerFeedData.header;
			if (self.checkNotEmpty(customizerSettings['customavatar'])) {
				return customizerSettings['customavatar'];
			} else if (header['local_avatar_url'] != false && self.checkNotEmpty(header['local_avatar_url'])) {
				return header['local_avatar_url'];
			} else {
				if (self.hasOwnNestedProperty(headerData, 'profile_picture')) {
					return headerData['profile_picture'];
				} else if (self.hasOwnNestedProperty(headerData, 'profile_picture_url')) {
					return headerData['profile_picture_url'];
				} else if (self.hasOwnNestedProperty(headerData, 'user.profile_picture')) {
					return headerData['user']['profile_picture'];
				} else if (self.hasOwnNestedProperty(headerData, 'data.profile_picture')) {
					return headerData['data']['profile_picture'];
				}
			}
			return self.pluginURL + 'img/thumb-placeholder.png';
		},


		//Header Bio
		getHeaderBio: function () {
			let self = this,
				customizerSettings = self.customizerFeedData.settings,
				headerData = self.customizerFeedData.headerData;

			if (self.checkNotEmpty(customizerSettings['custombio'])) {
				return customizerSettings['custombio'];
			} else if (self.hasOwnNestedProperty(headerData, 'data.bio')) {
				return headerData['data']['bio'];
			} else if (self.hasOwnNestedProperty(headerData, 'bio')) {
				return headerData['bio'];
			} else if (self.hasOwnNestedProperty(headerData, 'biography')) {
				return headerData['biography'];
			}
			return '';
		},


		//Header Text Class
		getTextHeaderClass: function () {
			let self = this,
				customizerSettings = self.customizerFeedData.settings,
				headerData = self.customizerFeedData.headerData,
				headerClass = 'sbi_header_text ',
				shouldShowBio = self.checkNotEmpty(self.getHeaderBio()) ? self.valueIsEnabled(customizerSettings['showbio']) : false,
				shouldShowInfo = shouldShowBio || self.valueIsEnabled(customizerSettings['showfollowers']);
			headerClass += !shouldShowBio ? 'sbi_no_bio ' : '',
				headerClass += !shouldShowInfo ? 'sbi_no_info' : '';

			return headerClass;
		},

		//Header Bio Info Class
		getHeaderBioInfoClass: function () {
			let self = this,
				customizerSettings = self.customizerFeedData.settings,
				headerData = self.customizerFeedData.headerData,
				headerClass = 'sbi_bio_info sbi_feedtheme_bio ',
				shouldShowBio = self.checkNotEmpty(self.getHeaderBio()) ? self.valueIsEnabled(customizerSettings['showbio']) : false,
				shouldShowInfo = shouldShowBio || self.valueIsEnabled(customizerSettings['showfollowers']);
			headerClass += !shouldShowBio ? 'sbi_no_bio ' : '',
				headerClass += !shouldShowInfo ? 'sbi_no_info' : '';

			return headerClass;
		},

		//Get Story Delays
		getStoryDelays: function () {
			let self = this,
				customizerSettings = self.customizerFeedData.settings;
			return self.checkNotEmpty(customizerSettings['storiestime']) ? Math.max(500, parseInt(customizerSettings['storiestime'])) : 5000;
		},

		//Get Story Data
		getStoryData: function () {
			let self = this,
				customizerSettings = self.customizerFeedData.settings,
				headerData = self.customizerFeedData.headerData;
			if (self.hasOwnNestedProperty(headerData, 'stories') && headerData.stories.length > 0 && self.valueIsEnabled(customizerSettings['stories'])) {
				return headerData['stories'];
			}
			return false;
		},


		//Image Chooser
		imageChooser: function (settingID) {
			let self = this;
			let uploader = wp.media({
				frame: 'post',
				title: 'Media Uploader',
				button: {text: 'Choose Media'},
				library: {type: 'image'},
				multiple: false
			}).on('close', function () {
				let selection = uploader.state().get('selection');
				if (selection.length != 0) {
					attachment = selection.first().toJSON();
					self.customizerFeedData.settings[settingID] = attachment.url;
				}
			}).open();
		},

		//Change Switcher Settings
		changeSwitcherSettingValue: function (settingID, onValue, offValue, ajaxAction = false) {
			let self = this;
			self.customizerFeedData.settings[settingID] = self.customizerFeedData.settings[settingID] == onValue ? offValue : onValue;
			if (ajaxAction !== false) {
				self.customizerControlAjaxAction(ajaxAction);
			}


			self.regenerateLayout(settingID);
		},

		//Checkbox List
		changeCheckboxListValue: function (settingID, value, ajaxAction = false) {
			let self = this,
				settingValue = self.customizerFeedData.settings[settingID].split(',');
			if (!Array.isArray(settingValue)) {
				settingValue = [settingValue];
			}
			if (settingValue.includes(value)) {
				settingValue.splice(settingValue.indexOf(value), 1);
			} else {
				settingValue.push(value);
			}
			self.customizerFeedData.settings[settingID] = settingValue.join(',');
		},


		//Section Checkbox
		changeCheckboxSectionValue: function (settingID, value, ajaxAction = false) {
			let self = this;
			let settingValue = self.customizerFeedData.settings[settingID];
			if (!Array.isArray(settingValue) && settingID == 'type') {
				settingValue = [settingValue];
			}
			if (settingValue.includes(value)) {
				settingValue.splice(settingValue.indexOf(value), 1);
			} else {
				settingValue.push(value);
			}
			if (settingID == 'type') {
				self.processFeedTypesSources(settingValue);
			}
			//settingValue = (settingValue.length == 1 && settingID == 'type') ? settingValue[0] : settingValue;
			self.customizerFeedData.settings[settingID] = settingValue;
			if (ajaxAction !== false) {
				self.customizerControlAjaxAction(ajaxAction);
			}
			event.stopPropagation()

		},
		checkboxSectionValueExists: function (settingID, value) {
			let self = this;
			let settingValue = self.customizerFeedData.settings[settingID];
			return settingValue.includes(value) ? true : false;
		},

		/**
		 * Check Control Condition
		 *
		 * @since 4.0
		 */
		checkControlCondition: function (conditionsArray = [], checkExtensionActive = false, checkExtensionActiveDimmed = false) {
			let self = this,
				isConditionTrue = 0;
			if (conditionsArray['hoverdisplay'] != undefined) {
				if (self.customizerFeedData.settings['hoverdisplay']
					&& this.checkboxSectionValueExists('hoverdisplay', conditionsArray['hoverdisplay'])) {
					isConditionTrue += 1;
				} else {
					isConditionTrue += 0;
				}
			} else {
				Object.keys(conditionsArray).forEach(function (condition, index) {
					if (conditionsArray[condition].indexOf(self.customizerFeedData.settings[condition]) !== -1)
						isConditionTrue += 1

					if (condition == 'checkPersonalAccount' && self[condition] != undefined && self.checkPersonalAccount() == conditionsArray[condition][0]) {
						isConditionTrue += 1;
					}
				});
			}
			let extensionCondition = checkExtensionActive != undefined && checkExtensionActive != false ? self.checkExtensionActive(checkExtensionActive) : true;
				extensionCondition = checkExtensionActiveDimmed != undefined && checkExtensionActiveDimmed != false && !self.checkExtensionActive(checkExtensionActiveDimmed) ? false : extensionCondition;

			return (isConditionTrue == Object.keys(conditionsArray).length) ? (extensionCondition) : false;
		},

		/**
		 * Check Color Override Condition
		 *
		 * @since 4.0
		 */
		checkControlOverrideColor: function (overrideConditionsArray = []) {
			let self = this,
				isConditionTrue = 0;
			overrideConditionsArray.forEach(function (condition, index) {
				if (self.checkNotEmpty(self.customizerFeedData.settings[condition]) && self.customizerFeedData.settings[condition].replace(/ /gi, '') != '#') {
					isConditionTrue += 1
				}
			});
			return (isConditionTrue >= 1) ? true : false;
		},

		/**
		 * Show Control
		 *
		 * @since 4.0
		 */
		isControlShown: function (control) {
			let self = this;
			if (control.checkViewDisabled != undefined) {
				return !self.viewsActive[control.checkViewDisabled];
			}
			if (control.checkView != undefined) {
				return !self.viewsActive[control.checkView];
			}

			if (control.checkExtension != undefined && control.checkExtension != false && !self.checkExtensionActive(control.checkExtension)) {
				return self.checkExtensionActive(control.checkExtension);
			}

			if (control.conditionDimmed != undefined && self.checkControlCondition(control.conditionDimmed))
				return self.checkControlCondition(control.conditionDimmed);
			if (control.overrideColorCondition != undefined) {
				return self.checkControlOverrideColor(control.overrideColorCondition);
			}

			return (control.conditionHide != undefined && control.condition != undefined || control.checkExtension != undefined)
				? self.checkControlCondition(control.condition, control.checkExtension)
				: true;
		},

		checkExtensionActive: function (extension) {
			let self = this;
			return self.activeExtensions[extension];
		},

		expandSourceInfo: function (sourceId) {
			let self = this;
			self.customizerScreens.sourceExpanded = (self.customizerScreens.sourceExpanded === sourceId) ? null : sourceId;
			window.event.stopPropagation()
		},

		resetColor: function (controlId) {
			this.customizerFeedData.settings[controlId] = '';
		},

		//Source Active Customizer
		isSourceActiveCustomizer: function (source) {
			let self = this;
			return (
					Array.isArray(self.customizerFeedData.settings.sources.map) ||
					self.customizerFeedData.settings.sources instanceof Object
				) &&
				self.customizerScreens.sourcesChoosed.map(s => s.account_id).includes(source.account_id);
			//self.customizerFeedData.settings.sources.map(s => s.account_id).includes(source.account_id);
		},
		//Choose Source From Customizer
		selectSourceCustomizer: function (source, isRemove = false) {
			let self = this,
				isMultifeed = (self.activeExtensions['multifeed'] !== undefined && self.activeExtensions['multifeed'] == true),
				sourcesListMap = Array.isArray(self.customizerFeedData.settings.sources) || self.customizerFeedData.settings.sources instanceof Object ? self.customizerFeedData.settings.sources.map(s => s.account_id) : [];
			if (isMultifeed) {
				if (self.customizerScreens.sourcesChoosed.map(s => s.account_id).includes(source.account_id)) {
					let indexToRemove = self.customizerScreens.sourcesChoosed.findIndex(src => src.account_id === source.account_id);
					self.customizerScreens.sourcesChoosed.splice(indexToRemove, 1);
					if (isRemove) {
						self.customizerFeedData.settings.sources.splice(indexToRemove, 1);
					}
				} else {
					self.customizerScreens.sourcesChoosed.push(source);
				}
			} else {
				self.customizerScreens.sourcesChoosed = (sourcesListMap.includes(source)) ? [] : [source];
			}
			sbiBuilder.$forceUpdate();
		},
		closeSourceCustomizer: function () {
			let self = this;
			self.viewsActive['sourcePopup'] = false;
			//self.customizerFeedData.settings.sources = self.customizerScreens.sourcesChoosed;
			sbiBuilder.$forceUpdate();
		},
		customizerFeedThemePrint: function () {
			let self = this;
			// Support for versions before v4.2
			if (self.customizerFeedData.settings.feedtheme == undefined) {
				self.customizerFeedData.settings.feedtheme = 'default_theme';
			}
			result = self.feedThemes.filter(function (tp) {
				return tp.type === self.customizerFeedData.settings.feedtheme
			});
			self.customizerScreens.printedTheme = result.length > 0 ? result[0] : [];
			return result.length > 0 ? true : false;
		},
		customizerFeedTypePrint: function () {
			let self = this,
				combinedTypes = self.feedTypes.concat(self.advancedFeedTypes);
			result = combinedTypes.filter(function (tp) {
				return tp.type === self.customizerFeedData.settings.feedtype
			});
			self.customizerScreens.printedType = result.length > 0 ? result[0] : [];
			return result.length > 0 ? true : false;
		},
		customizerFeedTemplatePrint: function () {
			let self = this;
			if (self.customizerFeedData.settings.feedtemplate == undefined) {
				self.customizerFeedData.settings.feedtemplate = 'ft_default';
			}
			result = self.feedTemplates.filter(function (tp) {
				return tp.type === self.customizerFeedData.settings.feedtemplate
			});
			self.customizerScreens.printedTemplate = result.length > 0 ? result[0] : [];
			return result.length > 0 ? true : false;
		},
		choosedFeedTypeCustomizer: function (feedType) {
			let self = this, result = false;
			if (
				(self.viewsActive.feedTypeElement === null && self.customizerFeedData.settings.feedtype === feedType) ||
				(self.viewsActive.feedTypeElement !== null && self.viewsActive.feedTypeElement == feedType)
			) {
				result = true;
			}
			return result;
		},
		choosedFeedTemplateCustomizer: function (feedtemplate) {
			let self = this, result = false;
			if (
				(self.viewsActive.feedTemplateElement === null && self.customizerFeedData.settings.feedtemplate === feedtemplate) ||
				(self.viewsActive.feedTemplateElement !== null && self.viewsActive.feedTemplateElement == feedtemplate)
			) {
				result = true;
			}
			return result;
		},
		updateFeedTypeCustomizer: function () {
			let self = this;
			if (self.viewsActive.feedTypeElement === 'socialwall') {
				window.location.href = sbi_builder.pluginsInfo.social_wall.settingsPage;
				return;
			}
			self.setType(self.viewsActive.feedTypeElement);

			self.customizerFeedData.settings.feedtype = self.viewsActive.feedTypeElement;
			self.viewsActive.feedTypeElement = null;
			self.viewsActive.feedtypesPopup = false;
			self.customizerControlAjaxAction('feedFlyPreview');
			sbiBuilder.$forceUpdate();
		},
		updateFeedTemplateCustomizer: function () {
			let self = this;
			self.customizerFeedData.settings.feedtemplate = self.viewsActive.feedTemplateElement;
			self.viewsActive.feedTemplateElement = null;
			self.viewsActive.feedtemplatesPopup = false;
			self.customizerControlAjaxAction('feedTemplateFlyPreview');
			sbiBuilder.$forceUpdate();
		},
		updateFeedThemeCustomizer: function (feedTheme) {
			let self = this;
			self.customizerFeedData.settings.feedtheme = feedTheme;
			sbiBuilder.$forceUpdate();
		},
		updateInputWidth: function () {
			this.customizerScreens.inputNameWidth = ((document.getElementById("sbi-csz-hd-input").value.length + 6) * 8) + 'px';
		},

		feedPreviewMaker: function () {
			let self = this;
			return self.template;
			//return self.template == null ? null : "<div>" + self.template + "</div>";
		},

		customizerStyleMaker: function () {
			let self = this;
			if (self.customizerSidebarBuilder) {
				self.feedStyle = '';
				Object.values(self.customizerSidebarBuilder).map(function (tab) {
					self.customizerSectionStyle(tab.sections);
				});
				return '<style type="text/css">' + self.feedStyle + '</style>';
			}
			return false;
		},

		escapeHTML: function (text) {
			return text.replace(/&/g, "&amp;")
				.replace(/</g, "&lt;")
				.replace(/>/g, "&gt;")
				.replace(/"/g, "&quot;")
				.replace(/'/g, "&#039;");
		},

		decodeVueHTML: function (text) {
			// write a regex to match the v-show and v-if attributes
			const regex = /(?:v-if|v-show|:data-.+?)="([^"]*?)"/g;
			let match;
			let decodedText = text;

			const map = {
				'&amp;': '&',
				'&lt;': '<',
				'&gt;': '>',
				'&quot;': '"',
				'&#039;': "'",
			};

			while ((match = regex.exec(text)) !== null) {
				// This is necessary to avoid infinite loops with zero-width matches
				if (match.index === regex.lastIndex) {
					regex.lastIndex++;
				}

				match.forEach((match, groupIndex) => {
					if (groupIndex == 1) {
						decodedText = decodedText.replace(match, match.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, m => map[m]));
					}
				});
			}

			return decodedText;
		},

		/**
		 * Get Feed Preview Global CSS Class
		 *
		 * @since 4.0
		 * @return String
		 */
		getPaletteClass: function (context = '') {
			let self = this,
				colorPalette = self.customizerFeedData.settings.colorpalette;

			if (self.checkNotEmpty(colorPalette)) {
				let feedID = colorPalette === 'custom' ? ('_' + self.customizerFeedData.feed_info.id) : '';
				return colorPalette !== 'inherit' ? ' sbi' + context + '_palette_' + colorPalette + feedID : '';
			}
			return '';
		},

		customizerSectionStyle: function (sections) {
			let self = this;
			Object.values(sections).map(function (section) {
				if (section.controls) {
					Object.values(section.controls).map(function (control) {
						self.returnControlStyle(control);
					});
				}
				if (section.nested_sections) {
					self.customizerSectionStyle(section.nested_sections);
					Object.values(section.nested_sections).map(function (nestedSections) {
						Object.values(nestedSections.controls).map(function (nestedControl) {
							if (nestedControl.section) {
								self.customizerSectionStyle(nestedControl);
							}
						});
					});
				}
			});
		},
		returnControlStyle: function (control) {
			let self = this;
			if (control.style) {
				let controlStyle = self.legacyCSSEnabled && control.legacy != undefined
					? control.legacy : control.style;
				Object.entries(controlStyle).map(function (css) {
					let condition = control.condition != undefined || control.checkExtension != undefined ? self.checkControlCondition(control.condition, control.checkExtension) : true;
					if (condition) {
						self.feedStyle +=
							css[0] + '{' +
							css[1].replace("{{value}}", self.customizerFeedData.settings[control.id]) +
							'}';
					}
				});
			}
		},


		/**
		 * Customizer Control Ajax
		 * Some of the customizer controls need to perform Ajax
		 * Calls in order to update the preview
		 *
		 * @since 6.0
		 */
		customizerControlAjaxAction: function (actionType, settingID = false) {
			let self = this;
			switch (actionType) {
				case 'feedFlyPreview':
					self.loadingBar = true;
					self.templateRender = false;
					let previewFeedData = {
						action: 'sbi_feed_saver_manager_fly_preview',
						feedID: self.customizerFeedData.feed_info.id,
						previewSettings: self.customizerFeedData.settings,
						feedName: self.customizerFeedData.feed_info.feed_name,
					};
					if (self.getModerationShoppableMode) {
						previewFeedData['moderationShoppableMode'] = true;
						previewFeedData['moderationShoppableModeOffset'] = self.moderationShoppableModeOffset;
						previewFeedData['moderationShoppableShowSelected'] = self.moderationShoppableShowSelected;
					}

					self.ajaxPost(previewFeedData, function (_ref) {
						self.moderationShoppableisLoading = false;
						let data = _ref.data;
						if (data !== false) {
							self.updatedTimeStamp = new Date().getTime();
							self.shouldPaginateNext = true;
							if (self.getModerationShoppableMode && self.moderationShoppableModeOffset >= 1) {
								self.template = String("<div>" + this.decodeVueHTML(data.feed_html) + "</div>");
								if (data?.feedStatus?.shouldPaginate != undefined && data.feedStatus.shouldPaginate == false) {
									self.shouldPaginateNext = false;
								}
							} else {
								self.template = String("<div>" + this.decodeVueHTML(data) + "</div>");
							}

							self.moderationShoppableModeAjaxDone = self.getModerationShoppableMode ? true : false;
							self.processNotification("previewUpdated");
						} else {
							self.processNotification("unkownError");
						}
						jQuery('body').find('#sbi_load .sbi_load_btn').unbind('click')
					});
					break;
				case 'feedTemplateFlyPreview':
					self.loadingBar = true;
					let previewTemplateFeedData = {
						action: 'sbi_feed_saver_manager_fly_preview',
						feedID: self.customizerFeedData.feed_info.id,
						previewSettings: self.customizerFeedData.settings,
						isFeedTemplatesPopup: true,
						feedName: self.customizerFeedData.feed_info.feed_name
					};
					self.ajaxPost(previewTemplateFeedData, function (_ref) {
						let data = _ref.data;
						if (data !== false) {
							self.customizerFeedData.settings = data.customizerData;
							self.template = String("<div>" + this.decodeVueHTML(data.feed_html) + "</div>");
							self.processNotification("previewUpdated");
						} else {
							self.processNotification("unkownError");
						}
					});
					break;
				case 'feedPreviewRender':
					setTimeout(function () {
					}, 150);
					break;
			}
		},

		checkModerationList: function () {
			if (this.customizerFeedData?.settings?.moderationlist?.list_type_selected === undefined) {
				this.customizerFeedData.settings.moderationlist = {
					"list_type_selected": "allow",
					"allow_list": [],
					"block_list": []
				};
			}
		},

		/**
		 * Ajax Action : Save Feed Settings
		 *
		 * @since 4.0
		 */
		saveFeedSettings: function (leavePage = false) {
			let self = this;
			self.checkModerationList();
			Object.assign(this.customizerFeedData.settings.moderationlist, this.moderationSettings);
			this.customizerFeedData.settings.customBlockModerationlist = `${this.customBlockModerationlistTemp}`;
			let sources = [],
				updateFeedData = {
					action: 'sbi_feed_saver_manager_builder_update',
					update_feed: 'true',
					feed_id: self.customizerFeedData.feed_info.id,
					feed_name: self.customizerFeedData.feed_info.feed_name,
					settings: self.customizerFeedData.settings,
					sources: self.getFeedIdSourcesSaver(),
					tagged: self.getFeedIdSourcesTaggedSaver(),
					hashtag: self.getFeedHashtagsSaver(),
					type: self.getFeedTypeSaver(),
					shoppablelist: self.customizerFeedData.settings.shoppablelist,
					moderationlist: self.customizerFeedData.settings.moderationlist
				};
			self.loadingBar = true;
			self.ajaxPost(updateFeedData, function (_ref) {
				let data = _ref.data;
				if (data && data.success === true) {
					self.processNotification('feedSaved');
					self.customizerFeedDataInitial = self.customizerFeedData;
					if (leavePage === true) {
						setTimeout(function () {
							window.location.href = self.builderUrl;
						}, 1500)
					}
				} else {
					self.processNotification('feedSavedError');
				}
			});
			sbiBuilder.$forceUpdate();

		},

		/**
		 * Ajax Action : Clear Single Feed Cache
		 * Update Feed Preview Too
		 * @since 4.0
		 */
		clearSingleFeedCache: function () {
			let self = this,
				sources = [],
				clearFeedData = {
					action: 'sbi_feed_saver_manager_clear_single_feed_cache',
					feedID: self.customizerFeedData.feed_info.id,
					previewSettings: self.customizerFeedData.settings,
				};
			self.loadingBar = true;
			self.ajaxPost(clearFeedData, function (_ref) {
				let data = _ref.data;
				if (data !== false) {

					self.processNotification('cacheCleared');
				} else {
					self.processNotification("unkownError");
				}
			})
			sbiBuilder.$forceUpdate();
		},

		/**
		 * Clear & Reset Color Override
		 *
		 * @since 4.0
		 */
		resetColorOverride: function (settingID) {
			this.customizerFeedData.settings[settingID] = '';
		},

		/**
		 * Moderation & Shoppable Mode Pagination
		 *
		 * @since 4.0
		 */
		moderationModePagination: function (type) {
			let self = this;
			if (self.moderationShoppableisLoading) {
				return;
			}
			if (type == 'next') {
				self.moderationShoppableModeOffset = self.moderationShoppableModeOffset + 1;
			}
			if (type == 'previous') {
				self.moderationShoppableModeOffset = self.moderationShoppableModeOffset > 0 ? (self.moderationShoppableModeOffset - 1) : 0;
			}

			if (type == 'first') {
				self.moderationShoppableModeOffset = 0;
			}

			if (type == 'last') {
				self.moderationShoppableModeOffset = self.moderationShoppableModeOffsetLast;
			}

			self.moderationShoppableModeOffsetLast = self.moderationShoppableModeOffset > self.moderationShoppableModeOffsetLast ? self.moderationShoppableModeOffset : self.moderationShoppableModeOffsetLast;
			self.moderationShoppableisLoading = true;
			self.customizerControlAjaxAction('feedFlyPreview');
		},

		/**
		 * Moderation & Shoppable Mode Show Selected
		 *
		 * @since 4.0
		 */
		moderationModeShowSelected: function (showSelected) {
			if (showSelected !== this.moderationShoppableShowSelected) {
				this.moderationShoppableShowSelected = showSelected;
				this.moderationShoppableModeOffset = 0;
				this.customizerControlAjaxAction('feedFlyPreview');
			}

		},


		/**
		 * Remove Source Form List Multifeed
		 *
		 * @since 4.0
		 */
		removeSourceCustomizer: function (type, args = []) {
			let self = this;
			Object.assign(self.customizerScreens.sourcesChoosed, self.customizerFeedData.settings.sources);
			self.selectSourceCustomizer(args, true);
			sbiBuilder.$forceUpdate();
			window.event.stopPropagation();
		},

		/**
		 * Custom Flied CLick
		 * Action
		 * @since 6.0
		 */
		fieldCustomClickAction: function (clickAction) {
			let self = this;
			switch (clickAction) {
				case 'clearCommentCache':
					self.clearCommentCache();
					break;
			}
		},

		/**
		 * Clear Comment Cache
		 * Action
		 * @since 6.0
		 */
		clearCommentCache: function () {
			let self = this;
			self.loadingBar = true;
			let clearCommentCacheData = {
				action: 'sbi_feed_saver_manager_clear_comments_cache',
			};
			self.ajaxPost(clearCommentCacheData, function (_ref) {
				let data = _ref.data;
				if (data === 'success') {
					self.processNotification("commentCacheCleared");
				} else {
					self.processNotification("unkownError");
				}
			});
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
				case "deleteSourceCustomizer":
					self.sourceToDelete = args;
					heading = heading.replace("#", self.sourceToDelete.username);
					break;
				case "deleteSingleFeed":
					self.feedToDelete = args;
					heading = heading.replace("#", self.feedToDelete.feed_name);
					break;
			}
			self.dialogBox = {
				active: true,
				type: type,
				heading: heading,
				description: description,
				customButtons: customButtons
			};
			window.event.stopPropagation();
		},

		/**
		 * Confirm Dialog Box Actions
		 *
		 * @since 4.0
		 */
		confirmDialogAction: function () {
			let self = this;
			switch (self.dialogBox.type) {
				case 'deleteSourceCustomizer':
					self.selectSourceCustomizer(self.sourceToDelete, true);
					self.customizerControlAjaxAction('feedFlyPreview');
					break;
				case 'deleteSingleFeed':
					self.feedActionDelete([self.feedToDelete.id]);
					break;
				case 'deleteMultipleFeeds':
					self.feedActionDelete(self.feedsSelected);
					break;
				case 'backAllToFeed':
					//Save & Exist;
					self.saveFeedSettings(true);
					break;
				case 'unsavedFeedSources':
					self.updateFeedTypeAndSourcesCustomizer();
					break;
			}
		},

		/*
		closeConfirmDialog : function(){
			this.sourceToDelete = {};
			this.feedToDelete = {};
			this.dialogBox = {
				active : false,
				type : null,
				heading : null,
				description : null
			};
		},
		*/

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
				setTimeout(function () {
					if (self.tooltip.hoverType != 'inside') {
						self.tooltip.hover = false;
					}
				}, 200)
			}
		},

		/**
		 * Hover Tooltip
		 *
		 * @since 4.0
		 */
		hoverTooltip: function (type, hoverType) {
			this.tooltip.hover = type;
			this.tooltip.hoverType = hoverType;
		},

		/**
		 * Loading Bar & Notification
		 *
		 * @since 4.0
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
		 * Return Account Avatar
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
		 * Returns the caption for posts
		 *
		 * @since 6.0
		 *
		 * @return string
		 */
		getPostCaption: function (caption, postID) {
			let self = this,
				customizerSettings = self.customizerFeedData.settings;
			caption = caption.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/&lt;br&gt;|&lt;br \/&gt;/g, '<br>');

			let captionLength = self.checkNotEmpty(customizerSettings.captionlength) ? parseInt(customizerSettings.captionlength) : 50;

			return '<span class="sbi_caption" data-text-limit="' + captionLength + '" data-cap-length="' + caption.length + '">' + caption + '</span>' +
				(caption.length > parseInt(captionLength) ? '<span class="sbi_expand" style="display:inline-block;" onclick="sbiBuilderToggleCaption(' + postID + ')"> <a><span class="sbi_more">...</span></a></span>' : '');
		},

		/**
		 * Returns the caption for posts on hover
		 *
		 * @since 6.0
		 *
		 * @return string
		 */
		getPostHoverCaption: function (caption) {
			let self = this,
				customizerSettings = self.customizerFeedData.settings;
			caption = caption.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/&lt;br&gt;|&lt;br \/&gt;/g, '<br>');

			let captionLength = self.checkNotEmpty(customizerSettings.captionlength) ? parseInt(customizerSettings.captionlength) : 50;
			let hoverCaptionLength = self.checkNotEmpty(customizerSettings.hovercaptionlength) ? parseInt(customizerSettings.hovercaptionlength) : captionLength;

			return '<span class="sbi_caption" data-text-limit="' + hoverCaptionLength + '" data-cap-length="' + caption.length + '">' + caption + '</span>';
		},

		/**
		 * Check if Post Is a Shoppable Post
		 *
		 * @since 6.0
		 *
		 * @return boolean
		 */
		checkPostShoppableFeed: function (postId) {
			let self = this,
				customizerSettings = self.customizerFeedData.settings;
			return typeof self.customizerFeedData.settings.shoppablelist === 'object' && customizerSettings.shoppablelist[postId] !== undefined;
		},

		/**
		 * Open Shoppable Control for adding new Post
		 *
		 * @since 6.0
		 */
		openPostShoppableFeed: function (postId, media, caption = '') {
			let self = this,
				customizerSettings = self.customizerFeedData.settings,
				shoppableBtn = window.event.currentTarget;
			self.shoppableFeed.postId = postId;
			self.shoppableFeed.postShoppableUrl = (typeof self.customizerFeedData.settings.shoppablelist === 'object' && customizerSettings.shoppablelist[postId] !== undefined) ? customizerSettings.shoppablelist[postId] : '';
			self.shoppableFeed.postCaption = caption.slice(0, 80);
			self.shoppableFeed.postCaption = caption.length > 80 ? self.shoppableFeed.postCaption.trim() + '...' : self.shoppableFeed.postCaption;

			let currentPostDOM = shoppableBtn.parentNode.parentNode;

			if (currentPostDOM != undefined && currentPostDOM != null) {
				let sbiPhoto = currentPostDOM.querySelector('.sbi_photo');
				self.shoppableFeed.postMedia = sbiPhoto.getAttribute('data-full-res');
				self.shoppableFeed.postId = currentPostDOM.closest('.sbi_item')?.id.replace('sbi_', '');
			} else {
				self.shoppableFeed.postMedia = self.pluginURL + 'img/thumb-placeholder.png';
			}

		},

		/**
		 * Save Post Shoppable Feed
		 *
		 * @since 6.0
		 */
		addPostShoppableFeed: function () {
			let self = this,
				customizerSettings = self.customizerFeedData.settings;
			if (self.checkNotEmpty(self.shoppableFeed.postShoppableUrl)) {
				self.customizerFeedData.settings.shoppablelist = (typeof self.customizerFeedData.settings.shoppablelist === 'object') ? self.customizerFeedData.settings.shoppablelist : {};
				self.customizerFeedData.settings.shoppablelist[self.shoppableFeed.postId] = self.shoppableFeed.postShoppableUrl;
				self.shoppableFeed = {
					postId: null,
					postMedia: null,
					postCaption: null,
					postShoppableUrl: ''
				};
			} else {
				delete self.customizerFeedData.settings.shoppablelist[self.shoppableFeed.postId];
			}
		},

		/**
		 * Cancel Post Shoppable Feed
		 *
		 * @since 6.0
		 */
		cancelPostShoppableFeed: function () {
			let self = this;
			if (!self.checkNotEmpty(self.shoppableFeed.postShoppableUrl)) {
				delete self.customizerFeedData.settings.shoppablelist[self.shoppableFeed.postId];
			}

			self.shoppableFeed = {
				postId: null,
				postMedia: null,
				postCaption: null,
				postShoppableUrl: ''
			};
		},

		/**
		 * Open Moderation Mode
		 *
		 * @since 6.0
		 */
		openModerationMode: function () {
			let self = this;
			Object.assign(self.moderationSettings, self.customizerFeedData.settings.moderationlist);
			self.customBlockModerationlistTemp = `${self.customizerFeedData.settings.customBlockModerationlist}`;
			self.activateView('moderationMode');
		},

		/**
		 * Switch Moderation List Type
		 *
		 * @since 6.0
		 */
		switchModerationListType: function (moderationlistType) {
			let self = this;
			self.moderationSettings.list_type_selected = moderationlistType;
		},

		/**
		 * Switch Moderation List Type
		 *
		 * @since 6.0
		 */
		saveModerationSettings: function () {
			let self = this;
			Object.assign(self.customizerFeedData.settings.moderationlist, self.moderationSettings);
			self.customizerFeedData.settings.customBlockModerationlist = `${self.customBlockModerationlistTemp}`;
			self.activateView('moderationMode');
		},

		/**
		 * Check Post in Moderation Mode
		 *
		 * @since 6.0
		 */
		checkPostModertationMode: function (postID) {
			let self = this;
			if (self.moderationSettings.list_type_selected == "allow") {
				if (self.moderationSettings.allow_list.includes(postID) || self.moderationSettings.allow_list.includes(postID.toString())) {
					return 'active';
				} else {
					return 'inactive';
				}
			}
			if (self.moderationSettings.list_type_selected == "block") {
				let combinedBlockedList = Array.from(self.moderationSettings.block_list.concat(self.customBlockModerationlistTemp.split(',')));
				if (combinedBlockedList.includes(postID) || combinedBlockedList.includes(postID.toString())) {
					return 'inactive';
				} else {
					return 'active';
				}
			}
		},

		checkPostModertationModeAttribute: function (postID) {
			return '';
		},


		/**
		 * Add Post To Moderation List
		 * Depending on
		 *
		 * @since 6.0
		 */
		addPostToModerationList: function (postID) {
			let self = this;
			if (self.moderationSettings.list_type_selected == "allow") {
				if (self.moderationSettings.allow_list.includes(postID)) {
					self.moderationSettings.allow_list.push(postID);
					self.moderationSettings.allow_list.splice(self.moderationSettings.allow_list.indexOf(postID), 1);
					self.moderationSettings.allow_list.splice(self.moderationSettings.allow_list.indexOf(postID.toString()), 1);
				} else {
					self.moderationSettings.allow_list.push(postID);
				}
			}

			if (self.moderationSettings.list_type_selected == "block") {
				if (self.moderationSettings.block_list.includes(postID)) {
					self.moderationSettings.block_list.push(postID);
					self.moderationSettings.block_list.splice(self.moderationSettings.block_list.indexOf(postID), 1);
					self.moderationSettings.block_list.splice(self.moderationSettings.block_list.indexOf(postID.toString()), 1);
				} else {
					self.moderationSettings.block_list.push(postID);
				}
			}

		},


		/**
		 * Choose Hashtag Order By
		 *
		 * @since 6.0
		 */
		selectedHastagOrderBy: function (orderBy) {
			if (this.customizerFeedData != undefined) {
				this.customizerFeedData.settings.order = orderBy;
			} else {
				this.hashtagOrderBy = orderBy;
			}
		},

		//Dummy Lightbox Elements
		/**
		 * Print Element Overlay
		 *
		 * @since 4.0
		 */
		hideLightBox: function () {
			let self = this,
				domBody = document.getElementsByTagName("body")[0];
			domBody.classList.remove("no-overflow");
			self.dummyLightBoxData.visibility = 'hidden';
			sbiBuilder.$forceUpdate();
		},

		alterLightboxBehavior: function () {
			let self = this;
			jQuery('body').find('.sbi_link:not(.sbi_disable_lightbox) .sbi_link_area').on('click', function (event) {
				event.preventDefault();
				event.stopPropagation();
				let customize_lightbox = self.customizerSidebarBuilder['customize'].sections.customize_lightbox;
				self.customizerScreens.activeSection = 'customize_lightbox';
				self.customizerScreens.activeSectionData = self.customizerSidebarBuilder['customize'].sections.customize_lightbox;
				self.switchCustomizerSection('customize_lightbox', customize_lightbox, false, false);
				sbiBuilder.$forceUpdate();
			})
		},

		/**
		 * Check Personal Account Info
		 *
		 * @since 6.1
		 */
		checkPeronalAccount: function () {
			let self = this;
			if (self.selectedSources.length > 0) {
				let sourceInfo = self.sourcesList.filter(function (source) {
					return source.account_id == self.selectedSources[0];
				});
				sourceInfo = sourceInfo[0] ? sourceInfo[0] : [];
				if (sourceInfo?.header_data?.account_type &&
					sourceInfo?.header_data?.account_type.toLowerCase() === 'personal' &&
					self.checkSinglePersonalData(sourceInfo?.header_data?.biography) &&
					self.checkSinglePersonalData(sourceInfo?.local_avatar)
				) {
					self.$refs.personalAccountRef.personalAccountInfo.id = sourceInfo.account_id;
					self.$refs.personalAccountRef.personalAccountInfo.username = sourceInfo.username;
					return false
				}
			}
			return true;
		},

		checkPersonalAccount: function () {
			let self = this;
			if (self.selectedSources.length > 0) {
				let sourceInfo = self.sourcesList.filter(function (source) {
					return source.account_id == self.selectedSources[0];
				});
				sourceInfo = sourceInfo[0] ? sourceInfo[0] : [];
				if (sourceInfo?.header_data?.account_type &&
					sourceInfo?.header_data?.account_type.toLowerCase() === 'personal') {
					return true;
				} else {
					return false;
				}
			}
			return false;
		},

		checkSinglePersonalData: function (data) {
			return data === false || data === undefined || (data !== undefined && data !== false && !this.checkNotEmpty(data));
		},

		/**
		 * Cancel Personal Account
		 *
		 * @since 6.1
		 */
		cancelPersonalAccountUpdate: function () {
			let self = this;
			if (self.shouldDisableProFeatures || !self.hasFeature('feed_templates')) {
				self.submitNewFeed();
			} else {
				self.switchScreen('selectedFeedSection', 'selectTemplate');
			}
		},

		/**
		 * Triggered When updating Personal Account info
		 *
		 * @since 6.1
		 */
		successPersonalAccountUpdate: function () {
			let self = this;
			self.processNotification('personalAccountUpdated');
			this.creationProcessNext();
		},

		themePreview: function (themeType) {
			this.previewTheme = ''
			setTimeout(() => {
				this.previewTheme = themeType
			}, 250)
		},
		themePreviewClear: function () {
			setTimeout(() => {
				this.previewTheme = ''
			}, 250)
		}

	}

});

function sbiBuilderToggleCaption(postID) {
	if (sbiBuilder.expandedCaptions.includes(postID)) {
		sbiBuilder.expandedCaptions.splice(sbiBuilder.expandedCaptions.indexOf(postID), 1);
	} else {
		sbiBuilder.expandedCaptions.push(postID);
	}
}


jQuery(document).ready(function () {
	jQuery('body').find('#sbi_load .sbi_load_btn').unbind('click')
})
