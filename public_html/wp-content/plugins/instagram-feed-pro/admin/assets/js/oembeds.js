let sbioembeds_data = {
	nonce: sbi_oembeds.nonce,
	ajax_handler: sbi_oembeds.ajax_handler,
	genericText: sbi_oembeds.genericText,
	images: sbi_oembeds.images,
	modal: sbi_oembeds.modal,
	links: sbi_oembeds.links,
	supportPageUrl: sbi_oembeds.supportPageUrl,
	socialWallActivated: sbi_oembeds.socialWallActivated,
	socialWallLinks: sbi_oembeds.socialWallLinks,
	stickyWidget: false,
	facebook: sbi_oembeds.facebook,
	instagram: sbi_oembeds.instagram,
	connectionURL: sbi_oembeds.connectionURL,
	isFacebookActivated: sbi_oembeds.facebook.active,
	facebookInstallBtnText: null,
	fboEmbedLoader: false,
	instaoEmbedLoader: false,
	openFacebookInstaller: false,
	installerStatus: null,
	sbiLicenseNoticeActive: (sbi_oembeds.sbiLicenseNoticeActive === '1'),
	sbiLicenseInactiveState: (sbi_oembeds.sbiLicenseInactiveState === '1'),
	licenseBtnClicked: false,
	svgIcons: sbi_svgs,
	recheckLicenseStatus: null,
	licenseKey: sbi_oembeds.licenseKey,
	viewsActive: {
		whyRenewLicense: false,
		licenseLearnMore: false,
	},
	notificationElement: {
		type: 'success', // success, error, warning, message
		text: '',
		shown: null
	},
}

let sbioEmbeds = new Vue({
	el: "#sbi-oembeds",
	http: {
		emulateJSON: true,
		emulateHTTP: true
	},
	data: sbioembeds_data,
	methods: {
		recheckLicense: function (optionName = null) {
			let self = this;
			let licenseNoticeWrapper = document.querySelector('.sb-license-notice');
			self.recheckLicenseStatus = 'loading';
			let data = new FormData();
			data.append('action', 'sbi_recheck_connection');
			data.append('license_key', self.licenseKey);
			data.append('option_name', optionName);
			data.append('nonce', this.nonce);
			fetch(this.ajax_handler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					console.log(data);
					if (data.success == true) {
						if (data.data.license == 'valid') {
							this.recheckLicenseStatus = 'success';
						}
						if (data.data.license != 'valid') {
							this.recheckLicenseStatus = 'error';
						}

						setTimeout(function () {
							this.recheckLicenseStatus = null;
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

		openFacebookllModal: function () {
			this.openFacebookInstaller = true
		},
		closeModal: function () {
			this.openFacebookInstaller = false
		},
		isoEmbedsEnabled: function () {
			if (this.facebook.doingOembeds && this.instagram.doingOembeds) {
				return true;
			}

		},
		FacebookShouldInstallOrEnable: function () {
			// if the plugin is activated and installed then just enable oEmbed
			if (this.isFacebookActivated) {
				this.enableFacebookOembed();
				return;
			}
			// if the plugin is not activated and installed then open the modal to install and activate the plugin
			if (!this.isFacebookActivated) {
				this.openFacebookllModal();

			}
		},
		installFacebook: function () {
			this.installerStatus = 'loading';
			let data = new FormData();
			data.append('action', sbi_oembeds.facebook.installer.action);
			data.append('nonce', sbi_oembeds.nonce);
			data.append('plugin', sbi_oembeds.facebook.installer.plugin);
			data.append('type', 'plugin');
			data.append('referrer', sbi_oembeds.facebook.installer.referrer);

			fetch(sbi_oembeds.ajax_handler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					if (data.success == false) {
						this.installerStatus = 'error'
					}
					if (data.success == true) {
						this.isFacebookActivated = true;
						this.installerStatus = 'success'
					}
					if (typeof data.data === 'object') {
						this.facebookInstallBtnText = data.data.msg;
					} else {
						this.facebookInstallBtnText = data.data;
					}
					setTimeout(function () {
						this.installerStatus = null;
					}.bind(this), 3000);

				});
		},
		enableInstaoEmbed: function () {
			this.instaoEmbedLoader = true;

			let oembedConnectUrl = this.connectionURL.connect,
				appendURL = this.connectionURL.stateURL;

			const urlParams = {
				'sbi_con': this.connectionURL.sbi_con,
				'state': "{'{url=" + appendURL + "}'}"
			}

			let form = document.createElement('form');
			form.setAttribute('method', 'post');
			form.setAttribute('action', oembedConnectUrl);

			for (const key in urlParams) {
				let hiddenField = document.createElement('input');
				hiddenField.setAttribute('type', 'hidden');
				hiddenField.setAttribute('name', key);
				hiddenField.setAttribute('value', urlParams[key]);
				form.appendChild(hiddenField);
			}

			document.body.appendChild(form);
			form.submit();
		},
		enableFacebookOembed: function () {
			this.fboEmbedLoader = true;
			let oembedConnectUrl = this.connectionURL.connect,
				appendURL = this.connectionURL.stateURL;

			const urlParams = {
				'sbi_con': this.connectionURL.sbi_con,
				'state': "{'{url=" + appendURL + "}'}"
			}

			let form = document.createElement('form');
			form.setAttribute('method', 'post');
			form.setAttribute('action', oembedConnectUrl);

			for (const key in urlParams) {
				let hiddenField = document.createElement('input');
				hiddenField.setAttribute('type', 'hidden');
				hiddenField.setAttribute('name', key);
				hiddenField.setAttribute('value', urlParams[key]);
				form.appendChild(hiddenField);
			}

			document.body.appendChild(form);
			form.submit();
		},
		disableFboEmbed: function () {
			this.fboEmbedLoader = true;
			let data = new FormData();
			data.append('action', 'disable_facebook_oembed_from_instagram');
			data.append('nonce', this.nonce);
			fetch(sbi_oembeds.ajax_handler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					if (data.success == true) {
						this.fboEmbedLoader = false;
						this.facebook.doingOembeds = false;
						// get the updated connection URL after disabling oEmbed
						this.connectionURL = data.data.connectionUrl;
					}

				});
		},
		disableInstaoEmbed: function () {
			this.instaoEmbedLoader = true;
			let data = new FormData();
			data.append('action', 'disable_instagram_oembed_from_instagram');
			data.append('nonce', this.nonce);
			fetch(sbi_oembeds.ajax_handler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					if (data.success == true) {
						this.instaoEmbedLoader = false;
						this.instagram.doingOembeds = false;
						// get the updated connection URL after disabling oEmbed
						this.connectionURL = data.data.connectionUrl;
					}

				});
		},
		installButtonText: function (buttonText = null) {
			if (buttonText) {
				return buttonText;
			} else if (this.facebook.installer.nextStep == 'free_install') {
				return this.modal.install;
			} else if (this.facebook.installer.nextStep == 'free_activate') {
				return this.modal.activate;
			}
		},
		installIcon: function () {
			if (this.isFacebookActivated) {
				return;
			}
			if (this.installerStatus == null) {
				return this.modal.plusIcon;
			} else if (this.installerStatus == 'loading') {
				return this.svgIcons['loaderSVG'];
			} else if (this.installerStatus == 'success') {
				return this.svgIcons['checkmarkSVG'];
			} else if (this.installerStatus == 'error') {
				return this.svgIcons['timesSVG'];
			}
		},

		/**
		 * Activate license key from license error post grace period header notice
		 *
		 * @since 6.2.0
		 */
		activateLicense: function () {
			let self = this;
			if (self.licenseKey == null) {
				return;
			}
			self.licenseBtnClicked = true;
			let data = new FormData();
			data.append('action', 'sbi_license_activation');
			data.append('nonce', sbi_admin.nonce);
			data.append('license_key', self.licenseKey);
			fetch(sbi_oembeds.ajax_handler, {
				method: "POST",
				credentials: 'same-origin',
				body: data
			})
				.then(response => response.json())
				.then(data => {
					self.licenseBtnClicked = false;
					if (data && data.success == false) {
						self.processNotification("licenseError");
						return;
					}
					if (data.success != false) {
						self.processNotification("licenseActivated");
					}
				});
		},
		/**
		 * Activate View
		 *
		 * @since 6.2.0
		 */
		activateView: function (viewName, sourcePopupType = 'creation', ajaxAction = false) {
			let self = this;
			self.viewsActive[viewName] = (self.viewsActive[viewName] == false) ? true : false;
		},
		/**
		 * Toggle Sticky Widget view
		 *
		 * @since 4.0
		 */
		toggleStickyWidget: function () {
			this.stickyWidget = !this.stickyWidget;
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
	},
	created() {
		// Display the "Install" button text on modal depending on condition
		if (this.facebook.installer.nextStep == 'free_install') {
			this.facebookInstallBtnText = this.modal.install;
		} else if (this.facebook.installer.nextStep == 'free_activate' || this.facebook.installer.nextStep == 'pro_activate') {
			this.facebookInstallBtnText = this.modal.activate;
		}
	}
})
