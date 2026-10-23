<tr>
<td style="padding: 20px 0;">
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center" style="color: #9ca3af; font-size: 12px; padding: 20px 40px;">
{{ Illuminate\Mail\Markdown::parse($slot) }}
<p style="margin: 10px 0 0 0;">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
</td>
</tr>
</table>
</td>
</tr>
