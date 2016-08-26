<?php
$timeframe = get_timeframe();
$in = new DateTime();
$out = new DateTime();
$in->setTimeStamp($timeframe[0]);
$out->setTimeStamp($timeframe[1]);
$oneDay = new DateInterval('P1D');
 ?>
<div id="weekSheet_head">
    <div class="left">
        <?php if (isset($this->kga['user'])): ?>
            <a href="#" onclick="floaterShow('../extensions/ki_weeksheets/floaters.php','add_edit_weekSheetEntry',selected_project+'|'+selected_activity,0,650); $(this).blur(); return false;"><?php echo $this->kga['lang']['add'] ?></a>
        <?php endif; ?>
    </div>
    <table>
        <tbody>
        <tr>
            <td>&nbsp;</td>
            <td>
                <a href="#" id="ws_ext_previous_week" onclick="ws_ext_this_week()">Show This Week</a>
                |
                <a href="#" id="ws_ext_previous_week" onclick="ws_ext_copy_previous_week()">Show This and Last Week</a>
            </td>
            <td class="nav">
                <a href="#" onclick="ws_ext_jump_days(-7)">Previous Week</a>
                <a href="#" onclick="ws_ext_jump_days(7)">Next Week</a>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div id="weekSheet"><?php echo $this->weekSheet_display ?> </div>
<script type="text/javascript">
    $(document).ready(function () {
        ws_ext_onload();
    });
</script>
