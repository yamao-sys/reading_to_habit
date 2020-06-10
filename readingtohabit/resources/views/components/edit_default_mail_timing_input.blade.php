<div class="mb_3">
    <select name="mail_timing_by_day">
    @php
    for($i=1; $i<=\RemindMailTimingConst::REMIND_MAIL_TIMING_DAY_LIMIT; $i++) {
        if ($i === $default_mail_timing['by_day']) {
            echo '<option value="'.$i.'" selected>'.$i.'</option>';
        }
        else {
            echo '<option value="'.$i.'">'.$i.'</option>';
        }
    }
    @endphp
    </select>日毎
</div>
<div class="mb_3">
    <select name="mail_timing_by_week">
    @php
    for($i=1; $i<=\RemindMailTimingConst::REMIND_MAIL_TIMING_WEEK_LIMIT; $i++) {
        if ($i === $default_mail_timing['by_week']) {
            echo '<option value="'.$i.'" selected>'.$i.'</option>';
        }
        else {
            echo '<option value="'.$i.'">'.$i.'</option>';
        }
    }
    @endphp
    </select>週間毎
</div>
<div>
    <select name="mail_timing_by_month">
    @php
    for($i=1; $i<=\RemindMailTimingConst::REMIND_MAIL_TIMING_MONTH_LIMIT; $i++) {
        if ($i === $default_mail_timing['by_month']) {
            echo '<option value="'.$i.'" selected>'.$i.'</option>';
        }
        else {
            echo '<option value="'.$i.'">'.$i.'</option>';
        }
    }
    @endphp
    </select>ヶ月毎
</div>
