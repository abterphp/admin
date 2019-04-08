<% extends("layouts/backend/default") %>

<% part("content") %>

<h1>{{ $title }}</h1>

<% include("./partials/backend/general/messages", compact("errorMessages", "successMessages")) %>

{{! $grid !}}

<% if ($createUrl) %>
<div class="form-group pmd-textfield pmd-textfield-floating-label">
    <a class="btn btn-success pmd-checkbox-ripple-effect" href="{{ $createUrl }}">{{ tr("framework:createNew") }}</a>
</div>
<% endif %>

<% endpart %>