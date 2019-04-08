<% if (is_array($errorMessages)) %>
    <% foreach ($errorMessages as $message) %>
        <div role="alert" class="alert alert-danger alert-dismissible">
            <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span></button>
            {{ $message }}
        </div>
    <% endforeach %>
<% endif %>

<% if (is_array($successMessages)) %>
    <% foreach ($successMessages as $message) %>
    <div role="alert" class="alert alert-success alert-dismissible">
        <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span></button>
        {{ $message }}
    </div>
    <% endforeach %>
<% endif %>
