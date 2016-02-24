<% if $NextPage || $PreviousPage %>
	<div class="cms-content-actions cms-content-controls south next-prev clearfix">
		<% if $PreviousPage %>
			<a class="cms-panel-link prev-link" href="$PreviousPage.Link">&laquo; $PreviousPage.Title.XML</a>
		<% end_if %>

		<% if $NextPage %>
			<a class="cms-panel-link next-link" href="$NextPage.Link">$NextPage.Title.XML &raquo;</a>
		<% end_if %>
	</div>
<% end_if %>