/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) 2006-2009 Kimai-Development-Team
 *
 * Kimai is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; Version 3, 29 June 2007
 *
 * Kimai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimai; If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Javascript functions used in the weeksheet extension.
 */

/**
 * Called when the extension loaded. Do some initial stuff.
 */
function ws_ext_onload() {
	ws_ext_applyHoverIntent();
	ws_ext_resize();
	$("#loader").hide();
	lisws_visible(true);
}

/**
 * Formats a date object to be used in the time input field.
 * @param value
 * @returns {string}
 */
function ws_formatTime(value) {
	var hours = prependZeroIfNeeded(value.getHours());
	var minutes = prependZeroIfNeeded(value.getMinutes());
	var seconds = prependZeroIfNeeded(value.getSeconds());

	return hours + ':' + minutes + ':' + seconds;
}

/**
 * format a date object to be used in the date input field.
 * @param {Date} value
 * @returns {string}
 */
function ws_formatDate(value) {
	var day = prependZeroIfNeeded(value.getDate());
	var month = prependZeroIfNeeded(value.getMonth() + 1);
	var year = value.getFullYear();

	return day + '.' + month + '.' + year;
}

/**
 * Update the dimension variables to reflect new height and width.
 */
function ws_ext_get_dimensions() {
	scroller_width = 17;
	if (navigator.platform.substr(0, 3) == 'Mac') {
		scroller_width = 16;
	}

	(customerShrinkMode) ? subtableCount = 2 : subtableCount = 3;
	subtableWidth = (pageWidth() - 10) / subtableCount - 7;

	weekSheet_width = pageWidth() - 24;
	weekSheet_height = pageHeight() - 224 - headerHeight() - 28;
}

/**
 * Hover a row if the mouse is over it for more than half a second.
 */
function ws_ext_applyHoverIntent() {
	$('#weekSheet tr').hoverIntent({
		sensitivity: 1,
		interval: 500,
		over: function() {
			$('#weekSheet tr').removeClass('hover');
			$(this).addClass('hover');
		},
		out: function() {
			$(this).removeClass('hover');
		}
	});
}

/**
 * The window has been resized, we have to adjust to the new space.
 */
function ws_ext_resize() {
	ws_ext_set_tableWrapperWidths();
	ws_ext_set_heightTop();
}

/**
 * Set width of table and faked table head.
 */
function ws_ext_set_tableWrapperWidths() {
	ws_ext_get_dimensions();
	$("#weekSheet_head,#weekSheet").css("width",weekSheet_width);
	ws_ext_set_TableWidths();
}

/**
 * If the extension is being shrinked so the sublists are shown larger
 * adjust to that.
 */
function ws_ext_set_heightTop() {
	ws_ext_get_dimensions();
	if (!extensionShrinkMode) {
		$("#weekSheet").css("height", weekSheet_height);
	} else {
		$("#weekSheet").css("height", "70px");
	}

	ws_ext_set_TableWidths();
}

/**
 * Set the width of the table.
 */
function ws_ext_set_TableWidths() {
	ws_ext_get_dimensions();
	// set table widths
	($("#weekSheet").innerHeight()-$("#weekSheet table").outerHeight()>0)?scr=0:scr=scroller_width; // width of weekSheet table depending on scrollbar or not
	$("#weekSheet table").css("width",weekSheet_width-scr);
	$("div#weekSheet > div > table > tbody > tr > td.trackingnumber").css("width", $("#weekSheet_head > table > tbody > tr > td.trackingnumber").width());
	// stretch duration column in faked weekSheet table head
	$("#weekSheet_head > table > tbody > tr > td.time").css("width", $("div#weekSheet > div > table > tbody > tr > td.time").width());
	// stretch customer column in faked weekSheet table head
	$("#weekSheet_head > table > tbody > tr > td.customer").css("width", $("div#weekSheet > div > table > tbody > tr > td.customer").width());
	// stretch project column in faked weekSheet table head
	$("#weekSheet_head > table > tbody > tr > td.project").css("width", $("div#weekSheet > div > table > tbody > tr > td.project").width());
	// stretch activity column in faked weekSheet table head
	$("#weekSheet_head > table > tbody > tr > td.activity").css("width", $("div#weekSheet > div > table > tbody > tr > td.activity").width());
}

function weeksheet_extension_tab_changed() {
	$('#display_total').html(ws_total);
	if (weeksheet_timeframe_changed_hook_flag) {
		ws_ext_reload();
		weeksheet_customers_changed_hook_flag = 0;
		weeksheet_projecws_changed_hook_flag = 0;
		weeksheet_activities_changed_hook_flag = 0;
	}
	if (weeksheet_customers_changed_hook_flag) {
		weeksheet_extension_customers_changed();
		weeksheet_projecws_changed_hook_flag = 0;
		weeksheet_activities_changed_hook_flag = 0;
	}
	if (weeksheet_projecws_changed_hook_flag) {
		weeksheet_extension_projecws_changed();
	}
	if (weeksheet_activities_changed_hook_flag) {
		weeksheet_extension_activities_changed();
	}

	weeksheet_timeframe_changed_hook_flag = 0;
	weeksheet_customers_changed_hook_flag = 0;
	weeksheet_projecws_changed_hook_flag = 0;
	weeksheet_activities_changed_hook_flag = 0;
}

function weeksheet_extension_timeframe_changed() {
	if ($('.ki_weeksheet').css('display') == "block") {
		ws_ext_reload();
	} else {
		weeksheet_timeframe_changed_hook_flag++;
	}
}
function weeksheet_extension_customers_changed() {
	if ($('.ki_weeksheet').css('display') == "block") {
		ws_ext_reload();
	} else {
		weeksheet_customers_changed_hook_flag++;
	}
}

function weeksheet_extension_projecws_changed() {
	if ($('.ki_weeksheet').css('display') == "block") {
		ws_ext_reload();
	} else {
		weeksheet_projecws_changed_hook_flag++;
	}
}

function weeksheet_extension_activities_changed() {
	if ($('.ki_weeksheet').css('display') == "block") {
		ws_ext_reload();
	} else {
		weeksheet_activities_changed_hook_flag++;
	}
}

/**
 * reloads weeksheet, customer, project and activity tables
 */
function ws_ext_reload() {
	$.post(ws_ext_path + "processor.php", {
		axAction: "reload_weekSheet",
		axValue: filterUsers.join(":") + '|' + filterCustomers.join(":") + '|' + filterProjects.join(":") + '|' + filterActivities.join(":"),
		id: 0,
		first_day: new Date($('#pick_in').val()).getTime() / 1000,
		last_day: new Date($('#pick_out').val()).getTime() / 1000
	}, function(data) {
		$("#weekSheet").html(data);

		ws_ext_set_TableWidths();
		ws_ext_applyHoverIntent();
	});
}

/**
 * reloads weeksheet, customer, project and activity tables
 * 
 * @param project
 * @param noUpdateRate
 * @param activity
 * @param weekSheetEntry
 */
function ws_ext_reload_activities(project, noUpdateRate, activity, weekSheetEntry) {
	var selected_activity = $('#add_edit_weekSheetEntry_activityID').val();
	$.post(ws_ext_path + "processor.php", {
		axAction: "reload_activities_options",
		axValue: 0,
		id: 0,
		project: project
	}, function (data) {
		delete window['__cacheselect_add_edit_weekSheetEntry_activityID'];
		$("#add_edit_weekSheetEntry_activityID").html(data);
		$("#add_edit_weekSheetEntry_activityID").val(selected_activity);
		if (noUpdateRate == undefined) {
			getBestRates();
		}
		if (activity > 0) {
			$.getJSON("../extensions/ki_weeksheets/processor.php", {
				axAction: "budgets",
				project_id: project,
				activity_id: activity,
				weekSheetEntryID: weekSheetEntry
			}, function (data) {
				ws_ext_updateBudget(data);
			});
		}
	});
}

/**
 * reloads budget
 * 
 * everything in data['weekSheetEntry'] has to be subtracted in case the time sheet entry is in the db already
 * part of this activity. In other cases, we already took case on server side that the values are 0
 * @param data
 */
function ws_ext_updateBudget(data) {
	var budget = data['activityBudgets']['budget'];
	// that is the case if we changed the project and no activity is selected
	if (isNaN(budget)) {
		budget = 0;
	}
	if ($('#budget_val').val() != '') {
		budget += parseFloat($('#budget_val').val());
	}
	budget -= data['weekSheetEntry']['budget'];
	$('#budget_activity').text(budget);
	var approved = data['activityBudgets']['approved'];
	// that is the case if we changed the project and no activity is selected
	if (isNaN(approved)) {
		approved = 0;
	}
	if ($('#approved').val() != '') {
		approved += parseFloat($('#approved').val());
	}
	approved -= data['weekSheetEntry']['approved'];
	$('#budget_activity_approved').text(approved);
	var budgetUsed = data['activityUsed'];
	if (isNaN(budgetUsed)) {
		budgetUsed = 0;
	}
	var durationArray = $("#duration").val().split(/:|\./);
	if (end != null && durationArray.length > 0 && durationArray.length < 4) {
		secs = durationArray[0] * 3600;
		if (durationArray.length > 1) {
			secs += durationArray[1] * 60;
		}
		if (durationArray.length > 2) {
			secs += parseInt(durationArray[2]);
		}
		var rate = $('#rate').val();
		if (rate != '') {
			budgetUsed += secs / 3600 * rate;
			budgetUsed -= data['weekSheetEntry']['duration'] / 3600 * data['weekSheetEntry']['rate'];
		}
	}
	$('#budget_activity_used').text(Math.round(budgetUsed,2));
}

/**
 * this function is attached to the little green arrows in front of each weeksheet record
 * and starts recording that activity anew
 * 
 * @param project
 * @param activity
 * @param id
 */
function ws_ext_recordAgain(project,activity,id) {
	$('#weekSheetEntry' + id + '>td>a').blur();

	if (currentRecording > -1) {
		stopRecord();
	}

	$('#weekSheetEntry' + id + '>td>a.recordAgain>img').attr("src", "../skins/" + skin + "/grfx/loading13.gif");
	var now = Math.floor(((new Date()).getTime()) / 1000);
	offset = now;
	startsec = 0;
	show_stopwatch();
	$('#weekSheetEntry'+id+'>td>a').removeAttr('onclick');

	$.post(ws_ext_path + "processor.php", {
		axAction: "record", 
		axValue: 0,
		id: id
	}, function (data) {
		if (data.errors.length > 0) {
			return;
		}

		customer = data.customer;
		customerName = data.customerName;
		projectName = data.projectName;
		activityName = data.activityName;
		currentRecording = data.currentRecording;

		ws_ext_reload();
		buzzer_preselect_project(project, projectName, customer, customerName, false);
		buzzer_preselect_activity(activity, activityName, 0, 0, false);
		$("#ticker_customer").html(customerName);
		$("#ticker_project").html(projectName);
		$("#ticker_activity").html(activityName);
	});
}

/**
 * this function is attached to the little green arrows in front of each weeksheet record
 * and starts recording that activity anew
 * 
 * @param id
 */
function ws_ext_stopRecord(id) {
	ticktack_off();
	show_selectors();
	if (id) {
		$('#weekSheetEntry' + id + '>td').css("background-color", "#F00");
		$('#weekSheetEntry' + id + '>td>a.stop>img').attr("src", "../skins/" + skin + "/grfx/loading13_red.gif");
		$('#weekSheetEntry' + id + '>td>a').blur();
		$('#weekSheetEntry' + id + '>td>a').removeAttr('onclick');
		$('#weekSheetEntry' + id + '>td').css("color", "#FFF");
	}
	$.post(ws_ext_path + "processor.php", {
		axAction: "stop",
		axValue: 0,
		id: id
	}, function (data) {
		ws_ext_reload();
	});
}

/**
 * delete a weeksheet record immediately
 * @param id
 */
function quickdelete(id) {
	$('#weekSheetEntry'+id+'>td>a').blur();

	if (confirmText != undefined) {
		var check = confirm(confirmText);
		if (check == false) {
			return;
		}
	}

	$('#weekSheetEntry' + id + '>td>a').removeAttr('onclick');
	$('#weekSheetEntry' + id + '>td>a.quickdelete>img').attr("src", "../skins/" + skin + "/grfx/loading13.gif");

	$.post(ws_ext_path + "processor.php", {
		axAction: "quickdelete",
		axValue: 0,
		id: id
	}, function (result) {
		if (result.errors.length == 0) {
			ws_ext_reload();
		} else {
			var messages = [];
			for (var index in result.errors) {
				messages.push(result.errors[index]);
			}
			alert(messages.join("\n"));
		}
	});
}

/**
 * edit a weeksheet record
 * @param id
 */
function editRecord(id) {
	floaterShow(ws_ext_path + "floaters.php", "add_edit_weekSheetEntry", 0, id, 650);
}

/**
 * edit a weeksheet quick note
 * @param id
 */
function editQuickNote(id) {
	floaterShow(ws_ext_path + "floaters.php", "add_edit_weekSheetQuickNote", 0, id, 650);
}

/**
 * refresh the rate with a new value, if this is a new entry
 */
function getBestRates() {
	$.getJSON(ws_ext_path + "processor.php", {
		axAction: "bestFittingRates",
		axValue: 0,
		project_id: $("#add_edit_weekSheetEntry_projectID").val(),
		activity_id: $("#add_edit_weekSheetEntry_activityID").val()
	}, function (data) {
		if (data.errors.length > 0) {
			return;
		}

		if (data.hourlyRate == false) {
			//TODO: why does Kimai do this? If we already set a rate
			// we might want to keep it, not just reset it to empty..?
			// $("#ws_ext_form_add_edit_weekSheetEntry #rate").val('');
		} else {
			$("#ws_ext_form_add_edit_weekSheetEntry #rate").val(data.hourlyRate);
		}

		if (data.fixedRate == false) {
			$("#ws_ext_form_add_edit_weekSheetEntry #fixedRate").val('');
		} else {
			$("#ws_ext_form_add_edit_weekSheetEntry #fixedRate").val(data.fixedRate);
		}
	});
}

/**
 * pastes the current date and time in the outPoint field of the
 * change dialog for weeksheet entries
 *
 * $view->pasteValue = date("d.m.Y - H:i:s",$kga['now']);
 *
 * @param value
 */
function pasteNow(value) {
	var now = new Date();
	var hours = prependZeroIfNeeded(now.getHours());
	var minutes = prependZeroIfNeeded(now.getMinutes());
	var seconds = prependZeroIfNeeded(now.getSeconds());

	$("#end_time").val(hours + ':' + minutes + ':' + seconds);
	$('#end_time').trigger('change');

	$("#end_day").datepicker("setDate", now);
}

/**
 * Returns a Date object, based on 2 strings
 * @param dateStr
 * @param timeStr
 * @returns {Date}
 */
function ws_getDateFromStrings(dateStr, timeStr) {
	var result = new Date();
	var dateArray = dateStr.split(/\./);
	var timeArray = timeStr.split(/:|\./);
	if (dateArray.length != 3 || timeArray.length < 1 || timeArray.length > 3) {
		return null;
	}
	result.setFullYear(dateArray[2], dateArray[1] - 1, dateArray[0]);
	if (timeArray[0].length > 2) {
		result.setHours(timeArray[0].substring(0, 2));
		result.setMinutes(timeArray[0].substring(2, 4));
	} else {
		result.setHours(timeArray[0]);
	}
	if (timeArray.length > 1) {
		result.setMinutes(timeArray[1]);
	} else {
		result.setMinutes(0);
	}
	if (timeArray.length > 2) {
		result.setSeconds(timeArray[2]);
	} else {
		result.setSeconds(0);
	}
	return result;
}

/**
 * Gets the begin Date, while editing a weeksheet record
 * @returns {Date}
 */
function ws_getStartDate() {
	return ws_getDateFromStrings($("#start_day").val(), $("#start_time").val());
}

/**
 * Gets the end Date, while editing a weeksheet record
 * @returns {Date}
 */
function ws_getEndDate() {
	return ws_getDateFromStrings($("#end_day").val(), $("#end_time").val());
}

/**
 * Change the end time field, based on the duration, while editing a weeksheet record
 */
function ws_durationToTime() {
	end = ws_getEndDate();
	durationArray = $("#duration").val().split(/:|\./);
	if (end != null && durationArray.length > 0 && durationArray.length < 4) {
		secs = durationArray[0] * 3600;
		if (durationArray.length > 1) {
			secs += durationArray[1] * 60;
		}
		if (durationArray.length > 2) {
			secs += parseInt(durationArray[2]);
		}
		begin = new Date();
		begin.setTime(end.getTime() - (secs * 1000));

		$("#start_time").val(ws_formatTime(begin));
		$("#end_time").val(ws_formatTime(end));
		$("#start_day").val(ws_formatDate(begin));
		$("#end_day").val(ws_formatDate(end));
	}
}

/**
 * Change the duration field, based on the time, while editing a weeksheet record
 */
function ws_timeToDuration() {
	begin = ws_getStartDate();
	end = ws_getEndDate();
	if (begin == null || end == null) {
		$("#duration").val("");
	} else {
		beginSecs = Math.floor(begin.getTime() / 1000);
		endSecs = Math.floor(end.getTime() / 1000);
		durationSecs = endSecs - beginSecs;
		if (durationSecs < 0) {
			$("#duration").val("");
		} else {
			secs = prependZeroIfNeeded(durationSecs % 60);
			durationSecs = Math.floor(durationSecs / 60);
			mins = prependZeroIfNeeded(durationSecs % 60);
			hours = prependZeroIfNeeded(Math.floor(durationSecs / 60));
			$("#duration").val(hours + ":" + mins + ":" + secs);
			$('#duration').trigger('change');
		}
	}
}

/**
 * shows comment line for weeksheet entry
 * @param id
 * @returns {boolean}
 */
function ws_comment(id) {
	$('#c' + id).toggle();
	return false;
}
