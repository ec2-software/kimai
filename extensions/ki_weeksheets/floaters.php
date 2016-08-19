<?php
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

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/";
require("../../includes/kspi.php");

switch ($axAction) {

    case "add_edit_weekSheetEntry":
        if (isset($kga['customer'])) die();
    // ==============================================
    // = display edit dialog for weeksheet record   =
    // ==============================================
    $selected = explode('|', $axValue);

    $view->assign('projects', makeSelectBox("project", $kga['user']['groups']));
    $view->assign('activities', makeSelectBox("activity", $kga['user']['groups']));

    // edit record
    if ($id) {
        $weekSheetEntry = $database->timeSheet_get_data($id);
        $view->assign('id', $id);
        $view->assign('location', $weekSheetEntry['location']);

        // check if this entry may be edited
        if ($weekSheetEntry['userID'] == $kga['user']['userID']) {
          // the user's own entry
          if (!$database->global_role_allows($kga['user']['globalRoleID'], 'ki_weeksheets-ownEntry-edit'))
            break;
        }
        else if (count(array_intersect(
            $database->getGroupMemberships($kga['user']['userID']),
            $database->getGroupMemberships($weekSheetEntry['userID'])
          )) != 0) {
          // same group as the entry's user
          if (!$database->checkMembershipPermission($kga['user']['userID'], $database->getGroupMemberships($weekSheetEntry['userID']), 'ki_weeksheets-otherEntry-ownGroup-edit'))
            break;
        }
        else if (!$database->global_role_allows($kga['user']['globalRoleID'], 'ki_weeksheets-otherEntry-otherGroup-edit'))
          break;

        // set list of users to what the user may do
        $users = array();
        if ($database->global_role_allows($kga['user']['globalRoleID'], 'ki_weeksheets-otherEntry-otherGroup-edit'))
         $users  = makeSelectBox("allUser", $kga['user']['groups']);
        else if ($database->checkMembershipPermission($kga['user']['userID'], $database->getGroupMemberships($kga['user']['userID']), 'ki_weeksheets-otherEntry-ownGroup-edit')) {
          $users = makeSelectBox("sameGroupUser", $kga['user']['groups']);
          if ($database->global_role_allows($kga['user']['globalRoleID'], 'ki_weeksheets-ownEntry-edit'))
            $users[$kga['user']['userID']] = $kga['user']['name'];
        }

        $view->assign('users', $users);

        $view->assign('trackingNumber', $weekSheetEntry['trackingNumber']);
        $view->assign('description', $weekSheetEntry['description']);
        $view->assign('comment', $weekSheetEntry['comment']);

        $view->assign('showRate', $database->global_role_allows($kga['user']['globalRoleID'], 'ki_weeksheets-editRates'));
        $view->assign('rate', $weekSheetEntry['rate']);
        $view->assign('fixedRate', $weekSheetEntry['fixedRate']);

        $view->assign('cleared', $weekSheetEntry['cleared'] != 0);

        $view->assign('userID', $weekSheetEntry['userID']);

        $view->assign('start_day', date("d.m.Y", $weekSheetEntry['start']));
        $view->assign('start_time', date("H:i:s", $weekSheetEntry['start']));

        if ($weekSheetEntry['end'] == 0) {
          $view->assign('end_day', '');
          $view->assign('end_time', '');
        }
        else {
          $view->assign('end_day', date("d.m.Y", $weekSheetEntry['end']));
          $view->assign('end_time', date("H:i:s", $weekSheetEntry['end']));
        }

        $view->assign('approved', $weekSheetEntry['approved']);
        $view->assign('budget', $weekSheetEntry['budget']);

        // preselected
        $view->assign('projectID', $weekSheetEntry['projectID']);
        $view->assign('activityID', $weekSheetEntry['activityID']);

        $view->assign('commentType', $weekSheetEntry['commentType']);
        $view->assign('statusID', $weekSheetEntry['statusID']);
        $view->assign('billable_active', $weekSheetEntry['billable']);

        // budget
        $activityBudgets = $database->get_activity_budget($weekSheetEntry['projectID'], $weekSheetEntry['activityID']);
        $activityUsed = $database->get_budget_used($weekSheetEntry['projectID'], $weekSheetEntry['activityID']);
        $view->assign('budget_activity', round($activityBudgets['budget'], 2));
        $view->assign('approved_activity', round($activityBudgets['approved'], 2));
        $view->assign('budget_activity_used', $activityUsed);


        if (!isset($view->projects[$weekSheetEntry['projectID']])) {
          // add the currently assigned project to the list
          $projectData = $database->project_get_data($weekSheetEntry['projectID']);
          $customerData = $database->customer_get_data($projectData['customerID']);
          $view->projects[$projectData['projectID']] = $customerData['name'] . ':' . $projectData['name'];
        }

    } else {
        // create new record
        //$view->id = 0;

        $view->assign('statusID', $kga['conf']['defaultStatusID']);


        $users = array();
        if ($database->global_role_allows($kga['user']['globalRoleID'], 'ki_weeksheets-otherEntry-otherGroup-add'))
         $users  = makeSelectBox("allUser", $kga['user']['groups']);
        else if ($database->checkMembershipPermission($kga['user']['userID'], $database->getGroupMemberships($kga['user']['userID']), 'ki_weeksheets-otherEntry-ownGroup-add'))
          $users = makeSelectBox("sameGroupUser", $kga['user']['groups']);
        if ($database->global_role_allows($kga['user']['globalRoleID'], 'ki_weeksheets-ownEntry-add'))
          $users[$kga['user']['userID']] = $kga['user']['name'];

        $view->assign('users', $users);

        $view->assign('start_day', date("d.m.Y"));
        $view->assign('end_day', date("d.m.Y"));

        $view->assign('userID', $kga['user']['userID']);

        if ($kga['user']['lastRecord'] != 0 && $kga['conf']['roundTimesheetEntries'] != '') {
          $weekSheetData = $database->timeSheet_get_data($kga['user']['lastRecord']);
          $minutes = date('i');
          if ($kga['conf']['roundMinutes'] < 60) {
            if ($kga['conf']['roundMinutes'] <= 0) {
                    $minutes = 0;
            } else {
              while ($minutes % $kga['conf']['roundMinutes'] != 0) {
                if ($minutes >= 60) {
                  $minutes = 0;
                } else {
                  $minutes++;
                }
              }
            }
          }
          $seconds = date('s');
          if ($kga['conf']['roundSeconds'] < 60) {
            if ($kga['conf']['roundSeconds'] <= 0) {
                    $seconds = 0;
            } else {
              while ($seconds % $kga['conf']['roundSeconds'] != 0) {
                if ($seconds >= 60) {
                  $seconds = 0;
                } else {
                  $seconds++;
                }
              }
            }
          }
          $end = mktime(date("H"), $minutes, $seconds);
          $day = date("d");
          $dayEntry = date("d", $weekSheetData['end']);

          if ($day == $dayEntry) {
                  $view->assign('start_time', date("H:i:s", $weekSheetData['end']));
          } else {
                  $view->assign('start_time', date("H:i:s"));
          }
          $view->assign('end_time', date("H:i:s", $end));
        } else {
          $view->assign('start_time', date("H:i:s"));
          $view->assign('end_time', date("H:i:s"));
        }

        $view->assign('location', $kga['conf']['defaultLocation']);
        $view->assign('showRate', $database->global_role_allows($kga['user']['globalRoleID'], 'ki_weeksheets-editRates'));
        $view->assign('rate', $database->get_best_fitting_rate($kga['user']['userID'], $selected[0], $selected[1]));
        $view->assign('fixedRate', $database->get_best_fitting_fixed_rate($selected[0], $selected[1]));

        // budget
        $view->assign('budget_activity', 0);
        $view->assign('approved_activity', 0);
        $view->assign('budget_activity_used', 0);

        $view->assign('cleared', false);
    }

    $view->assign('status', $kga['conf']['status']);

    $billableValues = $kga['billable'];
    $billableText = array();
    foreach ($billableValues as $billableValue) {
      $billableText[] = $billableValue . '%';
    }
    $view->assign('billable', array_combine($billableValues, $billableText));

    echo $view->render("floaters/add_edit_weekSheetEntry.php");

    break;


    case "add_edit_weekSheetQuickNote":
        if (isset($kga['customer'])) die();
        // ================================================
        // = display edit dialog for weeksheet quick note =
        // ================================================
        $selected = explode('|', $axValue);

        $view->assign('projects', makeSelectBox("project", $kga['user']['groups']));
        $view->assign('activities', makeSelectBox("activity", $kga['user']['groups']));

        if ($id) {
            $weekSheetEntry = $database->timeSheet_get_data($id);
            $view->assign('id', $id);
            $view->assign('location', $weekSheetEntry['location']);

            // check if this entry may be edited
            if ($weekSheetEntry['userID'] == $kga['user']['userID']) {
                if (!$database->global_role_allows($kga['user']['globalRoleID'], 'ki_weeksheets-ownEntry-edit')) {
                    break;
                }
            } elseif ($database->is_watchable_user($kga['user'], $weekSheetEntry['userID'])) {
                if (!$database->checkMembershipPermission($kga['user']['userID'], $database->getGroupMemberships($weekSheetEntry['userID']), 'ki_weeksheets-otherEntry-ownGroup-edit')) {
                    break;
                }
            } elseif (!$database->global_role_allows($kga['user']['globalRoleID'], 'ki_weeksheets-otherEntry-otherGroup-edit')) {
                break;
            }

            // set list of users to what the user may do
            $users = array();
            if ($database->global_role_allows($kga['user']['globalRoleID'], 'ki_weeksheets-otherEntry-otherGroup-edit')) {
                $users = makeSelectBox("allUser", $kga['user']['groups']);
            } elseif ($database->checkMembershipPermission($kga['user']['userID'], $database->getGroupMemberships($kga['user']['userID']), 'ki_weeksheets-otherEntry-ownGroup-edit')) {
                $users = makeSelectBox("sameGroupUser", $kga['user']['groups']);
                if ($database->global_role_allows($kga['user']['globalRoleID'], 'ki_weeksheets-ownEntry-edit')) {
                    $users[$kga['user']['userID']] = $kga['user']['name'];
                }
            }

            $view->assign('users', $users);

            $view->assign('trackingNumber', $weekSheetEntry['trackingNumber']);
            $view->assign('description', $weekSheetEntry['description']);
            $view->assign('comment', $weekSheetEntry['comment']);

            $view->assign('commentType', $weekSheetEntry['commentType']);
            $view->assign('cleared', $weekSheetEntry['cleared'] != 0);

            $view->assign('userID', $weekSheetEntry['userID']);

            $view->assign('projectID', $weekSheetEntry['projectID']);
            $view->assign('activityID', $weekSheetEntry['activityID']);

            $view->assign('commentType', $weekSheetEntry['commentType']);
            $view->assign('statusID', $weekSheetEntry['statusID']);
            $view->assign('billable_active', $weekSheetEntry['billable']);
        }
        echo $view->render("floaters/add_edit_weekSheetQuickNote.php");

        break;
}
