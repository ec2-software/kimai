<?php

$timeframe = get_timeframe();
$in = new DateTime();
$out = new DateTime();
$in->setTimeStamp($timeframe[0]);
$out->setTimeStamp($timeframe[1]);
$oneDay = new DateInterval('P1D');

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
            <th>
                <?php for ($day = clone $in; $day <= $out; $day->add($oneDay)): ?>
                    <td class="date <?php if ($day->format('N') >= 6) echo "weekend"; ?>"><?php echo $day->format('D j'); ?></td>
                <?php endfor; ?>
                <td class="total"><?php echo $this->kga['lang']['total'] ?></td>
            </th>

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

      if (isset($entry[$date]))
      {
        $entry[$date]['total'] += $row['duration'];
      }
      else
      {
        $entry[$date] = array(
          'total' => $row['duration'],
          'id' => $row['timeEntryID'],
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

    foreach ($projects as $project)
    { ?>
      <tr>
          <td class="project">
            <?php
              echo $project['customerName'] . ' ' . $project['projectName'];
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
                  <input type="number" value="<?php if ($entry['total']) echo $entry['total']; ?>" />
              </td>
              <?php
          }
          ?>

          <td class="total"><?php echo $project['total'] ?></td>
      </tr>
    <?php } ?>

    <tr>
        <td class="project">
          Totals
        </td>
        <?php


        for ($day = clone $in; $day <= $out; $day->add($oneDay))
        {
            $fdate = $day->format('Y-m-d');
            echo '<td class="date">';
            echo $dayTotals[$fdate];
            echo '</td>';
        }
        ?>

        <td class="total"><?php echo $project['total'] ?></td>
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
