{"version":3,"sources":["calendar-settings-slider.js"],"names":["window","SettingsSlider","params","this","calendar","id","uid","Math","round","random","button","zIndex","sliderId","inPersonal","util","userIsOwner","showGeneralSettings","config","perm","access","settings","SLIDER_WIDTH","SLIDER_DURATION","BX","bind","delegate","show","prototype","SidePanel","Instance","open","contentCallback","create","width","animationDuration","addCustomEvent","proxy","hide","destroy","disableKeyHandler","close","event","getSliderPage","getUrl","denyClose","denyAction","removeCustomEvent","enableKeyHandler","promise","Promise","ajax","get","getActionUrl","action","is_personal","show_general_settings","unique_id","sessid","bitrix_sessid","bx_event_calendar_request","reqId","html","fulfill","trim","initControls","save","DOM","denyBusyInvitation","showWeekNumbers","sectionSelect","crmSelect","showDeclined","showTasks","showCompletedTasks","timezoneSelect","workTimeStart","workTimeEnd","weekHolidays","yearHolidays","yearWorkdays","typeAccess","TYPE_ACCESS","accessWrap","initAccessController","code","hasOwnProperty","insertAccessRow","getAccessName","manageCalDav","syncSlider","showCalDavSyncDialog","options","length","sections","sectionController","getSectionList","meetSection","getUserOption","crmSection","i","section","selected","belongToOwner","add","Option","name","checked","showWeekNumber","value","work_time_start","work_time_end","in_array","week_holidays","year_holidays","year_workdays","userSettings","userTimezoneName","data","user_settings","user_timezone_name","push","type_access","request","type","handler","response","reload","accessControls","accessTasks","getTypeAccessTasks","accessLink","hasClass","removeClass","addClass","Access","Init","accessWrapInner","appendChild","props","className","accessTable","accessButtonWrap","accessButton","message","ShowForm","callback","provider","GetProviderName","popup","popupContainer","style","e","target","findTargetNode","srcElement","outerWrap","getAttribute","showAccessSelectorPopup","node","removeIcon","setValueCallback","valueNode","innerHTML","htmlspecialchars","title","cleanNode","rowNode","undefined","getDefaultTypeAccessTask","adjust","insertRow","titleNode","insertCell","valueCell","attrs","data-bx-calendar-access-selector","selectNode","text","data-bx-calendar-access-remove","accessPopupMenu","popupWindow","isShown","menuId","taskId","_this","menuItems","onclick","PopupMenu","closeByEsc","autoHide","offsetTop","offsetLeft","angle","BXEventCalendar"],"mappings":"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","file":""}