<% extends("layouts/backend/default") %>

<% part("content") %>

<h1>{{ $title }}</h1>

{{! $dashboard !}}

<% include("./partials/backend/general/messages", compact("errorMessages", "successMessages")) %>

<% endpart %>
