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
    

    $i = 0;
    
    ksort($this->weekSheetEntries['projects']);

    foreach ($this->weekSheetEntries['projects'] as $key => $project)
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
            <?php if ($this->showTrackingNumber) { ?>
              <?php echo $this->escape($this->truncate($project['description'],50,'...')) ?>
                <?php if ($project['description']): ?>
                <a href="#" onclick="$(this).blur();  return false;" ><img src="<?php echo $this->skin('grfx/blase_sys.gif'); ?>" width="12" height="13" title='<?php echo $this->escape($project['description'])?>' border="0" /></a>
              <?php endif; ?>
                <?php echo $this->escape($project['trackingNumber']) ?>
            <?php } ?>
          </td>
          <?php

          for ($day = clone $in; $day <= $out; $day->add($oneDay))
          {
              $fdate = $day->format('Y-m-d');
              if (!isset($project['dates'][$fdate]))
              {
                  $project['dates'][$fdate] = array('total' => 0, 'id' => null, 'edit_locked' => false);
              }
              $entry = $project['dates'][$fdate];
              ?>
              <td class="date">
                  <?php if (!$entry['edit_locked']) { ?>
                      <input type=""
                        id="<?php echo "input-$fdate-$key" ?>"
                        value="<?php echo formatTime($entry['total']); ?>"
                        min="0"
                        max="24"
                        step=""
                        data-entries="<?php
                        if (isset($entry['entries'])) {
                         echo htmlspecialchars(json_encode($entry['entries']));
                        } else echo 'null'; ?>"
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
        $dayTotals = $this->weekSheetEntries['dayTotals'];

        for ($day = clone $in; $day <= $out; $day->add($oneDay))
        {
            $fdate = $day->format('Y-m-d');
            echo '<td id="sum-' . $day->format('Y-m-d') . '" class="date">';
            if (isset($dayTotals[$fdate])) {
				echo formatHours($dayTotals[$fdate]);
				$totalTotals += $dayTotals[$fdate];
			}
            echo '</td>';
        }
        ?>

        <td class="total"><?php echo formatHours($totalTotals); ?></td>
    </tr>
                </tbody>
            </table>
        </div>
    <?php
} else {
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

    updateRecordStatus(
        <?php echo $this->latest_running_entry['timeEntryID']?>,
        <?php echo $this->latest_running_entry['start']?>,
        <?php echo $this->latest_running_entry['customerID']?>,
        '<?php echo $this->jsEscape($this->latest_running_entry['customerName'])?>',
        <?php echo $this->latest_running_entry['projectID']?>,
        '<?php echo $this->jsEscape($this->latest_running_entry['projectName'])?>',
        <?php echo $this->latest_running_entry['activityID']?>,
        '<?php echo $this->jsEscape($this->latest_running_entry['activityName'])?>'
    );
  <?php endif; ?>

    function weeksheet_hide_column(name) {
        $('.'+name).hide();
    }

    <?php if (!$this->showTrackingNumber) { ?>
        weeksheet_hide_column('trackingnumber');
    <?php } ?>

</script>
