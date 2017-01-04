<span
	data-toggle="tooltip" 
	data-html="true"
	title="Composer installations: <br/>Total: $Downloads<br />
	Monthly: $DownloadsMonthly<br />
	Average downloads per day: $relativePopularityFormatted<br />
	Favers: $Favers <br/>Source: packagist.org"
>
	<i class="icon-download"></i> <% if $Score %>$Score<% else %>$Downloads<% end_if %>
</span>