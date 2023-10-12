<% loop $PopularAddons(5) %>
    <li>
        <a href="$Link">
            <span class="meta"><% include AddonDownloadStats %></span>
            <span class="name">$Name</span>
            <span class="description">$Description</span>
        </a>
    </li>
<% end_loop %>
