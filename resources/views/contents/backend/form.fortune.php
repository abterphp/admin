<% extends("layouts/backend/default") %>

<% part("content") %>

<h1>{{ $title }}</h1>

<% include("./partials/backend/general/messages", compact("errorMessages", "successMessages")) %>

{{! $form !}}

<% endpart %>
