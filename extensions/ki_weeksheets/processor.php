<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) Kimai-Development-Team since 2006
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

// ================
// = TS PROCESSOR =
// ================

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/";
require "../../includes/kspi.php";

/**
 * Sets a value on $target if the $original does not have the same value
 * as $_REQUEST. Used to change values if the original values from the
 * form have not changed.
 * 
 * @param $name      string    Index in the array (on $_REQUEST, 
 *                               $original, and $target) to get/set the
 *                               value
 * @param $original  array     Associative array containing the original
 *                               values to compare against.
 * @param $target    array     Associative array to store the value
 * @param $accessor  callable  Optional, called with the value of 
 *                               $_REQUEST[$name] as it's only parameter 
 *                               Should return the new value to set.
 */
function conditionalSet_REQUEST($name, $original, &$target, $accessor = null)
{
  if ($accessor === null)
    $val = $_REQUEST[$name];
  else
    $val = $accessor($_REQUEST[$name]);
  
  if ($original[$name] != $val)
    $target[$name] = $val;
}

// Convert the numeric string from using the decimal seperator configured in settings to use a period, as PHP uses to parse it.
function fixDecimal($val)
{
  return str_replace($kga['conf']['decimalSeparator'], '.', $val);
}

function weeksheetAccessAllowed($entry, $action, &$errors) {
    global $database, $kga;

    if (!isset($kga['user'])) {
        $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];
        return false;
    }


    if ($kga->isEditLimit() && time() - $entry['end'] > $kga->getEditLimit() && $entry['end'] != 0 ) {
        $errors[''] = $kga['lang']['editLimitError'];
        return;
    }
    $groups = $database->getGroupMemberships($entry['userID']);

    if ($entry['userID'] == $kga['user']['userID']) {
        $permissionName = 'ki_timesheets-ownEntry-' . $action;
        if ($database->global_role_allows($kga['user']['globalRoleID'], $permissionName)) {
            return true;
        } else {
            Kimai_Logger::logfile("missing global permission $permissionName for user " . $kga['user']['name']);
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];
            return false;
        }
    }

    $assignedOwnGroups = array_intersect($groups, $database->getGroupMemberships($kga['user']['userID']));

    if (count($assignedOwnGroups) > 0) {
        $permissionName = 'ki_timesheets-otherEntry-ownGroup-' . $action;
        if ($database->checkMembershipPermission($kga['user']['userID'], $assignedOwnGroups, $permissionName)) {
            return true;
        } else {
            Kimai_Logger::logfile("missing membership permission $permissionName of own group(s) " . implode(", ", $assignedOwnGroups) . " for user " . $kga['user']['name']);
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];
            return false;
        }

    }

    $permissionName = 'ki_timesheets-otherEntry-otherGroup-' . $action;
    if ($database->global_role_allows($kga['user']['globalRoleID'], $permissionName)) {
        return true;
    } else {
        Kimai_Logger::logfile("missing global permission $permissionName for user " . $kga['user']['name']);
        $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];
        return false;
    }

}

// ==================
// = handle request =
// ==================
switch ($axAction) {

    // ==============================================
    // = start a new recording based on another one =
    // ==============================================
    case 'record':
        $response = array();

        $weekSheetEntry = $database->timeSheet_get_data($id);

        $weekSheetEntry['start'] = time();
        $weekSheetEntry['end'] = 0;
        $weekSheetEntry['duration'] = 0;
        $weekSheetEntry['cleared'] = 0;

        $errors = array();
        weeksheetAccessAllowed($weekSheetEntry, 'edit', $errors);
        $response['errors'] = $errors;

        if (count($errors) == 0) {

            $newTimeSheetEntryID = $database->timeEntry_create($weekSheetEntry);

            $userData = array();
            $userData['lastRecord'] = $newTimeSheetEntryID;
            $userData['lastProject'] = $weekSheetEntry['projectID'];
            $userData['lastActivity'] = $weekSheetEntry['activityID'];
            $database->user_edit($kga['user']['userID'], $userData);


            $project = $database->project_get_data($weekSheetEntry['projectID']);
            $customer = $database->customer_get_data($project['customerID']);
            $activity = $database->activity_get_data($weekSheetEntry['activityID']);

            $response['customer'] = $customer['customerID'];
            $response['projectName'] = $project['name'];
            $response['customerName'] = $customer['name'];
            $response['activityName'] = $activity['name'];
            $response['currentRecording'] = $newTimeSheetEntryID;
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode($response);
        break;

    // ==================
    // = stop recording =
    // ==================
    case 'stop':
        $errors = array();

        $data = $database->timeSheet_get_data($id);

        weeksheetAccessAllowed($data, 'edit', $errors);

        if (count($errors) == 0) {
            $database->stopRecorder($id);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(
            array('errors' => $errors)
        );
        break;

    // =======================================
    // = set comment for a running recording =
    // =======================================
    case 'edit_running':
        $errors = array();

        $data = $database->timeSheet_get_data($id);

        weeksheetAccessAllowed($data, 'edit', $errors);

        if (count($errors) == 0) {
            if (isset($_REQUEST['project'])) {
                $database->timeEntry_edit_project($id, $_REQUEST['project']);
            }

            if (isset($_REQUEST['activity'])) {
                $database->timeEntry_edit_activity($id, $_REQUEST['activity']);
            }
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(
            array('errors' => $errors)
        );
        break;

    // =========================================
    // = Erase weeksheet entry via quickdeleteWeeksheet =
    // =========================================
    case 'quickdeleteWeeksheet':
        $errors = array();

        $data = $database->timeSheet_get_data($id);

        weeksheetAccessAllowed($data, 'delete', $errors);

        if (count($errors) == 0) {
          $database->timeEntry_delete($id);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(
            array('errors' => $errors)
        );
        break;

    // ==================================================
    // = Get the best rate for the project and activity =
    // ==================================================
    case 'bestFittingRates':
        $data = array('errors' => array());

        if (!isset($kga['user'])) {
            $data['errors'][] = $kga['lang']['editLimitError'];
        }

        if (!$database->global_role_allows($kga['user']['globalRoleID'], 'ki_timesheets-showRates')) {
            $data['errors'][] = $kga['lang']['editLimitError'];
        }

        if (count($data['errors']) == 0) {
            $data['hourlyRate'] = $database->get_best_fitting_rate($kga['user']['userID'], $_REQUEST['project_id'], $_REQUEST['activity_id']);
            $data['fixedRate'] = $database->get_best_fitting_fixed_rate($_REQUEST['project_id'], $_REQUEST['activity_id']);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode($data);
        break;


    // ==============================================================
    // = Get the new budget data after changing project or activity =
    // ==============================================================
    case 'budgets':
        $data = array('errors' => array());

        if (!isset($kga['user'])) {
            $data['errors'][] = $kga['lang']['editLimitError'];
        }

        if (!$database->global_role_allows($kga['user']['globalRoleID'], 'ki_timesheets-showRates')) {
            $data['errors'][] = $kga['lang']['editLimitError'];
        }

        if (count($data['errors']) == 0) {
            $weekSheetEntry = $database->timeSheet_get_data($_REQUEST['weekSheetEntryID']);
            // we subtract the used data in case the activity is the same as in the db, otherwise
            // it would get counted twice. For all aother cases, just set the values to 0
            // so we don't subtract too much
            if ($weekSheetEntry['activityID'] != $_REQUEST['activity_id'] || $weekSheetEntry['projectID'] != $_REQUEST['project_id']) {
                $weekSheetEntry['budget'] = 0;
                $weekSheetEntry['approved'] = 0;
                $weekSheetEntry['rate'] = 0;
            }
            $data['activityBudgets'] = $database->get_activity_budget($_REQUEST['project_id'], $_REQUEST['activity_id']);
            $data['activityUsed'] = $database->get_budget_used($_REQUEST['project_id'], $_REQUEST['activity_id']);
            $data['weekSheetEntry'] = $weekSheetEntry;
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode($data);
        break;

    // ==============================================
    // = Get all rates for the project and activity =
    // ==============================================
    case 'allFittingRates':
        $data = array('errors' => array());

        if (!isset($kga['user'])) {
            $data['errors'][] = $kga['lang']['editLimitError'];
        }

        if (!$database->global_role_allows($kga['user']['globalRoleID'], 'ki_timesheets-showRates')) {
            $data['errors'][] = $kga['lang']['editLimitError'];
        }

        if (count($data['errors']) == 0) {
            $rates = $database->allFittingRates($kga['user']['userID'], $_REQUEST['project'], $_REQUEST['activity']);

            if ($rates !== false) {
                foreach ($rates as $rate) {
                    $line = Kimai_Format::formatCurrency($rate['rate']);

                    $setFor = array(); // contains the list of "types" for which this rate was set
                    if ($rate['userID'] != null) {
                        $setFor[] = $kga['lang']['username'];
                    }
                    if ($rate['projectID'] != null) {
                        $setFor[] = $kga['lang']['project'];
                    }
                    if ($rate['activityID'] != null) {
                        $setFor[] = $kga['lang']['activity'];
                    }

                    if (count($setFor) != 0) {
                        $line .= ' (' . implode($setFor, ', ') . ')';
                    }

                    $data['rates'][] = array('value' => $rate['rate'], 'desc' => $line);
                }
            }
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode($data);
        break;

    // ==============================================
    // = Get all rates for the project and activity =
    // ==============================================
    case 'allFittingFixedRates':
        $data = array('errors' => array());

        if (!isset($kga['user'])) {
            $data['errors'][] = $kga['lang']['editLimitError'];
        }

        if (!$database->global_role_allows($kga['user']['globalRoleID'], 'ki_timesheets-showRates')) {
            $data['errors'][] = $kga['lang']['editLimitError'];
        }

        if (count($data['errors']) == 0) {
            $rates = $database->allFittingFixedRates($_REQUEST['project'], $_REQUEST['activity']);

            if ($rates !== false) {
                foreach ($rates as $rate) {
                    $line = Kimai_Format::formatCurrency($rate['rate']);

                    $setFor = array(); // contains the list of "types" for which this rate was set
                    if ($rate['projectID'] != null) {
                        $setFor[] = $kga['lang']['project'];
                    }

                    if ($rate['activityID'] != null) {
                        $setFor[] = $kga['lang']['activity'];
                    }

                    if (count($setFor) != 0) {
                        $line .= ' (' . implode($setFor, ', ') . ')';
                    }

                    $data['rates'][] = array('value' => $rate['rate'], 'desc' => $line);
                }
            }

        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode($data);
        break;

    // ==================================================
    // = Get the best rate for the project and activity =
    // ==================================================
    case 'reload_activities_options':
        if (isset($kga['customer'])) die();
        $activities = $database->get_activities_by_project($_REQUEST['project'], $kga['user']['groups']);
        foreach ($activities as $activity) {
            if (!$activity['visible']) {
                continue;
            }
            echo '<option value="' . $activity['activityID'] . '">' . $activity['name'] . '</option>\n';
        }
        break;

    // =============================================
    // = Load weeksheet data from DB and return it =
    // =============================================
    case 'reload_weekSheet':
        $filters = explode('|', $axValue);
        
        if (empty($filters[0])) {
            $filterUsers = array();  
        } else {
            $filterUsers = explode(':', $filters[0]);
        }

        $filterCustomers = array_map(
            function($customer) {
                return $customer['customerID'];
            },
            $database->get_customers($kga['user']['groups'])
        );
        
        if (!empty($filters[1])) {
            $filterCustomers = array_intersect($filterCustomers, explode(':', $filters[1]));
        }

        $filterProjects = array_map(
            function($project) {
                return $project['projectID'];
            }, 
            $database->get_projects($kga['user']['groups'])
        );
        if (!empty($filters[2])) {
            $filterProjects = array_intersect($filterProjects, explode(':', $filters[2]));
        }

        $filterActivities = array_map(
            function($activity) {
                return $activity['activityID'];
            },
            $database->get_activities($kga['user']['groups'])
        );

        if (!empty($filters[3])) {
            $filterActivities = array_intersect($filterActivities, explode(':', $filters[3]));  
        }
          

        // if no userfilter is set, set it to current user
        if (isset($kga['user']) && count($filterUsers) == 0) {
            array_push($filterUsers, $kga['user']['userID']);
        }

        if (isset($kga['customer'])) {
            $filterCustomers = array($kga['customer']['customerID']);
        }

        $weekSheetEntries = $database->get_weekSheet($in, $out, $filterUsers, $filterCustomers, $filterProjects, $filterActivities, 1);
        if (count($weekSheetEntries) > 0) {
            $view->assign('weekSheetEntries', $weekSheetEntries);
        } else {
            $view->assign('weekSheetEntries', 0);
        }
        $view->assign('latest_running_entry', $database->get_latest_running_entry());
        $view->assign('total', Kimai_Format::formatDuration($database->get_duration($in, $out, $filterUsers, $filterCustomers, $filterProjects, $filterActivities)));

        $ann = $database->get_time_users($in, $out, $filterUsers, $filterCustomers, $filterProjects, $filterActivities);
        Kimai_Format::formatAnnotations($ann);
        $view->assign('user_annotations', $ann);

        $ann = $database->get_time_customers($in, $out, $filterUsers, $filterCustomers, $filterProjects, $filterActivities);
        Kimai_Format::formatAnnotations($ann);
        $view->assign('customer_annotations', $ann);

        $ann = $database->get_time_projects($in, $out, $filterUsers, $filterCustomers, $filterProjects, $filterActivities);
        Kimai_Format::formatAnnotations($ann);
        $view->assign('project_annotations', $ann);

        $ann = $database->get_time_activities($in, $out, $filterUsers, $filterCustomers, $filterProjects, $filterActivities);
        Kimai_Format::formatAnnotations($ann);
        $view->assign('activity_annotations', $ann);

        $view->assign('hideComments', true);
        $view->assign('showOverlapLines', false);
        $view->assign('showTrackingNumber', false);

        // user can change these settings
        if (isset($kga['user'])) {
            $view->assign('hideComments', !$kga->getSettings()->isShowComments());
            $view->assign('showOverlapLines', $kga->getSettings()->isShowOverlapLines());
            $view->assign('showTrackingNumber', $kga->isTrackingNumberEnabled() && $kga->getSettings()->isShowTrackingNumber());
        }

        $view->assign('showRates', isset($kga['user']) && $database->global_role_allows($kga['user']['globalRoleID'], 'ki_timesheets-showRates'));

        echo $view->render("weekSheet.php");
        break;


    // ==============================
    // = add / edit weekSheet entry =
    // ==============================
    case 'add_edit_weekSheetEntry':
        header('Content-Type: application/json;charset=utf-8');
        $errors = array();

        $action = 'add';
        if ($id) {
            $action = 'edit';
        }
        if (isset($_REQUEST['erase'])) {
            $action = 'delete';
        }

        if ($id) {
            $data = $database->timeSheet_get_data($id);

            // check if editing or deleting with the old values would be allowed
            if (!weeksheetAccessAllowed($data, $action, $errors)) {
                echo json_encode(array('errors' => $errors));
                break;
            }
        }
        
        // Get the other id's
        $timeframe = get_timeframe();
        $in = $timeframe[0];
        $out = $timeframe[1];
        $sheet = $database->get_weekSheet($in, $out, null, array($data['customerID']), null, 1);
        $hash = "$data[customerID]-$data[projectID]-$data[activityID]-$data[description]";
        $ids = $sheet['projects'][$hash]['ids'];

        // delete the record and stop processing at this point
        if (isset($_REQUEST['erase'])) {
            foreach ($ids as $id) {
                $database->timeEntry_delete($id);
            }
            echo json_encode(array('errors' => $errors));
            break;
        }

      if ($id) { // TIME RIGHT - NEW OR EDIT ?

        if (!weeksheetAccessAllowed($data, $action, $errors)) {
            echo json_encode(array('errors'=>$errors));
            break;
        }
        
        $odata = $data;
        
        foreach ($ids as $id) {
            $data = $database->timeSheet_get_data($id);
            if (!weeksheetAccessAllowed($data, $action, $errors)) {
                echo json_encode(array('errors' => $errors)); 
                break;
            }
        
            $data['projectID']      = $_REQUEST['projectID'];
            $data['activityID']     = $_REQUEST['activityID']; 
            $data['description']    = $_REQUEST['description'];
          
            conditionalSet_REQUEST('location', $odata, $data);
            conditionalSet_REQUEST('trackingNumber', $odata, $data, function() { return isset($_REQUEST['trackingNumber']); });
            conditionalSet_REQUEST('comment', $odata, $data);
            conditionalSet_REQUEST('commentType', $odata, $data);
            
            if ($database->global_role_allows($kga['user']['globalRoleID'], 'ki_timesheets-editRates')) {
              conditionalSet_REQUEST('rate', $odata, $data, 'fixDecimal');
              conditionalSet_REQUEST('fixedRate', $odata, $data, 'fixDecimal');
            } else if (!$id) {
              $data['rate'] = $database->get_best_fitting_rate($kga['user']['userID'], $data['projectID'], $data['activityID']);
              conditionalSet_REQUEST('fixedRate', $odata, $data, 'fixDecimal');
            }
            
            conditionalSet_REQUEST('cleared', $odata, $data, function() { return isset($_REQUEST['cleared']); });
            conditionalSet_REQUEST('statusID', $odata, $data);
            conditionalSet_REQUEST('billable', $odata, $data);
            conditionalSet_REQUEST('budget', $odata, $data, 'fixDecimal');
            conditionalSet_REQUEST('approved', $odata, $data, 'fixDecimal');
            conditionalSet_REQUEST('userID', $odata, $data);
            
            if (!is_numeric($data['activityID']))
              $errors['activityID'] = $kga['lang']['errorMessages']['noActivitySelected'];
            
            if (!is_numeric($data['projectID']))
              $errors['projectID'] = $kga['lang']['errorMessages']['noProjectSelected'];
            
            if (count($errors) > 0) {
                echo json_encode(array('errors'=>$errors));
                break 2;
            }
            // TIME RIGHT - EDIT ENTRY
            Kimai_Logger::logfile("timeEntry_edit: " . $id);
            $database->timeEntry_edit($id, $data);
        }
    } else {
        // NEW ENTRY
        
        $data['projectID'] = $_REQUEST['projectID'];
        $data['activityID'] = $_REQUEST['activityID'];
        $data['location'] = $_REQUEST['location'];
        $data['trackingNumber'] = isset($_REQUEST['trackingNumber']) ? $_REQUEST['trackingNumber'] : '';
        $data['description'] = $_REQUEST['description'];
        $data['comment'] = $_REQUEST['comment'];
        $data['commentType'] = $_REQUEST['commentType'];
        if ($database->global_role_allows($kga['user']['globalRoleID'], 'ki_timesheets-editRates')) {
            $data['rate'] = fixDecimal($_REQUEST['rate']);
            $data['fixedRate'] = fixDecimal($_REQUEST['fixedRate']);
        } else if (!$id) {
            $data['rate'] = $database->get_best_fitting_rate($kga['user']['userID'], $data['projectID'], $data['activityID']);
            $data['fixedRate'] = fixDecimal($_REQUEST['fixedRate']);
        }
        $data['cleared'] = isset($_REQUEST['cleared']);
        $data['statusID'] = $_REQUEST['statusID'];
        $data['billable'] = $_REQUEST['billable'];
        $data['budget'] = fixDecimal($_REQUEST['budget']);
        $data['approved'] = fixDecimal($_REQUEST['approved']);
        $data['userID'] = $_REQUEST['userID'];
        $data['start'] = $in + 32400;
        $data['duration'] = 0;
        $data['end'] = $data['start'];

        if (!is_numeric($data['activityID'])) {
            $errors['activityID'] = $kga['lang']['errorMessages']['noActivitySelected'];
        }
        if (!is_numeric($data['projectID'])) {
            $errors['projectID'] = $kga['lang']['errorMessages']['noProjectSelected'];
        }

        if (count($errors) > 0) {
            echo json_encode(array('errors' => $errors));
            return;
        }

        // TIME RIGHT - NEW ENTRY

        $database->transaction_begin();

        foreach ($_REQUEST['userID'] as $userID) {
              $data['userID'] = $userID;
          if (!weeksheetAccessAllowed($data, $action, $errors)) {
            echo json_encode(array('errors' => $errors));
            $database->transaction_rollback();
            break 2;
          }

          Kimai_Logger::logfile("timeEntry_create");
          $createdId = $database->timeEntry_create($data);
          if (!$createdId) {
            $errors[''] = $kga['lang']['error'];
          }
        }

            $database->transaction_end();
        }

        echo json_encode(array('errors' => $errors));
        break;

    // ===================================
    // = add / edit timeSheet quick note =
    // ===================================
    case 'add_edit_timeSheetQuickNote':
        header('Content-Type: application/json;charset=utf-8');
        $errors = array();

        $action = 'add';

        if ($id) {
            $action = 'edit';
            $data = $database->timeSheet_get_data($id);

            // check if editing or deleting with the old values would be allowed
            if (!timesheetAccessAllowed($data, $action, $errors)) {
                echo json_encode(array('errors' => $errors));
                break;
            }
        }

        $data['location'] = $_REQUEST['location'];
        $data['trackingNumber'] = isset($_REQUEST['trackingNumber']) ? $_REQUEST['trackingNumber'] : '';
        $data['comment'] = $_REQUEST['comment'];
        $data['commentType'] = $_REQUEST['commentType'];
        $data['userID'] = $_REQUEST['userID'];

        if (!timesheetAccessAllowed($data, $action, $errors)) {
            echo json_encode(array('errors' => $errors));
            break;
        }
        if ($id) {
            // TIME RIGHT - EDIT ENTRY
            Kimai_Logger::logfile("timeNote_edit: " . $id);
            $database->timeEntry_edit($id, $data);
        } else {
            // TIME RIGHT - NEW ENTRY
            Kimai_Logger::logfile("timeNote_create");
            $database->timeEntry_create($data);
        }
        echo json_encode(array('errors' => $errors));
        break;

    case 'add_weekday':
        $action = 'add';
        $errors = array();
        $data = $_REQUEST['project'];

        $data['start'] = strtotime($_REQUEST['date']);
        $data['duration'] = $_REQUEST['duration'] * 3600;
        $data['end'] = $data['start'] + $data['duration'];

        if (!weeksheetAccessAllowed($data, $action, $errors)) {
          echo json_encode(array('errors'=>$errors));
          break;
        }

        $createdId = $database->timeEntry_create($data);
        if (!$createdId) {
            $errors[''] = $kga['lang']['error'];
            $errors[] = $database->conn->Error();
        }

        //$database->transaction_end();
        echo json_encode(array('errors' => $errors));
        break;

    case 'update_weekday':
        //$database->transaction_begin();
        $errors = array();

        foreach ($_REQUEST['entries'] as $entry) {
            $data = $database->timeSheet_get_data($entry['id']);
            $errors = array();
            
            if (isset($entry['deleted']) && $entry['deleted']) {
              $action = 'delete';
              if (!weeksheetAccessAllowed($data, $action, $errors)) {
                echo json_encode(array('errors'=>$errors));
                $database->transaction_rollback();
                break 2;
              }
              $deletedId = $database->timeEntry_delete($entry['id']);
              if (!$deletedId) {
                $errors[''] = $kga['lang']['error'];
                echo json_encode(array('errors' => $errors));
                //$database->transaction_rollback();
                break 2;
              }
            } else {
              $action = 'edit';

              if (!weeksheetAccessAllowed($data, $action, $errors)) {
                echo json_encode(array('errors'=>$errors));
                //$database->transaction_rollback();
                break 2;
              }

              $data['duration'] = $entry['duration'] * 3600;
              $data['end'] = $data['start'] + $data['duration'];

              Kimai_Logger::logfile("timeEntry_edit");
              $createdId = $database->timeEntry_edit($entry['id'], $data);
              if (!$createdId) {
                $errors[''] = $kga['lang']['error'];
              }
            }
        }

        //$database->transaction_end();

        echo json_encode(array('errors' => $errors));
        break;
}
