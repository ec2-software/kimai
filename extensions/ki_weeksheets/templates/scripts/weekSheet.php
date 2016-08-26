<?php

$timeframe = get_timeframe();
$in = new DateTime();
$out = new DateTime();
$in->setTimeStamp($timeframe[0]);
$out->setTimeStamp($timeframe[1]);
$oneDay = new DateInterval('P1D');

function formatHours($seconds) {
    if (!$seconds) return '';
    return round($seconds / 3600, 2) . ' h';
}
function formatTime($seconds) {
    if (!$seconds) return '';
    $hours = floor($seconds / 3600);
    $mins = floor(($seconds - ($hours * 3600)) / 60);
    if ($mins == 0) return $hours;
    if ($mins < 10) $mins = '0'.$mins;
    return "$hours:$mins";
}

if (!function_exists('weeksheetAccessAllowed')) {

    function weeksheetAccessAllowed($entry, $action, &$errors) {
        global $database, $kga;

      if (!isset($kga['user'])) {
        $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];
        return false;
      }


      if ($kga['conf']['editLimit'] != "-" && time() - $entry['end'] > $kga['conf']['editLimit'] && $entry['end'] != 0) {
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
}

if ($this->weekSheetEntries)
{
    ?>
        <div id="weekSheetTable">

          <table>
              <colgroup>
                  <col class="project"/>
                  <?php for ($day = clone $in; $day <= $out; $day->add($oneDay)): ?>
                      <col class="date"/>
                  <?php endfor; ?>
                  <col class="total"/>
              </colgroup>
            <tbody>
            <tr class="header odd">
                <th></th>
                <?php for ($day = clone $in; $day <= $out; $day->add($oneDay)): ?>
                    <th class="date <?php if ($day->format('N') >= 6) echo "weekend"; ?>"><?php echo $day->format('D j'); ?></th>
                <?php endfor; ?>
                <th class="total"><?php echo $this->kga['lang']['total'] ?></th>
            </tr>

    <?php
    $day_buffer     = 0; // last day entry
    $time_buffer    = 0; // last time entry
    $end_buffer     = 0; // last time entry
    $ws_buffer      = 0; // current time entry

    $projects = array();
    $dayTotals = array();

    foreach ($this->weekSheetEntries as $row)
    {
      $id = "$row[customerID]-$row[projectID]-$row[activityID]";
      if (isset($projects[$id]))
      {
        $entry = $projects[$id];
      }
      else
      {
        $entry = $row;
      }


      $date = new DateTime();
      $date->setTimeStamp($row['start']);
      $date = $date->format('Y-m-d');
      $pair = array('id' => $row['timeEntryID'] + 0, 'duration' => $row['duration']);

      if (isset($entry[$date]))
      {
        $entry[$date]['total'] += $row['duration'];
        $entry[$date]['entries'][] = $pair;
      }
      else
      {
        $entry[$date] = array(
          'total' => $row['duration'],
          'entries' => array($pair),
        );
      }

      if (isset($dayTotals[$date]))
      {
          $dayTotals[$date] += $row['duration'];
      }
      else
      {
          $dayTotals[$date] = $row['duration'];
      }

      $entry['total'] += $row['duration'];
      $projects[$id] = $entry;
    }

    $i = 0;

    foreach ($projects as $key => $project)
    { ?>
      <tr class="project-row <?php echo $i++ % 2 ? 'odd' : 'even'; ?>">
          <td class="project">
            <a href="#" onclick="ws_ext_delete_project(event)"><img src="../skins/standard/grfx/button_trashcan.png" /></a>
            <?php
            echo '<a href="#" class="preselect_lnk" onclick="ws_edit_project(event)">';
            echo "<strong>$project[projectName]</strong>";
            if ($project['customerName'] != 'ec² Software Solutions')
                echo " ($project[customerName])";
            echo " - $project[activityName]";
            echo "</a>";
            ?>
          </td>
          <?php

          for ($day = clone $in; $day <= $out; $day->add($oneDay))
          {
              $fdate = $day->format('Y-m-d');
              if (!isset($project[$fdate]))
              {
                  $project[$fdate] = array('total' => 0, 'id' => null);
              }
              $entry = $project[$fdate];
              ?>
              <td class="date">
                  <?php
                  $errors = array();
                  if (weeksheetAccessAllowed($entry, 'edit', $errors)) {
                      ?>
                      <input type=""
                        id="<?php echo "input-$fdate-$key" ?>"
                        value="<?php echo formatTime($entry['total']); ?>"
                        min="0"
                        max="24"
                        step=""
                        data-entries="<?php echo htmlspecialchars(json_encode($entry['entries'])); ?>"
                        data-project="<?php echo htmlspecialchars(json_encode($project)); ?>"
                        data-date="<?php echo htmlspecialchars($fdate); ?>"
                        onchange="ws_ext_on_input_change(event)"
                        />
                      <?php
                  } else {
                      echo $entry['total'];
                  } ?>
              </td>
              <?php
          }
          ?>

          <td class="total" id="<?php echo "sum-$key"; ?>">
              <?php echo formatHours($project['total']); ?>
          </td>
      </tr>
    <?php } ?>

    <tr class="day-totals <?php echo $i++ % 2 ? 'odd' : 'even'; ?>">
        <td class="project">
          Totals
        </td>
        <?php
        $totalTotals = 0;

        for ($day = clone $in; $day <= $out; $day->add($oneDay))
        {
            $fdate = $day->format('Y-m-d');
            echo '<td id="sum-' . $date . '" class="date">';
            echo formatHours($dayTotals[$fdate]);
            echo '</td>';
            $totalTotals += $dayTotals[$fdate];
        }
        ?>

        <td class="total"><?php echo formatHours($totalTotals); ?></td>
    </tr>
                </tbody>
            </table>
        </div>
    <?php
}
else
{
    echo $this->error();
}
?>

<script type="text/javascript">
    ws_user_annotations = <?php echo json_encode($this->user_annotations); ?>;
    ws_customer_annotations = <?php echo json_encode($this->customer_annotations) ?>;
    ws_project_annotations = <?php echo json_encode($this->project_annotations) ?>;
    ws_activity_annotations = <?php echo json_encode($this->activity_annotations) ?>;
    ws_total = '<?php echo $this->total?>';

    lists_update_annotations(parseInt($('#gui div.ki_weeksheets').attr('id').substring(7)),ws_user_annotations,ws_customer_annotations,ws_project_annotations,ws_activity_annotations);
    $('#display_total').html(ws_total);

  <?php if ($this->latest_running_entry == null): ?>
    updateRecordStatus(false);
  <?php else: ?>

    updateRecordStatus(<?php echo $this->latest_running_entry['timeEntryID']?>,<?php echo $this->latest_running_entry['start']?>,
                             <?php echo $this->latest_running_entry['customerID']?>,'<?php echo $this->jsEscape($this->latest_running_entry['customerName'])?>',
                             <?php echo $this->latest_running_entry['projectID']?> ,'<?php echo $this->jsEscape($this->latest_running_entry['projectName'])?>',
                             <?php echo $this->latest_running_entry['activityID']?>,'<?php echo $this->jsEscape($this->latest_running_entry['activityName'])?>');
  <?php endif; ?>

    function weeksheet_hide_column(name) {
        $('.'+name).hide();
    }

    <?php if (!$this->showTrackingNumber) { ?>
        weeksheet_hide_column('trackingnumber');
    <?php } ?>

</script>
