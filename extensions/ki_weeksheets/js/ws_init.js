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

// ===========
// TS EXT init
// ===========

// set path of extension
var ws_ext_path = "../extensions/ki_weeksheets/";
var ws_total = '';

var scroller_width;
var drittel;
var weekSheet_width;
var weekSheet_height;

var weeksheet_timeframe_changed_hook_flag = 0;
var weeksheet_customers_changed_hook_flag = 0;
var weeksheet_projecws_changed_hook_flag = 0;
var weeksheet_activities_changed_hook_flag = 0;



var ws_dayFormatExp = new RegExp("^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{2,4})$");
var ws_timeFormatExp = new RegExp("^([0-9]{1,2})(:[0-9]{1,2})?(:[0-9]{1,2})?$");

$(document).ready(function(){

    var ws_resizeTimer = null;
    $(window).bind('resize', function() {
       if (ws_resizeTimer) clearTimeout(ws_resizeTimer);
       ws_resizeTimer = setTimeout(ws_ext_resize, 500);
    });

    
});
