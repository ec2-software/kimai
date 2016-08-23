<?php
$timeframe = get_timeframe();
$in = new DateTime();
$out = new DateTime();
$in->setTimeStamp($timeframe[0]);
$out->setTimeStamp($timeframe[1]);
$oneDay = new DateInterval('P1D');
 ?>
<div id="weekSheet_head" class="ext ki_weeksheet">
    <div class="left">
        <?php if (isset($this->kga['user'])): ?>
            <a href="#" onclick="floaterShow('../extensions/ki_weeksheets/floaters.php','add_edit_weekSheetEntry',selected_project+'|'+selected_activity,0,650); $(this).blur(); return false;"><?php echo $this->kga['lang']['add'] ?></a>
        <?php endif; ?>
    </div>
    <table>
        <colgroup>
            <col class="options">
        </colgroup>
        <tbody>
        <tr>
            <td class="option">&nbsp;</td>
            <td class="copy-previous"><a href="#" id="ws_ext_previous_week" onclick="ws_ext_copy_previous_week()">Copy Previous Week</a></td>
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
