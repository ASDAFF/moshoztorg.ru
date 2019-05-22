(function() {

if (BX.IM)
	return;

BX.IM = function(domNode, params)
{
	BX.browser.addGlobalClass();
	if(typeof(BX.message("USER_TZ_AUTO")) == 'undefined' || BX.message("USER_TZ_AUTO") == 'Y')
		BX.message({"USER_TZ_OFFSET": -(new Date).getTimezoneOffset()*60-parseInt(BX.message("SERVER_TZ_OFFSET"))});

	this.revision = 32; // api revieion - check include.php
	this.errorMessage = '';
	this.animationSupport = true;
	this.bitrixNetworkStatus = params.bitrixNetworkStatus;
	this.bitrix24Status = params.bitrix24Status;
	this.bitrix24Admin = params.bitrix24Admin;
	this.bitrixIntranet = params.bitrixIntranet;
	this.bitrix24net = params.bitrix24net;
	this.bitrixXmpp = params.bitrixXmpp;
	this.ppStatus = params.ppStatus;
	this.ppServerStatus = this.ppStatus? params.ppServerStatus: false;
	this.updateStateInterval = params.updateStateInterval;
	this.desktopStatus = params.desktopStatus || false;
	this.desktopVersion = params.desktopVersion;
	this.xmppStatus = params.xmppStatus;
	this.lastRecordId = 0;
	this.userId = params.userId;
	this.userEmail = params.userEmail;
	this.userParams = params.users && params.users[this.userId]? params.users[this.userId]: {};
	this.path = params.path;
	this.language = params.language || 'en';
	this.init = typeof(params.init) != 'undefined'? params.init: true;
	this.windowFocus = true;
	this.windowFocusTimeout = null;
	this.extraBind = null;
	this.extraOpen = false;
	this.dialogOpen = false;
	this.notifyOpen = false;
	this.adjustSizeTimeout = null;
	this.tryConnect = true;
	this.openSettingsFlag =  typeof(params.openSettings) != 'undefined'? params.openSettings: false;
	this.popupConfirm = null;

	this.settings = params.settings;
	this.settingsView = params.settingsView || {common:{}, notify:{}, privacy:{}};
	this.settingsNotifyBlocked = params.settingsNotifyBlocked || {};
	this.settingsTableConfig = {};
	this.settingsSaveCallback = {};
	this.saveSettingsTimeout = {};
	this.popupSettings = null;

	this.audio = {};
	this.audio.reminder = null;
	this.audio.newMessage1 = null;
	this.audio.newMessage2 = null;
	this.audio.send = null;
	this.audio.dialtone = null;
	this.audio.ringtone = null;
	this.audio.start = null;
	this.audio.stop = null;
	this.audio.current = null;
	this.audio.timeout = {};

	this.mailCount = params.mailCount;
	this.notifyCount = params.notifyCount || 0;
	this.messageCount = params.messageCount || 0;

	this.quirksMode = (BX.browser.IsIE() && !BX.browser.IsDoctype() && (/MSIE 8/.test(navigator.userAgent) || /MSIE 9/.test(navigator.userAgent)));
	this.platformName = BX.browser.IsMac()? 'OS X': (/windows/.test(navigator.userAgent.toLowerCase())? 'Windows': '');

	if (BX.browser.IsIE() && !BX.browser.IsIE9() && (/MSIE 7/i.test(navigator.userAgent)))
		this.errorMessage = BX.message('IM_M_OLD_BROWSER');

	this.desktop = new BX.IM.Desktop(this, {
		'desktop': params.desktop
	});

	this.webrtc = new BX.IM.WebRTC(this, {
		'desktopClass': this.desktop,
		'turnServer': params.webrtc && params.webrtc.turnServer || '',
		'turnServerFirefox': params.webrtc && params.webrtc.turnServerFirefox || '',
		'turnServerLogin': params.webrtc && params.webrtc.turnServerLogin || '',
		'turnServerPassword': params.webrtc && params.webrtc.turnServerPassword || '',
		'phoneEnabled': params.webrtc && params.webrtc.phoneEnabled || false,
		'phoneAvailable': params.webrtc && params.webrtc.phoneAvailable || 0,
		'phoneCrm': params.phoneCrm && params.phoneCrm || {},
		'panel': domNode != null? domNode: BX.create('div')
	});

	this.desktop.webrtc = this.webrtc;

	this.windowTitle = this.desktop.ready()? '': document.title;
	for (var i in params.notify)
	{
		params.notify[i].date = parseInt(params.notify[i].date)+parseInt(BX.message('USER_TZ_OFFSET'));
		if (parseInt(i) > this.lastRecordId)
			this.lastRecordId = parseInt(i);
	}
	for (var i in params.message)
	{
		params.message[i].date = parseInt(params.message[i].date)+parseInt(BX.message('USER_TZ_OFFSET'));
		if (parseInt(i) > this.lastRecordId)
			this.lastRecordId = parseInt(i);
	}
	for (var i in params.recent)
	{
		params.recent[i].date = parseInt(params.recent[i].date)+parseInt(BX.message('USER_TZ_OFFSET'));
	}
	if (BX.browser.SupportLocalStorage())
	{
		BX.addCustomEvent(window, "onLocalStorageSet", BX.proxy(this.storageSet, this));

		var lri = BX.localStorage.get('lri');
		if (parseInt(lri) > this.lastRecordId)
			this.lastRecordId = parseInt(lri);

		BX.garbage(function(){
			BX.localStorage.set('lri', this.lastRecordId, 60);
		}, this);
	}

	this.notifyManager = new BX.IM.NotifyManager(this, {});
	this.notify = new BX.Notify(this, {
		'desktopClass': this.desktop,
		'webrtcClass': this.webrtc,
		'domNode': domNode != null? domNode: BX.create('div'),
		'counters': params.counters || {},
		'mailCount': params.mailCount || 0,
		'notify': params.notify || {},
		'unreadNotify' : params.unreadNotify || {},
		'flashNotify' : params.flashNotify || {},
		'countNotify' : params.countNotify || 0,
		'loadNotify' : params.loadNotify
	});
	this.webrtc.notify = this.notify;
	this.desktop.notify = this.notify;

	if (this.init)
	{
		BX.addCustomEvent(window, "onImUpdateCounterNotify", BX.proxy(this.updateCounter, this));
		BX.addCustomEvent(window, "onImUpdateCounterMessage", BX.proxy(this.updateCounter, this));
		BX.addCustomEvent(window, "onImUpdateCounterMail", BX.proxy(this.updateCounter, this));
		BX.addCustomEvent(window, "onImUpdateCounter", BX.proxy(this.updateCounter, this));
	}

	this.messenger = new BX.Messenger(this, {
		'updateStateInterval': params.updateStateInterval,
		'notifyClass': this.notify,
		'webrtcClass': this.webrtc,
		'desktopClass': this.desktop,
		'recent': params.recent,
		'users': params.users || {},
		'groups': params.groups || {},
		'userInGroup': params.userInGroup || {},
		'woGroups': params.woGroups || {},
		'woUserInGroup': params.woUserInGroup || {},
		'currentTab' : params.currentTab || 0,
		'chat' : params.chat || {},
		'userInChat' : params.userInChat || {},
		'hrphoto' : params.hrphoto || {},
		'message' : params.message || {},
		'showMessage' : params.showMessage || {},
		'unreadMessage' : params.unreadMessage || {},
		'flashMessage' : params.flashMessage || {},
		'countMessage' : params.countMessage || 0,
		'smile' : params.smile || false,
		'smileSet' : params.smileSet || false,
		'history' : params.history || {},
		'openMessenger' : typeof(params.openMessenger) != 'undefined'? params.openMessenger: false,
		'openHistory' : typeof(params.openHistory) != 'undefined'? params.openHistory: false,
		'openNotify' : typeof(params.openNotify) != 'undefined'? params.openNotify: false
	});
	this.webrtc.messenger = this.messenger;
	this.notify.messenger = this.messenger;
	this.desktop.messenger = this.messenger;

	this.network = new BX.Network(this, {
		notifyClass: this.notify,
		messengerClass: this.messenger,
		desktopClass: this.desktop
	});

	if (this.init)
	{
		BX.bind(window, "blur", BX.delegate(function(){ this.changeFocus(false);}, this));
		BX.bind(window, "focus", this.setFocusFunction = BX.delegate(function(){
			if (this.windowFocus)
				return false;

			if (this.desktop.ready() && !BX.desktop.isActiveWindow())
				return false;

			this.changeFocus(true);
			if (this.isFocus() && this.messenger.unreadMessage[this.messenger.currentTab] && this.messenger.unreadMessage[this.messenger.currentTab].length>0)
				this.messenger.readMessage(this.messenger.currentTab);

			if (this.isFocus('notify'))
			{
				if (this.notify.unreadNotifyLoad)
					this.notify.loadNotify();
				else if (this.notify.notifyUpdateCount > 0)
					this.notify.viewNotifyAll();
			}
		}, this));

		if (this.desktop.ready())
			BX.bind(window, "click", this.setFocusFunction);

		BX.addCustomEvent("onPullEvent-xmpp", BX.delegate(function(command, params)
		{
			if (command == 'lastActivityDate')
			{
				this.xmppStatus = params.timestamp > 0;
			}
		}, this));
	}

	if (this.init)
	{
		this.updateCounter();
		BX.onCustomEvent(window, 'onImInit', [this]);
	}

	if (this.openSettingsFlag !== false)
		this.openSettings(this.openSettingsFlag == 'Y'? {}: {'onlyPanel': this.openSettingsFlag.toString().toLowerCase()});
};

BX.IM.prototype.isFocus = function(context)
{
	context = typeof(context) == 'undefined'? 'dialog': context;
	if (!this.desktop.run() && (this.messenger == null || this.messenger.popupMessenger == null))
		return false;

	if (context == 'dialog')
	{
		if (this.desktop.ready() && BX.desktop.getCurrentTab() != 'im' && BX.desktop.getCurrentTab() != 'im-phone')
			return false;
		if (this.messenger && !this.isScrollMax(this.messenger.popupMessengerBody, 200))
			return false;
		if (this.dialogOpen == false)
			return false;
	}
	else if (context == 'notify')
	{
		if (this.desktop.ready() && BX.desktop.getCurrentTab() != 'notify' && BX.desktop.getCurrentTab() != 'im-phone')
			return false;
		if (this.notifyOpen == false)
			return false;
	}

	if (this.quirksMode || (BX.browser.IsIE() && !BX.browser.IsIE9()))
		return true;

	return this.windowFocus;
};

BX.IM.prototype.changeFocus = function (focus)
{
	this.windowFocus = typeof(focus) == "boolean"? focus: false;

	return this.windowFocus;
};

BX.IM.prototype.isScrollMax = function(element, infelicity)
{
	if (!element) return true;
	infelicity = typeof(infelicity) == 'number'? infelicity: 0;
	return (element.scrollHeight - element.offsetHeight - infelicity <= element.scrollTop);
};

BX.IM.prototype.isScrollMin = function(element)
{
	if (!element) return false;
	return (0 == element.scrollTop);
};

BX.IM.prototype.enableScroll = function(element, max, scroll)
{
	if (!element)
		return false;

	scroll = scroll !== false;
	max = parseInt(max);

	return (scroll && this.isScrollMax(element, max));
};

BX.IM.prototype.playSound = function(sound)
{
	if (!this.init || this.webrtc.callActive)
		return false;

	var whiteList = {'stop': true, 'start': true, 'dialtone': true, 'ringtone': true};
	if (!this.settings.enableSound && !whiteList[sound])
		return false;

	BX.localStorage.set('mps', true, 1);

	try{
		this.stopSound();
		this.audio.current = this.audio[sound];
		this.audio[sound].play();
	}
	catch(e)
	{
		this.audio.current = null
	}

};

BX.IM.prototype.repeatSound = function(sound, time)
{
	BX.localStorage.set('mrs', {sound: sound, time: time}, 1);
	if (this.audio.timeout[sound])
		clearTimeout(this.audio.timeout[sound]);

	if (this.desktop.ready() || !this.desktopStatus)
		this.playSound(sound);

	this.audio.timeout[sound] = setTimeout(BX.delegate(function(){
		this.repeatSound(sound, time);
	}, this), time);
};

BX.IM.prototype.stopRepeatSound = function(sound, send)
{
	send = send != false;
	if (send)
		BX.localStorage.set('mrss', {sound: sound}, 1);

	if (this.audio.timeout[sound])
		clearTimeout(this.audio.timeout[sound]);

	if (!this.audio[sound])
		return false;

	this.audio[sound].pause();
	this.audio[sound].currentTime = 0;
};

BX.IM.prototype.stopSound = function()
{
	if (this.audio.current)
	{
		this.audio.current.pause();
		this.audio.current.currentTime = 0;
	}
};

BX.IM.prototype.autoHide = function(e)
{
	e = e||window.event;
	if (e.which == 1)
	{
		if (this.popupSettings != null)
			this.popupSettings.destroy();
		else if (!this.webrtc.callInit && this.messenger.popupMessenger != null)
			this.messenger.popupMessenger.destroy();

	}
};

BX.IM.prototype.updateCounter = function(count, type)
{
	if (type == 'MESSAGE')
		this.messageCount = count;
	else if (type == 'NOTIFY')
		this.notifyCount = count;
	else if (type == 'MAIL')
		this.mailCount = count;

	var sumCount = 0;
	if (this.notifyCount > 0)
		sumCount += parseInt(this.notifyCount);
	if (this.messageCount > 0)
		sumCount += parseInt(this.messageCount);

	if (this.desktop.run())
	{
		var sumLabel = '';
		if (sumCount > 99)
			sumLabel = '99+';
		else if (sumCount > 0)
			sumLabel = sumCount;

		var iconTitle = BX.message('IM_DESKTOP_UNREAD_EMPTY');
		if (this.notifyCount > 0 && this.messageCount > 0)
			iconTitle = BX.message('IM_DESKTOP_UNREAD_MESSAGES_NOTIFY');
		else if (this.notifyCount > 0)
			iconTitle = BX.message('IM_DESKTOP_UNREAD_NOTIFY');
		else if (this.messageCount > 0)
			iconTitle = BX.message('IM_DESKTOP_UNREAD_MESSAGES');
		else if (this.notify != null && this.notify.getCounter('**') > 0)
			iconTitle = BX.message('IM_DESKTOP_UNREAD_LF');

		BX.desktop.setIconTooltip(iconTitle);
		BX.desktop.setIconBadge(sumLabel, this.messageCount > 0);

		if (this.notify != null)
		{
			var lfCounter = this.notify.getCounter('**');
			BX.desktop.setTabBadge('im-lf', lfCounter);
		}
	}
	BX.onCustomEvent(window, 'onImUpdateSumCounters', [sumCount, 'SUM']);

	if (this.settings.status != 'dnd' && !this.desktopStatus && sumCount > 0)
	{
		if (!this.desktop.ready() && document.title != '('+sumCount+') '+this.windowTitle)
			document.title = '('+sumCount+') '+this.windowTitle;

		if (this.messageCount > 0)
			BX.addClass(this.notify.panelButtonMessage, 'bx-notifier-message-new');
		else
			BX.removeClass(this.notify.panelButtonMessage, 'bx-notifier-message-new');
	}
	else
	{
		if (!this.desktop.ready() && document.title != this.windowTitle)
			document.title = this.windowTitle;

		if (this.messageCount <= 0 || this.settings.status == 'dnd' || this.desktopStatus)
			BX.removeClass(this.notify.panelButtonMessage, 'bx-notifier-message-new');
	}
};

BX.IM.prototype.openNotify = function(params)
{
	setTimeout(BX.delegate(function(){
		this.notify.openNotify();
	}, this), 200);
};

BX.IM.prototype.closeNotify = function()
{
	BX.onCustomEvent(window, 'onImNotifyWindowClose', []);
	if (this.messenger.popupMessenger != null && !this.webrtc.callInit)
		this.messenger.popupMessenger.destroy();
};

BX.IM.prototype.toggleNotify = function()
{
	if (this.isOpenNotify())
		this.closeNotify();
	else
		this.openNotify();
};

BX.IM.prototype.isOpenNotify = function()
{
	return this.notifyOpen;
};

BX.IM.prototype.callTo = function(userId, video)
{
	video = !(typeof(video) != 'undefined' && !video);
	if (!this.desktop.ready() && this.desktopStatus && this.desktopVersion >= 18)
	{
		location.href = "bx://callto/"+(video? 'video': 'audio')+"/"+userId+(this.bitrix24net? '/bitrix24net/Y':'');
	}
	else
		this.webrtc.callInvite(userId, video);
};

BX.IM.prototype.phoneTo = function(number, params)
{
	params = params? params: {};
	if (!this.desktop.ready() && this.desktopStatus && this.desktopVersion >= 18)
	{
		var stringParams = '';
		if (params)
		{
			if (typeof(params) != 'object')
			{
				try { params = JSON.parse(params); } catch(e) { params = {} }
			}
			for (var i in params)
				stringParams = stringParams+'!!'+i+'!!'+params[i];
			stringParams = '/params/'+stringParams.substr(2);
		}
		if (this.webrtc.popupKeyPad)
			this.webrtc.popupKeyPad.close();

		location.href = "bx://callto/phone/"+escape(number)+stringParams+(this.bitrix24net? '/bitrix24net/Y':'');
	}
	else
	{
		if (typeof(params) != 'object')
		{
			try { params = JSON.parse(params); } catch(e) { params = {} }
		}
		setTimeout(BX.delegate(function(){
			this.webrtc.phoneCall(number, params);
		}, this), 200);
	}
	return true;
};

BX.IM.prototype.checkCallSupport = function()
{
	return this.webrtc.callSupport();
};

BX.IM.prototype.openMessenger = function(userId)
{
	setTimeout(BX.delegate(function(){
		this.messenger.openMessenger(userId);
	}, this), 200);
};

BX.IM.prototype.closeMessenger = function()
{
	if (this.messenger.popupMessenger != null && !this.webrtc.callInit)
		this.messenger.popupMessenger.destroy();
};

BX.IM.prototype.isOpenMessenger = function()
{
	return this.dialogOpen;
};

BX.IM.prototype.toggleMessenger = function()
{
	if (this.isOpenMessenger())
		this.closeMessenger();
	else if (this.extraOpen && !this.isOpenNotify())
		this.closeMessenger();
	else
		this.openMessenger(this.messenger.currentTab);
};

BX.IM.prototype.openHistory = function(userId)
{
	setTimeout(BX.proxy(function(){
		this.messenger.openHistory(userId);
	},this), 200);
};

BX.IM.prototype.openContactList = function()
{
	return false;
};

BX.IM.prototype.closeContactList = function()
{
	return false;
};

BX.IM.prototype.isOpenContactList = function()
{
	return false;
};

BX.IM.prototype.checkRevision = function(revision)
{
	revision = parseInt(revision);
	if (typeof(revision) == "number" && this.revision < revision)
	{
		if (this.desktop.run())
		{
			console.log('NOTICE: Window reload, becouse REVISION UP ('+this.revision+' -> '+revision+')');
			location.reload();
		}
		else
		{
			if (this.isOpenMessenger())
			{
				this.closeMessenger();
				this.openMessenger();
			}
			this.errorMessage = BX.message('IM_M_OLD_REVISION').replace('#WM_NAME#', this.bitrixIntranet? BX.message('IM_BC'): BX.message('IM_WM'));
		}
		return false;
	}
	return true;
};


BX.IM.prototype.openSettings = function(params)
{
	params = typeof(params) == 'object'? params: {};
	if (this.popupSettings != null)
		return false;

	if (this.messenger.popupMessenger != null && !this.desktop.run())
		this.messenger.popupMessenger.setClosingByEsc(false);

	this.settingsSaveCallback = {};
	this.settingsTableConfig = {};

	this.settingsView.common = {
		'title' : BX.message('IM_SETTINGS_COMMON'),
		'settings': [
			{'title': BX.message('IM_M_VIEW_OFFLINE_OFF'), 'type': 'checkbox', 'name':'viewOffline',  'checked': !this.settings.viewOffline, 'saveCallback': BX.delegate(function(element) { return !element.checked; }, this)},
			{'title': BX.message('IM_M_VIEW_GROUP_OFF'), 'type': 'checkbox', 'name':'viewGroup', 'checked': !this.settings.viewGroup, 'saveCallback': BX.delegate(function(element) { return !element.checked; }, this)},
			{'type': 'space'},
			{'title': BX.message('IM_M_LLM'), 'type': 'checkbox', 'name':'loadLastMessage', 'checked': this.settings.loadLastMessage},
			{'title': BX.message('IM_M_LLN'), 'type': 'checkbox', 'name':'loadLastNotify', 'checked': this.settings.loadLastNotify},
			{'type': 'space'},
			{'title': BX.message('IM_M_ENABLE_SOUND'), 'type': 'checkbox', 'name':'enableSound', 'checked': this.settings.enableSound},
			this.desktop.ready()? {'title': BX.message('IM_M_ENABLE_BIRTHDAY'), 'type': 'checkbox', 'checked': this.desktop.birthdayStatus(), 'callback': BX.delegate(function(){ this.desktop.birthdayStatus(!this.desktop.birthdayStatus()); }, this)}: null,
			{'title': BX.message('IM_M_KEY_SEND'), 'type': 'select', 'name':'sendByEnter', 'value': this.settings.sendByEnter?'Y':'N', items: [{title: (BX.browser.IsMac()? "&#8984;+Enter": "Ctrl+Enter"), value: 'N'}, {title: 'Enter', value: 'Y'}], 'saveCallback': BX.delegate(function(element) { return element[element.selectedIndex].value == 'Y'; }, this)},
			{'type': 'space'},
			this.desktop.ready()? {'title': BX.message('IM_M_DESKTOP_AUTORUN_ON'), 'type': 'checkbox', 'checked': BX.desktop.autorunStatus(), 'callback': BX.delegate(function(){ BX.desktop.autorunStatus(!BX.desktop.autorunStatus()); }, this)}: null
		]
	};
	this.settingsView.notify = {
		'title' : BX.message('IM_SETTINGS_NOTIFY'),
		'settings': [
			{'type': 'notifyControl'},
			{'type': 'table', name: 'notify', show: this.settings.notifyScheme == 'expert'},
			{'type': 'table', name: 'simpleNotify', show: this.settings.notifyScheme == 'simple'}
		]
	};

	this.settingsTableConfig['notify'] = {
		'condition': BX.delegate(function(){ return this.settingsTableConfig['notify'].rows.length > 0 }, this),
		'headers' : ['', BX.message('IM_SETTINGS_NOTIFY_SITE'), this.bitrixXmpp? BX.message('IM_SETTINGS_NOTIFY_XMPP'): false, BX.message('IM_SETTINGS_NOTIFY_EMAIL')],
		'rows' : [],
		'error_rows': BX.create("div", {props: {className: " bx-messenger-content-item-progress bx-messenger-content-item-progress-with-text"}, html: BX.message('IM_SETTINGS_LOAD')})
	};

	this.settingsTableConfig['simpleNotify'] = {
		'condition': BX.delegate(function(){  return this.settingsTableConfig['simpleNotify'].rows.length > 0 }, this),
		'headers' : [BX.message('IM_SETTINGS_SNOTIFY'), ''],
		'rows' : []
	};

	this.settingsView.privacy = {
		'title' : BX.message('IM_SETTINGS_PRIVACY'),
		'condition': BX.delegate(function(){ return !this.bitrixIntranet}, this),
		'settings': [
			{'title': BX.message('IM_SETTINGS_PRIVACY_MESS'), name: 'privacyMessage', 'type': 'select', items: [{title: BX.message('IM_SETTINGS_SELECT_1'), value: 'all'}, {title: BX.message('IM_SETTINGS_SELECT_2'), value: 'contact'}], 'value': this.settings.privacyMessage},
			{'title': BX.message('IM_SETTINGS_PRIVACY_CALL'), name: 'privacyCall', 'type': 'select', items: [{title: BX.message('IM_SETTINGS_SELECT_1'), value: 'all'}, {title: BX.message('IM_SETTINGS_SELECT_2'), value: 'contact'}], 'value': this.settings.privacyCall},
			{'title': BX.message('IM_SETTINGS_PRIVACY_CHAT'), name: 'privacyChat', 'type': 'select', items: [{title: BX.message('IM_SETTINGS_SELECT_1_2'), value: 'all'}, {title: BX.message('IM_SETTINGS_SELECT_2_2'), value: 'contact'}], 'value': this.settings.privacyChat},
			{'title': BX.message('IM_SETTINGS_PRIVACY_SEARCH'), name: 'privacySearch', 'type': 'select', items: [{title: BX.message('IM_SETTINGS_SELECT_1_3'), value: 'all'}, {title: BX.message('IM_SETTINGS_SELECT_2_3'), value: 'contact'}], 'value': this.settings.privacySearch},
			this.bitrix24net? {'title': BX.message('IM_SETTINGS_PRIVACY_PROFILE'), name: 'privacyProfile', 'type': 'select', items: [{title: BX.message('IM_SETTINGS_SELECT_1_3'), value: 'all'}, {title: BX.message('IM_SETTINGS_SELECT_2_3'), value: 'contact'}, {title: BX.message('IM_SETTINGS_SELECT_3_3'), value: 'nobody'}], 'value': this.settings.privacyProfile}: null
		]
	};

	BX.onCustomEvent(this, "prepareSettingsView", []);

	if (params.onlyPanel && !this.settingsView[params.onlyPanel])
		return false;

	this.popupSettingsButtonSave = new BX.PopupWindowButton({
		text : BX.message('IM_SETTINGS_SAVE'),
		className : "popup-window-button-accept",
		events : { click : BX.delegate(function() {
			this.popupSettingsButtonSave.setClassName('popup-window-button');
			this.popupSettingsButtonSave.setName(BX.message('IM_SETTINGS_WAIT'));
			BX.hide(this.popupSettingsButtonClose.buttonNode);
			this.saveFormSettings();
		}, this) }
	});
	this.popupSettingsButtonClose = new BX.PopupWindowButton({
		text : BX.message('IM_SETTINGS_CLOSE'),
		className : "popup-window-button-close",
		events : { click : BX.delegate(function() { this.popupSettings.close(); BX.hide(this.popupSettingsButtonSave.buttonNode); BX.hide(this.popupSettingsButtonClose.buttonNode); }, this) }
	});
	this.popupSettingsBody = BX.create("div", { props : { className : "bx-messenger-settings" }, children: this.prepareSettings({onlyPanel: params.onlyPanel? params.onlyPanel: false, active: params.active? params.active: false})});

	if (this.desktop.ready())
	{
		if (this.init)
		{
			this.desktop.openSettings(this.popupSettingsBody, "BXIM.openSettings("+JSON.stringify(params)+"); BX.desktop.resize(); ", params);
			return false;
		}
		else
		{
			this.popupSettings = new BX.PopupWindowDesktop();
			this.desktop.drawOnPlaceholder(this.popupSettingsBody);
		}
	}
	else
	{
		this.popupSettings = new BX.PopupWindow('bx-messenger-popup-settings', null, {
			lightShadow : true,
			autoHide: false,
			zIndex: 200,
			overlay: {opacity: 50, backgroundColor: "#000000"},
			buttons: [this.popupSettingsButtonSave, this.popupSettingsButtonClose],
			draggable: {restrict: true},
			closeByEsc: true,
			events : {
				onPopupClose : function() { this.destroy(); },
				onPopupDestroy : BX.delegate(function() {
					this.popupSettings = null;
					if (!this.desktop.run() && this.messenger.popupMesseger == null)
						BX.bind(document, "click", BX.proxy(this.autoHide, this));

					if (this.messenger.popupMessenger != null && !this.webrtc.callInit)
					{
						this.messenger.popupMessenger.setClosingByEsc(true)
					}

				}, this)
			},
			titleBar: {content: BX.create('span', {props : { className : "bx-messenger-title" }, html: params.onlyPanel? this.settingsView[params.onlyPanel].title: BX.message('IM_SETTINGS')})},
			closeIcon : {'top': '10px', 'right': '13px'},
			content : this.popupSettingsBody
		});
		this.popupSettings.show();
		BX.bind(this.popupSettings.popupContainer, "click", BX.IM.preventDefault);
	}

	BX.bindDelegate(this.popupSettingsBody, 'click', {className: 'bx-messenger-settings-tab'}, BX.delegate(function() {
		var elements = BX.findChildren(BX.proxy_context.parentNode, {className : "bx-messenger-settings-tab"}, false);
		for (var i = 0; i < elements.length; i++)
			BX.removeClass(elements[i], 'bx-messenger-settings-tab-active');
		BX.addClass(BX.proxy_context, 'bx-messenger-settings-tab-active');

		var elements = BX.findChildren(BX.proxy_context.parentNode.nextSibling, {className : "bx-messenger-settings-content"}, false);
		for (var i = 0; i < elements.length; i++)
		{
			if (parseInt(BX.proxy_context.getAttribute('data-id')) == i)
				BX.addClass(elements[i], 'bx-messenger-settings-content-active');
			else
				BX.removeClass(elements[i], 'bx-messenger-settings-content-active');
		}
		if (this.desktop.ready())
			this.desktop.autoResize();

	}, this));

	if (this.settings.notifyScheme == 'simple')
		this.GetSimpleNotifySettings();
	else
		this.GetNotifySettings();

	if (!this.desktop.ready())
		BX.bind(document, "click", BX.proxy(this.autoHide, this));
};

BX.IM.prototype.prepareSettings = function(params)
{
	params = typeof(params) == "object"? params: {};

	var items = [];

	var tabs = [];
	var tabActive = true;
	var i = 0;

	for (var tab in this.settingsView)
	{
		if (this.settingsView[tab].condition && !this.settingsView[tab].condition())
			continue;
		var events = {};
		if (this.settingsView[tab].click)
			events = {click: BX.delegate(this.settingsView[tab].click, this)};

		if (params.active && this.settingsView[params.active])
		{
			if (params.active == tab)
				tabActive = true;
			else
				tabActive = false;
		}

		tabs.push(BX.create('div', {attrs: {'data-id': i+""}, props : { className : "bx-messenger-settings-tab"+(tabActive ? " bx-messenger-settings-tab-active": "") }, html: this.settingsView[tab].title, events: events}));
		tabActive = false;
		i++;
	}
	items.push(BX.create("div", {style: {display: !params.onlyPanel? 'block': 'none' }, props : { className: "bx-messenger-settings-tabs"}, children : tabs}));

	var tabs = [];
	var tabActive = true;
	for (var tab in this.settingsView)
	{
		if (this.settingsView[tab].condition && !this.settingsView[tab].condition())
			continue;

		if (params.active && this.settingsView[params.active])
		{
			if (params.active == tab)
				tabActive = true;
			else
				tabActive = false;
		}

		var table = [];
		if (this.settingsView[tab].settings)
		{
			var tableItems = [];
			for (var item = 0; item < this.settingsView[tab].settings.length; item++)
			{
				if (typeof(this.settingsView[tab].settings[item]) != 'object' || this.settingsView[tab].settings[item] === null)
					continue;

				if (this.settingsView[tab].settings[item].condition && !this.settingsView[tab].settings[item].condition())
					continue;

				if (this.settingsView[tab].settings[item].type == 'notifyControl' || this.settingsView[tab].settings[item].type == 'table' || this.settingsView[tab].settings[item].type == 'space')
				{
					tableItems.push(BX.create("tr", {children : [
						BX.create("td", {attrs: {'colspan': 2}, children: this.prepareSettingsItem(this.settingsView[tab].settings[item])})
					]}));
				}
				else
				{
					tableItems.push(BX.create("tr", {children : [
						BX.create("td", {attrs: {'width': '55%'}, html: this.settingsView[tab].settings[item].title}),
						BX.create("td", {attrs: {'width': '45%'}, children: this.prepareSettingsItem(this.settingsView[tab].settings[item])})
					]}));
				}
			}
			if (tableItems.length > 0)
				table.push(BX.create("table", {attrs : {'cellpadding': '0', 'cellspacing': '0', 'border': '0', 'width': '100%'}, props : { className: "bx-messenger-settings-table bx-messenger-settings-table-style-"+tab}, children: tableItems}));
		}

		tabs.push(BX.create("div", {style: {display: params.onlyPanel? (params.onlyPanel == tab? 'block': 'none'): '' }, props : { id: 'bx-messenger-settings-content-'+tab, className: "bx-messenger-settings-content"+(tabActive? " bx-messenger-settings-content-active": "")}, children: table}));
		tabActive = false;
	}
	items.push(BX.create("div", {props : { className: "bx-messenger-settings-contents"}, children : tabs}));
	if (this.desktop.ready())
	{
		items.push(BX.create("div", {props : { className: "popup-window-buttons"}, children : [this.popupSettingsButtonSave.buttonNode, this.popupSettingsButtonClose.buttonNode]}));
	}

	return items;
};

BX.IM.prototype.prepareSettingsTable = function(tab)
{
	var config = this.settingsTableConfig[tab];

	if (!config.error_rows && config.condition && !BX.delegate(config.condition, this)())
		return null;

	var tableNotify = [];
	var tableHeaders = [];
	for (var item = 0; item < config.headers.length; item++)
	{
		if (typeof(config.headers[item]) == 'boolean')
			continue;
		tableHeaders.push(BX.create("th", {html: config.headers[item]}));
	}

	if (tableHeaders.length > 0)
		tableNotify.push(BX.create("tr", {children : tableHeaders}));

	if (config.error_rows && config.condition && !config.condition())
	{
		tableNotify.push(BX.create("tr", {children: [
			BX.create("td", {attrs: {'colspan': config.headers.length}, style: {textAlign: 'center'}, children: [config.error_rows]})
		]}));
		config.rows = [];
	}

	for (var item = 0; item < config.rows.length; item++)
	{
		var tableRows = [];
		for (var column = 0; column < config.rows[item].length; column++)
		{
			if (typeof(config.rows[item][column]) != 'object' || config.rows[item][column] === null)
				continue;

			var attrs = {};
			var props = {};
			if (config.rows[item][column].type == 'separator')
			{
				attrs = {'colspan': config.headers.length};
				props = {className: "bx-messenger-settings-table-sep"};
			}
			else if (config.rows[item][column].type == 'error')
			{
				attrs = {'colspan': config.headers.length};
				props = {className: "bx-messenger-settings-table-error"};
			}
			tableRows.push(BX.create("td", {attrs: attrs, props:props, children: this.prepareSettingsItem(config.rows[item][column])}));
		}
		if (tableRows.length > 0)
			tableNotify.push(BX.create("tr", {children : tableRows}));
	}
	var currentTable = null;
	if (tableNotify.length > 0)
		currentTable = BX.create("table", {attrs : {'cellpadding': '0', 'cellspacing': '0', 'border': '0'}, props : { className: "bx-messenger-settings-table-extra bx-messenger-settings-table-extra-"+tab}, children: tableNotify});

	return currentTable;
};

BX.IM.prototype.prepareSettingsItem = function(params)
{
	var items = [];
	var config = BX.clone(params);
	if (config.type == 'space')
	{
		items.push(BX.create("span", {props: {className: "bx-messenger-settings-space"}}));
	}
	if (config.type == 'text' || config.type == 'separator' || config.type == 'error')
	{
		items.push(BX.create("span", {html: config.title }))
	}
	if (config.type == 'link')
	{
		if (config.callback)
			var events = { click: config.callback };

		items.push(BX.create("span", {props: {className: "bx-messenger-settings-link"}, attrs: config.attrs, html: config.title, events: events }))
	}
	if (config.type == 'checkbox')
	{
		if (config.callback)
			var events = { change: config.callback };

		if (typeof(config.checked) == 'undefined')
			config.checked = this.settings[config.name] != false;

		var attrs = { type: "checkbox", name: config.name? config.name: false, checked: config.checked == true? "true": false, disabled: config.disabled == true? "true": false};
		if (config.name)
			attrs['data-save'] = 1;

		var element = BX.create("input", {attrs: attrs, events: events });
		items.push(element);

		if (config.saveCallback)
			this.settingsSaveCallback[config.name] = config.saveCallback;
	}
	else if (config.type == 'select')
	{
		if (config.callback)
			var events = { change: config.callback };

		var options = [];
		for (var i = 0; i < config.items.length; i++)
		{
			options.push(BX.create("option", {attrs : { value: config.items[i].value, selected: config.value == config.items[i].value? "true": false}, html: config.items[i].title}));
		}
		var attrs = { name: config.name};
		if (config.name)
			attrs['data-save'] = 1;
		var element = BX.create("select", {attrs : attrs, events: events, children: options});
		items.push(element);

		if (config.saveCallback)
			this.settingsSaveCallback[config.name] = config.saveCallback;
	}
	else if (config.type == 'table')
	{
		items.push(BX.create("div", {attrs: {id: 'bx-messenger-settings-table-'+config.name}, style: {'display': config.show? 'block':'none'}, children: [this.prepareSettingsTable(config.name)]}));
	}
	else if (config.type == 'notifyControl')
	{
		var onChangeNotifyScheme = BX.delegate(function(){
			if (BX.proxy_context.value == 'simple')
			{
				BX.hide(BX('bx-messenger-settings-table-notify'));
				BX.show(BX('bx-messenger-settings-table-simpleNotify'));
				BX.show(BX('bx-messenger-settings-notify-clients'));

				this.GetSimpleNotifySettings();
			}
			else
			{
				BX.show(BX('bx-messenger-settings-table-notify'));
				BX.hide(BX('bx-messenger-settings-table-simpleNotify'));
				BX.hide(BX('bx-messenger-settings-notify-clients'));

				this.GetNotifySettings();
			}
		}, this);
		items.push(BX.create("div", {props : { className: "bx-messenger-settings-notify-type"}, children : [
			BX.create("input", {attrs : { id: 'notifySchemeSimpleValue', 'data-save': 1,  type: "radio", name: "notifyScheme", value: 'simple', checked: this.settings.notifyScheme == 'simple'}, events: {change: onChangeNotifyScheme}}),
			BX.create("label", {attrs : { 'for': 'notifySchemeSimpleValue'}, html: ' '+BX.message('IM_SETTINGS_NS_1')+' '}),
			BX.create("input", {attrs : { id: 'notifySchemeExpertValue', 'data-save': 1,  type: "radio", name: "notifyScheme", value: 'expert', checked: this.settings.notifyScheme == 'expert'}, events: {change: onChangeNotifyScheme}}),
			BX.create("label", {attrs : { 'for': 'notifySchemeExpertValue'}, html: ' '+BX.message('IM_SETTINGS_NS_2')+' '})
		]}));
		/*
		items.push(BX.create("div", {attrs: {id: "bx-messenger-settings-notify-important"}, style : {display: this.settings.notifyScheme == 'simple'? 'block':'none'}, props : { className: "bx-messenger-settings-notify-important"}, children : [
			BX.create("input", {attrs : { id: 'notifySchemeLevelImportantValue', 'data-save': 1,  type: "radio", name: "notifySchemeLevel", value: 'important', checked: this.settings.notifySchemeLevel == 'important'}}),
			BX.create("label", {attrs : { 'for': 'notifySchemeLevelImportantValue'}, html: ' '+BX.message('IM_SETTINGS_NSL_1')+' '}),
			BX.create("input", {attrs : { id: 'notifySchemeLevelNormalValue', 'data-save': 1,  type: "radio", name: "notifySchemeLevel", value: 'normal', checked: this.settings.notifySchemeLevel == 'normal'}}),
			BX.create("label", {attrs : { 'for': 'notifySchemeLevelNormalValue'}, html: ' '+BX.message('IM_SETTINGS_NSL_2')+' '})
		]}));
		*/
		items.push(BX.create("div", {attrs: {id: "bx-messenger-settings-notify-clients"}, style : {display: this.settings.notifyScheme == 'simple'? 'block':'none'}, props : { className: "bx-messenger-settings-notify-clients"}, children : [
			BX.create("div", {props: {className: 'bx-messenger-settings-notify-clients-title'}, html: BX.message('IM_SETTINGS_NC_1')}),
			BX.create("div", {props: {className: 'bx-messenger-settings-notify-clients-item'}, children: [
				BX.create("input", {attrs : { 'data-save': 1,  type: "checkbox", id: "notifySchemeSendSite", name: "notifySchemeSendSite", value: 'Y', checked: this.settings.notifySchemeSendSite}}),
				BX.create("label", {attrs : {'for': "notifySchemeSendSite"}, html: ' '+BX.message('IM_SETTINGS_NC_2')+'<br>'})
			]}),
			this.bitrixXmpp? BX.create("div", {props: {className: 'bx-messenger-settings-notify-clients-item'}, children: [
				BX.create("input", {attrs : { 'data-save': 1,  type: "checkbox", id: "notifySchemeSendXmpp", name: "notifySchemeSendXmpp", value: 'Y', checked: this.settings.notifySchemeSendXmpp}}),
				BX.create("label", {attrs : {'for': "notifySchemeSendXmpp"}, html: ' '+BX.message('IM_SETTINGS_NC_3')+'<br>'})
			]}): null,
			BX.create("div", {props: {className: 'bx-messenger-settings-notify-clients-item'}, children: [
				BX.create("input", {attrs : { 'data-save': 1,  type: "checkbox", id: "notifySchemeSendEmail", name: "notifySchemeSendEmail", value: 'Y', checked: this.settings.notifySchemeSendEmail}}),
				BX.create("label", {attrs : {'for': "notifySchemeSendEmail"}, html: ' '+BX.message('IM_SETTINGS_NC_4').replace('#MAIL#', this.userEmail)+''})
			]})
		]}));
	}
	return items;
};

BX.IM.prototype.saveSettings = function(settings)
{
	var timeoutKey = '';
	for (var config in settings)
	{
		this.settings[config] = settings[config];
		timeoutKey = timeoutKey+config;
	}
	BX.localStorage.set('ims', JSON.stringify(this.settings), 5);

	if (this.saveSettingsTimeout[timeoutKey])
		clearTimeout(this.saveSettingsTimeout[timeoutKey]);

	this.saveSettingsTimeout[timeoutKey] = setTimeout(BX.delegate(function(){
		BX.ajax({
			url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?SETTINGS_SAVE',
			method: 'POST',
			dataType: 'json',
			timeout: 30,
			data: {'IM_SETTING_SAVE' : 'Y', 'IM_AJAX_CALL' : 'Y', SETTINGS: JSON.stringify(settings), 'sessid': BX.bitrix_sessid()}
		});
		delete this.saveSettingsTimeout[timeoutKey];
	}, this), 700);
};

BX.IM.prototype.saveFormSettings = function()
{
	var inputs = BX.findChildren(this.popupSettingsBody, {attribute : "data-save"}, true);
	for (var i = 0; i < inputs.length; i++)
	{
		if (inputs[i].tagName == 'INPUT' && inputs[i].type == 'checkbox')
		{
			if (typeof(this.settingsSaveCallback[inputs[i].name]) == 'function')
				this.settings[inputs[i].name] = this.settingsSaveCallback[inputs[i].name](inputs[i]);
			else
				this.settings[inputs[i].name] = inputs[i].checked;
		}
		else if (inputs[i].tagName == 'INPUT' && inputs[i].type == 'radio' && inputs[i].checked)
		{
			if (typeof(this.settingsSaveCallback[inputs[i].name]) == 'function')
				this.settings[inputs[i].name] = this.settingsSaveCallback[inputs[i].name](inputs[i]);
			else
				this.settings[inputs[i].name] = inputs[i].value;
		}
		else if (inputs[i].tagName == 'SELECT')
		{
			if (typeof(this.settingsSaveCallback[inputs[i].name]) == 'function')
				this.settings[inputs[i].name] = this.settingsSaveCallback[inputs[i].name](inputs[i]);
			else
				this.settings[inputs[i].name] = inputs[i][inputs[i].selectedIndex].value;
		}
	}

	var values = this.settings['notifyScheme'] == 'simple'? {}: {notify: {}};
	for (var config in this.settings)
	{
		if (config.substr(0,7) == 'notify|')
		{
			if (values['notify'])
				values['notify'][config.substr(7)] = this.settings[config];
		}
		else
		{
			values[config] = this.settings[config];
		}
	}

	if (this.desktop.ready())
	{
		BX.desktop.onCustomEvent("bxSaveSettings", [this.settings]);
	}
	else
	{
		BX.localStorage.set('ims', JSON.stringify(this.settings), 5);
	}

	if (this.messenger != null)
	{
		this.messenger.userListRedraw(true);
		if (this.messenger.popupMessengerTextareaSendType)
			this.messenger.popupMessengerTextareaSendType.innerHTML = this.settings.sendByEnter? 'Enter': (BX.browser.IsMac()? "&#8984;+Enter": "Ctrl+Enter");
	}

	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?SETTINGS_FORM_SAVE',
		method: 'POST',
		dataType: 'json',
		timeout: 30,
		data: {'IM_SETTINGS_SAVE' : 'Y', 'IM_AJAX_CALL' : 'Y', SETTINGS: JSON.stringify(values), 'sessid': BX.bitrix_sessid()},
		onsuccess: BX.delegate(function() {
			this.popupSettings.close();
		}, this),
		onfailure: BX.delegate(function() {
			this.popupSettingsButtonSave.setClassName('popup-window-button popup-window-button-accept');
			this.popupSettingsButtonSave.setName(BX.message('IM_SETTINGS_SAVE'));
			BX.show(this.popupSettingsButtonClose.buttonNode);
		}, this)
	});
};

BX.IM.prototype.GetNotifySettings = function()
{
	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?SETTINGS_NOTIFY_LOAD',
		method: 'POST',
		dataType: 'json',
		timeout: 30,
		data: {'IM_SETTINGS_NOTIFY_LOAD' : 'Y', 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
		onsuccess: BX.delegate(function(data) {
			if (data.ERROR == "")
			{
				if (this.settings.notifyScheme == 'simple')
				{
					for (var configName in data.VALUES)
					{
						if (!BX('notifySchemeSendSite').checked && configName.substr(0,5) == 'site|')
							data.VALUES[configName] = false;
						else if (this.bitrixXmpp && !BX('notifySchemeSendXmpp').checked && configName.substr(0,5) == 'xmpp|')
							data.VALUES[configName] = false;
						else if (!BX('notifySchemeSendEmail').checked && configName.substr(0,6) == 'email|')
							data.VALUES[configName] = false;

						this.settings['notify|'+configName] = data.VALUES[configName];
					}
				}
				else
				{
					for (var configName in data.VALUES)
						this.settings['notify|'+configName] = data.VALUES[configName];
				}

				var rows = [];
				if (data.NAMES['im'])
				{
					rows.push([{'type': 'separator', title: data.NAMES['im'].NAME}]);
					for (var notifyId in data.NAMES['im']['NOTIFY'])
					{
						var notifyName = data.NAMES['im']['NOTIFY'][notifyId];
						if (notifyId == 'message')
							rows.push([{'type': 'text', title: notifyName}, {'type': 'checkbox', checked: true, disabled: true}, this.bitrixXmpp? {'type': 'checkbox', checked: true, disabled: true}: false, {'type': 'checkbox', name: 'notify|email|im|'+notifyId}]);
						else
							rows.push([{'type': 'text', title: notifyName}, {'type': 'checkbox', name: 'notify|site|im|'+notifyId}, this.bitrixXmpp? {'type': 'checkbox', name: 'notify|xmpp|im|'+notifyId}: false, {'type': 'checkbox', name: 'notify|email|im|'+notifyId}]);
					}
					delete data.NAMES['im'];
				}

				for (var moduleId in data.NAMES)
				{
					if (moduleId == 'im')
						continue;

					rows.push([{'type': 'separator', title: data.NAMES[moduleId].NAME}]);
					for (var notifyId in data.NAMES[moduleId]['NOTIFY'])
					{
						var notifyName = data.NAMES[moduleId]['NOTIFY'][notifyId];
						rows.push([{'type': 'text', title: notifyName}, {'type': 'checkbox', name: 'notify|site|'+moduleId+'|'+notifyId}, this.bitrixXmpp? {'type': 'checkbox', name: 'notify|xmpp|'+moduleId+'|'+notifyId}: false, {'type': 'checkbox', name: 'notify|email|'+moduleId+'|'+notifyId}]);
					}
				}
				this.settingsTableConfig['notify'].rows = rows;
			}
			else
			{
				this.settingsTableConfig['notify'].rows = [
					[{'type': 'error', title: BX.message('IM_M_ERROR')}]
				];
			}
			BX('bx-messenger-settings-table-notify').innerHTML = '';
			BX.adjust(BX('bx-messenger-settings-table-notify'), {children: [this.prepareSettingsTable('notify')]});
			if (data.ERROR != "")
				this.settingsTableConfig['notify'].rows = [];
			if (this.desktop.ready())
				this.desktop.autoResize();
		}, this),
		onfailure: BX.delegate(function() {
			this.settingsTableConfig['notify'].rows = [
				[{'type': 'error', title: BX.message('IM_M_ERROR')}]
			];
			BX('bx-messenger-settings-table-notify').innerHTML = '';
			BX.adjust(BX('bx-messenger-settings-table-notify'), {children: [this.prepareSettingsTable('notify')]});
			this.settingsTableConfig['notify'].rows = [];
			if (this.desktop.ready())
				this.desktop.autoResize()
		}, this)
	});
};

BX.IM.prototype.GetSimpleNotifySettings = function()
{
	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?SETTINGS_SIMPLE_NOTIFY_LOAD',
		method: 'POST',
		dataType: 'json',
		timeout: 30,
		data: {'IM_SETTINGS_SIMPLE_NOTIFY_LOAD' : 'Y', 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
		onsuccess: BX.delegate(function(data) {
			if (data.ERROR == "")
			{
				var rows = [];
				for (var moduleId in data.VALUES)
				{
					rows.push([{'type': 'separator', title: data.NAMES[moduleId].NAME}]);
					for (var notifyId in data.VALUES[moduleId])
					{
						var notifyName = data.NAMES[moduleId]['NOTIFY'][notifyId];
						rows.push([
							{'type': 'text', title: notifyName},
							{'type': 'link', title: BX.message('IM_SETTINGS_SNOTIFY_ENABLE'), attrs: { 'data-settingName': moduleId+'|'+notifyId}, callback: BX.delegate(function(){ this.removeSimpleNotify(BX.proxy_context)}, this)}
						]);
						this.settingsNotifyBlocked[moduleId+"|"+notifyId] = true;
					}
				}
				this.settingsTableConfig['simpleNotify'].rows = rows;
			}
			else
			{
				this.settingsTableConfig['simpleNotify'].rows = [
					[{'type': 'error', title: BX.message('IM_M_ERROR')}]
				];
			}
			BX('bx-messenger-settings-table-simpleNotify').innerHTML = '';
			BX.adjust(BX('bx-messenger-settings-table-simpleNotify'), {children: [this.prepareSettingsTable('simpleNotify')]});
			if (data.ERROR != "")
				this.settingsTableConfig['simpleNotify'].rows = [];
			if (this.desktop.ready())
				this.desktop.autoResize();
		}, this),
		onfailure: BX.delegate(function() {
			this.settingsTableConfig['simpleNotify'].rows = [
				[{'type': 'error', title: BX.message('IM_M_ERROR')}]
			];
			if (BX('bx-messenger-settings-table-simpleNotify'))
			{
				BX('bx-messenger-settings-table-simpleNotify').innerHTML = '';
				BX.adjust(BX('bx-messenger-settings-table-simpleNotify'), {children: [this.prepareSettingsTable('simpleNotify')]});
			}
			this.settingsTableConfig['simpleNotify'].rows = [];
			if (this.desktop.ready())
				this.desktop.autoResize();
		}, this)
	});
};

BX.IM.prototype.removeSimpleNotify = function(element)
{
	var table = element.parentNode.parentNode.parentNode;
	if (!element.parentNode.parentNode.nextSibling && element.parentNode.parentNode.previousSibling.childNodes[0].className != "bx-messenger-settings-table-sep")
	{
		BX.remove(element.parentNode.parentNode);
	}
	else if (element.parentNode.parentNode.previousSibling && element.parentNode.parentNode.previousSibling.childNodes[0].className != "bx-messenger-settings-table-sep")
	{
		BX.remove(element.parentNode.parentNode);
	}
	else if (element.parentNode.parentNode.nextSibling && element.parentNode.parentNode.nextSibling.childNodes[0].className != "bx-messenger-settings-table-sep")
	{
		BX.remove(element.parentNode.parentNode);
	}
	else if (element.parentNode.parentNode.previousSibling.childNodes[0].className == "bx-messenger-settings-table-sep" && !element.parentNode.parentNode.nextSibling)
	{
		BX.remove(element.parentNode.parentNode.previousSibling);
		BX.remove(element.parentNode.parentNode);
	}
	else if (element.parentNode.parentNode.previousSibling.childNodes[0].className == "bx-messenger-settings-table-sep" && element.parentNode.parentNode.nextSibling.childNodes[0].className == "bx-messenger-settings-table-sep")
	{
		BX.remove(element.parentNode.parentNode.previousSibling);
		BX.remove(element.parentNode.parentNode);
	}
	if (table.childNodes.length <= 1)
		BX.remove(table);

	this.notify.blockNotifyType(element.getAttribute('data-settingName'));

	if (this.desktop.ready())
		this.desktop.autoResize();
};

BX.IM.prototype.openConfirm = function(text, buttons, modal)
{
	if (this.popupConfirm != null)
		this.popupConfirm.destroy();

	if (typeof(text) == "object")
		text = '<div class="bx-messenger-confirm-title">'+text.title+'</div>'+text.message;

	modal = modal !== false;
	if (typeof(buttons) == "undefined" || typeof(buttons) == "object" && buttons.length <= 0)
	{
		buttons = [new BX.PopupWindowButton({
			text : BX.message('IM_NOTIFY_CONFIRM_CLOSE'),
			className : "popup-window-button-decline",
			events : { click : function(e) { this.popupWindow.close(); BX.PreventDefault(e) } }
		})];
	}
	this.popupConfirm = new BX.PopupWindow('bx-notifier-popup-confirm', null, {
		zIndex: 200,
		autoHide: buttons === false,
		buttons : buttons,
		closeByEsc: buttons === false,
		overlay : modal,
		events : { onPopupClose : function() { this.destroy() }, onPopupDestroy : BX.delegate(function() { this.popupConfirm = null }, this)},
		content : BX.create("div", { props : { className : (buttons === false? " bx-messenger-confirm-without-buttons": "bx-messenger-confirm") }, html: text})
	});
	this.popupConfirm.show();
	BX.bind(this.popupConfirm.popupContainer, "click", BX.IM.preventDefault);
	BX.bind(this.popupConfirm.contentContainer, "click", BX.PreventDefault);
	BX.bind(this.popupConfirm.overlay.element, "click", BX.PreventDefault);
};


BX.IM.preventDefault = function(event)
{
	event = event||window.event;

	if (event.stopPropagation)
		event.stopPropagation();
	else
		event.cancelBubble = true;

	if (BXIM && BXIM.messenger)
		BXIM.messenger.closeMenuPopup();
};

BX.IM.formatDate = function(timestamp)
{
	var format = [
		["tommorow", "tommorow, "+BX.message("IM_MESSAGE_FORMAT_TIME")],
		["today", "today, "+BX.message("IM_MESSAGE_FORMAT_TIME")],
		["yesterday", "yesterday, "+BX.message("IM_MESSAGE_FORMAT_TIME")],
		["", BX.date.convertBitrixFormat(BX.message("FORMAT_DATETIME"))]
	];

	return BX.date.format(format, parseInt(timestamp)+parseInt(BX.message("SERVER_TZ_OFFSET")), BX.IM.getNowDate()+parseInt(BX.message("SERVER_TZ_OFFSET")), true);
};

BX.IM.getNowDate = function(today)
{
	var currentDate = (new Date);
	if (today == true)
		currentDate = (new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate(), 0, 0, 0));

	return Math.round((+currentDate/1000))+parseInt(BX.message("USER_TZ_OFFSET"));
};

BX.IM.prepareText = function(text, prepare, quote, image, highlightText)
{
	var textElement = text;
	prepare = prepare == true;
	quote = quote == true;
	image = image == true;
	highlightText = highlightText? highlightText: false;

	textElement = BX.util.trim(textElement);
	if (prepare)
		textElement = BX.util.htmlspecialchars(textElement);
	if (quote)
	{
		textElement = textElement.replace(/------------------------------------------------------<br \/>(.*?)\[(.*?)\]<br \/>(.*?)------------------------------------------------------(<br \/>)?/g, "<div class=\"bx-messenger-content-quote\"><span class=\"bx-messenger-content-quote-icon\"></span><div class=\"bx-messenger-content-quote-wrap\"><div class=\"bx-messenger-content-quote-name\">$1 <span class=\"bx-messenger-content-quote-time\">$2</span></div>$3</div></div>");
		textElement = textElement.replace(/------------------------------------------------------<br \/>(.*?)<br \/>------------------------------------------------------(<br \/>)?/g, "<div class=\"bx-messenger-content-quote\"><span class=\"bx-messenger-content-quote-icon\"></span><div class=\"bx-messenger-content-quote-wrap\">$1</div></div>");
	}
	if (prepare)
		textElement = textElement.replace(/\n/gi, '<br />');
	textElement = textElement.replace(/\t/gi, '&nbsp;&nbsp;&nbsp;&nbsp;');

	if (image)
	{
		textElement = textElement.replace(/<a(.*?)>(http[s]{0,1}:\/\/.*?)<\/a>/ig, function(whole, aInner, text)
		{
			if(!text.match(/\.(jpg|jpeg|png|gif)$/i) || text.indexOf("/docs/pub/") > 0)
				return whole;
			else
				return '<span class="bx-messenger-image-box"><a' +aInner+ '><img src="' +text+'" class="bx-messenger-image-text"></a></span>';
		});
	}
	if (highlightText)
	{
		textElement = textElement.replace(new RegExp("("+highlightText.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&")+")",'ig'), '<span class="bx-messenger-highlight">$1</span>');
	}

	if (true)
	{
		textElement = textElement.replace(/\[USER=([0-9]{1,})\](.*?)\[\/USER\]/ig, function(whole, userId, text)
		{
			var html = '';

			userId = parseInt(userId);
			if (quote && text && userId > 0)
				html = '<span class="bx-messenger-ajax" data-entity="user" data-userId="'+userId+'">'+text+'</span>';
			else
				html = text;

			return html;
		});
		textElement = textElement.replace(/\[PCH=([0-9]{1,})\](.*?)\[\/PCH\]/ig, function(whole, historyId, text)
		{
			var html = '';

			historyId = parseInt(historyId);
			if (quote && text && historyId > 0)
				html = '<span class="bx-messenger-ajax" data-entity="phoneCallHistory" data-historyId="'+historyId+'">'+text+'</span>';
			else
				html = text;

			return html;
		});
	}

	return textElement;
};

BX.IM.prepareTextBack = function(text)
{
	var textElement = text;

	textElement = BX.util.htmlspecialcharsback(textElement);
	textElement = textElement.replace(/<(\/*)([buis]+)>/ig, '[$1$2]');
	textElement = textElement.replace(/<img.*?data-code="([^"]*)".*?>/ig, '$1');
	textElement = textElement.replace(/<a.*?href="([^"]*)".*?>.*?<\/a>/ig, '$1');
	textElement = textElement.replace(/------------------------------------------------------(.*?)------------------------------------------------------/gmi, "["+BX.message("IM_M_QUOTE_BLOCK")+"]");
	textElement = textElement.split('&nbsp;&nbsp;&nbsp;&nbsp;').join("\t");
	textElement = textElement.split('<br />').join("\n");//.replace(/<\/?[^>]+>/gi, '');

	return textElement;
};

BX.IM.prototype.getLocalConfig = function(name, def)
{
	if (this.desktop.ready())
	{
		return BX.desktop.getLocalConfig(name, def);
	}

	def = typeof(def) == 'undefined'? null: def;

	if (!BX.browser.SupportLocalStorage())
	{
		return def;
	}

	if (this.desktop.run() && !this.desktop.ready())
		name = 'full-'+name;

	var result = BX.localStorage.get(name);
	if (result == null)
	{
		return def;
	}

	if (typeof(result) == 'string' && result.length > 0)
	{
		try {
			result = JSON.parse(result);
		}
		catch(e) { result = def; }
	}

	return result;
};

BX.IM.prototype.setLocalConfig = function(name, value)
{
	if (this.desktop.run())
	{
		if (this.desktop.ready())
			return BX.desktop.setLocalConfig(name, value);
		else
			return false;
	}

	if (typeof(value) == 'object')
		value = JSON.stringify(value);
	else if (typeof(value) == 'boolean')
		value = value? 'true': 'false';
	else if (typeof(value) == 'undefined')
		value = '';
	else if (typeof(value) != 'string')
		value = value+'';

	if (!BX.browser.SupportLocalStorage())
		return false;

	if (this.desktop.run() && !this.desktop.ready())
		name = 'full-'+name;

	BX.localStorage.set(name, value, 86400);

	return true;
};

BX.IM.prototype.removeLocalConfig = function(name)
{
	if (this.desktop.ready())
	{
		return BX.desktop.removeLocalConfig(name);
	}

	if (!BX.browser.SupportLocalStorage())
		return false;

	if (this.desktop.run() && !this.desktop.ready())
		name = 'full-'+name;

	BX.localStorage.remove(name);

	return true;
};

BX.IM.prototype.storageSet = function(params)
{
	if (params.key == 'mps')
	{
		this.stopSound();
	}
	else if (params.key == 'mrs')
	{
		this.repeatSound(params.value.sound, params.value.time);
	}
	else if (params.key == 'mrss')
	{
		this.stopRepeatSound(params.value.sound, false);
	}
};
})();


/* IM notify class */

(function() {

if (BX.Notify)
	return;

BX.Notify = function(BXIM, params)
{
	this.BXIM = BXIM;
	this.settings = {};
	this.params = params || {};
	this.windowInnerSize = {};
	this.windowScrollPos = {};
	this.sendAjaxTry = 0;

	this.webrtc = params.webrtcClass;
	this.desktop = params.desktopClass;

	this.panel = params.domNode;
	if (this.desktop.run())
		BX.hide(this.panel);

	BX.bind(this.panel, "click", BX.IM.preventDefault);

	this.notifyCount = params.countNotify;
	this.notifyUpdateCount = params.countNotify;
	this.counters = params.counters;
	this.mailCount = params.mailCount;

	this.notifyHistoryPage = 0;
	this.notifyHistoryLoad = false;

	this.notifyBody = null;
	this.notify = params.notify;
	this.notifyLoad = false;
	this.unreadNotify = params.unreadNotify;
	this.unreadNotifyLoad = params.loadNotify;
	this.flashNotify = params.flashNotify;
	this.initNotifyCount = params.countNotify;
	this.confirmDisabledButtons = false;

	if (this.unreadNotifyLoad)
	{
		for (var i in this.notify)
			this.initNotifyCount--;
	}

	if (BX.browser.IsDoctype())
		BX.addClass(this.panel, 'bx-notifier-panel-doc');
	else
		BX.addClass(document.body, 'bx-no-doctype');


	this.panelButtonCall = BX.findChild(this.panel, {className : "bx-notifier-call"}, true);
	if (!this.webrtc.phoneEnabled)
	{
		BX.style(this.panelButtonCall, 'display', 'none');
	}

	this.panelButtonNetwork = BX.findChild(this.panel, {className : "bx-notifier-network"}, true);
	this.panelButtonNetworkCount = BX.findChild(this.panelButtonNetwork, {className : "bx-notifier-indicator-count"}, true);
	if (this.panelButtonNetwork != null)
	{
		if (this.BXIM.bitrixNetworkStatus)
		{
			this.panelButtonNetwork.href = "https://www.bitrix24.net/";
			this.panelButtonNetwork.setAttribute('target', '_blank');
			if (this.panelButtonNetworkCount != null)
				this.panelButtonNetworkCount.innerHTML = '';
		}
		else
		{
			BX.style(this.panelButtonNetwork, 'display', 'none');
		}
	}

	this.panelButtonNotify = BX.findChild(this.panel, {className : "bx-notifier-notify"}, true);
	this.panelButtonNotifyCount = BX.findChild(this.panelButtonNotify, {className : "bx-notifier-indicator-count"}, true);
	if (this.panelButtonNotifyCount != null)
		this.panelButtonNotifyCount.innerHTML = '';

	this.panelButtonMessage = BX.findChild(this.panel, {className : "bx-notifier-message"}, true);
	this.panelButtonMessageCount = BX.findChild(this.panelButtonMessage, {className : "bx-notifier-indicator-count"}, true);
	if (this.panelButtonMessageCount != null)
		this.panelButtonMessageCount.innerHTML = '';

	this.panelButtonMail = BX.findChild(this.panel, {className : "bx-notifier-mail"}, true);
	this.panelButtonMailCount = BX.findChild(this.panelButtonMail, {className : "bx-notifier-indicator-count"}, true);
	if (this.panelButtonMail != null)
	{
		this.panelButtonMail.href = this.BXIM.path.mail;
		this.panelButtonMail.setAttribute('target', '_blank');
		if (this.panelButtonMessageCount != null)
			this.panelButtonMailCount.innerHTML = '';
	}

	this.panelDragLabel = BX.findChild(this.panel, {className : "bx-notifier-drag"}, true);

	this.messenger = null;
	this.messengerNotifyButton = null;
	this.messengerNotifyButtonCount = null;

	/* full window notify */
	this.popupNotifyItem = null;
	this.popupNotifySize = 383;

	this.popupNotifyButtonFilter = null;
	this.popupNotifyButtonFilterBox = null;
	this.popupHistoryFilterVisible = false;
	/* more users from notify */
	this.popupNotifyMore = null;

	this.dragged = false;
	this.dragPageX = 0;
	this.dragPageY = 0;

	if (this.BXIM.init)
	{
		if (this.desktop.run())
		{
			BX.desktop.addTab({
				id: 'notify',
				title: BX.message('IM_SETTINGS_NOTIFY'),
				order: 110,
				target: 'im',
				events: {
					open: BX.delegate(function(){
						this.openNotify(false, true)
					}, this)
				}
			});
		}

		this.panel.appendChild(this.BXIM.audio.reminder = BX.create("audio", { props : { className : "bx-notify-audio" }, children : [
			BX.create("source", { attrs : { src : "/bitrix/js/im/audio/reminder.ogg", type : "audio/ogg; codecs=vorbis" }}),
			BX.create("source", { attrs : { src : "/bitrix/js/im/audio/reminder.mp3", type : "audio/mpeg" }})
		]}));
		if (typeof(this.BXIM.audio.reminder.play) == 'undefined')
		{
			this.BXIM.settings.enableSound = false;
		}

		if (BX.browser.SupportLocalStorage())
		{
			BX.addCustomEvent(window, "onLocalStorageSet", BX.proxy(this.storageSet, this));
			var panelPosition = BX.localStorage.get('npp');
			this.BXIM.settings.panelPositionHorizontal = !!panelPosition? panelPosition.h: this.BXIM.settings.panelPositionHorizontal;
			this.BXIM.settings.panelPositionVertical = !!panelPosition? panelPosition.v: this.BXIM.settings.panelPositionVertical;

			var mfn = BX.localStorage.get('mfn');
			if (mfn)
			{
				for (var i in this.flashNotify)
					if (this.flashNotify[i] != mfn[i] && mfn[i] == false)
						this.flashNotify[i] = false;
			}

			BX.garbage(function(){
				BX.localStorage.set('mfn', this.flashNotify, 15);
			}, this);
		}

		BX.bind(this.panelButtonNotify, "click", BX.proxy(function(){
			this.toggleNotify()
		}, this.BXIM));

		if (this.webrtc.phoneEnabled)
		{
			BX.bind(this.panelButtonCall, "click", BX.delegate(this.webrtc.openKeyPad, this.webrtc));
			BX.bind(window, 'scroll', BX.delegate(function(){
				if (this.webrtc.popupKeyPad)
					this.webrtc.popupKeyPad.close();
			}, this));
		}

		BX.bind(this.panelDragLabel, "mousedown", BX.proxy(this._startDrag, this));
		BX.bind(this.panelDragLabel, "dobleclick", BX.proxy(this._stopDrag, this));

		this.updateNotifyMailCount();

		if (!this.desktop.run())
		{
			this.adjustPosition({resize: true});
			BX.bind(window, "resize", BX.proxy(function(){
				this.closePopup();
				this.adjustPosition({resize: true});
			}, this));
			if (!BX.browser.IsDoctype())
				BX.bind(window, "scroll", BX.proxy(function(){ this.adjustPosition({scroll: true});}, this));
		}
		setTimeout(BX.delegate(function(){
			this.newNotify();
			this.updateNotifyCounters();
			this.updateNotifyCount();
		}, this), 500);

		this.setStatus(this.BXIM.settings.status, false);
	}

	BX.addCustomEvent(window, "onSonetLogCounterClear", BX.proxy(function(counter){
		var sendObject = {};
		sendObject[counter] = 0;
		this.updateNotifyCounters(sendObject);
	}, this));
};

BX.Notify.prototype.getCounter = function(type)
{
	if (typeof(type) != 'string')
		return false;

	type = type.toString();

	if (type == 'im_notify')
		return this.notifyCount;
	if (type == 'im_message')
		return this.BXIM.messageCount;

	return this.counters[type]? this.counters[type]: 0;
};

BX.Notify.prototype.updateNotifyCounters = function(arCounter, send)
{
	send = send != false;
	if (typeof(arCounter) == "object")
	{
		for (var i in arCounter)
			this.counters[i] = arCounter[i];
	}
	BX.onCustomEvent(window, 'onImUpdateCounter', [this.counters]);
	if (send)
		BX.localStorage.set('nuc', this.counters, 5);
};

BX.Notify.prototype.updateNotifyMailCount = function(count, send)
{
	send = send != false;

	if (typeof(count) != "undefined" || parseInt(count)>0)
		this.mailCount = parseInt(count);

	if (this.mailCount > 0)
		BX.removeClass(this.panelButtonMail, 'bx-notifier-hide');
	else
		BX.addClass(this.panelButtonMail, 'bx-notifier-hide');

	var mailCountLabel = '';
	if (this.mailCount > 99)
		mailCountLabel = '99+';
	else if (this.mailCount > 0)
		mailCountLabel = this.mailCount;

	if (this.panelButtonMailCount != null)
	{
		this.panelButtonMailCount.innerHTML = mailCountLabel;
		this.adjustPosition({"resize": true, "timeout": 500});
	}

	BX.onCustomEvent(window, 'onImUpdateCounterMail', [this.mailCount, 'MAIL']);

	if (send)
		BX.localStorage.set('numc', this.mailCount, 5);
};

BX.Notify.prototype.updateNotifyCount = function(send)
{
	send = send != false;

	var count = 0;
	var updateCount = 0;

	if (this.unreadNotifyLoad)
		count = this.initNotifyCount;

	for (var i in this.unreadNotify)
	{
		if (this.unreadNotify[i] == null)
			continue;

		var notify = this.notify[this.unreadNotify[i]];
		if (!notify)
			continue;

		if (notify.type != 1)
			updateCount++;

		count++;
	}

	var notifyCountLabel = '';
	if (count > 99)
		notifyCountLabel = '99+';
	else if (count > 0)
		notifyCountLabel = count;

	if (this.panelButtonNotifyCount != null)
	{
		this.panelButtonNotifyCount.innerHTML = notifyCountLabel;
		this.adjustPosition({"resize": true, "timeout": 500});
	}
	if (this.messengerNotifyButtonCount != null)
		this.messengerNotifyButtonCount.innerHTML = parseInt(notifyCountLabel)>0? '<span class="bx-messenger-cl-count-digit">'+notifyCountLabel+'</span>':'';
	if (this.desktop.run())
		BX.desktop.setTabBadge('notify', count)

	this.notifyCount = parseInt(count);
	this.notifyUpdateCount = parseInt(updateCount);

	BX.onCustomEvent(window, 'onImUpdateCounterNotify', [this.notifyCount, 'NOTIFY']);

	if (send)
		BX.localStorage.set('nunc', {'unread': this.unreadNotify, 'flash': this.flashNotify}, 5);
};

BX.Notify.prototype.changeUnreadNotify = function(unreadNotify, send)
{
	send = send != false;
	var redraw = false;
	for (var i in unreadNotify)
	{
		if (!this.BXIM.xmppStatus && this.BXIM.settings.status != 'dnd')
			this.flashNotify[unreadNotify[i]] = true;
		else
			this.flashNotify[unreadNotify[i]] = false;

		this.unreadNotify[unreadNotify[i]] = unreadNotify[i];
		redraw = true;
	}
	this.newNotify(send);

	if (redraw && this.BXIM.notifyOpen)
		this.openNotify(true);

	this.updateNotifyCount(send);
};

BX.Notify.prototype.viewNotify = function(id)
{
	if (parseInt(id) <= 0)
		return false;

	var notify = this.notify[id];
	if (notify && notify.type != 1)
		delete this.unreadNotify[id];

	delete this.flashNotify[id];

	BX.localStorage.set('mfn', this.flashNotify, 80);

	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?NOTIFY_VIEW',
		method: 'POST',
		dataType: 'json',
		timeout: 60,
		data: {'IM_NOTIFY_VIEW' : 'Y', 'ID' : parseInt(id), 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()}
	});

	if (this.BXIM.notifyOpen)
	{
		var elements = BX.findChildren(this.popupNotifyItem, {className : "bx-notifier-item-new"}, false);
		if (elements != null)
			for (var i = 0; i < elements.length; i++)
				BX.removeClass(elements[i], 'bx-notifier-item-new');
	}

	this.updateNotifyCount(false);

	return true;
};

BX.Notify.prototype.viewNotifyAll = function()
{
	var id = 0;
	for (var i in this.unreadNotify)
	{
		var notify = this.notify[i];
		if (notify && notify.type != 1)
			delete this.unreadNotify[i];

		delete this.flashNotify[i];
		id = id < i? i: id;
	}

	if (parseInt(id) <= 0)
		return false;

	BX.localStorage.set('mfn', this.flashNotify, 80);

	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?NOTIFY_VIEWED',
		method: 'POST',
		dataType: 'json',
		timeout: 60,
		data: {'IM_NOTIFY_VIEWED' : 'Y', 'MAX_ID' : parseInt(id), 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()}
	});

	if (this.BXIM.notifyOpen)
	{
		var elements = BX.findChildren(this.popupNotifyItem, {className : "bx-notifier-item-new"}, false);
		if (elements != null)
			for (var i = 0; i < elements.length; i++)
				if (elements[i].getAttribute('data-notifyType') != 1)
					BX.removeClass(elements[i], 'bx-notifier-item-new');
	}

	this.updateNotifyCount(false);

	return true;
};

BX.Notify.prototype.newNotify = function(send)
{
	send = send != false;

	var arNotify = [];
	var arNotifyText = [];
	var arNotifySort = [];
	for (var i in this.flashNotify)
	{
		if (this.flashNotify[i] === true)
		{
			arNotifySort.push(parseInt(i));
			this.flashNotify[i] = false;
		}
	}
	var flashNames = {};
	arNotifySort.sort(BX.delegate(function(a, b) {if (!this.notify[a] || !this.notify[b]){return 0;}var i1 = parseInt(this.notify[a].date); var i2 = parseInt(this.notify[b].date);var t1 = parseInt(this.notify[a].type); var t2 = parseInt(this.notify[b].type);if (t1 == 1 && t2 != 1) { return -1;}else if (t2 == 1 && t1 != 1) { return 1;}else if (i2 > i1) { return 1; }else if (i2 < i1) { return -1;}else{ return 0;}}, this));
	for (var i = 0; i < arNotifySort.length; i++)
	{
		var notify = this.notify[arNotifySort[i]];
		if (notify && notify.userId && notify.userName)
			flashNames[notify.userId] = notify.userName;

		notify = this.createNotify(this.notify[arNotifySort[i]], true);
		if (notify !== false)
		{
			arNotify.push(notify);

			notify = this.notify[arNotifySort[i]];
			arNotifyText.push({
				'title':  notify.userName? BX.util.htmlspecialcharsback(notify.userName): BX.message('IM_NOTIFY_WINDOW_NEW_TITLE'),
				'text':  BX.util.htmlspecialcharsback(notify.text).split('<br />').join("\n").replace(/<\/?[^>]+>/gi, ''),
				'icon':  notify.userAvatar? notify.userAvatar: '',
				'tag':  'im-notify-'+notify.tag
			});
		}
	}
	if (arNotify.length > 5)
	{
		var names = '';
		for (var i in flashNames)
			names += ', <i>'+flashNames[i]+'</i>';

		var notify = {
			id: 0, type: 4,date: (+new Date)/1000, tag: '', original_tag: '',
			title: BX.message('IM_NM_NOTIFY_1').replace('#COUNT#', arNotify.length),
			text: names.length>0? BX.message('IM_NM_NOTIFY_2').replace('#USERS#', names.substr(2)): BX.message('IM_NM_NOTIFY_3')
		};
		notify = this.createNotify(notify, true);
		BX.style(notify, 'cursor', 'pointer');
		arNotify = [notify];

		arNotifyText = [{
			'id': '',
			'title':  BX.message('IM_NM_NOTIFY_1').replace('#COUNT#', arNotify.length),
			'text': names.length>0? BX.message('IM_NM_NOTIFY_2').replace('#USERS#', BX.util.htmlspecialcharsback(names.substr(2))).replace(/<\/?[^>]+>/gi, ''): BX.message('IM_NM_NOTIFY_3')
		}];
	}
	if (arNotify.length == 0)
		return false;

	if (this.desktop.ready())
		BX.desktop.flashIcon(false);

	this.closePopup();

	if (!(!this.desktop.ready() && this.desktop.run()) && (this.BXIM.settings.status == 'dnd' || !this.desktop.ready() && this.BXIM.desktopStatus))
		return false;

	if (send && !this.BXIM.xmppStatus)
		this.BXIM.playSound("reminder");

	if (send && this.desktop.ready())
	{
		for (var i = 0; i < arNotify.length; i++)
		{
			var dataNotifyId = arNotify[i].getAttribute("data-notifyId");
			var messsageJs =
				'var notify = BX.findChild(document.body, {className : "bx-notifier-item"}, true);'+
				'BX.bind(BX.findChild(notify, {className : "bx-notifier-item-delete"}, true), "click", function(event){ if (this.getAttribute("data-notifyType") != 1) { BX.desktop.onCustomEvent("main", "bxImClickCloseNotify", [this.getAttribute("data-notifyId")]); } BX.desktop.windowCommand("close"); BX.IM.preventDefault(event); });'+
				(arNotify[i].id>0? '': 'BX.bind(notify, "click", function(event){ BX.desktop.onCustomEvent("main", "bxImClickNotify", []); BX.desktop.windowCommand("close"); BX.IM.preventDefault(event); });')+
				'BX.bindDelegate(notify, "click", {className: "bx-notifier-item-button"}, BX.delegate(function(){ '+
					'BX.desktop.windowCommand("freeze");'+
					'notifyId = BX.proxy_context.getAttribute("data-id");'+
					'BXIM.notify.confirmRequest({'+
						'"notifyId": notifyId,'+
						'"notifyValue": BX.proxy_context.getAttribute("data-value"),'+
						'"notifyURL": BX.proxy_context.getAttribute("data-url"),'+
						'"notifyTag": BXIM.notify.notify[notifyId] && BXIM.notify.notify[notifyId].tag? BXIM.notify.notify[notifyId].tag: null,'+
						'"groupDelete": BX.proxy_context.getAttribute("data-group") == null? false: true,'+
					'}, true);'+
					'BX.desktop.onCustomEvent("main", "bxImClickConfirmNotify", [notifyId]); '+
				'}, BXIM.notify));'+
				'BX.bind(notify, "contextmenu", function(){ BX.desktop.windowCommand("close")});';
			this.desktop.openNewNotify(dataNotifyId, arNotify[i], messsageJs);
		}
	}
	else if(send && !this.BXIM.windowFocus && this.BXIM.notifyManager.nativeNotifyGranted())
	{
		for (var i = 0; i < arNotifyText.length; i++)
		{
			var notify = arNotifyText[i];
			notify.onshow = function() {
				var notify = this;
				setTimeout(function(){
					notify.close();
				}, 5000)
			}
			notify.onclick = function() {
				window.focus();
				top.BXIM.openNotify();
				this.close();
			}
			this.BXIM.notifyManager.nativeNotify(notify)
		}
	}
	else
	{
		if (this.BXIM.windowFocus && this.BXIM.notifyManager.nativeNotifyGranted())
		{
			BX.localStorage.set('mnnb', true, 1);
		}
		for (var i = 0; i < arNotify.length; i++)
		{
			this.BXIM.notifyManager.add({
				'html': arNotify[i],
				'tag': arNotify[i].id>0? 'im-notify-'+this.notify[arNotify[i].getAttribute("data-notifyId")].tag:'',
				'originalTag': arNotify[i].id>0? this.notify[arNotify[i].getAttribute("data-notifyId")].original_tag:'',
				'notifyId': arNotify[i].getAttribute("data-notifyId"),
				'notifyType': arNotify[i].getAttribute("data-notifyType"),
				'click': arNotify[i].id > 0? null: BX.delegate(function(popup) {
					this.openNotify();
					popup.close();
				}, this),
				'close': BX.delegate(function(popup) {
					if (popup.notifyParams.notifyType != 1 && popup.notifyParams.notifyId)
						this.viewNotify(popup.notifyParams.notifyId);
				}, this)
			});
		}
	}
	return true;
};

BX.Notify.prototype.confirmRequest = function(params, popup)
{
	if (this.confirmDisabledButtons)
		return false;

	popup = popup == true;

	params.notifyOriginTag = this.notify[params.notifyId]? this.notify[params.notifyId].original_tag: '';

	if (params.groupDelete && params.notifyTag != null)
	{
		for (var i in this.notify)
		{
			if (this.notify[i].tag == params.notifyTag)
				delete this.notify[i];
		}
	}
	else
		delete this.notify[params.notifyId];

	this.updateNotifyCount();

	if (popup && this.desktop.ready())
		BX.desktop.windowCommand("freeze");
	else
		BX.hide(BX.proxy_context.parentNode.parentNode.parentNode);

	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?NOTIFY_CONFIRM',
		method: 'POST',
		dataType: 'json',
		timeout: 30,
		data: {'IM_NOTIFY_CONFIRM' : 'Y', 'NOTIFY_ID' : params.notifyId, 'NOTIFY_VALUE' : params.notifyValue, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
		onsuccess: BX.delegate(function() {
			if (params.notifyURL != null)
			{
				if (popup && this.desktop.ready())
					BX.desktop.browse(params.notifyURL);
				else
					location.href = params.notifyURL;

				this.confirmDisabledButtons = true;
			}
			BX.onCustomEvent(window, 'onImConfirmNotify', [{'NOTIFY_ID' : params.notifyId, 'NOTIFY_TAG' : params.notifyOriginTag, 'NOTIFY_VALUE' : params.notifyValue}]);
			if (popup && this.desktop.ready())
				BX.desktop.windowCommand("close");
		}, this),
		onfailure: BX.delegate(function() {
			if (this.desktop.ready())
				BX.desktop.windowCommand("close");
		}, this)
	});

	if (params.groupDelete)
		BX.localStorage.set('nrgn', params.notifyTag, 5);
	else
		BX.localStorage.set('nrn', params.notifyId, 5);

	return false;
};

BX.Notify.prototype.prepareNotify = function(arItemsNotify, loadMore)
{
	loadMore = loadMore == true;
	var itemsNotify = typeof(arItemsNotify) == 'object'? arItemsNotify: BX.clone(this.notify);

	var arGroupedNotify = {};
	var arGroupedNotifyByUser = {};
	for (var i in itemsNotify)
	{
		if (itemsNotify[i].tag != '')
		{
			if (!arGroupedNotifyByUser[itemsNotify[i].tag] || !arGroupedNotifyByUser[itemsNotify[i].tag][itemsNotify[i].userId])
			{
				if (arGroupedNotifyByUser[itemsNotify[i].tag])
				{
					if (!arGroupedNotifyByUser[itemsNotify[i].tag][itemsNotify[i].userId])
						arGroupedNotifyByUser[itemsNotify[i].tag][itemsNotify[i].userId] = itemsNotify[i].id;

					if (arGroupedNotify[itemsNotify[i].tag].date < itemsNotify[i].date)
					{
						itemsNotify[i].groupped = true;
						delete itemsNotify[arGroupedNotify[itemsNotify[i].tag].id];
						arGroupedNotify[itemsNotify[i].tag] = itemsNotify[i];
					}
					else
					{
						delete itemsNotify[i];
					}
				}
				else
				{
					arGroupedNotifyByUser[itemsNotify[i].tag] = {};
					arGroupedNotifyByUser[itemsNotify[i].tag][itemsNotify[i].userId] = itemsNotify[i].id;
					arGroupedNotify[itemsNotify[i].tag] = itemsNotify[i];
				}
			}
			else
			{
				if (arGroupedNotify[itemsNotify[i].tag].date < itemsNotify[i].date)
				{
					itemsNotify[i].groupped = true;
					delete itemsNotify[arGroupedNotify[itemsNotify[i].tag].id];
					arGroupedNotify[itemsNotify[i].tag] = itemsNotify[i];
				}
				else
				{
					delete itemsNotify[i];
				}
			}
		}
	}

	var arNotify = [];
	var arNotifySort = [];
	for (var i in itemsNotify)
		arNotifySort.push(parseInt(i));

	arNotifySort.sort(function(a, b) {if (!itemsNotify[a] || !itemsNotify[b]){return 0;}var i1 = parseInt(itemsNotify[a].date); var i2 = parseInt(itemsNotify[b].date);var t1 = parseInt(itemsNotify[a].type); var t2 = parseInt(itemsNotify[b].type);if (t1 == 1 && t2 != 1) { return -1;}else if (t2 == 1 && t1 != 1) { return 1;}else if (i2 > i1) { return 1; }else if (i2 < i1) { return -1;}else{ return 0;}});
	for (var i = 0; i < arNotifySort.length; i++)
	{
		var notify = itemsNotify[arNotifySort[i]];
		if (notify.groupped)
		{
			notify.otherCount = 0;
			if (this.notify[notify.id])
			{
				this.notify[notify.id].otherItems = [];
				for (var userId in arGroupedNotifyByUser[notify.tag])
				{
					if (this.notify[notify.id].userId != userId)
						this.notify[notify.id].otherItems.push(arGroupedNotifyByUser[notify.tag][userId]);
				}
				notify.otherCount = this.notify[notify.id].otherItems.length;
			}
			if (notify.otherCount > 0 && notify.type == 2)
				notify.type = 3;
		}
		notify = this.createNotify(notify);
		if (notify !== false)
			arNotify.push(notify);
	}

	if (arNotify.length == 0)
	{
		if (this.BXIM.settings.loadLastNotify && !this.notifyLoad || this.unreadNotifyLoad)
		{
			arNotify.push(BX.create("div", { attrs : { style : "padding-top: 162px;"}, props : { className: "bx-notifier-content-load", id : "bx-notifier-content-load"}, children : [
				BX.create("div", {props : { className: "bx-notifier-content-load-block bx-notifier-item"}, children : [
					BX.create('span', { props : { className : "bx-notifier-content-load-block-img" }}),
					BX.create('span', {props : { className : "bx-notifier-content-load-block-text"}, html: BX.message('IM_NOTIFY_LOAD_NOTIFY')})
				]})
			]}));
		}
		else if (!loadMore && !this.BXIM.settings.loadLastNotify)
		{
			arNotify.push(BX.create("div", { attrs : { style : "padding-top: 248px; margin-bottom: 31px;"}, props : { className : "bx-messenger-box-empty bx-notifier-content-empty", id : "bx-notifier-content-empty"}, html: BX.message('IM_NOTIFY_EMPTY_2')}));
		}
		else if (!loadMore)
		{
			arNotify.push(BX.create("div", { attrs : { style : "padding-top: 248px; margin-bottom: 31px;"}, props : { className : "bx-messenger-box-empty bx-notifier-content-empty", id : "bx-notifier-content-empty"}, html: BX.message('IM_NOTIFY_EMPTY_3')}));
			arNotify.push(BX.create('a', { attrs : { href : "#notifyHistory", id : "bx-notifier-content-link-history"}, props : { className : "bx-notifier-content-link-history bx-notifier-content-link-history-empty" }, children: [
				BX.create('span', {props : { className : "bx-notifier-item-button bx-notifier-item-button-white" }, html: '<i class="bx-notifier-item-button-fc"></i><span>'+BX.message('IM_NOTIFY_HISTORY_LATE')+'</span><i></i>'})
			]}));
		}
		if (this.BXIM.settings.loadLastNotify)
			return arNotify;
	}

	if (!this.unreadNotifyLoad)
	{
		arNotify.push(
			BX.create('a', { attrs : { href : "#notifyHistory", id : "bx-notifier-content-link-history"}, props : { className : "bx-notifier-content-link-history bx-notifier-content-link-history-empty" }, children: [
				BX.create('span', {props : { className : "bx-notifier-item-button bx-notifier-item-button-white" }, html: '<i class="bx-notifier-item-button-fc"></i><span>'+BX.message('IM_NOTIFY_HISTORY')+'</span><i></i>'})
			]})
		);
	}

	return arNotify;
};

BX.Notify.prototype.openNotify = function(reOpen, force)
{
	reOpen = reOpen == true;
	force = force == true;

	if (this.messenger.popupMessenger == null)
		this.messenger.openMessenger(false);

	if (this.BXIM.notifyOpen && !force)
	{
		if (!reOpen)
		{
			this.messenger.extraClose(true);
			return false;
		}
	}
	else
	{
		this.BXIM.dialogOpen = false;
		this.BXIM.notifyOpen = true;
		if (!this.desktop.run())
		{
			this.messengerNotifyButton.className = "bx-messenger-cl-notify-button bx-messenger-cl-notify-button-active";
		}
	}

	this.webrtc.callOverlayToggleSize(true);

	var arNotify = this.prepareNotify();
	this.notifyBody = BX.create("div", { props : { className : "bx-notifier-wrap" }, children : [
		BX.create("div", { props : { className : "bx-messenger-panel" }, children : [
			BX.create('span', { props : { className : "bx-messenger-panel-avatar bx-messenger-avatar-notify"}}),
			//this.popupNotifyButtonFilter = BX.create("a", { props : { className : "bx-messenger-panel-filter bx-messenger-panel-filter-middle"}, html: (this.popupNotifyFilterVisible? BX.message("IM_PANEL_FILTER_OFF"):BX.message("IM_PANEL_FILTER_ON"))}),
			BX.create("span", { props : { className : "bx-messenger-panel-title bx-messenger-panel-title-middle"}, html: BX.message('IM_NOTIFY_WINDOW_TITLE')})
		]}),
		this.popupNotifyButtonFilterBox = BX.create("div", { props : { className : "bx-messenger-panel-filter-box" }, style : {display: this.popupNotifyFilterVisible? 'block': 'none'}, children : [
			BX.create('div', {props : { className : "bx-messenger-filter-name" }, html: BX.message('IM_PANEL_FILTER_NAME')}),
			BX.create('div', {props : { className : "bx-messenger-filter-date bx-messenger-input-wrap" }, html: '<input type="text" class="bx-messenger-input" value="" placeholder="'+BX.message('IM_PANEL_FILTER_DATE')+'" />'}),
			BX.create('div', {props : { className : "bx-messenger-filter-text bx-messenger-input-wrap" }, html: '<input type="text" class="bx-messenger-input" value="" />'})
		]}),
		this.popupNotifyItem = BX.create("div", { props : { className : "bx-notifier-item-wrap" }, style : {height: this.popupNotifySize+'px'}, children : arNotify})
	]});
	this.messenger.extraOpen(this.notifyBody);

	this.BXIM.notifyManager.nativeNotifyAccessForm();

	if (this.unreadNotifyLoad)
		this.loadNotify();
	else if (!this.notifyLoad && this.BXIM.settings.loadLastNotify)
		this.notifyHistory();

	if (!reOpen && this.BXIM.isFocus('notify') && this.notifyUpdateCount > 0)
		this.viewNotifyAll();

	BX.bind(this.popupNotifyButtonFilter, "click",  BX.delegate(function(){
		if (this.popupNotifyFilterVisible)
		{
			this.popupNotifyButtonFilter.innerHTML = BX.message("IM_PANEL_FILTER_ON");
			this.popupNotifySize = this.popupNotifySize+this.popupNotifyButtonFilterBox.offsetHeight;
			this.popupNotifyItem.style.height = this.popupNotifySize+'px';
			BX.style(this.popupNotifyButtonFilterBox, 'display', 'none');
			this.popupNotifyFilterVisible = false;
		}
		else
		{
			this.popupNotifyButtonFilter.innerHTML = BX.message("IM_PANEL_FILTER_OFF");
			BX.style(this.popupNotifyButtonFilterBox, 'display', 'block');
			this.popupNotifySize = this.popupNotifySize-this.popupNotifyButtonFilterBox.offsetHeight;
			this.popupNotifyItem.style.height = this.popupNotifySize+'px';
			this.popupNotifyFilterVisible = true;
		}
	}, this));

	BX.bind(this.popupNotifyItem, "scroll", BX.delegate(function() {
		if (this.messenger.popupPopupMenu != null)
			this.messenger.popupPopupMenu.close();
	}, this));

	BX.bind(BX('bx-notifier-content-link-history'), "click", BX.delegate(this.notifyHistory, this));

	BX.bind(this.popupNotifyItem, "click", BX.delegate(this.closePopup, this));

	BX.bindDelegate(this.popupNotifyItem, 'click', {className: 'bx-notifier-item-help'}, BX.proxy(function(e) {
		if (this.popupNotifyMore != null)
			this.popupNotifyMore.destroy();
		else
		{
			var notifyHelp = this.notify[BX.proxy_context.getAttribute('data-help')];
			if (!notifyHelp.otherItems)
				return false;

			var htmlElement = '<span class="bx-notifier-item-help-popup">';
				for (var i = 0; i < notifyHelp.otherItems.length; i++)
					htmlElement += '<a class="bx-notifier-item-help-popup-img" href="'+this.notify[notifyHelp.otherItems[i]].userLink+'"  onclick="BXIM.openMessenger('+this.notify[notifyHelp.otherItems[i]].userId+'); return false;" target="_blank"><span class="bx-notifier-popup-avatar"><img class="bx-notifier-popup-avatar-img" src="'+this.notify[notifyHelp.otherItems[i]].userAvatar+'"></span><span class="bx-notifier-item-help-popup-name">'+BX.IM.prepareText(this.notify[notifyHelp.otherItems[i]].userName)+'</span></a>';
			htmlElement += '</span>';

			this.popupNotifyMore = new BX.PopupWindow('bx-notifier-other-window', BX.proxy_context, {
				zIndex: 200,
				lightShadow : true,
				offsetTop: -2,
				offsetLeft: 3,
				autoHide: true,
				closeByEsc: true,
				bindOptions: {position: "top"},
				events : {
					onPopupClose : function() { this.destroy() },
					onPopupDestroy : BX.proxy(function() { this.popupNotifyMore = null; }, this)
				},
				content : BX.create("div", { props : { className : "bx-notifier-popup-menu" }, children: [
					BX.create("div", { props : { className : " " }, html: htmlElement})
				]})
			});
			this.popupNotifyMore.setAngle({});
			this.popupNotifyMore.show();
			BX.bind(this.popupNotifyMore.popupContainer, "click", BX.IM.preventDefault);
		}

		return BX.PreventDefault(e);
	}, this));

	BX.bindDelegate(this.popupNotifyItem, 'click', {className: 'bx-notifier-item-delete'}, BX.proxy(function(e) {
		if (!BX.proxy_context) return;

		BX.proxy_context.setAttribute('id', 'bx-notifier-item-delete-'+BX.proxy_context.getAttribute('data-notifyId'));
		this.deleteNotify(BX.proxy_context.getAttribute('data-notifyId'));

		return BX.PreventDefault(e);
	}, this));

	BX.bindDelegate(this.popupNotifyItem, 'click', {className: 'bx-notifier-item-button'}, BX.proxy(function(e) {
		var notifyId = BX.proxy_context.getAttribute('data-id');
		this.confirmRequest({
			'notifyId': notifyId,
			'notifyValue': BX.proxy_context.getAttribute('data-value'),
			'notifyURL': BX.proxy_context.getAttribute('data-url'),
			'notifyTag': this.notify[notifyId] && this.notify[notifyId].tag? this.notify[notifyId].tag: null,
			'groupDelete': BX.proxy_context.getAttribute('data-group') != null
		});
		if (BX.proxy_context.parentNode.parentNode.parentNode.previousSibling == null && BX.proxy_context.parentNode.parentNode.parentNode.nextSibling == null)
			this.openNotify(true);
		else if (BX.proxy_context.parentNode.parentNode.parentNode.previousSibling == null && BX.proxy_context.parentNode.parentNode.parentNode.nextSibling.tagName.toUpperCase() == 'A')
			this.openNotify(true);
		else
			BX.remove(BX.proxy_context.parentNode.parentNode.parentNode);

		return BX.PreventDefault(e);
	}, this));

	if (this.desktop.ready())
	{
		BX.bindDelegate(this.popupNotifyItem, "contextmenu", {className: 'bx-notifier-item-content'}, BX.delegate(function(e) {
			this.messenger.openPopupMenu(e, 'notify', false);
			return BX.PreventDefault(e);
		}, this));
	}
	else
	{
		BX.bindDelegate(this.popupNotifyItem, 'contextmenu', {className: 'bx-notifier-item-delete'}, BX.proxy(function(e) {
			if (!BX.proxy_context) return;

			BX.proxy_context.setAttribute('id', 'bx-notifier-item-delete-'+BX.proxy_context.getAttribute('data-notifyId'));
			this.messenger.openPopupMenu(BX.proxy_context, 'notifyDelete');

			return BX.PreventDefault(e);
		}, this));
	}


	return false;
};


BX.Notify.prototype.deleteNotify = function(notifyId)
{
	var notifyDiv = BX('bx-notifier-item-delete-'+notifyId);
	var sendRequest = false;

	if (this.notify[notifyId])
	{
		sendRequest = true;
		var notifyTag = null;
		if (this.notify[notifyId].tag)
			notifyTag = this.notify[notifyId].tag;

		var groupDelete = !(notifyDiv.getAttribute('data-group') == null || notifyTag == null);
		if (groupDelete)
		{
			for (var i in this.notify)
			{
				if (this.notify[i].tag == notifyTag)
					delete this.notify[i];
			}
		}
		else
			delete this.notify[notifyId];
	}
	this.updateNotifyCount();

	if (sendRequest)
	{
		var DATA = {};
		if (groupDelete)
			DATA = {'IM_NOTIFY_GROUP_REMOVE' : 'Y', 'NOTIFY_ID' : notifyId, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()};
		else
			DATA = {'IM_NOTIFY_REMOVE' : 'Y', 'NOTIFY_ID' : notifyId, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()};

		BX.ajax({
			url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?NOTIFY_REMOVE',
			method: 'POST',
			dataType: 'json',
			timeout: 30,
			data: DATA
		});

		if (groupDelete)
			BX.localStorage.set('nrgn', notifyTag, 5);
		else
			BX.localStorage.set('nrn', notifyId, 5);
	}

	if (notifyDiv.parentNode.parentNode.previousSibling == null && notifyDiv.parentNode.parentNode.nextSibling == null)
	{
		this.openNotify(true);
	}
	else if (notifyDiv.parentNode.parentNode.previousSibling == null && notifyDiv.parentNode.parentNode.nextSibling.tagName.toUpperCase() == 'A')
	{
		this.notifyLoad = false;
		this.notifyHistoryPage = 0;
		this.openNotify(true);
	}
	else
		BX.remove(notifyDiv.parentNode.parentNode);

	return true;
};

BX.Notify.prototype.blockNotifyType = function(settingName)
{
	var blockResult = typeof(this.BXIM.settingsNotifyBlocked[settingName]) == 'undefined';
	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?NOTIFY_BLOCK_TYPE',
		method: 'POST',
		dataType: 'json',
		timeout: 30,
		data: {'IM_NOTIFY_BLOCK_TYPE' : 'Y', 'BLOCK_TYPE' : settingName, 'BLOCK_RESULT' : (blockResult? 'Y': 'N'), 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()}
	});

	if (blockResult)
	{
		this.BXIM.settingsNotifyBlocked[settingName] = true;
		this.BXIM.settings['site|'.settingName] = false;
		this.BXIM.settings['xmpp|'.settingName] = false;
		this.BXIM.settings['email|'.settingName] = false;
	}
	else
	{
		delete this.BXIM.settingsNotifyBlocked[settingName];
		this.BXIM.settings['site|'.settingName] = true;
		this.BXIM.settings['xmpp|'.settingName] = true;
		this.BXIM.settings['email|'.settingName] = true;
	}

	return true;
};

BX.Notify.prototype.closeNotify = function()
{
	if (!this.desktop.run())
	{
		this.messengerNotifyButton.className = "bx-messenger-cl-notify-button";
	}

	this.BXIM.notifyOpen = false;
	this.popupNotifyItem = null;
	BX.unbindAll(this.popupNotifyButtonFilter);
	BX.unbindAll(this.popupNotifyItem);
};

BX.Notify.prototype.loadNotify = function(send)
{
	if (this.loadNotityBlock)
		return false;

	send = send != false;
	this.loadNotityBlock = true;
	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?NOTIFY_LOAD',
		method: 'POST',
		dataType: 'json',
		lsId: 'IM_NOTIFY_LOAD',
		lsTimeout: 5,
		timeout: 30,
		data: {'IM_NOTIFY_LOAD' : 'Y', 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
		onsuccess: BX.delegate(function(data) {
			this.loadNotityBlock = false;
			this.unreadNotifyLoad = false;
			this.notifyLoad = true;
			var arNotify = {};
			if (typeof(data.NOTIFY) == 'object')
			{
				for (var i in data.NOTIFY)
				{
					data.NOTIFY[i].date = parseInt(data.NOTIFY[i].date)+parseInt(BX.message('USER_TZ_OFFSET'));
					arNotify[i] = this.notify[i] = data.NOTIFY[i];
					this.BXIM.lastRecordId = parseInt(i) > this.BXIM.lastRecordId? parseInt(i): this.BXIM.lastRecordId;
					if (data.NOTIFY[i].type != '1')
						delete this.unreadNotify[i];
					else
						this.unreadNotify[i] = i;
				}
			}
			if (send)
			{
				this.openNotify(true);
				if (this.BXIM.settings.loadLastNotify)
					this.notifyHistory();

				BX.localStorage.set('nln', true, 5);
			}

			this.updateNotifyCount();

		}, this),
		onfailure: BX.delegate(function() {
			this.loadNotityBlock = false;
		}, this)
	});
};

BX.Notify.prototype.notifyHistory = function(event)
{
	event = event || window.event;
	if (this.notifyHistoryLoad)
		return false;

	if (BX('bx-notifier-content-link-history'))
	{
		var linkHistoryText = BX.findChild(BX('bx-notifier-content-link-history').firstChild, {tagName : "span"}, true);
		linkHistoryText.innerHTML = BX.message('IM_NOTIFY_LOAD_NOTIFY')+'...';
	}

	this.notifyHistoryLoad = true;
	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?NOTIFY_HISTORY_LOAD_MORE',
		method: 'POST',
		dataType: 'json',
		timeout: 30,
		data: {'IM_NOTIFY_HISTORY_LOAD_MORE' : 'Y', 'PAGE' : !this.BXIM.settings.loadLastNotify && this.notifyHistoryPage == 0? 1: this.notifyHistoryPage, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
		onsuccess: BX.delegate(function(data)
		{
			if (data.ERROR == '')
			{
				this.notifyLoad = true;
				BX.remove(BX('bx-notifier-content-load'));

				this.sendAjaxTry = 0;
				var arNotify = {};
				var count = 0;
				if (typeof(data.NOTIFY) == 'object')
				{
					for (var i in data.NOTIFY)
					{
						data.NOTIFY[i].date = parseInt(data.NOTIFY[i].date)+parseInt(BX.message('USER_TZ_OFFSET'));
						if (!this.notify[i])
							arNotify[i] = data.NOTIFY[i];

						if (!this.notify[i])
						{
							this.notify[i] = BX.clone(data.NOTIFY[i]);
						}
						count++;
					}
				}
				if (BX('bx-notifier-content-link-history'))
					BX.remove(BX('bx-notifier-content-link-history'));

				if (count > 0)
				{
					if (BX('bx-notifier-content-empty'))
						BX.remove(BX('bx-notifier-content-empty'));

					var arNotify = this.prepareNotify(arNotify, true);
					for (var i = 0; i < arNotify.length; i++) {
						this.popupNotifyItem.appendChild(arNotify[i]);
					}
					if (count < 20 && this.notifyHistoryPage > 0)
					{
						BX.remove(BX('bx-notifier-content-link-history'));
					}
					else
					{
						if (BX('bx-notifier-content-link-history'))
						{
							BX('bx-notifier-content-link-history').className = "bx-notifier-content-link-history";
							var linkHistoryText = BX.findChild(BX('bx-notifier-content-link-history').firstChild, {tagName : "span"}, true);
							linkHistoryText.innerHTML = count < 20 && this.notifyHistoryPage == 0? BX.message('IM_NOTIFY_HISTORY_LATE'): BX.message('IM_NOTIFY_HISTORY_MORE');
							BX.bind(BX('bx-notifier-content-link-history'), "click", BX.delegate(this.notifyHistory, this));
						}
						if (count >= 20 && this.notifyHistoryPage == 0)
							this.notifyHistoryPage = 1;
					}
				}
				else if (count <= 0 && this.notifyHistoryPage == 0)
				{
					this.popupNotifyItem.innerHTML = '';
					this.popupNotifyItem.appendChild(BX.create("div", { attrs : { style : "padding-top: 248px; margin-bottom: 31px;"}, props : { className : "bx-messenger-box-empty bx-notifier-content-empty", id : "bx-notifier-content-empty"}, html: BX.message('IM_NOTIFY_EMPTY_3')}));
					this.popupNotifyItem.appendChild(
						BX.create('a', { attrs : { href : "#notifyHistory", id : "bx-notifier-content-link-history"}, events: {'click': BX.delegate(this.notifyHistory, this)}, props : { className : "bx-notifier-content-link-history bx-notifier-content-link-history-empty" }, children: [
							BX.create('span', {props : { className : "bx-notifier-item-button bx-notifier-item-button-white" }, html: '<i class="bx-notifier-item-button-fc"></i><span>'+BX.message('IM_NOTIFY_HISTORY_LATE')+'</span><i></i>'})
						]})
					);
				}
				else
				{
					if (this.popupNotifyItem.innerHTML == '')
					{
						this.popupNotifyItem.appendChild(BX.create("div", { attrs : { style : "padding-top: 248px; margin-bottom: 31px;"}, props : { className : "bx-messenger-box-empty bx-notifier-content-empty", id : "bx-notifier-content-empty"}, html: BX.message('IM_NOTIFY_EMPTY_3')}));
					}
				}
				this.notifyHistoryLoad = false;
				this.notifyHistoryPage++;
			}
			else
			{
				if (data.ERROR == 'SESSION_ERROR' && this.sendAjaxTry < 2)
				{
					this.sendAjaxTry++;
					BX.message({'bitrix_sessid': data.BITRIX_SESSID});
					setTimeout(BX.delegate(function(){
						this.notifyHistoryLoad = false;
						this.notifyHistory();
					}, this), 2000);
					BX.onCustomEvent(window, 'onImError', [data.ERROR, data.BITRIX_SESSID]);
				}
				else if (data.ERROR == 'AUTHORIZE_ERROR' && this.sendAjaxTry < 1)
				{
					this.sendAjaxTry++;
					setTimeout(BX.delegate(function(){
						this.notifyHistoryLoad = false;
						this.notifyHistory();
					}, this), 10000);
					BX.onCustomEvent(window, 'onImError', [data.ERROR]);
				}
			}
		}, this),
		onfailure: BX.delegate(function(){
			this.notifyHistoryLoad = false;
			this.sendAjaxTry = 0;
		}, this)
	});

	if (event)
		return BX.PreventDefault(event);
	else
		return true;
};

BX.Notify.prototype.setStatus = function(status, send)
{
	send = send != false;
	if (this.BXIM.settings.status != status)
	{
		this.BXIM.settings.status = status;

		this.updateCounter();

		if (send)
		{
			this.BXIM.saveSettings({'status': status});
			BX.onCustomEvent(this, 'onNotifyStatusChange', [status]);
			BX.localStorage.set('nms', status, 5);
		}
	}
	if (this.desktop.ready())
		BX.desktop.setIconStatus(status);
};

BX.Notify.prototype.adjustPosition = function(params)
{
	if (this.desktop.run())
		return false;

	params = params || {};
	params.timeout = typeof(params.timeout) == "number"? parseInt(params.timeout): 0;

	clearTimeout(this.adjustPositionTimeout);
	this.adjustPositionTimeout = setTimeout(BX.delegate(function(){
		params.scroll = params.scroll || !BX.browser.IsDoctype();
		params.resize = params.resize || false;

		if (!this.windowScrollPos.scrollLeft)
			this.windowScrollPos = {scrollLeft : 0, scrollTop : 0};
		if (params.scroll)
			this.windowScrollPos = BX.GetWindowScrollPos();

		if (params.resize || !this.windowInnerSize.innerWidth)
		{
			this.windowInnerSize = BX.GetWindowInnerSize();

			if (this.BXIM.settings.panelPositionVertical == 'bottom' && typeof(window.scroll) == 'function' && !(BX.browser.IsAndroid() || BX.browser.IsIOS()))
			{
				if (typeof(window.scrollX) != 'undefined' && typeof(window.scrollY) != 'undefined')
				{
					var originalScrollLeft = window.scrollX;
					window.scroll(1, window.scrollY);
					this.windowInnerSize.innerHeight += window.scrollX == 1? -16: 0;
					window.scroll(originalScrollLeft, window.scrollY);
				}
				else
				{
					var scrollX = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft;
					var scrollY = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop;
					var originalScrollLeft = scrollX;
					window.scroll(1, scrollY);
					scrollX = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft;
					this.windowInnerSize.innerHeight += scrollX == 1? -16: 0;
					window.scroll(originalScrollLeft, scrollY);
				}
			}
		}

		if (params.scroll || params.resize)
		{
			if (this.BXIM.settings.panelPositionHorizontal == 'left')
				this.panel.style.left = (this.windowScrollPos.scrollLeft+25)+'px';
			else if (this.BXIM.settings.panelPositionHorizontal == 'center')
				this.panel.style.left = (this.windowScrollPos.scrollLeft+this.windowInnerSize.innerWidth-this.panel.offsetWidth)/2+'px';
			else if (this.BXIM.settings.panelPositionHorizontal == 'right')
				this.panel.style.left = (this.windowScrollPos.scrollLeft+this.windowInnerSize.innerWidth-this.panel.offsetWidth-35)+'px';

			if (this.BXIM.settings.panelPositionVertical == 'top')
			{
				this.panel.style.top = (this.windowScrollPos.scrollTop)+'px';
				if (BX.hasClass(this.panel, 'bx-notifier-panel-doc'))
					this.panel.className = 'bx-notifier-panel bx-notifier-panel-top bx-notifier-panel-doc';
				else
					this.panel.className = 'bx-notifier-panel bx-notifier-panel-top';
			}
			else if (this.BXIM.settings.panelPositionVertical == 'bottom')
			{
				if (BX.hasClass(this.panel, 'bx-notifier-panel-doc'))
					this.panel.className = 'bx-notifier-panel bx-notifier-panel-bottom bx-notifier-panel-doc';
				else
					this.panel.className = 'bx-notifier-panel bx-notifier-panel-bottom';

				this.panel.style.top = (this.windowScrollPos.scrollTop+this.windowInnerSize.innerHeight-this.panel.offsetHeight)+'px';
			}
		}
	},this), params.timeout);
};
BX.Notify.prototype.move = function(offsetX, offsetY)
{
	var left = parseInt(this.panel.style.left) + offsetX;
	var top = parseInt(this.panel.style.top) + offsetY;

	if (left < 0)
		left = 0;

	var scrollSize = BX.GetWindowScrollSize();
	var floatWidth = this.panel.offsetWidth;
	var floatHeight = this.panel.offsetHeight;

	if (left > (scrollSize.scrollWidth - floatWidth))
		left = scrollSize.scrollWidth - floatWidth;

	if (top > (scrollSize.scrollHeight - floatHeight))
		top = scrollSize.scrollHeight - floatHeight;

	if (top < 0)
		top = 0;

	this.panel.style.left = left + "px";
	this.panel.style.top = top + "px";
};
BX.Notify.prototype._startDrag = function(event)
{
	event = event || window.event;
	BX.fixEventPageXY(event);

	this.dragPageX = event.pageX;
	this.dragPageY = event.pageY;
	this.dragged = false;

	this.closePopup();

	BX.bind(document, "mousemove", BX.proxy(this._moveDrag, this));
	BX.bind(document, "mouseup", BX.proxy(this._stopDrag, this));

	if (document.body.setCapture)
		document.body.setCapture();

	document.body.ondrag = BX.False;
	document.body.onselectstart = BX.False;
	document.body.style.cursor = "move";
	document.body.style.MozUserSelect = "none";
	this.panel.style.MozUserSelect = "none";
	BX.addClass(this.panel, "bx-notifier-panel-drag-"+(this.BXIM.settings.panelPositionVertical == 'top'? 'top': 'bottom'));

	return BX.PreventDefault(event);
};

BX.Notify.prototype._moveDrag = function(event)
{
	event = event || window.event;
	BX.fixEventPageXY(event);

	if(this.dragPageX == event.pageX && this.dragPageY == event.pageY)
		return;

	this.move((event.pageX - this.dragPageX), (event.pageY - this.dragPageY));
	this.dragPageX = event.pageX;
	this.dragPageY = event.pageY;

	if (!this.dragged)
	{
		BX.onCustomEvent(this, "onPopupDragStart");
		this.dragged = true;
	}

	BX.onCustomEvent(this, "onPopupDrag");
};

BX.Notify.prototype._stopDrag = function(event)
{
	if(document.body.releaseCapture)
		document.body.releaseCapture();

	BX.unbind(document, "mousemove", BX.proxy(this._moveDrag, this));
	BX.unbind(document, "mouseup", BX.proxy(this._stopDrag, this));

	document.body.ondrag = null;
	document.body.onselectstart = null;
	document.body.style.cursor = "";
	document.body.style.MozUserSelect = "";
	this.panel.style.MozUserSelect = "";
	BX.removeClass(this.panel, "bx-notifier-panel-drag-"+(this.BXIM.settings.panelPositionVertical == 'top'? 'top': 'bottom'));
	BX.onCustomEvent(this, "onPopupDragEnd");

	var windowScrollPos = BX.GetWindowScrollPos();
	this.BXIM.settings.panelPositionVertical = (this.windowInnerSize.innerHeight/2 > (event.pageY - windowScrollPos.scrollTop||event.y))? 'top' : 'bottom';
	if (this.windowInnerSize.innerWidth/3 > (event.pageX- windowScrollPos.scrollLeft||event.x))
		this.BXIM.settings.panelPositionHorizontal = 'left';
	else if (this.windowInnerSize.innerWidth/3*2 < (event.pageX - windowScrollPos.scrollLeft||event.x))
		this.BXIM.settings.panelPositionHorizontal = 'right';
	else
		this.BXIM.settings.panelPositionHorizontal = 'center';

	this.BXIM.saveSettings({'panelPositionVertical': this.BXIM.settings.panelPositionVertical, 'panelPositionHorizontal': this.BXIM.settings.panelPositionHorizontal});

	BX.localStorage.set('npp', {v: this.BXIM.settings.panelPositionVertical, h: this.BXIM.settings.panelPositionHorizontal});

	this.adjustPosition({resize: true});

	this.dragged = false;

	return BX.PreventDefault(event);
};

BX.Notify.prototype.closePopup = function()
{
	if (this.popupNotifyMore != null)
		this.popupNotifyMore.destroy();
	if (this.messenger != null && this.messenger.popupPopupMenu != null)
		this.messenger.popupPopupMenu.destroy();
};

BX.Notify.prototype.createNotify = function(notify, popup)
{
	var element = false;
	if (!notify)
		return false;

	popup = popup == true;

	if (this.desktop.run())
	{
		notify.text = notify.text.replace(/<a(.*?)>(.*?)<\/a>/ig, function(whole, aInner, text)
		{
			return '<a' +aInner.replace('target="_self"', 'target="_blank"')+ '>'+text+'</a>';
		});
	}

	var itemNew = (this.unreadNotify[notify.id] && !popup? " bx-notifier-item-new": "");
	if (notify.type == 1 && typeof(notify.buttons) != "undefined" && notify.buttons.length > 0)
	{
		var arButtons = [];
		for (var i = 0; i < notify.buttons.length; i++)
		{
			var type = notify.buttons[i].TYPE == 'accept'? 'accept': 'cancel';
			var arAttr = { 'data-id' : notify.id, 'data-value' : notify.buttons[i].VALUE};
			if (notify.grouped)
				arAttr['data-group'] = 'Y';

			if (notify.buttons[i].URL)
				arAttr['data-url'] = notify.buttons[i].URL;

			arButtons.push(BX.create('span', {props : { className : "bx-notifier-item-button bx-notifier-item-button-"+type }, attrs : arAttr, html: '<i class="bx-notifier-item-button-fc"></i><span>'+notify.buttons[i].TITLE+'</span><i></i>'}));
		}
		element = BX.create("div", {attrs : {'data-notifyId' : notify.id, 'data-notifyType' : notify.type}, props : { className: "bx-notifier-item"+itemNew}, children : [
			BX.create('span', {props : { className : "bx-notifier-item-content" }, children : [
				notify.userAvatar ? BX.create('span', {props : { className : "bx-notifier-item-avatar" }, children : [
					BX.create('img', {props : { className : "bx-notifier-item-avatar-img" }, attrs : {src : notify.userAvatar}})
				]}): BX.create('span', {props : { className : "bx-notifier-item-avatar bx-messenger-avatar-notify" }}),
				BX.create("span", {props : { className: "bx-notifier-item-delete bx-notifier-item-delete-fake"}}),
				BX.create('span', {props : { className : "bx-notifier-item-date" }, html: BX.IM.formatDate(notify.date)}),
				notify.userName? BX.create('span', {props : { className : "bx-notifier-item-name" }, html: '<a href="'+notify.userLink+'" onclick="if (BXIM.init) { BXIM.openMessenger('+notify.userId+'); return false; } ">'+BX.IM.prepareText(notify.userName)+'</a>'}): null,
				BX.create('span', {props : { className : "bx-notifier-item-text" }, html: notify.text}),
				BX.create('span', {props : { className : "bx-notifier-item-button-wrap" }, children : arButtons})
			]})
		]});
	}
	else if (notify.type == 2 || (notify.type == 1 && typeof(notify.buttons) != "undefined" && notify.buttons.length <= 0))
	{
		element = BX.create("div", {attrs : {'data-notifyId' : notify.id, 'data-notifyType' : notify.type}, props : { className: "bx-notifier-item"+itemNew}, children : [
			BX.create('span', {props : { className : "bx-notifier-item-content" }, children : [
				BX.create('span', {props : { className : "bx-notifier-item-avatar" }, children : [
					BX.create('img', {props : { className : "bx-notifier-item-avatar-img" },attrs : {src : notify.userAvatar}})
				]}),
				BX.create("a", {attrs : {href : '#', 'data-notifyId' : notify.id, 'data-notifyType' : notify.type}, props : { className: "bx-notifier-item-delete"}}),
				BX.create('span', {props : { className : "bx-notifier-item-date" }, html: BX.IM.formatDate(notify.date)}),
				BX.create('span', {props : { className : "bx-notifier-item-name" }, html: '<a href="'+notify.userLink+'" onclick="if (BXIM.init) { BXIM.openMessenger('+notify.userId+'); return false; } ">'+BX.IM.prepareText(notify.userName)+'</a>'}),
				BX.create('span', {props : { className : "bx-notifier-item-text" }, html: notify.text})
			]})
		]});
	}
	else if (notify.type == 3)
	{
		element = BX.create("div", {attrs : {'data-notifyId' : notify.id, 'data-notifyType' : notify.type}, props : { className: "bx-notifier-item"+itemNew}, children : [
			BX.create('span', {props : { className : "bx-notifier-item-content" }, children : [
				BX.create('span', {props : { className : "bx-notifier-item-avatar bx-notifier-item-avatar-group" }, children : [
					BX.create('span', {props : { className : "bx-notifier-item-avatar" }, children : [
						BX.create('img', {props : { className : "bx-notifier-item-avatar-img" },attrs : {src : notify.userAvatar}})
					]})
				]}),
				BX.create("a", {attrs : {href : '#', 'data-notifyId' : notify.id, 'data-group' : 'Y', 'data-notifyType' : notify.type}, props : { className: "bx-notifier-item-delete"}}),
				BX.create('span', {props : { className : "bx-notifier-item-date" }, html: BX.IM.formatDate(notify.date)}),
				BX.create('span', {props : { className : "bx-notifier-item-name" }, html: BX.message('IM_NOTIFY_GROUP_NOTIFY').replace('#USER_NAME#', '<a href="'+notify.userLink+'" onclick="if (BXIM.init) { BXIM.openMessenger('+notify.userId+'); return false;} ">'+BX.IM.prepareText(notify.userName)+'</a>').replace('#U_START#', '<span class="bx-notifier-item-help" data-help="'+notify.id+'">').replace('#U_END#', '</span>').replace('#COUNT#', notify.otherCount)}),
				BX.create('span', {props : { className : "bx-notifier-item-text" }, html: notify.text})
			]})
		]});
	}
	else
	{
		element = BX.create("div", {attrs : {'data-notifyId' : notify.id}, props : { className: "bx-notifier-item"+itemNew}, children : [
			BX.create('span', {props : { className : "bx-notifier-item-content" }, children : [
				BX.create('span', {props : { className : "bx-notifier-item-avatar bx-messenger-avatar-notify" }}),
				BX.create("a", {attrs : {href : '#', 'data-notifyId' : notify.id, 'data-notifyType' : notify.type}, props : { className: "bx-notifier-item-delete"}}),
				BX.create('span', {props : { className : "bx-notifier-item-date" }, html: BX.IM.formatDate(notify.date)}),
				notify.title && notify.title.length>0? BX.create('span', {props : { className : "bx-notifier-item-name" }, html: BX.IM.prepareText(notify.title)}): null,
				BX.create('span', {props : { className : "bx-notifier-item-text" }, html: notify.text})
			]})
		]});
	}
	return element;
};

BX.Notify.prototype.storageSet = function(params)
{
	if (params.key == 'npp')
	{
		var panelPosition = BX.localStorage.get(params.key);
		this.BXIM.settings.panelPositionHorizontal = !!panelPosition? panelPosition.h: this.BXIM.settings.panelPositionHorizontal;
		this.BXIM.settings.panelPositionVertical = !!panelPosition? panelPosition.v: this.BXIM.settings.panelPositionVertical;
		this.adjustPosition({resize: true});
	}
	else if (params.key == 'nms')
	{
		this.setStatus(params.value, false);
	}
	else if (params.key == 'nun')
	{
		this.notify = params.value;
	}
	else if (params.key == 'nrn')
	{
		delete this.notify[params.value];
		this.updateNotifyCount(false);
	}
	else if (params.key == 'nrgn')
	{
		for (var i in this.notify)
		{
			if (this.notify[i].tag == params.value)
				delete this.notify[i];
		}
		this.updateNotifyCount();
	}
	else if (params.key == 'numc')
	{
		this.updateNotifyMailCount(params.value, false);
	}
	else if (params.key == 'nuc')
	{
		this.updateNotifyCounters(params.value, false);
	}
	else if (params.key == 'nunc')
	{
		setTimeout(BX.delegate(function(){
			this.unreadNotify = params.value.unread;
			this.flashNotify = params.value.flash;

			this.updateNotifyCount(false);
		},this), 500);
	}
	else if (params.key == 'nln')
	{
		this.loadNotify(false);
	}
};

})();


/* IM messenger class */
(function() {

if (BX.Messenger)
	return;

BX.Messenger = function(BXIM, params)
{
	this.BXIM = BXIM;
	this.settings = {};
	this.params = params || {};

	this.realSearch = !this.BXIM.bitrixIntranet;

	this.sendAjaxTry = 0;
	this.updateStateVeryFastCount = 0;
	this.updateStateFastCount = 0;
	this.updateStateStepDefault = this.BXIM.ppStatus? parseInt(params.updateStateInterval): 60;
	this.updateStateStep = this.updateStateStepDefault;
	this.updateStateTimeout = null;
	this.readMessageTimeout = null;
	this.readMessageTimeoutSend = null;

	this.webrtc = params.webrtcClass;
	this.notify = params.notifyClass;
	this.desktop = params.desktopClass;

	this.smile = params.smile;
	this.smileSet = params.smileSet;

	this.recentListIndex = [];
	if (params.recent)
	{
		this.recent = params.recent;
		this.recentListLoad = true;
	}
	else
	{
		this.recent = [];
		this.recentListLoad = false;
	}

	this.users = params.users;
	this.groups = params.groups;
	this.userInGroup = params.userInGroup;
	this.woGroups = params.woGroups;
	this.woUserInGroup = params.woUserInGroup;
	this.currentTab = params.currentTab;
	this.redrawTab = {};
	this.showMessage = params.showMessage;
	this.unreadMessage = params.unreadMessage;
	this.flashMessage = params.flashMessage;

	this.chat = params.chat;
	this.userInChat = params.userInChat;
	this.hrphoto = params.hrphoto;

	this.phones = {};

	this.message = params.message;
	this.messageTmpIndex = 0;
	this.history = params.history;
	this.textareaHistory = {};
	this.textareaHistoryTimeout = null;
	this.messageCount = params.countMessage;
	this.sendMessageFlag = 0;
	this.sendMessageTmp = {};
	this.sendMessageTmpTimeout = {};

	this.popupSettings = null;
	this.popupSettingsBody = null;

	this.popupChatDialog = null;
	this.popupChatDialogContactListElements = null;
	this.popupChatDialogContactListSearch = null;
	this.popupChatDialogDestElements = null;
	this.popupChatDialogUsers = {};
	this.popupChatDialogSendBlock = false;
	this.renameChatDialogFlag = false;
	this.renameChatDialogInput = null;

	this.popupKeyPad = null;

	this.popupHistory = null;
	this.popupHistoryElements = null;
	this.popupHistoryItems = null;
	this.popupHistoryItemsSize = 475;
	this.popupHistorySearchWrap = null;
	this.popupHistoryButtonDeleteAll = null;
	this.popupHistoryButtonFilter = null;
	this.popupHistoryButtonFilterBox = null;
	this.popupHistoryFilterVisible = true;
	this.popupHistoryBodyWrap = null;
	this.popupHistorySearchInput = null;
	this.historyUserId = 0;
	this.historySearch = '';
	this.historySearchBegin = false;
	this.historySearchTimeout = null;
	this.historyWindowBlock = false;
	this.historyOpenPage = {};
	this.historyMessageSplit = '------------------------------------------------------';
	this.historyEndOfList = {};
	this.historyLoadFlag = {};

	this.popupMessenger = null;
	this.popupMessengerWindow = {};
	this.popupMessengerExtra = null;
	this.popupMessengerTopLine = null;
	this.popupMessengerDesktopTimeout = null;
	this.popupMessengerFullWidth = 864;
	this.popupMessengerMinWidth = 864;
	this.popupMessengerFullHeight = 454;
	this.popupMessengerMinHeight = 454;
	this.popupMessengerDialog = null;
	this.popupMessengerBody = null;
	this.popupMessengerBodyAnimation = null;
	this.popupMessengerBodySize = 295;
	this.popupMessengerBodyWrap = null;

	this.popupMessengerPanel = null;
	this.popupMessengerPanelAvatar = null;
	this.popupMessengerPanelCall1 = null;
	this.popupMessengerPanelCall2 = null;
	this.popupMessengerPanelTitle = null;
	this.popupMessengerPanelStatus = null;

	this.popupMessengerPanel2 = null;
	this.popupMessengerPanel3 = null;
	this.popupMessengerPanelChatTitle = null;
	this.popupMessengerPanelUsers = null;

	this.popupMessengerTextareaPlace = null;
	this.popupMessengerTextarea = null;
	this.popupMessengerTextareaSendType = null;
	this.popupMessengerTextareaResize = {};
	this.popupMessengerTextareaSize = 43;
	this.popupMessengerLastMessage = "";
	this.readedList = {};
	this.writingList = {};
	this.writingListTimeout = {};
	this.writingSendList = {};
	this.writingSendListTimeout = {};

	this.contactListPanelStatus = null;
	this.contactListSearchText = '';

	this.popupPopupMenu = null;

	this.popupSmileMenu = null;
	this.popupSmileMenuGallery = null;
	this.popupSmileMenuSet = null;

	this.recentList = true;
	this.recentListReturn = false;
	this.recentListTab = null;
	this.recentListTabCounter = null;

	this.contactList = false;
	this.contactListTab = null;

	this.openMessengerFlag = false;
	this.openChatFlag = false;
	this.openCallFlag = false;

	this.contactListLoad = false;
	this.popupContactListSize = 254;
	this.popupContactListSearchInput = null;
	this.popupContactListSearchClose = null;
	this.popupContactListWrap = null;
	this.popupContactListElements = null;
	this.popupContactListElementsSize = this.desktop.run()? 319: 279;
	this.popupContactListElementsWrap = null;
	this.contactListPanelSettings = null;

	this.enableGroupChat = this.BXIM.ppStatus? true: false;

	if (this.BXIM.init)
	{
		if (this.desktop.run())
		{
			BX.desktop.addTab({
				id: 'im',
				title: BX.message('IM_DESKTOP_OPEN_MESSENGER').replace('#COUNTER#', ''),
				order: 100,
				events: {
					open: BX.delegate(function(){
						if (!this.BXIM.dialogOpen)
							this.openMessenger(this.currentTab);
					}, this)
				}
			});
			if (this.webrtc.phoneSupport())
			{
				BX.desktop.addTab({
					id: 'im-phone',
					title: BX.message('IM_PHONE_DESC'),
					order: 120,
					target: 'im',
					events: {
						open: BX.delegate(this.webrtc.openKeyPad, this.webrtc),
						close: BX.delegate(function(){
							if (this.webrtc.popupKeyPad)
								this.webrtc.popupKeyPad.close();
						}, this)
					}
				});
			}
		}

		this.notify.panel.appendChild(this.BXIM.audio.newMessage1 = BX.create("audio", { props : { className : "bx-messenger-audio" }, children : [
			BX.create("source", { attrs : { src : "/bitrix/js/im/audio/new-message-1.ogg", type : "audio/ogg; codecs=vorbis" }}),
			BX.create("source", { attrs : { src : "/bitrix/js/im/audio/new-message-1.mp3", type : "audio/mpeg" }})
		]}));
		this.notify.panel.appendChild(this.BXIM.audio.newMessage2 = BX.create("audio", { props : { className : "bx-messenger-audio" }, children : [
			BX.create("source", { attrs : { src : "/bitrix/js/im/audio/new-message-2.ogg", type : "audio/ogg; codecs=vorbis" }}),
			BX.create("source", { attrs : { src : "/bitrix/js/im/audio/new-message-2.mp3", type : "audio/mpeg" }})
		]}));
		this.notify.panel.appendChild(this.BXIM.audio.send = BX.create("audio", { props : { className : "bx-messenger-audio" }, children : [
			BX.create("source", { attrs : { src : "/bitrix/js/im/audio/send.ogg", type : "audio/ogg; codecs=vorbis" }}),
			BX.create("source", { attrs : { src : "/bitrix/js/im/audio/send.mp3", type : "audio/mpeg" }})
		]}));
		if (typeof(this.BXIM.audio.send.play) == 'undefined')
		{
			this.BXIM.settings.enableSound = false;
		}

		for (var i in this.unreadMessage)
		{
			if (typeof (this.flashMessage[i]) == 'undefined')
				this.flashMessage[i] = {};
			for (var k = this.unreadMessage[i].length - 1; k >= 0; k--)
			{
				BX.localStorage.set('mum', {'userId': i, 'message': this.message[this.unreadMessage[i][k]]}, 5);
			}
		}
		BX.localStorage.set('muum', this.unreadMessage, 5);

		BX.bind(this.notify.panelButtonMessage, "click", BX.delegate(function(){
			if (this.BXIM.messageCount <= 0)
				this.BXIM.toggleMessenger()
			else
				this.BXIM.openMessenger();
		}, this));

		var mtabs = this.BXIM.getLocalConfig('msz3', false);
		if (mtabs)
		{
			this.popupMessengerFullWidth = parseInt(mtabs.wz);
			this.popupMessengerTextareaSize = parseInt(mtabs.ta);
			this.popupMessengerBodySize = parseInt(mtabs.b) > 0? parseInt(mtabs.b): this.popupMessengerBodySize;
			this.popupHistoryItemsSize = parseInt(mtabs.hi);
			this.popupMessengerFullHeight = parseInt(mtabs.fz);
			this.popupContactListElementsSize = parseInt(mtabs.ez);
			this.notify.popupNotifySize = parseInt(mtabs.nz);
			this.popupHistoryFilterVisible = mtabs.hf;
			if (this.desktop.ready())
			{
				BX.desktop.setWindowSize({ Width: parseInt(mtabs.dw), Height: parseInt(mtabs.dh) })
				this.desktop.initHeight = parseInt(mtabs.dh);
			}
		}
		else
		{
			if (this.desktop.ready())
			{
				BX.desktop.setWindowSize({ Width: BX.desktop.minWidth, Height: BX.desktop.minHeight });
				this.desktop.initHeight = BX.desktop.minHeight;
			}
		}
		if (this.desktop.ready())
		{
			BX.bind(window, "resize", BX.delegate(function(){
				this.adjustSize()
			}, this.desktop));
		}


		if (BX.browser.SupportLocalStorage())
		{
			var mcr = BX.localStorage.get('mcr2');
			if (mcr)
			{
				for (var i in mcr.users)
					this.users[i] = mcr.users[i];

				for (var i in mcr.hrphoto)
					this.hrphoto[i] = mcr.hrphoto[i];

				for (var i in mcr.chat)
					this.chat[i] = mcr.chat[i];

				for (var i in mcr.userInChat)
					this.userInChat[i] = mcr.userInChat[i];

				this.callInit = true;
				setTimeout(BX.delegate(function(){
					this.webrtc.callNotifyWait(mcr.callChatId, mcr.callUserId, mcr.callVideo, mcr.callToGroup);
				}, this), 500);
			}
			BX.addCustomEvent(window, "onLocalStorageSet", BX.delegate(this.storageSet, this));
			BX.addCustomEvent(this.notify, "onNotifyStatusChange", BX.delegate(this.setStatus, this));
			this.textareaHistory = BX.localStorage.get('mtah') || {};
			this.currentTab = BX.localStorage.get('mct') || this.currentTab;
			this.contactListSearchText = BX.localStorage.get('mcls') != null?  BX.localStorage.get('mcls')+'': '';
			this.messageTmpIndex = BX.localStorage.get('mti') || 0;
			var mfm = BX.localStorage.get('mfm');
			if (mfm)
			{
				for (var i in this.flashMessage)
					for (var j in this.flashMessage[i])
						if (mfm[i] && this.flashMessage[i][j] != mfm[i][j] && mfm[i][j] == false)
							this.flashMessage[i][j] = false;
			}

			BX.garbage(function(){
				BX.localStorage.set('mti', this.messageTmpIndex, 15);
				BX.localStorage.set('mtah', this.textareaHistory, 15);
				BX.localStorage.set('mct', this.currentTab, 15);
				BX.localStorage.set('mfm', this.flashMessage, 15);
				BX.localStorage.set('mcls', this.contactListSearchText+'', 15);

				this.BXIM.setLocalConfig('mtah2', this.textareaHistory);

				if (this.desktop.ready() && (window.innerWidth < BX.desktop.minWidth || window.innerHeight < BX.desktop.minHeight))
					return false;

				this.BXIM.setLocalConfig('msz3', {
					'wz': this.popupMessengerFullWidth,
					'ta': this.popupMessengerTextareaSize,
					'b': this.popupMessengerBodySize,
					'cl': this.popupContactListSize,
					'hi': this.popupHistoryItemsSize,
					'fz': this.popupMessengerFullHeight,
					'ez': this.popupContactListElementsSize,
					'nz': this.notify.popupNotifySize,
					'hf': this.popupHistoryFilterVisible,
					'dw': window.innerWidth,
					'dh': window.innerHeight,
					'place': 'garbage'
				});

			}, this);
		}
		else
		{
			var mtah = this.BXIM.getLocalConfig('mtah', false);
			if (mtah)
			{
				this.textareaHistory = mtah;
				this.BXIM.removeLocalConfig('mtah');
			}
			var mct = this.BXIM.getLocalConfig('mct', false);
			if (mct)
			{
				this.currentTab = mct;
				this.BXIM.removeLocalConfig('mct');
			}

			BX.garbage(function(){
				this.BXIM.setLocalConfig('mct', this.currentTab);
				this.BXIM.setLocalConfig('mtah', this.textareaHistory);

				if (this.desktop.ready() && (window.innerWidth < BX.desktop.minWidth || window.innerHeight < BX.desktop.minHeight))
					return false;

				this.BXIM.setLocalConfig('msz3', {
					'wz': this.popupMessengerFullWidth,
					'ta': this.popupMessengerTextareaSize,
					'b': this.popupMessengerBodySize,
					'cl': this.popupContactListSize,
					'hi': this.popupHistoryItemsSize,
					'fz': this.popupMessengerFullHeight,
					'ez': this.popupContactListElementsSize,
					'nz': this.notify.popupNotifySize,
					'hf': this.popupHistoryFilterVisible,
					'dw': window.innerWidth,
					'dh': window.innerHeight,
					'place': 'garbage'
				});
			}, this);
		}
		BX.addCustomEvent("onPullEvent-im", BX.delegate(function(command,params) {
			if (command == 'desktopOffline')
			{
				this.BXIM.desktopStatus = false;
			}
			else if (command == 'desktopOnline')
			{
				this.BXIM.desktopStatus = true;
			}
			else if (command == 'readMessage')
			{
				this.readMessage(params.userId, false, false);
			}
			else if (command == 'readMessageChat')
			{
				this.readMessage('chat'+params.chatId, false, false);
			}
			else if (command == 'readMessageApponent')
			{
				params.date = parseInt(params.date)+parseInt(BX.message('USER_TZ_OFFSET'));
				this.drawReadMessage(params.userId, params.lastId, params.date);
			}
			else if (command == 'startWriting')
			{
				this.startWriting(params.senderId, params.dialogId);
			}
			else if (command == 'readNotify')
			{
				this.notify.initNotifyCount = 0;
				params.lastId = parseInt(params.lastId);
				for (var i in this.notify.unreadNotify)
				{
					var notify = this.notify.notify[this.notify.unreadNotify[i]];
					if (notify && notify.type != 1 && notify.id <= params.lastId)
					{
						delete this.notify.unreadNotify[i];
					}
				}
				this.notify.updateNotifyCount(false);
			}
			else if (command == 'confirmNotify')
			{
				var notifyId = parseInt(params.id);
				delete this.notify.notify[notifyId];
				delete this.notify.unreadNotify[notifyId];
				delete this.notify.flashNotify[notifyId];
				this.notify.updateNotifyCount(false);
				if (this.BXIM.messenger.popupMessenger != null && this.BXIM.notifyOpen)
					this.notify.openNotify(true);
			}
			else if (command == 'readNotifyOne')
			{
				var notify = this.notify.notify[params.id];
				if (notify && notify.type != 1)
					delete this.notify.unreadNotify[params.id];

				this.notify.updateNotifyCount(false);
				if (this.BXIM.messenger.popupMessenger != null && this.BXIM.notifyOpen)
					this.notify.openNotify(true);

			}
			else if (command == 'message' || command == 'messageChat')
			{
				if (this.BXIM.lastRecordId >= params.MESSAGE.id)
					return false;

				var data = {};
				data.MESSAGE = {};
				data.USERS_MESSAGE = {};
				params.MESSAGE.date = parseInt(params.MESSAGE.date)+parseInt(BX.message('USER_TZ_OFFSET'));
				for (var i in params.CHAT)
				{
					if (this.chat[i] && this.chat[i].fake)
						params.CHAT[i].fake = true;
					else if (!this.chat[i])
						params.CHAT[i].fake = true;

					this.chat[i] = params.CHAT[i];
				}
				for (var i in params.USER_IN_CHAT)
				{
					this.userInChat[i] = params.USER_IN_CHAT[i];
				}
				var userChangeStatus = {};
				for (var i in params.USERS)
				{
					if (this.users[i] && this.users[i].status != params.USERS[i].status && params.MESSAGE.date+180 > BX.IM.getNowDate())
					{
						userChangeStatus[i] = this.users[i].status;
						this.users[i].status = params.USERS[i].status;
					}
				}
				for (var i in userChangeStatus)
				{
					if (!this.users[i])
						continue;

					var elements = BX.findChildren(this.popupContactListElementsWrap, {attribute: {'data-userId': ''+i+''}}, true);
					if (elements != null)
					{
						for (var j = 0; j < elements.length; j++)
						{
							BX.removeClass(elements[j], 'bx-messenger-cl-status-'+userChangeStatus[i]);
							BX.addClass(elements[j], 'bx-messenger-cl-status-'+this.users[i].status);
							elements[j].setAttribute('data-status', this.users[i].status);
						}
					}
				}
				elements = null;

				data.USERS = params.USERS;

				data.MESSAGE[params.MESSAGE.id] = params.MESSAGE;
				this.BXIM.lastRecordId = params.MESSAGE.id;

				if (params.MESSAGE.senderId == this.BXIM.userId)
				{
					if (this.sendMessageFlag > 0 || this.message[params.MESSAGE.id])
						return;

					this.readMessage(params.MESSAGE.recipientId, false, false);

					data.USERS_MESSAGE[params.MESSAGE.recipientId] = [params.MESSAGE.id];
					this.updateStateVar(data);

					this.recentListAdd({
						'userId': params.MESSAGE.recipientId,
						'id': params.MESSAGE.id,
						'date': params.MESSAGE.date+parseInt(BX.message("SERVER_TZ_OFFSET")),
						'recipientId': params.MESSAGE.recipientId,
						'senderId': params.MESSAGE.senderId,
						'text': params.MESSAGE.text
					}, true);
				}
				else
				{
					data.UNREAD_MESSAGE = {};
					data.UNREAD_MESSAGE[command == 'messageChat'? params.MESSAGE.recipientId: params.MESSAGE.senderId] = [params.MESSAGE.id];
					data.USERS_MESSAGE[command == 'messageChat'?params.MESSAGE.recipientId: params.MESSAGE.senderId] = [params.MESSAGE.id];
					if (command == 'message')
						this.endWriting(params.MESSAGE.senderId);
					else
						this.endWriting(params.MESSAGE.senderId, params.MESSAGE.recipientId);

					this.updateStateVar(data);

					this.recentListAdd({
						'userId': command == 'messageChat'? params.MESSAGE.recipientId: params.MESSAGE.senderId,
						'id': params.MESSAGE.id,
						'date': params.MESSAGE.date+parseInt(BX.message("SERVER_TZ_OFFSET")),
						'recipientId': params.MESSAGE.recipientId,
						'senderId': params.MESSAGE.senderId,
						'text': params.MESSAGE.text
					}, true);
				}
				BX.localStorage.set('mfm', this.flashMessage, 80);
			}
			else if (command == 'chatRename')
			{
				if (this.chat[params.chatId])
				{
					this.chat[params.chatId].name = params.chatTitle;
					this.redrawChatHeader();
				}
			}
			else if (command == 'chatUserAdd')
			{
				for (var i in params.users)
					this.users[i] = params.users[i];

				if (!this.chat[params.chatId])
				{
					this.chat[params.chatId] = {'id': params.chatId, 'name': params.chatId, 'owner': params.chatOwner, 'fake': true};
				}
				else
				{
					if (this.userInChat[params.chatId])
					{
						for (i = 0; i < params.newUsers.length; i++)
							this.userInChat[params.chatId].push(params.newUsers[i]);
					}
					else
						this.userInChat[params.chatId] = params.newUsers;

					this.redrawChatHeader();
				}
			}
			else if (command == 'chatUserLeave')
			{
				if (params.userId == this.BXIM.userId)
				{
					this.readMessage('chat'+params.chatId, true, false);
					this.leaveFromChat(params.chatId, false);
					if (params.message.length > 0)
						this.BXIM.openConfirm({title: BX.util.htmlspecialchars(params.chatTitle), message: params.message});
				}
				else
				{
					if (!this.chat[params.chatId] || !this.userInChat[params.chatId])
						return false;

					var newStack = [];
					for (var i = 0; i < this.userInChat[params.chatId].length; i++)
						if (this.userInChat[params.chatId][i] != params.userId)
							newStack.push(this.userInChat[params.chatId][i]);

					this.userInChat[params.chatId] = newStack;
					this.redrawChatHeader();
				}
			}
			else if (command == 'notify')
			{
				if (this.BXIM.lastRecordId >= params.id)
					return false;

				params.date = parseInt(params.date)+parseInt(BX.message('USER_TZ_OFFSET'));
				var data = {};
				data.UNREAD_NOTIFY = {};
				data.UNREAD_NOTIFY[params.id] = [params.id];
				this.notify.notify[params.id] = params;
				this.notify.flashNotify[params.id] = params.silent != 'Y';
				if (params.silent == 'N')
					this.notify.changeUnreadNotify(data.UNREAD_NOTIFY);
				BX.localStorage.set('mfn', this.notify.flashNotify, 80);
				this.BXIM.lastRecordId = params.id;
			}

		}, this));

		BX.addCustomEvent("onPullOnlineEvent", BX.delegate(function(command,params)
		{
			if (command == 'user_authorize')
			{
				if (this.users[params.USER_ID])
				{
					if (this.users[params.USER_ID].status != 'online')
					{
						this.users[params.USER_ID].status = 'online';
						this.dialogStatusRedraw();
						this.userListRedraw();
					}
				}
			}
			else if (command == 'user_logout')
			{
				if (this.users[params.USER_ID])
				{
					if (this.users[params.USER_ID].status != 'offline')
					{
						this.users[params.USER_ID].status = 'offline';
						this.dialogStatusRedraw();
						this.userListRedraw();
					}
				}
			}
			else if (command == 'online_list')
			{
				var contactListRedraw = false;
				var userChangeStatus = {};
				for (var i in this.users)
				{
					if (typeof(params.USERS[i]) == 'undefined')
					{
						if (this.users[i].status != 'offline')
						{
							userChangeStatus[i] = this.users[i].status;
							this.users[i].status = 'offline';
							contactListRedraw = true;
						}
					}
					else
					{
						if (this.users[i].status != params.USERS[i].status)
						{
							userChangeStatus[i] = this.users[i].status;
							this.users[i].status = params.USERS[i].status;
							contactListRedraw = true;
						}
					}
				}
				if (contactListRedraw)
				{
					this.dialogStatusRedraw();
					this.userListRedraw();
				}
			}

		}, this));

		BX.addCustomEvent("onPullError", BX.delegate(function(error) {
			if (error == 'AUTHORIZE_ERROR')
				this.sendAjaxTry++;
		}, this));

		for(var userId in this.users)
		{
			if (this.users[userId].birthday && userId != this.BXIM.userId)
			{
				this.message[userId+'birthday'] = {'id' : userId+'birthday', 'senderId' : 0, 'recipientId' : userId, 'date' : BX.IM.getNowDate(true), 'text' : BX.message('IM_M_BIRTHDAY_MESSAGE').replace('#USER_NAME#', '<img src="/bitrix/js/im/images/blank.gif" class="bx-messenger-birthday-icon"><strong>'+this.users[userId].name+'</strong>') };
				if (!this.showMessage[userId])
					this.showMessage[userId] = [];
				this.showMessage[userId].push(userId+'birthday');
				this.showMessage[userId].sort(BX.delegate(function(i, ii) {if (!this.message[i] || !this.message[ii]){return 0;} var i1 = parseInt(this.message[i].date); var i2 = parseInt(this.message[ii].date); if (i1 < i2) { return -1; } else if (i1 > i2) { return 1;} else{ if (i < ii) { return -1; } else if (i > ii) { return 1;}else{ return 0;}}}, this));

				var messageLastId = this.showMessage[userId][this.showMessage[userId].length-1];
				this.recentListAdd({
					'userId': userId,
					'id': this.message[messageLastId].id,
					'date': this.message[messageLastId].date-parseInt(BX.message('USER_TZ_OFFSET')),
					'recipientId': this.message[messageLastId].recipientId,
					'senderId': this.message[messageLastId].senderId,
					'text': messageLastId == userId+'birthday'? BX.message('IM_M_BIRTHDAY_MESSAGE_SHORT').replace('#USER_NAME#', this.users[userId].name): this.message[messageLastId].text
				}, true);
				this.recent.sort(BX.delegate(function(i, ii) {if (!this.message[i.id] || !this.message[ii.id]){return 0;} var i1 = parseInt(this.message[i.id].date); var i2 = parseInt(this.message[ii.id].date); if (i1 > i2) { return -1; } else if (i1 < i2) { return 1;} else{ if (i > ii) { return -1; } else if (i < ii) { return 1;}else{ return 0;}}}, this));

				var birthdayList = this.BXIM.getLocalConfig('birthdayPopup'+((new Date).getFullYear()), {});
				if (this.desktop.birthdayStatus() && !birthdayList[userId])
				{
					this.message[userId+'birthdayPopup'] = {'id' : userId+'birthdayPopup', 'senderId' : 0, 'recipientId' : userId, 'date' : BX.IM.getNowDate(true), 'text' : BX.message('IM_M_BIRTHDAY_MESSAGE_SHORT').replace('#USER_NAME#', this.users[userId].name) };
					if (this.desktop.ready())
					{
						if (!this.unreadMessage[userId])
							this.unreadMessage[userId] = [];
						this.unreadMessage[userId].push(userId+'birthdayPopup');

						if (!this.flashMessage[userId])
							this.flashMessage[userId] = {};
						this.flashMessage[userId][userId+'birthdayPopup'] = true;
					}
					birthdayList[userId] = true;
					this.BXIM.removeLocalConfig('birthdayPopup'+((new Date).getFullYear()-1));
					this.BXIM.setLocalConfig('birthdayPopup'+((new Date).getFullYear()), birthdayList);
				}
			}
		}

		this.updateState();
		if (params.openMessenger !== false)
			this.openMessenger(params.openMessenger);
		else if (this.openMessengerFlag)
			this.openMessenger(this.currentTab);

		if (params.openHistory !== false)
			this.openHistory(params.openHistory);
		if (params.openNotify !== false)
			this.BXIM.openNotify();

		if (this.BXIM.settings.status != 'dnd')
			this.newMessage();

		this.updateMessageCount();
	}
	else
	{
		if (params.openMessenger !== false)
			this.BXIM.openMessenger(params.openMessenger);
		if (params.openHistory !== false)
			this.BXIM.openHistory(params.openHistory);
	}
};

BX.Messenger.prototype.openMessenger = function(userId)
{
	if (this.BXIM.errorMessage != '')
	{
		this.BXIM.openConfirm(this.BXIM.errorMessage);
		return false;
	}
	if (this.BXIM.popupSettings != null && !this.desktop.run())
		this.BXIM.popupSettings.close();

	if (this.popupMessenger != null && this.dialogOpen && this.currentTab == userId && userId != 0)
		return false;

	if (userId == this.BXIM.userId)
	{
		this.currentTab = 0;
		userId = 0;
	}

	BX.localStorage.set('mcam', true, 5);
	if (typeof(userId) == "undefined" || userId == null)
		userId = 0;

	if (this.currentTab == null)
		this.currentTab = 0;

	this.openChatFlag = false;
	this.openCallFlag = false;
	var setSearchFocus = false;
	if (typeof(userId) == "boolean")
	{
		userId = 0;
	}
	else if (userId == 0)
	{
		setSearchFocus = true;
		for (var i in this.unreadMessage)
		{
			userId = i;
			setSearchFocus = false;
			break;
		}
		if (userId == 0 && this.currentTab != null)
		{
			if (this.users[this.currentTab] && this.users[this.currentTab].id)
				userId = this.currentTab;
			else if (this.chat[this.currentTab.toString().substr(4)] && this.chat[this.currentTab.toString().substr(4)].id)
				userId = this.currentTab;
		}
		if (userId.toString().substr(0,4) == 'chat')
		{
			if (!(this.chat[userId.toString().substr(4)] && this.chat[userId.substr(4)].id))
				this.chat[userId.toString().substr(4)] = {'id': userId.toString().substr(4), 'name': BX.message('IM_M_LOAD_USER'), 'owner': 0, 'style': 'group', 'fake': true};

			this.openChatFlag = true;
			if (this.chat[userId.toString().substr(4)].style == 'call')
				this.openCallFlag = true;
		}
		else
		{
			userId = parseInt(userId);
		}
	}
	else if (userId.toString().substr(0,4) == 'chat')
	{
		if (!(this.chat[userId.toString().substr(4)] && this.chat[userId.toString().substr(4)].id))
			this.chat[userId.toString().substr(4)] = {'id': userId.substr(4), 'name': BX.message('IM_M_LOAD_USER'), 'owner': 0, 'fake': true};

		this.openChatFlag = true;
		if (this.chat[userId.toString().substr(4)].style == 'call')
			this.openCallFlag = true;
	}
	else if (this.users[userId] && this.users[userId].id)
	{
		userId = parseInt(userId);
	}
	else
	{
		userId = parseInt(userId);
		if (isNaN(userId))
		{
			userId = 0;
		}
		else
		{
			this.users[userId] = {'id': userId, 'avatar': '/bitrix/js/im/images/blank.gif', 'name': BX.message('IM_M_LOAD_USER'), 'profile': this.BXIM.path.profileTemplate.replace('#user_id#', userId), 'status': 'guest', 'fake': true};
			this.hrphoto[userId] = '/bitrix/js/im/images/hidef-avatar.png';
		}
	}

	if (!this.openChatFlag && typeof(userId) != 'number')
		userId = 0;

	if (this.openChatFlag || userId > 0)
	{
		this.currentTab = userId;
		this.BXIM.notifyManager.closeByTag('im-message-'+userId);
		BX.localStorage.set('mct', this.currentTab, 15);
	}

	if (this.desktop.run() && BX.desktop.currentTab != 'im')
	{
		BX.desktop.changeTab('im');
	}

	if (this.popupMessenger != null)
	{
		this.openDialog(userId, this.BXIM.dialogOpen? false: true);

		if (!(BX.browser.IsAndroid() || BX.browser.IsIOS()))
		{
			if (setSearchFocus && this.popupContactListSearchInput != null)
				this.popupContactListSearchInput.focus();
			else
				this.popupMessengerTextarea.focus();
		}
		return false;
	}


	var styleOfContent = {width: this.popupMessengerFullWidth+'px'};
	if (this.desktop.run())
	{
		styleOfContent = {};
		if (!BX.desktop.contentFullWindow)
		{
			var newHeight = BX.desktop.content.offsetHeight - this.popupMessengerFullHeight;
			this.popupContactListElementsSize = this.popupContactListElementsSize + newHeight;
			this.popupMessengerBodySize = this.popupMessengerBodySize + newHeight;
			this.popupMessengerFullHeight = this.popupMessengerFullHeight + newHeight;
			this.notify.popupNotifySize = this.notify.popupNotifySize + newHeight;
		}
	}

	this.popupMessengerContent = BX.create("div", { props : { className : "bx-messenger-box"+(this.webrtc.callInit? ' bx-messenger-call': '') }, style: styleOfContent, children : [
		/* CL */
		this.popupContactListWrap = BX.create("div", { props : { className : "bx-messenger-box-contact" }, style : {width: this.popupContactListSize+'px'},  children : [
			BX.create('div', {props : { className : "bx-messenger-cl-switcher" }, children: [BX.create('div', {props : { className : "bx-messenger-cl-switcher-wrap" }, children: [
				this.contactListTab = BX.create('span', {props : { className : "bx-messenger-cl-switcher-tab bx-messenger-cl-switcher-tab-cl"}, children: [BX.create('div', {props : { className : "bx-messenger-cl-switcher-tab-wrap"}, html: BX.message('IM_CL_TAB_LIST')})]}),
				this.recentListTab = BX.create('span', {props : { className : "bx-messenger-cl-switcher-tab bx-messenger-cl-switcher-tab-recent"}, children: [
					BX.create('div', {props : { className : "bx-messenger-cl-switcher-tab-wrap"}, children: [
						this.recentListTabCounter = BX.create('span', {props : { className : "bx-messenger-cl-count bx-messenger-cl-switcher-tab-count"}, html: this.messageCount>0? '<span class="bx-messenger-cl-count-digit">'+(this.messageCount<100? this.messageCount: '99+')+'</span>': ''}),
						BX.create('div', {props : { className : "bx-messenger-cl-switcher-tab-text"}, html: BX.message('IM_CL_TAB_RECENT')})
					]})
				]})
			]})]}),
			BX.create("div", { props : { className : "bx-messenger-input-search"+(this.webrtc.phoneEnabled && !this.desktop.run()? ' bx-messenger-input-search-phone': '') }, children : [
				this.popupContactListSearchCall = BX.create("span", {props : { className : "bx-messenger-cl-switcher-tab-wrap bx-messenger-input-search-call" }, html: '<span class="bx-messenger-input-search-call-icon"></span>'}),
				BX.create("div", { props : { className : "bx-messenger-input-wrap bx-messenger-cl-search-wrap" }, children : [
					this.popupContactListSearchClose = BX.create("a", {attrs: {href: "#close"}, props : { className : "bx-messenger-input-close" }}),
					this.popupContactListSearchInput = BX.create("input", {attrs: {type: "text", placeholder: BX.message('IM_M_SEARCH_PLACEHOLDER'), value: this.contactListSearchText}, props : { className : "bx-messenger-input" }})
				]})
			]}),
			this.popupContactListElements = BX.create("div", { props : { className : "bx-messenger-cl" }, style : {height: this.popupContactListElementsSize+'px'}, children : [
				this.popupContactListElementsWrap = BX.create("div", { props : { className : "bx-messenger-cl-wrap" }})
			]}),
			this.desktop.run()? null: BX.create('div', {props : { className : "bx-messenger-cl-notify-wrap" }, children : [
				this.notify.messengerNotifyButton = BX.create("div", { props : { className : "bx-messenger-cl-notify-button"}, events : { click : BX.delegate(this.notify.openNotify, this.notify)}, children : [
					BX.create('span', {props : { className : "bx-messenger-cl-notify-text"}, html: BX.message('IM_NOTIFY_BUTTON_TITLE')}),
					this.notify.messengerNotifyButtonCount = BX.create('span', { props : { className : "bx-messenger-cl-count" }, html: parseInt(this.notify.notifyCount)>0? '<span class="bx-messenger-cl-count-digit">'+this.notify.notifyCount+'</span>':''})
				]})
			]}),
			BX.create('div', {props : { className : "bx-messenger-cl-panel" }, children : [ BX.create('div', {props : { className : "bx-messenger-cl-panel-wrap" }, children : [
				this.contactListPanelStatus = BX.create("span", { props : { className : "bx-messenger-cl-panel-status-wrap bx-messenger-cl-panel-status-"+this.BXIM.settings.status }, html: '<span class="bx-messenger-cl-panel-status"></span><span class="bx-messenger-cl-panel-status-text">'+BX.message("IM_STATUS_"+this.BXIM.settings.status.toUpperCase())+'</span><span class="bx-messenger-cl-panel-status-arrow"></span>'}),
				BX.create('span', {props : { className : "bx-messenger-cl-panel-right-wrap" }, children : [
					this.contactListPanelSettings = this.desktop.run()? null: BX.create("span", { props : { title : BX.message("IM_SETTINGS"), className : "bx-messenger-cl-panel-settings-wrap"}})
				]})
			]}) ]})
		]}),
		/* DIALOG */
		this.popupMessengerDialog = BX.create("div", { props : { className : "bx-messenger-box-dialog" }, style : {marginLeft: this.popupContactListSize+'px'},  children : [
			this.popupMessengerPanel = BX.create("div", { props : { className : "bx-messenger-panel"+(this.openChatFlag? ' bx-messenger-hide': '') }, children : [
				BX.create('a', { attrs : { href : this.users[this.currentTab]? this.users[this.currentTab].profile: this.BXIM.userParams.profile}, props : { className : "bx-messenger-panel-avatar bx-messenger-panel-avatar-status-"+(this.users[this.currentTab]? this.users[this.currentTab].status: this.BXIM.settings.status) }, children: [
					this.popupMessengerPanelAvatar = BX.create('img', { attrs : { src : this.users[this.currentTab]? this.users[this.currentTab].avatar: '/bitrix/js/im/images/blank.gif'}, props : { className : "bx-messenger-panel-avatar-img" }}),
					BX.create('span', { props : { className : "bx-messenger-panel-avatar-status" }})
				]}),
				BX.create("a", {attrs: {href: "#history", title: BX.message("IM_M_OPEN_HISTORY_2")}, props : { className : "bx-messenger-panel-history"}, events : { click: BX.delegate(function(e){ this.openHistory(this.currentTab); BX.PreventDefault(e)}, this)}}),
				this.popupMessengerPanelCall1 = this.callButton(),
				this.enableGroupChat? BX.create("a", {attrs: {href: "#chat", title: BX.message("IM_M_CHAT_TITLE")}, props : { className : "bx-messenger-panel-chat"}, events : { click: BX.delegate(function(e){ this.openChatDialog({'type': 'CHAT_ADD', 'bind': BX.proxy_context}); BX.PreventDefault(e)}, this)}}): null,
				BX.create("span", { props : { className : "bx-messenger-panel-title"}, children: [
					this.popupMessengerPanelTitle = BX.create('a', { props : { className : "bx-messenger-panel-title-link"}, attrs : { href : this.users[this.currentTab]? this.users[this.currentTab].profile: this.BXIM.userParams.profile}, html: this.users[this.currentTab]? this.users[this.currentTab].name: ''})
				]}),
				this.popupMessengerPanelStatus = BX.create("span", { props : { className : "bx-messenger-panel-desc"}, html: BX.message("IM_STATUS_"+(this.users[this.currentTab]? this.users[this.currentTab].status: this.BXIM.settings.status).toUpperCase())})
			]}),
			this.popupMessengerPanel2 = BX.create("div", { props : { className : "bx-messenger-panel"+(this.openChatFlag && !this.openCallFlag? '': ' bx-messenger-hide') }, children : [
				this.popupMessengerPanelAvatar2 = BX.create('img', { attrs : { src : this.chat[this.currentTab.toString().substr(4)] && this.chat[this.currentTab.toString().substr(4)].avatar? this.chat[this.currentTab.toString().substr(4)].avatar: '/bitrix/js/im/images/blank.gif'}, props : { className : "bx-messenger-panel-avatar bx-messenger-panel-avatar-chat" }}),
				this.popupMessengerPanelCall2 = this.callButton(),
				this.enableGroupChat? BX.create("a", {attrs: {href: "#chat", title: BX.message("IM_M_CHAT_TITLE")}, props : { className : "bx-messenger-panel-chat"}, events : { click: BX.delegate(function(e){ this.openChatDialog({'chatId': this.currentTab.toString().substr(4),'type': 'CHAT_EXTEND', 'bind': BX.proxy_context}); BX.PreventDefault(e)}, this)}}): null,
				BX.create("a", {attrs: {href: "#history", title: BX.message("IM_M_OPEN_HISTORY_2")}, props : { className : "bx-messenger-panel-history"}, events : { click: BX.delegate(function(e){ this.openHistory(this.currentTab); BX.PreventDefault(e)}, this)}}),
				this.popupMessengerPanelChatTitle = BX.create("span", { props : { className : "bx-messenger-panel-title bx-messenger-panel-title-chat"}, html: this.chat[this.currentTab.toString().substr(4)]? this.chat[this.currentTab.toString().substr(4)].name: BX.message('IM_CL_LOAD')}),
				BX.create("span", { props : { className : "bx-messenger-panel-desc"}, children : [
					this.popupMessengerPanelUsers = BX.create('div', { props : { className : "bx-messenger-panel-chat-users"}, html: BX.message('IM_CL_LOAD')})
				]})
			]}),
			this.popupMessengerPanel3 = BX.create("div", { props : { className : "bx-messenger-panel"+(this.openChatFlag && this.openCallFlag? '': ' bx-messenger-hide') }, children : [
				this.popupMessengerPanelAvatar3 = BX.create('img', { attrs : { src : this.chat[this.currentTab.toString().substr(4)] && this.chat[this.currentTab.toString().substr(4)].avatar? this.chat[this.currentTab.toString().substr(4)].avatar: '/bitrix/js/im/images/blank.gif'}, props : { className : "bx-messenger-panel-avatar bx-messenger-panel-avatar-call" }}),
				BX.create("a", {attrs: {href: "#history", title: BX.message("IM_M_OPEN_HISTORY_2")}, props : { className : "bx-messenger-panel-history"}, events : { click: BX.delegate(function(e){ this.openHistory(this.currentTab); BX.PreventDefault(e)}, this)}}),
				this.callButton('call'),
				this.popupMessengerPanelCallTitle = BX.create("span", { props : { className : "bx-messenger-panel-title"}, html: this.chat[this.currentTab.toString().substr(4)]? this.chat[this.currentTab.toString().substr(4)].name: BX.message('IM_CL_LOAD')}),
				this.popupMessengerPanelCallStatus = BX.create("span", { props : { className : "bx-messenger-panel-desc"}, html: BX.message('IM_PHONE_DESC')})
			]}),
			this.popupMessengerBody = BX.create("div", { props : { className : "bx-messenger-body" }, style : {height: this.popupMessengerBodySize+'px'}, children: [
				this.popupMessengerBodyWrap = BX.create("div", { props : { className : "bx-messenger-body-wrap" }})
			]}),
			this.popupMessengerTextareaPlace = BX.create("div", { props : { className : "bx-messenger-textarea-place"+(this.smile == false? " bx-messenger-textarea-smile-disabled": "") }, children : [
				BX.create("div", { props : { className : "bx-messenger-textarea-resize" }, events : { mousedown : BX.delegate(this.resizeTextareaStart, this)}}),
				BX.create("div", { props : { className : "bx-messenger-textarea-send" }, children : [
					BX.create("div", { props : { className : "bx-messenger-textarea-smile" }, events : { click : BX.delegate(function(e){this.openSmileMenu(); return BX.PreventDefault(e);}, this)}}),
					BX.create("a", {attrs: {href: "#send"}, props : { className : "bx-messenger-textarea-send-button" }, events : { click : BX.delegate(this.sendMessage, this)}}),
					this.popupMessengerTextareaSendType = BX.create("span", {attrs : {title : BX.message('IM_M_SEND_TYPE_TITLE')}, props : { className : "bx-messenger-textarea-cntr-enter"}, html: this.BXIM.settings.sendByEnter? 'Enter': (BX.browser.IsMac()? "&#8984;+Enter": "Ctrl+Enter") })
				]}),
				BX.create("div", { props : { className : "bx-messenger-textarea" }, children : [
					this.popupMessengerTextarea = BX.create("textarea", { props : { value: (this.textareaHistory[userId]? this.textareaHistory[userId]: ''), className : "bx-messenger-textarea-input" }, style : {height: this.popupMessengerTextareaSize+'px'}})
				]}),
				BX.create("div", { props : { className : "bx-messenger-textarea-clear" }}),
				this.BXIM.desktop.run()? null: BX.create("span", { props : { className : "bx-messenger-resize" }, events : { mousedown : BX.delegate(this.resizeWindowStart, this)}})
			]})
		]}),
		/* EXTRA PANEL */
		this.popupMessengerExtra = BX.create("div", { props : { className : "bx-messenger-box-extra"}, style : {marginLeft: this.popupContactListSize+'px', height: this.popupMessengerFullHeight+'px'}})
	]});

	this.BXIM.dialogOpen = true;
	if (this.desktop.run())
	{
		var windowTitle = this.BXIM.bitrixIntranet? (!BX.browser.IsMac()? BX.message('IM_DESKTOP_B24_TITLE'): BX.message('IM_DESKTOP_B24_OSX_TITLE')): BX.message('IM_WM');
		BX.desktop.setWindowTitle(windowTitle);
		this.popupMessenger = new BX.PopupWindowDesktop(this.BXIM);
		BX.desktop.setTabContent('im', this.popupMessengerContent);
	}
	else
	{
		this.popupMessenger = new BX.PopupWindow('bx-messenger-popup-messenger', null, {
			lightShadow : true,
			autoHide: false,
			closeByEsc: true,
			overlay: {opacity: 50, backgroundColor: "#000000"},
			draggable: {restrict: true},
			events : {
				onPopupClose : function() { this.destroy(); },
				onPopupDestroy : BX.delegate(function() {
					if (this.BXIM.popupSettings != null)
						this.BXIM.popupSettings.close();

					if (this.webrtc.callInit)
					{
						this.webrtc.callCommand(this.webrtc.callChatId, 'decline', {'ACTIVE': this.callActive? 'Y': 'N', 'INITIATOR': this.initiator? 'Y': 'N'});
						this.webrtc.callAbort();
					}
					this.closeMenuPopup();
					this.popupMessenger = null;
					this.popupMessengerContent = null;
					this.BXIM.extraOpen = false;
					this.BXIM.dialogOpen = false;
					this.BXIM.notifyOpen = false;

					clearTimeout(this.popupMessengerDesktopTimeout);

					this.setUpdateStateStep();
					BX.unbind(document, "click", BX.proxy(this.BXIM.autoHide, this.BXIM));
					this.webrtc.callOverlayClose();
				}, this)
			},
			titleBar: {content: BX.create('span', {props : { className : "bx-messenger-title" }, html: this.BXIM.bitrixIntranet? BX.message('IM_BC'): BX.message('IM_WM')})},
			closeIcon : {'top': '10px', 'right': '13px'},
			content : this.popupMessengerContent
		});
		this.popupMessenger.show();
		BX.bind(this.popupMessenger.popupContainer, "click", BX.IM.preventDefault);
		if (this.webrtc.ready())
		{
			BX.addCustomEvent(this.popupMessenger, "onPopupDragStart", BX.delegate(function(){
				if (this.webrtc.callDialogAllow != null)
					this.webrtc.callDialogAllow.destroy();
			}, this));
		}
		BX.bind(document, "click", BX.proxy(this.BXIM.autoHide, this.BXIM));
	}

	this.popupMessengerTopLine = BX.create("div", { props : { className : "bx-messenger-box-topline"}});
	this.popupMessengerContent.insertBefore(this.popupMessengerTopLine, this.popupMessengerContent.firstChild);

	if (!this.desktop.run() && this.BXIM.bitrixIntranet && this.BXIM.platformName != '' && this.BXIM.settings.bxdNotify)
	{
		clearTimeout(this.popupMessengerDesktopTimeout);
		this.popupMessengerDesktopTimeout = setTimeout(BX.delegate(function(){
			var acceptButton = BX.delegate(function(){
				window.open(BX.browser.IsMac()? "http://dl.bitrix24.com/b24/bitrix24_desktop.dmg": "http://dl.bitrix24.com/b24/bitrix24_desktop.exe", "desktopApp");
				this.BXIM.settings.bxdNotify = false;
				this.BXIM.saveSettings({'bxdNotify': this.BXIM.settings.bxdNotify});
				this.hideTopLine();
			}, this);
			var declineButton = BX.delegate(function(){
				this.BXIM.settings.bxdNotify = false;
				this.BXIM.saveSettings({'bxdNotify': this.BXIM.settings.bxdNotify});
				this.hideTopLine();
			}, this);
			this.showTopLine(BX.message('IM_DESKTOP_INSTALL').replace('#WM_NAME#', this.BXIM.bitrixIntranet? BX.message('IM_BC'): BX.message('IM_WM')).replace('#OS#', this.BXIM.platformName), [{title: BX.message('IM_DESKTOP_INSTALL_Y'), callback: acceptButton},{title: BX.message('IM_DESKTOP_INSTALL_N'), callback: declineButton}]);
		}, this), 15000);
	}

	if (this.webrtc.callNotify != null)
	{
		if (this.webrtc.ready())
		{
			this.popupMessenger.setClosingByEsc(false);
			BX.addClass(BX('bx-messenger-popup-messenger'), 'bx-messenger-popup-messenger-dont-close');
			BX.removeClass(this.webrtc.callNotify.contentContainer.children[0], 'bx-messenger-call-overlay-float');
			this.popupMessengerContent.insertBefore(this.webrtc.callNotify.contentContainer.children[0], this.popupMessengerContent.firstChild);
			this.webrtc.callNotify.close();
		}
		else
		{
			this.webrtc.callOverlayClose(false);
		}
	}

	this.userListRedraw();
	if (this.BXIM.quirksMode)
	{
		this.popupContactListWrap.style.position = "absolute";
		this.popupContactListWrap.style.display = "block";
	}
	this.setUpdateStateStep();
	if (!(BX.browser.IsAndroid() || BX.browser.IsIOS()) && this.popupMessenger != null)
	{
		if (setSearchFocus && this.popupContactListSearchInput != null)
		{
			setTimeout(BX.delegate(function(){
				this.popupContactListSearchInput.focus();
			}, this), 50);
		}
		else
		{
			setTimeout(BX.delegate(function(){
				this.popupMessengerTextarea.focus();
			}, this), 50);
		}
	}

	/* RL */
	BX.bind(this.recentListTab, "click",  BX.delegate(function(e){
		var params = {};

		if (e.metaKey == true || e.ctrlKey == true)
			params.showOnlyChat = true;

		this.recentListRedraw(params);
	}, this));

	/* CL */
	if (this.webrtc.phoneEnabled)
	{
		if (!this.desktop.run())
		{
			BX.bind(this.popupContactListSearchCall, "click", BX.delegate(this.webrtc.openKeyPad, this.webrtc));
		}
	}

	BX.bind(this.contactListTab, "click", BX.delegate(function(){ this.contactListSearchText = ''; this.popupContactListSearchInput.value = ''; this.contactListRedraw()}, this));

	BX.bind(this.popupContactListSearchClose, "click",  BX.delegate(function(e){
		this.popupContactListSearchInput.value = '';
		this.contactListSearchText = BX.util.trim(this.popupContactListSearchInput.value);
		BX.localStorage.set('mns', this.contactListSearchText, 5);
		if (this.recentListReturn)
		{
			this.recentList = true;
			this.contactList = false;
		}
		this.userListRedraw();
		return BX.PreventDefault(e);
	}, this));
	BX.bind(this.popupContactListSearchInput, "focus", BX.delegate(function() {
		if (this.popupMessenger != null)
			this.popupMessenger.setClosingByEsc(false);
	}, this));
	BX.bind(this.popupContactListSearchInput, "blur", BX.delegate(function() {
		if (this.popupMessenger != null && !this.webrtc.callInit)
		{
			this.popupMessenger.setClosingByEsc(true);
		}
	}, this));
	if (this.desktop.ready())
	{
		BX.bind(this.popupContactListSearchInput, "contextmenu", BX.delegate(function(e) {
			this.openPopupMenu(e, 'copypaste', false);
			return BX.PreventDefault(e);
		}, this));
	}
	BX.bind(this.popupContactListSearchInput, "keyup", BX.delegate(this.contactListSearch, this));

	BX.bind(this.popupMessengerPanelChatTitle, "click",  BX.delegate(this.renameChatDialog, this));

	BX.bindDelegate(this.popupMessengerPanelUsers, "click", {className: 'bx-messenger-panel-chat-user'}, BX.delegate(function(e){this.openPopupMenu(BX.proxy_context, 'chatUser'); return BX.PreventDefault(e);}, this));

	BX.bindDelegate(this.popupMessengerPanelUsers, "click", {className: 'bx-notifier-popup-user-more'}, BX.delegate(function(e) {
		if (this.popupChatUsers != null)
		{
			this.popupChatUsers.destroy();
			return false;
		}

		var currentTab = this.currentTab.toString().substr(4);
		var htmlElement = '<span class="bx-notifier-item-help-popup">';
			for (var i = parseInt(BX.proxy_context.getAttribute('data-last-item')); i < this.userInChat[currentTab].length; i++)
				htmlElement += '<span class="bx-notifier-item-help-popup-img bx-messenger-panel-chat-user" data-userId="'+this.userInChat[currentTab][i]+'"><span class="bx-notifier-popup-avatar  bx-notifier-popup-avatar-status-'+this.users[this.userInChat[currentTab][i]].status+'"><img class="bx-notifier-popup-avatar-img" src="'+this.users[this.userInChat[currentTab][i]].avatar+'"></span><span class="bx-notifier-item-help-popup-name">'+this.users[this.userInChat[currentTab][i]].name+'</span></span>';
		htmlElement += '</span>';

		this.popupChatUsers = new BX.PopupWindow('bx-notifier-other-window', BX.proxy_context, {
			zIndex: 200,
			lightShadow : true,
			offsetTop: -2,
			offsetLeft: 3,
			autoHide: true,
			closeByEsc: true,
			events : {
				onPopupClose : function() { this.destroy() },
				onPopupDestroy : BX.proxy(function() { this.popupChatUsers = null; }, this)
			},
			content : BX.create("div", { props : { className : "bx-notifier-popup-menu" }, children: [
				BX.create("div", { props : { className : " " }, html: htmlElement})
			]})
		});
		this.popupChatUsers.setAngle({offset: BX.proxy_context.offsetWidth});
		this.popupChatUsers.show();

		BX.bindDelegate(this.popupChatUsers.popupContainer, "click", {className: 'bx-messenger-panel-chat-user'}, BX.delegate(function(e){this.openPopupMenu(BX.proxy_context, 'chatUser'); return BX.PreventDefault(e);}, this));

		return BX.PreventDefault(e);
	}, this));
	BX.bindDelegate(this.popupContactListElements, "contextmenu", {className: 'bx-messenger-cl-item'}, BX.delegate(function(e) {
		this.openPopupMenu(BX.proxy_context, 'contactList');
		return BX.PreventDefault(e);
	}, this));
	BX.bindDelegate(this.popupContactListElements, "click", {className: 'bx-messenger-cl-item'}, BX.delegate(function(e) {
		this.closeMenuPopup();
		if (this.popupContactListSearchInput.value != '')
		{
			this.popupContactListSearchInput.value = '';
			this.contactListSearchText = '';
			BX.localStorage.set('mns', this.contactListSearchText, 5);
			if (this.recentListReturn)
			{
				this.recentList = true;
				this.contactList = false;
			}
			this.userListRedraw();
		}
		this.openMessenger(BX.proxy_context.getAttribute('data-userId'));
		return BX.PreventDefault(e);
	}, this));
	BX.bind(this.popupContactListElements, "scroll", BX.delegate(function() {
		if (this.popupPopupMenu != null && this.popupPopupMenuDateCreate+500 < (+new Date()))
			this.popupPopupMenu.close();
	}, this));
	BX.bindDelegate(this.popupContactListElements, 'click', {className: 'bx-messenger-cl-group-title'}, BX.delegate(function() {
		var status = '';
		var wrapper = BX.findNextSibling(BX.proxy_context, {className: 'bx-messenger-cl-group-wrapper'});
		if (wrapper.childNodes.length > 0)
		{
			var avatarNodes = BX.findChildren(wrapper, {className : "bx-messenger-cl-avatar-img"}, true);
			if (BX.hasClass(BX.proxy_context.parentNode, 'bx-messenger-cl-group-open'))
			{
				status = 'close';
				BX.removeClass(BX.proxy_context.parentNode, 'bx-messenger-cl-group-open');
				if (avatarNodes)
				{
					for (var i = 0; i < avatarNodes.length; i++)
					{
						avatarNodes[i].setAttribute('_src', avatarNodes[i].src);
						avatarNodes[i].src = "/bitrix/js/im/images/blank.gif";
					}
				}
			}
			else
			{
				status = 'open';
				BX.addClass(BX.proxy_context.parentNode, 'bx-messenger-cl-group-open');
				if (avatarNodes)
				{
					for (var i = 0; i < avatarNodes.length; i++)
					{
						avatarNodes[i].src = avatarNodes[i].getAttribute('_src');
						avatarNodes[i].setAttribute('_src', "/bitrix/js/im/images/blank.gif");
					}
				}
			}
		}
		else
		{
			if (BX.hasClass(BX.proxy_context.parentNode, 'bx-messenger-cl-group-open'))
			{
				status = 'close';
				BX.removeClass(BX.proxy_context.parentNode, 'bx-messenger-cl-group-open');
			}
			else
			{
				status = 'open';
				BX.addClass(BX.proxy_context.parentNode, 'bx-messenger-cl-group-open');
			}
		}

		var id = BX.proxy_context.getAttribute('data-groupId');
		var viewGroup = this.contactListSearchText != null && this.contactListSearchText.length > 0? false: this.BXIM.settings.viewGroup;
		if (viewGroup)
			this.groups[id].status = status;
		else if (this.woGroups[id])
			this.woGroups[id].status = status;

		BX.userOptions.save('IM', 'groupStatus', id, status);
		BX.localStorage.set('mgp', {'id': id, 'status': status}, 5);
	}, this));

	BX.bind(this.contactListPanelStatus, "click", BX.delegate(function(e){this.openPopupMenu(this.contactListPanelStatus, 'status');  return BX.PreventDefault(e);}, this));
	if (this.contactListPanelSettings)
		BX.bind(this.contactListPanelSettings, "click", BX.delegate(function(e){this.openSettings(); BX.PreventDefault(e)}, this.BXIM));

	/* DIALOG */
	BX.bind(this.popupMessengerBody, "scroll", BX.delegate(function() {
		if (this.unreadMessage[this.currentTab] && this.unreadMessage[this.currentTab].length > 0 && this.BXIM.isScrollMax(this.popupMessengerBody, 200) && this.BXIM.isFocus())
		{
			clearTimeout(this.readMessageTimeout);
			this.readMessageTimeout = setTimeout(BX.delegate(function(){
				this.readMessage(this.currentTab);
			}, this), 100);
		}
	}, this));
	if (this.desktop.ready())
	{
		BX.bind(this.popupMessengerTextarea, "contextmenu", BX.delegate(function(e) {
			this.openPopupMenu(e, 'copypaste', false);
			return BX.PreventDefault(e);
		}, this));
	}
	BX.bind(this.popupMessengerTextarea, "focus", BX.delegate(function() {
		if (this.popupMessenger != null)
			this.popupMessenger.setClosingByEsc(false);
	}, this));
	BX.bind(this.popupMessengerTextarea, "blur", BX.delegate(function() {
		if (this.popupMessenger != null && !this.webrtc.callInit)
		{
			this.popupMessenger.setClosingByEsc(true);
		}
	}, this));
	BX.bind(this.popupMessengerTextarea, "keydown", BX.delegate(function(e) {
		var result = true;
		if (e.keyCode == 9)
		{
			this.insertTextareaText("\t");
			return BX.PreventDefault(e);
		}
		if (e.keyCode == 27 && !this.desktop.ready())
		{
			if (BX.util.trim(this.popupMessengerTextarea.value).length <= 0)
			{
				this.popupMessengerTextarea.value = "";
				if (this.popupMessenger && !this.webrtc.callInit)
					this.popupMessenger.destroy();
			}
			else
				this.popupMessengerTextarea.value = "";
		}
		else if (e.keyCode == 38 && BX.util.trim(this.popupMessengerTextarea.value).length <= 0)
			this.popupMessengerTextarea.value = this.popupMessengerLastMessage;
		else if (this.BXIM.settings.sendByEnter == true && (e.ctrlKey == true || e.altKey == true) && e.keyCode == 13)
			this.insertTextareaText("\n");
		else if (this.BXIM.settings.sendByEnter == true && e.shiftKey == false && e.keyCode == 13)
			result = this.sendMessage();
		else if (this.BXIM.settings.sendByEnter == false && e.ctrlKey == true && e.keyCode == 13)
			result = this.sendMessage();
		else if (this.BXIM.settings.sendByEnter == false && (e.metaKey == true || e.altKey == true) && e.keyCode == 13 && BX.browser.IsMac())
			result = this.sendMessage();

		clearTimeout(this.textareaHistoryTimeout);
		this.textareaHistoryTimeout = setTimeout(BX.delegate(function(){
			this.textareaHistory[this.currentTab] = this.popupMessengerTextarea.value;
		}, this), 200);

		if (BX.util.trim(this.popupMessengerTextarea.value).length > 2)
			this.sendWriting(this.currentTab);

		if (!result)
			return BX.PreventDefault(e);
	}, this));

	BX.bind(this.popupMessengerTextareaSendType, "click", BX.delegate(function() {
		this.BXIM.settings.sendByEnter = this.BXIM.settings.sendByEnter? false: true;
		this.BXIM.saveSettings({'sendByEnter': this.BXIM.settings.sendByEnter});
		BX.proxy_context.innerHTML = this.BXIM.settings.sendByEnter? 'Enter': (BX.browser.IsMac()? "&#8984;+Enter": "Ctrl+Enter");
	}, this));

	if (this.desktop.ready())
	{
		BX.bindDelegate(this.popupMessengerBodyWrap, "contextmenu", {className: 'bx-messenger-content-item-content'}, BX.delegate(function(e) {
			this.openPopupMenu(e, 'dialog', false);
			return BX.PreventDefault(e);
		}, this));
	}

	BX.bindDelegate(this.popupMessengerBodyWrap, 'click', {className: 'bx-messenger-content-item-quote'}, BX.delegate(function() {
		var arQuote = [];
		var firstMessage = true;
		var messageName = '';
		var messageDate = '';

		var stackMessages = BX.findChildren(BX.proxy_context.parentNode.nextSibling.firstChild, {tagName : "span"}, false);
		for (var i = 0; i < stackMessages.length; i++) {
			var messageId = stackMessages[i].getAttribute('data-textMessageId');
			if (this.message[messageId])
			{
				if (firstMessage)
				{
					if (this.users[this.message[messageId].senderId])
					{
						messageName = this.users[this.message[messageId].senderId].name;
						messageDate = this.message[messageId].date;
					}
					firstMessage = false;
				}
				arQuote.push(BX.IM.prepareTextBack(this.message[messageId].text));
			}
		}
		this.insertQuoteText(messageName, messageDate, arQuote.join("\n"));
	}, this));

	BX.bindDelegate(this.popupMessengerBodyWrap, 'click', {className: 'bx-messenger-ajax'}, BX.delegate(function() {
		if (BX.proxy_context.getAttribute('data-entity') == 'user')
		{
			this.openPopupExternalData(BX.proxy_context, 'user', true, {'ID': BX.proxy_context.getAttribute('data-userId')})
		}
		else if (this.webrtc.phoneSupport() && BX.proxy_context.getAttribute('data-entity') == 'phoneCallHistory')
		{
			this.openPopupExternalData(BX.proxy_context, 'phoneCallHistory', true, {'ID': BX.proxy_context.getAttribute('data-historyID')})
		}
	}, this));

	BX.bind(this.popupMessengerBody, "scroll", BX.delegate(function() {
		if (this.popupPopupMenu != null)
			this.popupPopupMenu.close();
	}, this));

	BX.bindDelegate(this.popupMessengerBodyWrap, 'click', {className: 'bx-messenger-content-item-error'}, BX.delegate(this.sendMessageRetry, this));

	if (userId == 0)
	{
		this.extraOpen(
			BX.create("div", { attrs : { style : "padding-top: 300px"}, props : { className : "bx-messenger-box-empty" }, html: BX.message('IM_M_EMPTY')})
		);
	}
	else
		this.openDialog(userId);
};


BX.Messenger.prototype.openDialog = function(userId, extraClose, callToggle)
{
	var user = this.openChatFlag? this.chat[userId.toString().substr(4)]: this.users[userId];
	if (typeof(user) == 'undefined' || typeof(user.id) == 'undefined')
		return false;

	this.dialogStatusRedraw();

	this.popupMessengerPanel.className  = this.openChatFlag? 'bx-messenger-panel bx-messenger-hide': 'bx-messenger-panel';
	if (this.openChatFlag)
	{
		this.popupMessengerPanel2.className = this.openCallFlag? 'bx-messenger-panel bx-messenger-hide': 'bx-messenger-panel';
		this.popupMessengerPanel3.className = this.openCallFlag? 'bx-messenger-panel': 'bx-messenger-panel bx-messenger-hide';
	}
	else
	{
		this.popupMessengerPanel2.className = 'bx-messenger-panel bx-messenger-hide';
		this.popupMessengerPanel3.className = 'bx-messenger-panel bx-messenger-hide';
	}

	extraClose = extraClose == true;
	callToggle = callToggle != false;

	var arMessage = [];
	if (typeof(this.showMessage[userId]) != 'undefined' && this.showMessage[userId].length > 0)
	{
		if (!user.fake && this.showMessage[userId].length >= 15)
		{
			this.redrawTab[userId] = false;
		}
		else
		{
			this.drawTab(userId, true);
			this.redrawTab[userId] = true;
		}
	}
	else if (typeof(this.showMessage[userId]) == 'undefined')
	{
		arMessage = [BX.create("div", { props : { className : "bx-messenger-content-load"}, children : [
			BX.create('span', { props : { className : "bx-messenger-content-load-img" }}),
			BX.create("span", { props : { className : "bx-messenger-content-load-text"}, html: BX.message('IM_M_LOAD_MESSAGE')})
		]})];
		this.redrawTab[userId] = true;
	}
	else if (this.redrawTab[user.id] && this.showMessage[userId].length == 0)
	{
		arMessage = [BX.create("div", { props : { className : "bx-messenger-content-load"}, children : [
			BX.create('span', { props : { className : "bx-messenger-content-load-img" }}),
			BX.create("span", { props : { className : "bx-messenger-content-load-text"}, html: BX.message("IM_M_LOAD_MESSAGE")})
		]})];
		this.showMessage[userId] = [];
	}
	else
	{
		arMessage = [BX.create("div", { props : { className : "bx-messenger-content-empty"}, children : [
			BX.create("span", { props : { className : "bx-messenger-content-load-text"}, html: BX.message(this.BXIM.settings.loadLastMessage? "IM_M_NO_MESSAGE_2": "IM_M_NO_MESSAGE")})
		]})];
	}
	if (arMessage.length > 0)
	{
		this.popupMessengerBodyWrap.innerHTML = '';
		BX.adjust(this.popupMessengerBodyWrap, {children: arMessage});
	}

	if (extraClose)
		this.extraClose();

	this.popupMessengerTextarea.value = this.textareaHistory[userId]? this.textareaHistory[userId]: "";

	this.currentTab = userId;
	BX.localStorage.set('mct', this.currentTab, 15);

	if (this.redrawTab[userId])
	{
		if (this.BXIM.settings.loadLastMessage)
		{
			this.loadLastMessage(userId, this.openChatFlag);
		}
		else
		{
			if (this.openChatFlag)
				this.loadChatData(userId.toString().substr(4));
			else
				this.loadUserData(userId);

			delete this.redrawTab[userId];
			this.drawTab(userId, true);
		}
	}
	else
		this.drawTab(userId, true);

	if (this.BXIM.isFocus() && !this.redrawTab[userId])
		this.readMessage(userId);

	this.resizeMainWindow();

	if (this.countWriting(userId))
	{
		if (this.openChatFlag)
			this.drawWriting(0, userId);
		else
			this.drawWriting(userId);
	}
	else if (this.readedList[userId])
	{
		this.drawReadMessage(userId, this.readedList[userId].messageId, this.readedList[userId].date, false);
	}

	if (callToggle)
		this.webrtc.callOverlayToggleSize(true);

	BX.onCustomEvent(window, 'onImDrawDialog', [userId]);
};

BX.Messenger.prototype.drawTab = function(userId, scroll)
{
	if (this.popupMessenger == null || userId != this.currentTab)
		return false;

	this.dialogStatusRedraw();

	this.popupMessengerBodyWrap.innerHTML = '';
	if (!this.showMessage[userId] || this.showMessage[userId].length <= 0)
	{
		this.popupMessengerBodyWrap.appendChild(BX.create("div", { props : { className : "bx-messenger-content-empty"}, children : [
			BX.create("span", { props : { className : "bx-messenger-content-load-text"}, html: BX.message(this.BXIM.settings.loadLastMessage? "IM_M_NO_MESSAGE_2": "IM_M_NO_MESSAGE")})
		]}));
	}

	if (this.showMessage[userId])
		this.showMessage[userId].sort(BX.delegate(function(i, ii) {if (!this.message[i] || !this.message[ii]){return 0;} var i1 = parseInt(this.message[i].date); var i2 = parseInt(this.message[ii].date); if (i1 < i2) { return -1; } else if (i1 > i2) { return 1;} else{ if (i < ii) { return -1; } else if (i > ii) { return 1;}else{ return 0;}}}, this));
	else
		this.showMessage[userId] = [];

	for (var i = 0; i < this.showMessage[userId].length; i++)
		this.drawMessage(userId, this.message[this.showMessage[userId][i]], false);

	scroll = scroll != false;
	if (scroll)
	{
		if (this.popupMessengerBodyAnimation != null)
			this.popupMessengerBodyAnimation.stop();

		if (this.unreadMessage[userId] && this.unreadMessage[userId].length > 0)
		{
			var textElement = BX.findChild(this.popupMessengerBodyWrap, {attribute: {'data-textMessageId': ''+this.unreadMessage[userId][0]+''}}, true);
			if (textElement)
				this.popupMessengerBody.scrollTop  = textElement.offsetTop-20-this.popupMessengerBodyWrap.offsetTop;
			else
				this.popupMessengerBody.scrollTop = this.popupMessengerBody.scrollHeight - this.popupMessengerBody.offsetHeight;
		}
		else
		{
			this.popupMessengerBody.scrollTop = this.popupMessengerBody.scrollHeight - this.popupMessengerBody.offsetHeight;
		}
	}
	delete this.redrawTab[userId];
};

BX.Messenger.prototype.drawMessage = function(userId, message, scroll)
{
	if (this.popupMessenger == null || userId != this.currentTab || typeof(message) != 'object' || userId == 0)
		return false;

	var temp = message.id.indexOf('temp') == 0;
	var retry = temp && message.retry;
	var system = message.senderId == 0;
	if (message.system && message.system == 'Y')
	{
		system = true;
		message.senderId = 0;
	}
	if (!this.history[userId])
		this.history[userId] = [];

	if (parseInt(message.id) > 0)
		this.history[userId].push(message.id);

	var messageId = 0;
	var skipAddMessage = false;
	var messageUser = this.users[message.senderId];
	if (!system && typeof(messageUser) == 'undefined')
		return false;

	var markNewMessage = false;
	if (this.unreadMessage[userId] && BX.util.in_array(message.id, this.unreadMessage[userId]))
		markNewMessage = true;

	var insertBefore = false;

	var lastMessage = this.popupMessengerBodyWrap.lastChild;
	if (lastMessage && BX.hasClass(lastMessage, "bx-messenger-content-empty"))
	{
		BX.remove(lastMessage);
	}
	else if (lastMessage && BX.hasClass(lastMessage, "bx-messenger-content-item-notify"))
	{
		if (message.senderId == this.currentTab || !this.countWriting(this.currentTab))
		{
			BX.remove(lastMessage);
			insertBefore = false;
			lastMessage = this.popupMessengerBodyWrap.lastChild;
		}
		else
		{
			insertBefore = true;
			lastMessage = this.popupMessengerBodyWrap.lastChild.previousSibling;
		}
	}

	if (!system && lastMessage)
	{
		if (message.senderId == lastMessage.getAttribute('data-senderId') && parseInt(message.date)-300 < parseInt(lastMessage.getAttribute('data-messageDate')))
		{
			var lastMessageElement = BX.findChild(lastMessage, {className : "bx-messenger-content-item-text-message"}, true);
			lastMessageElement.innerHTML = lastMessageElement.innerHTML+'<div class="bx-messenger-hr"></div>'+'<span class="bx-messenger-message" data-textMessageId="'+message.id+'">'+BX.IM.prepareText(message.text, false, true, true, (!this.openChatFlag || message.senderId == this.BXIM.userId? false: (this.users[this.BXIM.userId].name)))+'</span>';
			lastMessageElement.nextSibling.innerHTML = (temp? BX.message('IM_M_DELIVERED'): ' &nbsp; '+(this.openChatFlag? messageUser.name: '')+' &nbsp; '+BX.IM.formatDate(message.date));
			if (markNewMessage)
				BX.addClass(lastMessage, 'bx-messenger-content-item-new');

			if (retry)
			{
				var lastMessageElementStatus = BX.findChild(lastMessage, {className : "bx-messenger-content-item-status"}, true);
				if (lastMessageElementStatus)
				{
					lastMessageElementStatus.innerHTML = '';
					BX.adjust(lastMessageElementStatus, {children: [
						BX.create("span", { attrs: { title: BX.message('IM_M_RETRY'), 'data-messageid': message.id, 'data-chat': parseInt(message.recipientId) > 0? 'Y':'N' }, props : { className : "bx-messenger-content-item-error"}, children:[
							BX.create("span", { props : { className : "bx-messenger-content-item-error-icon"}})
						]})
					]});
				}
			}
			else if (temp)
			{
				var lastMessageElementStatus = BX.findChild(lastMessage, {className : "bx-messenger-content-item-status"}, true);
				if (lastMessageElementStatus)
				{
					lastMessageElementStatus.innerHTML = '';
					BX.adjust(lastMessageElementStatus, {children: [
						BX.create("span", { props : { className : "bx-messenger-content-item-progress"}})
					]});
				}
			}

			lastMessage.setAttribute('data-messageDate', message.date);
			lastMessage.setAttribute('data-messageId', message.id);
			lastMessage.setAttribute('data-senderId', message.senderId);

			messageId = message.id;
			skipAddMessage = true;
		}
	}
	if (!skipAddMessage)
	{
		if (lastMessage)
			messageId = lastMessage.getAttribute('data-messageId');

		if (system)
		{
			var lastSystemElement = BX.findChild(this.popupMessengerBodyWrap, {attribute: {'data-messageId': ''+message.id+''}}, false);
			if (!lastSystemElement)
			{
				var arMessage = BX.create("div", { attrs : { 'data-type': 'system', 'data-senderId' : message.senderId, 'data-messageId' : message.id }, props: { className : "bx-messenger-content-item bx-messenger-content-item-2 bx-messenger-content-item-system"}, children: [
					BX.create("span", { props : { className : "bx-messenger-content-item-content"}, children : [
						typeof(messageUser) == 'undefined'? []:
						BX.create("span", { props : { className : "bx-messenger-content-item-avatar"}, children : [
							BX.create("span", { props : { className : "bx-messenger-content-item-arrow"}}),
							BX.create('img', { props : { className : "bx-messenger-content-item-avatar-img" }, attrs : {src : messageUser.avatar}})
						]}),
						BX.create("span", { props : { className : "bx-messenger-content-item-text-center"}, children: [
							BX.create("span", {  props : { className : "bx-messenger-content-item-text-message"}, html: '<span class="bx-messenger-message" data-textMessageId="'+message.id+'">'+BX.IM.prepareText(message.text, false, true, true)+'</span>'}),
							BX.create("span", { props : { className : "bx-messenger-content-item-date"}, html: ' &nbsp; '+(messageUser? (this.openChatFlag? messageUser.name: ''): BX.message('IM_M_SYSTEM_USER'))+' &nbsp; '+BX.IM.formatDate(message.date)}),
							BX.create("span", { props : { className : "bx-messenger-clear"}})
						]})
					]})
				]});

				if (message.system && message.system == 'Y' && markNewMessage)
					BX.addClass(arMessage, 'bx-messenger-content-item-new');
			}
		}
		else if (message.senderId == this.BXIM.userId)
		{
			var arMessage = BX.create("div", { attrs : { 'data-type': 'self', 'data-senderId' : message.senderId, 'data-messageDate' : message.date, 'data-messageId' : message.id }, props: { className : "bx-messenger-content-item"}, children: [
				BX.create("span", { props : { className : "bx-messenger-content-item-content"}, children : [
					BX.create("span", { props : { className : "bx-messenger-content-item-avatar"}, children : [
						BX.create("span", { props : { className : "bx-messenger-content-item-arrow"}}),
						BX.create('img', { props : { className : "bx-messenger-content-item-avatar-img" }, attrs : {src : messageUser.avatar}})
					]}),
					retry? (
						BX.create("span", { props : { className : "bx-messenger-content-item-status"}, children:[
							BX.create("span", { attrs: { title: BX.message('IM_M_RETRY'), 'data-messageid': message.id, 'data-chat': parseInt(message.recipientId) > 0? 'Y':'N' }, props : { className : "bx-messenger-content-item-error"}, children:[
								BX.create("span", { props : { className : "bx-messenger-content-item-error-icon"}})
							]})
						]})
					):(
						BX.create("span", { props : { className : "bx-messenger-content-item-status"}, children:[
							temp? BX.create("span", { props : { className : "bx-messenger-content-item-progress"}})
							: BX.create("span", { attrs: {title : BX.message('IM_M_QUOTE_TITLE')}, props : { className : "bx-messenger-content-item-quote"}})
						]})
					),
					BX.create("span", { props : { className : "bx-messenger-content-item-text-center"}, children: [
						BX.create("span", {  props : { className : "bx-messenger-content-item-text-message"}, html: '<span class="bx-messenger-message" data-textMessageId="'+message.id+'">'+BX.IM.prepareText(message.text, false, true, true)+'</span>'}),
						BX.create("span", { props : { className : "bx-messenger-content-item-date"}, html: (retry? BX.message('IM_M_NOT_DELIVERED') : temp? BX.message('IM_M_DELIVERED'): ' &nbsp; '+(this.openChatFlag? messageUser.name: '')+' &nbsp; '+BX.IM.formatDate(message.date))}),
						BX.create("span", { props : { className : "bx-messenger-clear"}})
					]})
				]})
			]});
		}
		else
		{
			var arMessage = BX.create("div", { attrs : { 'data-type': 'other', 'data-senderId' : message.senderId, 'data-messageDate' : message.date, 'data-messageId' : message.id }, props: { className : "bx-messenger-content-item bx-messenger-content-item-2"+(markNewMessage? ' bx-messenger-content-item-new': '')}, children: [
				BX.create("span", { props : { className : "bx-messenger-content-item-content"}, children : [
					BX.create("span", { props : { className : "bx-messenger-content-item-avatar"}, children : [
						BX.create("span", { props : { className : "bx-messenger-content-item-arrow"}}),
						BX.create('img', { props : { className : "bx-messenger-content-item-avatar-img" }, attrs : {src : messageUser.avatar}})
					]}),
					BX.create("span", { props : { className : "bx-messenger-content-item-status"}, children:[
						BX.create("span", { attrs: {title : BX.message('IM_M_QUOTE_TITLE')}, props : { className : "bx-messenger-content-item-quote"}})
					]}),
					BX.create("span", { props : { className : "bx-messenger-content-item-text-center"}, children: [
						BX.create("span", {  props : { className : "bx-messenger-content-item-text-message"}, html: '<span class="bx-messenger-message" data-textMessageId="'+message.id+'">'+BX.IM.prepareText(message.text, false, true, true, (!this.openChatFlag || message.senderId == this.BXIM.userId? false: (this.users[this.BXIM.userId].name)))+'</span>'}),
						BX.create("span", { props : { className : "bx-messenger-content-item-date"}, html: (temp? BX.message('IM_M_DELIVERED'): ' &nbsp; '+(this.openChatFlag? messageUser.name: '')+' &nbsp; '+BX.IM.formatDate(message.date))}),
						BX.create("span", { props : { className : "bx-messenger-clear"}})
					]})
				]})
			]});
		}
		if (insertBefore && lastMessage.nextElementSibling)
			this.popupMessengerBodyWrap.insertBefore(arMessage, lastMessage.nextElementSibling);
		else
			this.popupMessengerBodyWrap.appendChild(arMessage);
	}

	if (this.BXIM.enableScroll(this.popupMessengerBody, this.popupMessengerBody.offsetHeight, scroll))
	{
		if (this.BXIM.animationSupport)
		{
			if (this.popupMessengerBodyAnimation != null)
				this.popupMessengerBodyAnimation.stop();
			(this.popupMessengerBodyAnimation = new BX.easing({
				duration : 800,
				start : { scroll : this.popupMessengerBody.scrollTop },
				finish : { scroll : this.popupMessengerBody.scrollHeight - this.popupMessengerBody.offsetHeight},
				transition : BX.easing.makeEaseInOut(BX.easing.transitions.quart),
				step : BX.delegate(function(state){
					this.popupMessengerBody.scrollTop = state.scroll;
				}, this)
			})).animate();
		}
		else
		{
			this.popupMessengerBody.scrollTop = this.popupMessengerBody.scrollHeight - this.popupMessengerBody.offsetHeight;
		}
	}
	return messageId;
};

BX.Messenger.prototype.drawNotifyMessage = function(userId, icon, message, animation)
{
	if (this.popupMessenger == null || userId != this.currentTab || typeof(message) == 'undefined' || typeof(icon) == 'undefined' || userId == 0)
		return false;

	var lastChild = this.popupMessengerBodyWrap.lastChild;
	if (BX.hasClass(lastChild, "bx-messenger-content-empty"))
		return false;

	var arMessage = BX.create("div", { attrs : { 'data-type': 'notify'}, props: { className : "bx-messenger-content-item bx-messenger-content-item-notify"}, children: [
		BX.create("span", { props : { className : "bx-messenger-content-item-content"}, children : [
			BX.create("span", { props : { className : "bx-messenger-content-item-text-center"}, children: [
				BX.create("span", {  props : { className : "bx-messenger-content-item-text-message"}, html: '<span class="bx-messenger-content-item-notify-icon-'+icon+'"></span>'+BX.IM.prepareText(message, false, true, true)})
			]})
		]})
	]});

	if (BX.hasClass(lastChild, "bx-messenger-content-item-notify"))
		BX.remove(lastChild);

	this.popupMessengerBodyWrap.appendChild(arMessage);

	animation = animation != false;
	if (this.BXIM.enableScroll(this.popupMessengerBody, this.popupMessengerBody.offsetHeight))
	{
		if (this.BXIM.animationSupport && animation)
		{
			if (this.popupMessengerBodyAnimation != null)
				this.popupMessengerBodyAnimation.stop();
			(this.popupMessengerBodyAnimation = new BX.easing({
				duration : 800,
				start : { scroll : this.popupMessengerBody.scrollTop},
				finish : { scroll : this.popupMessengerBody.scrollHeight - this.popupMessengerBody.offsetHeight},
				transition : BX.easing.makeEaseInOut(BX.easing.transitions.quart),
				step : BX.delegate(function(state){
					this.popupMessengerBody.scrollTop = state.scroll;
				}, this)
			})).animate();
		}
		else
		{
			this.popupMessengerBody.scrollTop = this.popupMessengerBody.scrollHeight - this.popupMessengerBody.offsetHeight;
		}
	}
};

BX.Messenger.prototype.dialogStatusRedraw = function()
{
	if (this.popupMessenger == null)
		return false;

	this.popupMessengerPanelCall1.className = this.callButtonStatus(this.currentTab);
	this.popupMessengerPanelCall2.className = this.callButtonStatus(this.currentTab);

	if (this.openChatFlag)
	{
		var renameDialog = false;
		if (this.renameChatDialogFlag)
			renameDialog = true;

		this.redrawChatHeader();

		if (renameDialog)
			this.renameChatDialog();
	}
	else if (this.users[this.currentTab])
	{
		this.popupMessengerPanelAvatar.parentNode.href = this.users[this.currentTab].profile;
		this.popupMessengerPanelAvatar.parentNode.className = 'bx-messenger-panel-avatar bx-messenger-panel-avatar-status-'+(this.users[this.currentTab].birthday? 'birthday': this.users[this.currentTab].status);
		this.popupMessengerPanelAvatar.src = this.users[this.currentTab].avatar;
		this.popupMessengerPanelTitle.href = this.users[this.currentTab].profile;
		this.popupMessengerPanelTitle.innerHTML = this.users[this.currentTab].name;
		this.popupMessengerPanelStatus.innerHTML = BX.message("IM_STATUS_"+this.users[this.currentTab].status.toUpperCase());
	}

	return true;
};

BX.Messenger.prototype.callButton = function(type)
{
	var button = null;
	if (type == 'call')
	{
		button = BX.create("span", {props : {className : 'bx-messenger-panel-call-phone'}, children: [
			BX.create("a", {
				attrs: { href: "#call", title: BX.message("IM_PHONE_CALL") },
				props : { className : 'bx-messenger-panel-call-audio' },
				events : {
					click: BX.delegate(function(e){
						if (this.webrtc.callInit)
							return false;

						var currentChat = this.chat[this.currentTab.toString().substr(4)];
						this.BXIM.phoneTo('+'+currentChat.call_number);

						BX.PreventDefault(e);
					}, this)
				}
			})
		]});
	}
	else
	{
		button = BX.create("span", {props : {className : this.callButtonStatus(this.currentTab)}, children: [
			BX.create("a", {
				attrs: { href: "#call", title: BX.message("IM_M_CALL_VIDEO") },
				props : { className : 'bx-messenger-panel-call-video' },
				events : {
					click: BX.delegate(function(e){
						if (!this.webrtc.callInit)
							this.BXIM.callTo(this.currentTab, true);
						BX.PreventDefault(e);
					}, this)
				}
			}),
			BX.create("a", {
				attrs: { href: "#callMenu" },
				props : { className : 'bx-messenger-panel-call-menu' },
				events : {
					click: BX.delegate(function(e){
						if (!this.webrtc.callInit)
							this.openPopupMenu(BX.proxy_context, 'callMenu');
						BX.PreventDefault(e);
					}, this)
				}
			})
		]});
	}
	return button;
};

BX.Messenger.prototype.callButtonStatus = function(userId)
{
	var elementClassName = 'bx-messenger-panel-call-hide';
	if (this.BXIM.ppServerStatus)
		elementClassName = (!this.webrtc.callSupport(userId, this) || this.webrtc.callInit)? 'bx-messenger-panel-call-disabled': 'bx-messenger-panel-call-enabled';

	return elementClassName;
};

/* CHAT */
BX.Messenger.prototype.leaveFromChat = function(chatId, sendAjax)
{
	if (!this.chat[chatId])
		return false;

	sendAjax = sendAjax != false;

	if (!sendAjax)
	{
		delete this.chat[chatId];
		delete this.userInChat[chatId];
		if (this.popupMessenger != null)
		{
			if (this.currentTab == 'chat'+chatId)
			{
				this.currentTab = 0;
				this.extraClose();
			}
			if (this.recentList)
				this.recentListRedraw();
		}
	}
	else
	{
		BX.ajax({
			url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?CHAT_LEAVE',
			method: 'POST',
			dataType: 'json',
			timeout: 60,
			data: {'IM_CHAT_LEAVE' : 'Y', 'CHAT_ID' : chatId, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
			onsuccess: BX.delegate(function(data){
				if (data.ERROR == '')
				{
					delete this.chat[data.CHAT_ID];
					delete this.userInChat[data.CHAT_ID];
					this.readMessage('chat'+data.CHAT_ID, true, false);
					if (this.popupMessenger != null)
					{
						if (this.currentTab == 'chat'+data.CHAT_ID)
						{
							this.currentTab = 0;
							BX.localStorage.set('mct', this.currentTab, 15);
							this.extraClose();
						}
						if (this.recentList)
							this.recentListRedraw();
					}
					BX.localStorage.set('mcl', data.CHAT_ID, 5);
				}
			}, this)
		});
	}
};

BX.Messenger.prototype.kickFromChat = function(chatId, userId)
{
	if (!this.chat[chatId] && this.chat[chatId].owner != this.BXIM.userId && !this.userId[userId])
		return false;

	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?CHAT_LEAVE',
		method: 'POST',
		dataType: 'json',
		timeout: 60,
		data: {'IM_CHAT_LEAVE' : 'Y', 'CHAT_ID' : chatId, 'USER_ID' : userId, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
		onsuccess: BX.delegate(function(data){
			if (data.ERROR == '')
			{
				for (var i = 0; i < this.userInChat[data.CHAT_ID].length; i++)
					if (this.userInChat[data.CHAT_ID][i] == userId)
						delete this.userInChat[data.CHAT_ID][i];

				if (this.popupMessenger != null && this.recentList)
					this.recentListRedraw();

				if (!this.BXIM.ppServerStatus)
					BX.PULL.updateState(true);

				BX.localStorage.set('mclk', {'chatId': data.CHAT_ID, 'userId': data.USER_ID}, 5);
			}
		}, this)
	});
};

BX.Messenger.prototype.redrawChatHeader = function()
{
	if (!this.openChatFlag)
		return false;

	var chatId = this.currentTab.toString().substr(4);
	if (!this.chat[chatId])
		return false;

	this.renameChatDialogFlag = false;

	if (this.chat[chatId].style == 'group')
	{
		this.popupMessengerPanelAvatar2.src = this.chat[chatId].avatar;
		this.popupMessengerPanelChatTitle.innerHTML = this.chat[chatId].name;
	}
	else
	{
		if (this.chat[chatId].avatar)
			this.popupMessengerPanelAvatar3.src = this.chat[chatId].avatar;
		this.popupMessengerPanelCallTitle.innerHTML = this.chat[chatId].name;
	}

	this.popupMessengerPanel2.className = this.chat[chatId].style == 'call'? 'bx-messenger-panel bx-messenger-hide': 'bx-messenger-panel';
	this.popupMessengerPanel3.className = this.chat[chatId].style == 'call'? 'bx-messenger-panel': 'bx-messenger-panel bx-messenger-hide';

	if (!this.userInChat[chatId])
		return false;

	var showUser = false;
	this.popupMessengerPanelUsers.innerHTML = '';
	var maxCount = Math.floor((this.popupMessengerPanelUsers.offsetWidth)/135);
	if (maxCount >= this.userInChat[chatId].length)
	{
		for (var i = 0; i < this.userInChat[chatId].length && i < maxCount; i++)
		{
			var user = this.users[this.userInChat[chatId][i]];
			if (user)
			{
				this.popupMessengerPanelUsers.innerHTML += '<span class="bx-messenger-panel-chat-user" data-userId="'+user.id+'"><span class="bx-notifier-popup-avatar bx-notifier-popup-avatar-status-'+user.status+(this.chat[chatId].owner == user.id? ' bx-messenger-panel-chat-user-owner': '')+'"><img class="bx-notifier-popup-avatar-img" src="'+user.avatar+'"></span><span class="bx-notifier-popup-user-name">'+user.name+'</span></span>';
				showUser = true;
			}
		}
	}
	else
	{
		maxCount = Math.floor((this.popupMessengerPanelUsers.offsetWidth-50)/28);
		for (var i = 0; i < this.userInChat[chatId].length && i < maxCount; i++)
		{
			var user = this.users[this.userInChat[chatId][i]];
			if (user)
			{
				this.popupMessengerPanelUsers.innerHTML += '<span class="bx-messenger-panel-chat-user" data-userId="'+user.id+'"><span class="bx-notifier-popup-avatar bx-notifier-popup-avatar-status-'+user.status+(this.chat[chatId].owner == user.id? ' bx-messenger-panel-chat-user-owner': '')+'"><img class="bx-notifier-popup-avatar-img" src="'+user.avatar+'" title="'+user.name+'"></span></span>';
				showUser = true;
			}
		}
		if (showUser && this.userInChat[chatId].length > maxCount)
			this.popupMessengerPanelUsers.innerHTML += '<span class="bx-notifier-popup-user-more" data-last-item="'+i+'">'+BX.message('IM_M_CHAT_MORE_USER').replace('#USER_COUNT#', (this.userInChat[chatId].length-maxCount))+'</span>';
	}
	if (!showUser)
		this.popupMessengerPanelUsers.innerHTML = BX.message('IM_CL_LOAD');
};

BX.Messenger.prototype.renameChatDialog = function()
{
	if (this.renameChatDialogFlag)
		return false;

	this.renameChatDialogFlag = true;

	var chatId = this.currentTab.toString().substr(4);
	this.popupMessengerPanelChatTitle.innerHTML = '';

	BX.adjust(this.popupMessengerPanelChatTitle, {children: [
		BX.create("div", { props : { className : "bx-messenger-input-wrap bx-messenger-panel-title-chat-input" }, children : [
			this.renameChatDialogInput = BX.create("input", {props : { className : "bx-messenger-input" }, attrs: {type: "text", value: BX.util.htmlspecialcharsback(this.chat[chatId].name)}})
		]})
	]});
	this.renameChatDialogInput.focus();
	BX.bind(this.renameChatDialogInput, "blur", BX.delegate(function(){
		this.renameChatDialogInput.value = BX.util.trim(this.renameChatDialogInput.value);
		if (this.renameChatDialogInput.value.length > 0 && this.chat[chatId].name != BX.util.htmlspecialchars(this.renameChatDialogInput.value))
		{
			this.chat[chatId].name = BX.util.htmlspecialchars(this.renameChatDialogInput.value);
			this.popupMessengerPanelChatTitle.innerHTML = this.chat[chatId].name;
			BX.ajax({
				url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?CHAT_RENAME',
				method: 'POST',
				dataType: 'json',
				timeout: 60,
				data: {'IM_CHAT_RENAME' : 'Y', 'CHAT_ID' : chatId, 'CHAT_TITLE': this.renameChatDialogInput.value, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
				onsuccess: BX.delegate(function(){
					if (!this.BXIM.ppServerStatus)
						BX.PULL.updateState(true);
				}, this)
			});
		}
		BX.remove(this.renameChatDialogInput);
		this.renameChatDialogInput = null;
		this.popupMessengerPanelChatTitle.innerHTML = this.chat[chatId].name;
		this.renameChatDialogFlag = false;
	}, this));

	BX.bind(this.renameChatDialogInput, "keydown", BX.delegate(function(e) {
		if (e.keyCode == 27 && !this.desktop.ready())
		{
			this.renameChatDialogInput.value = this.chat[chatId].name;
			this.popupMessengerTextarea.focus();
			return BX.PreventDefault(e);
		}
		else if (e.keyCode == 9 || e.keyCode == 13)
		{
			this.popupMessengerTextarea.focus();
			return BX.PreventDefault(e);
		}
	}, this));
};

BX.Messenger.prototype.loadChatData = function(chatId)
{
	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?CHAT_DATA_LOAD',
		method: 'POST',
		dataType: 'json',
		timeout: 30,
		data: {'IM_CHAT_DATA_LOAD' : 'Y', 'CHAT_ID' : chatId, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
		onsuccess: BX.delegate(function(data)
		{
			if (data.ERROR == '')
			{
				if (this.chat[data.CHAT_ID].fake)
					this.chat[data.CHAT_ID].name = BX.message('IM_M_USER_NO_ACCESS');

				for (var i in data.CHAT)
				{
					this.chat[i] = data.CHAT[i];
				}
				for (var i in data.USER_IN_CHAT)
				{
					this.userInChat[i] = data.USER_IN_CHAT[i];
				}
				this.dialogStatusRedraw();
			}
		}, this),
		onfailure: BX.delegate(function(){
		}, this)
	});
};



BX.Messenger.prototype.openChatDialog = function(params)
{
	if (!this.enableGroupChat)
		return false;

	if (this.popupChatDialog != null)
	{
		this.popupChatDialog.close();
		return false;
	}

	var type = null;
	if (params.type == 'CHAT_ADD' || params.type == 'CHAT_EXTEND' || params.type == 'CALL_INVITE_USER')
		type = params.type;
	else
		return false;

	params.maxUsers = typeof(params.maxUsers) == 'undefined'? 100: parseInt(params.maxUsers);

	var exceptUsers = [];
	if (typeof(params.chatId) != 'undefined' && this.userInChat[params.chatId])
	{
		exceptUsers = this.userInChat[params.chatId];
		params.maxUsers = params.maxUsers-this.userInChat[params.chatId].length;
	}

	var bindElement = params.bind? params.bind: null;

	this.popupChatDialog = new BX.PopupWindow('bx-messenger-popup-newchat', bindElement, {
		lightShadow : true,
		offsetTop: 5,
		offsetLeft: this.desktop.run()? this.webrtc.callActive? 5: 0: this.webrtc.callActive? -162: -170,
		autoHide: true,
		buttons: [
			new BX.PopupWindowButton({
				text : BX.message('IM_M_CHAT_BTN_JOIN'),
				className : "popup-window-button-accept",
				events : { click : BX.delegate(function() {
					if (type == 'CHAT_ADD')
					{
						var arUsers = [this.currentTab];
						for (var i in this.popupChatDialogUsers)
							arUsers.push(this.popupChatDialogUsers[i]);

						this.sendRequestChatDialog(type, arUsers);
					}
					else if (type == 'CHAT_EXTEND')
					{
						var arUsers = [];
						for (var i in this.popupChatDialogUsers)
							arUsers.push(this.popupChatDialogUsers[i]);

						this.sendRequestChatDialog(type, arUsers, this.currentTab.toString().substr(4));
					}
					else if (type == 'CALL_INVITE_USER')
					{
						var arUsers = [];
						for (var i in this.popupChatDialogUsers)
							arUsers.push(this.popupChatDialogUsers[i]);

						this.webrtc.callInviteUserToChat(arUsers);
					}
				}, this) }
			}),
			new BX.PopupWindowButton({
				text : BX.message('IM_M_CHAT_BTN_CANCEL'),
				events : { click : BX.delegate(function() { this.popupChatDialog.close(); }, this) }
			})
		],
		closeByEsc: true,
		zIndex: 200,
		events : {
			onPopupClose : function() { this.destroy() },
			onPopupDestroy : BX.delegate(function() { this.popupChatDialogUsers = {}; this.popupChatDialog = null; this.popupChatDialogContactListElements = null; }, this)
		},
		content : BX.create("div", { props : { className : "bx-messenger-popup-newchat-wrap" }, children: [
			BX.create("div", { props : { className : "bx-messenger-popup-newchat-caption" }, html: BX.message('IM_M_CHAT_TITLE')}),
			BX.create("div", { props : { className : "bx-messenger-popup-newchat-box bx-messenger-popup-newchat-dest bx-messenger-popup-newchat-dest-even" }, children: [
				this.popupChatDialogDestElements = BX.create("span", { props : { className : "bx-messenger-dest-items" }}),
				this.popupChatDialogContactListSearch = BX.create("input", {props : { className : "bx-messenger-input" }, attrs: {type: "text", placeholder: BX.message('IM_M_SEARCH_PLACEHOLDER'), value: ''}})
			]}),
			this.popupChatDialogContactListElements = BX.create("div", { props : { className : "bx-messenger-popup-newchat-box bx-messenger-popup-newchat-cl" }, children: this.contactListPrepare({'viewGroup': true, 'viewChat': false, 'viewOffline': true, 'extra': false, 'groupOpen': true, 'searchText': '', 'exceptUsers': exceptUsers})})
		]})
	});
	this.popupChatDialog.setAngle({offset: this.desktop.run()? 20: 188});
	this.popupChatDialog.show();
	this.popupChatDialogContactListSearch.focus();

	BX.bind(this.popupChatDialog.popupContainer, "click", BX.PreventDefault);

	BX.bind(this.popupChatDialogContactListSearch, "keyup", BX.delegate(function(event){
		if (event.keyCode == 16 || event.keyCode == 17 || event.keyCode == 18 || event.keyCode == 20 || event.keyCode == 244 || event.keyCode == 224 || event.keyCode == 91)
			return false;

		if (event.keyCode == 27 && this.popupChatDialogContactListSearch.value != '')
			BX.IM.preventDefault(event);

		if (event.keyCode == 27)
		{
			this.popupChatDialogContactListSearch.value = '';
		}

		if (event.keyCode == 13)
		{
			this.popupContactListSearchInput.value = '';
			var item = BX.findChild(this.popupChatDialogContactListElements, {className : "bx-messenger-cl-item"}, true);
			if (item)
			{
				if (this.popupChatDialogContactListSearch.value != '')
				{
					this.popupChatDialogContactListSearch.value = '';
					BX.adjust(this.popupChatDialogContactListElements, {children: this.contactListPrepare({ 'viewOffline': true, 'viewChat': false, 'viewGroup': true, 'extra': false, 'searchText': '', 'groupOpen': true, 'exceptUsers': exceptUsers})});
				}
				if (this.popupChatDialogUsers[item.getAttribute('data-userId')])
					delete this.popupChatDialogUsers[item.getAttribute('data-userId')];
				else
					this.popupChatDialogUsers[item.getAttribute('data-userId')] = item.getAttribute('data-userId');

				this.redrawChatDialogDest();
			}
		}

		this.popupChatDialogContactListElements.innerHTML = '';
		BX.adjust(this.popupChatDialogContactListElements, {children: this.contactListPrepare({'groupOpen': true, 'viewOffline': true, 'viewGroup': true, 'viewChat': false, 'extra': false, 'searchText': this.popupChatDialogContactListSearch.value, 'exceptUsers': exceptUsers})});
	}, this));
	BX.bindDelegate(this.popupChatDialogDestElements, "click", {className: 'bx-messenger-dest-del'}, BX.delegate(function() {
		delete this.popupChatDialogUsers[BX.proxy_context.getAttribute('data-userId')];
		params.maxUsers = params.maxUsers+1;
		if (params.maxUsers > 0)
			BX.show(this.popupChatDialogContactListSearch);
		this.redrawChatDialogDest();
	}, this));
	BX.bindDelegate(this.popupChatDialogContactListElements, "click", {className: 'bx-messenger-cl-item'}, BX.delegate(function(e) {
		if (this.popupChatDialogContactListSearch.value != '')
		{
			this.popupChatDialogContactListSearch.value = '';
			BX.adjust(this.popupChatDialogContactListElements, {children: this.contactListPrepare({'viewOffline': true, 'viewGroup': true, 'viewChat': false, 'groupOpen': true,  'extra': false, 'searchText': '', 'exceptUsers': exceptUsers})});
		}
		if (this.popupChatDialogUsers[BX.proxy_context.getAttribute('data-userId')])
		{
			params.maxUsers = params.maxUsers+1;
			delete this.popupChatDialogUsers[BX.proxy_context.getAttribute('data-userId')];
		}
		else
		{
			if (params.maxUsers <= 0)
				return false;
			params.maxUsers = params.maxUsers-1;
			this.popupChatDialogUsers[BX.proxy_context.getAttribute('data-userId')] = BX.proxy_context.getAttribute('data-userId');
		}
		if (params.maxUsers <= 0)
			BX.hide(this.popupChatDialogContactListSearch);
		else
			BX.show(this.popupChatDialogContactListSearch);

		this.redrawChatDialogDest();

		return BX.PreventDefault(e);
	}, this));
};

BX.Messenger.prototype.redrawChatDialogDest = function()
{
	var content = '';
	var count = 0;
	for (var i in this.popupChatDialogUsers)
	{
		count++;
		content += '<span class="bx-messenger-dest-block">'+
						'<span class="bx-messenger-dest-text">'+(this.users[i].name)+'</span>'+
					'<span class="bx-messenger-dest-del" data-userId="'+i+'"></span></span>';
	}

	this.popupChatDialogDestElements.innerHTML = content;
	this.popupChatDialogDestElements.parentNode.scrollTop = this.popupChatDialogDestElements.parentNode.offsetHeight;

	if (BX.util.even(count))
		BX.addClass(this.popupChatDialogDestElements.parentNode, 'bx-messenger-popup-newchat-dest-even');
	else
		BX.removeClass(this.popupChatDialogDestElements.parentNode, 'bx-messenger-popup-newchat-dest-even');

	this.popupChatDialogContactListSearch.focus();
};

BX.Messenger.prototype.sendRequestChatDialog = function(type, users, chatId)
{
	if (this.popupChatDialogSendBlock)
		return false;

	var error = '';
	if (type == 'CHAT_ADD' && users.length <= 1)
	{
		error = BX.message('IM_M_CHAT_ERROR_1');
	}
	else if (type == 'CHAT_EXTEND' && users.length == 0)
	{
		if (this.popupChatDialog != null)
			this.popupChatDialog.close();
		return false;
	}

	if (error != "")
	{
		this.BXIM.openConfirm(error);
		return false;
	}

	this.popupChatDialogSendBlock = true;
	if (this.popupChatDialog != null)
		this.popupChatDialog.buttons[0].setClassName('popup-window-button-disable');

	var data = false;
	if (type == 'CHAT_ADD')
		data = {'IM_CHAT_ADD' : 'Y', 'USERS' : JSON.stringify(users), 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()};
	else if (type == 'CHAT_EXTEND')
		data = {'IM_CHAT_EXTEND' : 'Y', 'CHAT_ID' : chatId, 'USERS' : JSON.stringify(users), 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()};

	if (!data)
		return false;

	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?'+type,
		method: 'POST',
		dataType: 'json',
		timeout: 60,
		data: data,
		onsuccess: BX.delegate(function(data){
			this.popupChatDialogSendBlock = false;
			if (this.popupChatDialog != null)
				this.popupChatDialog.buttons[0].setClassName('popup-window-button-accept');
			if (data.ERROR == '')
			{
				if (!this.BXIM.ppServerStatus)
					BX.PULL.updateState(true);

				if (data.CHAT_ID)
				{
					if (this.BXIM.ppServerStatus && this.currentTab != 'chat'+data.CHAT_ID)
					{
						this.openMessenger('chat'+data.CHAT_ID);
					}
					else if (!this.BXIM.ppServerStatus && this.currentTab != 'chat'+data.CHAT_ID)
					{
						setTimeout( BX.delegate(function(){
							this.openMessenger('chat'+data.CHAT_ID);
						}, this), 500);
					}
				}
				this.popupChatDialogSendBlock = false;
				if (this.popupChatDialog != null)
					this.popupChatDialog.close();
			}
			else
			{
				this.BXIM.openConfirm(data.ERROR);
			}
		}, this)
	});
};

/* RL & CL */
BX.Messenger.prototype.userListRedraw = function(params)
{
	if (this.recentList && this.contactListSearchText != null && this.contactListSearchText.length == 0)
		this.recentListRedraw(params);
	else
		this.contactListRedraw(params);
};

/* RL */
BX.Messenger.prototype.recentListRedraw = function(params)
{
	if (this.popupMessenger == null)
		return false;

	this.recentList = true;
	BX.addClass(this.recentListTab, 'bx-messenger-cl-switcher-tab-active');
	this.contactList = false;
	BX.removeClass(this.contactListTab, 'bx-messenger-cl-switcher-tab-active');

	if (this.contactListSearchText != null && this.contactListSearchText.length == 0)
		this.recentListReturn = true;
	else
	{
		this.contactListSearchText = '';
		this.popupContactListSearchInput.value = '';
	}

	if (this.popupPopupMenu != null)
		this.popupPopupMenu.close();

	BX.addClass(this.popupContactListElementsWrap, 'bx-messenger-recent-wrap');
	this.popupContactListElementsWrap.innerHTML = '';
	BX.adjust(this.popupContactListElementsWrap, {children: this.recentListPrepare(params)});
};

BX.Messenger.prototype.recentListPrepare = function(params)
{
	var items = [];
	var groups = {};
	params = typeof(params) == 'object'? params: {};

	var showOnlyChat = params.showOnlyChat;

	if (!this.recentListLoad)
	{
		items.push(BX.create("div", {
			props : { className: "bx-messenger-cl-item-load"},
			html : BX.message('IM_CL_LOAD')
		}));

		this.recentListGetFromServer();
		return items;
	}
	this.recent.sort(function(i, ii) {var i1 = parseInt(i.date); var i2 = parseInt(ii.date); if (i1 > i2) { return -1; } else if (i1 < i2) { return 1;} else{ if (i > ii) { return -1; } else if (i < ii) { return 1;}else{ return 0;}}});
	this.recentListIndex = [];
	for (var i = 0; i < this.recent.length; i++)
	{
		if (typeof(this.recent[i].userIsChat) == 'undefined')
			this.recent[i].userIsChat = this.recent[i].recipientId.toString().substr(0,4) == 'chat';

		var item = BX.clone(this.recent[i]);
		if (item.userIsChat)
		{
			user = this.chat[item.userId.toString().substr(4)];
			if (typeof(user) == 'undefined' || typeof(user.name) == 'undefined')
				continue;
			var userId = 'chat'+user.id;
		}
		else if (!showOnlyChat)
		{
			var user = this.users[item.userId];
			if (typeof(user) == 'undefined' || this.BXIM.userId == user.id || typeof(user.name) == 'undefined')
				continue;

			var userId = user.id;
		}
		else
		{
			continue;
		}

		if (item.date > 0)
		{
			var format = [
				["tommorow", "tommorow"],
				["today", "today"],
				["yesterday", "yesterday"],
				["", BX.date.convertBitrixFormat(BX.message("IM_RESENT_FORMAT_DATE"))]
			];
			item.date = BX.date.format(format, parseInt(item.date)+parseInt(BX.message("SERVER_TZ_OFFSET")), BX.IM.getNowDate()+parseInt(BX.message("SERVER_TZ_OFFSET")), true);
			if (!groups[item.date])
			{
				groups[item.date] = true;
				items.push(BX.create("div", {props : { className: "bx-messenger-recent-group"}, children : [
					BX.create("span", {props : { className: "bx-messenger-recent-group-title"}, html : item.date})
				]}));
			}
		}
		else
		{
			if (!groups['never'])
			{
				groups['never'] = true;
				items.push(BX.create("div", {props : { className: "bx-messenger-recent-group"}, children : [
					BX.create("span", {props : { className: "bx-messenger-recent-group-title"}, html : BX.message('IM_RESENT_NEVER')})
				]}));
			}
		}

		var newMessage = '';
		var newMessageCount = '';
		if (this.unreadMessage[userId] && this.unreadMessage[userId].length>0)
		{
			newMessage = 'bx-messenger-cl-status-new-message';
			newMessageCount = '<span class="bx-messenger-cl-count-digit">'+(this.unreadMessage[userId].length<100? this.unreadMessage[userId].length: '99+')+'</span>';
		}

		var writingMessage = '';
		var directionIcon = '';

		if (this.countWriting(userId))
			writingMessage = 'bx-messenger-cl-status-writing';

		if (item.senderId != this.BXIM.userId)
			directionIcon = '<span class="bx-messenger-cl-user-reply"></span>';

		if (!user.avatar)
			user.avatar = '/bitrix/js/im/images/blank.gif';

		items.push(BX.create("a", {
			props : { className: item.userIsChat? "bx-messenger-cl-item bx-messenger-cl-item-chat " +newMessage+" "+writingMessage: "bx-messenger-cl-item bx-messenger-cl-status-" +(user.birthday? 'birthday': user.status)+ " " +newMessage+" "+writingMessage },
			attrs : { href: item.userIsChat? '#chat'+user.id: '#user'+user.id, 'data-userId' : userId, 'data-name' : user.name, 'data-status' : user.status? user.status: 'online', 'data-avatar' : user.avatar, 'data-userIsChat' : item.userIsChat },
			html :  '<span class="bx-messenger-cl-count">'+newMessageCount+'</span>'+
					'<span class="bx-messenger-cl-avatar '+(item.userIsChat? 'bx-messenger-cl-avatar-'+user.style: '')+'"><img class="bx-messenger-cl-avatar-img" src="'+user.avatar+'"><span class="bx-messenger-cl-status"></span></span>'+
					'<span class="bx-messenger-cl-user"><div class="bx-messenger-cl-user-title">'+(user.nameList? user.nameList: user.name)+'</div>'+
					'<div class="bx-messenger-cl-user-desc">'+directionIcon+''+BX.IM.prepareText(item.text)+'</div></span>'
		}));

		this.recentListIndex.push(userId);
	}

	if (items.length <= 0)
	{
		items.push(BX.create("div", {
			props : { className: "bx-messenger-cl-item-empty"},
			html :  BX.message('IM_M_CL_EMPTY')
		}));
	}
	return items;
};

BX.Messenger.prototype.recentListAdd = function(params)
{
	params.text = params.text.replace(/<img.*?data-code="([^"]*)".*?>/ig, '$1');
	params.text = params.text.replace(/<s>([^"]*)<\/s>/ig, '');
	params.text = params.text.replace('<br />', ' ').replace(/<\/?[^>]+>/gi, '').replace(/------------------------------------------------------(.*?)------------------------------------------------------/gmi, " ["+BX.message("IM_M_QUOTE_BLOCK")+"] ");

	if (!params.skipDateCheck)
	{
		for (var i = 0; i < this.recent.length; i++)
		{
			if (this.recent[i].userId == params.userId && this.recent[i].date > params.date)
				return false;
		}
	}

	var newRecent = [];
	newRecent.push(params);

	for (var i = 0; i < this.recent.length; i++)
		if (this.recent[i].userId != params.userId)
			newRecent.push(this.recent[i]);

	this.recent = newRecent;

	if (this.recentList)
		this.recentListRedraw();
};

BX.Messenger.prototype.recentListHide = function(userId, sendAjax)
{
	var newRecent = [];
	for (var i = 0; i < this.recent.length; i++)
		if (this.recent[i].userId != userId)
			newRecent.push(this.recent[i]);

	this.recent = newRecent;
	if (this.recentList)
		this.recentListRedraw();

	BX.localStorage.set('mrlr', userId, 5);

	sendAjax = sendAjax != false;
	if (sendAjax)
	{
		BX.ajax({
			url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?RECENT_HIDE',
			method: 'POST',
			dataType: 'json',
			timeout: 60,
			data: {'IM_RECENT_HIDE' : 'Y', 'USER_ID' : userId, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()}
		});
		this.readMessage(userId, true, true);
	}
};

BX.Messenger.prototype.recentListGetFromServer = function()
{
	if (this.recentListLoad)
		return false;

	this.recentListLoad = true;
	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?RECENT_LIST',
		method: 'POST',
		dataType: 'json',
		timeout: 30,
		data: {'IM_RECENT_LIST' : 'Y', 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
		onsuccess: BX.delegate(function(data)
		{
			if (data.ERROR == '')
			{
				this.recent = [];
				for (var i in data.RECENT)
				{
					data.RECENT[i].date = parseInt(data.RECENT[i].date)-parseInt(BX.message('USER_TZ_OFFSET'));
					this.recent.push(data.RECENT[i]);
				}

				var arRecent = false;
				for(var i in this.unreadMessage)
				{
					for (var k = 0; k < this.unreadMessage[i].length; k++)
					{
						if (!arRecent || arRecent.SEND_DATE <= this.message[this.unreadMessage[i][k]].date)
						{
							arRecent = {
								'ID': this.message[this.unreadMessage[i][k]].id,
								'SEND_DATE': this.message[this.unreadMessage[i][k]].date,
								'RECIPIENT_ID': this.message[this.unreadMessage[i][k]].recipientId,
								'SENDER_ID': this.message[this.unreadMessage[i][k]].senderId,
								'USER_ID': this.message[this.unreadMessage[i][k]].senderId,
								'SEND_MESSAGE': this.message[this.unreadMessage[i][k]].text
							};
						}
					}
				}
				if (arRecent)
				{
					this.recentListAdd({
						'userId': arRecent.RECIPIENT_ID.toString().substr(0,4) == 'chat'? arRecent.RECIPIENT_ID: arRecent.USER_ID,
						'id': arRecent.ID,
						'date': arRecent.SEND_DATE,
						'recipientId': arRecent.RECIPIENT_ID,
						'senderId': arRecent.SENDER_ID,
						'text': arRecent.SEND_MESSAGE
					}, true);
				}

				for (var i in data.CHAT)
				{
					if (this.chat[i] && this.chat[i].fake)
						data.CHAT[i].fake = true;
					else if (!this.chat[i])
						data.CHAT[i].fake = true;

					this.chat[i] = data.CHAT[i];
				}

				for (var i in data.USERS)
					this.users[i] = data.USERS[i];

				if (this.recentList)
					this.recentListRedraw();

				this.smile = data.SMILE;
				this.smileSet = data.SMILE_SET;

				this.BXIM.settingsNotifyBlocked = data.NOTIFY_BLOCKED;

				if (this.smile != false)
					BX.removeClass(this.popupMessengerTextareaPlace, 'bx-messenger-textarea-smile-disabled');

				this.dialogStatusRedraw();
			}
			else
			{
				this.recentListLoad = false;
				if (data.ERROR == 'SESSION_ERROR' && this.sendAjaxTry < 2)
				{
					this.sendAjaxTry++;
					BX.message({'bitrix_sessid': data.BITRIX_SESSID});
					setTimeout(BX.delegate(this.recentListGetFromServer, this), 2000);
					BX.onCustomEvent(window, 'onImError', [data.ERROR, data.BITRIX_SESSID]);
				}
				else if (data.ERROR == 'AUTHORIZE_ERROR' && this.sendAjaxTry < 1)
				{
					this.sendAjaxTry++;
					setTimeout(BX.delegate(this.recentListGetFromServer, this), 10000);
					BX.onCustomEvent(window, 'onImError', [data.ERROR]);
				}
			}
		}, this),
		onfailure: BX.delegate(function(){
			this.sendAjaxTry = 0;
			this.recentListLoad = false;
		}, this)
	});
};

/* CL */
BX.Messenger.prototype.contactListRedraw = function(send)
{
	if (this.popupMessenger == null)
		return false;

	this.contactList = true;
	BX.addClass(this.contactListTab, 'bx-messenger-cl-switcher-tab-active');
	this.recentList = false;
	BX.removeClass(this.recentListTab, 'bx-messenger-cl-switcher-tab-active');

	if (this.contactListSearchText != null && this.contactListSearchText.length == 0)
		this.recentListReturn = false;

	if (this.popupPopupMenu != null)
		this.popupPopupMenu.close();

	BX.removeClass(this.popupContactListElementsWrap, 'bx-messenger-recent-wrap');
	this.popupContactListElementsWrap.innerHTML = '';
	BX.adjust(this.popupContactListElementsWrap, {children: this.contactListPrepare()});

	send = send == true;
	if (send)
		BX.localStorage.set('mrd', {viewGroup: this.BXIM.settings.viewGroup, viewOffline: this.BXIM.settings.viewOffline}, 5);
};

BX.Messenger.prototype.contactListPrepare = function(params)
{
	params = typeof(params) == 'object'? params: {};
	var items = [];
	var groupsTmp = {};
	var groups = {};
	var unreadUsers = [];
	var userInGroup = {};

	var searchText = typeof(params.searchText) != 'undefined'? params.searchText: this.contactListSearchText;
	var activeSearch = !(searchText != null && searchText.length == 0);
	var extraEnable =  typeof(params.extra) != 'undefined'? params.extra: true;
	var groupOpen =  typeof(params.groupOpen) != 'undefined'? params.groupOpen: 'auto';
	var viewGroup =  typeof(params.viewGroup) != 'undefined'? params.viewGroup: activeSearch? false: this.BXIM.settings.viewGroup;
	var viewOffline =  typeof(params.viewOffline) != 'undefined'? params.viewOffline: activeSearch? true: this.BXIM.settings.viewOffline;
	var viewChat =  typeof(params.viewChat) != 'undefined'? params.viewChat: true;

	var exceptUsers = {};
	if (typeof(params.exceptUsers) != 'undefined')
	{
		for (var i = 0; i < params.exceptUsers.length; i++)
			exceptUsers[params.exceptUsers[i]] = true;
	}

	if (viewGroup)
	{
		groupsTmp = this.groups;
		userInGroup = this.userInGroup;
	}
	else
	{
		groupsTmp = this.woGroups;
		userInGroup = this.woUserInGroup;
	}

	var groupCount = 0;
	for (var i in groupsTmp)
		groupCount++;

	if (groupCount <= 0 && !this.contactListLoad)
	{
		items.push(BX.create("div", {
			props : { className: "bx-messenger-cl-item-load"},
			html : BX.message('IM_CL_LOAD')
		}));

		this.contactListGetFromServer();
		return items;

	}
	var arSearch = [];
	if (activeSearch)
		arSearch = (searchText+'').split(" ");

	groups[0] = {'id': 0, 'name': BX.message('IM_M_CL_UNREAD'), 'status':'open'};
	for (var i in this.unreadMessage) unreadUsers.push(i);
	userInGroup[0] = {'id':0, 'users': unreadUsers};
	for (var i in groupsTmp)
	{
		if (i != 'last' && i != 0 )
			groups[i] = groupsTmp[i];
	}
	if (activeSearch && viewChat)
	{
		groups['chat'] = {'id': 'chat', 'name': BX.message('IM_M_CALL_BTN_CHAT'), 'status':'open'};
		var groupChat = [];
		for (var i in this.chat) groupChat.push(i);
		userInGroup['chat'] = {'id':'chat', 'users': groupChat, 'isChat': true};
	}

	for (var i in groups)
	{
		var group = groups[i];
		if (typeof(group) == 'undefined' || !group.name || !BX.type.isNotEmptyString(group.name))
			continue;

		var userItems = [];
		if (userInGroup[i] && !userInGroup[i].isChat)
		{
			for (var j = 0; j < userInGroup[i].users.length; j++)
			{
				var user = this.users[userInGroup[i].users[j]];
				if (typeof(user) == 'undefined' || this.BXIM.userId == user.id || typeof(user.name) == 'undefined' || exceptUsers[user.id])
					continue;

				if (activeSearch)
				{
					var skipUser = false;
					for (var s = 0; s < arSearch.length; s++)
						if (user.name.toLowerCase().indexOf(arSearch[s].toLowerCase()) < 0)
							skipUser = true;

					if (skipUser)
						continue;
				}

				var newMessage = '';
				var newMessageCount = '';
				if (extraEnable && this.unreadMessage[user.id] && this.unreadMessage[user.id].length>0)
				{
					newMessage = 'bx-messenger-cl-status-new-message';
					newMessageCount = '<span class="bx-messenger-cl-count-digit">'+(this.unreadMessage[user.id].length<100? this.unreadMessage[user.id].length: '99+')+'</span>';
				}

				var writingMessage = '';
				if (extraEnable && this.countWriting(user.id))
					writingMessage = 'bx-messenger-cl-status-writing';

				if (i != 'last' && viewOffline == false && user.status == "offline" && newMessage == '')
					continue;

				var src = '_src="'+user.avatar+'" src="/bitrix/js/im/images/blank.gif"';
				if (activeSearch || (group.status == "open" && groupOpen == 'auto') || groupOpen == true)
					src = 'src="'+user.avatar+'" _src="/bitrix/js/im/images/blank.gif"';

				userItems.push(BX.create("a", {
					props : { className: "bx-messenger-cl-item bx-messenger-cl-status-" +(user.birthday? 'birthday': user.status)+ " " +newMessage+" "+writingMessage },
					attrs : { href:'#user'+user.id, 'data-userId' : user.id, 'data-name' : user.name, 'data-status' : user.status, 'data-avatar' : user.avatar },
					html :  '<span class="bx-messenger-cl-count">'+newMessageCount+'</span>'+
							'<span class="bx-messenger-cl-avatar"><img class="bx-messenger-cl-avatar-img" '+src+'><span class="bx-messenger-cl-status"></span></span>'+
							'<span class="bx-messenger-cl-user">'+(user.nameList? user.nameList: user.name)+'</span>'
				}));
			}
			if (userItems.length > 0)
			{
				items.push(BX.create("div", {
					attrs : { 'data-groupId-wrap' : group.id },
					props : { className: "bx-messenger-cl-group" +  (activeSearch || (group.status == "open" && groupOpen == 'auto') || groupOpen == true ? " bx-messenger-cl-group-open" : "")},
					children : [
						BX.create("div", {props : { className: "bx-messenger-cl-group-title"}, attrs : { 'data-groupId' : group.id, title : group.name }, html : group.name}),
						BX.create("span", {props : { className: "bx-messenger-cl-group-wrapper"}, children : userItems})
					]
				}));
			}
		}
		else if (userInGroup[i] && userInGroup[i].isChat)
		{
			for (var j = 0; j < userInGroup[i].users.length; j++)
			{
				var chat = this.chat[userInGroup[i].users[j]];
				if (typeof (chat) == 'undefined' || typeof(chat.name) == 'undefined')
					continue;

				if (activeSearch)
				{
					var skipUser = false;
					for (var s = 0; s < arSearch.length; s++)
						if (chat.name.toLowerCase().indexOf(arSearch[s].toLowerCase()) < 0)
							skipUser = true;

					if (skipUser)
						continue;
				}

				var writingMessage = '';
				if (extraEnable && this.countWriting('chat'+chat.id))
					writingMessage = 'bx-messenger-cl-status-writing';

				var newMessage = '';
				var newMessageCount = '';
				if (extraEnable && this.unreadMessage['chat'+chat.id] && this.unreadMessage['chat'+chat.id].length>0)
				{
					newMessage = 'bx-messenger-cl-status-new-message';
					newMessageCount = '<span class="bx-messenger-cl-count-digit">'+(this.unreadMessage['chat'+chat.id].length<100? this.unreadMessage['chat'+chat.id].length: '99+')+'</span>';
				}

				var src = '_src="'+chat.avatar+'" src="/bitrix/js/im/images/blank.gif"';
				if (activeSearch || (group.status == "open" && groupOpen == 'auto') || groupOpen == true)
					src = 'src="'+chat.avatar+'" _src="/bitrix/js/im/images/blank.gif"';

				userItems.push(BX.create("a", {
					props : { className: "bx-messenger-cl-item bx-messenger-cl-status-online "+newMessage+" "+writingMessage},
					attrs : { href:'#chat'+chat.id, 'data-userId' : 'chat'+chat.id,  'data-userIsChat' : 'Y', 'data-name' : chat.name, 'data-status' : 'online', 'data-avatar' : chat.avatar },
					html :  '<span class="bx-messenger-cl-count">'+newMessageCount+'</span>'+
							'<span class="bx-messenger-cl-avatar bx-messenger-cl-avatar-'+chat.style+'"><img class="bx-messenger-cl-avatar-img" '+src+'><span class="bx-messenger-cl-status"></span></span>'+
							'<span class="bx-messenger-cl-user">'+chat.name+'</span>'
				}));
			}
			if (userItems.length > 0)
			{
				items.push(BX.create("div", {
					attrs : { 'data-groupId-wrap' : group.id },
					props : { className: "bx-messenger-cl-group" +  (activeSearch || (group.status == "open" && groupOpen == 'auto') || groupOpen == true ? " bx-messenger-cl-group-open" : "")},
					children : [
						BX.create("div", {props : { className: "bx-messenger-cl-group-title"}, attrs : { 'data-groupId' : group.id, title : group.name }, html : group.name}),
						BX.create("span", {props : { className: "bx-messenger-cl-group-wrapper"}, children : userItems})
					]
				}));
			}
		}
	}
	if (items.length <= 0)
	{
		items.push(BX.create("div", {
			props : { className: "bx-messenger-cl-item-empty"},
			html :  BX.message('IM_M_CL_EMPTY')
		}));
	}

	return items;
};

BX.Messenger.prototype.contactListGetFromServer = function()
{
	if (this.contactListLoad)
		return false;

	this.contactListLoad = true;
	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?CONTACT_LIST',
		method: 'POST',
		dataType: 'json',
		timeout: 30,
		data: {'IM_CONTACT_LIST' : 'Y', 'IM_AJAX_CALL' : 'Y', 'DESKTOP' : (this.desktop.ready()? 'Y': 'N'), 'sessid': BX.bitrix_sessid()},
		onsuccess: BX.delegate(function(data)
		{
			if (data.ERROR == '')
			{
				for (var i in data.USERS)
					this.users[i] = data.USERS[i];

				for (var i in data.GROUPS)
					this.groups[i] = data.GROUPS[i];

				for (var i in data.USER_IN_GROUP)
				{
					if (typeof(this.userInGroup[i]) == 'undefined')
					{
						this.userInGroup[i] = data.USER_IN_GROUP[i];
					}
					else
					{
						for (var j = 0; j < data.USER_IN_GROUP[i].users.length; j++)
							this.userInGroup[i].users.push(data.USER_IN_GROUP[i].users[j]);

						this.userInGroup[i].users = BX.util.array_unique(this.userInGroup[i].users)
					}
				}

				for (var i in data.WO_GROUPS)
					this.woGroups[i] = data.WO_GROUPS[i];

				for (var i in data.WO_USER_IN_GROUP)
				{
					if (typeof(this.woUserInGroup[i]) == 'undefined')
					{
						this.woUserInGroup[i] = data.WO_USER_IN_GROUP[i];
					}
					else
					{
						for (var j = 0; j < data.WO_USER_IN_GROUP[i].users.length; j++)
							this.woUserInGroup[i].users.push(data.WO_USER_IN_GROUP[i].users[j]);

						this.woUserInGroup[i].users = BX.util.array_unique(this.woUserInGroup[i].users)
					}
				}

				this.userListRedraw();
				this.dialogStatusRedraw();

				if (this.popupChatDialogContactListElements != null)
				{
					this.popupChatDialogContactListElements.innerHTML = '';
					BX.adjust(this.popupChatDialogContactListElements, {children: this.contactListPrepare({'groupOpen': true, 'viewOffline': true, 'viewGroup': false, 'extra': false, 'searchText': this.popupChatDialogContactListSearch.value})});
				}
			}
			else
			{
				this.contactListLoad = false;
				if (data.ERROR == 'SESSION_ERROR' && this.sendAjaxTry < 2)
				{
					this.sendAjaxTry++;
					BX.message({'bitrix_sessid': data.BITRIX_SESSID});
					setTimeout(BX.delegate(this.contactListGetFromServer, this), 2000);
					BX.onCustomEvent(window, 'onImError', [data.ERROR, data.BITRIX_SESSID]);
				}
				else if (data.ERROR == 'AUTHORIZE_ERROR' && this.sendAjaxTry < 1)
				{
					this.sendAjaxTry++;
					setTimeout(BX.delegate(this.contactListGetFromServer, this), 10000);
					BX.onCustomEvent(window, 'onImError', [data.ERROR]);
				}
			}
		}, this),
		onfailure: BX.delegate(function(){
			this.sendAjaxTry = 0;
			this.contactListLoad = false;
		}, this)
	});
};

BX.Messenger.prototype.openContactList = function()
{
	return this.openMessenger();
};

BX.Messenger.prototype.contactListSearch = function(event)
{
	if (event.keyCode == 16 || event.keyCode == 17 || event.keyCode == 18 || event.keyCode == 20 || event.keyCode == 244 || event.keyCode == 224 || event.keyCode == 91)
		return false;

	this.recentList = false;
	this.contactList = true;

	if (event.keyCode == 27)
	{
		if (this.contactListSearchText <= 0)
		{
			this.popupContactListSearchInput.value = "";
			if (this.popupMessenger && !this.desktop.ready() && !this.webrtc.callInit)
				this.popupMessenger.destroy();
		}
		else
		{
			this.popupContactListSearchInput.value = "";
			this.popupMessengerTextarea.focus();
		}
	}

	if (event.keyCode == 13)
	{
		this.popupContactListSearchInput.value = '';
		var item = BX.findChild(this.popupContactListElementsWrap, {className : "bx-messenger-cl-item"}, true);
		if (item)
		{
			this.openMessenger(item.getAttribute('data-userid'));
		}
	}

	this.contactListSearchText = BX.util.trim(this.popupContactListSearchInput.value);
	BX.localStorage.set('mns', this.contactListSearchText, 5);

	if (this.contactListSearchText == '')
	{
		if (this.recentListReturn)
		{
			this.recentList = true;
			this.contactList = false;
		}
	}
	else if (this.realSearch)
	{
		clearTimeout(this.contactListSearchTimeout);
		this.contactListSearchTimeout = setTimeout(BX.delegate(function(){
			if (this.contactListSearchText.length <= 3)
				return false;

			BX.ajax({
				url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?CONTACT_LIST_SEARCH',
				method: 'POST',
				dataType: 'json',
				timeout: 30,
				data: {'IM_CONTACT_LIST_SEARCH' : 'Y', 'SEARCH' : this.contactListSearchText, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
				onsuccess: BX.delegate(function(data){
					if (!this.userInGroup['other'])
						this.userInGroup['other'] = {'id':'other', 'users': []};
					if (!this.woUserInGroup['other'])
						this.woUserInGroup['other'] = {'id':'other', 'users': []};

					var _users = BX.clone(this.userInGroup['other']['users']);
					var _woUsers = BX.clone(this.woUserInGroup['other']['users']);
					for (var i in data.USERS)
					{
						this.users[i] = data.USERS[i];
						this.userInGroup['other']['users'].push(i);
						this.woUserInGroup['other']['users'].push(i);
					}

					if (this.contactList)
						this.userListRedraw();

					this.userInGroup['other']['users'] = _users;
					this.woUserInGroup['other']['users'] = _woUsers;

				}, this),
				onfailure: function(data)	{}
			});
		}, this), 1000);
	}
	this.userListRedraw();
};

BX.Messenger.prototype.openPopupMenu = function(bind, type, setAngle, params)
{
	if (this.popupSmileMenu != null)
		this.popupSmileMenu.destroy();
	if (this.popupPopupMenu != null)
	{
		this.popupPopupMenu.destroy();
		return false;
	}
	var offsetTop = 0;
	var offsetLeft = 10;
	var menuItems = [];
	var bindOptions = {};
	if (type == 'status')
	{
		bindOptions = {position: "top"};
		menuItems = [
			{icon: 'bx-messenger-status-online', text: BX.message("IM_STATUS_ONLINE"), onclick: BX.delegate(function(){ this.setStatus('online'); this.closeMenuPopup(); }, this)},
			{icon: 'bx-messenger-status-dnd', text: BX.message("IM_STATUS_DND"), onclick: BX.delegate(function(){ this.setStatus('dnd'); this.closeMenuPopup(); }, this)}
		];
	}
	else if (type == 'notifyDelete')
	{
		var notifyId = bind.getAttribute('data-notifyId');
		var settingName = this.notify.notify[notifyId].settingName;
		var blockNotifyText = typeof (this.BXIM.settingsNotifyBlocked[settingName]) == 'undefined'? BX.message("IM_NOTIFY_DELETE_2"): BX.message("IM_NOTIFY_DELETE_3");
		menuItems = [
			{text: BX.message("IM_NOTIFY_DELETE_1"), onclick: BX.delegate(function(){ this.notify.deleteNotify(notifyId); this.closeMenuPopup(); }, this)},
			{text: blockNotifyText, onclick: BX.delegate(function(){ this.notify.blockNotifyType(settingName); this.closeMenuPopup(); }, this)}
		];
	}
	else if (type == 'callMenu')
	{
		offsetTop = 2;
		offsetLeft = 20;

		menuItems = [
			{icon: 'bx-messenger-menu-call-video', text: BX.message('IM_M_CALL_VIDEO'), onclick: BX.delegate(function(){ this.BXIM.callTo(this.currentTab, true); this.closeMenuPopup(); }, this)},
			{icon: 'bx-messenger-menu-call-voice', text: BX.message('IM_M_CALL_VOICE'), onclick: BX.delegate(function(){ this.BXIM.callTo(this.currentTab, false); this.closeMenuPopup(); }, this)},
			this.webrtc.screenSharingEnabled? {icon: 'bx-messenger-menu-call-screen', text: BX.message('IM_M_CALL_SCREEN'), onclick: BX.delegate(function(){ this.webrtc.callInvite(this.currentTab, true, true); this.closeMenuPopup(); }, this)}: null
		];

		if (!this.openChatFlag && this.phones[this.currentTab])
		{
			menuItems.push({separator: true});

			if (this.phones[this.currentTab].PERSONAL_MOBILE)
			{
				menuItems.push(
					{type: 'call', text: BX.message('IM_PHONE_PERSONAL_MOBILE'), phone: BX.util.htmlspecialchars(this.phones[this.currentTab].PERSONAL_MOBILE), onclick: BX.delegate(function(){ this.BXIM.phoneTo(this.phones[this.currentTab].PERSONAL_MOBILE); this.closeMenuPopup(); }, this)}
				);
			}

			if (this.phones[this.currentTab].PERSONAL_PHONE)
			{
				menuItems.push(
					{type: 'call', text: BX.message('IM_PHONE_PERSONAL_PHONE'), phone: BX.util.htmlspecialchars(this.phones[this.currentTab].PERSONAL_PHONE), onclick: BX.delegate(function(){ this.BXIM.phoneTo(this.phones[this.currentTab].PERSONAL_PHONE); this.closeMenuPopup(); }, this)}
				);
			}

			if (this.phones[this.currentTab].WORK_PHONE)
			{
				menuItems.push(
					{type: 'call', text: BX.message('IM_PHONE_WORK_PHONE'), phone: BX.util.htmlspecialchars(this.phones[this.currentTab].WORK_PHONE), onclick: BX.delegate(function(){ this.BXIM.phoneTo(this.phones[this.currentTab].WORK_PHONE); this.closeMenuPopup(); }, this)}
				);
			}
		}
	}
	else if (type == 'callPhoneMenu')
	{
		offsetTop = 2;
		offsetLeft = 25;

		menuItems = [
			{icon: 'bx-messenger-menu-call-'+(params.video? 'video': 'voice'), text: '<b>'+BX.message('IM_M_CALL_BTN_RECALL_3')+'</b>', onclick: BX.delegate(function(){ this.webrtc.callInvite(params.userId, params.video) }, this)}
		];
		menuItems.push({separator: true});
		if (this.phones[this.currentTab])
		{
			menuItems.push({separator: true});

			if (this.phones[params.userId].PERSONAL_MOBILE)
			{
				menuItems.push(
					{type: 'call', text: BX.message('IM_PHONE_PERSONAL_MOBILE'), phone: BX.util.htmlspecialchars(this.phones[params.userId].PERSONAL_MOBILE), onclick: BX.delegate(function(){
						this.BXIM.phoneTo(this.phones[params.userId].PERSONAL_MOBILE);
						this.closeMenuPopup();
					}, this)}
				);
			}

			if (this.phones[params.userId].PERSONAL_PHONE)
			{
				menuItems.push(
					{type: 'call', text: BX.message('IM_PHONE_PERSONAL_PHONE'), phone: BX.util.htmlspecialchars(this.phones[params.userId].PERSONAL_PHONE), onclick: BX.delegate(function(){
						this.BXIM.phoneTo(this.phones[params.userId].PERSONAL_PHONE);
						this.closeMenuPopup();
					}, this)}
				);
			}

			if (this.phones[params.userId].WORK_PHONE)
			{
				menuItems.push(
					{type: 'call', text: BX.message('IM_PHONE_WORK_PHONE'), phone: BX.util.htmlspecialchars(this.phones[params.userId].WORK_PHONE), onclick: BX.delegate(function(){
						this.BXIM.phoneTo(this.phones[params.userId].WORK_PHONE);
						this.closeMenuPopup();
					}, this)}
				);
			}
		}
	}
	else if (type == 'chatUser')
	{
		var userId = bind.getAttribute('data-userId');
		var chatId = this.currentTab.toString().substr(4);
		if (userId == this.BXIM.userId)
		{
			menuItems = [
				{icon: 'bx-messenger-menu-chat-exit', text: BX.message('IM_M_CHAT_EXIT'), onclick: BX.delegate(function(){ this.leaveFromChat(chatId); this.closeMenuPopup();}, this)}
			];
		}
		else
		{
			menuItems = [
				{icon: 'bx-messenger-menu-chat-put', text: BX.message('IM_M_CHAT_PUT'), onclick: BX.delegate(function(){ this.insertTextareaText(' '+BX.util.htmlspecialcharsback(this.users[userId].name)+', ', false); this.popupMessengerTextarea.focus(); this.closeMenuPopup(); }, this)},
				{icon: 'bx-messenger-menu-write', text: BX.message('IM_M_WRITE_MESSAGE'), onclick: BX.delegate(function(){ this.openMessenger(userId); this.closeMenuPopup(); }, this)},
				(!this.webrtc.callSupport(userId, this) || this.webrtc.callInit)? null: {icon: 'bx-messenger-menu-video', text: BX.message('IM_M_CALL_VIDEO'), onclick: BX.delegate(function(){ this.BXIM.callTo(userId, true); this.closeMenuPopup(); }, this)},
				{icon: 'bx-messenger-menu-history', text: BX.message('IM_M_OPEN_HISTORY'), onclick: BX.delegate(function(){ this.openHistory(userId); this.closeMenuPopup();}, this)},
				{icon: 'bx-messenger-menu-profile', text: BX.message('IM_M_OPEN_PROFILE'), href: this.users[userId].profile, onclick: BX.delegate(function(){ this.closeMenuPopup(); }, this)},
				this.chat[chatId].owner == this.BXIM.userId? {icon: 'bx-messenger-menu-chat-exit', text: BX.message('IM_M_CHAT_KICK'), onclick: BX.delegate(function(){ this.kickFromChat(chatId, userId); this.closeMenuPopup();}, this)}: {}
			];
		}
	}
	else if (type == 'contactList')
	{
		var userId = bind.getAttribute('data-userId');
		var userIsChat = bind.getAttribute('data-userIsChat');
		if (this.recentList || userIsChat)
		{
			menuItems = [
				{icon: 'bx-messenger-menu-write', text: BX.message('IM_M_WRITE_MESSAGE'), onclick: BX.delegate(function(){ this.openMessenger(userId); this.closeMenuPopup(); }, this)},
				(userIsChat && ((!this.webrtc.callSupport(userId, this) || this.webrtc.callInit) || this.chat[userId.toString().substr(4)].style == 'call'))? null: {icon: 'bx-messenger-menu-video', text: BX.message('IM_M_CALL_VIDEO'), onclick: BX.delegate(function(){ this.BXIM.callTo(userId, true); this.closeMenuPopup(); }, this)},
				{icon: 'bx-messenger-menu-history', text: BX.message('IM_M_OPEN_HISTORY'), onclick: BX.delegate(function(){ this.openHistory(userId); this.closeMenuPopup();}, this)},
				!userIsChat? {icon: 'bx-messenger-menu-profile', text: BX.message('IM_M_OPEN_PROFILE'), href: this.users[userId].profile, onclick: BX.delegate(function(){ this.closeMenuPopup(); }, this)}: {},
				userIsChat && this.chat[userId.toString().substr(4)].style == 'group' ? {icon: 'bx-messenger-menu-chat-rename', text: BX.message('IM_M_CHAT_RENAME'), onclick: BX.delegate(function(){ this.openMessenger(userId); this.renameChatDialog();  this.closeMenuPopup();}, this)}: {},
				userIsChat && this.chat[userId.toString().substr(4)].style == 'group'? {icon: 'bx-messenger-menu-chat-exit', text: BX.message('IM_M_CHAT_EXIT'), onclick: BX.delegate(function(){ this.leaveFromChat(userId.toString().substr(4)); this.closeMenuPopup();}, this)}: {},
				userIsChat && this.chat[userId.toString().substr(4)].style == 'group'? {}: {icon: 'bx-messenger-menu-hide-'+(userIsChat? 'chat': 'dialog'), text: BX.message('IM_M_HIDE_'+(userIsChat? (this.chat[userId.toString().substr(4)].style == 'group'? 'CHAT': 'CALL'): 'DIALOG')), onclick: BX.delegate(function(){ this.recentListHide(userId); this.closeMenuPopup();}, this)}
			];
		}
		else
		{
			menuItems = [
				{icon: 'bx-messenger-menu-write', text: BX.message('IM_M_WRITE_MESSAGE'), onclick: BX.delegate(function(){ this.openMessenger(userId); this.closeMenuPopup(); }, this)},
				(!userIsChat && (!this.webrtc.callSupport(userId, this) || this.webrtc.callInit))? null: {icon: 'bx-messenger-menu-video', text: BX.message('IM_M_CALL_VIDEO'), onclick: BX.delegate(function(){ this.BXIM.callTo(userId, true); this.closeMenuPopup(); }, this)},
				{icon: 'bx-messenger-menu-history', text: BX.message('IM_M_OPEN_HISTORY'), onclick: BX.delegate(function(){ this.openHistory(userId); this.closeMenuPopup();}, this)},
				{icon: 'bx-messenger-menu-profile', text: BX.message('IM_M_OPEN_PROFILE'), href: this.users[userId].profile, onclick: BX.delegate(function(){ this.closeMenuPopup(); }, this)}
			];
		}
	}
	else if (type == 'dialog')
	{
		var messages = [];
		if (bind.target.className == "bx-messenger-message")
		{
			messages = [bind.target];
		}
		else if (bind.target.className.indexOf("bx-messenger-content-quote") >= 0)
		{
			messages = BX.findParent(bind.target, {className : "bx-messenger-message"});
			messages = [messages];
		}
		else
		{
			messages = BX.findChildren(bind.target, {className : "bx-messenger-message"}, true);
		}
		if (messages.length <= 0)
		{
			messages = BX.findParent(bind.target, {className : "bx-messenger-message"});
			messages = [messages];
		}
		if (messages.length <= 0 || !messages[messages.length-1])
			return false;

		var messageName = BX.message('IM_M_SYSTEM_USER');
		var messageId = messages[messages.length-1].getAttribute('data-textMessageId');
		if (this.message[messageId].senderId && this.users[this.message[messageId].senderId])
			messageName = this.users[this.message[messageId].senderId].name;

		var messageDate = this.message[messageId].date;
		var selectedText = BX.desktop.clipboardSelected();

		var copyLink = false;
		var userName = '';
		if (this.openChatFlag && this.message[messageId].senderId != this.BXIM.userId && this.users[this.message[messageId].senderId])
		{
			userName = this.users[this.message[messageId].senderId].name;
		}

		var copyLinkHref = '';
		if (bind.target.tagName == 'IMG' && bind.target.parentNode.tagName == 'A' || bind.target.tagName == 'A')
		{
			if (bind.target.tagName == 'A')
				copyLinkHref = bind.target.href;
			else
				copyLinkHref = bind.target.parentNode.href;

			if (copyLinkHref.indexOf('/desktop_app/') < 0)
				copyLink = true;
		}

		menuItems = [
			userName.length <= 0? null: {text: BX.message("IM_MENU_ANSWER"), onclick: BX.delegate(function(e){ this.insertTextareaText(' '+BX.util.htmlspecialcharsback(userName)+', ', false);  setTimeout(BX.delegate(function(){ this.popupMessengerTextarea.focus(); }, this), 200);  this.closeMenuPopup(); }, this)},
			userName.length <= 0? null: {separator: true},
			copyLink? {text: BX.message("IM_MENU_COPY3"), onclick: BX.delegate(function()
				{
					BX.desktop.clipboardCopy(BX.delegate(function(){
						return copyLinkHref;
					}, this));
					this.closeMenuPopup();
				}, this)
			}: null,
			copyLink? {separator: true}: null,
			selectedText.length <= 0? null: {text: BX.message("IM_MENU_QUOTE"), onclick: BX.delegate(function(){ var text = BX.desktop.clipboardCopy(); this.insertQuoteText(messageName, messageDate, text); this.closeMenuPopup(); }, this)},
			{text: BX.message("IM_MENU_QUOTE2"), onclick: BX.delegate(function()
				{
					var arQuote = [];
					for (var i = 0; i < messages.length; i++)
					{
						var messageId = messages[i].getAttribute('data-textMessageId');
						if (this.message[messageId])
						{
							arQuote.push(BX.IM.prepareTextBack(this.message[messageId].text));
						}
					}
					this.insertQuoteText(messageName, messageDate, arQuote.join("\n"));

					this.closeMenuPopup();
				}, this)
			},
			{separator: true},
			selectedText.length <= 0? null: {text: BX.message("IM_MENU_COPY"), onclick: BX.delegate(function(){ BX.desktop.clipboardCopy(); this.closeMenuPopup(); }, this)},
			{text: BX.message("IM_MENU_COPY2"), onclick: BX.delegate(function()
				{
					var arQuote = [];
					for (var i = 0; i < messages.length; i++)
					{
						var messageId = messages[i].getAttribute('data-textMessageId');
						if (this.message[messageId])
						{
							arQuote.push(BX.IM.prepareTextBack(this.message[messageId].text));
						}
					}
					BX.desktop.clipboardCopy(BX.delegate(function(value){
						return this.insertQuoteText(messageName, messageDate, arQuote.join("\n"), false);
					}, this));
					this.closeMenuPopup();
				}, this)
			}
		];
	}
	else if (type == 'history')
	{
		var messages = [];
		if (bind.target.className == "bx-messenger-history-item")
		{
			messages = [bind.target];
		}
		else if (bind.target.className.indexOf("bx-messenger-content-quote") >= 0)
		{
			messages = BX.findParent(bind.target, {className : "bx-messenger-history-item"});
			messages = [messages];
		}
		else
		{
			messages = BX.findChildren(bind.target, {className : "bx-messenger-history-item"}, true);
		}
		if (messages.length <= 0)
		{
			messages = BX.findParent(bind.target, {className : "bx-messenger-history-item"});
			messages = [messages];
		}
		if (messages.length <= 0 || !messages[messages.length-1])
			return false;

		var messageName = BX.message('IM_M_SYSTEM_USER');
		var messageId = messages[messages.length-1].getAttribute('data-messageId');
		if (this.message[messageId].senderId && this.users[this.message[messageId].senderId])
			messageName = this.users[this.message[messageId].senderId].name;

		var messageDate = this.message[messageId].date;
		var selectedText = BX.desktop.clipboardSelected();

		var copyLink = false;
		var copyLinkHref = '';
		if (bind.target.tagName == 'IMG' && bind.target.parentNode.tagName == 'A' || bind.target.tagName == 'A')
		{
			if (bind.target.tagName == 'A')
				copyLinkHref = bind.target.href;
			else
				copyLinkHref = bind.target.parentNode.href;

			if (copyLinkHref.indexOf('/desktop_app/') < 0)
				copyLink = true;
		}

		menuItems = [
			copyLink? {text: BX.message("IM_MENU_COPY3"), onclick: BX.delegate(function()
				{
					BX.desktop.clipboardCopy(BX.delegate(function(){
						return copyLinkHref;
					}, this));
					this.closeMenuPopup();
				}, this)
			}: null,
			copyLink? {separator: true}: null,
			selectedText.length <= 0? null: {text: BX.message("IM_MENU_QUOTE"), onclick: BX.delegate(function(){ var text = BX.desktop.clipboardCopy(); this.insertQuoteText(messageName, messageDate, text); this.closeMenuPopup(); }, this)},
			{text: BX.message("IM_MENU_QUOTE2"), onclick: BX.delegate(function()
				{
					var arQuote = [];
					for (var i = 0; i < messages.length; i++)
					{
						var messageId = messages[i].getAttribute('data-messageId');
						if (this.message[messageId])
						{
							arQuote.push(BX.IM.prepareTextBack(this.message[messageId].text));
						}
					}
					this.insertQuoteText(messageName, messageDate, arQuote.join("\n"));

					this.closeMenuPopup();
				}, this)
			},
			{separator: true},
			selectedText.length <= 0? null: {text: BX.message("IM_MENU_COPY"), onclick: BX.delegate(function(){  this.closeMenuPopup(); }, this)},
			{text: BX.message("IM_MENU_COPY2"), onclick: BX.delegate(function()
				{
					var arQuote = [];
					for (var i = 0; i < messages.length; i++)
					{
						var messageId = messages[i].getAttribute('data-messageId');
						if (this.message[messageId])
						{
							arQuote.push(BX.IM.prepareTextBack(this.message[messageId].text));
						}
					}
					BX.desktop.clipboardCopy(BX.delegate(function(value){
						return this.insertQuoteText(messageName, messageDate, arQuote.join("\n"), false);
					}, this));
					this.closeMenuPopup();
				}, this)
			}
		];
	}
	else if (type == 'notify')
	{
		if (bind.target.className == 'bx-notifier-item-delete')
		{
			bind.target.setAttribute('id', 'bx-notifier-item-delete-'+bind.target.getAttribute('data-notifyId'));
			this.openPopupMenu(bind.target, 'notifyDelete');

			return false;
		}

		var selectedText = BX.desktop.clipboardSelected();

		var copyLink = false;
		if (bind.target.tagName == 'A' && bind.target.href.indexOf('/desktop_app/') < 0)
		{
			copyLink = true;
			var copyLinkHref = bind.target.href;
		}

		if (!copyLink && selectedText.length <= 0)
			return false;

		menuItems = [
			copyLink? {text: BX.message("IM_MENU_COPY3"), onclick: BX.delegate(function()
				{
					BX.desktop.clipboardCopy(BX.delegate(function(){
						return copyLinkHref;
					}, this));
					this.closeMenuPopup();
				}, this)
			}: null,
			copyLink? {separator: true}: null,
			selectedText.length <= 0? null: {text: BX.message("IM_MENU_COPY"), onclick: BX.delegate(function(){ BX.desktop.clipboardCopy(); this.closeMenuPopup(); }, this)}
		];

	}
	else if (type == 'copylink')
	{
		if (bind.target.tagName != 'A' || bind.target.href.indexOf('/desktop_app/') >= 0)
			return false;

		menuItems = [
			{text: BX.message("IM_MENU_COPY3"), onclick: BX.delegate(function()
				{
					BX.desktop.clipboardCopy(BX.delegate(function(value){
						return bind.target.href;
					}, this));
					this.closeMenuPopup();
				}, this)
			}
		];
	}
	else if (type == 'copypaste')
	{
		bindOptions = {position: "top"};
		var selectedText = BX.desktop.clipboardSelected(bind.target);
		menuItems = [
			selectedText.length <= 0? null: {text: BX.message("IM_MENU_CUT"), onclick: BX.delegate(function(){ BX.desktop.clipboardCut(); this.closeMenuPopup(); }, this)},
			selectedText.length <= 0? null: {text: BX.message("IM_MENU_COPY"), onclick: BX.delegate(function(){ BX.desktop.clipboardCopy(); this.closeMenuPopup(); }, this)},
			{text: BX.message("IM_MENU_PASTE"), onclick: BX.delegate(function(){ BX.desktop.clipboardPaste(); this.closeMenuPopup(); }, this)},
			selectedText.length <= 0? null: {text: BX.message("IM_MENU_DELETE"), onclick: BX.delegate(function(){ BX.desktop.clipboardDelete(); this.closeMenuPopup(); }, this)}
		];
	}
	else
	{
		menuItems = [];
	}

	this.popupPopupMenuDateCreate = +new Date();
	this.popupPopupMenu = new BX.PopupWindow('bx-messenger-popup-status-menu', bind, {
		lightShadow : true,
		offsetTop: offsetTop,
		offsetLeft: offsetLeft,
		autoHide: true,
		closeByEsc: true,
		zIndex: 200,
		bindOptions: bindOptions,
		events : {
			onPopupClose : BX.delegate(function() {
				if (this.popupPopupMenuDateCreate+1000 < (+new Date()))
					this.destroy()
			}),
			onPopupDestroy : BX.delegate(function() { this.popupPopupMenu = null; }, this)
		},
		content : BX.create("div", { props : { className : "bx-messenger-popup-menu" }, children: [
			BX.create("div", { props : { className : "bx-messenger-popup-menu-items" }, children: BX.Messenger.MenuPrepareList(menuItems)})
		]})
	});
	if (setAngle !== false)
		this.popupPopupMenu.setAngle({offset: 4});
	this.popupPopupMenu.show();

	BX.bind(this.popupPopupMenu.popupContainer, "click", BX.IM.preventDefault);

	if (type == 'dialog' || type == 'notify' || type == 'history' || type == 'copypaste')
	{
		BX.bind(this.popupPopupMenu.popupContainer, "mousedown", function(event){
			event.target.click();
		});
	}

	return false;
};

BX.Messenger.prototype.openPopupExternalData = function(bind, type, setAngle, params)
{
	if (this.popupSmileMenu != null)
		this.popupSmileMenu.destroy();

	if (this.popupPopupMenu != null)
	{
		this.popupPopupMenu.destroy();
		return false;
	}

	this.popupPopupMenuDateCreate = +new Date();
	var offsetTop = this.desktop.ready()? 0: -10;
	var offsetLeft = 10;
	var bindOptions = {position: "top"};
	var sizesOptions = { width: '272px', height: '100px'};
	var ajaxData = { 'IM_GET_EXTERNAL_DATA' : 'Y', 'TYPE': type, 'TS': this.popupPopupMenuDateCreate, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()};

	if (type == 'user')
	{
		sizesOptions = { width: '272px', height: '100px'};
		ajaxData['USER_ID'] = parseInt(params['ID']);
		if (this.users[ajaxData['USER_ID']] && !this.users[ajaxData['USER_ID']].fake)
		{
			ajaxData = false;
		}
	}
	else if (type == 'phoneCallHistory')
	{
		sizesOptions = { width: '239px', height: '122px'};
		ajaxData['HISTORY_ID'] = parseInt(params['ID']);
	}
	else
	{
		return false;
	}


	this.popupPopupMenu = new BX.PopupWindow('bx-messenger-popup-external-data', bind, {
		lightShadow : true,
		offsetTop: offsetTop,
		offsetLeft: offsetLeft,
		autoHide: true,
		closeByEsc: true,
		zIndex: 200,
		bindOptions: bindOptions,
		events : {
			onPopupClose : function() { this.destroy() },
			onPopupDestroy : BX.delegate(function() { this.popupPopupMenu = null; }, this)
		},
		content : BX.create("div", { attrs: {'id': 'bx-messenger-external-data'}, props : { className : "bx-messenger-external-data" },  style: sizesOptions, children: [
			BX.create("div", { props : { className : "bx-messenger-external-data-load" }, html: BX.message('IM_CL_LOAD')})
		]})
	});
	if (setAngle !== false)
		this.popupPopupMenu.setAngle({offset: 4});
	this.popupPopupMenu.show();

	if (ajaxData)
	{
		BX.ajax({
			url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?GET_EXTERNAL_DATA',
			method: 'POST',
			dataType: 'json',
			timeout: 30,
			data: ajaxData,
			onsuccess: BX.delegate(function(data){

				if (data.ERROR)
				{
					data.TYPE = 'noAccess';
				}
				else if (data.TYPE == 'user')
				{
					for (var i in data.USERS)
					{
						this.users[i] = data.USERS[i];
					}
					for (var i in data.PHONES)
					{
						this.phones[i] = {};
						for (var j in data.PHONES[i])
						{
							this.phones[i][j] = BX.util.htmlspecialcharsback(data.PHONES[i][j]);
						}
					}
					for (var i in data.USER_IN_GROUP)
					{
						if (typeof(this.userInGroup[i]) == 'undefined')
						{
							this.userInGroup[i] = data.USER_IN_GROUP[i];
						}
						else
						{
							for (var j = 0; j < data.USER_IN_GROUP[i].users.length; j++)
								this.userInGroup[i].users.push(data.USER_IN_GROUP[i].users[j]);

							this.userInGroup[i].users = BX.util.array_unique(this.userInGroup[i].users)
						}
					}
					for (var i in data.WO_USER_IN_GROUP)
					{
						if (typeof(this.woUserInGroup[i]) == 'undefined')
						{
							this.woUserInGroup[i] = data.WO_USER_IN_GROUP[i];
						}
						else
						{
							for (var j = 0; j < data.WO_USER_IN_GROUP[i].users.length; j++)
								this.woUserInGroup[i].users.push(data.WO_USER_IN_GROUP[i].users[j]);

							this.woUserInGroup[i].users = BX.util.array_unique(this.woUserInGroup[i].users)
						}
					}
				}

				if (data.TS != this.popupPopupMenuDateCreate || !this.popupPopupMenu)
					return false;

				this.drawExternalData(data.TYPE, data);
			}, this),
			onfailure: BX.delegate(function(){
				if (this.popupPopupMenu)
					this.popupPopupMenu.destroy();
			}, this)
		});
	}
	else
	{
		if (type == 'user')
			this.drawExternalData('user', {'USER_ID': params['ID']});
	}

	BX.bind(this.popupPopupMenu.popupContainer, "click", BX.PreventDefault);

	return false;
};

BX.Messenger.prototype.drawExternalData = function(type, params)
{
	if (!BX('bx-messenger-external-data'))
		return false;

	if (type == 'noAccess')
	{
		BX('bx-messenger-external-data').innerHTML = BX.message('IM_M_USER_NO_ACCESS');
	}
	else if (type == 'user')
	{
		if (!this.users[params['USER_ID']])
		{
			if (this.popupPopupMenu)
				this.popupPopupMenu.destroy();

			return false;
		}
		BX('bx-messenger-external-data').innerHTML = '';
		BX.adjust(BX('bx-messenger-external-data'), {children: [
			BX.create('div', { props : { className : "bx-messenger-external-avatar" }, children: [
				BX.create('div', { props : { className : "bx-messenger-panel-avatar bx-messenger-panel-avatar-status-"+(this.users[params['USER_ID']].birthday? 'birthday': this.users[params['USER_ID']].status) }, children: [
					BX.create('img', { attrs : { src : this.users[params['USER_ID']].avatar}, props : { className : "bx-messenger-panel-avatar-img" }}),
					BX.create('span', { props : { className : "bx-messenger-panel-avatar-status" }})
				]}),
				BX.create("span", { props : { className : "bx-messenger-panel-title"}, html: this.users[params['USER_ID']].name}),
				BX.create("span", { props : { className : "bx-messenger-panel-desc"}, html: BX.message("IM_STATUS_"+this.users[params['USER_ID']].status.toUpperCase())})
			]}),
			params['USER_ID'] != this.BXIM.userId? BX.create('div', {props : { className : "bx-messenger-external-data-buttons"}, children: [
				BX.create('span', {
					props : { className : "bx-notifier-item-button bx-notifier-item-button-white" },
					html: '<i class="bx-notifier-item-button-fc"></i><span>'+BX.message('IM_M_WRITE_MESSAGE')+'</span><i></i>',
					events: {click: BX.delegate(function(e){
						this.openMessenger(params['USER_ID']);
					}, this)}
				}),
				BX.create('span', {
					props : { className : "bx-notifier-item-button bx-notifier-item-button-white" },
					html: '<i class="bx-notifier-item-button-fc"></i><span>'+BX.message('IM_M_CALL_BTN_HISTORY')+'</span><i></i>',
					events: {click: BX.delegate(function(){
						this.openHistory(params['USER_ID']);
					}, this)}
				})
			]}): null
		]});
	}
	else if (type == 'phoneCallHistory')
	{
		var recordHtml = false;
		if (params['CALL_RECORD_HTML'])
		{
			var recordHtml = {
				HTML: BX.message('CALL_RECORD_ERROR'),
				SCRIPT: []
			}
			if (!this.desktop.ready() || this.desktop.enableInVersion(23))
				recordHtml = BX.processHTML(params['CALL_RECORD_HTML'], false);
		}

		BX('bx-messenger-external-data').innerHTML = '';
		BX.adjust(BX('bx-messenger-external-data'), {children: [
			BX.create('div', { props : { className : "bx-messenger-record" }, children: [
				BX.create('div', { props : { className : "bx-messenger-record-phone-box" }, children: [
					BX.create('span', { props : { className : "bx-messenger-record-icon bx-messenger-record-icon-"+params['CALL_ICON'] }, attrs: {title: params['INCOMING_TEXT']}}),
					BX.create('span', { props : { className : "bx-messenger-record-phone" }, html: '+'+params['PHONE_NUMBER']})
				]}),
				BX.create("div", { props : { className : "bx-messenger-record-reason"}, html: params['CALL_FAILED_REASON']}),
				BX.create('div', { props : { className : "bx-messenger-record-stats" }, children: [
					BX.create('span', { props : { className : "bx-messenger-record-time" }, html: params['CALL_DURATION_TEXT']}),
					BX.create('span', { props : { className : "bx-messenger-record-cost" }, html: params['COST_TEXT']})
				]}),
				recordHtml? BX.create('div', { props : { className : "bx-messenger-record-box" }, children: [
					BX.create('span', { props : { className : "bx-messenger-record-player" }, html: recordHtml.HTML})
				]}): null
			]})
		]});

		if (recordHtml)
		{
			for (var i = 0; i < recordHtml.SCRIPT.length; i++)
			{
				BX.evalGlobal(recordHtml.SCRIPT[i].JS);
			}
		}
	}
}

/* HISTORY */
BX.Messenger.prototype.openHistory = function(userId)
{
	if (userId == this.BXIM.userId)
		return false;

	if (this.historyWindowBlock)
		return false;

	this.historyEndOfList[userId] = false;
	this.historyLoadFlag[userId] = false;

	if (this.popupHistory != null)
		this.popupHistory.destroy();

	var chatId = 0;
	var isChat = false;
	if (userId.toString().substr(0,4) == 'chat')
	{
		isChat = true;
		chatId = parseInt(userId.toString().substr(4));
		if (chatId <= 0)
			return false;
	}
	else
	{
		userId = parseInt(userId);
		if (userId <= 0)
			return false;
	}

	if (!isChat && !this.users[userId])
	{
		this.users[userId] = {'id': userId, 'avatar': '/bitrix/js/im/images/blank.gif', 'name': BX.message('IM_M_LOAD_USER'), 'profile': this.BXIM.path.profileTemplate.replace('#user_id#', userId), 'status': 'guest', 'fake': true};
		this.hrphoto[userId] = '/bitrix/js/im/images/hidef-avatar.png';
	}
	else if (isChat && !this.chat[chatId])
		this.chat[chatId] = {'id': chatId, 'name': BX.message('IM_M_LOAD_USER'), 'owner': 0, 'fake': true};

	this.historyUserId = userId;

	if (this.popupMessenger != null && !this.desktop.run())
		this.popupMessenger.setClosingByEsc(false);

	this.popupHistoryElements = BX.create("div", { props : { className : "bx-messenger-history" }, children: [
			!isChat?
			BX.create("div", { props : { className : "bx-messenger-panel bx-messenger-panel-bg2" }, children : [
				BX.create('a', { attrs : { href : this.users[userId].profile}, props : { className : "bx-messenger-panel-avatar bx-messenger-panel-avatar-status-"+(this.users[userId].birthday? 'birthday': this.users[userId].status) }, children: [
					BX.create('img', { attrs : { src : this.users[userId].avatar}, props : { className : "bx-messenger-panel-avatar-img" }}),
					BX.create('span', { props : { className : "bx-messenger-panel-avatar-status" }})
				]}),
				this.popupHistoryButtonDeleteAll = BX.create("a", { props : { className : "bx-messenger-panel-basket"}}),
				this.popupHistoryButtonFilter = BX.create("a", { props : { className : "bx-messenger-panel-filter"}, html: (this.popupHistoryFilterVisible? BX.message("IM_HISTORY_FILTER_OFF"):BX.message("IM_HISTORY_FILTER_ON"))}),
				BX.create("span", { props : { className : "bx-messenger-panel-title"}, html: this.users[userId].name}),
				BX.create("span", { props : { className : "bx-messenger-panel-desc"}, html: BX.message("IM_STATUS_"+this.users[userId].status.toUpperCase())})
			]})
			:BX.create("div", { props : { className : "bx-messenger-panel bx-messenger-panel-bg2" }, children : [
				BX.create('span', { props : { className : "bx-messenger-panel-avatar bx-messenger-panel-avatar-"+(this.chat[chatId].style == 'group'? 'chat': 'call') }}),
				this.popupHistoryButtonDeleteAll = BX.create("a", { props : { className : "bx-messenger-panel-basket"}}),
				this.popupHistoryButtonFilter = BX.create("a", { props : { className : "bx-messenger-panel-filter"}, html: (this.popupHistoryFilterVisible? BX.message("IM_HISTORY_FILTER_OFF"):BX.message("IM_HISTORY_FILTER_ON"))}),
				BX.create("span", { props : { className : "bx-messenger-panel-title bx-messenger-panel-title-middle"}, html: this.chat[chatId].name})
			]}),
			this.popupHistoryButtonFilterBox = BX.create("div", { props : { className : "bx-messenger-panel-filter-box" }, style : {display: this.popupHistoryFilterVisible? 'block': 'none'}, children : [
				BX.create('div', {props : { className : "bx-messenger-filter-name" }, html: BX.message('IM_HISTORY_FILTER_NAME')}),
				//BX.create('div', {props : { className : "bx-messenger-filter-date bx-messenger-input-wrap" }, html: '<input type="text" class="bx-messenger-input" value="" placeholder="'+BX.message('IM_PANEL_FILTER_DATE')+'" />'}),
				this.popupHistorySearchWrap = BX.create('div', {props : { className : "bx-messenger-filter-text bx-messenger-history-filter-text bx-messenger-input-wrap" }, html: '<a class="bx-messenger-input-close" href="#close"></a><input type="text" class="bx-messenger-input" placeholder="'+BX.message('IM_PANEL_FILTER_TEXT')+'" value="" />'})
			]}),
			this.popupHistoryItems = BX.create("div", { props : { className : "bx-messenger-history-items" }, style : {height: this.popupHistoryItemsSize+'px'}, children : [
				this.popupHistoryBodyWrap = BX.create("div", { props : { className : "bx-messenger-history-items-wrap" }})
			]})
	]});

	if (this.BXIM.init && this.desktop.ready())
	{
		this.desktop.openHistory(userId, this.popupHistoryElements, "BXIM.openHistory('"+userId+"');");
		return false;
	}
	else if (this.desktop.ready())
	{
		this.popupHistory = new BX.PopupWindowDesktop();
		this.desktop.drawOnPlaceholder(this.popupHistoryElements);
	}
	else
	{
		this.popupHistory = new BX.PopupWindow('bx-messenger-popup-history', null, {
			lightShadow : true,
			offsetTop: 0,
			autoHide: false,
			zIndex: 100,
			draggable: {restrict: true},
			closeByEsc: true,
			bindOptions: {position: "top"},
			events : {
				onPopupClose : function() { this.destroy(); },
				onPopupDestroy : BX.delegate(function() { this.popupHistory = null; this.historySearch = ''; if (this.popupMessenger != null && !this.webrtc.callInit) { this.popupMessenger.setClosingByEsc(true) }}, this)
			},
			titleBar: {content: BX.create('span', {props : { className : "bx-messenger-title" }, html: BX.message('IM_M_HISTORY')})},
			closeIcon : {'top': '10px', 'right': '13px'},
			content : this.popupHistoryElements
		});
		this.popupHistory.show();
		BX.bind(this.popupHistory.popupContainer, "click", BX.IM.preventDefault);
	}
	this.drawHistory(this.historyUserId);

	this.popupHistorySearchInput = BX.findChild(this.popupHistorySearchWrap, {className : "bx-messenger-input"}, true);
	this.popupHistorySearchInputClose = BX.findChild(this.popupHistorySearchInput.parentNode, {className : "bx-messenger-input-close"}, true);

	if (this.popupHistoryFilterVisible && !BX.browser.IsAndroid() && !BX.browser.IsIOS())
		BX.focus(this.popupHistorySearchInput);

	BX.bind(this.popupHistorySearchInputClose, "click",  BX.delegate(function(e){
		this.popupHistorySearchInput.value = '';
		this.historySearch = "";
		this.drawHistory(this.historyUserId);
		return BX.PreventDefault(e);
	}, this));
	if (this.desktop.ready())
	{
		BX.bind(this.popupHistorySearchInput, "contextmenu", BX.delegate(function(e) {
			this.openPopupMenu(e, 'copypaste', false);
			return BX.PreventDefault(e);
		}, this));

		BX.bindDelegate(this.popupHistoryElements, "contextmenu", {className: 'bx-messenger-history-item'}, BX.delegate(function(e) {
			this.openPopupMenu(e, 'history', false);
			return BX.PreventDefault(e);
		}, this));
	}

	BX.bindDelegate(this.popupHistoryElements, 'click', {className: 'bx-messenger-ajax'}, BX.delegate(function() {
		if (BX.proxy_context.getAttribute('data-entity') == 'user')
		{
			this.openPopupExternalData(BX.proxy_context, 'user', true, {'ID': BX.proxy_context.getAttribute('data-userId')})
		}
		else if (this.webrtc.phoneSupport() && BX.proxy_context.getAttribute('data-entity') == 'phoneCallHistory')
		{
			this.openPopupExternalData(BX.proxy_context, 'phoneCallHistory', true, {'ID': BX.proxy_context.getAttribute('data-historyID')})
		}
	}, this));

	BX.bind(this.popupHistorySearchInput, "keyup", BX.delegate(this.newHistorySearch, this));

	BX.bind(this.popupHistoryButtonFilter, "click",  BX.delegate(function(){
		if (this.popupHistoryFilterVisible)
		{
			this.popupHistoryButtonFilter.innerHTML = BX.message("IM_HISTORY_FILTER_ON");
			this.popupHistoryItemsSize = this.popupHistoryItemsSize+this.popupHistoryButtonFilterBox.offsetHeight;
			this.popupHistoryItems.style.height = this.popupHistoryItemsSize+'px';
			BX.style(this.popupHistoryButtonFilterBox, 'display', 'none');
			this.popupHistoryFilterVisible = false;
			this.popupHistorySearchInput.value = '';
			this.historySearch = "";
			this.drawHistory(this.historyUserId);
		}
		else
		{
			this.popupHistoryButtonFilter.innerHTML = BX.message("IM_HISTORY_FILTER_OFF");
			BX.style(this.popupHistoryButtonFilterBox, 'display', 'block');
			this.popupHistoryItemsSize = this.popupHistoryItemsSize-this.popupHistoryButtonFilterBox.offsetHeight;
			this.popupHistoryItems.style.height = this.popupHistoryItemsSize+'px';
			BX.focus(this.popupHistorySearchInput);
			this.popupHistoryFilterVisible = true;
		}
	}, this));

	BX.bind(this.popupHistoryButtonDeleteAll, "click",  BX.delegate(function(){
		this.BXIM.openConfirm(BX.message('IM_M_HISTORY_DELETE_ALL_CONFIRM'), [
			new BX.PopupWindowButton({
				text : BX.message('IM_M_HISTORY_DELETE_ALL'),
				className : "popup-window-button-accept",
				events : { click : BX.delegate(function() { this.deleteAllHistory(userId); BX.proxy_context.popupWindow.close(); }, this) }
			}),
			new BX.PopupWindowButton({
				text : BX.message('IM_NOTIFY_CONFIRM_CLOSE'),
				className : "popup-window-button-decline",
				events : { click : function() { this.popupWindow.close(); } }
			})
		], true);
	}, this));
	BX.bind(this.popupHistoryItems, "scroll", BX.delegate(function(){ this.loadHistory(userId) }, this));
};


BX.Messenger.prototype.loadHistory = function(userId)
{
	if (this.historyLoadFlag[userId])
		return;

	if (this.historySearch != "")
		return;

	if (!(this.popupHistoryItems.scrollTop > this.popupHistoryItems.scrollHeight - this.popupHistoryItems.offsetHeight-50))
		return;

	if (!this.historyEndOfList[userId])
	{
		this.historyLoadFlag[userId] = true;

		if (this.history[userId])
			this.historyOpenPage[userId] = Math.floor(this.history[userId].length/20)+1;
		else
			this.historyOpenPage[userId] = 1;

		var tmpLoadMoreWait = null;
		this.popupHistoryBodyWrap.appendChild(tmpLoadMoreWait = BX.create("div", { props : { className : "bx-messenger-content-load-more-history" }, children : [
			BX.create('span', { props : { className : "bx-messenger-content-load-img" }}),
			BX.create("span", { props : { className : "bx-messenger-content-load-text" }, html : BX.message('IM_M_LOAD_MESSAGE')})
		]}));

		BX.ajax({
			url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?HISTORY_LOAD_MORE',
			method: 'POST',
			dataType: 'json',
			timeout: 30,
			data: {'IM_HISTORY_LOAD_MORE' : 'Y', 'USER_ID' : userId, 'PAGE_ID' : this.historyOpenPage[userId], 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
			onsuccess: BX.delegate(function(data){
				BX.remove(tmpLoadMoreWait);
				this.historyLoadFlag[userId] = false;
				if (data.MESSAGE.length == 0)
				{
					this.historyEndOfList[userId] = true;
					return;
				}

				for (var i in data.MESSAGE)
				{
					data.MESSAGE[i].date = parseInt(data.MESSAGE[i].date)+parseInt(BX.message('USER_TZ_OFFSET'));
					if (this.message[i])
					{
						this.message[i].moreHistoryDraw = false;
					}
					else
					{
						data.MESSAGE[i].moreHistoryDraw = true;
						this.message[i] = data.MESSAGE[i];
					}
				}
				for (var i in data.USERS_MESSAGE)
				{
					if (this.history[i])
						this.history[i] = BX.util.array_merge(this.history[i], data.USERS_MESSAGE[i]);
					else
						this.history[i] = data.USERS_MESSAGE[i];
				}
				for (var i = 0; i < data.USERS_MESSAGE[userId].length; i++)
				{
					var history = this.message[data.USERS_MESSAGE[userId][i]];
					if (history && history.moreHistoryDraw)
					{
						this.popupHistoryBodyWrap.appendChild(
							BX.create("div", { attrs : { 'data-messageId' : history.id}, props : { className : "bx-messenger-history-item"+(history.senderId == 0? " bx-messenger-history-item-3": (history.senderId == this.BXIM.userId?"": " bx-messenger-history-item-2")) }, children : [
								BX.create("div", { props : { className : "bx-messenger-history-item-name" }, html : (this.users[history.senderId]? this.users[history.senderId].name: BX.message('IM_M_SYSTEM_USER'))+' <span class="bx-messenger-history-hide">[</span><span class="bx-messenger-history-item-date">'+BX.IM.formatDate(history.date)+'</span><span class="bx-messenger-history-hide">]</span>'/*<span class="bx-messenger-history-item-delete-icon" title="'+BX.message('IM_M_HISTORY_DELETE')+'" data-messageId="'+history.id+'"></span>*/}),
								//BX.create("div", { props : { className : "bx-messenger-history-item-nearby" }, html : BX.message('IM_HISTORY_NEARBY')}),
								BX.create("div", { props : { className : "bx-messenger-history-item-text" }, html : BX.IM.prepareText(history.text, false, true, true)}),
								BX.create("div", { props : { className : "bx-messenger-history-hide" }, html : '<br>'})
							]})
						);
					}
				}
			}, this),
			onfailure: function(){
				BX.remove(tmpLoadMoreWait);
			}
		});
	}
};

BX.Messenger.prototype.deleteAllHistory = function(userId)
{
	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?HISTORY_REMOVE_ALL',
		method: 'POST',
		dataType: 'json',
		timeout: 30,
		data: {'IM_HISTORY_REMOVE_ALL' : 'Y', 'USER_ID' : userId, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()}
	});
	BX.localStorage.set('mhra', userId, 5);

	this.history[userId] = [];
	this.showMessage[userId] = [];
	this.popupHistoryBodyWrap.innerHTML = '';
	this.popupHistoryBodyWrap.appendChild(BX.create("div", { props : { className : "bx-messenger-content-history-empty" }, children : [
		BX.create("span", { props : { className : "bx-messenger-content-load-text" }, html : BX.message('IM_M_NO_MESSAGE')})
	]}));

	if (this.desktop.ready())
		BX.desktop.onCustomEvent("main", "bxImClearHistory", [userId]);
	else if (this.BXIM.init)
		this.drawTab(userId);
};

BX.Messenger.prototype.drawHistory = function(userId, boxOfHistory, loadFromServer)
{
	if (this.popupHistory == null)
		return false;

	loadFromServer = typeof(loadFromServer) == 'undefined'? true: loadFromServer;

	var userIsChat = false;
	if (userId.toString().substr(0,4) == 'chat')
	{
		userIsChat = true;
		var chatId = userId.toString().substr(4);
	}

	var activeSearch = this.historySearch.length > 0;
	var boxOfHistory = !boxOfHistory? this.history: boxOfHistory;
	if (boxOfHistory[userId] && (!userIsChat && this.users[userId] || userIsChat && this.chat[chatId]))
	{
		var arHistory = [];
		var arHistorySort = BX.util.array_unique(boxOfHistory[userId]);
		arHistorySort.sort(BX.delegate(function(i, ii) {i = parseInt(i); ii = parseInt(ii); if (!this.message[i] || !this.message[ii]){return 0;} var i1 = parseInt(this.message[i].date); var i2 = parseInt(this.message[ii].date); if (i1 > i2) { return -1; } else if (i1 < i2) { return 1;} else{ if (i > ii) { return -1; } else if (i < ii) { return 1;}else{ return 0;}}}, this));
		for (var i = 0; i < arHistorySort.length; i++)
		{
			var history = this.message[boxOfHistory[userId][i]];

			if (history)
			{
				if (activeSearch && history.text.toLowerCase().indexOf((this.historySearch+'').toLowerCase()) < 0)
					continue;

				arHistory.push(
					BX.create("div", { attrs : { 'data-messageId' : history.id}, props : { className : "bx-messenger-history-item"+(history.senderId == 0? " bx-messenger-history-item-3": (history.senderId == this.BXIM.userId?"": " bx-messenger-history-item-2")) }, children : [
						BX.create("div", { props : { className : "bx-messenger-history-item-name" }, html : (this.users[history.senderId]? this.users[history.senderId].name: BX.message('IM_M_SYSTEM_USER'))+' <span class="bx-messenger-history-hide">[</span><span class="bx-messenger-history-item-date">'+BX.IM.formatDate(history.date)+'</span><span class="bx-messenger-history-hide">]</span>'/*<span class="bx-messenger-history-item-delete-icon" title="'+BX.message('IM_M_HISTORY_DELETE')+'" data-messageId="'+history.id+'"></span>*/}),
						//BX.create("div", { props : { className : "bx-messenger-history-item-nearby" }, html : BX.message('IM_HISTORY_NEARBY')}),
						BX.create("div", { props : { className : "bx-messenger-history-item-text" }, html : BX.IM.prepareText(history.text, false, true, true)}),
						BX.create("div", { props : { className : "bx-messenger-history-hide" }, html : '<br>'})
					]})
				);
			}
		}

		if (arHistory.length <= 0)
		{
			if (this.historySearchBegin)
			{
				arHistory = [
					BX.create("div", { props : { className : "bx-messenger-content-load-history" }, children : [
						BX.create('span', { props : { className : "bx-messenger-content-load-img" }}),
						BX.create("span", { props : { className : "bx-messenger-content-load-text" }, html : BX.message('IM_M_LOAD_MESSAGE')})
					]})
				];
			}
			else
			{
				arHistory = [
					BX.create("div", { props : { className : "bx-messenger-content-history-empty" }, children : [
						BX.create("span", { props : { className : "bx-messenger-content-load-text" }, html : BX.message('IM_M_NO_MESSAGE')})
					]})
				];
			}
		}
	}
	else if (this.showMessage[userId] && this.showMessage[userId].length <= 0)
	{
		arHistory = [
			BX.create("div", { props : { className : "bx-messenger-content-history-empty" }, children : [
				BX.create("span", { props : { className : "bx-messenger-content-load-text" }, html : BX.message('IM_M_NO_MESSAGE')})
			]})
		];
	}

	if (loadFromServer && (!this.showMessage[userId] || this.showMessage[userId] && this.showMessage[userId].length < 20))
	{
		arHistory = [
			BX.create("div", { props : { className : "bx-messenger-content-load-history" }, children : [
				BX.create('span', { props : { className : "bx-messenger-content-load-img" }}),
				BX.create("span", { props : { className : "bx-messenger-content-load-text" }, html : BX.message('IM_M_LOAD_MESSAGE')})
			]})
		];
		BX.ajax({
			url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?HISTORY_LOAD',
			method: 'POST',
			dataType: 'json',
			timeout: 30,
			data: {'IM_HISTORY_LOAD' : 'Y', 'USER_ID' : userId, 'USER_LOAD' : userIsChat? (this.chat[userId.toString().substr(4)] && this.chat[userId.toString().substr(4)].fake? 'Y': 'N'): (this.users[userId] && this.users[userId].fake? 'Y': 'N'), 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
			onsuccess: BX.delegate(function(data)
			{
				if (data.ERROR == '')
				{
					this.showMessage[userId] = [];
					this.sendAjaxTry = 0;
					for (var i in data.MESSAGE)
					{
						data.MESSAGE[i].date = parseInt(data.MESSAGE[i].date)+parseInt(BX.message('USER_TZ_OFFSET'));
						this.message[i] = data.MESSAGE[i];
						if (this.BXIM.settings.loadLastMessage)
							this.showMessage[userId].push(i);
					}
					for (var i in data.USERS_MESSAGE)
					{
						if (this.history[i])
							this.history[i] = BX.util.array_merge(this.history[i], data.USERS_MESSAGE[i]);
						else
							this.history[i] = data.USERS_MESSAGE[i];
					}
					if ((!userIsChat && this.users[userId] && !this.users[userId].fake) ||
						(userIsChat && this.chat[userId.toString().substr(4)] && !this.chat[userId.toString().substr(4)].fake))
					{
						BX.cleanNode(this.popupHistoryBodyWrap);
						if (!data.USERS_MESSAGE[userId] || data.USERS_MESSAGE[userId].length <= 0)
						{
							this.popupHistoryBodyWrap.appendChild(
								BX.create("div", { props : { className : "bx-messenger-content-history-empty" }, children : [
									BX.create("span", { props : { className : "bx-messenger-content-load-text" }, html : BX.message('IM_M_NO_MESSAGE')})
								]})
							);
						}
						else
						{
							for (var i = 0; i < data.USERS_MESSAGE[userId].length; i++)
							{
								var history = this.message[data.USERS_MESSAGE[userId][i]];
								if (history)
								{
									this.popupHistoryBodyWrap.appendChild(
										BX.create("div", { attrs : { 'data-messageId' : history.id}, props : { className : "bx-messenger-history-item"+(history.senderId == 0? " bx-messenger-history-item-3": (history.senderId == this.BXIM.userId?"": " bx-messenger-history-item-2")) }, children : [
											BX.create("div", { props : { className : "bx-messenger-history-item-name" }, html : (this.users[history.senderId]? this.users[history.senderId].name: BX.message('IM_M_SYSTEM_USER'))+' <span class="bx-messenger-history-hide">[</span><span class="bx-messenger-history-item-date">'+BX.IM.formatDate(history.date)+'</span><span class="bx-messenger-history-hide">]</span>'/*<span class="bx-messenger-history-item-delete-icon" title="'+BX.message('IM_M_HISTORY_DELETE')+'" data-id="'+history.id+'"></span>*/}),
											//BX.create("div", { props : { className : "bx-messenger-history-item-nearby" }, html : BX.message('IM_HISTORY_NEARBY')}),
											BX.create("div", { props : { className : "bx-messenger-history-item-text" }, html : BX.IM.prepareText(history.text, false, true, true)}),
											BX.create("div", { props : { className : "bx-messenger-history-hide" }, html : '<br>'})
										]})
									);
								}
							}
						}
						if (this.BXIM.settings.loadLastMessage && this.currentTab == userId)
							this.drawTab(this.currentTab, true);
					}
					else
					{

						if (userIsChat && this.chat[data.USER_ID.substr(4)].fake)
							this.chat[data.USER_ID.toString().substr(4)].name = BX.message('IM_M_USER_NO_ACCESS');

						if (!userIsChat)
						{
							this.users[userId] = {'id': userId, 'avatar': '/bitrix/js/im/images/blank.gif', 'name': BX.message('IM_M_USER_NO_ACCESS'), 'profile': '#', 'status': 'guest'};
							this.hrphoto[userId] = '/bitrix/js/im/images/hidef-avatar.png';
						}

						for (var i in data.USERS)
						{
							this.users[i] = data.USERS[i];
						}
						for (var i in data.USER_IN_GROUP)
						{
							if (typeof(this.userInGroup[i]) == 'undefined')
							{
								this.userInGroup[i] = data.USER_IN_GROUP[i];
							}
							else
							{
								for (var j = 0; j < data.USER_IN_GROUP[i].users.length; j++)
									this.userInGroup[i].users.push(data.USER_IN_GROUP[i].users[j]);

								this.userInGroup[i].users = BX.util.array_unique(this.userInGroup[i].users)
							}

						}
						for (var i in data.WO_USER_IN_GROUP)
						{
							if (typeof(this.woUserInGroup[i]) == 'undefined')
							{
								this.woUserInGroup[i] = data.WO_USER_IN_GROUP[i];
							}
							else
							{
								for (var j = 0; j < data.WO_USER_IN_GROUP[i].users.length; j++)
									this.woUserInGroup[i].users.push(data.WO_USER_IN_GROUP[i].users[j]);

								this.woUserInGroup[i].users = BX.util.array_unique(this.woUserInGroup[i].users)
							}
						}
						for (var i in data.CHAT)
						{
							this.chat[i] = data.CHAT[i];
						}
						for (var i in data.USER_IN_CHAT)
						{
							this.userInChat[i] = data.USER_IN_CHAT[i];
						}
						if (!userIsChat)
							this.userListRedraw();
						this.dialogStatusRedraw();

						this.openHistory(userId);
					}
				}
				else
				{
					if (data.ERROR == 'SESSION_ERROR' && this.sendAjaxTry < 2)
					{
						this.sendAjaxTry++;
						BX.message({'bitrix_sessid': data.BITRIX_SESSID});
						setTimeout(BX.delegate(function(){this.drawHistory(userId, boxOfHistory, loadFromServer)}, this), 1000);
						BX.onCustomEvent(window, 'onImError', [data.ERROR, data.BITRIX_SESSID]);
					}
					else if (data.ERROR == 'AUTHORIZE_ERROR' && this.sendAjaxTry < 1)
					{
						this.sendAjaxTry++;
						setTimeout(BX.delegate(function(){this.drawHistory(userId, boxOfHistory, loadFromServer)}, this), 10000);
						BX.onCustomEvent(window, 'onImError', [data.ERROR]);
					}
				}
			}, this),
			onfailure: BX.delegate(function(){
				this.sendAjaxTry = 0;
			}, this)
		});
	}

	this.popupHistoryBodyWrap.innerHTML = '';
	BX.adjust(this.popupHistoryBodyWrap, {children: arHistory});
	this.popupHistoryItems.scrollTop = 0;
};

BX.Messenger.prototype.newHistorySearch = function(event)
{
	event = event||window.event;
	if (event.keyCode == 27 && this.historySearch != '')
		BX.IM.preventDefault(event);

	if (event.keyCode == 27)
		this.popupHistorySearchInput.value = '';


	this.historySearch = this.popupHistorySearchInput.value;
	if (this.popupHistorySearchInput.value.length <= 3)
	{
		this.historySearch = "";
		this.drawHistory(this.historyUserId, false, false);
		return false;
	}

	this.historySearchBegin = true;
	this.historySearch = this.popupHistorySearchInput.value;
	this.drawHistory(this.historyUserId, false, false);

	clearTimeout(this.historySearchTimeout);
	if (this.popupHistorySearchInput.value != '')
	{
		this.historySearchTimeout = setTimeout(BX.delegate(function(){
			BX.ajax({
				url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?HISTORY_SEARCH',
				method: 'POST',
				dataType: 'json',
				timeout: 30,
				data: {'IM_HISTORY_SEARCH' : 'Y', 'USER_ID' : this.historyUserId, 'SEARCH' : this.popupHistorySearchInput.value, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
				onsuccess: BX.delegate(function(data){
					this.historySearchBegin = false;
					if (data.MESSAGE.length == 0)
					{
						this.drawHistory(data.USER_ID);
						return;
					}

					for (var i in data.MESSAGE)
					{
						data.MESSAGE[i].date = parseInt(data.MESSAGE[i].date)+parseInt(BX.message('USER_TZ_OFFSET'));
						data.MESSAGE[i].moreHistoryDraw = false;
						this.message[i] = data.MESSAGE[i];
					}

					this.drawHistory(data.USER_ID, data.USERS_MESSAGE, false);
				}, this),
				onfailure: function(data)	{}
			});
		}, this), 1500);
	}

	return BX.PreventDefault(event);
};

/* GET DATA */
BX.Messenger.prototype.setUpdateStateStep = function(send)
{
	send = send != false;

	var step = this.updateStateStepDefault;
	if (!this.BXIM.ppStatus)
	{
		if (this.popupMessenger != null)
		{
			step = 20;
			if (this.updateStateVeryFastCount > 0)
			{
				step = 5;
				this.updateStateVeryFastCount--;
			}
			else if (this.updateStateFastCount > 0)
			{
				step = 10;
				this.updateStateFastCount--;
			}
		}
	}

	this.updateStateStep = parseInt(step);

	if (send)
		BX.localStorage.set('uss', this.updateStateStep, 5);

	this.updateState();
};

BX.Messenger.prototype.updateState = function(force, send)
{
	if (!this.BXIM.tryConnect)
		return false;

	force = force == true;
	send = send != false;
	clearTimeout(this.updateStateTimeout);
	this.updateStateTimeout = setTimeout(
		BX.delegate(function(){
			var _ajax = BX.ajax({
				url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?UPDATE_STATE',
				method: 'POST',
				dataType: 'json',
				lsId: 'IM_UPDATE_STATE',
				lsTimeout: 1,
				timeout: 30,
				data: {'IM_UPDATE_STATE' : 'Y', 'OPEN_MESSENGER' : this.popupMessenger != null? 1: 0, 'TAB' : this.currentTab, 'FM' : JSON.stringify(this.flashMessage), 'FN' :  JSON.stringify(this.notify.flashNotify), 'SITE_ID': BX.message('SITE_ID'),'IM_AJAX_CALL' : 'Y', 'DESKTOP' : (this.desktop.ready()? 'Y': 'N'), 'sessid': BX.bitrix_sessid()},
				onsuccess: BX.delegate(function(data)
				{
					if (send)
						BX.localStorage.set('mus', true, 5);

					if (data && data.ERROR == '')
					{
						if (!this.BXIM.checkRevision(data.REVISION))
							return false;

						BX.message({'SERVER_TIME': data.SERVER_TIME});
						this.notify.updateNotifyCounters(data.COUNTERS, send);
						this.notify.updateNotifyMailCount(data.MAIL_COUNTER, send);

						if (!this.BXIM.xmppStatus && data.XMPP_STATUS && data.XMPP_STATUS == 'Y')
							this.BXIM.xmppStatus = true;

						if (!this.BXIM.desktopStatus && data.DESKTOP_STATUS && data.DESKTOP_STATUS == 'Y')
							this.BXIM.desktopStatus = true;

						var contactListRedraw = false;
						if (!(data.ONLINE.length <= 0))
						{
							var userChangeStatus = {};
							for (var i in this.users)
							{
								if (typeof(data.ONLINE[i]) == 'undefined')
								{
									if (this.users[i].status != 'offline')
									{
										userChangeStatus[i] = this.users[i].status;
										this.users[i].status = 'offline';
										contactListRedraw = true;
									}
								}
								else
								{
									if (this.users[i].status != data.ONLINE[i].status)
									{
										userChangeStatus[i] = this.users[i].status;
										this.users[i].status = data.ONLINE[i].status;
										contactListRedraw = true;
									}
								}
							}
						}
						if (typeof(data.MESSAGE) != "undefined")
							for (var i in data.MESSAGE)
								data.MESSAGE[i].date = parseInt(data.MESSAGE[i].date)+parseInt(BX.message('USER_TZ_OFFSET'));

						this.updateStateVar(data, send);
						if (typeof(data.USERS_MESSAGE) != "undefined")
							contactListRedraw = true;

						if (contactListRedraw)
						{
							this.dialogStatusRedraw();
							this.userListRedraw();
						}

						if (typeof(data.NOTIFY) != "undefined")
						{
							for (var i in data.NOTIFY)
							{
								data.NOTIFY[i].date = parseInt(data.NOTIFY[i].date)+parseInt(BX.message('USER_TZ_OFFSET'));
								this.notify.notify[i] = data.NOTIFY[i];
								this.BXIM.lastRecordId = parseInt(i) > this.BXIM.lastRecordId? parseInt(i): this.BXIM.lastRecordId;
							}

							for (var i in data.FLASH_NOTIFY)
								if (typeof(this.notify.flashNotify[i]) == 'undefined')
									this.notify.flashNotify[i] = data.FLASH_NOTIFY[i];

							this.notify.changeUnreadNotify(data.UNREAD_NOTIFY, send);
						}


						if (BX.PULL && data.PULL_CONFIG)
						{
							BX.PULL.updateChannelID({
								'METHOD': data.PULL_CONFIG.METHOD,
								'CHANNEL_ID': data.PULL_CONFIG.CHANNEL_ID,
								'CHANNEL_DT': data.PULL_CONFIG.CHANNEL_DT,
								'PATH': data.PULL_CONFIG.PATH,
								'LAST_ID': data.PULL_CONFIG.LAST_ID,
								'PATH_WS': data.PULL_CONFIG.PATH_WS
							});
							BX.PULL.tryConnect();
						}

						this.setUpdateStateStep(false);
					}
					else
					{
						if (data.ERROR == 'SESSION_ERROR' && this.sendAjaxTry < 2)
						{
							this.sendAjaxTry++;
							BX.message({'bitrix_sessid': data.BITRIX_SESSID});
							setTimeout(BX.delegate(function(){
								this.updateState(true, send);
							}, this), 2000);
							BX.onCustomEvent(window, 'onImError', [data.ERROR, data.BITRIX_SESSID]);
						}
						else if (data.ERROR == 'AUTHORIZE_ERROR' && this.sendAjaxTry < 1)
						{
							this.sendAjaxTry++;
							setTimeout(BX.delegate(function(){
								this.updateState(true, send);
							}, this), 10000);
							BX.onCustomEvent(window, 'onImError', [data.ERROR]);
						}
						else if (this.sendAjaxTry < 5)
						{
							this.sendAjaxTry++;
							if (this.sendAjaxTry >= 2 && !this.BXIM.desktop.ready())
							{
								BX.onCustomEvent(window, 'onImError', [data.ERROR]);
								return false;
							}

							setTimeout(BX.delegate(function(){
								this.updateState(true, send);
							}, this), 60000);
							BX.onCustomEvent(window, 'onImError', [data.ERROR]);
						}
					}
				}, this),
				onfailure: BX.delegate(function() {
					this.sendAjaxTry = 0;
					this.setUpdateStateStep(false);
					try {
						if (typeof(_ajax) == 'object' && _ajax.status == 0)
							BX.onCustomEvent(window, 'onImError', ['CONNECT_ERROR']);
					}
					catch(e) {}
				}, this)
			});
		}, this)
	, force? 150: this.updateStateStep*1000);
};

BX.Messenger.prototype.updateStateLight = function(force, send)
{
	if (!this.BXIM.tryConnect)
		return false;

	force = force == true;
	send = send != false;
	clearTimeout(this.updateStateTimeout);
	this.updateStateTimeout = setTimeout(
		BX.delegate(function(){
			BX.ajax({
				url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?UPDATE_STATE_LIGHT',
				method: 'POST',
				dataType: 'json',
				lsId: 'IM_UPDATE_STATE_LIGHT',
				lsTimeout: 1,
				timeout: this.updateStateStepDefault > 10? this.updateStateStepDefault-2: 10,
				data: {'IM_UPDATE_STATE_LIGHT' : 'Y', 'SITE_ID': BX.message('SITE_ID'), 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
				onsuccess: BX.delegate(function(data)
				{
					if (send)
						BX.localStorage.set('musl', true, 5);

					if (data && data.ERROR == '')
					{
						if (!this.BXIM.checkRevision(data.REVISION))
							return false;

						BX.message({'SERVER_TIME': data.SERVER_TIME});

						this.notify.updateNotifyCounters(data.COUNTERS, send);

						if (BX.PULL && data.PULL_CONFIG)
						{
							BX.PULL.updateChannelID({
								'METHOD': data.PULL_CONFIG.METHOD,
								'CHANNEL_ID': data.PULL_CONFIG.CHANNEL_ID,
								'CHANNEL_DT': data.PULL_CONFIG.CHANNEL_DT,
								'PATH': data.PULL_CONFIG.PATH,
								'LAST_ID': data.PULL_CONFIG.LAST_ID,
								'PATH_WS': data.PULL_CONFIG.PATH_WS
							});
							BX.PULL.tryConnect();
						}

						this.updateStateLight(force, send);
					}
					else
					{
						if (data.ERROR == 'SESSION_ERROR' && this.sendAjaxTry < 2)
						{
							this.sendAjaxTry++;
							BX.message({'bitrix_sessid': data.BITRIX_SESSID});
							setTimeout(BX.delegate(function(){
								this.updateStateLight(true, send);
							}, this), 2000);
							BX.onCustomEvent(window, 'onImError', [data.ERROR, data.BITRIX_SESSID]);
						}
						else if (data.ERROR == 'AUTHORIZE_ERROR' && this.sendAjaxTry < 1)
						{
							this.sendAjaxTry++;
							setTimeout(BX.delegate(function(){
								this.updateStateLight(true, send);
							}, this), 10000);
							BX.onCustomEvent(window, 'onImError', [data.ERROR]);
						}
						else if (this.sendAjaxTry < 5)
						{
							this.sendAjaxTry++;
							if (this.sendAjaxTry >= 2 && !this.BXIM.desktop.ready())
							{
								BX.onCustomEvent(window, 'onImError', [data.ERROR]);
								return false;
							}

							setTimeout(BX.delegate(function(){
								this.updateStateLight(true, send);
							}, this), 60000);
							BX.onCustomEvent(window, 'onImError', [data.ERROR]);
						}
					}
				}, this),
				onfailure: BX.delegate(function() {
					this.sendAjaxTry = 0;
					this.setUpdateStateStep(false);
					try {
						if (typeof(_ajax) == 'object' && _ajax.status == 0)
							BX.onCustomEvent(window, 'onImError', ['CONNECT_ERROR']);
					}
					catch(e) {}
				}, this)
			});
		}, this)
	, force? 150: this.updateStateStepDefault*1000);
};

BX.Messenger.prototype.updateStateVar = function(data, send, writeMessage)
{
	writeMessage = writeMessage !== false;
	if (typeof(data.CHAT) != "undefined")
	{
		for (var i in data.CHAT)
		{
			if (this.chat[i] && this.chat[i].fake)
				data.CHAT[i].fake = true;
			else if (!this.chat[i])
				data.CHAT[i].fake = true;

			this.chat[i] = data.CHAT[i];
		}
	}
	if (typeof(data.USER_IN_CHAT) != "undefined")
	{
		for (var i in data.USER_IN_CHAT)
		{
			this.userInChat[i] = data.USER_IN_CHAT[i];
		}
	}
	if (typeof(data.USERS) != "undefined")
	{
		for (var i in data.USERS)
		{
			this.users[i] = data.USERS[i];
		}
	}
	if (typeof(data.USER_IN_GROUP) != "undefined")
	{
		for (var i in data.USER_IN_GROUP)
		{
			if (typeof(this.userInGroup[i]) == 'undefined')
			{
				this.userInGroup[i] = data.USER_IN_GROUP[i];
			}
			else
			{
				for (var j = 0; j < data.USER_IN_GROUP[i].users.length; j++)
					this.userInGroup[i].users.push(data.USER_IN_GROUP[i].users[j]);

				this.userInGroup[i].users = BX.util.array_unique(this.userInGroup[i].users)
			}
		}
	}
	if (typeof(data.WO_USER_IN_GROUP) != "undefined")
	{
		for (var i in data.WO_USER_IN_GROUP)
		{
			if (typeof(this.woUserInGroup[i]) == 'undefined')
			{
				this.woUserInGroup[i] = data.WO_USER_IN_GROUP[i];
			}
			else
			{
				for (var j = 0; j < data.WO_USER_IN_GROUP[i].users.length; j++)
					this.woUserInGroup[i].users.push(data.WO_USER_IN_GROUP[i].users[j]);

				this.woUserInGroup[i].users = BX.util.array_unique(this.woUserInGroup[i].users)
			}
		}
	}
	if (typeof(data.MESSAGE) != "undefined")
	{
		for (var i in data.MESSAGE)
		{
			this.message[i] = data.MESSAGE[i];
			this.BXIM.lastRecordId = parseInt(i) > this.BXIM.lastRecordId? parseInt(i): this.BXIM.lastRecordId;
		}
	}
	this.changeUnreadMessage(data.UNREAD_MESSAGE, send);
	if (typeof(data.USERS_MESSAGE) != "undefined")
	{
		for (var i in data.USERS_MESSAGE)
		{
			data.USERS_MESSAGE[i].sort(BX.delegate(function(i, ii) {i = parseInt(i); ii = parseInt(ii); if (!this.message[i] || !this.message[ii]){return 0;} var i1 = parseInt(this.message[i].date); var i2 = parseInt(this.message[ii].date); if (i1 < i2) { return -1; } else if (i1 > i2) { return 1;} else{ if (i < ii) { return -1; } else if (i > ii) { return 1;}else{ return 0;}}}, this));
			if (!this.showMessage[i])
				this.showMessage[i] = data.USERS_MESSAGE[i];

			for (var j = 0; j < data.USERS_MESSAGE[i].length; j++)
			{
				if (!BX.util.in_array(data.USERS_MESSAGE[i][j], this.showMessage[i]))
				{
					this.showMessage[i].push(data.USERS_MESSAGE[i][j]);
					if (this.history[i])
						this.history[i] = BX.util.array_merge(this.history[i], data.USERS_MESSAGE[i]);
					else
						this.history[i] = data.USERS_MESSAGE[i];

					if (writeMessage && this.currentTab == i)
						this.drawMessage(i, this.message[data.USERS_MESSAGE[i][j]]);
				}
			}
		}
	}
};

BX.Messenger.prototype.changeUnreadMessage = function(unreadMessage, send)
{
	send = send != false;

	var playSound = false;
	var contactListRedraw = false;
	for (var i in unreadMessage)
	{
		var skipPopup = false;
		if (this.BXIM.xmppStatus && i.toString().substr(0,4) != 'chat')
		{
			if (!(this.popupMessenger != null && this.currentTab == i && this.BXIM.isFocus()))
			{
				contactListRedraw = true;
				if (this.unreadMessage[i])
					this.unreadMessage[i] = BX.util.array_unique(BX.util.array_merge(this.unreadMessage[i], unreadMessage[i]));
				else
					this.unreadMessage[i] = unreadMessage[i];
			}
			skipPopup = true;
		}
		if (!skipPopup)
		{
			if (this.popupMessenger != null && this.currentTab == i && this.BXIM.isFocus())
			{
				if (typeof (this.flashMessage[i]) == 'undefined')
					this.flashMessage[i] = {};

				for (var k = 0; k < unreadMessage[i].length; k++)
				{
					if (this.BXIM.isFocus())
						this.flashMessage[i][unreadMessage[i][k]] = false;

					if (this.message[unreadMessage[i][k]] && this.message[unreadMessage[i][k]].senderId == this.currentTab)
						playSound = true;
				}
				this.readMessage(i, true, true, true);
			}
			else
			{
				contactListRedraw = true;
				if (this.unreadMessage[i])
					this.unreadMessage[i] = BX.util.array_unique(BX.util.array_merge(this.unreadMessage[i], unreadMessage[i]));
				else
					this.unreadMessage[i] = unreadMessage[i];

				if (typeof (this.flashMessage[i]) == 'undefined')
				{
					this.flashMessage[i] = {};
					for (var k = 0; k < unreadMessage[i].length; k++)
					{
						var resultOfNameSearch = this.message[unreadMessage[i][k]].text.match(new RegExp("("+this.users[this.BXIM.userId].name.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&")+")",'ig'));
						if (this.BXIM.settings.status != 'dnd' || resultOfNameSearch)
						{
							this.flashMessage[i][unreadMessage[i][k]] = send;
						}
					}
				}
				else
				{
					for (var k = 0; k < unreadMessage[i].length; k++)
					{
						var resultOfNameSearch = this.message[unreadMessage[i][k]].text.match(new RegExp("("+this.users[this.BXIM.userId].name.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&")+")",'ig'));
						if (this.BXIM.settings.status != 'dnd' || resultOfNameSearch)
						{
							if (!send && !this.BXIM.isFocus())
							{
								this.flashMessage[i][unreadMessage[i][k]] = false;
							}
							else
							{
								if (typeof (this.flashMessage[i][unreadMessage[i][k]]) == 'undefined')
									this.flashMessage[i][unreadMessage[i][k]] = true;
							}
						}
					}
				}

			}
		}
		var arRecent = false;
		for (var k = 0; k < unreadMessage[i].length; k++)
		{
			if (!arRecent || arRecent.SEND_DATE <= this.message[unreadMessage[i][k]].date+parseInt(BX.message("SERVER_TZ_OFFSET")))
			{
				arRecent = {
					'ID': this.message[unreadMessage[i][k]].id,
					'SEND_DATE': this.message[unreadMessage[i][k]].date+parseInt(BX.message("SERVER_TZ_OFFSET")),
					'RECIPIENT_ID': this.message[unreadMessage[i][k]].recipientId,
					'SENDER_ID': this.message[unreadMessage[i][k]].senderId,
					'USER_ID': this.message[unreadMessage[i][k]].senderId,
					'SEND_MESSAGE': this.message[unreadMessage[i][k]].text
				};
			}
		}
		if (arRecent)
		{
			this.recentListAdd({
				'userId': arRecent.RECIPIENT_ID.toString().substr(0,4) == 'chat'? arRecent.RECIPIENT_ID: arRecent.USER_ID,
				'id': arRecent.ID,
				'date': arRecent.SEND_DATE,
				'recipientId': arRecent.RECIPIENT_ID,
				'senderId': arRecent.SENDER_ID,
				'text': arRecent.SEND_MESSAGE
			}, true);
		}
		if (this.popupMessenger != null && this.currentTab == i)
			this.dialogStatusRedraw();
	}
	if (this.popupMessenger != null && !this.recentList && contactListRedraw)
		this.userListRedraw();

	this.newMessage(send);

	this.updateMessageCount(send);

	if (send && playSound && this.BXIM.settings.status != 'dnd')
	{
		this.BXIM.playSound("newMessage2");
	}
};

BX.Messenger.prototype.readMessage = function(userId, send, sendAjax, skipCheck)
{
	skipCheck = skipCheck == true;
	if (!skipCheck && (!this.unreadMessage[userId] || this.unreadMessage[userId].length <= 0))
		return false;

	send = send != false;
	sendAjax = !(this.readMessageTimeoutSend == null && sendAjax == false);
	if (sendAjax)
		this.readMessageTimeoutSend = true;

	clearTimeout(this.readMessageTimeout);
	this.readMessageTimeout = setTimeout(BX.delegate(function(){
		if (this.popupMessenger != null)
		{
			var elements = BX.findChildren(this.popupContactListElementsWrap, {attribute: {'data-userId': ''+userId+''}}, true);
			if (elements != null)
				for (var i = 0; i < elements.length; i++)
					elements[i].firstChild.innerHTML = '';

			elements = BX.findChildren(this.popupMessengerBodyWrap, {className : "bx-messenger-content-item-new"}, false);
			if (elements != null)
				for (var i = 0; i < elements.length; i++)
					if (elements[i].getAttribute('data-notifyType') != 1)
						BX.removeClass(elements[i], 'bx-messenger-content-item-new');
		}
		var lastId = 0;
		if (Math && this.unreadMessage[userId])
			lastId = Math.max.apply(Math, this.unreadMessage[userId]);

		if (this.unreadMessage[userId])
			delete this.unreadMessage[userId];

		if (this.flashMessage[userId])
			delete this.flashMessage[userId];

		BX.localStorage.set('mfm', this.flashMessage, 80);

		this.updateMessageCount(send);

		if (sendAjax)
		{
			this.readMessageTimeoutSend = null;
			var sendData = {'IM_READ_MESSAGE' : 'Y', 'USER_ID' : userId, 'TAB' : this.currentTab, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()};
			if (parseInt(lastId) > 0)
				sendData['LAST_ID'] = lastId;
			var _ajax = BX.ajax({
				url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?READ_MESSAGE',
				method: 'POST',
				dataType: 'json',
				timeout: 60,
				data: sendData,
				onsuccess: BX.delegate(function(data)
				{
					if (data.ERROR != '')
					{
						if (data.ERROR == 'SESSION_ERROR' && this.sendAjaxTry < 2)
						{
							this.sendAjaxTry++;
							BX.message({'bitrix_sessid': data.BITRIX_SESSID});
							setTimeout(BX.delegate(function(){
								this.readMessage(userId, false, true);
							}, this), 2000);
							BX.onCustomEvent(window, 'onImError', [data.ERROR, data.BITRIX_SESSID]);
						}
						else if (data.ERROR == 'AUTHORIZE_ERROR' && this.sendAjaxTry < 1)
						{
							this.sendAjaxTry++;
							setTimeout(BX.delegate(function(){
								this.readMessage(userId, false, true);
							}, this), 10000);
							BX.onCustomEvent(window, 'onImError', [data.ERROR]);
						}
					}
				}, this),
				onfailure: BX.delegate(function()	{
					this.sendAjaxTry = 0;
					try {
						if (typeof(_ajax) == 'object' && _ajax.status == 0)
							BX.onCustomEvent(window, 'onImError', ['CONNECT_ERROR']);
					}
					catch(e) {}
				}, this)
			});
		}
		if (send)
		{
			BX.localStorage.set('mrm', userId, 5);
		}
	}, this), 500);
	if (send)
	{
		BX.localStorage.set('mnnb', true, 1);
	}
};

BX.Messenger.prototype.drawReadMessage = function(userId, messageId, date, animation)
{
	var lastId = Math.max.apply(Math, this.showMessage[userId]);
	if (lastId != messageId || this.message[lastId].senderId == userId)
	{
		this.readedList[userId] = false;
		return false;
	}

	this.readedList[userId] = {
		'messageId' : messageId,
		'date' : date
	};
	if (!this.countWriting(userId))
	{
		animation = animation != false;

		this.drawNotifyMessage(userId, 'readed', BX.message('IM_M_READED').replace('#DATE#', BX.IM.formatDate(date)), animation);
	}
};

BX.Messenger.prototype.loadLastMessage = function(userId, userIsChat)
{
	this.historyWindowBlock = true;
	delete this.redrawTab[userId];
	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?LOAD_LAST_MESSAGE',
		method: 'POST',
		dataType: 'json',
		timeout: 90,
		data: {'IM_LOAD_LAST_MESSAGE' : 'Y', 'CHAT' : userIsChat? 'Y': 'N', 'USER_ID' : userId, 'USER_LOAD' : userIsChat? (this.chat[userId.toString().substr(4)] && this.chat[userId.toString().substr(4)].fake? 'Y': 'N'): 'Y', 'TAB' : this.currentTab, 'READ' : this.BXIM.isFocus()? 'Y': 'N', 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
		onsuccess: BX.delegate(function(data)
		{
			if (data.ERROR == '')
			{
				if (!userIsChat && data.USER_LOAD == 'Y')
				{
					this.users[userId] = {'id': userId, 'avatar': '/bitrix/js/im/images/blank.gif', 'name': BX.message('IM_M_USER_NO_ACCESS'), 'profile': '#', 'status': 'guest'};
					this.hrphoto[userId] = '/bitrix/js/im/images/hidef-avatar.png';
				}

				for (var i in data.USERS)
				{
					this.users[i] = data.USERS[i];
				}
				for (var i in data.PHONES)
				{
					this.phones[i] = {};
					for (var j in data.PHONES[i])
					{
						this.phones[i][j] = BX.util.htmlspecialcharsback(data.PHONES[i][j]);
					}
				}
				for (var i in data.USER_IN_GROUP)
				{
					if (typeof(this.userInGroup[i]) == 'undefined')
					{
						this.userInGroup[i] = data.USER_IN_GROUP[i];
					}
					else
					{
						for (var j = 0; j < data.USER_IN_GROUP[i].users.length; j++)
							this.userInGroup[i].users.push(data.USER_IN_GROUP[i].users[j]);

						this.userInGroup[i].users = BX.util.array_unique(this.userInGroup[i].users)
					}
				}
				for (var i in data.WO_USER_IN_GROUP)
				{
					if (typeof(this.woUserInGroup[i]) == 'undefined')
					{
						this.woUserInGroup[i] = data.WO_USER_IN_GROUP[i];
					}
					else
					{
						for (var j = 0; j < data.WO_USER_IN_GROUP[i].users.length; j++)
							this.woUserInGroup[i].users.push(data.WO_USER_IN_GROUP[i].users[j]);

						this.woUserInGroup[i].users = BX.util.array_unique(this.woUserInGroup[i].users)
					}
				}

				for (var i in data.READED_LIST)
				{
					data.READED_LIST[i].date = parseInt(data.READED_LIST[i].date)+parseInt(BX.message('USER_TZ_OFFSET'));
					this.readedList[i] = data.READED_LIST[i];
				}

				if (!userIsChat && data.USER_LOAD == 'Y')
					this.userListRedraw();

				this.sendAjaxTry = 0;
				var messageCnt = 0;
				for (var i in data.MESSAGE)
				{
					messageCnt++;
					data.MESSAGE[i].date = parseInt(data.MESSAGE[i].date)+parseInt(BX.message('USER_TZ_OFFSET'));
					this.message[i] = data.MESSAGE[i];
					this.BXIM.lastRecordId = parseInt(i) > this.BXIM.lastRecordId? parseInt(i): this.BXIM.lastRecordId;
				}

				if (messageCnt <= 0)
					delete this.redrawTab[data.USER_ID];

				for (var i in data.USERS_MESSAGE)
				{
					if (this.showMessage[i])
						this.showMessage[i] = BX.util.array_unique(BX.util.array_merge(data.USERS_MESSAGE[i], this.showMessage[i]));
					else
						this.showMessage[i] = data.USERS_MESSAGE[i];
				}
				if (userIsChat && this.chat[data.USER_ID.substr(4)].fake)
					this.chat[data.USER_ID.toString().substr(4)].name = BX.message('IM_M_USER_NO_ACCESS');

				for (var i in data.CHAT)
				{
					this.chat[i] = data.CHAT[i];
				}
				for (var i in data.USER_IN_CHAT)
				{
					this.userInChat[i] = data.USER_IN_CHAT[i];
				}
				this.drawTab(data.USER_ID, this.currentTab == data.USER_ID);

				if (this.currentTab == data.USER_ID && this.readedList[data.USER_ID])
					this.drawReadMessage(data.USER_ID, this.readedList[data.USER_ID].messageId, this.readedList[data.USER_ID].date, false);

				this.historyWindowBlock = false;
				if (this.BXIM.isFocus())
					this.readMessage(data.USER_ID, true, false);
			}
			else
			{
				this.redrawTab[userId] = true;
				if (data.ERROR == 'ACCESS_DENIED')
				{
					this.currentTab = 0;
					this.extraClose();
				}
				else if (data.ERROR == 'SESSION_ERROR' && this.sendAjaxTry < 2)
				{
					this.sendAjaxTry++;
					BX.message({'bitrix_sessid': data.BITRIX_SESSID});
					setTimeout(BX.delegate(function(){this.loadLastMessage(userId, userIsChat)}, this), 2000);
					BX.onCustomEvent(window, 'onImError', [data.ERROR, data.BITRIX_SESSID]);
				}
				else if (data.ERROR == 'AUTHORIZE_ERROR' && this.sendAjaxTry < 1)
				{
					this.sendAjaxTry++;
					setTimeout(BX.delegate(function(){this.loadLastMessage(userId, userIsChat)}, this), 10000);
					BX.onCustomEvent(window, 'onImError', [data.ERROR]);
				}
			}
		}, this),
		onfailure: BX.delegate(function(){
			this.historyWindowBlock = false;
			this.sendAjaxTry = 0;
			this.redrawTab[userId] = true;
		}, this)
	});
};

BX.Messenger.prototype.loadUserData = function(userId)
{
	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?USER_DATA_LOAD',
		method: 'POST',
		dataType: 'json',
		timeout: 30,
		data: {'IM_USER_DATA_LOAD' : 'Y', 'USER_ID' : userId, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
		onsuccess: BX.delegate(function(data)
		{
			if (data.ERROR == '')
			{
				this.users[userId] = {'id': userId, 'avatar': '/bitrix/js/im/images/blank.gif', 'name': BX.message('IM_M_USER_NO_ACCESS'), 'profile': '#', 'status': 'guest'};
				this.hrphoto[userId] = '/bitrix/js/im/images/hidef-avatar.png';

				for (var i in data.USERS)
				{
					this.users[i] = data.USERS[i];
				}
				for (var i in data.PHONES)
				{
					this.phones[i] = {};
					for (var j in data.PHONES[i])
					{
						this.phones[i][j] = BX.util.htmlspecialcharsback(data.PHONES[i][j]);
					}
				}
				for (var i in data.USER_IN_GROUP)
				{
					if (typeof(this.userInGroup[i]) == 'undefined')
					{
						this.userInGroup[i] = data.USER_IN_GROUP[i];
					}
					else
					{
						for (var j = 0; j < data.USER_IN_GROUP[i].users.length; j++)
							this.userInGroup[i].users.push(data.USER_IN_GROUP[i].users[j]);

						this.userInGroup[i].users = BX.util.array_unique(this.userInGroup[i].users)
					}
				}
				for (var i in data.WO_USER_IN_GROUP)
				{
					if (typeof(this.woUserInGroup[i]) == 'undefined')
					{
						this.woUserInGroup[i] = data.WO_USER_IN_GROUP[i];
					}
					else
					{
						for (var j = 0; j < data.WO_USER_IN_GROUP[i].users.length; j++)
							this.woUserInGroup[i].users.push(data.WO_USER_IN_GROUP[i].users[j]);

						this.woUserInGroup[i].users = BX.util.array_unique(this.woUserInGroup[i].users)
					}
				}

				this.dialogStatusRedraw();
			}
			else
			{
				this.redrawTab[userId] = true;
				if (data.ERROR == 'ACCESS_DENIED')
				{
					this.currentTab = 0;
					this.extraClose();
				}
			}
		}, this),
		onfailure: BX.delegate(function(){
		}, this)
	});
};

/* EXTRA */
BX.Messenger.prototype.extraOpen = function(content)
{
	if (this.popupMessenger != null)
		this.popupMessenger.setClosingByEsc(false);

	if (!this.BXIM.extraBind)
	{
		BX.bind(window, "keydown", this.BXIM.extraBind = BX.proxy(function(e) {
			if (e.keyCode == 27 && !this.webrtc.callInit)
			{
				if (this.popupMessenger && !this.desktop.ready())
					this.popupMessenger.destroy();
			}
		}, this));
	}

	this.BXIM.extraOpen = true;
	this.BXIM.dialogOpen = false;

	BX.style(this.popupMessengerDialog, 'display', 'none');
	BX.style(this.popupMessengerExtra, 'display', 'block');

	this.popupMessengerExtra.innerHTML = '';
	BX.adjust(this.popupMessengerExtra, {children: [content]});

	this.resizeMainWindow();
};

BX.Messenger.prototype.extraClose = function(openDialog, callToggle)
{
	setTimeout(BX.delegate(function(){
		if (this.popupMessenger != null && !this.webrtc.callInit)
		{
			this.popupMessenger.setClosingByEsc(true);
		}
	}, this), 200);

	if (this.BXIM.extraBind)
	{
		BX.unbind(window, "keydown", this.BXIM.extraBind);
		this.BXIM.extraBind = null;
	}

	this.BXIM.extraOpen = false;
	this.BXIM.dialogOpen = true;

	openDialog = openDialog == true;
	callToggle = callToggle != false;

	if (this.BXIM.notifyOpen)
		this.notify.closeNotify();

	this.closeMenuPopup();

	if (this.currentTab == 0)
	{
		this.extraOpen(
			BX.create("div", { attrs : { style : "padding-top: 300px"}, props : { className : "bx-messenger-box-empty" }, html: BX.message('IM_M_EMPTY')})
		);
	}
	else
	{
		BX.style(this.popupMessengerDialog, 'display', 'block');
		BX.style(this.popupMessengerExtra, 'display', 'none');
		this.popupMessengerExtra.innerHTML = '';

		if (openDialog)
		{
			this.openChatFlag = this.currentTab.toString().substr(0,4) == 'chat';
			this.openDialog(this.currentTab, false, callToggle);
		}
	}
	this.resizeMainWindow();
};

/* WRITING */
BX.Messenger.prototype.startWriting = function(userId, dialogId)
{
	if (dialogId == this.BXIM.userId)
	{
		this.writingList[userId] = true;
		this.drawWriting(userId);

		clearTimeout(this.writingListTimeout[userId]);
		this.writingListTimeout[userId] = setTimeout(BX.delegate(function(){
			this.endWriting(userId);
		}, this), 29500);
	}
	else
	{
		if (!this.writingList[dialogId])
			this.writingList[dialogId] = {};

		if (!this.writingListTimeout[dialogId])
			this.writingListTimeout[dialogId] = {};

		this.writingList[dialogId][userId] = true;
		this.drawWriting(userId, dialogId);

		clearTimeout(this.writingListTimeout[dialogId][userId]);
		this.writingListTimeout[dialogId][userId] = setTimeout(BX.delegate(function(){
			this.endWriting(userId, dialogId);
		}, this), 29500);
	}
};

BX.Messenger.prototype.drawWriting = function(userId, dialogId)
{
	if (userId == this.BXIM.userId)
		return false;

	if (this.writingList[userId] || dialogId && this.countWriting(dialogId) > 0)
	{
		if (this.popupMessenger != null)
		{
			var elements = BX.findChildren(this.popupContactListElementsWrap, {attribute: {'data-userId': ''+(dialogId? dialogId: userId)+''}}, true);
			if (elements)
			{
				for (var i = 0; i < elements.length; i++)
					BX.addClass(elements[i], 'bx-messenger-cl-status-writing');
			}
			if (this.currentTab == userId || dialogId && this.currentTab == dialogId)
			{
				if (dialogId)
				{
					var userList = [];
					for(var i in this.writingList[dialogId])
					{
						if(this.writingList[dialogId].hasOwnProperty(i))
						{
							userList.push(this.users[i].name);
						}
					}
					this.drawNotifyMessage(dialogId, 'writing', BX.message('IM_M_WRITING').replace('#USER_NAME#', userList.join(', ')));
				}
				else
				{
					this.popupMessengerPanelAvatar.parentNode.className = 'bx-messenger-panel-avatar bx-messenger-panel-avatar-status-writing';
					this.drawNotifyMessage(userId, 'writing', BX.message('IM_M_WRITING').replace('#USER_NAME#', this.users[userId].name));
				}
			}
		}
	}
	else if (!this.writingList[userId] || dialogId && this.countWriting(dialogId) == 0)
	{
		if (this.popupMessenger != null)
		{
			var elements = BX.findChildren(this.popupContactListElementsWrap, {attribute: {'data-userId': ''+(dialogId? dialogId: userId)+''}}, true);
			if (elements)
			{
				for (var i = 0; i < elements.length; i++)
					BX.removeClass(elements[i], 'bx-messenger-cl-status-writing');
			}
			if (this.currentTab == userId || this.currentTab == dialogId)
			{
				if (!dialogId)
					this.popupMessengerPanelAvatar.parentNode.className = 'bx-messenger-panel-avatar bx-messenger-panel-avatar-status-'+(!dialogId && this.users[userId].birthday? 'birthday': this.users[userId].status);

				var lastMessage = this.popupMessengerBodyWrap.lastChild;
				if (lastMessage && BX.hasClass(lastMessage, "bx-messenger-content-item-notify"))
				{
					if (!dialogId && this.readedList[userId])
					{
						this.drawReadMessage(userId, this.readedList[userId].messageId, this.readedList[userId].date, false);
					}
					else if (this.BXIM.enableScroll(this.popupMessengerBody, this.popupMessengerBody.offsetHeight))
					{
						if (this.BXIM.animationSupport)
						{
							if (this.popupMessengerBodyAnimation != null)
								this.popupMessengerBodyAnimation.stop();
							(this.popupMessengerBodyAnimation = new BX.easing({
								duration : 800,
								start : { scroll : this.popupMessengerBody.scrollTop},
								finish : { scroll : this.popupMessengerBody.scrollTop - lastMessage.offsetHeight},
								transition : BX.easing.makeEaseInOut(BX.easing.transitions.quart),
								step : BX.delegate(function(state){
									this.popupMessengerBody.scrollTop = state.scroll;
								}, this),
								complete : BX.delegate(function(){
									BX.remove(lastMessage);
								}, this)
							})).animate();
						}
						else
						{
							this.popupMessengerBody.scrollTop = this.popupMessengerBody.scrollTop - lastMessage.offsetHeight;
							BX.remove(lastMessage);
						}
					}
					else
					{
						this.popupMessengerBody.scrollTop = this.popupMessengerBody.scrollTop - lastMessage.offsetHeight;
						BX.remove(lastMessage);
					}
				}
			}
		}
	}
};

BX.Messenger.prototype.endWriting = function(userId, dialogId)
{
	if (dialogId)
	{
		if (this.writingListTimeout[dialogId] && this.writingListTimeout[dialogId][userId])
			clearTimeout(this.writingListTimeout[dialogId][userId]);

		if (this.writingList[dialogId] && this.writingList[dialogId][userId])
			delete this.writingList[dialogId][userId];
	}
	else
	{
		clearTimeout(this.writingListTimeout[userId]);
		delete this.writingList[userId];
	}
	this.drawWriting(userId, dialogId);
};

BX.Messenger.prototype.countWriting = function(dialogId)
{
	var count = 0;
	if (this.writingList[dialogId])
	{
		if (typeof(this.writingList[dialogId]) == 'object')
		{
			for(var i in this.writingList[dialogId])
			{
				if(this.writingList[dialogId].hasOwnProperty(i))
				{
					count++;
				}
			}
		}
		else
		{
			count = 1;
		}
	}

	return count;
}

BX.Messenger.prototype.sendWriting = function(dialogId)
{
	if (!this.BXIM.ppServerStatus)
		return false;

	if (!this.writingSendList[dialogId])
	{
		clearTimeout(this.writingSendListTimeout[dialogId]);
		this.writingSendList[dialogId] = true;
		BX.ajax({
			url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?START_WRITING',
			method: 'POST',
			dataType: 'json',
			timeout: 30,
			data: {'IM_START_WRITING' : 'Y', 'DIALOG_ID' : dialogId, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()}
		});
		this.writingSendListTimeout[dialogId] = setTimeout(BX.delegate(function(){
			this.endSendWriting(dialogId);
		}, this), 30000);
	}
};

BX.Messenger.prototype.endSendWriting = function(dialogId)
{
	clearTimeout(this.writingSendListTimeout[dialogId]);
	this.writingSendList[dialogId] = false;
};


/* TEXTAREA */

BX.Messenger.prototype.sendMessage = function(recipientId)
{
	recipientId = typeof(recipientId) == 'string' || typeof(recipientId) == 'number' ? recipientId: this.currentTab;
	this.endSendWriting(recipientId);

	this.popupMessengerTextarea.value = this.popupMessengerTextarea.value.replace('    ', "\t");
	this.popupMessengerTextarea.value = BX.util.trim(this.popupMessengerTextarea.value);
	if (this.popupMessengerTextarea.value.length == 0)
		return false;

	if (this.popupMessengerTextarea.value == '/clear')
	{
		this.popupMessengerTextarea.value = '';
		this.textareaHistory[this.currentTab] = '';
		this.showMessage[this.currentTab] = [];
		this.drawTab(this.currentTab, true);

		if (this.desktop.ready())
			console.log('NOTICE: User use /clear');

		return false;
	}
	else if (this.popupMessengerTextarea.value == '/webrtcDebug' || this.popupMessengerTextarea.value == '/webrtcDebug on' || this.popupMessengerTextarea.value == '/webrtcDebug off')
	{
		if (this.popupMessengerTextarea.value == '/webrtcDebug')
			this.webrtc.debug = this.webrtc.debug? false: true;
		else if (this.popupMessengerTextarea.value == '/webrtcDebug on')
			this.webrtc.debug = true;
		else if (this.popupMessengerTextarea.value == '/webrtcDebug off')
			this.webrtc.debug = false;

		this.textareaHistory[this.currentTab] = '';
		this.popupMessengerTextarea.value = '';

		if (console && console.log)
			console.log('NOTICE: User use /webrtcDebug and TURN '+(this.webrtc.debug? 'ON': 'OFF')+' debug');

		return false;
	}
	else if (this.popupMessengerTextarea.value == '/windowReload')
	{
		this.textareaHistory[this.currentTab] = '';
		this.popupMessengerTextarea.value = '';
		location.reload();

		if (this.desktop.ready())
			console.log('NOTICE: User use /windowReload');

		return false;
	}
	if (this.desktop.ready())
	{
		if (this.popupMessengerTextarea.value == '/openDeveloperTools')
		{
			this.textareaHistory[this.currentTab] = '';
			this.popupMessengerTextarea.value = '';
			BX.desktop.openDeveloperTools();

			console.log('NOTICE: User use /openDeveloperTools');
			return false;
		}
		else if (this.popupMessengerTextarea.value == '/clearWindowSize')
		{
			this.BXIM.setLocalConfig('msz3', false);
			BX.desktop.apiReady = false;
			location.reload();

			if (this.desktop.ready())
				console.log('NOTICE: User use /clearWindowSize');

			return false;
		}

	}
	if (this.popupMessengerTextarea.value == '/showOnlyChat')
	{
		this.recentListRedraw({'showOnlyChat': true});
		this.textareaHistory[this.currentTab] = '';
		this.popupMessengerTextarea.value = '';

		return false;
	}

	var messageTmpIndex = this.messageTmpIndex;
	this.message['temp'+messageTmpIndex] = {'id' : 'temp'+messageTmpIndex, 'senderId' : this.BXIM.userId, 'recipientId' : recipientId, 'date' : BX.IM.getNowDate(), 'text' : BX.IM.prepareText(this.popupMessengerTextarea.value, true) };
	if (!this.showMessage[recipientId])
		this.showMessage[recipientId] = [];
	this.showMessage[recipientId].push('temp'+messageTmpIndex);

	this.messageTmpIndex++;
	BX.localStorage.set('mti', this.messageTmpIndex, 5);
	if (this.popupMessengerTextarea == null || recipientId != this.currentTab)
		return false;

	clearTimeout(this.textareaHistoryTimeout);
	if (!BX.browser.IsAndroid() && !BX.browser.IsIOS())
		BX.focus(this.popupMessengerTextarea);

	var elLoad = BX.findChild(this.popupMessengerBodyWrap, {className : "bx-messenger-content-load"}, true);
	if (elLoad)
		BX.remove(elLoad);

	var elEmpty = BX.findChild(this.popupMessengerBodyWrap, {className : "bx-messenger-content-empty"}, true);
	if (elEmpty)
		BX.remove(elEmpty);

	this.drawMessage(recipientId, this.message['temp'+messageTmpIndex]);

	var messageText = this.popupMessengerTextarea.value;
	this.popupMessengerLastMessage = messageText;

	this.sendMessageAjax(messageTmpIndex, recipientId, messageText, recipientId.toString().substr(0,4) == 'chat');

	if (this.BXIM.settings.status != 'dnd')
	{
		this.BXIM.playSound("send");
	}

	this.textareaHistory[this.currentTab] = '';
	this.popupMessengerTextarea.value = '';
	setTimeout(BX.delegate(function(){
		this.popupMessengerTextarea.value = '';
	}, this), 0);

	return true;
};

BX.Messenger.prototype.sendMessageAjax = function(messageTmpIndex, recipientId, messageText, sendMessageToChat)
{
	if (this.sendMessageFlag < 0)
		this.sendMessageFlag = 0;

	clearTimeout(this.sendMessageTmpTimeout['temp'+messageTmpIndex]);
	if (this.sendMessageTmp[messageTmpIndex])
		return false;

	this.sendMessageTmp[messageTmpIndex] = true;
	sendMessageToChat = sendMessageToChat == true;
	this.sendMessageFlag++;

	this.recentListAdd({
		'id': 0,
		'date': BX.IM.getNowDate()+parseInt(BX.message("SERVER_TZ_OFFSET")),
		'skipDateCheck': true,
		'recipientId': recipientId,
		'senderId': this.BXIM.userId,
		'text': BX.IM.prepareText(messageText, true),
		'userId': recipientId
	}, true);

	var _ajax = BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/im.ajax.php?SEND_MESSAGE',
		method: 'POST',
		dataType: 'json',
		timeout: 60,
		data: {'IM_SEND_MESSAGE' : 'Y', 'CHAT': sendMessageToChat? 'Y': 'N', 'ID' : 'temp'+messageTmpIndex, 'RECIPIENT_ID' : recipientId, 'MESSAGE' : messageText, 'TAB' : this.currentTab, 'USER_TZ_OFFSET': BX.message('USER_TZ_OFFSET'), 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
		onsuccess: BX.delegate(function(data)
		{
			this.sendMessageFlag--;
			if (data.ERROR == '')
			{
				this.sendAjaxTry = 0;
				this.message[data.TMP_ID].text = data.SEND_MESSAGE;
				this.message[data.TMP_ID].date = data.SEND_DATE;
				this.message[data.TMP_ID].id = data.ID;
				this.message[data.ID] = this.message[data.TMP_ID];
				delete this.message[data.TMP_ID];
				var message = this.message[data.ID];

				var idx = BX.util.array_search(''+data.TMP_ID+'', this.showMessage[data.RECIPIENT_ID]);
				this.showMessage[data.RECIPIENT_ID][idx] = ''+data.ID+'';

				if (data.RECIPIENT_ID == this.currentTab)
				{
					var element = BX.findChild(this.popupMessengerBodyWrap, {attribute: {'data-messageid': ''+data.TMP_ID+''}}, true);
					if (!element)
						return false;

					element.setAttribute('data-messageid',	''+data.ID+'');

					var textElement = BX.findChild(element, {attribute: {'data-textMessageId': ''+data.TMP_ID+''}}, true);
					textElement.setAttribute('data-textMessageId',	''+data.ID+'');
					textElement.innerHTML =  BX.IM.prepareText(data.SEND_MESSAGE, false, true, true);

					var messageUser = this.users[message.senderId];
					var lastMessageElementDate = BX.findChild(element, {className : "bx-messenger-content-item-date"}, true);
					if (lastMessageElementDate)
						lastMessageElementDate.innerHTML = ' &nbsp; '+(sendMessageToChat? messageUser.name: '')+' &nbsp; '+BX.IM.formatDate(message.date);

					var lastMessageElementStatus = BX.findChild(element, {className : "bx-messenger-content-item-status"}, true);
					if (lastMessageElementStatus)
					{
						lastMessageElementStatus.innerHTML = '';
						BX.adjust(lastMessageElementStatus, {children: [
							BX.create("span", { attrs: {title : BX.message('IM_M_QUOTE_TITLE')}, props : { className : "bx-messenger-content-item-quote"}})
						]});
					}
				}

				if (this.history[data.RECIPIENT_ID])
					this.history[data.RECIPIENT_ID].push(message.id);
				else
					this.history[data.RECIPIENT_ID] = [message.id];

				this.updateStateVeryFastCount = 2;
				this.updateStateFastCount = 5;
				this.setUpdateStateStep();

				if (BX.PULL)
				{
					BX.PULL.setUpdateStateStepCount(2,5);
				}
				this.updateStateVar(data, true, true);
				BX.localStorage.set('msm', {'id': data.ID, 'recipientId': data.RECIPIENT_ID, 'date': data.SEND_DATE, 'text' : data.SEND_MESSAGE, 'senderId' : this.BXIM.userId, 'MESSAGE': data.MESSAGE, 'USERS_MESSAGE': data.USERS_MESSAGE, 'USERS': data.USERS, 'USER_IN_GROUP': data.USER_IN_GROUP, 'WO_USER_IN_GROUP': data.WO_USER_IN_GROUP}, 5);

				if (this.BXIM.animationSupport)
				{
					if (this.popupMessengerBodyAnimation != null)
						this.popupMessengerBodyAnimation.stop();
					(this.popupMessengerBodyAnimation = new BX.easing({
						duration : 800,
						start : { scroll : this.popupMessengerBody.scrollTop},
						finish : { scroll : this.popupMessengerBody.scrollHeight - this.popupMessengerBody.offsetHeight},
						transition : BX.easing.makeEaseInOut(BX.easing.transitions.quart),
						step : BX.delegate(function(state){
							this.popupMessengerBody.scrollTop = state.scroll;
						}, this)
					})).animate();
				}
				else
				{
					this.popupMessengerBody.scrollTop = this.popupMessengerBody.scrollHeight - this.popupMessengerBody.offsetHeight;
				}
			}
			else
			{
				if (data.ERROR == 'SESSION_ERROR' && this.sendAjaxTry < 2)
				{
					this.sendAjaxTry++;
					BX.message({'bitrix_sessid': data.BITRIX_SESSID});
					setTimeout(BX.delegate(function(){
						this.sendMessageTmp[messageTmpIndex] = false;
						this.sendMessageAjax(messageTmpIndex, recipientId, messageText, sendMessageToChat);
					}, this), 2000);
					BX.onCustomEvent(window, 'onImError', [data.ERROR, data.BITRIX_SESSID]);
				}
				else if (data.ERROR == 'AUTHORIZE_ERROR' && this.sendAjaxTry < 2)
				{
					this.sendAjaxTry++;
					setTimeout(BX.delegate(function(){
						this.sendMessageTmp[messageTmpIndex] = false;
						this.sendMessageAjax(messageTmpIndex, recipientId, messageText, sendMessageToChat);
					}, this), 10000);
					BX.onCustomEvent(window, 'onImError', [data.ERROR]);
				}
				else
				{
					this.sendMessageTmp[messageTmpIndex] = false;
					var element = BX.findChild(this.popupMessengerBodyWrap, {attribute: {'data-messageid': 'temp'+messageTmpIndex}}, true);
					var lastMessageElementDate = BX.findChild(element, {className : "bx-messenger-content-item-date"}, true);
					if (lastMessageElementDate)
					{
						if (data.ERROR == 'SESSION_ERROR' || data.ERROR == 'AUTHORIZE_ERROR' || data.ERROR == 'UNKNOWN_ERROR' || data.ERROR == 'IM_MODULE_NOT_INSTALLED')
							lastMessageElementDate.innerHTML = BX.message('IM_M_NOT_DELIVERED');
						else
							lastMessageElementDate.innerHTML = data.ERROR;
					}
					BX.onCustomEvent(window, 'onImError', ['SEND_ERROR', data.ERROR, data.TMP_ID, data.SEND_DATE, data.SEND_MESSAGE, data.RECIPIENT_ID]);

					var lastMessageElementStatus = BX.findChild(element, {className : "bx-messenger-content-item-status"}, true);
					if (lastMessageElementStatus)
					{
						lastMessageElementStatus.innerHTML = '';
						BX.adjust(lastMessageElementStatus, {children: [
							BX.create("span", { attrs: { title: BX.message('IM_M_RETRY') }, props : { className : "bx-messenger-content-item-error"}, children:[
								BX.create("span", { props : { className : "bx-messenger-content-item-error-icon"}})
							]})
						]});
					}
					if (this.message['temp'+messageTmpIndex])
						this.message['temp'+messageTmpIndex].retry = true;
				}
			}
		}, this),
		onfailure: BX.delegate(function()	{
			this.sendMessageFlag--;
			this.sendMessageTmp[messageTmpIndex] = false;
			var element = BX.findChild(this.popupMessengerBodyWrap, {attribute: {'data-messageid': 'temp'+messageTmpIndex}}, true);
			var lastMessageElementDate = BX.findChild(element, {className : "bx-messenger-content-item-date"}, true);
			if (lastMessageElementDate)
				lastMessageElementDate.innerHTML = BX.message('IM_M_NOT_DELIVERED');

			var lastMessageElementStatus = BX.findChild(element, {className : "bx-messenger-content-item-status"}, true);
			if (lastMessageElementStatus)
			{
				lastMessageElementStatus.innerHTML = '';
				BX.adjust(lastMessageElementStatus, {children: [
					BX.create("span", { attrs: { title: BX.message('IM_M_RETRY'), 'data-messageid': 'temp'+messageTmpIndex, 'data-chat': sendMessageToChat? 'Y':'N' }, props : { className : "bx-messenger-content-item-error"}, children:[
						BX.create("span", { props : { className : "bx-messenger-content-item-error-icon"}})
					]})
				]});
			}
			this.sendAjaxTry = 0;
			try {
				if (typeof(_ajax) == 'object' && _ajax.status == 0)
					BX.onCustomEvent(window, 'onImError', ['CONNECT_ERROR']);
			}
			catch(e) {}
			this.message['temp'+messageTmpIndex].retry = true;
		}, this)
	});
};

BX.Messenger.prototype.sendMessageRetry = function()
{
	var currentTab = this.currentTab;
	var messageStack = [];
	for (var i = 0; i < this.showMessage[currentTab].length; i++)
	{
		var message = this.message[this.showMessage[currentTab][i]];
		if (!message || message.id.indexOf('temp') != 0)
			continue;

		message.text = BX.IM.prepareTextBack(message.text);

		messageStack.push(message);
	}
	if (messageStack.length <= 0)
		return false;

	messageStack.sort(function(i, ii) {i = i.id.substr(4); ii = ii.id.substr(4); if (i < ii) { return -1; } else if (i > ii) { return 1;}else{ return 0;}});
	for (var i = 0; i < messageStack.length; i++)
	{
		var element = BX.findChild(this.popupMessengerBodyWrap, {attribute: {'data-messageid': ''+messageStack[i].id+''}}, true);
		var lastMessageElementStatus = BX.findChild(element, {className : "bx-messenger-content-item-status"}, true);
		if (lastMessageElementStatus)
		{
			lastMessageElementStatus.innerHTML = '';
			BX.adjust(lastMessageElementStatus, {children: [
				BX.create("span", { props : { className : "bx-messenger-content-item-progress"}})
			]});
		}

		var lastMessageElementDate = BX.findChild(element, {className : "bx-messenger-content-item-date"}, true);
		if (lastMessageElementDate)
			lastMessageElementDate.innerHTML = BX.message('IM_M_DELIVERED');

		this.sendMessageRetryTimeout(messageStack[i], 100*i);
	}
};

BX.Messenger.prototype.sendMessageRetryTimeout = function(message, timeout)
{
	clearTimeout(this.sendMessageTmpTimeout[message.id]);
	this.sendMessageTmpTimeout[message.id] = setTimeout(BX.delegate(function() {
		this.sendMessageAjax(message.id.substr(4), message.recipientId, message.text, message.recipientId.toString().substr(0,4) == 'chat');
	}, this), timeout);
};

BX.Messenger.prototype.openSmileMenu = function()
{
	if (!BX.proxy_context)
		return false;

	if (this.popupPopupMenu != null)
		this.popupPopupMenu.destroy();

	if (this.popupSmileMenu != null)
	{
		this.popupSmileMenu.destroy();
		return false;
	}

	var arGalleryItem = {};
	for (var id in this.smile)
	{
		if (!arGalleryItem[this.smile[id].SET_ID])
			arGalleryItem[this.smile[id].SET_ID] = [];

		arGalleryItem[this.smile[id].SET_ID].push(
			BX.create("img", { props : { className : 'bx-messenger-smile-gallery-image'}, attrs : { 'data-code': BX.util.htmlspecialcharsback(id), style: "width: "+this.smile[id].WIDTH+"px; height: "+this.smile[id].HEIGHT+"px", src : this.smile[id].IMAGE, alt : id, title : BX.util.htmlspecialcharsback(this.smile[id].NAME)}})
		);
	}

	var setCount = 0;
	var arGallery = [];
	var arSet = [
		BX.create("span", { props : { className : "bx-messenger-smile-nav-name" }, html: BX.message('IM_SMILE_SET')})
	];
	for (var id in this.smileSet)
	{
		if (!arGalleryItem[id])
			continue;

		setCount++;
		arGallery.push(
			BX.create("span", { attrs : { 'data-set-id': id }, props : { className : "bx-messenger-smile-gallery-set"+(setCount > 1? ' bx-messenger-smile-gallery-set-hide': '') }, children: arGalleryItem[id]})
		);
		arSet.push(
			BX.create("span", { attrs : { 'data-set-id': id, title : BX.util.htmlspecialcharsback(this.smileSet[id].NAME) }, props : { className : "bx-messenger-smile-nav-item"+(setCount == 1? ' bx-messenger-smile-nav-item-active': '')}})
		);
	}

	this.popupSmileMenu = new BX.PopupWindow('bx-messenger-popup-smile', BX.proxy_context, {
		lightShadow : false,
		offsetTop: this.desktop.run()? 0: -7,
		offsetLeft: 5,
		autoHide: true,
		closeByEsc: true,
		bindOptions: {position: "top"},
		zIndex: 200,
		events : {
			onPopupClose : function() { this.destroy() },
			onPopupDestroy : BX.delegate(function() { this.popupSmileMenu = null; }, this)
		},
		content : BX.create("div", { props : { className : "bx-messenger-smile" }, children: [
			this.popupSmileMenuGallery = BX.create("div", { props : { className : "bx-messenger-smile-gallery" }, children: arGallery}),
			this.popupSmileMenuSet = BX.create("div", { props : { className : "bx-messenger-smile-nav"+(setCount <= 1? " bx-messenger-smile-nav-disabled": "")}, children: arSet})
		]})
	});
	this.popupSmileMenu.setAngle({offset: 4});
	this.popupSmileMenu.show();

	BX.bindDelegate(this.popupSmileMenuGallery, "click", {className: 'bx-messenger-smile-gallery-image'}, BX.delegate(function(){
		this.insertTextareaText(' '+BX.proxy_context.getAttribute('data-code')+' ', false);
		this.popupSmileMenu.close();
	}, this));

	BX.bindDelegate(this.popupSmileMenuSet, "click", {className: 'bx-messenger-smile-nav-item'}, BX.delegate(function(){
		if (BX.hasClass(BX.proxy_context, 'bx-messenger-smile-nav-item-active'))
			return false;

		var nodesGallery = BX.findChildren(this.popupSmileMenuGallery, {className : "bx-messenger-smile-gallery-set"}, false);
		var nodesSet = BX.findChildren(this.popupSmileMenuSet, {className : "bx-messenger-smile-nav-item"}, false);
		for (var i = 0; i < nodesSet.length; i++)
		{
			if (BX.proxy_context == nodesSet[i])
			{
				BX.removeClass(nodesGallery[i], 'bx-messenger-smile-gallery-set-hide');
				BX.addClass(nodesSet[i], 'bx-messenger-smile-nav-item-active');
			}
			else
			{
				BX.addClass(nodesGallery[i], 'bx-messenger-smile-gallery-set-hide');
				BX.removeClass(nodesSet[i], 'bx-messenger-smile-nav-item-active');
			}
		}
	}, this));


	return false;
};

BX.Messenger.prototype.insertQuoteText = function(name, date, text, insertInTextarea)
{
	var arQuote = [];
	arQuote.push((this.popupMessengerTextarea && this.popupMessengerTextarea.value.length>0?"\n":'')+this.historyMessageSplit);
	arQuote.push(BX.util.htmlspecialcharsback(name)+' ['+BX.IM.formatDate(date)+']');
	arQuote.push(text);
	arQuote.push(this.historyMessageSplit+"\n");

	if (insertInTextarea !== false)
	{
		this.insertTextareaText(arQuote.join("\n"), false);

		setTimeout(BX.delegate(function(){
			this.popupMessengerTextarea.scrollTop = this.popupMessengerTextarea.scrollHeight;
			this.popupMessengerTextarea.focus();
		}, this), 100);
	}
	else
	{
		return arQuote.join("\n");
	}
}

BX.Messenger.prototype.insertTextareaText = function(text, returnBack)
{
	if (!this.popupMessengerTextarea && opener.BXIM.messenger.popupMessengerTextarea)
		this.popupMessengerTextarea = opener.BXIM.messenger.popupMessengerTextarea;

	if (this.popupMessengerTextarea.selectionStart || this.popupMessengerTextarea.selectionStart == '0')
	{
		var selectionStart = this.popupMessengerTextarea.selectionStart;
		var selectionEnd = this.popupMessengerTextarea.selectionEnd;
		this.popupMessengerTextarea.value = this.popupMessengerTextarea.value.substring(0,selectionStart)+text+this.popupMessengerTextarea.value.substring(selectionEnd, this.popupMessengerTextarea.value.length);

		returnBack = returnBack != false;
		if (returnBack)
		{
			this.popupMessengerTextarea.selectionStart = selectionStart+1;
			this.popupMessengerTextarea.selectionEnd = selectionStart+1;
		}
		else if (BX.browser.IsChrome() || BX.browser.IsSafari() || this.desktop.ready())
		{
			this.popupMessengerTextarea.selectionStart = this.popupMessengerTextarea.value.length+1;
			this.popupMessengerTextarea.selectionEnd = this.popupMessengerTextarea.value.length+1;
		}
	}
	if (document.selection && document.documentMode && document.documentMode <= 8)
	{
		this.popupMessengerTextarea.focus();
		var select=document.selection.createRange();
		select.text = text;
	}
};

BX.Messenger.prototype.resizeTextareaStart = function(e)
{
	if (this.webrtc.callOverlayFullScreen) return false;

	if(!e) e = window.event;

	this.popupMessengerTextareaResize.wndSize = BX.GetWindowScrollPos();
	this.popupMessengerTextareaResize.pos = BX.pos(this.popupMessengerTextarea);
	this.popupMessengerTextareaResize.y = e.clientY + this.popupMessengerTextareaResize.wndSize.scrollTop;
	this.popupMessengerTextareaResize.textOffset = this.popupMessengerTextarea.offsetHeight;
	this.popupMessengerTextareaResize.bodyOffset = this.popupMessengerBody.offsetHeight;

	BX.bind(document, "mousemove", BX.proxy(this.resizeTextareaMove, this));
	BX.bind(document, "mouseup", BX.proxy(this.resizeTextareaStop, this));

	if(document.body.setCapture)
		document.body.setCapture();

	document.onmousedown = BX.False;

	var b = document.body;
	b.ondrag = b.onselectstart = BX.False;
	b.style.MozUserSelect = 'none';
	b.style.cursor = 'move';

	if (this.popupSmileMenu)
		this.popupSmileMenu.close();
};
BX.Messenger.prototype.resizeTextareaMove = function(e)
{
	if(!e) e = window.event;

	var windowScroll = BX.GetWindowScrollPos();
	var x = e.clientX + windowScroll.scrollLeft;
	var y = e.clientY + windowScroll.scrollTop;
	if(this.popupMessengerTextareaResize.y == y)
		return;

	var textareaHeight = Math.max(Math.min(-(y-this.popupMessengerTextareaResize.pos.top) + this.popupMessengerTextareaResize.textOffset, 225), 43);

	this.popupMessengerTextareaSize = textareaHeight;
	this.popupMessengerTextarea.style.height = textareaHeight + 'px';
	this.popupMessengerBodySize = this.popupMessengerTextareaResize.textOffset-textareaHeight + this.popupMessengerTextareaResize.bodyOffset;
	this.popupMessengerBody.style.height = this.popupMessengerBodySize + 'px';
	this.resizeMainWindow();

	this.popupMessengerTextareaResize.x = x;
	this.popupMessengerTextareaResize.y = y;

};

BX.Messenger.prototype.resizeTextareaStop = function()
{
	if(document.body.releaseCapture)
		document.body.releaseCapture();

	BX.unbind(document, "mousemove", BX.proxy(this.resizeTextareaMove, this));
	BX.unbind(document, "mouseup", BX.proxy(this.resizeTextareaStop, this));

	document.onmousedown = null;

	this.popupMessengerBody.scrollTop = this.popupMessengerBody.scrollHeight - this.popupMessengerBody.offsetHeight;

	var b = document.body;
	b.ondrag = b.onselectstart = null;
	b.style.MozUserSelect = '';
	b.style.cursor = '';

	clearTimeout(this.BXIM.adjustSizeTimeout);
	this.BXIM.adjustSizeTimeout = setTimeout(BX.delegate(function(){
		this.BXIM.setLocalConfig('msz3', {
			'wz': this.popupMessengerFullWidth,
			'ta': this.popupMessengerTextareaSize,
			'b': this.popupMessengerBodySize,
			'cl': this.popupContactListSize,
			'hi': this.popupHistoryItemsSize,
			'fz': this.popupMessengerFullHeight,
			'ez': this.popupContactListElementsSize,
			'nz': this.notify.popupNotifySize,
			'hf': this.popupHistoryFilterVisible,
			'dw': window.innerWidth,
			'dh': window.innerHeight,
			'place': 'taMove'
		});
	}, this), 500);
};

BX.Messenger.prototype.resizeWindowStart = function()
{
	if (this.webrtc.callOverlayFullScreen) return false;
	if (this.popupMessengerTopLine)
		BX.remove(this.popupMessengerTopLine);

	this.popupMessengerWindow.pos = BX.pos(this.popupMessengerContent);
	this.popupMessengerWindow.mb = this.popupMessengerBodySize;
	this.popupMessengerWindow.nb = this.notify.popupNotifySize;

	BX.bind(document, "mousemove", BX.proxy(this.resizeWindowMove, this));
	BX.bind(document, "mouseup", BX.proxy(this.resizeWindowStop, this));

	if (document.body.setCapture)
		document.body.setCapture();

	document.onmousedown = BX.False;

	var b = document.body;
	b.ondrag = b.onselectstart = BX.False;
	b.style.MozUserSelect = 'none';
	b.style.cursor = 'move';
};
BX.Messenger.prototype.resizeWindowMove = function(e)
{
	if(!e) e = window.event;

	var windowScroll = BX.GetWindowScrollPos();
	var x = e.clientX + windowScroll.scrollLeft;
	var y = e.clientY + windowScroll.scrollTop;

	this.popupMessengerFullHeight = Math.max(Math.min(y-this.popupMessengerWindow.pos.top, 1000), this.popupMessengerMinHeight);
	this.popupMessengerFullWidth = Math.max(Math.min(x-this.popupMessengerWindow.pos.left, 1200), this.popupMessengerMinWidth);

	this.popupMessengerContent.style.height = this.popupMessengerFullHeight+'px';
	this.popupMessengerContent.style.width = this.popupMessengerFullWidth+'px';

	var changeHeight = this.popupMessengerFullHeight-Math.max(Math.min(this.popupMessengerWindow.pos.height, 1000), this.popupMessengerMinHeight);

	this.popupMessengerBodySize = this.popupMessengerWindow.mb+changeHeight;
	if (this.popupMessengerBody != null)
		this.popupMessengerBody.style.height = this.popupMessengerBodySize + 'px';

	if (this.popupMessengerExtra != null)
		this.popupMessengerExtra.style.height = this.popupMessengerFullHeight+'px';

	this.notify.popupNotifySize = Math.max(this.popupMessengerWindow.nb+(this.popupMessengerBodySize - this.popupMessengerWindow.mb), 383);
	if (this.notify.popupNotifyItem != null)
		this.notify.popupNotifyItem.style.height = this.notify.popupNotifySize+'px';

	if (this.webrtc.callOverlay)
	{
		BX.style(this.webrtc.callOverlay, 'transition', 'none');
		BX.style(this.webrtc.callOverlay, 'width', (this.popupMessengerExtra.style.display == "block"? this.popupMessengerExtra.offsetWidth-1: this.popupMessengerDialog.offsetWidth-1)+'px');
		BX.style(this.webrtc.callOverlay, 'height', (this.popupMessengerFullHeight-1)+'px');
	}

	this.BXIM.messenger.redrawChatHeader();
	this.resizeMainWindow();
};

BX.Messenger.prototype.resizeWindowStop = function()
{
	if(document.body.releaseCapture)
		document.body.releaseCapture();

	BX.unbind(document, "mousemove", BX.proxy(this.resizeWindowMove, this));
	BX.unbind(document, "mouseup", BX.proxy(this.resizeWindowStop, this));

	document.onmousedown = null;

	this.popupMessengerBody.scrollTop = this.popupMessengerBody.scrollHeight - this.popupMessengerBody.offsetHeight;

	var b = document.body;
	b.ondrag = b.onselectstart = null;
	b.style.MozUserSelect = '';
	b.style.cursor = '';

	if (this.webrtc.callOverlay)
		BX.style(this.webrtc.callOverlay, 'transition', '');

	clearTimeout(this.BXIM.adjustSizeTimeout);
	this.BXIM.adjustSizeTimeout = setTimeout(BX.delegate(function(){
		this.BXIM.setLocalConfig('msz3', {
			'wz': this.popupMessengerFullWidth,
			'ta': this.popupMessengerTextareaSize,
			'b': this.popupMessengerBodySize,
			'cl': this.popupContactListSize,
			'hi': this.popupHistoryItemsSize,
			'fz': this.popupMessengerFullHeight,
			'ez': this.popupContactListElementsSize,
			'nz': this.notify.popupNotifySize,
			'hf': this.popupHistoryFilterVisible,
			'dw': window.innerWidth,
			'dh': window.innerHeight,
			'place': 'winMove'
		});
	}, this), 500);
};

/* COMMON */

BX.Messenger.prototype.newMessage = function(send)
{
	send = send != false;

	var arNewMessage = [];
	var arNewMessageText = [];
	var flashCount = 0;
	var flashNames = {};
	for (var i in this.flashMessage)
	{
		if (this.BXIM.isFocus() && this.popupMessenger != null)
		{
			var skip = false;
			if (i == this.currentTab)
				skip = true;

			if (skip)
			{
				for (var k in this.flashMessage[i])
				{
					if (this.flashMessage[i][k] !== false)
					{
						this.flashMessage[i][k] = false;
						flashCount++;
					}
				}
				continue;
			}
		}

		for (var k in this.flashMessage[i])
		{
			if (this.flashMessage[i][k] !== false)
			{
				var isChat = this.message[k].recipientId.toString().substr(0,4) == 'chat';
				var recipientId = this.message[k].recipientId;
				var senderId = !isChat && this.message[k].senderId == 0? i: this.message[k].senderId;
				var messageText = this.message[k].text_mobile? this.message[k].text_mobile: this.message[k].text;
				if (i != this.BXIM.userId)
					flashNames[i] = (isChat? this.chat[recipientId.substr(4)].name: this.users[senderId].name);
				messageText = messageText.replace(/------------------------------------------------------(.*?)------------------------------------------------------/gmi, "["+BX.message("IM_M_QUOTE_BLOCK")+"]");
				if (messageText.length > 150)
				{
					messageText = messageText.substr(0, 150);
					var lastSpace = messageText.lastIndexOf(' ');
					if (lastSpace < 140)
						messageText = messageText.substr(0, lastSpace)+'...';
					else
						messageText = messageText.substr(0, 140)+'...';
				}

				var element = BX.create("div", {attrs : { 'data-userId' : isChat? recipientId: senderId, 'data-messageId' : k}, props : { className: "bx-notifier-item"}, children : [
					BX.create('span', {props : { className : "bx-notifier-item-content" }, children : [
						BX.create('span', {props : { className : "bx-notifier-item-avatar"+(isChat? ' bx-notifier-item-avatar-chat': '') }, children : [
							BX.create('img', {props : { className : "bx-notifier-item-avatar-img" },attrs : {src : isChat? this.chat[recipientId.substr(4)].avatar: this.users[senderId].avatar}})
						]}),
						BX.create("a", {attrs : {href : '#', 'data-messageId' : k}, props : { className: "bx-notifier-item-delete"}}),
						BX.create('span', {props : { className : "bx-notifier-item-date" }, html: BX.IM.formatDate(this.message[k].date)}),
						BX.create('span', {props : { className : "bx-notifier-item-name" }, html: isChat? this.chat[recipientId.substr(4)].name: this.users[senderId].name}),
						BX.create('span', {props : { className : "bx-notifier-item-text" }, html: (isChat && senderId>0?'<i>'+this.users[senderId].name+'</i>: ':'')+BX.IM.prepareText(messageText, false, true)})
					]})
				]});
				if (!this.BXIM.xmppStatus || this.BXIM.xmppStatus && isChat)
				{
					arNewMessage.push(element);

					messageText = BX.util.htmlspecialcharsback(messageText);
					messageText = messageText.split('<br />').join("\n");
					messageText = messageText.replace(/\[USER=([0-9]{1,})\](.*?)\[\/USER\]/ig, function(whole, userId, text) {return text;});
					messageText = messageText.replace(/\[PCH=([0-9]{1,})\](.*?)\[\/PCH\]/ig, function(whole, historyId, text) {return text;});

					arNewMessageText.push({
						'id':  isChat? recipientId: senderId,
						'title':  BX.util.htmlspecialcharsback(isChat? this.chat[recipientId.substr(4)].name: this.users[senderId].name),
						'text':  (isChat && senderId>0?this.users[senderId].name+': ':'')+messageText,
						'icon':  isChat? this.chat[recipientId.substr(4)].avatar: this.users[senderId].avatar,
						'tag':  'im-messenger-'+(isChat? recipientId: senderId)
					});
				}
				this.flashMessage[i][k] = false;
			}
		}
	}

	if (!(!this.desktop.ready() && this.desktop.run()) && !this.desktop.ready() && this.BXIM.desktopStatus)
		return false;

	if (arNewMessage.length > 5)
	{
		var names = '';
		for (var i in flashNames)
			names += ', <i>'+flashNames[i]+'</i>';

		var notify = {
			id: 0, type: 4, date: (+new Date)/1000,
			title: BX.message('IM_NM_MESSAGE_1').replace('#COUNT#', arNewMessage.length),
			text: BX.message('IM_NM_MESSAGE_2').replace('#USERS#', names.substr(2))
		};
		arNewMessage = [];
		arNewMessage.push(this.notify.createNotify(notify, true))

		arNewMessageText = []
		arNewMessageText.push({
			'id': '',
			'title':  BX.message('IM_NM_MESSAGE_1').replace('#COUNT#', arNewMessage.length),
			'text':  BX.message('IM_NM_MESSAGE_2').replace('#USERS#', BX.util.htmlspecialcharsback(names.substr(2))).replace(/<\/?[^>]+>/gi, '')
		})
	}
	else if (arNewMessage.length == 0)
	{
		if (flashCount > 0 && this.desktop.ready())
			BX.desktop.flashIcon();

		if (send && flashCount > 0 && this.BXIM.settings.status != 'dnd')
		{
			this.BXIM.playSound("newMessage2");
		}

		return false;
	}

	if (this.desktop.ready())
		BX.desktop.flashIcon();

	//if (this.BXIM.settings.status == 'dnd')
	//	return false;

	if (this.desktop.ready())
	{
		for (var i = 0; i < arNewMessage.length; i++)
		{
			var dataMessageId = arNewMessage[i].getAttribute("data-messageId");
			var messsageJs =
				'var notify = BX.findChild(document.body, {className : "bx-notifier-item"}, true);'+
				'notify.style.cursor = "pointer";'+
				'BX.bind(notify, "click", function(){BX.desktop.onCustomEvent("main", "bxImClickNewMessage", [notify.getAttribute("data-userId")]); BX.desktop.windowCommand("close")});'+
				'BX.bind(BX.findChild(notify, {className : "bx-notifier-item-delete"}, true), "click", function(event){ BX.desktop.onCustomEvent("main", "bxImClickCloseMessage", [notify.getAttribute("data-userId")]); BX.desktop.windowCommand("close"); BX.IM.preventDefault(event); });'+
				'BX.bind(notify, "contextmenu", function(){ BX.desktop.windowCommand("close")});';
			this.desktop.openNewMessage(dataMessageId, arNewMessage[i], messsageJs);
		}
	}
	else if(send && !this.BXIM.windowFocus && this.BXIM.notifyManager.nativeNotifyGranted())
	{
		for (var i = 0; i < arNewMessageText.length; i++)
		{
			var notify = arNewMessageText[i];
			notify.onshow = function() {
				var notify = this;
				setTimeout(function(){
					notify.close();
				}, 5000)
			}
			notify.onclick = function() {
				window.focus();
				top.BXIM.openMessenger(notify.id);
				this.close();
			}
			this.BXIM.notifyManager.nativeNotify(notify)
		}
	}
	else
	{
		if (this.BXIM.windowFocus && this.BXIM.notifyManager.nativeNotifyGranted())
		{
			BX.localStorage.set('mnnb', true, 1);
		}
		for (var i = 0; i < arNewMessage.length; i++)
		{
			this.BXIM.notifyManager.add({
				'html': arNewMessage[i],
				'tag': 'im-message-'+arNewMessage[i].getAttribute('data-userId'),
				'userId': arNewMessage[i].getAttribute('data-userId'),
				'click': BX.delegate(function(popup) {
					this.openMessenger(popup.notifyParams.userId);
					popup.close();
				}, this),
				'close': BX.delegate(function(popup) {
					this.readMessage(popup.notifyParams.userId);
				}, this)
			});
		}
	}

	if (this.desktop.ready())
		BX.desktop.flashIcon();

	if (send)
	{
		this.BXIM.playSound("newMessage1");
	}
};


BX.Messenger.prototype.updateMessageCount = function(send)
{
	send = send != false;
	var count = 0;
	for (var i in this.unreadMessage)
		count = count+this.unreadMessage[i].length;

	if (send)
		BX.localStorage.set('mumc', {'unread':this.unreadMessage, 'flash':this.flashMessage}, 5);
	if (this.messageCount != count)
		BX.onCustomEvent(window, 'onImUpdateCounterMessage', [count, 'MESSAGE']);

	this.messageCount = count;

	var messageCountLabel = '';
	if (this.messageCount > 99)
		messageCountLabel = '99+';
	else if (this.messageCount > 0)
		messageCountLabel = this.messageCount;

	if (this.notify.panelButtonMessageCount != null)
	{
		this.notify.panelButtonMessageCount.innerHTML = messageCountLabel;
		this.notify.adjustPosition({"resize": true, "timeout": 500});
	}

	if (this.recentListTabCounter != null)
		this.recentListTabCounter.innerHTML = this.messageCount>0? '<span class="bx-messenger-cl-count-digit">'+messageCountLabel+'</span>': '';

	if (this.desktop.run())
	{
		if (this.messageCount == 0)
			BX.hide(this.notify.panelButtonMessage);
		else
			BX.show(this.notify.panelButtonMessage);

		BX.desktop.setTabBadge('im', this.messageCount);
	}
	return this.messageCount;
};

BX.Messenger.prototype.setStatus = function(status, send)
{
	send = send != false;

	this.BXIM.settings.status = status;
	this.BXIM.updateCounter();

	if (this.contactListPanelStatus != null && !BX.hasClass(this.contactListPanelStatus, 'bx-messenger-cl-panel-status-'+status))
	{
		this.contactListPanelStatus.className = 'bx-messenger-cl-panel-status-wrap bx-messenger-cl-panel-status-'+status;

		var statusText = BX.findChild(this.contactListPanelStatus, {className : "bx-messenger-cl-panel-status-text"}, true);

		statusText.innerHTML = BX.message("IM_STATUS_"+status.toUpperCase());

		if (send)
		{
			this.BXIM.saveSettings({'status': status});
			this.notify.setStatus(status);
			BX.onCustomEvent(this, 'onStatusChange', [status]);
			BX.localStorage.set('mms', status, 5);
		}
	}
};

BX.Messenger.prototype.resizeMainWindow = function()
{
	if (!this.desktop.run())
	{
		if (this.popupMessengerExtra.style.display == "block")
			this.popupContactListElementsSize = this.popupMessengerExtra.offsetHeight-175;
		else
			this.popupContactListElementsSize = this.popupMessengerDialog.offsetHeight-175;

		this.popupContactListElements.style.height = this.popupContactListElementsSize+'px';
	}
};

BX.Messenger.prototype.showTopLine = function(text, buttons)
{
	if (typeof (text) != 'string')
		return false;

	var arElements = [];
	if (typeof (buttons) == 'object')
	{
		var arButtons = [];
		for (var i = 0; i < buttons.length; i++)
			arButtons.push(BX.create('span', { props : { className : "bx-messenger-box-topline-button" }, html: buttons[i].title, events: {click: buttons[i].callback}}));

		arElements.push(BX.create('span', { props : { className : "bx-messenger-box-topline-buttons" }, children: arButtons}));
	}
	arElements.push(BX.create('span', { props : { className : "bx-messenger-box-topline-text" }, children: [
		BX.create('span', { props : { className : "bx-messenger-box-topline-text-inner"}, html: text})
	]}));

	this.popupMessengerTopLine.innerHTML = '';
	BX.adjust(this.popupMessengerTopLine, {children: arElements});
	BX.addClass(this.popupMessengerTopLine, "bx-messenger-box-topline-show");

	return true;
};

BX.Messenger.prototype.hideTopLine = function()
{
	BX.removeClass(this.popupMessengerTopLine, "bx-messenger-box-topline-show");
};

BX.Messenger.prototype.closeMenuPopup = function()
{
	if (this.popupPopupMenu != null && this.popupPopupMenuDateCreate+100 < (+new Date()))
		this.popupPopupMenu.close();
	if (this.popupSmileMenu != null)
		this.popupSmileMenu.close();
	if (this.notify.popupNotifyMore != null)
		this.notify.popupNotifyMore.destroy();
	if (this.popupChatUsers != null)
		this.popupChatUsers.destroy();
	if (this.webrtc.popupKeyPad != null)
		this.webrtc.popupKeyPad.destroy();
	if (this.popupChatDialog != null)
		this.popupChatDialog.destroy();
};

BX.Messenger.MenuPrepareList = function(menuItems)
{
	var items = [];
	for (var i = 0; i < menuItems.length; i++)
	{
		var item = menuItems[i];
		if (item == null)
			continue;

		if (!item.separator && (!item.text || !BX.type.isNotEmptyString(item.text)))
			continue;

		if (item.separator)
		{
			items.push(BX.create("div", { props : { className : "bx-messenger-menu-hr" }}));
		}
		else if (item.type == 'call')
		{
			var a = BX.create("a", {
				props : { className: "bx-messenger-popup-menu-item"},
				attrs : { title : item.title ? item.title : "",  href : item.href ? item.href : ""},
				events : item.onclick && BX.type.isFunction(item.onclick) ? { click : item.onclick } : null,
				html :  '<div class="bx-messenger-popup-menu-item-call"><span class="bx-messenger-popup-menu-item-left"></span><span class="bx-messenger-popup-menu-item-title">' + item.text + '</span><span class="bx-messenger-popup-menu-right"></span></div>'+
						'<div><span class="bx-messenger-popup-menu-item-left"></span><span class="bx-messenger-popup-menu-item-text">' + item.phone + '</span><span class="bx-messenger-popup-menu-right"></span></div>'
			});

			if (item.href)
				a.href = item.href;
			items.push(a);
		}
		else
		{
			var a = BX.create("a", {
				props : { className: "bx-messenger-popup-menu-item" +  (BX.type.isNotEmptyString(item.className) ? " " + item.className : "")},
				attrs : { title : item.title ? item.title : "",  href : item.href ? item.href : ""},
				events : item.onclick && BX.type.isFunction(item.onclick) ? { click : item.onclick } : null,
				html :  '<span class="bx-messenger-popup-menu-item-left"></span>'+(item.icon? '<span class="bx-messenger-popup-menu-item-icon '+item.icon+'"></span>':'')+'<span class="bx-messenger-popup-menu-item-text">' + item.text + '</span><span class="bx-messenger-popup-menu-right"></span>'
			});

			if (item.href)
				a.href = item.href;
			items.push(a);
		}
	}
	return items;
};

BX.Messenger.prototype.storageSet = function(params)
{
	if (params.key == 'ims')
	{
		if (this.BXIM.settings.viewOffline != params.value.viewOffline || this.BXIM.settings.viewGroup != params.value.viewGroup)
			this.userListRedraw(true);

		if (this.BXIM.settings.sendByEnter != params.value.sendByEnter && this.popupMessengerTextareaSendType)
			this.popupMessengerTextareaSendType.innerHTML = this.BXIM.settings.sendByEnter? 'Enter': (BX.browser.IsMac()? "&#8984;+Enter": "Ctrl+Enter");

		this.BXIM.settings = params.value;
	}
	else if (params.key == 'mus')
	{
		this.updateState(true, false);
	}
	else if (params.key == 'musl')
	{
		this.updateStateLight(true, false);
	}
	else if (params.key == 'mms')
	{
		this.setStatus(params.value, false);
	}
	else if (params.key == 'mct')
	{
		//this.currentTab = params.value;
	}
	else if (params.key == 'mrlr')
	{
		this.recentListHide(userId, false);
	}
	else if (params.key == 'mrd')
	{
		this.BXIM.settings.viewGroup = params.value.viewGroup;
		this.BXIM.settings.viewOffline = params.value.viewOffline;

		this.userListRedraw();
	}
	else if (params.key == 'mgp')
	{
		var viewGroup =  this.contactListSearchText != null && this.contactListSearchText.length > 0? false: this.BXIM.settings.viewGroup;
		if (viewGroup)
			this.groups[params.value.id].status = params.value.status;
		else
			this.woGroups[params.value.id].status = params.value.status;

		this.userListRedraw();
	}
	else if (params.key == 'mrm')
	{
		this.readMessage(params.value, false, false);
	}
	else if (params.key == 'mcl')
	{
		this.leaveFromChat(params.value, false);
	}
	else if (params.key == 'mclk')
	{
		this.kickFromChat(params.value.chatId, params.value.userId);
	}
	else if (params.key == 'mes')
	{
		this.BXIM.settings.enableSound = params.value;
	}
	else if (params.key == 'mti')
	{
		this.messageTmpIndex = params.value;
	}
	else if (params.key == 'mns')
	{
		if (this.popupContactListSearchInput != null)
			this.popupContactListSearchInput.value = params.value != null? params.value+'': '';

		this.contactListSearchText = params.value != null? params.value+'': '';
	}
	else if (params.key == 'msm')
	{
		if (this.message[params.value.id])
			return;

		this.message[params.value.id] = params.value;

		if (this.history[params.value.recipientId])
			this.history[params.value.recipientId].push(params.value.id);
		else
			this.history[params.value.recipientId] = [params.value.id];

		if (this.showMessage[params.value.recipientId])
			this.showMessage[params.value.recipientId].push(params.value.id);
		else
			this.showMessage[params.value.recipientId] = [params.value.id];

		this.updateStateVar(params.value, false, false);

		this.drawTab(params.value.recipientId, true);
	}
	else if (params.key == 'uss')
	{
		this.updateStateStep = parseInt(params.value);
	}
	else if (params.key == 'mumc')
	{
		setTimeout(BX.delegate(function(){
			var send = false;
			if (this.popupMessenger != null && this.BXIM.isFocus())
			{
				delete params.value.unread[this.currentTab];
				send = true;
			}

			this.unreadMessage = params.value.unread;
			this.flashMessage = params.value.flash;

			this.updateMessageCount(send);
		}, this), 500);
	}
	else if (params.key == 'mum')
	{
		this.message[params.value.message.id] = params.value.message;

		if (this.showMessage[params.value.userId])
		{
			this.showMessage[params.value.userId].push(params.value.message.id);
			this.showMessage[params.value.userId] = BX.util.array_unique(this.showMessage[params.value.userId]);
		}
		else
			this.showMessage[params.value.userId] = [params.value.message.id];

		this.drawMessage(params.value.userId, params.value.message, this.currentTab == params.value.userId);
	}
	else if (params.key == 'muum')
	{
		this.changeUnreadMessage(params.value, false);
	}
	else if (params.key == 'mcam' && !this.BXIM.ppServerStatus)
	{
		if (this.popupMessenger != null && !this.webrtc.callInit)
			this.popupMessenger.close();
	}
};


BX.IM.Desktop = function(BXIM, params)
{
	this.BXIM = BXIM;

	this.clientVersion = false;
	this.markup = BX('placeholder-messanger');
	this.htmlWrapperHead = null;
	this.showNotifyId = {};
	this.showMessageId = {};
	this.lastSetIcon = null;

	this.topmostWindow = null;
	this.topmostWindowTimeout = null;
	this.topmostWindowCloseTimeout = null;

	this.minCallVideoWidth = 320;
	this.minCallVideoHeight = 240;
	this.minCallWidth = 320;
	this.minCallHeight = 35;
	this.minHistoryWidth = 608;
	this.minHistoryHeight = 593;
	this.minSettingsWidth = 567;
	this.minSettingsHeight = BX.browser.IsMac()? 326: 335;

	if (this.run() && !this.ready() && BX.desktop.getApiVersion() > 0)
	{
		this.BXIM.init = false;
		this.BXIM.tryConnect = false;
	}
	else if (this.run() && this.BXIM.init)
	{
		BX.desktop.setUserInfo(this.BXIM.userParams);

		BX.desktop.addTab({
			id: 'config',
			title: BX.message('IM_SETTINGS'),
			order: 150,
			target: false,
			events: {
				open: BX.delegate(function(e){
					this.BXIM.openSettings({'active': BX.desktop.getCurrentTab()});
				}, this)
			}
		});

		BX.desktop.addSeparator({
			order: 500
		});

		if (this.ready() && !this.BXIM.bitrix24net)
		{
			BX.desktop.addTab({
				id: 'im-lf',
				title: BX.message('IM_DESKTOP_GO_SITE').replace('#COUNTER#', ''),
				order: 550,
				target: false,
				events: {
					open: function(){
						BX.desktop.browse(BX.desktop.getCurrentUrl())
					}
				}
			});
		}

		if (this.BXIM.animationSupport && /Microsoft Windows NT 5/i.test(navigator.userAgent))
			this.BXIM.animationSupport = false;

		if (this.ready())
			this.BXIM.changeFocus(BX.desktop.windowIsFocused());

		BX.bind(window, "keydown", BX.delegate(function(e) {
			if (!(BX.desktop.getCurrentTab() == 'im' || BX.desktop.getCurrentTab() == 'notify' || BX.desktop.getCurrentTab() == 'im-phone'))
				return false;

			if (e.keyCode == 27)
			{
				if (this.messenger.popupSmileMenu)
				{
					this.messenger.popupSmileMenu.destroy();
				}
				else if (this.messenger.popupPopupMenu)
				{
					this.messenger.popupPopupMenu.destroy();
				}
				else if (this.messenger.popupChatDialog && this.messenger.popupChatDialogContactListSearch.value.length >= 0)
				{
					this.messenger.popupChatDialogContactListSearch.value = '';
				}
				else if (this.BXIM.extraOpen)
				{
					BX.desktop.changeTab('im');
					this.messenger.extraClose(true);
				}
				else if (this.messenger.renameChatDialogInput && this.messenger.renameChatDialogInput.value.length > 0)
				{
					this.messenger.renameChatDialogInput.value = this.messenger.chat[this.messenger.currentTab.toString().substr(4)].name;
					this.messenger.popupMessengerTextarea.focus();
				}
				else if (this.messenger.popupContactListSearchInput && this.messenger.popupContactListSearchInput.value.length > 0)
				{
					this.messenger.contactListSearch({'keyCode': 27});
					this.messenger.popupMessengerTextarea.focus();
				}
				else
				{
					this.messenger.textareaHistory[this.messenger.currentTab] = '';
					if (BX.util.trim(this.messenger.popupMessengerTextarea.value).length <= 0 && !this.webrtc.callInit)
					{
						this.messenger.popupMessengerTextarea.value = "";
						BX.desktop.windowCommand('hide');
					}
					else
						this.messenger.popupMessengerTextarea.value = "";
				}
			}
			else if (e.altKey == true)
			{
				if (e.keyCode == 49 || e.keyCode == 50 || e.keyCode == 51
					|| e.keyCode == 52 || e.keyCode == 53 || e.keyCode == 54
					|| e.keyCode == 55 || e.keyCode == 56 || e.keyCode == 57)
				{
					this.messenger.openMessenger(this.messenger.recentListIndex[parseInt(e.keyCode)-49]);
					BX.PreventDefault(e);
				}
				else if (e.keyCode == 48)
				{
					this.messenger.openMessenger(this.messenger.recentListIndex[9]);
					BX.PreventDefault(e);
				}
			}
		}, this));

		BX.desktop.addCustomEvent("bxImClickNewMessage", BX.delegate(function(userId) {
			BX.desktop.windowCommand("show");
			BX.desktop.changeTab('im');
			this.BXIM.openMessenger(userId);
		}, this));
		BX.desktop.addCustomEvent("bxImClickCloseMessage", BX.delegate(function(userId) {
			this.BXIM.messenger.readMessage(userId);
		}, this));
		BX.desktop.addCustomEvent("bxImClickCloseNotify", BX.delegate(function(notifyId) {
			this.BXIM.notify.viewNotify(notifyId);
		}, this));
		BX.desktop.addCustomEvent("bxImClickNotify", BX.delegate(function() {
			BX.desktop.windowCommand("show");
			BX.desktop.changeTab('notify');
		}, this));
		BX.desktop.addCustomEvent("bxCallDecline", BX.delegate(function() {
			var callVideo = this.webrtc.callVideo;
			this.webrtc.callSelfDisabled = true;
			this.webrtc.callCommand(this.webrtc.callChatId, 'decline', {'ACTIVE': this.webrtc.callActive? 'Y': 'N', 'INITIATOR': this.webrtc.initiator? 'Y': 'N'});
			this.BXIM.playSound('stop');
			if (callVideo && this.webrtc.callStreamSelf != null)
				this.webrtc.callOverlayVideoClose();
			else
				this.webrtc.callOverlayClose();
		}, this));
		BX.desktop.addCustomEvent("bxPhoneAnswer", BX.delegate(function() {
			BX.desktop.windowCommand("show");
			BX.desktop.changeTab('im');

			this.BXIM.stopRepeatSound('ringtone');
			this.webrtc.phoneIncomingAnswer();

			this.closeTopmostWindow();
		}, this));
		BX.desktop.addCustomEvent("bxPhoneSkip", BX.delegate(function() {
			this.webrtc.phoneCallFinish();
			this.webrtc.callAbort();
			this.webrtc.callOverlayClose();
		}, this));
		BX.desktop.addCustomEvent("bxCallOpenDialog", BX.delegate(function() {
			BX.desktop.windowCommand("show");
			BX.desktop.changeTab('im');
			if (this.BXIM.dialogOpen)
			{
				if (this.webrtc.callOverlayUserId > 0)
				{
					this.messenger.openChatFlag = false;
					this.messenger.openDialog(this.webrtc.callOverlayUserId, false, false);
				}
				else
				{
					this.messenger.openChatFlag = true;
					this.messenger.openDialog('chat'+this.webrtc.callOverlayChatId, false, false);
				}
			}
			else
			{
				if (this.webrtc.callOverlayUserId > 0)
				{
					this.messenger.openChatFlag = false;
					this.messenger.currentTab = this.webrtc.callOverlayUserId;
				}
				else
				{
					this.messenger.openChatFlag = true;
					this.messenger.currentTab = 'chat'+this.webrtc.callOverlayChatId;
				}
				this.messenger.extraClose(true, false);
			}
			this.webrtc.callOverlayToggleSize(false);
		}, this));
		BX.desktop.addCustomEvent("bxCallMuteMic", BX.delegate(function() {
			if (this.webrtc.phoneCurrentCall)
				this.webrtc.phoneToggleAudio();
			else
				this.webrtc.toggleAudio();

			var icon = BX.findChild(BX('bx-messenger-call-overlay-button-mic'), {className : "bx-messenger-call-overlay-button-mic"}, true);
			if (icon)
				BX.toggleClass(icon, 'bx-messenger-call-overlay-button-mic-off');
		}, this));
		BX.desktop.addCustomEvent("bxCallAnswer", BX.delegate(function(chatId, userId, video, callToGroup) {
			BX.desktop.windowCommand("show");
			BX.desktop.changeTab('im');
			this.webrtc.callActive = true;
			BX.ajax({
				url: '/bitrix/components/bitrix/im.messenger/call.ajax.php?CALL_ANSWER',
				method: 'POST',
				dataType: 'json',
				timeout: 30,
				data: {'IM_CALL' : 'Y', 'COMMAND': 'answer', 'CHAT_ID': chatId, 'CALL_TO_GROUP': callToGroup? 'Y': 'N', 'RECIPIENT_ID' : this.callUserId, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
				onsuccess: BX.delegate(function(){
					this.webrtc.callDialog();
				}, this)
			});
		}, this));
		BX.desktop.addCustomEvent("bxCallJoin", BX.delegate(function(chatId, userId, video, callToGroup) {
			BX.desktop.windowCommand("show");
			BX.desktop.changeTab('im');
			this.webrtc.callAbort();
			this.webrtc.callOverlayClose(false);
			this.webrtc.callInvite(callToGroup? 'chat'+chatId: userId, video);
		}, this));

		BX.desktop.addCustomEvent("bxImClearHistory", BX.delegate(function(userId) {
			this.messenger.history[userId] = [];
			this.messenger.showMessage[userId] = [];

			if (this.BXIM.init)
				this.messenger.drawTab(userId);
		}, this));
		BX.desktop.addCustomEvent("bxSaveSettings", BX.delegate(function(settings) {
			this.BXIM.settings = settings;
			if (this.BXIM.messenger != null)
			{
				this.BXIM.messenger.userListRedraw(true);
				if (this.BXIM.messenger.popupMessengerTextareaSendType)
					this.BXIM.messenger.popupMessengerTextareaSendType.innerHTML = this.BXIM.settings.sendByEnter? 'Enter': (BX.browser.IsMac()? "&#8984;+Enter": "Ctrl+Enter");
			}
		}, this));
		BX.desktop.addCustomEvent("bxImClickConfirmNotify", BX.delegate(function(notifyId) {
			delete this.BXIM.notify.notify[notifyId];
			delete this.BXIM.notify.unreadNotify[notifyId];
			delete this.BXIM.notify.flashNotify[notifyId];
			this.BXIM.notify.updateNotifyCount(false);
			if (this.BXIM.openNotify)
				this.BXIM.notify.openNotify(true, true);
		}, this));
		//BX.desktop.addCustomEvent("BXLoginSuccess", BX.delegate(function (){
		//	this.messenger.contactListLoad = false;
		//	this.messenger.contactListGetFromServer();
		//}, this));

		BX.desktop.addCustomEvent("BXDockAction", BX.delegate(this.onTrayAction, this));
		BX.desktop.addCustomEvent("BXTrayAction", BX.delegate(this.onTrayAction, this));

		BX.desktop.addCustomEvent("BXWakeAction", BX.delegate(this.onWakeAction, this));

		BX.desktop.addCustomEvent("BXForegroundChanged", BX.delegate(function(focus)
		{
			clearTimeout(this.BXIM.windowFocusTimeout);
			this.BXIM.windowFocusTimeout = setTimeout(BX.delegate(function(){
				this.BXIM.changeFocus(focus);
				if (this.BXIM.isFocus() && this.messenger.unreadMessage[this.messenger.currentTab] && this.messenger.unreadMessage[this.messenger.currentTab].length>0)
					this.messenger.readMessage(this.messenger.currentTab);

				if (this.BXIM.isFocus('notify'))
				{
					if (this.notify.unreadNotifyLoad)
						this.notify.loadNotify();
					else if (this.notify.notifyUpdateCount > 0)
						this.notify.viewNotifyAll();
				}
				if (focus)
				{
					this.closeCallFloatDialog();
				}
				else
				{
					this.openCallFloatDialog();
				}
			}, this), focus? 500: 0);
		}, this));

		BX.bind(window, "blur", BX.delegate(function(){
			this.openCallFloatDialog();
		}, this));
		BX.bind(window, "focus", BX.delegate(function(){
			this.closeCallFloatDialog();
		}, this));

		BX.desktop.addCustomEvent("BXTrayMenu", BX.delegate(function (){
			var lFcounter = BXIM.notify.getCounter('**');
			var notifyCounter = BXIM.notify.getCounter('im_notify');
			var messengerCounter = BXIM.notify.getCounter('im_message');

			BX.desktop.addTrayMenuItem({Id: "messenger", Order: 100,Title: (BX.message('IM_DESKTOP_OPEN_MESSENGER') || '').replace('#COUNTER#', (messengerCounter>0? '('+messengerCounter+')':'')), Callback: function(){
				BX.desktop.windowCommand("show");
				BX.desktop.changeTab('im');
				BXIM.messenger.openMessenger(BXIM.messenger.currentTab);
			},Default: true	});

			BX.desktop.addTrayMenuItem({Id: "notify",Order: 120,Title: (BX.message('IM_DESKTOP_OPEN_NOTIFY') || '').replace('#COUNTER#', (notifyCounter>0? '('+notifyCounter+')':'')), Callback: function(){
				BX.desktop.windowCommand("show");
				BX.desktop.changeTab('notify');
				BXIM.notify.openNotify(false, true);
			}});
			BX.desktop.addTrayMenuItem({Id: "bdisk",Order: 130, Title: BX.message('IM_DESKTOP_BDISK'), Callback: function(){
				if (BX.desktop.diskAttachStatus())
				{
					BX.desktop.diskOpenFolder();
				}
				else
				{
					BX.desktop.windowCommand("show");
					BX.desktop.changeTab('disk');
				}
			}});
			BX.desktop.addTrayMenuItem({Id: "site",Order: 140, Title: (BX.message('IM_DESKTOP_GO_SITE') || '').replace('#COUNTER#', (lFcounter>0? '('+lFcounter+')':'')), Callback: function(){
				BX.desktop.browse(BX.desktop.getCurrentUrl());
			}});
			BX.desktop.addTrayMenuItem({Id: "separator1",IsSeparator: true, Order: 150});
			BX.desktop.addTrayMenuItem({Id: "settings",Order: 160, Title: BX.message('IM_DESKTOP_SETTINGS'), Callback: function(){
				BXIM.openSettings();
			}});
			BX.desktop.addTrayMenuItem({Id: "separator2",IsSeparator: true,Order: 1000});
			BX.desktop.addTrayMenuItem({Id: "logout",Order: 1010, Title: BX.message('IM_DESKTOP_LOGOUT'),Callback: function(){ BX.desktop.logout() }});
		}, this));
		BX.desktop.addCustomEvent("BXProtocolUrl", BX.delegate(function(command, params) {
			params = params? params: {}
			if (params.bitrix24net && params.bitrix24net == 'Y' && !this.BXIM.bitrix24net)
				return false;

			BX.desktop.setActiveWindow();

			if (command == 'messenger')
			{
				if (params.dialog)
				{
					this.BXIM.openMessenger(params.dialog);
				}
				else if (params.chat)
				{
					this.BXIM.openMessenger('chat'+params.chat);
				}
				else
				{
					this.BXIM.openMessenger();
				}
				BX.desktop.windowCommand("show");
			}
			else if (command == 'chat' && params.id)
			{
				this.BXIM.openMessenger('chat'+params.id);
				BX.desktop.windowCommand("show");
			}
			else if (command == 'notify')
			{
				this.BXIM.openNotify();
				BX.desktop.windowCommand("show");
			}
			else if (command == 'history' && params.user)
			{
				if (params.dialog)
				{
					this.BXIM.openHistory(params.dialog);
				}
				else if (params.chat)
				{
					this.BXIM.openHistory('chat'+params.chat);
				}
				BX.desktop.windowCommand("show");
			}
			else if (command == 'callto')
			{
				if (params.video)
				{
					this.BXIM.callTo(params.video, true);
				}
				else if (params.audio)
				{
					this.BXIM.callTo(params.audio, false);
				}
				else if (params.phone)
				{
					if (params.params)
					{
						var phoneParams = {};
						params.params = params.params.split('!!');
						var lastParam = '';
						var lastTypeParam = true;
						for (var i = 0; i < params.params.length; i++)
						{
							if (lastTypeParam)
							{
								lastParam = params.params[i];
								lastTypeParam = false;
							}
							else
							{
								lastTypeParam = true;
								phoneParams[lastParam] = params.params[i];
							}
						}
						this.webrtc.phoneCall(unescape(params.phone), phoneParams);
					}
					else
					{
						this.BXIM.phoneTo(unescape(params.phone));
					}
				}
				BX.desktop.windowCommand("show");
			}
		}, this));

		BX.addCustomEvent("onPullEvent-webdav", function(command,params)
		{
			BX.desktop.diskReportStorageNotification(command, params);
		});
		BX.addCustomEvent("onPullEvent-main", BX.delegate(function(command,params)
		{
			if (command == 'user_counter' && params[BX.message('SITE_ID')])
			{
				if (params[BX.message('SITE_ID')]['**'])
				{
					var lfCounter = parseInt(params[BX.message('SITE_ID')]['**']);
					this.notify.updateNotifyCounters({'**':lfCounter});
				}
			}
		}, this));
	}
};

BX.IM.Desktop.prototype.run = function()
{
	return typeof(BX.desktop) != 'undefined';
};

BX.IM.Desktop.prototype.ready = function()
{
	return typeof(BX.desktop) != 'undefined' && BX.desktop.ready();
};
BX.IM.Desktop.prototype.getCurrentUrl = function()
{
	if (!this.run()) return false;
	return BX.desktop.getCurrentUrl();
}
BX.IM.Desktop.prototype.enableInVersion = function(version)
{
	if (!this.run()) return false;
	return BX.desktop.enableInVersion(version);
}
BX.IM.Desktop.prototype.addCustomEvent = function(eventName, eventHandler)
{
	if (!this.run()) return false;
	BX.desktop.addCustomEvent(eventName, eventHandler);
}
BX.IM.Desktop.prototype.onCustomEvent = function(windowTarget, eventName, arEventParams)
{
	if (!this.run()) return false;
	BX.desktop.addCustomEvent(windowTarget, eventName, arEventParams);
};

BX.IM.Desktop.prototype.windowCommand = function(command, currentWindow)
{
	if (!this.run()) return false;

	if (typeof(currentWindow) == "undefined")
		BX.desktop.windowCommand(command)
	else
		BX.desktop.windowCommand(currentWindow, command)
};

BX.IM.Desktop.prototype.browse = function(url)
{
	if (!this.run()) return false;
	BX.desktop.browse(url);
};

BX.IM.Desktop.prototype.drawOnPlaceholder = function(content)
{
	if (this.markup == null || !BX.type.isDomNode(content)) return false;

	this.markup.innerHTML = '';
	this.markup.appendChild(content);
};

BX.IM.Desktop.prototype.openNewNotify = function(notifyId, content, js)
{
	if (!this.ready()) return;
	if (content == "") return false;

	if (this.showNotifyId[notifyId])
		return false;

	this.showNotifyId[notifyId] = true;

	var sendNotify = {};
	sendNotify[notifyId] = this.BXIM.notify.notify[notifyId];

	BXDesktopSystem.ExecuteCommand('notification.show.html', this.getHtmlPage(content, js, {'notify' : sendNotify}, 'im-notify-popup'));
};

BX.IM.Desktop.prototype.openNewMessage = function(messageId, content, js)
{
	if (!this.ready()) return;
	if (content == "") return false;

	if (this.showMessageId[messageId])
		return false;

	this.showMessageId[messageId] = true;

	BXDesktopSystem.ExecuteCommand('notification.show.html', this.getHtmlPage(content, js, true, 'im-notify-popup'));
};

BX.IM.Desktop.prototype.adjustSize = function()
{
	if (!this.ready() || !this.BXIM.init  || !this.BXIM.messenger || !this.BXIM.notify) return false;

	if (window.innerWidth < BX.desktop.minWidth || window.innerHeight < BX.desktop.minHeight)
		return false;

	var newHeight = document.body.offsetHeight-this.initHeight;
	this.initHeight = document.body.offsetHeight;

	this.BXIM.messenger.popupMessengerBodySize = Math.max(this.BXIM.messenger.popupMessengerBodySize+newHeight, 295-(this.BXIM.messenger.popupMessengerTextareaSize-43));
	if (this.BXIM.messenger.popupMessengerBody != null)
	{
		this.BXIM.messenger.popupMessengerBody.style.height = this.BXIM.messenger.popupMessengerBodySize+'px';
		this.BXIM.messenger.redrawChatHeader();
	}

	this.BXIM.messenger.popupContactListElementsSize = Math.max(this.BXIM.messenger.popupContactListElementsSize+newHeight, 319);
	if (this.BXIM.messenger.popupContactListElements != null)
		this.BXIM.messenger.popupContactListElements.style.height = this.BXIM.messenger.popupContactListElementsSize+'px';

	this.BXIM.messenger.popupMessengerFullHeight = document.body.offsetHeight;
	if (this.BXIM.messenger.popupMessengerExtra != null)
		this.BXIM.messenger.popupMessengerExtra.style.height = this.BXIM.messenger.popupMessengerFullHeight+'px';

	this.BXIM.notify.popupNotifySize = Math.max(this.BXIM.notify.popupNotifySize+newHeight, 383);
	if (this.BXIM.notify.popupNotifyItem != null)
		this.BXIM.notify.popupNotifyItem.style.height = this.BXIM.notify.popupNotifySize+'px';

	if (this.BXIM.webrtc.callOverlay)
	{
		this.BXIM.webrtc.callOverlay.style.transition = 'none';
		this.BXIM.webrtc.callOverlay.style.width = (this.BXIM.messenger.popupMessengerExtra.style.display == "block"? this.BXIM.messenger.popupMessengerExtra.offsetWidth-1: this.BXIM.messenger.popupMessengerDialog.offsetWidth-1)+'px';
		this.BXIM.webrtc.callOverlay.style.height = (this.BXIM.messenger.popupMessengerFullHeight-1)+'px';
	}

	this.BXIM.messenger.closeMenuPopup();

	clearTimeout(this.BXIM.adjustSizeTimeout);
	this.BXIM.adjustSizeTimeout = setTimeout(BX.delegate(function(){
		this.BXIM.setLocalConfig('msz3', {
			'wz': this.BXIM.messenger.popupMessengerFullWidth,
			'ta': this.BXIM.messenger.popupMessengerTextareaSize,
			'b': this.BXIM.messenger.popupMessengerBodySize,
			'cl': this.BXIM.messenger.popupContactListSize,
			'hi': this.BXIM.messenger.popupHistoryItemsSize,
			'fz': this.BXIM.messenger.popupMessengerFullHeight,
			'ez': this.BXIM.messenger.popupContactListElementsSize,
			'nz': this.BXIM.notify.popupNotifySize,
			'hf': this.BXIM.messenger.popupHistoryFilterVisible,
			'dw': window.innerWidth,
			'dh': window.innerHeight,
			'place': 'desktop'
		});
		if (this.BXIM.webrtc.callOverlay)
			this.BXIM.webrtc.callOverlay.style.transition = '';
	}, this), 500);


	return true;
};

BX.IM.Desktop.prototype.autoResize = function(window)
{
	if (!this.ready()) return;

	BX.desktop.resize();
};

BX.IM.Desktop.prototype.openSettings = function(content, js, params)
{
	if (!this.ready()) return false;
	params = params || {};

	if(params.minSettingsWidth)
		this.minSettingsWidth = params.minSettingsWidth;

	if(params.minSettingsHeight)
		this.minSettingsHeight = params.minSettingsHeight;

	BX.desktop.createWindow("settings", BX.delegate(function(settings) {
		settings.SetProperty("clientSize", { Width: this.minSettingsWidth, Height: this.minSettingsHeight });
		settings.SetProperty("resizable", false);
		settings.SetProperty("title", BX.message('IM_SETTINGS'));
		settings.ExecuteCommand("html.load", this.getHtmlPage(content, js, {}));
	},this));
};

BX.IM.Desktop.prototype.openHistory = function(userId, content, js)
{
	if (!this.ready()) return false;

	BX.desktop.createWindow("history", BX.delegate(function(history)
	{
		var data = {'chat':{}, 'users':{}};
		if (userId.toString().substr(0,4) == 'chat')
		{
			var chatId = userId.substr(4);
			data['chat'][chatId] = this.messenger.chat[chatId];
			for (var i = 0; i < this.messenger.userInChat[chatId].length; i++)
				data['users'][this.messenger.userInChat[chatId][i]] = this.messenger.users[this.messenger.userInChat[chatId][i]];
		}
		else
		{
			data['users'][userId] = this.messenger.users[userId];
			data['users'][this.BXIM.userId] = this.messenger.users[this.BXIM.userId];
		}
		history.SetProperty("clientSize", { Width: this.minHistoryWidth, Height: this.minHistoryHeight });
		history.SetProperty("minClientSize", { Width: this.minHistoryWidth, Height: this.minHistoryHeight });
		history.SetProperty("resizable", false);
		history.ExecuteCommand("html.load", this.getHtmlPage(content, js, data));
		history.SetProperty("title", BX.message('IM_M_HISTORY'));
	},this));
};

BX.IM.Desktop.prototype.openCallFloatDialog = function()
{
	if (!this.enableInVersion(20) || !this.BXIM.init || !this.ready() || !this.webrtc.callActive || this.topmostWindow)
		return false;

	if (this.webrtc.callVideo && !this.webrtc.callStreamMain)
		return false;

	if (!this.webrtc.callOverlayTitleBlock)
		return false;

	this.openTopmostWindow("callFloatDialog", 'BXIM.webrtc.callFloatDialog("'+this.webrtc.callOverlayTitleBlock.innerHTML+'", "'+(this.webrtc.callVideo? this.webrtc.callOverlayVideoMain.src: '')+'", '+(this.webrtc.audioMuted?1:0)+')', {}, 'im-desktop-call');
};

BX.IM.Desktop.prototype.closeCallFloatDialog = function()
{
	if (!this.enableInVersion(20) || !this.ready() || !this.topmostWindow)
		return false;

	if (this.webrtc.callActive)
	{
		if (this.webrtc.callOverlayUserId > 0 && this.webrtc.callOverlayUserId == this.messenger.currentTab)
		{
			this.closeTopmostWindow();
		}
		else if (this.webrtc.callOverlayChatId > 0 && this.webrtc.callOverlayChatId == this.messenger.currentTab.toString().substr(4))
		{
			this.closeTopmostWindow();
		}
	}
	else
	{
		this.closeTopmostWindow();
	}
}

BX.IM.Desktop.prototype.openTopmostWindow = function(name, js, initJs, bodyClass)
{
	if (!this.ready())
		return false;

	this.closeTopmostWindow();

	clearTimeout(this.topmostWindowTimeout);
	this.topmostWindowTimeout = setTimeout(BX.delegate(function(){
		if (this.topmostWindow)
			return false;

		this.topmostWindow = BXDesktopSystem.ExecuteCommand('topmost.show.html', this.getHtmlPage("", js, initJs, bodyClass));
	}, this), 500);
};

BX.IM.Desktop.prototype.closeTopmostWindow = function()
{
	clearTimeout(this.topmostWindowTimeout);
	clearTimeout(this.topmostWindowCloseTimeout);

	if (!this.topmostWindow)
		return false;

	if (this.topmostWindow.document && this.topmostWindow.document.title.length > 0)
		BX.desktop.windowCommand(this.topmostWindow, "hide");

	this.topmostWindowCloseTimeout = setTimeout(BX.delegate(function(){
		if (this.topmostWindow)
		{
			if (this.topmostWindow.document && this.topmostWindow.document.title.length > 0)
			{
				BX.desktop.windowCommand(this.topmostWindow, "close");
				this.topmostWindow = null;
			}
			else
			{
				this.closeTopmostWindow();
			}
		}
	}, this), 300);
}

BX.IM.Desktop.prototype.getHtmlPage = function(content, jsContent, initImJs, bodyClass)
{
	if (!this.ready()) return;

	content = content || '';
	jsContent = jsContent || '';
	bodyClass = bodyClass || '';

	var initImConfig = typeof(initImJs) == "undefined" || typeof(initImJs) != "object"? {}: initImJs;
	initImJs = typeof(initImJs) != "undefined";
	if (this.htmlWrapperHead == null)
		this.htmlWrapperHead = document.head.outerHTML.replace(/BX\.PULL\.start\([^)]*\);/g, '');

	if (content != '' && BX.type.isDomNode(content))
		content = content.outerHTML;

	if (jsContent != '' && BX.type.isDomNode(jsContent))
		jsContent = jsContent.outerHTML;

	if (jsContent != '')
		jsContent = '<script type="text/javascript">BX.ready(function(){'+jsContent+'});</script>';

	var initJs = '';
	if (initImJs == true)
	{
		initJs = "<script type=\"text/javascript\">"+
			"BX.ready(function() {"+
				"BXIM = new BX.IM(null, {"+
					"'init': false,"+
					"'settings' : "+JSON.stringify(this.BXIM.settings)+","+
					"'settingsView' : "+JSON.stringify(this.BXIM.settingsView)+","+
					"'updateStateInterval': "+this.BXIM.updateStateInterval+","+
					"'desktop': "+this.run()+","+
					"'ppStatus': false,"+
					"'ppServerStatus': false,"+
					"'xmppStatus': "+this.BXIM.xmppStatus+","+
					"'bitrixNetworkStatus': "+this.BXIM.bitrixNetworkStatus+","+
					"'bitrix24Status': "+this.BXIM.bitrix24Status+","+
					"'bitrixIntranet': "+this.BXIM.bitrixIntranet+","+
					"'bitrixXmpp': "+this.BXIM.bitrixXmpp+","+
					"'notify' : "+(initImConfig.notify? JSON.stringify(initImConfig.notify): '{}')+","+
					"'users' : "+(initImConfig.users? JSON.stringify(initImConfig.users): '{}')+","+
					"'chat' : "+(initImConfig.chat? JSON.stringify(initImConfig.chat): '{}')+","+
					"'userInChat' : "+(initImConfig.userInChat? JSON.stringify(initImConfig.userInChat): '{}')+","+
					"'hrphoto' : "+(initImConfig.hrphoto? JSON.stringify(initImConfig.hrphoto): '{}')+","+
					"'phoneCrm' : "+(initImConfig.phoneCrm? JSON.stringify(initImConfig.phoneCrm): '{}')+","+
					"'userId': "+this.BXIM.userId+","+
					"'userEmail': '"+this.BXIM.userEmail+"',"+
					"'path' : "+JSON.stringify(this.BXIM.path)+
				"});"+
			"});"+
		"</script>";
	}
	return '<!DOCTYPE html><html>'+this.htmlWrapperHead+'<body class="im-desktop im-desktop-popup '+bodyClass+'"><div id="placeholder-messanger">'+content+'</div>'+initJs+jsContent+'</body></html>';
};

BX.IM.Desktop.prototype.onWakeAction = function ()
{
	BX.desktop.openConfirm('<div class="bx-desktop-reconnect">'+BX.message('IM_DESKTOP_RECONNECT')+'</div>', false);
	BX.desktop.setIconStatus('offline');

	BX.desktop.checkInternetConnection(function()
	{
		BX.desktop.windowReload();
	},
	BX.delegate(function()
	{
		BX.desktop.login();
	}, this), 10)
}
BX.IM.Desktop.prototype.onTrayAction = function ()
{
	BX.desktop.windowCommand("show");
	var messengerCounter = this.BXIM.notify.getCounter('im_message');
	var notifyCounter = this.BXIM.notify.getCounter('im_notify');
	if (messengerCounter > 0)
	{
		if (this.BXIM.notifyOpen == true && notifyCounter > 0)
		{
			BX.desktop.changeTab('notify');
			this.BXIM.notify.openNotify(false, true);
			this.BXIM.messenger.popupContactListSearchInput.focus();
		}
		else
		{
			BX.desktop.changeTab('im');
			this.BXIM.messenger.openMessenger();
			this.BXIM.messenger.popupMessengerTextarea.focus();
		}
	}
	else if (notifyCounter > 0)
	{
		BX.desktop.changeTab('notify');
		this.BXIM.notify.openNotify(false, true);
		this.BXIM.messenger.popupContactListSearchInput.focus();
	}
	else if (this.BXIM.messenger.popupMessengerTextarea)
	{
		BX.desktop.changeTab('im');
		this.BXIM.messenger.popupMessengerTextarea.focus();
	}
};
BX.IM.Desktop.prototype.birthdayStatus = function(value)
{
	if (!this.ready()) return false;

	if (typeof(value) !='boolean')
	{
		return this.BXIM.getLocalConfig('birthdayStatus', true);
	}
	else
	{
		this.BXIM.setLocalConfig('birthdayStatus', value);
		return value;
	}
};

BX.IM.Desktop.prototype.changeTab = function(currentTab)
{
	return false;
};

BX.PopupWindowDesktop = function()
{
	this.closeByEsc = true;
	this.setClosingByEsc = function(enable) { this.closeByEsc = enable; };
	this.close = function(){ BX.desktop.windowCommand('close'); };
	this.destroy = function(){ BX.desktop.windowCommand('close'); };
};

/* WebRTC */
BX.IM.WebRTC = function(BXIM, params)
{
	this.BXIM = BXIM;
	this.screenSharingEnabled = false;

	this.panel = params.panel;
	this.desktop = params.desktopClass;

	this.callScreen = false;
	this.callToPhone = false;
	this.callOverlayFullScreen = false;

	this.callInviteTimeout = null;
	this.callNotify = null;
	this.callAllowTimeout = null;
	this.callDialogAllow = null;
	this.callOverlay = null;
	this.callOverlayMinimize = null;
	this.callOverlayChatId = 0;
	this.callOverlayUserId = 0;
	this.callSelfDisabled = false;
	this.callOverlayPhotoSelf = null;
	this.callOverlayPhotoUsers = {};
	this.callOverlayVideoUsers = {};
	this.callOverlayVideoPhotoUsers = {};
	this.callOverlayOptions = {};
	this.callOverlayPhotoCompanion = null;
	this.callOverlayPhotoMini = null;
	this.callOverlayVideoMain = null;
	this.callOverlayVideoSelf = null;
	this.callOverlayProgressBlock = null;
	this.callOverlayStatusBlock = null;
	this.callOverlayButtonsBlock = null;

	this.setTurnServer(params)

	this.phoneEnabled = params.phoneEnabled;
	this.phoneAvailable = params.phoneAvailable;
	this.phoneCallerID = '';
	this.phoneLogin = '';
	this.phoneAccount = '';
	this.phoneCheckBalance = false;
	this.phoneCallHistory = {};

	this.phoneSDKinit = false;
	this.phoneMicAccess = false;
	this.phoneIncoming = false;
	this.phoneCallId = '';
	this.phoneNumber = '';
	this.phoneNumberUser = '';
	this.phoneApplication = '';
	this.phoneParams = {};
	this.phoneAPI = null;
	this.phoneCurrentCall = null;
	this.phoneCrm = params.phoneCrm? params.phoneCrm: {};
	this.phoneMicMuted = false;
	this.phoneRinging = 0;

	this.debug = false;

	this.configVideoMobile = {
		maxWidth: 640,
		maxHeight: 480
	};

	//if (this.detectedBrowser == 'chrome' && location.protocol == 'https:' && !this.desktop.ready())
	//	this.screenSharingEnabled = true;

	if (this.ready())
	{
		this.initAudio();

		BX.addCustomEvent("onPullEvent-im", BX.delegate(function(command,params)
		{
			if (command == 'call')
			{
				this.log('Incoming', params.command, params.senderId, JSON.stringify(params));

				if (params.command == 'join')
				{
					for (var i in params.users)
						this.messenger.users[i] = params.users[i];

					for (var i in params.hrphoto)
						this.messenger.hrphoto[i] = params.hrphoto[i];

					if (this.callInit || this.callActive)
					{
						setTimeout(BX.delegate(function(){
							BX.ajax({
								url: '/bitrix/components/bitrix/im.messenger/call.ajax.php?CALL_BUSY',
								method: 'POST',
								dataType: 'json',
								timeout: 30,
								data: {'IM_CALL' : 'Y', 'COMMAND': 'busy', 'CHAT_ID': params.chatId, 'RECIPIENT_ID' : params.senderId, 'VIDEO': params.video? 'Y': 'N', 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()}
							});
						}, this), params.callToGroup? 1000: 0);
					}
					else
					{
						if (this.desktop.ready() || !this.desktop.ready() && !this.BXIM.desktopStatus)
						{
							this.messenger.openMessenger('chat'+params.chatId);
							this.BXIM.repeatSound('ringtone', 5000);
							this.callNotifyWait(params.chatId, params.senderId, params.video, params.callToGroup, true);
						}
						if (this.desktop.ready() && !this.BXIM.windowFocus)
						{
							var data = {'users' : {}, 'chat' : {}, 'userInChat' : {}, 'hrphoto' : {}};
							if (params.callToGroup)
							{
								data['chat'][params.chatId] = this.messenger.chat[params.chatId];
								data['userInChat'][params.chatId] = this.messenger.userInChat[params.chatId];
							}
							for (var i = 0; i < this.messenger.userInChat[params.chatId].length; i++)
							{
								data['users'][this.messenger.userInChat[params.chatId][i]] = this.messenger.users[this.messenger.userInChat[params.chatId][i]];
								data['hrphoto'][this.messenger.userInChat[params.chatId][i]] = this.messenger.hrphoto[this.messenger.userInChat[params.chatId][i]];
							}
							this.desktop.openTopmostWindow("callNotifyWaitDesktop", "BXIM.webrtc.callNotifyWaitDesktop("+params.chatId+","+params.senderId+", "+(params.video?1:0)+", "+(params.callToGroup?1:0)+", true);", data, 'im-desktop-call');
						}
					}
				}
				else if (params.command == 'invite' || params.command == 'invite_join')
				{
					for (var i in params.users)
						this.messenger.users[i] = params.users[i];

					for (var i in params.hrphoto)
						this.messenger.hrphoto[i] = params.hrphoto[i];

					for (var i in params.chat)
						this.messenger.chat[i] = params.chat[i];

					for (var i in params.userInChat)
						this.messenger.userInChat[i] = params.userInChat[i];

					if (this.callInit || this.callActive)
					{
						if (params.command == 'invite')
						{
							if (this.callChatId == params.chatId)
							{
								this.callCommand(params.chatId, 'busy_self');
								this.callOverlayClose(false);
							}
							else
							{
								setTimeout(BX.delegate(function(){
									BX.ajax({
										url: '/bitrix/components/bitrix/im.messenger/call.ajax.php?CALL_BUSY',
										method: 'POST',
										dataType: 'json',
										timeout: 30,
										data: {'IM_CALL' : 'Y', 'COMMAND': 'busy', 'CHAT_ID': params.chatId, 'RECIPIENT_ID' : params.senderId, 'VIDEO': params.video? 'Y': 'N', 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()}
									});
								}, this), params.callToGroup? 1000: 0);
							}
						}
						else if (this.initiator && this.callChatId == params.chatId)
						{
							this.callDialog();
							BX.ajax({
								url: '/bitrix/components/bitrix/im.messenger/call.ajax.php?CALL_ANSWER',
								method: 'POST',
								dataType: 'json',
								timeout: 30,
								data: {'IM_CALL' : 'Y', 'COMMAND': 'answer', 'CHAT_ID': this.callChatId, 'CALL_TO_GROUP': this.callToGroup? 'Y': 'N',  'RECIPIENT_ID' : this.callUserId, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()}
							});
						}
					}
					else
					{
						if (this.desktop.ready() || !this.desktop.ready() && !this.BXIM.desktopStatus || this.desktop.run() && !this.desktop.ready() && this.BXIM.desktopStatus)
						{
							this.BXIM.repeatSound('ringtone', 5000);
							this.callCommand(params.chatId, 'wait');
							if (this.desktop.run())
								BX.desktop.changeTab('im');

							this.callNotifyWait(params.chatId, params.senderId, params.video, params.callToGroup);
						}
						if (this.desktop.ready() && !this.BXIM.isFocus('all'))
						{
							var data = {'users' : {}, 'chat' : {}, 'userInChat' : {}, 'hrphoto' : {}};
							if (params.callToGroup)
							{
								data['chat'][params.chatId] = this.messenger.chat[params.chatId];
								data['userInChat'][params.chatId] = this.messenger.userInChat[params.chatId];
							}
							for (var i = 0; i < this.messenger.userInChat[params.chatId].length; i++)
							{
								data['users'][this.messenger.userInChat[params.chatId][i]] = this.messenger.users[this.messenger.userInChat[params.chatId][i]];
								data['hrphoto'][this.messenger.userInChat[params.chatId][i]] = this.messenger.hrphoto[this.messenger.userInChat[params.chatId][i]];
							}
							this.desktop.openTopmostWindow("callNotifyWaitDesktop", "BXIM.webrtc.callNotifyWaitDesktop("+params.chatId+","+params.senderId+", "+(params.video?1:0)+", "+(params.callToGroup?1:0)+");", data, 'im-desktop-call');
						}
					}
				}
				else if (this.callInit && this.callChatId == params.lastChatId && params.command == 'invite_user')
				{
					for (var i in params.users)
						this.messenger.users[i] = params.users[i];

					for (var i in params.hrphoto)
						this.messenger.hrphoto[i] = params.hrphoto[i];

					this.callChatId = params.chatId;
					this.callGroupOverlayRedraw();
				}
				else if (!this.callActive && this.callInit && this.callChatId == params.chatId && params.command == 'wait')
				{
					clearTimeout(this.callDialtoneTimeout);
					this.callDialtoneTimeout = setTimeout(BX.delegate(function(){
						this.BXIM.repeatSound('dialtone', 5000);
					}, this), 2000);

					this.callWait(params.senderId);
				}
				else if (this.initiator && this.callChatId == params.chatId && params.command == 'answer')
				{
					this.callDialog();
				}
				else if (params.command == 'ready')
				{
					if (this.callActive && this.callStreamSelf == null)
					{
						clearTimeout(this.callAllowTimeout);
						this.callAllowTimeout = setTimeout(BX.delegate(function(){
							this.callOverlayProgress('offline');
							this.callCommand(this.callChatId, 'errorAccess');
							this.callOverlayButtons([{
								text: BX.message('IM_M_CALL_BTN_CLOSE'),
								className: 'bx-messenger-call-overlay-button-close',
								events: {
									click : BX.delegate(function() {
										this.callOverlayClose();
									}, this)
								}
							}]);
							this.callAbort(BX.message('IM_M_CALL_ST_NO_ACCESS_3'));
						}, this), 60000);
					}
					this.log('Apponent '+params.senderId+' ready!');
					this.connected[params.senderId] = true;
				}
				else if (this.callActive && this.callChatId == params.chatId &&  params.command == 'errorAccess' && (!params.callToGroup || params.closeConnect))
				{
					this.callOverlayProgress('offline');
					this.callOverlayStatus(BX.message('IM_M_CALL_ST_NO_ACCESS_2'));
					this.callOverlayButtons([
						{
							text: BX.message('IM_M_CALL_BTN_CLOSE'),
							className: 'bx-messenger-call-overlay-button-close',
							events: {
								click : BX.delegate(function() {
									this.callOverlayClose();
								}, this)
							}
						}
					]);
					this.callAbort(BX.message('IM_M_CALL_ST_NO_ACCESS_2'));
				}
				else if (this.callActive && this.callChatId == params.chatId  && params.command == 'reconnect')
				{
					clearTimeout(this.pcConnectTimeout[params.senderId]);
					clearTimeout(this.initPeerConnectionTimeout[params.senderId]);

					if (this.pc[params.senderId])
						this.pc[params.senderId].close();

					delete this.pc[params.senderId];
					delete this.pcStart[params.senderId];

					if (this.callStreamMain == this.callStreamUsers[params.senderId])
						this.callStreamMain = null;
					this.callStreamUsers[params.senderId] = null;

					this.initPeerConnection(params.senderId);
				}
				else if (this.callActive && this.callChatId == params.chatId  && params.command == 'signaling')
				{
					this.signalingPeerData(params.senderId, params.peer);
				}
				else if (this.callInit && this.callChatId == params.chatId  && params.command == 'waitTimeout' && (!params.callToGroup || params.closeConnect))
				{
					this.callAbort();
					this.callOverlayClose();
				}
				else if (this.callInit && this.callChatId == params.chatId  && (params.command == 'busy_self' || params.command == 'callToPhone'))
				{
					this.callAbort();
					this.callOverlayClose();
				}
				else if (this.callInit && this.callChatId == params.chatId  && params.command == 'busy' && (!params.callToGroup || params.closeConnect))
				{
					this.callOverlayProgress('offline');
					this.callOverlayButtons([
						{
							text: BX.message('IM_M_CALL_BTN_RECALL'),
							className: 'bx-messenger-call-overlay-button-recall',
							events: {
								click : BX.delegate(function() {
									this.callInvite(params.senderId, params.video);
								}, this)
							}
						},
						{
							text: BX.message('IM_M_CALL_BTN_HISTORY'),
							title: BX.message('IM_M_CALL_BTN_HISTORY_2'),
							showInMinimize: true,
							className: 'bx-messenger-call-overlay-button-history',
							events: { click : BX.delegate(function(){
								this.messenger.openHistory(this.messenger.currentTab);
							}, this) }
						},
						{
							text: BX.message('IM_M_CALL_BTN_CLOSE'),
							className: 'bx-messenger-call-overlay-button-close',
							events: {
								click : BX.delegate(function() {
									this.callOverlayClose();
								}, this)
							}
						}
					]);
					this.callAbort(BX.message('IM_M_CALL_ST_BUSY'));
				}
				else if (this.callInit && this.callChatId == params.chatId && params.command == 'decline' && (!params.callToGroup || params.closeConnect))
				{
					if (this.callInitUserId != this.BXIM.userId || this.callActive)
					{
						var callVideo = this.callVideo;
						this.callOverlayStatus(BX.message('IM_M_CALL_ST_DECLINE'));

						this.BXIM.playSound('stop');
						if (callVideo && this.callStreamSelf != null)
							this.callOverlayVideoClose();
						else
							this.callOverlayClose();
					}
					else if (this.callInitUserId == this.BXIM.userId)
					{
						this.callOverlayProgress('offline');
						this.callOverlayButtons([
							{
								text: BX.message('IM_M_CALL_BTN_CLOSE'),
								className: 'bx-messenger-call-overlay-button-close',
								events: {
									click : BX.delegate(function() {
										this.callOverlayClose();
									}, this)
								}
							}
						]);
						this.callAbort(BX.message('IM_M_CALL_ST_DECLINE'));
					}
					else
					{
						this.callAbort();
					}
				}
				else if ((params.command == 'decline_self' && this.callChatId == params.chatId || params.command == 'answer_self' && !this.callActive) && !this.callSelfDisabled)
				{
					this.BXIM.stopRepeatSound('ringtone');
					this.BXIM.stopRepeatSound('dialtone');

					this.callOverlayClose(true);
				}
				else if (this.callInit && params.callToGroup && this.callChatId == params.chatId && (params.command == 'errorAccess' || params.command == 'waitTimeout' || params.command == 'busy' || params.command == 'decline'))
				{
					var userId = this.callOverlayVideoMain.getAttribute('data-userId');
					if (userId == params.senderId)
					{
						var changeVideo = false;
						for (var i in this.callStreamUsers)
						{
							if (i == params.senderId)
								continue;

							this.callChangeMainVideo(i);
							changeVideo = true;
							break;
						}
						if (!changeVideo)
						{
							this.callStreamMain = null;
							this.callOverlayProgress('wait');
							this.callOverlayStatus(BX.message(this.callToGroup? 'IM_M_CALL_ST_WAIT_ACCESS_3':'IM_M_CALL_ST_WAIT_ACCESS_2'));
							BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-call-active');
							BX.removeClass(BXIM.webrtc.callOverlay, 'bx-messenger-call-overlay-call-video');
							BX.removeClass(this.callOverlayVideoUsers[userId].parentNode, 'bx-messenger-call-video-block-hide');
						}
					}
					BX.addClass(this.callOverlayVideoUsers[params.senderId].parentNode, 'bx-messenger-call-video-hide');
					this.connected[params.senderId] = false;
					this.callOverlayVideoUsers[params.senderId].src = '';
					this.pc[params.senderId] = null;
					delete this.pc[params.senderId];
					delete this.pcStart[params.senderId];
					if (this.callStreamUsers[params.senderId] && this.callStreamUsers[params.senderId].stop)
						this.callStreamUsers[params.senderId].stop();
					this.callStreamUsers[params.senderId] = null;
					delete this.callStreamUsers[params.senderId];
				}
				else
				{
					this.log('Command "'+params.command+'" skip (current chat: '+parseInt(this.callChatId)+'; command chat: '+parseInt(params.chatId));
				}
			}

		}, this));


		BX.addCustomEvent("onPullEvent-voximplant", BX.delegate(function(command,params)
		{
			if (command == 'invite')
			{
				if (!this.callInit && !this.callActive)
				{
					if (this.desktop.ready() || !this.desktop.ready() && !this.BXIM.desktopStatus || this.desktop.run() && !this.desktop.ready() && this.BXIM.desktopStatus)
					{
						if (params.CRM && params.CRM.FOUND)
						{
							this.phoneCrm = params.CRM;
						}
						this.BXIM.repeatSound('ringtone', 5000);
						this.phoneCommand('wait', {'CALL_ID' : params.callId});
						if (this.desktop.run())
							BX.desktop.changeTab('im');

						this.phoneNotifyWait(params.chatId, params.callId, params.callerId);
					}
					if (this.desktop.ready() && !this.BXIM.isFocus('all'))
					{
						var data = {'users' : {}, 'chat' : {}, 'userInChat' : {}, 'hrphoto' : {},  'phoneCrm': params.CRM};
						this.desktop.openTopmostWindow("callNotifyWaitDesktop", "BXIM.webrtc.phoneNotifyWaitDesktop("+params.chatId+",'"+params.callId+"', "+params.callerId+");", data, 'im-desktop-call');
					}
				}
			}
			else if (command == 'timeout' || command == 'answer_self')
			{
				if (this.phoneCallId == params.callId && !this.callSelfDisabled)
				{
					this.callInit = false;
					this.BXIM.stopRepeatSound('ringtone');
					this.phoneCallFinish();
					this.callAbort();
					this.callOverlayClose();
				}
			}
			else if (command == 'outgoing')
			{
				if (!this.phoneCallId && this.callInit && this.phoneNumber == params.phoneNumber)
				{
					this.phoneCallId = params.callId;
					this.phoneCrm = params.CRM;

					this.callOverlayDrawCrm();
					if (this.callNotify)
						this.callNotify.adjustPosition();
				}
			}
			else if (command == 'update_crm')
			{
				if (this.phoneCallId == params.callId && params.CRM && params.CRM.FOUND)
				{
					this.phoneCrm = params.CRM;

					this.callOverlayDrawCrm();
					if (this.callNotify)
						this.callNotify.adjustPosition();
				}
			}
		}, this));

		if (BX.browser.SupportLocalStorage())
		{
			BX.addCustomEvent(window, "onLocalStorageSet", BX.delegate(this.storageSet, this));
		}

		BX.garbage(function(){
			if (this.callInit && !this.callActive)
			{
				if (this.initiator)
				{
					this.callCommand(this.callChatId, 'decline', {'ACTIVE': this.callActive? 'Y': 'N', 'INITIATOR': this.initiator? 'Y': 'N'}, false);
					this.callAbort();
				}
				else
				{
					var calledUsers = {};
					for (var i in this.messenger.hrphoto)
						calledUsers[i] = this.messenger.users[i];

					BX.localStorage.set('mcr2', {
						'users': calledUsers,
						'hrphoto': this.messenger.hrphoto,
						'chat': this.messenger.chat,
						'userInChat': this.messenger.userInChat,
						'callChatId': this.callChatId,
						'callUserId': this.callUserId,
						'callVideo': this.callVideo,
						'callToGroup': this.callToGroup
					}, 5);
				}
			}
			if (this.callActive)
				this.callCommand(this.callChatId, 'errorAccess', {}, false);

			this.callOverlayClose();
		}, this);
	}
	else
	{
		if (this.BXIM.desktopStatus)
			return false;

		this.initAudio(true);
		BX.addCustomEvent("onPullEvent-im", BX.delegate(function(command,params) {
			if (params.command == 'call' && params.command == 'invite')
			{
				for (var i in params.users)
					this.messenger.users[i] = params.users[i];

				for (var i in params.hrphoto)
					this.messenger.hrphoto[i] = params.hrphoto[i];

				this.callOverlayShow({
					toUserId : this.BXIM.userId,
					fromUserId : params.senderId,
					callToGroup : this.callToGroup,
					video : params.video,
					progress : 'offline',
					minimize : false,
					status : this.desktop.ready()? BX.message('IM_M_CALL_ST_NO_WEBRTC_3'): BX.message('IM_M_CALL_ST_NO_WEBRTC_2'),
					buttons : [
						this.BXIM.platformName == ''? null: {
							text: BX.message('IM_M_CALL_BTN_DOWNLOAD'),
							className: 'bx-messenger-call-overlay-button-download',
							events: {
								click : BX.delegate(function() {
									window.open(BX.browser.IsMac()? "http://dl.bitrix24.com/b24/bitrix24_desktop.dmg": "http://dl.bitrix24.com/b24/bitrix24_desktop.exe", "desktopApp");
									this.callOverlayClose();
								}, this)
							}
						},
						{
							text: BX.message('IM_M_CALL_BTN_CLOSE'),
							className: 'bx-messenger-call-overlay-button-close',
							events: {
								click : BX.delegate(function() {
									this.callOverlayClose();
								}, this)
							}
						}
					]
				});
				this.callOverlayDeleteEvents({'closeNotify': false});
			}
		}, this));
	}
};
if (BX.inheritWebrtc)
	BX.inheritWebrtc(BX.IM.WebRTC);

BX.IM.WebRTC.prototype.initAudio = function(onlyError)
{
	if (onlyError === true)
	{
		this.panel.appendChild(this.BXIM.audio.error = BX.create("audio", { props : { className : "bx-messenger-audio" }, children : [
			BX.create("source", { attrs : { src : "/bitrix/js/im/audio/video-error.ogg", type : "audio/ogg; codecs=vorbis" }}),
			BX.create("source", { attrs : { src : "/bitrix/js/im/audio/video-error.mp3", type : "audio/mpeg" }})
		]}));

		return false;
	}

	this.panel.appendChild(this.BXIM.audio.dialtone = BX.create("audio", { props : { className : "bx-messenger-audio" }, children : [
		BX.create("source", { attrs : { src : "/bitrix/js/im/audio/video-dialtone.ogg", type : "audio/ogg; codecs=vorbis" }}),
		BX.create("source", { attrs : { src : "/bitrix/js/im/audio/video-dialtone.mp3", type : "audio/mpeg" }})
	]}));

	this.panel.appendChild(this.BXIM.audio.ringtone = BX.create("audio", { props : { className : "bx-messenger-audio" }, children : [
		BX.create("source", { attrs : { src : "/bitrix/js/im/audio/video-ringtone.ogg", type : "audio/ogg; codecs=vorbis" }}),
		BX.create("source", { attrs : { src : "/bitrix/js/im/audio/video-ringtone.mp3", type : "audio/mpeg" }})
	]}));

	this.panel.appendChild(this.BXIM.audio.start = BX.create("audio", { props : { className : "bx-messenger-audio" }, children : [
		BX.create("source", { attrs : { src : "/bitrix/js/im/audio/video-start.ogg", type : "audio/ogg; codecs=vorbis" }}),
		BX.create("source", { attrs : { src : "/bitrix/js/im/audio/video-start.mp3", type : "audio/mpeg" }})
	]}));

	this.panel.appendChild(this.BXIM.audio.stop = BX.create("audio", { props : { className : "bx-messenger-audio" }, children : [
		BX.create("source", { attrs : { src : "/bitrix/js/im/audio/video-stop.ogg", type : "audio/ogg; codecs=vorbis" }}),
		BX.create("source", { attrs : { src : "/bitrix/js/im/audio/video-stop.mp3", type : "audio/mpeg" }})
	]}));

	this.panel.appendChild(this.BXIM.audio.error = BX.create("audio", { props : { className : "bx-messenger-audio" }, children : [
		BX.create("source", { attrs : { src : "/bitrix/js/im/audio/video-error.ogg", type : "audio/ogg; codecs=vorbis" }}),
		BX.create("source", { attrs : { src : "/bitrix/js/im/audio/video-error.mp3", type : "audio/mpeg" }})
	]}));

	if (typeof(this.BXIM.audio.stop.play) == 'undefined')
	{
		this.BXIM.settings.enableSound = false;
	}

};

/* WebRTC UserMedia API */
BX.IM.WebRTC.prototype.startGetUserMedia = function(video, audio)
{
	clearTimeout(this.callDialtoneTimeout);
	this.BXIM.stopRepeatSound('ringtone');
	this.BXIM.stopRepeatSound('dialtone');

	clearTimeout(this.callInviteTimeout);
	clearTimeout(this.callDialogAllowTimeout);
	this.callDialogAllowTimeout = setTimeout(BX.delegate(function(){
		this.callDialogAllowShow();
	}, this), 1500);

	this.parent.startGetUserMedia.apply(this, arguments);
};

BX.IM.WebRTC.prototype.onUserMediaSuccess = function(stream)
{
	clearTimeout(this.callAllowTimeout);

	var result = this.parent.onUserMediaSuccess.apply(this, arguments);
	if (!result)
		return false;

	this.callOverlayProgress('online');
	this.callOverlayStatus(BX.message(this.callToGroup? 'IM_M_CALL_ST_WAIT_ACCESS_3':'IM_M_CALL_ST_WAIT_ACCESS_2'));
	if (this.callDialogAllow)
		this.callDialogAllow.close();

	this.attachMediaStream(this.callOverlayVideoSelf, this.callStreamSelf);
	this.callOverlayVideoSelf.muted = true;

	if (this.callToGroup && this.callVideo)
	{
		BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-call-active');
		BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-call-video');
	}
	setTimeout(BX.delegate(function(){
		if (!this.callActive)
			return false;

		BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-ready');
	}, this), 500);

	this.callCommand(this.callChatId, 'ready');
};

BX.IM.WebRTC.prototype.onUserMediaError = function(error)
{
	clearTimeout(this.callAllowTimeout);

	var result = this.parent.onUserMediaError.apply(this, arguments);
	if (!result)
		return false;

	if (this.callDialogAllow)
		this.callDialogAllow.close();

	this.callOverlayProgress('offline');
	this.callCommand(this.callChatId, 'errorAccess');
	this.callAbort(BX.message(this.callScreen? 'IM_M_CALL_ST_NO_ACCESS_SSH': 'IM_M_CALL_ST_NO_ACCESS'));
	if (this.callScreen)
		this.BXIM.saveSettings({'sshNotify': true});

	this.callOverlayButtons([{
		text: BX.message('IM_M_CALL_BTN_CLOSE'),
		className: 'bx-messenger-call-overlay-button-close',
		events: {
			click : BX.delegate(function() {
				this.callOverlayClose();
			}, this)
		}
	}]);
};

/* WebRTC PeerConnection Events */
BX.IM.WebRTC.prototype.setLocalAndSend = function(userId, desc)
{
	var result = this.parent.setLocalAndSend.apply(this, arguments);
	if (!result)
		return false;

	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/call.ajax.php?CALL_SIGNALING',
		method: 'POST',
		dataType: 'json',
		timeout: 30,
		data: {'IM_CALL' : 'Y', 'COMMAND': 'signaling', 'CHAT_ID': this.callChatId,  'RECIPIENT_ID' : userId, 'PEER': JSON.stringify( desc ), 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()}
	});

	return true;
};

BX.IM.WebRTC.prototype.onRemoteStreamAdded = function (userId, event, mainStream)
{
	if (mainStream)
	{
		this.attachMediaStream(this.callOverlayVideoMain, this.callStreamMain);
		if (this.desktop.ready())
			BX.desktop.onCustomEvent("bxCallChangeMainVideo", [this.callOverlayVideoMain.src]);

		if (!this.BXIM.windowFocus)
			this.desktop.openCallFloatDialog();

		this.callOverlayVideoMain.setAttribute('data-userId', userId);

		this.callOverlayVideoMain.muted = false;
		this.callOverlayVideoMain.volume = 1;

		BX('bx-messenger-call-overlay-button-plus').style.display = "inline-block";
		this.callOverlayStatus(BX.message('IM_M_CALL_ST_ONLINE'));

		BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-online');
		BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-call-active');
		if (this.callVideo)
			BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-call-video');
	}
	if (this.callToGroup)
	{
		if (!mainStream)
		{
			this.attachMediaStream(this.callOverlayVideoUsers[userId], this.callStreamUsers[userId]);
			BX.removeClass(this.callOverlayVideoUsers[userId].parentNode, 'bx-messenger-call-video-hide');
		}
		else
		{
			BX.addClass(this.callOverlayVideoUsers[userId].parentNode, 'bx-messenger-call-video-block-hide');
		}
	}
	if (this.initiator)
		this.callCommand(this.callChatId, 'start', {'CALL_TO_GROUP': this.callToGroup? 'Y': 'N', 'RECIPIENT_ID' : userId});
};

BX.IM.WebRTC.prototype.onRemoteStreamRemoved = function(userId, event)
{
	BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-online');
};

BX.IM.WebRTC.prototype.onIceCandidate = function (userId, candidates)
{
	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/call.ajax.php?CALL_SIGNALING',
		method: 'POST',
		dataType: 'json',
		timeout: 30,
		data: {'IM_CALL' : 'Y', 'COMMAND': 'signaling', 'CHAT_ID': this.callChatId,  'RECIPIENT_ID' : userId, 'PEER': JSON.stringify(candidates), 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()}
	});
}

BX.IM.WebRTC.prototype.peerConnectionError = function(userId, event)
{
	if (this.callDialogAllow)
		this.callDialogAllow.close();

	this.callOverlayProgress('offline');
	this.callCommand(this.callChatId, 'errorAccess');
	this.callAbort(BX.message('IM_M_CALL_ST_CON_ERROR'));

	this.callOverlayButtons([{
		text: BX.message('IM_M_CALL_BTN_CLOSE'),
		className: 'bx-messenger-call-overlay-button-close',
		events: {
			click : BX.delegate(function() {
				this.callOverlayClose();
			}, this)
		}
	}]);
};

BX.IM.WebRTC.prototype.peerConnectionReconnect = function (userId)
{
	var result = this.parent.peerConnectionReconnect.apply(this, arguments);
	if (!result)
		return false;

	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/call.ajax.php?CALL_RECONNECT',
		method: 'POST',
		dataType: 'json',
		timeout: 30,
		data: {'IM_CALL' : 'Y', 'COMMAND': 'reconnect', 'CHAT_ID' : this.callChatId,  'RECIPIENT_ID' : userId, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
		onsuccess: BX.delegate(function(){
			this.initPeerConnection(userId, true);
		}, this)
	});

	return true;
}

/* WebRTC Signaling API  */
BX.IM.WebRTC.prototype.callSupport = function(dialogId, messengerClass)
{
	messengerClass = messengerClass? messengerClass: this.messenger;
	var userCheck = true;
	if (typeof(dialogId) != 'undefined')
	{
		if (parseInt(dialogId)>0)
			userCheck = messengerClass.users[dialogId] && messengerClass.users[dialogId].status != 'guest';
		else
			userCheck = (messengerClass.userInChat[dialogId.toString().substr(4)] && messengerClass.userInChat[dialogId.toString().substr(4)].length <= 4);
	}
	return this.BXIM.ppServerStatus && this.enabled && userCheck;
};

BX.IM.WebRTC.prototype.callInvite = function(userId, video, screen)
{
	if (this.desktop.run() && BX.desktop.currentTab != 'im')
	{
		BX.desktop.changeTab('im');
	}

	if (this.screenSharingEnabled && this.BXIM.settings.sshNotify && screen)
	{
		this.BXIM.openConfirm(BX.message('IM_SSH_TEXT'), [
			new BX.PopupWindowButton({
				text : BX.message('IM_SSH_OK'),
				className : "popup-window-button-accept",
				events : { click : BX.delegate(function() {
					this.BXIM.saveSettings({'sshNotify': false});
					this.callInvite(userId, video, screen);
					BX.proxy_context.popupWindow.close();
				}, this)}
			}),
			new BX.PopupWindowButton({
				text : BX.message('IM_SSH_CANCEL'),
				className : "popup-window-button-decline",
				events : { click : function() { this.popupWindow.close(); } }
			})
		]);
		return false;
	}

	if (!this.callSupport())
	{
		if (!this.desktop.ready())
		{
			this.BXIM.openConfirm(BX.message('IM_CALL_NO_WEBRT'), [
				this.BXIM.platformName == ''? null: new BX.PopupWindowButton({
					text : BX.message('IM_M_CALL_BTN_DOWNLOAD'),
					className : "popup-window-button-accept",
					events : { click : BX.delegate(function() { window.open(BX.browser.IsMac()? "http://dl.bitrix24.com/b24/bitrix24_desktop.dmg": "http://dl.bitrix24.com/b24/bitrix24_desktop.exe", "desktopApp"); BX.proxy_context.popupWindow.close(); }, this) }
				}),
				new BX.PopupWindowButton({
					text : BX.message('IM_NOTIFY_CONFIRM_CLOSE'),
					className : "popup-window-button-decline",
					events : { click : function() { this.popupWindow.close(); } }
				})
			]);
		}
		return false;
	}

	var callToChat = false;
	if (parseInt(userId) > 0)
	{
		if (this.messenger.users[userId] && this.messenger.users[userId].status == 'guest')
		{
			this.BXIM.openConfirm(BX.message('IM_CALL_USER_OFFLINE'));
			return false;
		}
		else if (!this.messenger.users[userId])
		{
			this.messenger.users[userId] = {'id': userId, 'avatar': '/bitrix/js/im/images/blank.gif', 'name': BX.message('IM_M_LOAD_USER'), 'profile': this.BXIM.path.profileTemplate.replace('#user_id#', userId), 'status': 'guest', 'fake': true};
			this.messenger.hrphoto[userId] = '/bitrix/js/im/images/hidef-avatar.png';
		}
		userId = parseInt(userId);
	}
	else
	{
		userId = userId.toString().substr(4);
		if (!this.messenger.userInChat[userId] || this.messenger.userInChat[userId].length <= 1)
		{
			return false;
		}
		else if (!this.messenger.userInChat[userId] || this.messenger.userInChat[userId].length > 4)
		{
			this.BXIM.openConfirm(BX.message('IM_CALL_CHAT_LARGE'));
			return false;
		}
		callToChat = true;
	}

	video = video == true;
	screen = video === true && screen === true;

	if (!this.callActive && !this.callInit && userId > 0)
	{
		this.initiator = true;
		this.callInitUserId = this.BXIM.userId;
		this.callInit = true;
		this.callActive = false;
		this.callUserId = callToChat? 0: userId;
		this.callChatId = callToChat? userId: 0;
		this.callToGroup = callToChat;
		this.callGroupUsers = callToChat? this.messenger.userInChat[userId]: [];
		this.callVideo = video;
		this.callScreen = screen;

		this.callOverlayShow({
			toUserId : userId,
			fromUserId : this.BXIM.userId,
			callToGroup : this.callToGroup,
			video : video,
			status : BX.message('IM_M_CALL_ST_CONNECT'),
			buttons : [
				{
					text: BX.message('IM_M_CALL_BTN_HANGUP'),
					className: 'bx-messenger-call-overlay-button-hangup',
					events: {
						click : BX.delegate(function() {
							this.callSelfDisabled = true;
							this.callCommand(this.callChatId, 'decline', {'ACTIVE': this.callActive? 'Y': 'N', 'INITIATOR': this.initiator? 'Y': 'N'});
							this.callAbort();
							this.callOverlayClose();
						}, this)
					}
				},
				{
					text: BX.message('IM_M_CALL_BTN_CHAT'),
					className: 'bx-messenger-call-overlay-button-chat',
					showInMaximize: true,
					events: { click : BX.delegate(this.callOverlayToggleSize, this) }
				},
				{
					title: BX.message('IM_M_CALL_BTN_MAXI'),
					className: 'bx-messenger-call-overlay-button-maxi',
					showInMinimize: true,
					events: { click : BX.delegate(this.callOverlayToggleSize, this) }
				}
			]
		});
		this.BXIM.playSound("start");

		BX.ajax({
			url: '/bitrix/components/bitrix/im.messenger/call.ajax.php?CALL_INVITE',
			method: 'POST',
			dataType: 'json',
			timeout: 30,
			data: {'IM_CALL' : 'Y', 'COMMAND': 'invite', 'CHAT_ID' : userId, 'CHAT': (callToChat? 'Y': 'N'), 'VIDEO' : video? 'Y': 'N', 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
			onsuccess: BX.delegate(function(data)
			{
				if (data.ERROR == '')
				{
					this.callChatId = data.CHAT_ID;
					for (var i in data.USERS)
						this.messenger.users[i] = data.USERS[i];

					for (var i in data.HR_PHOTO)
						this.messenger.hrphoto[i] = data.HR_PHOTO[i];

					if (data.CALL_ENABLED && this.callToGroup)
					{
						for (var i in data.USERS_CONNECT)
						{
							this.connected[i] = true;
						}
						this.initiator = false;
						this.callInitUserId = 0;
						this.callInit = true;
						this.callActive = false;
						this.callUserId = 0;
						this.callChatId = data.CHAT_ID;
						this.callToGroup = data.CALL_TO_GROUP;
						this.callGroupUsers = this.messenger.userInChat[data.CHAT_ID];
						this.callVideo = data.CALL_VIDEO;
						this.callDialog();
						return false;
					}

					this.callOverlayUpdatePhoto();

					var callUserId = this.callToGroup? 'chat'+this.callChatId: this.callUserId;
					var callToGroup = this.callToGroup;
					var callVideo = this.callVideo;

					this.callInviteTimeout = setTimeout(BX.delegate(function(){
						this.callOverlayProgress('offline');
						this.callOverlayButtons([
							(callToGroup)? null: {
								text: BX.message('IM_M_CALL_BTN_RECALL'),
								className: 'bx-messenger-call-overlay-button-recall',
								events: {
									click : BX.delegate(function(e) {
										if (this.phoneCount(this.messenger.phones[callUserId]) > 0)
										{
											this.messenger.openPopupMenu(BX.proxy_context, 'callPhoneMenu', true, {userId: callUserId, video: callVideo });
										}
										else
										{
											this.callInvite(callUserId, callVideo);
										}
										BX.PreventDefault(e);
									}, this)
								}
							},
							{
								text: BX.message('IM_M_CALL_BTN_CLOSE'),
								className: 'bx-messenger-call-overlay-button-close',
								events: {
									click : BX.delegate(function() {
										this.callOverlayClose();
									}, this)
								}
							}
						]);

						this.callCommand(this.callChatId, 'errorOffline');
						this.callAbort(BX.message(callToGroup? 'IM_M_CALL_ST_NO_WEBRTC_1': 'IM_M_CALL_ST_NO_WEBRTC'));

					}, this), 30000);
				}
				else
				{
					this.callOverlayProgress('offline');
					this.callCommand(this.callChatId, 'errorOffline');
					this.callOverlayButtons([{
						text: BX.message('IM_M_CALL_BTN_CLOSE'),
						className: 'bx-messenger-call-overlay-button-close',
						events: {
							click : BX.delegate(function() {
								this.callOverlayClose();
							}, this)
						}
					}]);
					this.callAbort(data.ERROR);
				}
			}, this),
			onfailure: BX.delegate(function() {
				this.callAbort(BX.message('IM_M_CALL_ERR'));
				this.callOverlayClose();
			}, this)
		});
	}
};

BX.IM.WebRTC.prototype.callWait = function()
{
	if (!this.callSupport())
		return false;

	this.callOverlayStatus(BX.message(this.callToGroup? 'IM_M_CALL_ST_WAIT_2': 'IM_M_CALL_ST_WAIT'));

	clearTimeout(this.callInviteTimeout);
	this.callInviteTimeout = setTimeout(BX.delegate(function(){
		if (!this.initiator)
		{
			this.callAbort();
			this.callOverlayClose();
			return false;
		}
		this.callOverlayProgress('offline');
		var callUserId = this.callToGroup? 'chat'+this.callChatId: this.callUserId;
		var callVideo = this.callVideo;
		var callToGroup = this.callToGroup;

		this.callOverlayButtons([
			(callToGroup)? null: {
				text: BX.message('IM_M_CALL_BTN_RECALL'),
				className: 'bx-messenger-call-overlay-button-recall',
				events: {
					click : BX.delegate(function(e) {
						if (this.phoneCount(this.messenger.phones[callUserId]) > 0)
						{
							this.messenger.openPopupMenu(BX.proxy_context, 'callPhoneMenu', true, {userId: callUserId, video: callVideo });
						}
						else
						{
							this.callInvite(callUserId, callVideo);
						}
						BX.PreventDefault(e);
					}, this)
				}
			},
			{
				text: BX.message('IM_M_CALL_BTN_CLOSE'),
				className: 'bx-messenger-call-overlay-button-close',
				events: {
					click : BX.delegate(function() {
						this.callOverlayClose();
					}, this)
				}
			}
		]);

		this.callCommand(this.callChatId, 'waitTimeout');
		this.callAbort(BX.message(this.callToGroup? 'IM_M_CALL_ST_NO_ANSWER_2': 'IM_M_CALL_ST_NO_ANSWER'));

	}, this), 20000);
};

BX.IM.WebRTC.prototype.callChangeMainVideo = function(userId)
{
	var lastUserId = this.callOverlayVideoMain.getAttribute('data-userId');
	if (lastUserId == userId || !this.callStreamUsers[userId])
		return false;

	BX.addClass(this.callOverlayVideoMain, "bx-messenger-call-video-main-block-animation");

	clearTimeout(this.callChangeMainVideoTimeout);
	this.callChangeMainVideoTimeout = setTimeout(BX.delegate(function(){
		this.callOverlayVideoMain.setAttribute('data-userId', userId);
		this.attachMediaStream(this.callOverlayVideoMain, this.callStreamUsers[userId]);

		if (this.desktop.ready())
			BX.desktop.onCustomEvent("bxCallChangeMainVideo", [this.callOverlayVideoMain.src]);

		BX.addClass(this.callOverlayVideoUsers[userId].parentNode, 'bx-messenger-call-video-block-hide');
		BX.addClass(this.callOverlayVideoUsers[userId].parentNode, 'bx-messenger-call-video-hide');
		this.callOverlayVideoUsers[userId].parentNode.setAttribute('title', '');

		if (this.callStreamUsers[lastUserId])
		{
			this.attachMediaStream(this.callOverlayVideoUsers[lastUserId], this.callStreamUsers[lastUserId]);
			BX.removeClass(this.callOverlayVideoUsers[lastUserId].parentNode, 'bx-messenger-call-video-hide');
		}

		this.callOverlayVideoUsers[lastUserId].parentNode.setAttribute('title', BX.message('IM_CALL_MAGNIFY'));
		BX.removeClass(this.callOverlayVideoUsers[lastUserId].parentNode, 'bx-messenger-call-video-block-hide');
		BX.removeClass(this.callOverlayVideoMain, "bx-messenger-call-video-main-block-animation");

	}, this), 400);
};

BX.IM.WebRTC.prototype.callInviteUserToChat = function(users)
{
	if (this.callChatId <= 0 || this.messenger.popupChatDialogSendBlock)
		return false;

	var error = '';
	if (users.length == 0)
	{
		if (this.messenger.popupChatDialog != null)
			this.messenger.popupChatDialog.close();
		return false;
	}
	if (error != "")
	{
		this.BXIM.openConfirm(error);
		return false;
	}

	this.messenger.popupChatDialogSendBlock = true;
	if (this.messenger.popupChatDialog != null)
		this.messenger.popupChatDialog.buttons[0].setClassName('popup-window-button-disable');

	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/call.ajax.php?CALL_INVITE_USER',
		method: 'POST',
		dataType: 'json',
		timeout: 60,
		data: {'IM_CALL' : 'Y', 'COMMAND': 'invite_user', 'USERS': JSON.stringify(users), 'CHAT_ID': this.callChatId, 'RECIPIENT_ID' : this.callUserId, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
		onsuccess: BX.delegate(function(data){
			this.messenger.popupChatDialogSendBlock = false;
			if (this.messenger.popupChatDialog != null)
				this.messenger.popupChatDialog.buttons[0].setClassName('popup-window-button-accept');

			if (data.ERROR == '')
			{
				this.messenger.popupChatDialogSendBlock = false;
				if (this.messenger.popupChatDialog != null)
					this.messenger.popupChatDialog.close();
			}
			else
			{
				this.BXIM.openConfirm(data.ERROR);
			}
		}, this)
	});
};

BX.IM.WebRTC.prototype.callCommand = function(chatId, command, params, async)
{
	if (!this.callSupport())
		return false;

	chatId = parseInt(chatId);
	async = async != false;
	params = typeof(params) == 'object' ? params: {};

	if (chatId > 0)
	{
		BX.ajax({
			url: '/bitrix/components/bitrix/im.messenger/call.ajax.php?CALL_SHARED',
			method: 'POST',
			dataType: 'json',
			timeout: 30,
			async: async,
			data: {'IM_CALL' : 'Y', 'COMMAND': command, 'CHAT_ID': chatId, 'RECIPIENT_ID' : this.callUserId, 'PARAMS' : JSON.stringify(params), 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
			onsuccess: BX.delegate(function(){
				if (this.callDialogAllow)
					this.callDialogAllow.close();
			}, this)
		});
	}
};

/* WebRTC dialogs markup */
BX.IM.WebRTC.prototype.getHrPhoto = function(userId)
{
	var hrphoto = '';
	if (userId == 'phone')
		hrphoto = '/bitrix/js/im/images/hidef-phone.png';
	else if (this.messenger.hrphoto[userId])
		hrphoto = this.messenger.hrphoto[userId];
	else if (!this.messenger.users[userId] || this.messenger.users[userId].avatar == '/bitrix/js/im/images/blank.gif')
		hrphoto = '/bitrix/js/im/images/hidef-avatar.png';
	else
		hrphoto = this.messenger.users[userId].avatar;

	return hrphoto;
};

BX.IM.WebRTC.prototype.callDialog = function()
{
	if (!this.callSupport() && this.callOverlay == null)
		return false;

	clearTimeout(this.callInviteTimeout);
	clearTimeout(this.callDialogAllowTimeout);
	if (this.callDialogAllow)
		this.callDialogAllow.close();

	this.callActive = true;
	this.callOverlayProgress('wait');
	this.callOverlayStatus(BX.message('IM_M_CALL_ST_WAIT_ACCESS'));

	this.callOverlayButtons([
		{
			text: BX.message('IM_M_CALL_BTN_HANGUP'),
			className: 'bx-messenger-call-overlay-button-hangup',
			events: {
				click : BX.delegate(function() {
					var callVideo = this.callVideo;
					this.callSelfDisabled = true;
					this.callCommand(this.callChatId, 'decline', {'ACTIVE': this.callActive? 'Y': 'N', 'INITIATOR': this.initiator? 'Y': 'N'});
					this.BXIM.playSound('stop');
					if (callVideo && this.callStreamSelf != null)
						this.callOverlayVideoClose();
					else
						this.callOverlayClose();
				}, this)
			}
		},
		{
			hide: true,
			title: BX.message('IM_M_CHAT_TITLE'),
			className: 'bx-messenger-call-overlay-button-plus',
			events: { click : BX.delegate(function(e){
				if (this.messenger.userInChat[this.callChatId] && this.messenger.userInChat[this.callChatId].length == 4)
				{
					this.BXIM.openConfirm(BX.message('IM_CALL_GROUP_MAX_USERS'));
					return false;
				}
				this.messenger.openChatDialog({'chatId': this.callChatId, 'type': 'CALL_INVITE_USER', 'bind': BX.proxy_context, 'maxUsers': 4});
				BX.PreventDefault(e);
			}, this)}
		},
		this.callScreen? null: {
			title: BX.message('IM_M_CALL_BTN_MIC_TITLE'),
			id: 'bx-messenger-call-overlay-button-mic',
			className: 'bx-messenger-call-overlay-button-mic '+(this.audioMuted? ' bx-messenger-call-overlay-button-mic-off': ''),
			events: {
				click : BX.delegate(function() {
					this.toggleAudio();
					var icon = BX.findChild(BX.proxy_context, {className : "bx-messenger-call-overlay-button-mic"}, true);
					if (icon)
						BX.toggleClass(icon, 'bx-messenger-call-overlay-button-mic-off');
				}, this)
			}
		},
		!this.callScreen? null: {
			title: '',
			className: 'bx-messenger-call-overlay-button-mic bx-messenger-call-overlay-button-mic-off',
			disabled: true
		},
		{
			title: BX.message('IM_M_CALL_BTN_HISTORY_2'),
			className: 'bx-messenger-call-overlay-button-history2',
			events: { click : BX.delegate(function(){
				this.messenger.openHistory(this.messenger.currentTab);
			}, this) }
		},
		{
			title: BX.message('IM_M_CALL_BTN_CHAT_2'),
			className: 'bx-messenger-call-overlay-button-chat2',
			showInMaximize: true,
			events: { click : BX.delegate(this.callOverlayToggleSize, this) }
		},
		{
			title: BX.message('IM_M_CALL_BTN_MAXI'),
			className: 'bx-messenger-call-overlay-button-maxi',
			showInMinimize: true,
			events: { click : BX.delegate(this.callOverlayToggleSize, this) }
		},
		!this.callVideo || this.desktop.ready()? null: {
			title: BX.message('IM_M_CALL_BTN_FULL'),
			className: 'bx-messenger-call-overlay-button-full',
			events: { click : BX.delegate(this.overlayEnterFullScreen, this) }
		}
	]);

	if (this.messenger.popupMessenger == null)
	{
		this.messenger.openMessenger(this.callUserId);
		this.callOverlayToggleSize(false);
	}

	BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-maxi');
	BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-mini');
	BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-line');
	BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-call');
	if (!this.callToGroup && this.callVideo || !this.callVideo)
		BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-call-'+(this.callVideo? 'video': 'audio'));

	if (this.callScreen)
		this.startScreenSharing();
	else
		this.startGetUserMedia();
};

BX.IM.WebRTC.prototype.startScreenSharing = function()
{
	if (!this.screenSharingEnabled)
		return false;

	// errors
	// chromeMediaSource it is not permitted.
	// Screen capturing is requested multiple times. Multiple capturing of screen is not allowed even from two unique tabs.

	var options = {'chromeMediaSource': 'screen'};
	this.startGetUserMedia(options, false);
};

BX.IM.WebRTC.prototype.callOverlayShow = function(params)
{
	if (!params || !(params.toUserId || params.phoneNumber) || !(params.fromUserId || params.phoneNumber) || !params.buttons)
		return false;

	if (this.callOverlay != null)
	{
		this.callOverlayClose(false, true);
	}
	this.messenger.closeMenuPopup();

	params.video = params.video != false;
	params.callToGroup = params.callToGroup == true;
	params.callToPhone = params.callToPhone == true;
	params.minimize = typeof(params.minimize) == 'undefined'? (this.messenger.popupMessenger == null): (params.minimize == true);
	params.status = params.status? params.status: "";
	params.progress = params.progress? params.progress: "connect";

	this.callOldBeforeUnload = window.onbeforeunload;
	if (!params.prepare)
	{
		window.onbeforeunload = function(){
			return BX.message('IM_M_CALL_EFP')
		};
	}

	this.callOverlayMinimize = params.prepare? true: params.minimize;

	var scrollableArea = null;
	if (this.BXIM.dialogOpen)
		scrollableArea = this.messenger.popupMessengerBody;
	else if (this.BXIM.notifyOpen)
		scrollableArea = this.messenger.popupNotifyItem;

	if (scrollableArea)
	{
		if (this.BXIM.isScrollMin(scrollableArea))
		{
			setTimeout(BX.delegate(function(){
				BX.addClass(this.messenger.popupMessengerContent, 'bx-messenger-call');
			},this), params.minimize? 0: 400);
		}
		else
		{
			BX.addClass(this.messenger.popupMessengerContent, 'bx-messenger-call');
			scrollableArea.scrollTop = scrollableArea.scrollTop+50;
		}
	}
	else
	{
		BX.addClass(this.messenger.popupMessengerContent, 'bx-messenger-call');
	}

	var callOverlayStyle = {
		width : !this.messenger.popupMessenger? '610px': (this.messenger.popupMessengerExtra.style.display == "block"? this.messenger.popupMessengerExtra.offsetWidth-1: this.messenger.popupMessengerDialog.offsetWidth-1)+'px',
		height : (this.messenger.popupMessengerFullHeight-1)+'px',
		marginLeft : this.messenger.popupContactListSize+'px'
	};

	if (params.phoneNumber)
	{
		var callOverlayBody = this.callPhoneOverlayShow(params);
	}
	else
	{
		var callOverlayBody = params.callToGroup? this.callGroupOverlayShow(params): this.callUserOverlayShow(params);
	}

	this.callOverlay =  BX.create("div", { props : { className : 'bx-messenger-call-overlay '+(params.callToGroup? ' bx-messenger-call-overlay-group ':'')+(this.callOverlayMinimize? 'bx-messenger-call-overlay-mini': 'bx-messenger-call-overlay-maxi')}, style : callOverlayStyle, children: [
		BX.create("div", { props : { className : 'bx-messenger-call-overlay-lvl-1'}, children: [
			BX.create("div", { props : { className : 'bx-messenger-call-overlay-lvl-2'}, children: [
				BX.create("div", { props : { className : 'bx-messenger-call-video-main'}, children: [
					BX.create("div", { props : { className : 'bx-messenger-call-video-main-wrap'}, children: [
						BX.create("div", { props : { className : 'bx-messenger-call-video-main-watermark'}, children: [
							BX.create("img", { props : { className : 'bx-messenger-call-video-main-watermark-img'},  attrs : {src : '/bitrix/js/im/images/watermark_'+(this.BXIM.language == 'ru'? 'ru': 'en')+'.png'}})
						]}),
						BX.create("div", { props : { className : 'bx-messenger-call-video-main-cell'}, children: [
							BX.create("div", { props : { className : 'bx-messenger-call-video-main-bg'}, children: [
								this.callOverlayVideoMain = BX.create("video", { attrs : { autoplay : true }, props : { className : 'bx-messenger-call-video-main-block'}})
							]})
						]})
					]})
				]})
			]})
		]}),
		this.callOverlayBody = BX.create("div", { props : { className : 'bx-messenger-call-overlay-body'}, children: callOverlayBody})
	]});
	if (params.prepare)
	{
		BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-float');
		BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-show');
	}
	else if (this.messenger.popupMessenger != null)
	{
		this.messenger.popupMessenger.setClosingByEsc(false);
		BX.addClass(BX('bx-messenger-popup-messenger'), 'bx-messenger-popup-messenger-dont-close');
		this.messenger.popupMessengerContent.insertBefore(this.callOverlay, this.messenger.popupMessengerContent.firstChild);
	}
	else if (this.callNotify != null)
	{
		BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-float');
		this.callNotify.setContent(this.callOverlay);
	}
	else
	{
		this.callNotify = new BX.PopupWindow('bx-messenger-call-notify', null, {
			lightShadow : true,
			zIndex: 200,
			events : {
				onPopupClose : function() { this.destroy(); },
				onPopupDestroy : BX.delegate(function() {
					BX.unbind(window, "scroll", this.popupCallNotifyEvent);
					this.callNotify = null;
				}, this)},
			content : this.callOverlay
		});
		this.callNotify.show();

		BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-float');
		BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-show');
		BX.addClass(this.callNotify.popupContainer.children[0], 'bx-messenger-popup-window-transparent');
		BX.bind(window, "scroll", this.popupCallNotifyEvent = BX.proxy(function(){ this.callNotify.adjustPosition();}, this));
	}
	setTimeout(BX.delegate(function(){
		BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-show');
	}, this), 100);

	this.callOverlayStatus(params.status);
	this.callOverlayButtons(params.buttons);
	this.callOverlayProgress(params.progress);

	return true;
};

BX.IM.WebRTC.prototype.callGroupOverlayShow = function(params)
{
	this.callOverlayOptions = params;

	var callIncoming = params.fromUserId != this.BXIM.userId;
	var callChatId = params.fromUserId != this.BXIM.userId? params.fromUserId: params.toUserId;

	var callTitle = this.callOverlayTitle();

	this.callOverlayChatId = callChatId;

	var callOverlayPhotoUsers = [];
	var callOverlayVideoUsers = [];
	for (var i = 0; i < this.messenger.userInChat[callChatId].length; i++)
	{
		var userId = this.messenger.userInChat[callChatId][i];
		callOverlayPhotoUsers.push(BX.create("div", { props : { className : 'bx-messenger-call-overlay-photo-left'}, children: [
			BX.create("div", { props : { className : 'bx-messenger-call-overlay-photo-block'}, children: [
				this.callOverlayPhotoUsers[userId] = BX.create("img", { props : { className : 'bx-messenger-call-overlay-photo-img'}, attrs : { 'data-userId': userId, src : this.getHrPhoto(userId)}})
			]})
		]}));

		if (userId == this.BXIM.userId)
			continue;

		callOverlayVideoUsers.push(BX.create("div", { props : { className : 'bx-messenger-call-video-mini bx-messenger-call-video-hide'}, attrs: {'data-userId': userId}, events: {click: BX.delegate(function(){ this.callChangeMainVideo(BX.proxy_context.getAttribute('data-userId')); }, this)}, children: [
			this.callOverlayVideoUsers[userId] = BX.create("video", { attrs : { autoplay : true }, props : { className : 'bx-messenger-call-video-mini-block'}}),
			BX.create("div", { props : { className : 'bx-messenger-call-video-mini-photo'}, children: [
				this.callOverlayVideoPhotoUsers[userId] = BX.create("img", { props : { className : 'bx-messenger-call-video-mini-photo-img'}, attrs : { src : this.getHrPhoto(userId)}})
			]})
		]}));
	}
	return [
		BX.create("div", { props : { className : 'bx-messenger-call-overlay-line-maxi'}, attrs : { title: BX.message('IM_M_CALL_BTN_RETURN')}, children: [
			BX.create("div", { props : { className : 'bx-messenger-call-overlay-line-maxi-block'}})
		]}),
		BX.create("div", { props : { className : 'bx-messenger-call-video-users'}, children: callOverlayVideoUsers}),
		BX.create("div", { props : { className : 'bx-messenger-call-overlay-title'}, children: [
			this.callOverlayTitleBlock = BX.create("div", { props : { className : 'bx-messenger-call-overlay-title-block'}, html: callTitle})
		]}),
		BX.create("div", { props : { className : 'bx-messenger-call-overlay-photo'}, children: callOverlayPhotoUsers}),
		BX.create("div", { props : { className : 'bx-messenger-call-overlay-photo-progress-group'}, children: [
			this.callOverlayProgressBlock = BX.create("div", { props : { className : 'bx-messenger-call-overlay-photo-progress'}})
		]}),
		BX.create("div", { props : { className : 'bx-messenger-call-overlay-status'}, children: [
			this.callOverlayStatusBlock = BX.create("div", { props : { className : 'bx-messenger-call-overlay-status-block'}})
		]}),
		BX.create("div", { props : { className : 'bx-messenger-call-video-mini'}, children: [
			this.callOverlayVideoSelf = BX.create("video", { attrs : { autoplay : true }, props : { className : 'bx-messenger-call-video-mini-block'}}),
			BX.create("div", { props : { className : 'bx-messenger-call-video-mini-photo'}, children: [
				this.callOverlayPhotoMini = BX.create("img", { props : { className : 'bx-messenger-call-video-mini-photo-img'}, attrs : { src : this.getHrPhoto(this.BXIM.userId)}})
			]})
		]}),
		BX.create("div", { props : { className : 'bx-messenger-call-overlay-buttons'}, children: [
			this.callOverlayButtonsBlock = BX.create("div", { props : { className : 'bx-messenger-call-overlay-buttons-block'}})
		]})
	];
};

BX.IM.WebRTC.prototype.callUserOverlayShow = function(params)
{
	this.callOverlayOptions = params;

	var callIncoming = params.toUserId == this.BXIM.userId;
	var callUserId = callIncoming? params.fromUserId: params.toUserId;

	var callTitle = this.callOverlayTitle();

	this.callOverlayUserId = callUserId;

	return [
		BX.create("div", { props : { className : 'bx-messenger-call-overlay-line-maxi'}, attrs : { title: BX.message('IM_M_CALL_BTN_RETURN')}, children: [
			BX.create("div", { props : { className : 'bx-messenger-call-overlay-line-maxi-block'}})
		]}),
		BX.create("div", { props : { className : 'bx-messenger-call-overlay-title'}, children: [
			this.callOverlayTitleBlock = BX.create("div", { props : { className : 'bx-messenger-call-overlay-title-block'}, html: callTitle})
		]}),
		BX.create("div", { props : { className : 'bx-messenger-call-overlay-photo'}, children: [
			BX.create("div", { props : { className : 'bx-messenger-call-overlay-photo-left'}, children: [
				BX.create("div", { props : { className : 'bx-messenger-call-overlay-photo-block'}, children: [
					this.callOverlayPhotoCompanion = BX.create("img", { props : { className : 'bx-messenger-call-overlay-photo-img'}, attrs : { 'data-userId': callUserId, src : this.getHrPhoto(callUserId)}})
				]})
			]}),
			this.callOverlayProgressBlock = BX.create("div", { props : { className : 'bx-messenger-call-overlay-photo-progress'+(callIncoming?'': ' bx-messenger-call-overlay-photo-progress-incoming')}}),
			BX.create("div", { props : { className : 'bx-messenger-call-overlay-photo-right'}, children: [
				BX.create("div", { props : { className : 'bx-messenger-call-overlay-photo-block'}, children: [
					this.callOverlayPhotoSelf = BX.create("img", { props : { className : 'bx-messenger-call-overlay-photo-img'}, attrs : { 'data-userId': this.BXIM.userId, src : this.getHrPhoto(this.BXIM.userId)}})
				]})
			]})
		]}),
		BX.create("div", { props : { className : 'bx-messenger-call-overlay-status'}, children: [
			this.callOverlayStatusBlock = BX.create("div", { props : { className : 'bx-messenger-call-overlay-status-block'}})
		]}),
		BX.create("div", { props : { className : 'bx-messenger-call-video-mini'}, children: [
			this.callOverlayVideoSelf = BX.create("video", { attrs : { autoplay : true }, props : { className : 'bx-messenger-call-video-mini-block'}}),
			BX.create("div", { props : { className : 'bx-messenger-call-video-mini-photo'}, children: [
				this.callOverlayPhotoMini = BX.create("img", { props : { className : 'bx-messenger-call-video-mini-photo-img'}, attrs : { src : this.getHrPhoto(this.BXIM.userId)}})
			]})
		]}),
		BX.create("div", { props : { className : 'bx-messenger-call-overlay-buttons'}, children: [
			this.callOverlayButtonsBlock = BX.create("div", { props : { className : 'bx-messenger-call-overlay-buttons-block'}})
		]})
	];
};


BX.IM.WebRTC.prototype.callPhoneOverlayShow = function(params)
{
	this.callOverlayOptions = params;

	var callIncoming = params.toUserId == this.BXIM.userId;
	var callUserId = callIncoming? params.fromUserId: params.toUserId;

	this.callToPhone = true;
	var callTitle = BX.message(callIncoming? 'IM_PHONE_CALL_VOICE_FROM': 'IM_PHONE_CALL_VOICE_TO').replace('#PHONE#', (params.callTitle? params.callTitle: '+'+params.phoneNumber));

	this.callOverlayUserId = callUserId;

	return [
		BX.create("div", { props : { className : 'bx-messenger-call-overlay-line-maxi'}, attrs : { title: BX.message('IM_M_CALL_BTN_RETURN')}, children: [
			BX.create("div", { props : { className : 'bx-messenger-call-overlay-line-maxi-block'}})
		]}),
		BX.create("div", { props : { className : 'bx-messenger-call-overlay-title'}, children: [
			this.callOverlayTitleBlock = BX.create("div", { props : { className : 'bx-messenger-call-overlay-title-block'}, html: callTitle})
		]}),
		BX.create("div", { props : { className : 'bx-messenger-call-overlay-photo'}, children: [
			BX.create("div", { props : { className : 'bx-messenger-call-overlay-photo-left'}, children: [
				BX.create("div", { props : { className : 'bx-messenger-call-overlay-photo-block'}, children: [
					this.callOverlayPhotoCompanion = BX.create("img", { props : { className : 'bx-messenger-call-overlay-photo-img'}, attrs : { 'data-userId': 'phone', src : this.getHrPhoto('phone')}})
				]})
			]}),
			this.callOverlayProgressBlock = BX.create("div", { props : { className : 'bx-messenger-call-overlay-photo-progress'+(callIncoming?'': ' bx-messenger-call-overlay-photo-progress-incoming')}}),
			BX.create("div", { props : { className : 'bx-messenger-call-overlay-photo-right'}, children: [
				BX.create("div", { props : { className : 'bx-messenger-call-overlay-photo-block'}, children: [
					this.callOverlayPhotoSelf = BX.create("img", { props : { className : 'bx-messenger-call-overlay-photo-img'}, attrs : { 'data-userId': this.BXIM.userId, src : this.getHrPhoto(this.BXIM.userId)}})
				]})
			]})
		]}),
		BX.create("div", { props : { className : 'bx-messenger-call-overlay-crm-block'}, children: [
			this.callOverlayCrmBlock = BX.create("div", { props : { className : 'bx-messenger-call-overlay-crm-block-wrap'}})
		]}),
		BX.create("div", { props : { className : 'bx-messenger-call-overlay-status'}, children: [
			this.callOverlayStatusBlock = BX.create("div", { props : { className : 'bx-messenger-call-overlay-status-block'}})
		]}),
		BX.create("div", { props : { className : 'bx-messenger-call-video-mini'}, children: [
			this.callOverlayVideoSelf = BX.create("video", { attrs : { autoplay : true }, props : { className : 'bx-messenger-call-video-mini-block'}}),
			BX.create("div", { props : { className : 'bx-messenger-call-video-mini-photo'}, children: [
				this.callOverlayPhotoMini = BX.create("img", { props : { className : 'bx-messenger-call-video-mini-photo-img'}, attrs : { src : this.getHrPhoto(this.BXIM.userId)}})
			]})
		]}),
		BX.create("div", { props : { className : 'bx-messenger-call-overlay-buttons'}, children: [
			this.callOverlayButtonsBlock = BX.create("div", { props : { className : 'bx-messenger-call-overlay-buttons-block'}})
		]})
	];
};


BX.IM.WebRTC.prototype.callGroupOverlayRedraw = function()
{
	this.callToGroup = true;
	this.callGroupUsers = this.messenger.userInChat[this.callChatId];
	this.callOverlayUserId = 0;
	this.callOverlayChatId = this.callChatId;
	this.callOverlayBody.innerHTML = '';
	this.callOverlayOptions['callToGroup'] = this.callToGroup;
	this.callOverlayOptions['fromUserId'] = this.callChatId;
	BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-group');
	BX.adjust(this.callOverlayBody, {children: this.callGroupOverlayShow(this.callOverlayOptions)});
	this.callOverlayStatus(this.callOverlayOptions.status);
	this.callOverlayButtons(this.callOverlayOptions.buttons);
	this.callOverlayProgress(this.callOverlayOptions.progress);
	BX('bx-messenger-call-overlay-button-plus').style.display = "inline-block";

	this.attachMediaStream(this.callOverlayVideoSelf, this.callStreamSelf);
	this.callOverlayVideoSelf.muted = true;

	if (this.messenger.currentTab != 'chat'+this.callChatId)
	{
		this.messenger.openMessenger('chat'+this.callChatId);
		this.callOverlayToggleSize(false)
	}

	var userId = this.callOverlayVideoMain.getAttribute('data-userId');
	for (var i in this.callStreamUsers)
	{
		if (!this.callStreamUsers[i] && userId == i)
			continue;

		this.attachMediaStream(this.callOverlayVideoUsers[i], this.callStreamUsers[i]);
		BX.removeClass(this.callOverlayVideoUsers[i].parentNode, 'bx-messenger-call-video-hide');
	}
	BX.addClass(this.callOverlayVideoUsers[userId].parentNode, 'bx-messenger-call-video-block-hide');
	BX.addClass(this.callOverlayVideoUsers[userId].parentNode, 'bx-messenger-call-video-hide');
	this.callOverlayVideoUsers[userId].parentNode.setAttribute('title', '');

	return true;
};

BX.IM.WebRTC.prototype.overlayEnterFullScreen = function()
{
	if (this.callOverlayFullScreen)
	{
		BX.removeClass(this.messenger.popupMessengerContent, 'bx-messenger-call-overlay-full');
		if (document.cancelFullScreen)
			document.cancelFullScreen();
		else if (document.mozCancelFullScreen)
			document.mozCancelFullScreen();
		else if (document.webkitCancelFullScreen)
			document.webkitCancelFullScreen();
	}
	else
	{
		BX.addClass(this.messenger.popupMessengerContent, 'bx-messenger-call-overlay-full');
		if (this.detectedBrowser == 'chrome')
		{
			BX.bind(window, "webkitfullscreenchange", this.callOverlayFullScreenBind = BX.proxy(this.overlayEventFullScreen, this));
			this.messenger.popupMessengerContent.webkitRequestFullScreen(this.messenger.popupMessengerContent.ALLOW_KEYBOARD_INPUT);
		}
		else if (this.detectedBrowser == 'firefox')
		{
			BX.bind(window, "mozfullscreenchange", this.callOverlayFullScreenBind = BX.proxy(this.overlayEventFullScreen, this));
			this.messenger.popupMessengerContent.mozRequestFullScreen(this.messenger.popupMessengerContent.ALLOW_KEYBOARD_INPUT);
		}
	}
};

BX.IM.WebRTC.prototype.overlayEventFullScreen = function()
{
	if (this.callOverlayFullScreen)
	{
		if (this.detectedBrowser == 'chrome')
			BX.unbind(window, "webkitfullscreenchange", this.callOverlayFullScreenBind);
		else if (this.detectedBrowser == 'firefox')
			BX.unbind(window, "mozfullscreenchange", this.callOverlayFullScreenBind);

		BX.removeClass(this.messenger.popupMessengerContent, 'bx-messenger-call-overlay-full');
		BX.addClass(this.messenger.popupMessengerContent, 'bx-messenger-call-overlay-full-chrome-hack');
		setTimeout(BX.delegate(function(){
			BX.removeClass(this.messenger.popupMessengerContent, 'bx-messenger-call-overlay-full-chrome-hack');
		}, this), 100);
		this.callOverlayFullScreen = false;
	}
	else
	{
		BX.addClass(this.messenger.popupMessengerContent, 'bx-messenger-call-overlay-full');
		this.callOverlayFullScreen = true;
	}
	this.messenger.popupMessengerBody.scrollTop = this.messenger.popupMessengerBody.scrollHeight;
};

BX.IM.WebRTC.prototype.callOverlayToggleSize = function(minimize)
{
	if (this.callOverlay == null)
		return false;

	if (!this.ready())
	{
		this.callOverlayClose(true);
		return false;
	}

	var resizeToMax = typeof(minimize) == 'boolean'? !minimize: this.callOverlayMinimize;

	var minimizeToLine = false;
	if (this.messenger.popupMessenger != null && !this.BXIM.dialogOpen)
		minimizeToLine = true;
	else if (this.messenger.popupMessenger != null && this.callOverlayUserId > 0 && this.callOverlayUserId != this.messenger.currentTab)
		minimizeToLine = true;
	else if (this.messenger.popupMessenger != null && this.callOverlayChatId > 0 && this.callOverlayChatId != this.messenger.currentTab.toString().substr(4))
		minimizeToLine = true;
	else if (this.messenger.popupMessenger != null && this.callOverlayUserId == 0 && this.callOverlayChatId == 0 && this.phoneNumber)
		minimizeToLine = true;

	if (resizeToMax && this.callActive)
		BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-call');
	else
		BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-call');

	BX.unbindAll(this.callOverlay);
	if (resizeToMax)
	{
		this.callOverlayMinimize = false;

		BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-maxi');
		BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-line');
		BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-mini');
	}
	else
	{
		this.callOverlayMinimize = true;

		BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-mini');
		BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-maxi');

		if (minimizeToLine)
		{
			BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-line');

			setTimeout(BX.delegate(function(){
				BX.bind(this.callOverlay, 'click', BX.delegate(function() {
					if (this.BXIM.dialogOpen)
					{
						if (this.callOverlayUserId > 0)
						{
							this.messenger.openChatFlag = false;
							this.messenger.openDialog(this.callOverlayUserId, false, false);
						}
						else
						{
							this.messenger.openChatFlag = true;
							this.messenger.openDialog('chat'+this.callOverlayChatId, false, false);
						}
					}
					else
					{
						if (this.callOverlayUserId > 0)
						{
							this.messenger.openChatFlag = false;
							this.messenger.currentTab = this.callOverlayUserId;
						}
						else
						{
							this.messenger.openChatFlag = true;
							this.messenger.currentTab = 'chat'+this.callOverlayChatId;
						}
						this.messenger.extraClose(true, false);
					}
					this.callOverlayToggleSize(false);
				}, this));
			}, this), 200);
		}
		else
		{
			BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-line');
		}

		if (this.BXIM.isFocus())
			this.messenger.readMessage(this.messenger.currentTab);
		if (this.BXIM.isFocus() && this.notify.notifyUpdateCount > 0)
			this.notify.viewNotifyAll();
	}

	if (this.callOverlayUserId > 0 && this.callOverlayUserId == this.messenger.currentTab)
	{
		this.desktop.closeTopmostWindow();
	}
	else if (this.callOverlayChatId > 0 && this.callOverlayChatId == this.messenger.currentTab.toString().substr(4))
	{
		this.desktop.closeTopmostWindow();
	}
	else
	{
		this.desktop.openCallFloatDialog();
	}

	if (this.callDialogAllow != null)
	{
		if (this.callDialogAllow)
			this.callDialogAllow.close();

		setTimeout(BX.delegate(function(){
			this.callDialogAllowShow();
		}, this), 1500);
	}
};

BX.IM.WebRTC.prototype.callOverlayClose = function(animation, onlyMarkup)
{
	if (this.callOverlay == null)
		return false;

	this.audioMuted = true;
	this.toggleAudio(false);

	onlyMarkup = onlyMarkup == true;

	if (!onlyMarkup && this.callOverlayFullScreen)
	{
		if (this.detectedBrowser == 'firefox')
		{
			BX.removeClass(this.messenger.popupMessengerContent, 'bx-messenger-call-overlay-full');
			BX.remove(this.messenger.popupMessengerContent);
			BX.hide(this.messenger.popupMessenger.popupContainer);
			setTimeout(BX.delegate(function(){
				this.messenger.popupMessenger.destroy();
				this.messenger.openMessenger();
			}, this), 200);
		}
		else
			this.overlayEnterFullScreen();
	}

	if (this.messenger.popupMessenger != null)
	{
		var scrollableArea = null;
		if (this.BXIM.dialogOpen)
			scrollableArea = this.messenger.popupMessengerBody;
		else if (this.BXIM.notifyOpen)
			scrollableArea = this.messenger.popupNotifyItem;

		if (scrollableArea)
		{
			if (this.BXIM.isScrollMax(scrollableArea))
			{
				BX.removeClass(this.messenger.popupMessengerContent, 'bx-messenger-call');
			}
			else
			{
				BX.removeClass(this.messenger.popupMessengerContent, 'bx-messenger-call');
				scrollableArea.scrollTop = scrollableArea.scrollTop-50;
			}
		}
		else
		{
			BX.removeClass(this.messenger.popupMessengerContent, 'bx-messenger-call');
		}
	}
	this.messenger.closeMenuPopup();

	animation = animation != false;
	if (animation)
		BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-hide');

	if (animation)
	{
		setTimeout(BX.delegate(function(){
			BX.remove(this.callOverlay);
			this.callOverlay = null;
			this.callOverlayButtonsBlock = null;
			this.callOverlayTitleBlock = null;
			this.callOverlayStatusBlock = null;
			this.callOverlayProgressBlock = null;
			this.callOverlayMinimize = null;
			this.callOverlayChatId = 0;
			this.callOverlayUserId = 0;
			this.callOverlayPhotoSelf = null;
			this.callOverlayPhotoUsers = {};
			this.callOverlayVideoUsers = {};
			this.callOverlayVideoPhotoUsers = {};
			this.callOverlayOptions = {};
			this.callOverlayPhotoCompanion = null;
			this.callSelfDisabled = false;
			if (this.BXIM.isFocus())
				this.messenger.readMessage(this.messenger.currentTab);
		}, this), 300);
	}
	else
	{
		BX.remove(this.callOverlay);
		this.callOverlay = null;
		this.callOverlayButtonsBlock = null;
		this.callOverlayStatusBlock = null;
		this.callOverlayProgressBlock = null;
		this.callOverlayMinimize = null;
		this.callOverlayChatId = 0;
		this.callOverlayUserId = 0;
		this.callOverlayPhotoSelf = null;
		this.callOverlayPhotoUsers = {};
		this.callOverlayVideoUsers = {};
		this.callOverlayVideoPhotoUsers = {};
		this.callOverlayOptions = {};
		this.callOverlayPhotoCompanion = null;
		this.callSelfDisabled = false;
		if (this.BXIM.isFocus())
			this.messenger.readMessage(this.messenger.currentTab);
	}

	if (onlyMarkup)
	{
		window.onbeforeunload = this.callOldBeforeUnload;
		this.BXIM.stopRepeatSound('ringtone');
		this.BXIM.stopRepeatSound('dialtone');
	}
	else
	{
		this.callOverlayDeleteEvents();
	}

	this.desktop.closeTopmostWindow();
};

BX.IM.WebRTC.prototype.callOverlayVideoClose = function()
{
	this.audioMuted = true;
	this.toggleAudio(false);

	BX.style(this.callOverlayVideoMain, 'height', this.callOverlayVideoMain.parentNode.offsetHeight+'px');
	BX.addClass(this.callOverlayVideoMain.parentNode, 'bx-messenger-call-video-main-bg-start');

	setTimeout(BX.delegate(function(){
		this.callOverlayClose();
	}, this), 1700);
};

BX.IM.WebRTC.prototype.callAbort = function(reason)
{
	this.callOverlayDeleteEvents();

	if (reason)
		this.callOverlayStatus(reason);
};

BX.IM.WebRTC.prototype.callOverlayDeleteEvents = function(params)
{
	if (!this.callSupport())
		return false;

	params = params || {};

	this.desktop.closeTopmostWindow();

	window.onbeforeunload = this.callOldBeforeUnload;

	var closeNotify = params.closeNotify !== false;
	if (closeNotify && this.callNotify)
		this.callNotify.destroy();

	this.deleteEvents();

	this.callToPhone = false;
	this.callScreen = false;

	BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-call-audio');
	BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-call-video');

	if (this.messenger.popupMessenger)
	{
		this.messenger.popupMessenger.setClosingByEsc(true);
		BX.removeClass(BX('bx-messenger-popup-messenger'), 'bx-messenger-popup-messenger-dont-close');
		this.messenger.dialogStatusRedraw();
	}

	this.phoneCallFinish();

	clearTimeout(this.callDialtoneTimeout);
	this.BXIM.stopRepeatSound('ringtone');
	this.BXIM.stopRepeatSound('dialtone');

	clearTimeout(this.callInviteTimeout);
	clearTimeout(this.callDialogAllowTimeout);
	if (this.callDialogAllow)
		this.callDialogAllow.close();
}

BX.IM.WebRTC.prototype.callOverlayProgress = function(status)
{
	if (this.callOverlay == null)
		return false;

	this.callOverlayOptions.status = status;
	this.callOverlayProgressBlock.innerHTML = '';
	if (status == 'connect')
	{
		this.callOverlayProgressBlock.appendChild(
			BX.create("div", { props : { className : 'bx-messenger-call-overlay-progress'}, children: [
				BX.create("img", { props : { className : 'bx-messenger-call-overlay-progress-status bx-messenger-call-overlay-progress-status-anim-1'}}),
				BX.create("img", { props : { className : 'bx-messenger-call-overlay-progress-status bx-messenger-call-overlay-progress-status-anim-2'}})
			]})
		);
	}
	else if (status == 'online')
	{
		this.callOverlayProgressBlock.appendChild(
			BX.create("div", { props : { className : 'bx-messenger-call-overlay-progress bx-messenger-call-overlay-progress-online'}, children: [
				BX.create("img", { props : { className : 'bx-messenger-call-overlay-progress-status bx-messenger-call-overlay-progress-status-anim-3'}})
			]})
		);
	}
	else if (status == 'wait' || status == 'offline')
	{
		if (status == 'offline')
		{
			BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-online');
			BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-call');
			BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-call-active');
			this.BXIM.playSound('error');
		}
		this.callOverlayProgressBlock.appendChild(
			BX.create("div", { props : { className : 'bx-messenger-call-overlay-progress bx-messenger-call-overlay-progress-'+status}})
		);
	}
	else
		return false;
};

BX.IM.WebRTC.prototype.callOverlayStatus = function(status)
{
	if (this.callOverlay == null || typeof(status) == 'undefined')
		return false;
	this.callOverlayOptions.status = status;
	this.callOverlayStatusBlock.innerHTML = status.toString();
};

BX.IM.WebRTC.prototype.callOverlayTitle = function()
{
	var callTitle = '';
	var callIncoming = this.callInitUserId != this.BXIM.userId;
	if (this.callToPhone)
	{
		callTitle = this.callOverlayTitleBlock.innerHTML;
	}
	else if (this.callToGroup)
	{
		callTitle = this.messenger.chat[this.callChatId].name;
		if (callTitle.length > 85)
			callTitle = callTitle.substr(0,85)+'...';

		callTitle = BX.message('IM_CALL_GROUP_'+(this.callVideo? 'VIDEO':'VOICE')+(callIncoming? '_FROM': '_TO')).replace('#CHAT#', callTitle);
	}
	else
	{
		callTitle = BX.message('IM_M_CALL_'+(this.callVideo? 'VIDEO':'VOICE')+(callIncoming? '_FROM': '_TO')).replace('#USER#', this.messenger.users[this.callUserId].name);
	}

	return callTitle;
}

BX.IM.WebRTC.prototype.callOverlayUpdatePhoto = function()
{
	this.callOverlayTitleBlock.innerHTML = this.callOverlayTitle();

	for (var i in this.callOverlayPhotoUsers)
	{
		if (i == 'phone')
			this.callOverlayPhotoUsers[i].src = '/bitrix/js/im/images/hidef-phone.png';
		else if (this.messenger.hrphoto[i])
			this.callOverlayPhotoUsers[i].src = this.messenger.hrphoto[i];
		else if (this.messenger.users[i].avatar == '/bitrix/js/im/images/blank.gif')
			this.callOverlayPhotoUsers[i].src = '/bitrix/js/im/images/hidef-avatar.png';
		else
			this.callOverlayPhotoUsers[i].src = this.messenger.users[i].avatar;
	}
	for (var i in this.callOverlayVideoPhotoUsers)
	{
		if (i == 'phone')
			this.callOverlayVideoPhotoUsers[i].src = '/bitrix/js/im/images/hidef-phone.png';
		else if (this.messenger.hrphoto[i])
			this.callOverlayVideoPhotoUsers[i].src = this.messenger.hrphoto[i];
		else if (this.messenger.users[i].avatar == '/bitrix/js/im/images/blank.gif')
			this.callOverlayVideoPhotoUsers[i].src = '/bitrix/js/im/images/hidef-avatar.png';
		else
			this.callOverlayVideoPhotoUsers[i].src = this.messenger.users[i].avatar;
	}
	if (this.callOverlayPhotoCompanion)
	{
		var companionUserId = this.callOverlayPhotoCompanion.getAttribute('data-userId');
		if (companionUserId == 'phone')
			this.callOverlayPhotoCompanion.src = '/bitrix/js/im/images/hidef-phone.png';
		else if (this.messenger.hrphoto[companionUserId])
			this.callOverlayPhotoCompanion.src  = this.messenger.hrphoto[companionUserId];
		else if (this.messenger.users[companionUserId] && this.messenger.users[companionUserId].avatar == '/bitrix/js/im/images/blank.gif')
			this.callOverlayPhotoCompanion.src  = '/bitrix/js/im/images/hidef-avatar.png';
		else if (this.messenger.users[companionUserId])
			this.callOverlayPhotoCompanion.src  = this.messenger.users[companionUserId].avatar;
	}
	if (this.callOverlayPhotoSelf)
	{
		this.callOverlayPhotoSelf.src = this.getHrPhoto(this.BXIM.userId);
		this.callOverlayPhotoMini.src = this.callOverlayPhotoSelf.src;
	}
};

BX.IM.WebRTC.prototype.callOverlayDrawCrm = function()
{
	if (this.callOverlayCrmBlock && this.phoneCrm)
	{
		this.callOverlayCrmBlock.innerHTML = '';

		if (this.phoneCrm.FOUND == 'Y')
		{
			BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-mini');
			BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-maxi');
			BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-crm');
			BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-crm-short');

			var crmAbout = BX.create("div", { props : { className : 'bx-messenger-call-crm-about'}, children: [
				BX.create("div", { props : { className : 'bx-messenger-call-crm-about-block bx-messenger-call-crm-about-contact'}, children: [
					BX.create("div", { props : { className : 'bx-messenger-call-crm-about-block-header'}, html: BX.message('IM_CRM_ABOUT_CONTACT')}),
					BX.create("div", { props : { className : 'bx-messenger-call-crm-about-block-avatar'}, html: this.phoneCrm.CONTACT.PHOTO? '<img src="'+this.phoneCrm.CONTACT.PHOTO+'" class="bx-messenger-call-crm-about-block-avatar-img">': ''}),
					BX.create("div", { props : { className : 'bx-messenger-call-crm-about-block-line-1'}, html: this.phoneCrm.CONTACT.NAME? this.phoneCrm.CONTACT.NAME: ''}),
					BX.create("div", { props : { className : 'bx-messenger-call-crm-about-block-line-2'}, html: this.phoneCrm.CONTACT.POST? this.phoneCrm.CONTACT.POST: ''})
				]}),
				this.phoneCrm.COMPANY? BX.create("div", { props : { className : 'bx-messenger-call-crm-about-block bx-messenger-call-crm-about-company'}, children: [
					BX.create("div", { props : { className : 'bx-messenger-call-crm-about-block-header'}, html: BX.message('IM_CRM_ABOUT_COMPANY')}),
					BX.create("div", { props : { className : 'bx-messenger-call-crm-about-block-line-1'}, html: this.phoneCrm.COMPANY})
				]}): null
			]});

			var crmResponsibility = BX.create("div", { props : { className : 'bx-messenger-call-crm-about'}, children: [
				BX.create("div", { props : { className : 'bx-messenger-call-crm-about-block bx-messenger-call-crm-about-contact'}, children: (this.phoneCrm.RESPONSIBILITY.NAME? [
					BX.create("div", { props : { className : 'bx-messenger-call-crm-about-block-header'}, html: BX.message('IM_CRM_RESPONSIBILITY')}),
					BX.create("div", { props : { className : 'bx-messenger-call-crm-about-block-avatar'}, html: this.phoneCrm.RESPONSIBILITY.PHOTO? '<img src="'+this.phoneCrm.RESPONSIBILITY.PHOTO+'" class="bx-messenger-call-crm-about-block-avatar-img">': ''}),
					BX.create("div", { props : { className : 'bx-messenger-call-crm-about-block-line-1'}, html: this.phoneCrm.RESPONSIBILITY.NAME? this.phoneCrm.RESPONSIBILITY.NAME: ''}),
					BX.create("div", { props : { className : 'bx-messenger-call-crm-about-block-line-2'}, html: this.phoneCrm.RESPONSIBILITY.POST? this.phoneCrm.RESPONSIBILITY.POST: ''})
				]: [])})
			]});

			var crmButtons = null;
			if (this.phoneCrm.ACTIVITY_URL || this.phoneCrm.INVOICE_URL || this.phoneCrm.DEAL_URL)
			{
				crmButtons = BX.create("div", { props : { className : 'bx-messenger-call-crm-buttons'}, children: [
					this.phoneCrm.ACTIVITY_URL? BX.create("a", { attrs: {target: '_blank', href: this.phoneCrm.ACTIVITY_URL},  props : { className : 'bx-messenger-call-crm-button'}, html: BX.message('IM_CRM_BTN_ACTIVITY')}): null,
					this.phoneCrm.DEAL_URL? BX.create("a", { attrs: {target: '_blank', href: this.phoneCrm.DEAL_URL},  props : { className : 'bx-messenger-call-crm-button'}, html: BX.message('IM_CRM_BTN_DEAL')}): null,
					this.phoneCrm.INVOICE_URL? BX.create("a", { attrs: {target: '_blank', href: this.phoneCrm.INVOICE_URL},  props : { className : 'bx-messenger-call-crm-button'}, html: BX.message('IM_CRM_BTN_INVOICE')}): null,
					this.phoneCrm.CURRENT_CALL_URL? BX.create("a", { attrs: {target: '_blank', href: this.phoneCrm.CURRENT_CALL_URL},  props : { className : 'bx-messenger-call-crm-link'}, html: '+ '+BX.message('IM_CRM_BTN_CURRENT_CALL')}): null
				]})
			}

			var crmActivities = null;
			if (this.phoneCrm.ACTIVITIES.length > 0)
			{
				crmArActivities = [];
				for (var i = 0; i < this.phoneCrm.ACTIVITIES.length; i++)
				{
					crmArActivities.push(BX.create("div", { props : { className : 'bx-messenger-call-crm-activities-item'}, children: [
						BX.create("a", { attrs: {target: '_blank', href: this.phoneCrm.ACTIVITIES[i].URL}, props : { className : 'bx-messenger-call-crm-activities-name'}, html: this.phoneCrm.ACTIVITIES[i].TITLE}),
						BX.create("div", {
							props : { className : 'bx-messenger-call-crm-activities-status'},
							html: (this.phoneCrm.ACTIVITIES[i].OVERDUE == 'Y'? '<span class="bx-messenger-call-crm-activities-dot"></span>': '')+this.phoneCrm.ACTIVITIES[i].DATE
						})
					]}));
				}
				crmActivities = BX.create("div", { props : { className : 'bx-messenger-call-crm-activities'}, children: [
					BX.create("div", { props : { className : 'bx-messenger-call-crm-activities-header'}, html: BX.message('IM_CRM_ACTIVITIES')}),
					BX.create("div", { props : { className : 'bx-messenger-call-crm-activities-items'}, children: crmArActivities})
				]});
			}

			var crmDeals = null;
			if (this.phoneCrm.DEALS.length > 0)
			{
				crmArDeals = [];
				for (var i = 0; i < this.phoneCrm.DEALS.length; i++)
				{
					crmArDeals.push(BX.create("div", { props : { className : 'bx-messenger-call-crm-deals-item'}, children: [
						BX.create("a", { attrs: {target: '_blank', href: this.phoneCrm.DEALS[i].URL}, props : { className : 'bx-messenger-call-crm-deals-name'}, html: this.phoneCrm.DEALS[i].TITLE}),
						BX.create("div", {
							props : { className : 'bx-messenger-call-crm-deals-status'},
							html: this.phoneCrm.DEALS[i].STAGE
						})
					]}));
				}
				crmDeals = BX.create("div", { props : { className : 'bx-messenger-call-crm-deals'}, children: [
					BX.create("div", { props : { className : 'bx-messenger-call-crm-deals-header'}, html: BX.message('IM_CRM_DEALS')}),
					BX.create("div", { props : { className : 'bx-messenger-call-crm-deals-items'}, children: crmArDeals})
				]});
			}

			var crmBlock = [];
			if (crmActivities && crmDeals)
			{
				crmBlock = [
					BX.create("div", { props : { className : 'bx-messenger-call-crm-space'}}),
					crmAbout,
					crmActivities,
					crmDeals,
					BX.create("div", { props : { className : 'bx-messenger-call-crm-space'}}),
					crmButtons
				];
			}
			else
			{
				if (crmActivities || crmDeals)
				{
					crmBlock = [
						BX.create("div", { props : { className : 'bx-messenger-call-crm-space'}}),
						crmAbout,
						BX.create("div", { props : { className : 'bx-messenger-call-crm-space'}}),
						crmResponsibility,
						BX.create("div", { props : { className : 'bx-messenger-call-crm-space'}}),
						BX.create("div", { props : { className : 'bx-messenger-call-crm-space'}}),
						crmActivities? crmActivities: crmDeals,
						BX.create("div", { props : { className : 'bx-messenger-call-crm-space'}}),
						crmButtons
					];
				}
				else if (!crmActivities && !crmDeals && crmButtons)
				{
					BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-crm-short');
					this.callOverlayCrmBlock.innerHTML = '';
					crmBlock = [
						BX.create("div", { props : { className : 'bx-messenger-call-crm-space'}}),
						crmAbout,
						BX.create("div", { props : { className : 'bx-messenger-call-crm-space'}}),
						crmResponsibility,
						BX.create("div", { props : { className : 'bx-messenger-call-crm-space'}}),
						BX.create("div", { props : { className : 'bx-messenger-call-crm-space'}}),
						crmButtons
					];
				}
				else
				{
					BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-crm-short');
					this.callOverlayCrmBlock.innerHTML = '';
					crmBlock = [
						BX.create("div", { props : { className : 'bx-messenger-call-crm-space'}}),
						BX.create("div", { props : { className : 'bx-messenger-call-crm-space'}}),
						BX.create("div", { props : { className : 'bx-messenger-call-crm-space'}}),
						crmAbout,
						BX.create("div", { props : { className : 'bx-messenger-call-crm-space'}}),
						BX.create("div", { props : { className : 'bx-messenger-call-crm-space'}}),
						BX.create("div", { props : { className : 'bx-messenger-call-crm-space'}}),
						BX.create("div", { props : { className : 'bx-messenger-call-crm-space'}}),
						crmResponsibility
					];
				}
			}
		}
		else if (this.phoneCrm.LEAD_URL || this.phoneCrm.CONTACT_URL)
		{
			BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-mini');
			BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-maxi');
			BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-crm');
			BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-crm-short');
			crmBlock = [
				BX.create("div", { props : { className : 'bx-messenger-call-crm-phone-space'}}),
				BX.create("div", { props : { className : 'bx-messenger-call-crm-phone-icon'}, children: [
					BX.create("div", { props : { className : 'bx-messenger-call-crm-phone-icon-block'}})
				]}),
				BX.create("div", { props : { className : 'bx-messenger-call-crm-phone-space'}}),
				BX.create("div", { props : { className : 'bx-messenger-call-crm-buttons bx-messenger-call-crm-buttons-center'}, children: [
					this.phoneCrm.CONTACT_URL? BX.create("a", { attrs: {target: '_blank', href: this.phoneCrm.CONTACT_URL},  props : { className : 'bx-messenger-call-crm-button'}, html: BX.message('IM_CRM_BTN_NEW_CONTACT')}): null,
					this.phoneCrm.LEAD_URL? BX.create("a", { attrs: {target: '_blank', href: this.phoneCrm.LEAD_URL},  props : { className : 'bx-messenger-call-crm-button'}, html: BX.message('IM_CRM_BTN_NEW_LEAD')}): null
				]})
			];
		}
		BX.adjust(this.callOverlayCrmBlock, {children: crmBlock});
	}
};

BX.IM.WebRTC.prototype.callOverlayButtons = function(buttons)
{
	if (this.callOverlay == null)
		return false;

	this.callOverlayOptions.buttons = buttons;
	BX.cleanNode(this.callOverlayButtonsBlock);
	for (var i = 0; i < buttons.length; i++)
	{
		if (buttons[i] == null)
			continue;

		var button = {};
		button.title = buttons[i].title || "";
		button.text = buttons[i].text || "";
		button.subtext = buttons[i].subtext || "";
		button.className = buttons[i].className || "";
		button.id = buttons[i].id || button.className;
		button.events = buttons[i].events || {};
		button.style = {};

		var classHide = "";
		if (typeof(buttons[i].showInMinimize) == 'boolean')
			classHide = ' bx-messenger-call-overlay-button-show-'+(buttons[i].showInMinimize? 'mini': 'maxi');
		else if (typeof(buttons[i].showInMaximize) == 'boolean')
			classHide = ' bx-messenger-call-overlay-button-show-'+(buttons[i].showInMaximize? 'maxi': 'mini');
		else if (typeof(buttons[i].disabled) == 'boolean' && buttons[i].disabled)
			classHide = ' bx-messenger-call-overlay-button-disabled';
		if (typeof(buttons[i].hide) == 'boolean' && buttons[i].hide)
			button.style.display = 'none';

		this.callOverlayButtonsBlock.appendChild(
			BX.create("div", { attrs: {id: button.id, title: button.title}, style: button.style, props : { className : 'bx-messenger-call-overlay-button'+(button.subtext? ' bx-messenger-call-overlay-button-sub': '')+classHide}, events : button.events, html: '<span class="'+button.className+'"></span><span class="bx-messenger-call-overlay-button-text">'+button.text+(button.subtext? '<div class="bx-messenger-call-overlay-button-text-sub">'+button.subtext+'</div>': '')+'</span>'})
		);
	}
};

BX.IM.WebRTC.prototype.callDialogAllowShow = function(checkActive)
{
	if (this.desktop.ready())
		return false;

	if (this.phoneMicAccess)
		return false;

	checkActive = checkActive != false;
	if (!this.phoneAPI)
	{
		if (this.callStreamSelf != null)
			return false;

		if (checkActive && !this.callActive)
			return false;
	}

	if (this.callDialogAllow)
		this.callDialogAllow.close();

	this.callDialogAllow = new BX.PopupWindow('bx-messenger-call-notify', this.popupMessengerDialog, {
		lightShadow : true,
		zIndex: 200,
		offsetTop: (this.popupMessengerDialog? (this.callOverlayMinimize? -20: -this.popupMessengerDialog.offsetHeight/2-100): -20),
		offsetLeft: (this.callOverlay? (this.callOverlay.offsetWidth/2-170): 0),
		events : {
			onPopupClose : function() { this.destroy(); },
			onPopupDestroy : BX.delegate(function() {
				this.callDialogAllow = null;
			}, this)},
		content : BX.create("div", { props : { className : 'bx-messenger-call-dialog-allow'}, children: [
			BX.create("div", { props : { className : 'bx-messenger-call-dialog-allow-image-block'}, children: [
				BX.create("div", { props : { className : 'bx-messenger-call-dialog-allow-center'}, children: [
					BX.create("div", { props : { className : 'bx-messenger-call-dialog-allow-arrow'}})
				]}),
				BX.create("div", { props : { className : 'bx-messenger-call-dialog-allow-center'}, children: [
					BX.create("div", { props : { className : 'bx-messenger-call-dialog-allow-button'}, html: BX.message('IM_M_CALL_ALLOW_BTN')})
				]})
			]}),
			BX.create("div", { props : { className : 'bx-messenger-call-dialog-allow-text'}, html: BX.message('IM_M_CALL_ALLOW_TEXT')})
		]})
	});
	this.callDialogAllow.show();
};

BX.IM.WebRTC.prototype.callNotifyWait = function(chatId, userId, video, callToGroup, join)
{
	if (!this.callSupport())
		return false;

	join = join == true;
	video = video == true;
	callToGroup = callToGroup == true;

	this.initiator = false;
	this.callInitUserId = userId;
	this.callInit = true;
	this.callActive = false;
	this.callUserId = callToGroup? 0: userId;
	this.callChatId = chatId;
	this.callToGroup = callToGroup;
	this.callGroupUsers = this.messenger.userInChat[chatId];
	this.callVideo = video;

	this.callOverlayShow({
		toUserId : this.BXIM.userId,
		fromUserId : this.callToGroup? chatId: userId,
		callToGroup : this.callToGroup,
		video : video,
		status : BX.message(this.callToGroup? 'IM_M_CALL_ST_INVITE_2': 'IM_M_CALL_ST_INVITE'),
		buttons : [
			{
				text: BX.message('IM_M_CALL_BTN_ANSWER'),
				className: 'bx-messenger-call-overlay-button-answer',
				events: {
					click : BX.delegate(function() {
						this.BXIM.stopRepeatSound('ringtone');
						if (join)
						{
							var callToGroup = this.callToGroup;
							var callChatId = this.callChatId;
							var callUserId = this.callUserId;
							var callVideo = this.callVideo;

							this.callAbort();
							this.callOverlayClose(false);
							this.callInvite(callToGroup? 'chat'+callChatId: callUserId, callVideo);
						}
						else
						{
							this.callDialog();
							BX.ajax({
								url: '/bitrix/components/bitrix/im.messenger/call.ajax.php?CALL_ANSWER',
								method: 'POST',
								dataType: 'json',
								timeout: 30,
								data: {'IM_CALL' : 'Y', 'COMMAND': 'answer', 'CHAT_ID': this.callChatId, 'CALL_TO_GROUP': this.callToGroup? 'Y': 'N',  'RECIPIENT_ID' : this.callUserId, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()}
							});
							this.desktop.closeTopmostWindow();
						}
					}, this)
				}
			},
			{
				text: BX.message('IM_M_CALL_BTN_HANGUP'),
				className: 'bx-messenger-call-overlay-button-hangup',
				events: {
					click : BX.delegate(function() {
						this.BXIM.stopRepeatSound('ringtone');
						this.callSelfDisabled = true;
						this.callCommand(this.callChatId, 'decline', {'ACTIVE': this.callActive? 'Y': 'N', 'INITIATOR': this.initiator? 'Y': 'N'});
						this.callAbort();
						this.callOverlayClose();
					}, this)
				}
			},
			{
				text: BX.message('IM_M_CALL_BTN_CHAT'),
				className: 'bx-messenger-call-overlay-button-chat',
				showInMaximize: true,
				events: { click : BX.delegate(this.callOverlayToggleSize, this) }
			},
			{
				title: BX.message('IM_M_CALL_BTN_MAXI'),
				className: 'bx-messenger-call-overlay-button-maxi',
				showInMinimize: true,
				events: { click : BX.delegate(this.callOverlayToggleSize, this) }
			}
		]
	});

	if(!this.BXIM.windowFocus && this.BXIM.notifyManager.nativeNotifyGranted())
	{
		var notify = {
			'title':  BX.message('IM_PHONE_DESC'),
			'text':  BX.util.htmlspecialcharsback(this.callOverlayTitle()),
			'icon': this.callUserId? this.messenger.users[this.callUserId].avatar: '',
			'tag':  'im-call'
		};
		notify.onshow = function() {
			var notify = this;
			setTimeout(function(){
				notify.close();
			}, 5000)
		}
		notify.onclick = function() {
			window.focus();
			this.close();
		}
		this.BXIM.notifyManager.nativeNotify(notify)
	}
};

BX.IM.WebRTC.prototype.callNotifyWaitDesktop = function(chatId, userId, video, callToGroup, join)
{
	this.BXIM.ppServerStatus = true;
	if (!this.callSupport() || !this.desktop.ready())
		return false;

	join = join == true;
	video = video == true;
	callToGroup = callToGroup == true;

	this.initiator = false;
	this.callInitUserId = userId;
	this.callInit = true;
	this.callActive = false;
	this.callUserId = callToGroup? 0: userId;
	this.callChatId = chatId;
	this.callToGroup = callToGroup;
	this.callGroupUsers = this.messenger.userInChat[chatId];
	this.callVideo = video;

	this.callOverlayShow({
		prepare : true,
		toUserId : this.BXIM.userId,
		fromUserId : this.callToGroup? chatId: userId,
		callToGroup : this.callToGroup,
		video : video,
		status : BX.message(this.callToGroup? 'IM_M_CALL_ST_INVITE_2': 'IM_M_CALL_ST_INVITE'),
		buttons : [
			{
				text: BX.message('IM_M_CALL_BTN_ANSWER'),
				className: 'bx-messenger-call-overlay-button-answer',
				events: {
					click : BX.delegate(function() {
						if (join)
							BX.desktop.onCustomEvent("main", "bxCallJoin", [chatId, userId, video, callToGroup]);
						else
							BX.desktop.onCustomEvent("main", "bxCallAnswer", [chatId, userId, video, callToGroup]);

						BX.desktop.windowCommand('close');
					}, this)
				}
			},
			{
				text: BX.message('IM_M_CALL_BTN_HANGUP'),
				className: 'bx-messenger-call-overlay-button-hangup',
				events: {
					click : BX.delegate(function() {
						BX.desktop.onCustomEvent("main", "bxCallDecline", []);
						BX.desktop.windowCommand('close');
					}, this)
				}
			}
		]
	});
	this.desktop.drawOnPlaceholder(this.callOverlay);
	BX.desktop.setWindowPosition({X:STP_CENTER, Y:STP_VCENTER, Width: 470, Height: 120});
};

BX.IM.WebRTC.prototype.callFloatDialog = function(title, stream, audioMuted)
{
	if (!this.desktop.ready())
		return false;

	this.audioMuted = audioMuted;

	var minCallWidth = stream? this.desktop.minCallVideoWidth: this.desktop.minCallWidth;
	var minCallHeight = stream? this.desktop.minCallVideoHeight: this.desktop.minCallHeight;

	var callOverlayStyle = {
		width : minCallWidth+'px',
		height : minCallHeight+'px'
	};

	this.callOverlay =  BX.create("div", { props : { className : 'bx-messenger-call-float'+(stream? '': ' bx-messenger-call-float-audio')}, style : callOverlayStyle, children: [
		this.callOverlayVideoMain = (!stream? null: BX.create("video", {
			attrs : { autoplay : true, src: stream },
			props : { className : 'bx-messenger-call-float-video'},
			events: {'click': BX.delegate(function(){
				BX.desktop.onCustomEvent("main", "bxCallOpenDialog", []);
			}, this)}
		})),
		BX.create("div", { props : { className : 'bx-messenger-call-float-buttons'}, children: [
			BX.create("div", {
				props : { className : 'bx-messenger-call-float-button bx-messenger-call-float-button-mic'+(this.audioMuted? ' bx-messenger-call-float-button-mic-disabled':'')},
				events: {'click': BX.delegate(function(e)
				{
					this.audioMuted = !this.audioMuted;
					BX.desktop.onCustomEvent("main", "bxCallMuteMic", [this.audioMuted]);

					BX.toggleClass(BX.proxy_context, 'bx-messenger-call-float-button-mic-disabled');
					var text = BX.findChild(BX.proxy_context, {className : "bx-messenger-call-float-button-text"}, true);
					text.innerHTML = BX.message('IM_M_CALL_BTN_MIC')+' '+BX.message('IM_M_CALL_BTN_MIC_'+(this.audioMuted? 'OFF': 'ON'));

					BX.PreventDefault(e);
				}, this)},
				children: [
					BX.create("span", { props : { className : 'bx-messenger-call-float-button-icon'}}),
					BX.create("span", { props : { className : 'bx-messenger-call-float-button-text'}, html: BX.message('IM_M_CALL_BTN_MIC')+' '+BX.message('IM_M_CALL_BTN_MIC_'+(this.audioMuted? 'OFF': 'ON'))})
				]
			}),
			BX.create("div", {
				props : { className : 'bx-messenger-call-float-button bx-messenger-call-float-button-decline'},
				events: {'click': BX.delegate(function(e){
					BX.desktop.onCustomEvent("main", "bxCallDecline", []);
					BX.desktop.windowCommand('close');

					BX.PreventDefault(e);
				}, this)},
				children: [
					BX.create("span", { props : { className : 'bx-messenger-call-float-button-icon'}}),
					BX.create("span", { props : { className : 'bx-messenger-call-float-button-text'}, html: BX.message('IM_M_CALL_BTN_HANGUP')})
				]
			})
		]})
	]});

	this.desktop.drawOnPlaceholder(this.callOverlay);

	BX.desktop.setWindowMinSize({ Width: minCallWidth, Height: minCallHeight });
	BX.desktop.setWindowResizable(false);
	BX.desktop.setWindowClosable(false);
	BX.desktop.setWindowResizable(false);
	BX.desktop.setWindowTitle(BX.util.htmlspecialcharsback(BX.util.htmlspecialcharsback(title)));

	BX.desktop.setWindowPosition({X: STP_RIGHT, Y: STP_TOP, Width: minCallWidth, Height: minCallHeight, Mode: STP_FRONT});
	if (!BX.browser.IsMac())
		BX.desktop.setWindowPosition({X: STP_RIGHT, Y: STP_TOP, Width: minCallWidth, Height: minCallHeight, Mode: STP_FRONT});

	BX.desktop.addCustomEvent("bxCallChangeMainVideo", BX.delegate(function(src) {
		this.callOverlayVideoMain.src = src;
	}, this));
};

BX.IM.WebRTC.prototype.storageSet = function(params)
{
};

/* WebRTC Cloud Phone */
BX.IM.WebRTC.prototype.phoneSupport = function()
{
	return this.phoneEnabled && this.ready();
}

BX.IM.WebRTC.prototype.openKeyPad = function(e)
{
	this.phoneKeyPadPutPlusFlag = false
	if (!this.phoneSupport() && !(this.BXIM.desktopStatus && this.BXIM.desktopVersion >= 18))
	{
		if (!this.desktop.ready())
		{
			this.BXIM.openConfirm(BX.message('IM_CALL_NO_WEBRT'), [
				this.BXIM.platformName == ''? null: new BX.PopupWindowButton({
					text : BX.message('IM_M_CALL_BTN_DOWNLOAD'),
					className : "popup-window-button-accept",
					events : { click : BX.delegate(function() { window.open(BX.browser.IsMac()? "http://dl.bitrix24.com/b24/bitrix24_desktop.dmg": "http://dl.bitrix24.com/b24/bitrix24_desktop.exe", "desktopApp"); BX.proxy_context.popupWindow.close(); }, this) }
				}),
				new BX.PopupWindowButton({
					text : BX.message('IM_NOTIFY_CONFIRM_CLOSE'),
					className : "popup-window-button-decline",
					events : { click : function() { this.popupWindow.close(); } }
				})
			]);
		}
		return false;
	}

	if ((this.callInit && !this.callActive) || (this.callActive && !this.phoneCurrentCall))
	{
		if (this.desktop.run())
		{
			if (BX.desktop.lastTabTarget != 'im')
			{
				BX.desktop.changeTab(this.BXIM.dialogOpen? 'im': 'notify');
			}
			else
			{
				BX.desktop.closeTab('im-phone');
			}
		}
		return false;
	}
	if (this.callActive && this.desktop.run() && BX.hasClass(this.callOverlay, 'bx-messenger-call-overlay-line'))
	{
		BX.desktop.closeTab('im-phone');
		return false;
	}

	if (this.popupKeyPad != null)
	{
		this.popupKeyPad.close();
		return false;
	}

	if (this.messenger.popupMessenger)
	{
		if (!this.callActive)
		{
			if (this.desktop.run())
			{
				var bindElement = BX('bx-desktop-tab-im-phone');
				var offsetTop = -100;
				var offsetLeft = 50;
			}
			else
			{
				BX.addClass(this.messenger.popupContactListSearchCall, 'bx-messenger-input-search-call-active');
				var bindElement = this.messenger.popupContactListSearchCall;
				var offsetTop = 5;
				var offsetLeft = this.desktop.run()? -100: -75;
			}
		}
		else
		{
			var bindElement = BX('bx-messenger-call-overlay-button-keypad');
			var offsetTop = 7;
			var offsetLeft = this.desktop.run()? -90: -65;

			if (this.desktop.run())
				BX.desktop.closeTab('im-phone');
		}
	}
	else
	{
		var bindElement = this.notify.panelButtonCall;
		var offsetTop = 5;
		var offsetLeft = -75;
	}

	if (this.messenger.popupMessenger)
		this.messenger.popupMessenger.setClosingByEsc(false);

	this.popupKeyPad = new BX.PopupWindow('bx-messenger-popup-keypad', bindElement, {
		lightShadow : true,
		offsetTop: offsetTop,
		offsetLeft: offsetLeft,
		closeByEsc: true,
		angle : { position : this.desktop.run() && !this.callActive? "left": "top", offset: this.desktop.run()? (this.callActive? 120: 76): 92 },
		autoHide: true,
		zIndex: 200,
		events : {
			onPopupClose : function() { this.destroy() },
			onPopupDestroy : BX.delegate(function() {
				if (this.desktop.run())
				{
					if (BX.desktop.lastTabTarget != 'im')
					{
						BX.desktop.changeTab(this.BXIM.dialogOpen? 'im': 'notify');
					}
					else
					{
						BX.desktop.closeTab('im-phone');
					}
				}

				this.popupKeyPad = null;
				if (this.messenger.popupMessenger && !this.callInit)
				{
					this.messenger.popupMessenger.setClosingByEsc(true);
				}
				BX.removeClass(this.messenger.popupContactListSearchCall, 'bx-messenger-input-search-call-active');
			}, this)
		},
		content : BX.create("div", { props : { className : "bx-messenger-calc-wrap"+(this.desktop.run()? ' bx-messenger-calc-wrap-desktop': '') }, children: [
			BX.create("div", { props : { className : "bx-messenger-calc-body" }, children: [
				this.popupKeyPadButtons = BX.create("div", { props: {className: 'bx-messenger-calc-panel'}, children: [
					this.popupKeyPadInputDelete = BX.create("span", { props : { className : "bx-messenger-calc-panel-delete" }}),
					this.popupKeyPadInput = BX.create("input", {attrs: {'readonly': this.callActive? true: false, type: "text", value: '', placeholder: BX.message(this.callActive? 'IM_PHONE_PUT_DIGIT': 'IM_PHONE_PUT_NUMBER')}, props : { className : "bx-messenger-calc-panel-input" }})
				]}),
				this.popupKeyPadButtons = BX.create("div", { props : { className : "bx-messenger-calc-btns-block" }, children: [
					BX.create("span", { attrs: {'data-digit': 1}, props : { className : "bx-messenger-calc-btn bx-messenger-calc-btn-1"}, html: '<span class="bx-messenger-calc-btn-num"></span>'}),
					BX.create("span", { attrs: {'data-digit': 2}, props : { className : "bx-messenger-calc-btn bx-messenger-calc-btn-2"}, html: '<span class="bx-messenger-calc-btn-num"></span>'}),
					BX.create("span", { attrs: {'data-digit': 3}, props : { className : "bx-messenger-calc-btn bx-messenger-calc-btn-3"}, html: '<span class="bx-messenger-calc-btn-num"></span>'}),
					BX.create("span", { attrs: {'data-digit': 4}, props : { className : "bx-messenger-calc-btn bx-messenger-calc-btn-4"}, html: '<span class="bx-messenger-calc-btn-num"></span>'}),
					BX.create("span", { attrs: {'data-digit': 5}, props : { className : "bx-messenger-calc-btn bx-messenger-calc-btn-5"}, html: '<span class="bx-messenger-calc-btn-num"></span>'}),
					BX.create("span", { attrs: {'data-digit': 6}, props : { className : "bx-messenger-calc-btn bx-messenger-calc-btn-6"}, html: '<span class="bx-messenger-calc-btn-num"></span>'}),
					BX.create("span", { attrs: {'data-digit': 7}, props : { className : "bx-messenger-calc-btn bx-messenger-calc-btn-7"}, html: '<span class="bx-messenger-calc-btn-num"></span>'}),
					BX.create("span", { attrs: {'data-digit': 8}, props : { className : "bx-messenger-calc-btn bx-messenger-calc-btn-8"}, html: '<span class="bx-messenger-calc-btn-num"></span>'}),
					BX.create("span", { attrs: {'data-digit': 9}, props : { className : "bx-messenger-calc-btn bx-messenger-calc-btn-9"}, html: '<span class="bx-messenger-calc-btn-num"></span>'}),
					BX.create("span", { attrs: {'data-digit': '*'}, props : { className : "bx-messenger-calc-btn bx-messenger-calc-btn-10"}, html: '<span class="bx-messenger-calc-btn-num"></span>'}),
					BX.create("span", { attrs: {'data-digit': '0'}, props : { className : "bx-messenger-calc-btn bx-messenger-calc-btn-0"}, html: '<span class="bx-messenger-calc-btn-num"></span>'}),
					BX.create("span", { attrs: {'data-digit': '#'}, props : { className : "bx-messenger-calc-btn bx-messenger-calc-btn-11"}, html: '<span class="bx-messenger-calc-btn-num"></span>'})
				]})
			]}),
			this.callActive? null: BX.create("div", { props : { className : "bx-messenger-call-btn-wrap" }, children: [
				this.popupKeyPadCall = BX.create("span", { props : { className : "bx-messenger-call-btn" }, children: [
					BX.create("span", { props : { className : "bx-messenger-call-btn-icon" }}),
					BX.create("span", { props : { className : "bx-messenger-call-btn-text" }, html: BX.message('IM_PHONE_CALL')})
				]})
			]})
		]})
	});
	this.popupKeyPad.show();
	this.popupKeyPadInput.focus();
	BX.bind(this.popupKeyPad.popupContainer, "click", BX.PreventDefault);

	BX.bind(this.popupKeyPadInput, "keydown", BX.delegate(function(e) {
		if (e.keyCode == 13)
		{
			this.BXIM.phoneTo(this.popupKeyPadInput.value);
		}
		else if (e.keyCode == 37 || e.keyCode == 39 || e.keyCode == 8 || e.keyCode == 107 || e.keyCode == 46 || e.keyCode == 35 || e.keyCode == 36) // left, right, backspace, num plus, home, end
		{}
		else if ((e.keyCode == 61 || e.keyCode == 187 || e.keyCode == 51 || e.keyCode == 56) && e.shiftKey) // +
		{}
		else if ((e.keyCode == 67 || e.keyCode == 86 || e.keyCode == 65 || e.keyCode == 88) && (e.metaKey || e.ctrlKey)) // ctrl+v/c/a/x
		{}
		else if (e.keyCode >= 48 && e.keyCode <= 57 && !e.shiftKey) // 0-9
		{}
		else if (e.keyCode >= 96 && e.keyCode <= 105 && !e.shiftKey) // extra 0-9
		{}
		else
		{
			return BX.PreventDefault(e);
		}
	}, this));

	var correctNumber = BX.delegate(function() {
		if (!this.callActive && this.popupKeyPadInput.value.length > 0)
		{
			if (this.popupKeyPadInput.parentNode.className == 'bx-messenger-calc-panel')
				BX.addClass(this.popupKeyPadInput.parentNode, 'bx-messenger-calc-panel-active');
		}
		else
		{
			if (this.popupKeyPadInput.parentNode.className == 'bx-messenger-calc-panel bx-messenger-calc-panel-active')
				BX.removeClass(this.popupKeyPadInput.parentNode, 'bx-messenger-calc-panel-active');
		}
		this.popupKeyPadInput.focus();
	}, this);

	BX.bind(this.popupKeyPadCall, "click", BX.delegate(function(e) {
		this.BXIM.phoneTo(this.popupKeyPadInput.value);
	}, this));
	BX.bind(this.popupKeyPadInputDelete, "click", BX.delegate(function(e) {
		if (this.callActive)
			return false;

		this.popupKeyPadInput.value = this.popupKeyPadInput.value.substr(0, this.popupKeyPadInput.value.length-1);
		correctNumber();
	}, this));
	BX.bind(this.popupKeyPadInput, "keyup",  correctNumber);

	BX.bindDelegate(this.popupKeyPadButtons, "mousedown", {className: 'bx-messenger-calc-btn'}, BX.delegate(function() {
		var key = BX.proxy_context.getAttribute('data-digit');
		if (key != 0)
			return false;

		this.phoneKeyPadPutPlus();
	}, this));

	BX.bindDelegate(this.popupKeyPadButtons, "mouseup", {className: 'bx-messenger-calc-btn'}, BX.delegate(function() {
		var key = BX.proxy_context.getAttribute('data-digit');
		if (key == 0)
		{
			this.phoneKeyPadPutPlusEnd();
		}
		else
		{
			this.popupKeyPadInput.value = this.popupKeyPadInput.value+''+key;
		}
		this.phoneSendDTMF(key);
		correctNumber();
	}, this));

	return e? BX.PreventDefault(e): true;
};

BX.IM.WebRTC.prototype.phoneKeyPadPutPlus = function()
{
	this.phoneKeyPadPutPlusTimeout = setTimeout(BX.delegate(function(){
		this.phoneKeyPadPutPlusFlag = true;
		this.popupKeyPadInput.value = this.popupKeyPadInput.value+'+';
	},this), 500);
}

BX.IM.WebRTC.prototype.phoneKeyPadPutPlusEnd = function()
{
	clearTimeout(this.phoneKeyPadPutPlusTimeout);
	if (!this.phoneKeyPadPutPlusFlag)
		this.popupKeyPadInput.value = this.popupKeyPadInput.value+'0';

	this.phoneKeyPadPutPlusFlag = false;
}

BX.IM.WebRTC.prototype.phoneCount = function(numbers)
{
	var count = 0;
	if (typeof (numbers) === 'object')
	{
		if (numbers.PERSONAL_MOBILE)
			count++;
		else if (numbers.PERSONAL_PHONE)
			count++;
		else if (numbers.WORK_PHONE)
			count++;
	}

	return count;
}

BX.IM.WebRTC.prototype.phoneCorrect = function(number)
{
	number = BX.util.trim(number+'');

	if (number.substr(0, 2) == '+8')
	{
		number = '008'+number.substr(2);
	}
	number = number.replace(/[^0-9\#\*]/g, '');
	if (number.substr(0, 2) == '80' || number.substr(0, 2) == '81' || number.substr(0, 2) == '82')
	{
	}
	else if (number.substr(0, 2) == '00')
	{
		number = number.substr(2);
	}
	else if (number.substr(0, 3) == '011')
	{
		number = number.substr(3);
	}
	else if (number.substr(0, 1) == '8')
	{
		number = '7'+number.substr(1);
	}
	else if (number.substr(0, 1) == '0')
	{
		number = number.substr(1);
	}

	return number;
}

BX.IM.WebRTC.prototype.phoneCall = function(number, params)
{
	if (this.debug)
		this.phoneLog(number, params);

	this.phoneNumberUser = BX.util.htmlspecialchars(number);

	number = this.phoneCorrect(number);
	if (typeof(params) != 'object')
		params = {};

	if (params.RECORDING != 'Y')
		delete params.RECORDING;

	params.APPLICATION = params.APPLICATION? params.APPLICATION: 'call';

	if (number.length <= 3 && number.length >= 1)
	{
		this.BXIM.openConfirm({title: BX.message('IM_PHONE_WRONG_NUMBER'), message: BX.message('IM_PHONE_NO_EMERGENCY')});
		return false;
	}
	if (number.length < 10)
	{
		this.BXIM.openConfirm({title: BX.message('IM_PHONE_WRONG_NUMBER'), message: BX.message('IM_PHONE_WRONG_NUMBER_DESC')});
		return false;
	}

	if (this.desktop.run() && BX.desktop.currentTab != 'im')
	{
		BX.desktop.changeTab('im');
	}

	if (this.popupKeyPad)
		this.popupKeyPad.close();

	if (!this.phoneSupport())
	{
		if (!this.desktop.ready())
		{
			this.BXIM.openConfirm(BX.message('IM_CALL_NO_WEBRT'), [
				new BX.PopupWindowButton({
					text : BX.message('IM_M_CALL_BTN_DOWNLOAD'),
					className : "popup-window-button-accept",
					events : { click : BX.delegate(function() { window.open(BX.browser.IsMac()? "http://dl.bitrix24.com/b24/bitrix24_desktop.dmg": "http://dl.bitrix24.com/b24/bitrix24_desktop.exe", "desktopApp"); BX.proxy_context.popupWindow.close(); }, this) }
				}),
				new BX.PopupWindowButton({
					text : BX.message('IM_NOTIFY_CONFIRM_CLOSE'),
					className : "popup-window-button-decline",
					events : { click : function() { this.popupWindow.close(); } }
				})
			]);
		}
		return false;
	}

	if (!this.messenger.popupMessenger)
		this.messenger.openMessenger();

	if (!this.callActive && !this.callInit)
	{
		this.initiator = true;
		this.callInitUserId = this.BXIM.userId;
		this.callInit = true;
		this.callActive = false;
		this.callUserId = 0;
		this.callChatId = 0;
		this.callToGroup = 0;
		this.callGroupUsers = [];
		this.phoneNumber = number;
		this.phoneApplication = params.APPLICATION;
		this.phoneParams = params;

		this.callOverlayShow({
			toUserId : 0,
			phoneNumber : this.phoneNumber,
			callTitle : this.phoneNumberUser,
			fromUserId : this.BXIM.userId,
			callToGroup : false,
			callToPhone : true,
			video : false,
			status : BX.message('IM_M_CALL_ST_CONNECT'),
			buttons : [
				{
					text: BX.message('IM_M_CALL_BTN_HANGUP'),
					className: 'bx-messenger-call-overlay-button-hangup',
					events: {
						click : BX.delegate(function() {
							this.phoneCallFinish();
							this.callAbort();
							this.callOverlayClose();
						}, this)
					}
				},
				{
					text: BX.message('IM_M_CALL_BTN_CHAT'),
					className: 'bx-messenger-call-overlay-button-chat',
					showInMaximize: true,
					events: { click : BX.delegate(this.callOverlayToggleSize, this) }
				},
				{
					title: BX.message('IM_M_CALL_BTN_MAXI'),
					className: 'bx-messenger-call-overlay-button-maxi',
					showInMinimize: true,
					events: { click : BX.delegate(this.callOverlayToggleSize, this) }
				}
			]
		});
		this.BXIM.playSound("start");

		if (false)
		{
			this.phoneCurrentCall = true;
			this.callActive = true;
			this.phoneOnCallConnected();
		}
		if (!this.phoneLogin || !this.phoneAccount)
		{
			BX.ajax({
				url: '/bitrix/components/bitrix/im.messenger/call.ajax.php?PHONE_AUTHORIZE',
				method: 'POST',
				dataType: 'json',
				timeout: 30,
				data: {'IM_PHONE' : 'Y', 'COMMAND': 'authorize', 'UPDATE_INFO': this.phoneCheckBalance? 'Y': 'N', 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
				onsuccess: BX.delegate(function(data)
				{
					this.phoneCheckBalance = false;
					if (data.ERROR == '')
					{
						if (data.HR_PHOTO)
						{
							for (var i in data.HR_PHOTO)
								this.messenger.hrphoto[i] = data.HR_PHOTO[i];

							this.callOverlayUpdatePhoto();
						}

						this.phoneLogin = data.LOGIN;
						this.phoneAccount = data.ACCOUNT;
						this.phoneCallerID = data.CALLERID;
						this.phoneAvailable = data.ENABLE? data.ENABLE: 0;

						this.phoneApiInit();
					}
					else
					{
						this.callOverlayDeleteEvents();
						this.callOverlayProgress('offline');

						this.phoneLog('onetimekey', data.ERROR, data.CODE);
						if (data.ERROR == 'AUTHORIZE_ERROR')
							this.callAbort(BX.message('IM_PHONE_ERROR_CONNECT'));
						else
							this.callAbort(data.ERROR+(this.debug? '<br>('+BX.message('IM_ERROR_CODE')+': '+data.CODE+')': ''));

						this.callOverlayButtons([{
							text: BX.message('IM_M_CALL_BTN_CLOSE'),
							className: 'bx-messenger-call-overlay-button-close',
							events: {
								click : BX.delegate(function() {
									this.phoneCallFinish();
									this.callOverlayClose();
								}, this)
							}
						}]);
					}
				}, this),
				onfailure: BX.delegate(function() {
					this.phoneCallFinish();
					this.callAbort(BX.message('IM_M_CALL_ERR'));
					this.callOverlayClose();
				}, this)
			});
		}
		else
		{
			this.phoneApiInit();
		}
	}
}

BX.IM.WebRTC.prototype.phoneIncomingAnswer = function()
{
	this.callSelfDisabled = true;
	this.phoneCommand('answer', {'CALL_ID' : this.phoneCallId});

	if (this.popupKeyPad)
		this.popupKeyPad.close();

	this.callOverlayButtons([
		{
			text: BX.message('IM_M_CALL_BTN_HANGUP'),
			className: 'bx-messenger-call-overlay-button-hangup',
			events: {
				click : BX.delegate(function() {
					this.phoneCallFinish();
					this.callAbort();
					this.callOverlayClose();
				}, this)
			}
		},
		{
			text: BX.message('IM_M_CALL_BTN_CHAT'),
			className: 'bx-messenger-call-overlay-button-chat',
			showInMaximize: true,
			events: { click : BX.delegate(this.callOverlayToggleSize, this) }
		},
		{
			title: BX.message('IM_M_CALL_BTN_MAXI'),
			className: 'bx-messenger-call-overlay-button-maxi',
			showInMinimize: true,
			events: { click : BX.delegate(this.callOverlayToggleSize, this) }
		}
	]);

	if (this.messenger.popupMessenger == null)
	{
		this.messenger.openMessenger(this.callUserId);
		this.callOverlayToggleSize(false);
	}

	BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-maxi ');
	BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-call');
	BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-mini');
	BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-line');
	BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-call-audio');

	if (!this.phoneLogin || !this.phoneAccount)
	{
		BX.ajax({
			url: '/bitrix/components/bitrix/im.messenger/call.ajax.php?PHONE_AUTHORIZE',
			method: 'POST',
			dataType: 'json',
			timeout: 30,
			data: {'IM_PHONE' : 'Y', 'COMMAND': 'authorize', 'UPDATE_INFO': this.phoneCheckBalance? 'Y': 'N', 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
			onsuccess: BX.delegate(function(data)
			{
				this.phoneCheckBalance = false;
				if (data.ERROR == '')
				{
					if (data.HR_PHOTO)
					{
						for (var i in data.HR_PHOTO)
							this.messenger.hrphoto[i] = data.HR_PHOTO[i];

						this.callOverlayUpdatePhoto();
					}

					this.phoneLogin = data.LOGIN;
					this.phoneAccount = data.ACCOUNT;
					this.phoneCallerID = data.CALLERID;
					this.phoneAvailable = data.ENABLE? data.ENABLE: 0;

					this.phoneApiInit();
				}
				else
				{
					this.phoneCallFinish();
					this.callOverlayProgress('offline');

					this.phoneLog('onetimekey', data.ERROR, data.CODE);
					if (data.ERROR == 'AUTHORIZE_ERROR')
						this.callAbort(BX.message('IM_PHONE_ERROR_CONNECT'));
					else
						this.callAbort(data.ERROR+(this.debug? '<br>('+BX.message('IM_ERROR_CODE')+': '+data.CODE+')': ''));

					this.callOverlayButtons([{
						text: BX.message('IM_M_CALL_BTN_CLOSE'),
						className: 'bx-messenger-call-overlay-button-close',
						events: {
							click : BX.delegate(function() {
								this.phoneCallFinish();
								this.callOverlayClose();
							}, this)
						}
					}]);
				}
			}, this),
			onfailure: BX.delegate(function() {
				this.phoneCallFinish();
				this.callAbort(BX.message('IM_M_CALL_ERR'));
				this.callOverlayClose();
			}, this)
		});
	}
	else
	{
		this.phoneApiInit();
	}

}

BX.IM.WebRTC.prototype.phoneApiInit = function()
{
	if (!this.phoneSupport())
		return false;

	if (!this.phoneAvailable || this.phoneAvailable == 1 && !this.phoneIncoming)
	{
		this.phoneLogin  = '';
		this.phoneAccount  = '';

		this.phoneCallFinish();
		this.callOverlayProgress('offline');

		this.callAbort(BX.message('IM_PHONE_NO_MONEY')+(this.BXIM.bitrix24Admin? '<br>'+BX.message('IM_PHONE_PAY_URL'): ''));

		this.callOverlayButtons([{
			text: BX.message('IM_M_CALL_BTN_CLOSE'),
			className: 'bx-messenger-call-overlay-button-close',
			events: {
				click : BX.delegate(function() {
					this.phoneCallFinish();
					this.callOverlayClose();
				}, this)
			}
		}]);

		return false;
	}

	if (!this.phoneLogin || !this.phoneAccount)
	{
		this.phoneCallFinish();
		this.callOverlayProgress('offline');
		this.callAbort(BX.message('IM_PHONE_ERROR'));
		this.callOverlayButtons([{
			text: BX.message('IM_M_CALL_BTN_CLOSE'),
			className: 'bx-messenger-call-overlay-button-close',
			events: {
				click : BX.delegate(function() {
					this.callOverlayClose();
				}, this)
			}
		}]);

		return false;
	}

	if (this.phoneAPI)
	{
		if (this.phoneSDKinit)
		{
			if (this.callInitUserId == this.BXIM.userId)
				this.phoneCreateCall();
		}
		else
		{
			this.phoneOnSDKReady();
		}
		return true;
	}

	this.phoneAPI = VoxImplant.getInstance();
	this.phoneAPI.addEventListener(VoxImplant.Events.SDKReady, BX.delegate(this.phoneOnSDKReady, this));
	this.phoneAPI.addEventListener(VoxImplant.Events.ConnectionEstablished, BX.delegate(this.phoneOnConnectionEstablished, this));
	this.phoneAPI.addEventListener(VoxImplant.Events.ConnectionFailed, BX.delegate(this.phoneOnConnectionFailed, this));
	this.phoneAPI.addEventListener(VoxImplant.Events.ConnectionClosed, BX.delegate(this.phoneOnConnectionClosed, this));
	this.phoneAPI.addEventListener(VoxImplant.Events.IncomingCall, BX.delegate(this.phoneOnIncomingCall, this));
	this.phoneAPI.addEventListener(VoxImplant.Events.AuthResult, BX.delegate(this.phoneOnAuthResult, this));
	this.phoneAPI.addEventListener(VoxImplant.Events.MicAccessResult, BX.delegate(this.phoneOnMicResult, this));
	this.phoneAPI.addEventListener(VoxImplant.Events.SourcesInfoUpdated, BX.delegate(this.phoneOnInfoUpdated, this));

	var progressToneCountry = this.BXIM.language.toUpperCase();
	if (progressToneCountry == 'EN')
		progressToneCountry = 'US';

	this.phoneAPI.init({ useRTCOnly: true, micRequired: true, videoSupport: false, progressTone: true, progressToneCountry: progressToneCountry });
	this.phoneSDKinit = true;

	return true;
}

BX.IM.WebRTC.prototype.phoneOnSDKReady = function()
{
	this.phoneLog('SDK ready');
	this.phoneAPI.connect();

	clearTimeout(this.callDialogAllowTimeout);
	this.callDialogAllowTimeout = setTimeout(BX.delegate(function(){
		this.callDialogAllowShow();
	}, this), 1500);

	this.callOverlayProgress('wait');
	this.callOverlayStatus(BX.message('IM_M_CALL_ST_WAIT_ACCESS'));
}

BX.IM.WebRTC.prototype.phoneOnConnectionEstablished = function()
{
	this.phoneLog('Connection established', this.phoneAPI.connected());
	this.phoneAPI.requestOneTimeLoginKey(this.phoneLogin+"@"+this.phoneApplication+'.'+this.phoneAccount);
}

BX.IM.WebRTC.prototype.phoneOnConnectionFailed = function()
{
	this.phoneLog('Connection failed');
}

BX.IM.WebRTC.prototype.phoneOnConnectionClosed = function()
{
	this.phoneLog('Connection closed');
	this.phoneSDKinit = false;
}

BX.IM.WebRTC.prototype.phoneOnIncomingCall = function(params)
{
	this.phoneCurrentCall = params.call;
	this.phoneCurrentCall.addEventListener(VoxImplant.CallEvents.Connected, BX.delegate(this.phoneOnCallConnected, this));
	this.phoneCurrentCall.addEventListener(VoxImplant.CallEvents.Disconnected, BX.delegate(this.phoneOnCallDisconnected, this));
	this.phoneCurrentCall.addEventListener(VoxImplant.CallEvents.Failed, BX.delegate(this.phoneOnCallFailed, this));
	this.phoneCurrentCall.answer();
}

BX.IM.WebRTC.prototype.phoneOnAuthResult = function(e)
{
	if (e.result)
	{
		this.phoneLog('Authorize result', 'success');
		if (this.phoneIncoming)
		{
			this.phoneCommand('ready', {'CALL_ID': this.phoneCallId});
		}
		else if (this.callInitUserId == this.BXIM.userId)
		{
			this.phoneCreateCall();
		}

	}
	else if (e.code == 302)
	{
		BX.ajax({
			url: '/bitrix/components/bitrix/im.messenger/call.ajax.php?PHONE_ONETIMEKEY',
			method: 'POST',
			dataType: 'json',
			timeout: 30,
			data: {'IM_PHONE' : 'Y', 'COMMAND': 'onetimekey', 'KEY': e.key, 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
			onsuccess: BX.delegate(function(data)
			{
				if (data.ERROR == '')
				{
					this.phoneLog('auth with', this.phoneLogin+"@"+this.phoneApplication+'.'+this.phoneAccount);
					this.phoneAPI.loginWithOneTimeKey(this.phoneLogin+"@"+this.phoneApplication+'.'+this.phoneAccount, data.HASH);
				}
				else
				{
					this.phoneCallFinish();
					this.callOverlayProgress('offline');

					this.phoneLog('onetimekey', data.ERROR, data.CODE);
					if (data.CODE)
						this.callAbort(BX.message('IM_PHONE_ERROR_CONNECT'));
					else
						this.callAbort(data.ERROR+(this.debug? '<br>('+BX.message('IM_ERROR_CODE')+': '+data.CODE+')': ''));

					this.callOverlayButtons([{
						text: BX.message('IM_M_CALL_BTN_CLOSE'),
						className: 'bx-messenger-call-overlay-button-close',
						events: {
							click : BX.delegate(function() {
								this.callOverlayClose();
							}, this)
						}
					}]);
				}
			}, this),
			onfailure: BX.delegate(function() {
				this.callAbort(BX.message('IM_M_CALL_ERR'));
				this.phoneCallFinish();
				this.callOverlayClose();
			}, this)
		});
	}
	else
	{
		if (e.code == 401 || e.code == 403 || e.code == 404)
		{
			this.callAbort(BX.message('IM_PHONE_401'));
			this.phoneCommand('authorize_error');
		}
		else
		{
			this.callAbort(BX.message('IM_M_CALL_ERR'));
		}
		this.callOverlayProgress('offline');
		this.phoneCallFinish();
		this.callOverlayButtons([{
			text: BX.message('IM_M_CALL_BTN_CLOSE'),
			className: 'bx-messenger-call-overlay-button-close',
			events: {
				click : BX.delegate(function() {
					this.callOverlayClose();
				}, this)
			}
		}]);
		this.phoneLog('Authorize result', 'failed', e.code);
		this.phoneAccount = '';
		this.phoneLogin = '';
	}
}

BX.IM.WebRTC.prototype.phoneOnMicResult = function(e)
{
	this.phoneMicAccess = e.result;
	this.phoneLog('Mic Access Allowed', e.result);

	clearTimeout(this.callDialogAllowTimeout);
	if (this.callDialogAllow)
		this.callDialogAllow.close();

	if (e.result)
	{
		this.callOverlayProgress('connect');
		this.callOverlayStatus(BX.message('IM_M_CALL_ST_CONNECT'));
	}
	else
	{
		this.phoneCallFinish();
		this.callOverlayProgress('offline');
		this.callAbort(BX.message('IM_M_CALL_ST_NO_ACCESS'));
		this.callOverlayButtons([{
			text: BX.message('IM_M_CALL_BTN_CLOSE'),
			className: 'bx-messenger-call-overlay-button-close',
			events: {
				click : BX.delegate(function() {
					this.callOverlayClose();
				}, this)
			}
		}]);
	}
}

BX.IM.WebRTC.prototype.phoneOnInfoUpdated = function(e)
{
	this.phoneLog('Info updated', this.phoneAPI.audioSources(), this.phoneAPI.videoSources());
}

BX.IM.WebRTC.prototype.phoneCreateCall = function()
{
	this.phoneParams['CALLER_ID'] = '';
	this.phoneLog('Call params: ', this.phoneNumber, this.phoneParams);
	if (!this.phoneAPI.connected())
	{
		this.phoneOnSDKReady();
		return false;
	}

	this.phoneAPI.setOperatorACDStatus('ONLINE');

	this.phoneCurrentCall = this.phoneAPI.call(this.phoneNumber, false, JSON.stringify(this.phoneParams));
	this.phoneCurrentCall.addEventListener(VoxImplant.CallEvents.Connected, BX.delegate(this.phoneOnCallConnected, this));
	this.phoneCurrentCall.addEventListener(VoxImplant.CallEvents.Disconnected, BX.delegate(this.phoneOnCallDisconnected, this));
	this.phoneCurrentCall.addEventListener(VoxImplant.CallEvents.Failed, BX.delegate(this.phoneOnCallFailed, this));
	this.phoneCurrentCall.addEventListener(VoxImplant.CallEvents.ProgressToneStart, BX.delegate(this.phoneOnProgressToneStart, this));
	this.phoneCurrentCall.addEventListener(VoxImplant.CallEvents.ProgressToneStop, BX.delegate(this.phoneOnProgressToneStop, this));

	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/call.ajax.php?PHONE_INIT',
		method: 'POST',
		dataType: 'json',
		timeout: 30,
		data: {'IM_PHONE' : 'Y', 'COMMAND': 'init', 'NUMBER' : this.phoneNumber, 'NUMBER_USER' : BX.util.htmlspecialcharsback(this.phoneNumberUser), 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
		onsuccess: BX.delegate(function(data){
			if (data.ERROR == '')
			{
				if (!(data.HR_PHOTO.length == 0))
				{
					for (var i in data.HR_PHOTO)
						this.messenger.hrphoto[i] = data.HR_PHOTO[i];

					this.callOverlayUserId = data.DIALOG_ID;
					this.callOverlayPhotoCompanion.setAttribute('data-userId', this.callOverlayUserId);
					this.callOverlayUpdatePhoto();
				}
				else
				{
					this.callOverlayChatId = data.DIALOG_ID.substr(4);
				}
				if (data.CRM && data.CRM.FOUND)
				{
					this.phoneCrm = data.CRM;
					this.callOverlayDrawCrm();
				}
				this.messenger.openMessenger(data.DIALOG_ID);
				this.callOverlayToggleSize(false);
			}
		}, this)
	});
}

BX.IM.WebRTC.prototype.phoneOnCallConnected = function(e)
{
	this.phoneLog('Call connected', e);
	this.callOverlayButtons([
		{
			text: BX.message('IM_M_CALL_BTN_HANGUP'),
			className: 'bx-messenger-call-overlay-button-hangup',
			events: {
				click : BX.delegate(function() {
					this.phoneCallFinish();
					this.callAbort();
					this.BXIM.playSound('stop');
					this.callOverlayClose();
				}, this)
			}
		},
		{
			title: BX.message('IM_M_CALL_BTN_MIC_TITLE'),
			id: 'bx-messenger-call-overlay-button-mic',
			className: 'bx-messenger-call-overlay-button-mic '+(this.phoneMicMuted? ' bx-messenger-call-overlay-button-mic-off': ''),
			events: {
				click : BX.delegate(function() {
					this.phoneToggleAudio();
					var icon = BX.findChild(BX.proxy_context, {className : "bx-messenger-call-overlay-button-mic"}, true);
					if (icon)
						BX.toggleClass(icon, 'bx-messenger-call-overlay-button-mic-off');
				}, this)
			}
		},
		{
			title: BX.message('IM_PHONE_OPEN_KEYPAD'),
			className: 'bx-messenger-call-overlay-button-keypad',
			events: { click : BX.delegate(function(e){
				this.openKeyPad(e)
			}, this) }
		},
		{
			title: BX.message('IM_M_CALL_BTN_HISTORY_2'),
			className: 'bx-messenger-call-overlay-button-history2',
			events: { click : BX.delegate(function(){
				this.messenger.openHistory(this.messenger.currentTab);
			}, this) }
		},
		{
			title: BX.message('IM_M_CALL_BTN_CHAT_2'),
			className: 'bx-messenger-call-overlay-button-chat2',
			showInMaximize: true,
			events: { click : BX.delegate(this.callOverlayToggleSize, this) }
		},
		{
			title: BX.message('IM_M_CALL_BTN_MAXI'),
			className: 'bx-messenger-call-overlay-button-maxi',
			showInMinimize: true,
			events: { click : BX.delegate(this.callOverlayToggleSize, this) }
		},
		this.desktop.ready()? null: {
			title: BX.message('IM_M_CALL_BTN_FULL'),
			className: 'bx-messenger-call-overlay-button-full',
			events: { click : BX.delegate(this.overlayEnterFullScreen, this) }
		}
	]);

	BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-maxi');
	BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-mini');
	BX.removeClass(this.callOverlay, 'bx-messenger-call-overlay-line');
	BX.addClass(this.callOverlay, 'bx-messenger-call-overlay-call');

	this.callOverlayProgress('online');
	this.callOverlayStatus(BX.message('IM_M_CALL_ST_ONLINE'));
	this.callActive = true;
	if (!this.BXIM.windowFocus)
		this.desktop.openCallFloatDialog();

	this.phoneCommand('start', {'CALL_ID': this.phoneCallId});
}

BX.IM.WebRTC.prototype.phoneOnCallDisconnected = function(e)
{
	this.phoneLog('Call disconnected', this.phoneCurrentCall? this.phoneCurrentCall.id(): '-', this.phoneCurrentCall? this.phoneCurrentCall.state(): '-');

	if (this.phoneCurrentCall)
	{
		this.phoneCallFinish();
		this.callOverlayDeleteEvents();
		this.callOverlayClose();
		this.BXIM.playSound('stop');
	}

	if (this.phoneAPI && this.phoneAPI.connected())
	{
		setTimeout(BX.delegate(function(){
			if (this.phoneAPI && this.phoneAPI.connected())
				this.phoneAPI.disconnect();
		}, this), 500)
	}
}

BX.IM.WebRTC.prototype.phoneOnCallFailed = function(e)
{
	this.phoneLog('Call failed', e.code, e.reason);

	var reason = BX.message('IM_M_CALL_ST_DECLINE');
	if (e.code == 603 && this.phoneRinging == 0)
	{
		reason = BX.message('IM_PHONE_END');
	}
	else if (e.code == 603 && this.phoneRinging > 0)
	{
		reason = BX.message('IM_M_CALL_ST_DECLINE');
	}
	else if (e.code == 408)
	{
		reason = BX.message('IM_PHONE_NO_ANSWER');
	}
	else if (e.code == 403)
	{
		reason = BX.message('IM_PHONE_403');
		this.phoneAccount = '';
		this.phoneLogin = '';
		this.phoneCheckBalance = true;
	}

	this.phoneCallFinish();
	if (e.code == 408 || e.code == 403)
	{
		if (this.phoneAPI && this.phoneAPI.connected())
		{
			setTimeout(BX.delegate(function(){
				if (this.phoneAPI && this.phoneAPI.connected())
					this.phoneAPI.disconnect();
			}, this), 500)
		}
	}
	this.callOverlayProgress('offline');
	this.callAbort(reason);
	this.callOverlayButtons([{
		text: BX.message('IM_M_CALL_BTN_CLOSE'),
		className: 'bx-messenger-call-overlay-button-close',
		events: {
			click : BX.delegate(function() {
				this.callOverlayClose();
			}, this)
		}
	}]);
}

BX.IM.WebRTC.prototype.phoneOnProgressToneStart = function(e)
{
	this.phoneLog('Progress tone start', this.phoneCurrentCall.id());
	this.callOverlayStatus(BX.message('IM_PHONE_WAIT_ANSWER'));
	this.phoneRinging++;
}

BX.IM.WebRTC.prototype.phoneOnProgressToneStop = function(e)
{
	this.phoneLog('Progress tone stop', this.phoneCurrentCall.id());
}

BX.IM.WebRTC.prototype.phoneSendDTMF = function(key)
{
	if (!this.phoneCurrentCall)
		return false;

	this.phoneLog('Send DTMF code', this.phoneCurrentCall.id(), key);

	this.phoneCurrentCall.sendTone(key);
}

BX.IM.WebRTC.prototype.phoneToggleAudio = function()
{
	if (!this.phoneCurrentCall)
		return false;

	if (this.phoneMicMuted)
	{
		this.phoneCurrentCall.unmuteMicrophone();
	}
	else
	{
		this.phoneCurrentCall.muteMicrophone();
	}
	this.phoneMicMuted = !this.phoneMicMuted;
}

BX.IM.WebRTC.prototype.phoneCallFinish = function()
{
	if (this.callInit && this.phoneIncoming)
	{
		this.phoneCommand('skip', {'CALL_ID': this.phoneCallId});
	}

	if (this.phoneCurrentCall)
	{
		try { this.phoneCurrentCall.hangup(); } catch (e) {}
		this.phoneCurrentCall = null;
		this.phoneLog('Call hangup call');
	}
	else if (this.phoneAPI && this.phoneAPI.connected())
	{
		setTimeout(BX.delegate(function(){
			if (this.phoneAPI && this.phoneAPI.connected())
				this.phoneAPI.disconnect();
		}, this), 500)
	}

	if (this.popupKeyPad)
		this.popupKeyPad.close();

	this.phoneRinging = 0;
	this.phoneIncoming = false;
	this.phoneCallId = '';
	this.phoneNumber = '';
	this.phoneNumberUser = '';
	this.phoneApplication = '';
	this.phoneParams = {};
	this.phoneCrm = {};
	this.phoneMicMuted = false;
	this.phoneMicAccess = false;
}

BX.IM.WebRTC.prototype.phoneCommand = function(command, params, async)
{
	if (!this.phoneSupport())
		return false;

	async = async != false;
	params = typeof(params) == 'object' ? params: {};

	BX.ajax({
		url: '/bitrix/components/bitrix/im.messenger/call.ajax.php?PHONE_SHARED',
		method: 'POST',
		dataType: 'json',
		timeout: 30,
		async: async,
		data: {'IM_PHONE' : 'Y', 'COMMAND': command, 'PARAMS' : JSON.stringify(params), 'IM_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()}
	});
};

BX.IM.WebRTC.prototype.phoneNotifyWait = function(chatId, callId, callerId)
{
	if (this.debug)
		this.phoneLog('incoming call', chatId, callId, callerId);

	if (!this.phoneSupport())
	{
		if (!this.desktop.ready())
		{
			this.BXIM.openConfirm(BX.message('IM_CALL_NO_WEBRT'), [
				new BX.PopupWindowButton({
					text : BX.message('IM_M_CALL_BTN_DOWNLOAD'),
					className : "popup-window-button-accept",
					events : { click : BX.delegate(function() { window.open(BX.browser.IsMac()? "http://dl.bitrix24.com/b24/bitrix24_desktop.dmg": "http://dl.bitrix24.com/b24/bitrix24_desktop.exe", "desktopApp"); BX.proxy_context.popupWindow.close(); }, this) }
				}),
				new BX.PopupWindowButton({
					text : BX.message('IM_NOTIFY_CONFIRM_CLOSE'),
					className : "popup-window-button-decline",
					events : { click : function() { this.popupWindow.close(); } }
				})
			]);
		}
		return false;
	}

	this.phoneNumberUser = BX.util.htmlspecialchars(callerId);
	callerId = this.phoneCorrect(callerId);

	if (!this.callActive && !this.callInit)
	{
		this.initiator = true;
		this.callInitUserId = 0;
		this.callInit = true;
		this.callActive = false;
		this.callUserId = 0;
		this.callChatId = 0;
		this.callToGroup = 0;
		this.callGroupUsers = [];
		this.phoneIncoming = true;
		this.phoneCallId = callId;
		this.phoneNumber = callerId;
		this.phoneApplication = 'incoming';
		this.phoneParams = {};

		this.callOverlayShow({
			toUserId : this.BXIM.userId,
			phoneNumber : this.phoneNumber,
			callTitle : this.phoneNumberUser,
			fromUserId : 0,
			callToGroup : false,
			callToPhone : true,
			video : false,
			status : BX.message('IM_PHONE_INVITE'),
			buttons : [
				{
					text: BX.message('IM_PHONE_BTN_ANSWER'),
					className: 'bx-messenger-call-overlay-button-answer',
					events: {
						click : BX.delegate(function() {
							this.BXIM.stopRepeatSound('ringtone');
							this.phoneIncomingAnswer();

							this.desktop.closeTopmostWindow();

						}, this)
					}
				},
				{
					text: BX.message('IM_PHONE_BTN_BUSY'),
					className: 'bx-messenger-call-overlay-button-hangup',
					events: {
						click : BX.delegate(function() {
							this.phoneCallFinish();
							this.callAbort();
							this.callOverlayClose();
						}, this)
					}
				},
				{
					text: BX.message('IM_M_CALL_BTN_CHAT'),
					className: 'bx-messenger-call-overlay-button-chat',
					showInMaximize: true,
					events: { click : BX.delegate(this.callOverlayToggleSize, this) }
				},
				{
					title: BX.message('IM_M_CALL_BTN_MAXI'),
					className: 'bx-messenger-call-overlay-button-maxi',
					showInMinimize: true,
					events: { click : BX.delegate(this.callOverlayToggleSize, this) }
				}
			]
		});

		this.callOverlayDrawCrm();
		if (this.callNotify)
			this.callNotify.adjustPosition();

		if(!this.BXIM.windowFocus && this.BXIM.notifyManager.nativeNotifyGranted())
		{
			var notify = {
				'title':  BX.message('IM_PHONE_DESC'),
				'text':  BX.util.htmlspecialcharsback(this.callOverlayTitle()),
				'icon': this.callUserId? this.messenger.users[this.callUserId].avatar: '',
				'tag':  'im-call'
			};
			notify.onshow = function() {
				var notify = this;
				setTimeout(function(){
					notify.close();
				}, 5000)
			}
			notify.onclick = function() {
				window.focus();
				this.close();
			}
			this.BXIM.notifyManager.nativeNotify(notify)
		}
	}
};

BX.IM.WebRTC.prototype.phoneNotifyWaitDesktop = function(chatId, callId, callerId)
{
	this.BXIM.ppServerStatus = true;
	if (!this.callSupport() || !this.desktop.ready())
		return false;

	this.phoneNumberUser = BX.util.htmlspecialchars(callerId);
	callerId = this.phoneCorrect(callerId);

	if (!this.callActive && !this.callInit)
	{
		this.initiator = true;
		this.callInitUserId = 0;
		this.callInit = true;
		this.callActive = false;
		this.callUserId = 0;
		this.callChatId = 0;
		this.callToGroup = 0;
		this.callGroupUsers = [];
		this.phoneIncoming = true;
		this.phoneCallId = callId;
		this.phoneNumber = callerId;
		this.phoneApplication = 'incoming';
		this.phoneParams = {};

		this.callOverlayShow({
			prepare : true,
			toUserId : this.BXIM.userId,
			phoneNumber : this.phoneNumber,
			callTitle : this.phoneNumberUser,
			fromUserId : 0,
			callToGroup : false,
			callToPhone : true,
			video : false,
			status : BX.message('IM_PHONE_INVITE'),
			buttons : [
				{
					text: BX.message('IM_PHONE_BTN_ANSWER'),
					className: 'bx-messenger-call-overlay-button-answer',
					events: {
						click : BX.delegate(function() {
							BX.desktop.onCustomEvent("main", "bxPhoneAnswer", [chatId, callId, callerId]);
							BX.desktop.windowCommand('close');
						}, this)
					}
				},
				{
					text: BX.message('IM_PHONE_BTN_BUSY'),
					className: 'bx-messenger-call-overlay-button-hangup',
					events: {
						click : BX.delegate(function() {
							BX.desktop.onCustomEvent("main", "bxPhoneSkip", []);
							BX.desktop.windowCommand('close');
						}, this)
					}
				}
			]
		});
		this.callOverlayDrawCrm();

		this.desktop.drawOnPlaceholder(this.callOverlay);

		if (this.phoneCrm)
			BX.desktop.setWindowPosition({X:STP_CENTER, Y:STP_VCENTER, Width: 609, Height: 453});
		else
			BX.desktop.setWindowPosition({X:STP_CENTER, Y:STP_VCENTER, Width: 470, Height: 120});
	}
};



BX.IM.WebRTC.prototype.phoneLog = function()
{
	if (this.desktop.ready())
	{
		var text = '';
		for (var i = 0; i < arguments.length; i++)
		{
			text = text+' | '+(typeof(arguments[i]) == 'object'? JSON.stringify(arguments[i]): arguments[i]);
		}
		BX.desktop.log('phone.'+this.BXIM.userEmail+'.log', text.substr(3));
	}
	if (this.debug)
	{
		if (console) console.log('Phone Log', JSON.stringify(arguments));
	}
};




/* NotifyManager */
BX.IM.NotifyManager = function(BXIM)
{
	this.stack = [];
	this.stackTimeout = null;
	this.stackPopup = {};
	this.stackPopupTimeout = {};
	this.stackPopupTimeout2 = {};
	this.stackPopupId = 0;
	this.stackOverflow = false;

	this.blockNativeNotify = false;
	this.blockNativeNotifyTimeout = null;

	this.notifyShow = 0;
	this.notifyHideTime = 5000;
	this.notifyHeightCurrent = 10;
	this.notifyHeightMax = 0;
	this.notifyGarbageTimeout = null;
	this.notifyAutoHide = true;
	this.notifyAutoHideTimeout = null;

	/*
	BX.bind(window, 'scroll', BX.delegate(function(events){
		if (this.notifyShow > 0)
			for (var i in this.stackPopup)
				this.stackPopup[i].close();
	}, this));
	*/

	if (BX.browser.SupportLocalStorage())
	{
		BX.addCustomEvent(window, "onLocalStorageSet", BX.proxy(this.storageSet, this));
	}

	this.BXIM = BXIM;
};

BX.IM.NotifyManager.prototype.storageSet = function(params)
{
	if (params.key == 'mnnb')
	{
		this.blockNativeNotify = true;
		clearTimeout(this.blockNativeNotifyTimeout);
		this.blockNativeNotifyTimeout = setTimeout(BX.delegate(function(){
			this.blockNativeNotify = false;
		}, this), 1000)
	}
}

BX.IM.NotifyManager.prototype.add = function(params)
{
	if (typeof(params) != "object" || !params.html)
		return false;

	if (BX.type.isDomNode(params.html))
		params.html = params.html.outerHTML;

	this.stack.push(params);

	if (!this.stackOverflow)
		this.setShowTimer(300);
};

BX.IM.NotifyManager.prototype.remove = function(stackId)
{
	delete this.stack[stackId];
};

BX.IM.NotifyManager.prototype.draw = function()
{
	this.show();
}

BX.IM.NotifyManager.prototype.show = function()
{
	this.notifyHeightMax = document.body.offsetHeight;

	var windowPos = BX.GetWindowScrollPos();
	for (var i = 0; i < this.stack.length; i++)
	{
		if (typeof(this.stack[i]) == 'undefined')
			continue;

		/* show notify to calc width & height */
		var notifyPopup = new BX.PopupWindow('bx-im-notify-flash-'+this.stackPopupId, {top: '-1000px', left: 0}, {
			lightShadow : true,
			zIndex: 200,
			events : {
				onPopupClose : BX.delegate(function() {
					BX.proxy_context.popupContainer.style.opacity = 0;
					this.notifyShow--;
					this.notifyHeightCurrent -= BX.proxy_context.popupContainer.offsetHeight+10;
					this.stackOverflow = false;
					setTimeout(BX.delegate(function() {
						this.destroy();
					}, BX.proxy_context), 1500);
				}, this),
				onPopupDestroy : BX.delegate(function() {
					BX.unbindAll(BX.findChild(BX.proxy_context.popupContainer, {className : "bx-notifier-item-delete"}, true));
					BX.unbindAll(BX.proxy_context.popupContainer);
					delete this.stackPopup[BX.proxy_context.uniquePopupId];
					delete this.stackPopupTimeout[BX.proxy_context.uniquePopupId];
					delete this.stackPopupTimeout2[BX.proxy_context.uniquePopupId];
				}, this)
			},
			bindOnResize: false,
			content : BX.create("div", {props : { className: "bx-notifyManager-item"}, html: this.stack[i].html})
		});
		notifyPopup.notifyParams = this.stack[i];
		notifyPopup.notifyParams.id = i;
		notifyPopup.show();
		BX.onCustomEvent(window, 'onNotifyManagerShow', [this.stack[i]]);

		/* move notify out monitor */
		notifyPopup.popupContainer.style.left = document.body.offsetWidth-notifyPopup.popupContainer.offsetWidth-10+'px';
		notifyPopup.popupContainer.style.opacity = 0;

		if (this.notifyHeightMax < this.notifyHeightCurrent+notifyPopup.popupContainer.offsetHeight+10)
		{
			if (this.notifyShow > 0)
			{
				notifyPopup.destroy();
				this.stackOverflow = true;
				break;
			}
		}

		/* move notify to top-right */
		BX.addClass(notifyPopup.popupContainer, 'bx-notifyManager-animation');
		notifyPopup.popupContainer.style.opacity = 1;
		notifyPopup.popupContainer.style.top = windowPos.scrollTop+this.notifyHeightCurrent+'px';

		this.notifyHeightCurrent = this.notifyHeightCurrent+notifyPopup.popupContainer.offsetHeight+10;
		this.stackPopupId++;
		this.notifyShow++;
		this.remove(i);

		/* notify events */
		this.stackPopupTimeout[notifyPopup.uniquePopupId] = null;

		BX.bind(notifyPopup.popupContainer, "mouseover", BX.delegate(function() {
			this.clearAutoHide();
		}, this));

		BX.bind(notifyPopup.popupContainer, "mouseout", BX.delegate(function() {
			this.setAutoHide(this.notifyHideTime/2);
		}, this));

		BX.bind(notifyPopup.popupContainer, "contextmenu", BX.delegate(function(e){
			if (this.stackPopup[BX.proxy_context.id].notifyParams.tag)
				this.closeByTag(this.stackPopup[BX.proxy_context.id].notifyParams.tag);
			else
				this.stackPopup[BX.proxy_context.id].close();

			return BX.PreventDefault(e);
		}, this));

		var arLinks = BX.findChildren(notifyPopup.popupContainer, {tagName : "a"}, true);
		for (var j = 0; j < arLinks.length; j++)
		{
			if (arLinks[j].href != '#')
				arLinks[j].target = "_blank";
		}

		BX.bind(BX.findChild(notifyPopup.popupContainer, {className : "bx-notifier-item-delete"}, true), 'click', BX.delegate(function(e){
			var id = BX.proxy_context.parentNode.parentNode.parentNode.parentNode.id.replace('popup-window-content-', '');

			if (this.stackPopup[id].notifyParams.close)
				this.stackPopup[id].notifyParams.close(this.stackPopup[id]);

			this.stackPopup[id].close();

			if (this.notifyAutoHide == false)
			{
				this.clearAutoHide();
				this.setAutoHide(this.notifyHideTime/2);
			}
			return BX.PreventDefault(e);
		}, this));

		BX.bindDelegate(notifyPopup.popupContainer, "click", {className: "bx-notifier-item-button"}, BX.delegate(function(e){
			var id = BX.proxy_context.getAttribute('data-id');
			this.BXIM.notify.confirmRequest({
				'notifyId': id,
				'notifyValue': BX.proxy_context.getAttribute('data-value'),
				'notifyURL': BX.proxy_context.getAttribute('data-url'),
				'notifyTag': this.BXIM.notify.notify[id] && this.BXIM.notify.notify[id].tag? this.BXIM.notify.notify[id].tag: null,
				'groupDelete': BX.proxy_context.getAttribute('data-group') != null
			}, true);
			for (var i in this.stackPopup)
			{
				if (this.stackPopup[i].notifyParams.notifyId == id)
					this.stackPopup[i].close();
			}
			if (this.notifyAutoHide == false)
			{
				this.clearAutoHide();
				this.setAutoHide(this.notifyHideTime/2);
			}
			return BX.PreventDefault(e);
		}, this));

		if (notifyPopup.notifyParams.click)
		{
			notifyPopup.popupContainer.style.cursor = 'pointer';
			BX.bind(notifyPopup.popupContainer, 'click', BX.delegate(function(e){
				this.notifyParams.click(this);
				if (this.notifyParams.notifyId != 'network')
					return BX.PreventDefault(e);
			}, notifyPopup));
		}
		this.stackPopup[notifyPopup.uniquePopupId] = notifyPopup;
	}

	if (this.stack.length > 0)
	{
		this.clearAutoHide(true);
		this.setAutoHide(this.notifyHideTime);
	}
	this.garbage();
};

BX.IM.NotifyManager.prototype.closeByTag = function(tag)
{
	for (var i = 0; i < this.stack.length; i++)
	{
		if (typeof(this.stack[i]) != 'undefined' && this.stack[i].tag == tag)
		{
			delete this.stack[i];
		}
	}
	for (var i in this.stackPopup)
	{
		if (this.stackPopup[i].notifyParams.tag == tag)
			this.stackPopup[i].close()
	}
};

BX.IM.NotifyManager.prototype.setShowTimer = function(time)
{
	clearTimeout(this.stackTimeout);
	this.stackTimeout = setTimeout(BX.delegate(this.draw, this), time);
};

BX.IM.NotifyManager.prototype.setAutoHide = function(time)
{
	this.notifyAutoHide = true;
	clearTimeout(this.notifyAutoHideTimeout);
	this.notifyAutoHideTimeout = setTimeout(BX.delegate(function(){
		for (var i in this.stackPopupTimeout)
		{
			this.stackPopupTimeout[i] = setTimeout(BX.delegate(function(){
				this.close();
			}, this.stackPopup[i]), time-1000);
			this.stackPopupTimeout2[i] = setTimeout(BX.delegate(function(){
				this.setShowTimer(300);
			}, this), time-700);
		}
	}, this), 1000);
};

BX.IM.NotifyManager.prototype.clearAutoHide = function(force)
{
	clearTimeout(this.notifyGarbageTimeout);
	this.notifyAutoHide = false;
	force = force==true;
	if (force)
	{
		clearTimeout(this.stackTimeout);
		for (var i in this.stackPopupTimeout)
		{
			clearTimeout(this.stackPopupTimeout[i]);
			clearTimeout(this.stackPopupTimeout2[i]);
		}
	}
	else
	{
		clearTimeout(this.notifyAutoHideTimeout);
		this.notifyAutoHideTimeout = setTimeout(BX.delegate(function(){
			clearTimeout(this.stackTimeout);
			for (var i in this.stackPopupTimeout)
			{
				clearTimeout(this.stackPopupTimeout[i]);
				clearTimeout(this.stackPopupTimeout2[i]);
			}
		}, this), 300);
	}
};

BX.IM.NotifyManager.prototype.garbage = function()
{
	clearTimeout(this.notifyGarbageTimeout);
	this.notifyGarbageTimeout = setTimeout(BX.delegate(function(){
		var newStack = [];
		for (var i = 0; i < this.stack.length; i++)
		{
			if (typeof(this.stack[i]) != 'undefined')
				newStack.push(this.stack[i]);
		}
		this.stack = newStack;
	}, this), 10000);
};

BX.IM.NotifyManager.prototype.nativeNotify = function(params, force)
{
	if (!params.title || params.title.length <= 0)
		return false;

	if (this.blockNativeNotify)
		return false;

	if (!force)
	{
		setTimeout(BX.delegate(function(){
			if (this.blockNativeNotify)
				return false;

			this.nativeNotify(params, true);
		}, this), Math.floor(Math.random() * (151)) + 50);

		return true;
	}

	BX.localStorage.set('mnnb', true, 1);

	var notify = new Notification(params.title, {
		tag : (params.tag? params.tag: ''),
		body : (params.text? params.text: ''),
		icon : (params.icon? params.icon: '')
	});
	if (typeof(params.onshow) == 'function')
		notify.onshow = params.onshow;
	if (typeof(params.onclick) == 'function')
		notify.onclick = params.onclick;
	if (typeof(params.onclose) == 'function')
		notify.onclose = params.onclose;
	if (typeof(params.onerror) == 'function')
		notify.onerror = params.onerror;

	return true;
};

BX.IM.NotifyManager.prototype.nativeNotifyShow = function()
{
	this.show();
};

BX.IM.NotifyManager.prototype.nativeNotifyGranted = function()
{
	return (window.Notification && window.Notification.permission && window.Notification.permission.toLowerCase() == "granted");
};

BX.IM.NotifyManager.prototype.nativeNotifyAccessForm = function()
{
	if (!this.BXIM.xmppStatus && !this.BXIM.desktopStatus && this.BXIM.settings.nativeNotify &&
		window.Notification && window.Notification.permission && window.Notification.permission.toLowerCase() == "default")
	{
		clearTimeout(this.popupMessengerDesktopTimeout);
		var acceptButton = BX.delegate(function(){
			Notification.requestPermission();
			BXIM.messenger.hideTopLine();
		}, this);
		var declineButton = BX.delegate(function(){
			this.BXIM.settings.nativeNotify = false;
			this.BXIM.saveSettings({'nativeNotify': this.BXIM.settings.nativeNotify});
			BXIM.messenger.hideTopLine();
		}, this);

		BXIM.messenger.showTopLine(BX.message("IM_WN_MAC")+"<br>"+BX.message("IM_WN_TEXT"), [{title: BX.message('IM_WN_ACCEPT'), callback: acceptButton},{title: BX.message('IM_DESKTOP_INSTALL_N'), callback: declineButton}]);
	}
	else
	{
		return false;
	}

	return true;
}
})();


/* IM Network class */

(function() {

if (BX.Network)
	return;

BX.Network = function(BXIM, params)
{
	this.BXIM = BXIM;
	this.params = params || {};

	this.notify = params.notifyClass;
	this.messenger = params.messengerClass;
	this.desktop = params.desktopClass;

	this.notifyCount = 0;
	this.messageCount = 0;
	this.callCount = 0;

	if (this.BXIM.init && this.BXIM.bitrixNetworkStatus)
	{
		BX.addCustomEvent("onPullEvent-b24network", BX.delegate(function(command,params)
		{
			if (command == 'notify')
			{
				if (params.COUNTER && params.COUNTER.TYPE && params.COUNTER.SUM)
				{
					if (params.COUNTER.SUM == 'increment')
						this.incrementCounter(params.COUNTER.TYPE);
					else
						this.setCounter(params.COUNTER.TYPE, params.COUNTER.SUM);
				}

				if (params.MESSAGE && params.LINK)
				{
					this.newNotify(params.MESSAGE, params.LINK);
				}
			}
		}, this));
	}
};

BX.Network.prototype.newNotify = function(message, link, send)
{
	if (!(!this.desktop.ready() && this.desktop.run()) && (this.BXIM.settings.status == 'dnd' || !this.desktop.ready() && this.BXIM.desktopStatus))
		return false;

	send = send != false;

	var notify = {
		"id":"network",
		"type":"4",
		"date":BX.IM.getNowDate(),
		"silent":"N",
		"text":message+(link? '<br><a href="'+link+'" target="_blank">'+BX.message('IM_LINK_MORE')+'</a>': ''),
		"textNative":message,
		"tag":"",
		"original_tag":"",
		"read":"",
		"settingName":"im|default",
		"userId":"0",
		"userName":"",
		"userAvatar":"",
		"userLink":"",
		"title":"",
		"href": link
	};
	var arNotify = [];
	var arNotifyText = [];
	notifyHtml = this.notify.createNotify(notify);

	if (notifyHtml !== false)
	{
		arNotify.push(notifyHtml);
		arNotifyText.push({
			'title':  notify.userName? BX.util.htmlspecialcharsback(notify.userName): BX.message('IM_NOTIFY_WINDOW_NEW_TITLE'),
			'text':  BX.util.htmlspecialcharsback(notify.textNative).split('<br />').join("\n").replace(/<\/?[^>]+>/gi, ''),
			'icon':  notify.userAvatar? notify.userAvatar: '',
			'tag':  'im-network-'+notify.tag
		});
	}

	if (arNotify.length == 0)
		return false;

	if (send)
		this.BXIM.playSound("reminder");

	if(send && !this.BXIM.windowFocus && this.BXIM.notifyManager.nativeNotifyGranted())
	{
		for (var i = 0; i < arNotifyText.length; i++)
		{
			var notify = arNotifyText[i];
			notify.onshow = function() {
				var notify = this;
				setTimeout(function(){
					notify.close();
				}, 15000)
			}
			notify.onclick = function() {
				window.focus();
				this.close();
			}
			this.BXIM.notifyManager.nativeNotify(notify)
		}
	}

	if (this.BXIM.windowFocus && this.BXIM.notifyManager.nativeNotifyGranted())
	{
		BX.localStorage.set('mnnb', true, 1);
	}
	for (var i = 0; i < arNotify.length; i++)
	{
		this.BXIM.notifyManager.add({
			'html': arNotify[i],
			'tag': '',
			'originalTag': '',
			'notifyId': 'network',
			'notifyType': arNotify[i].getAttribute("data-notifyType"),
			'click': BX.delegate(function(popup) {
				popup.close();
			}, this),
			'close': function() {}
		});
	}

	return true;
}

BX.Network.prototype.setCounter = function(type, sum)
{
	sum = parseInt(sum);
	if (sum <= 0)
		sum = 0;

	if (type == 'call')
		this.callCount = sum;
	else if (type == 'notify')
		this.notifyCount = sum;
	else if (type == 'message')
		this.messageCount = sum;

	this.updateCounters();

	return sum;
};

BX.Network.prototype.incrementCounter = function(type)
{
	if (type == 'call')
		this.callCount++;
	else if (type == 'notify')
		this.notifyCount++;
	else if (type == 'message')
		this.messageCount++;

	this.updateCounters();

	return true;
};

BX.Network.prototype.getCounter = function(type)
{
	var sum = 0;
	if (type == 'call')
		sum = this.callCount;
	else if (type == 'notify')
		sum = this.notifyCount;
	else if (type == 'message')
		sum = this.messageCount;

	return sum;
};

BX.Network.prototype.updateCounters = function()
{
	var count = this.getCounters();
	BX.onCustomEvent(window, 'onImUpdateCounterNetwork', [count]);

	var countLabel = '';
	if (count > 99)
		countLabel = '99+';
	else if (count > 0)
		countLabel = count;

	if (this.notify.panelButtonNetworkCount != null)
	{
		this.notify.panelButtonNetworkCount.innerHTML = countLabel;
		this.notify.adjustPosition({"resize": true, "timeout": 500});
	}
};

BX.Network.prototype.getCounters = function()
{
	return this.notifyCount+this.messageCount+this.callCount;
};

})();

