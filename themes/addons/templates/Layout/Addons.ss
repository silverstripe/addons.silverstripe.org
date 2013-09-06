<div class="page-header">
	<h1>Modules and Themes</h1>
</div>

<% with $AddonsSearchForm %>
	<form $FormAttributes>
		<% with $FieldMap %>
			<div class="addons-search-row">
				<% with $search %>
					<label for="$ID">$Title</label> $Field
				<% end_with %>

				<button type="submit" class="btn">
					<i class="icon-search"></i> Search Add-ons
				</button>
			</div>

			<div class="row">
				<div class="span4">
					<% with $type %>
						<label for="$ID">$Title</label> $Field
					<% end_with %>
				</div>
				<div class="span4">
					<% with $compatibility %>
						<label for="$ID">$Title</label> $Field
					<% end_with %>
				</div>
				<div class="span4">
					<% with $sort %>
						<label for="$ID">$Title</label> $Field
					<% end_with %>
				</div>
			</div>
		<% end_with %>
		<div id="legacy-search-notice" class="alert" style="display: none;">
			Some 2.4 compatible modules aren't listed here,
			you might have better luck <br>searching through our
			legacy database at <a href="http://www.silverstripe.org/modules">silverstripe.org/modules</a>
		</div>
	</form>
<% end_with %>

<hr>

<% if $Addons %>
	<table class="table table-hover table-striped table-boxed">
		<tbody>
			<% loop $Addons %>
				<tr>
					<td>
						<% if $Type == "module" %>
							<i class="icon-th-large"></i>
						<% else_if $Type == "theme" %>
							<i class="icon-picture"></i>
						<% end_if %>
					</td>
					<td><a href="$Link">$Name</a></td>
					<td>$Description.LimitCharacters(60)</td>
					<td><i class="icon-download"></i> $Downloads</td>
				</tr>
			<% end_loop %>
		</tbody>
	</table>

	<% include Pagination Items=$Addons %>
<% else %>
	<p>There are no add-ons to display.</p>
<% end_if %>
