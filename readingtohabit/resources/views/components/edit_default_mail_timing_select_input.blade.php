<div class="mb_3">
    @if ($default_mail_timing_select['by_day'] === 1)
    <input type="radio" name="mail_timing_select" value="by_day" checked="checked">日毎
    @else
    <input type="radio" name="mail_timing_select" value="by_day">日毎
    @endif
</div>
<div class="mb_3">
    @if ($default_mail_timing_select['by_week'] === 1)
    <input type="radio" name="mail_timing_select" value="by_week" checked="checked">週毎
    @else
    <input type="radio" name="mail_timing_select" value="by_week">週毎
    @endif
</div>
<div>
    @if ($default_mail_timing_select['by_month'] === 1)
    <input type="radio" name="mail_timing_select" value="by_month" checked="checked">月毎
    @else
    <input type="radio" name="mail_timing_select" value="by_month">月毎
    @endif
</div>
