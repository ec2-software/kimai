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

 var ws_active_box;

function beep() {
	var snd = new Audio("data:audio/wav;base64,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qJrx4KOQjahZxWbcZgztj2c49nKmkId44S71j0c8eV9yDK6uPRzx5X18eDvjvQ6yKo9ZSS6l//8elePK/Lf//IInrOF/FvDoADYAGBMGb7FtErm5MXMlmPAJQVgWta7Zx2go+8xJ0UiCb8LHHdftWyLJE0QIAIsI+UbXu67dZMjmgDGCGl1H+vpF4NSDckSIkk7Vd+sxEhBQMRU8j/12UIRhzSaUdQ+rQU5kGeFxm+hb1oh6pWWmv3uvmReDl0UnvtapVaIzo1jZbf/pD6ElLqSX+rUmOQNpJFa/r+sa4e/pBlAABoAAAAA3CUgShLdGIxsY7AUABPRrgCABdDuQ5GC7DqPQCgbbJUAoRSUj+NIEig0YfyWUho1VBBBA//uQZB4ABZx5zfMakeAAAAmwAAAAF5F3P0w9GtAAACfAAAAAwLhMDmAYWMgVEG1U0FIGCBgXBXAtfMH10000EEEEEECUBYln03TTTdNBDZopopYvrTTdNa325mImNg3TTPV9q3pmY0xoO6bv3r00y+IDGid/9aaaZTGMuj9mpu9Mpio1dXrr5HERTZSmqU36A3CumzN/9Robv/Xx4v9ijkSRSNLQhAWumap82WRSBUqXStV/YcS+XVLnSS+WLDroqArFkMEsAS+eWmrUzrO0oEmE40RlMZ5+ODIkAyKAGUwZ3mVKmcamcJnMW26MRPgUw6j+LkhyHGVGYjSUUKNpuJUQoOIAyDvEyG8S5yfK6dhZc0Tx1KI/gviKL6qvvFs1+bWtaz58uUNnryq6kt5RzOCkPWlVqVX2a/EEBUdU1KrXLf40GoiiFXK///qpoiDXrOgqDR38JB0bw7SoL+ZB9o1RCkQjQ2CBYZKd/+VJxZRRZlqSkKiws0WFxUyCwsKiMy7hUVFhIaCrNQsKkTIsLivwKKigsj8XYlwt/WKi2N4d//uQRCSAAjURNIHpMZBGYiaQPSYyAAABLAAAAAAAACWAAAAApUF/Mg+0aohSIRobBAsMlO//Kk4soosy1JSFRYWaLC4qZBYWFRGZdwqKiwkNBVmoWFSJkWFxX4FFRQWR+LsS4W/rFRb/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VEFHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU291bmRib3kuZGUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMjAwNGh0dHA6Ly93d3cuc291bmRib3kuZGUAAAAAAAAAACU=");
	snd.play();
}

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
	lists_visible(true);
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

function ws_ext_copy_previous_week() {
	var weekStart = new Date();
	weekStart.setDate(weekStart.getDate() - weekStart.getDay() + 1);

	var previousWeekStart = new Date(weekStart.getTime());
	previousWeekStart.setDate(previousWeekStart.getDate() - 7);

	var weekEnd = new Date(weekStart.getTime());
	weekEnd.setDate(weekEnd.getDate() + 6);

	setTimeframe(previousWeekStart, weekEnd);
}

function ws_ext_this_week() {
	var weekStart = new Date();
	weekStart.setDate(weekStart.getDate() - weekStart.getDay() + 1);
	var weekEnd = new Date(weekStart.getTime());
	weekEnd.setDate(weekEnd.getDate() + 6);

	setTimeframe(weekStart, weekEnd);
}

function ws_ext_jump_days(x) {
	var startDate = $('#pick_in').datepicker('getDate');
	var endDate = $('#pick_out').datepicker('getDate');

	startDate.setDate(startDate.getDate() + x);
	endDate.setDate(endDate.getDate() + x);

	setTimeframe(startDate, endDate);
}

function ws_ext_on_input_change_process_entries_values(entries, newValue) {
	var sum = entries.map(function(a) {
		return a.duration;
	}).reduce(function(a, b){
		return a + b;
	}, 0);
	var requiredDifference = sum - newValue;
	// Calculate the new values
	if (newValue == sum) return;
	if (newValue < sum) {
		for (var i = 0; i < entries.length && requiredDifference > 0; i++) {
			if (entries[i].duration > requiredDifference) {
				entries[i].duration -= requiredDifference;
				requiredDifference = 0;
			} else {
				entries[i].deleted = true;
				requiredDifference -= entries[i].duration;
				entries[i].duration = 0;
			}
		}
	} else {
		entries[0].duration += newValue - sum;
	}

	requiredDifference = newValue - sum;

	// Make sure there is one non-deleted entry
	for (var i = 0; i < entries.length; i++) {
		if (!entries[i].deleted) return requiredDifference;
	}
	entries[0].deleted = false;
	return requiredDifference;
}

function ws_ext_on_input_change(e) {
	// Parse the data
	var entries = JSON.parse(e.target.dataset.entries);
	var newValue = 0;

	var divider = 1;
	var timeComponents = e.target.value.split(':');
	for (var i = 0; i < timeComponents.length; i++) {
		var c = timeComponents[i];
		if (!c) continue;
		c = parseFloat(c);
		if (isNaN(c)) {
			$(e.target).wrap('<label class="error" style="display: block; padding: 0;">invalid</label>');
			beep();
			return;
		}
		newValue += c / divider;
		divider *= 60;
	}

	if (!entries) {
		return $.post(ws_ext_path + "processor.php", {
			axAction: "add_weekday",
			date: e.target.dataset.date,
			duration: newValue,
			project: JSON.parse(e.target.dataset.project),
		}, ws_ext_reload);
	}

	var diff = ws_ext_on_input_change_process_entries_values(entries, newValue);

	// Submit the new values to the server
	$.post(ws_ext_path + "processor.php", {
		axAction: "update_weekday",
		entries: entries,
	}, ws_ext_reload);
}


function ws_ext_delete_project(e) {
	if (!confirm("Are you sure you want to delete all entries for this project?")) return;

	var row = e.target;
	while (!row.classList.contains('project-row')) {
		row = row.parentElement;
	}

	var allEntries = [];

	$(row).find('input').each(function(index, input) {
		var entries = JSON.parse(input.dataset.entries);
		if (!entries) return;
		allEntries = allEntries.concat(entries);
	});

	allEntries.forEach(function(entry) {
		entry.deleted = true;
	});

	$.post(ws_ext_path + "processor.php", {
		axAction: "update_weekday",
		entries: allEntries,
	}, ws_ext_reload);
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
	return;
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
	ws_ext_reload();

	weeksheet_timeframe_changed_hook_flag = 0;
	weeksheet_customers_changed_hook_flag = 0;
	weeksheet_projecws_changed_hook_flag = 0;
	weeksheet_activities_changed_hook_flag = 0;
	timesheet_timeframe_changed_hook_flag++;
	timesheet_customers_changed_hook_flag++;
	timesheet_projects_changed_hook_flag++;
	timesheet_activities_changed_hook_flag++;
}

function weeksheet_extension_timeframe_changed() {
	if ($('.ki_weeksheets').css('display') == "block") {
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

		setTimeout(function() {
			$('#' + ws_active_box).focus().select();;
		},0);

	});

	setTimeout(function() {
		ws_active_box = document.activeElement.id;
	},0);
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
			getBestRatesWeeksheet();
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
	startsec = now;
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
function quickdeleteWeeksheet(id) {
	$('#weekSheetEntry'+id+'>td>a').blur();

	if (confirmText != undefined) {
		var check = confirm(confirmText);
		if (check == false) {
			return;
		}
	}

	$('#weekSheetEntry' + id + '>td>a').removeAttr('onclick');
	$('#weekSheetEntry' + id + '>td>a.quickdeleteWeeksheet>img').attr("src", "../skins/" + skin + "/grfx/loading13.gif");

	$.post(ws_ext_path + "processor.php", {
		axAction: "quickdeleteWeeksheet",
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
function editRecordWeeksheet(id) {
	floaterShow(ws_ext_path + "floaters.php", "add_edit_weekSheetEntry", 0, id, 650);
}

/**
 * refresh the rate with a new value, if this is a new entry
 */
function getBestRatesWeeksheet() {
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
function pasteNowWeeksheet(value) {
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

function ws_edit_project(e) {
	var row = e.target;
	var ids = [];

	while (!row.classList.contains('project-row')) row = row.parentElement;

	$(row).find('*[data-entries]').each(function(index, element) {
		var entries = JSON.parse(element.dataset.entries);
		if (entries) {
			ids = ids.concat(entries.map(function(entry) {
				return entry.id;
			}));
		}
	});

	console.log(ids)

	editRecordWeeksheet(ids[0]);
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
