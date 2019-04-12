
window.common = {
	loading: {
		spinner: {},
		show(target = 'null') {
			let targetName = '';
			target.split(' ').forEach((part, index, array) => {
				targetName += (part || 'null').replace('#', 'id-').replace('.', 'class-');
				if (index < array.length - 1) {
					targetName += '-';
				}
			});
			let el = $('.common-loading-' + targetName);
			if(el.length) {
				el.remove();
			}

			$(target).append($('<div />', { class: 'common-loading-' + targetName }));

			el = $('.common-loading-' + targetName);
			el.append($('<div />', { class: 'blob-' + targetName }));
			el.fadeIn();

			if(this.spinner[targetName] && this.spinner[targetName] != null) {
				this.spinner[targetName].multiple = true
			} else {
				this.spinner[targetName] = {
					el: el,
					multiple: false
				}
			}
		},
		hide: function(target = 'null') {
			let targetName = '';
			target.split(' ').forEach((part, index, array) => {
				targetName += (part || 'null').replace('#', 'id-').replace('.', 'class-');
				if (index < array.length - 1) {
					targetName += '-';
				}
			});
			let el = $('.common-loading-' + targetName);
			if (!this.spinner[targetName]) {
				return;
			}
			if(!this.spinner[targetName].multiple) {
				let _this = this;
				setTimeout(function() {
					el.fadeOut();
					delete _this.spinner[targetName];
				}, 250)
			} else {
				this.spinner[targetName].multiple = false
			}
		},
		hideAll: function() {
			let _this = this;
			Object.keys(this.spinner).forEach((key) => {
				setTimeout(function() {
					_this.spinner[key].el.fadeOut();
					delete _this.spinner[key];
				}, 250)
			});
		}
	},
	twoDigitNumber: (number) => number < 10 ? '0' + number : number,
	makeUserCantGoBack() {
		history.pushState(null, null, location.href);
		window.onpopstate = function () {
			history.go(1);
		}
	},
	askUserWhenLeavePage() {
		if (!window.onbeforeunload) {
			// this text is not really important, modern browser will use its default message
			window.onbeforeunload = () => 'Are you sure you want to leave this page?';
		}
	},
	getDateString(date, separator = '/') {
		return this.twoDigitNumber(date.getDate()) + separator + this.twoDigitNumber(date.getMonth() + 1) + separator + date.getFullYear()
	},
	getTimeString(date, separator = ':', getSecond = false) {
		return this.twoDigitNumber(date.getHours()) + separator + this.twoDigitNumber(date.getMinutes()) + (getSecond ? separator + this.twoDigitNumber(date.getSeconds()) : '');
	},
	makeTextareaAutoHeight(selector) {
		let el = $(selector);
		for (let i = 0; i < el.length; i++) {
			$(el.get(i)).on('input', () => {
				$(el.get(i)).css('height', '1px');
				$(el.get(i)).css('height', el.get(i).scrollHeight + 'px');
			});
		}
	},
	temporaryData(key, data, remove = false) {
		if (!key) {
			return null;
		}
		if (typeof sessionStorage === "undefined") {
			return this.cookie(key, data, remove);
		}

		return this.sessionStorage(key, data, remove);
	},
	sessionStorage(key, data, remove = false) {
		if (remove) {
			sessionStorage.removeItem(key);
			return;
		}
		if (typeof data !== "undefined") {
			sessionStorage.setItem(key, JSON.stringify(data));
		} else {
			let sessionData = sessionStorage.getItem(key);
			if (sessionData) {
				return JSON.parse(sessionData);
			}
			return null;
		}
	},
	cookie(key, data, remove = false) {
		if (remove) {
			Cookies.remove(key);
			return;
		}
		if (typeof data !== "undefined") {
			Cookies.set(key, data, {path: '/'});
		} else {
			return Cookies.get(key);
		}
	},
	alert(title, message, callback = () => {}) {
		if (arguments.length === 1) {
			return bootbox.alert(title || 'No message');
		}
		if ($.isFunction(message)) {
			return bootbox.alert(title || 'No message', message);
		}

		bootbox.alert({
			title: title,
			message: message || 'No message',
			callback: callback,
		});
	},
	confirm(title, message, callback = () => {}) {
		if (arguments.length === 1) {
			return bootbox.confirm(title || 'No message', callback);
		}
		if ($.isFunction(message)) {
			return bootbox.confirm(title || 'No message', message);
		}

		bootbox.confirm({
			title: title,
			message: message || 'No message',
			callback: callback,
		});
	},
	standardizedVietnamPhoneFormat(number) {
		let allowSpecialCharacterRegex = /[()\-\s]/g;
		return number.replace(allowSpecialCharacterRegex, '').replace(/\+84/g, '0');
	},
	validateVietnamPhone(number) {
		number = this.standardizedVietnamPhoneFormat(number);
		let numberRegex = /(\+84|0)([35789]\d{8}|1\d{9})$/g;
		return numberRegex.test(number);
	},
	validateString(str) {
		let lengthRegex = /.{1,}/g;
		let specialCharacterRegex = /[$&+,:;=?@#|<>.\-^*()%!]+/g;
		return !specialCharacterRegex.test(str) && lengthRegex.test(str);
	},
	validateEmail(email) {
		let emailRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return emailRegex.test(email);
	},
	openElementInFullscreen(selector) {
		let el = $(selector).get(0);
		if (!el) {
			return false;
		}
		if (el.requestFullscreen) {
			el.requestFullscreen();
			return true;
		}
		if (el.mozRequestFullScreen) { /* Firefox */
			el.mozRequestFullScreen();
			return true;
		}
		if (el.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
			el.webkitRequestFullscreen();
			return true;
		}
		if (elem.msRequestFullscreen) { /* IE/Edge */
			el.msRequestFullscreen();
			return true;
		}

		return false;
	},
	closeFullScreen() {
		if (document.exitFullscreen) {
			document.exitFullscreen();
			return true;
		}
		if (document.mozCancelFullScreen) { /* Firefox */
			document.mozCancelFullScreen();
			return true;
		}
		if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
			document.webkitExitFullscreen();
			return true;
		}
		if (document.msExitFullscreen) { /* IE/Edge */
			document.msExitFullscreen();
			return true;
		}

		return false;
	},
	checkFirebaseReady() {
		return new Promise((resolve) => {
			let firebaseReady = () => {
				if (firebase && firebase.apps && firebase.apps.length) {
					resolve();
				} else {
					setTimeout(firebaseReady, 1000);
				}
			};
			firebaseReady();
		});
    },

    countMessageNotSeen() {
        let count_message = new Promise((resolve) => {
            var id_admin = $("#id_admin_all").data('id_admin');
            firebase.database().ref(`members`)
                .on('value', (data) => {
                let childData = data.val();
                let count = 0;
                Object.keys(childData).map((objectKey, index) => {
                    var value = childData[objectKey];
                    if (value[`u${id_admin}`] != undefined) {
                        count = count + value[`u${id_admin}`].number_of_unseen_messages;
                    }
                });
                if (count > 0) {
                    $('#nb_ms_not_seen').addClass('nb_ms_not_seen');
                    $('#nb_ms_not_seen').text(count);
                } else {
                    $('#nb_ms_not_seen').removeClass('nb_ms_not_seen');
                    $('#nb_ms_not_seen').text("");
                }
                resolve();
            });
        });
    },
    notifyAllPage() {
        var id_admin = $("#id_admin_all").data('id_admin');
        firebase.database().ref(`chats_by_user/u${id_admin}/_all_conversation`)
            .on('child_changed', (data) => {
                if (data.key == 'from_user_id' && data.val() != id_admin) {
                    $.notify("Bạn có tin nhắn mới!", "info");
                }
            });
    }
};