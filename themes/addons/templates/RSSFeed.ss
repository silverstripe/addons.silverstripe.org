<?xml version="1.0"?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title>New module release - $Title</title>
		<link>$Link</link>
		<atom:link href="$Link" rel="self" type="application/rss+xml" />
		<description>$Description.XML</description>

		<% loop $Entries %>
		<item>
			<title>$Title.XML</title>
			<link>$AbsoluteLink</link>
			<% if $DescriptionText %><description>$DescriptionText</description><% end_if %>
			<% if $Date %><pubDate>$Date.Rfc822</pubDate>
			<% else %><pubDate>$Created.Rfc822</pubDate><% end_if %>
			<% if $Authors %>
			<dc:creator><% loop $Authors %><% if $Middle %>,<% end_if %>$Name<% end_loop %></dc:creator>
			<% end_if %>
			<guid>$AbsoluteLink</guid>
		</item>
		<% end_loop %>
	</channel>
</rss>
