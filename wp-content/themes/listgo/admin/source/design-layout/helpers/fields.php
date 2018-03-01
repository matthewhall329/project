<?php
/**
 * The file contains all of html fields
 * @since 1.0
 */
?>
<script id="wilokepa-textfield" type="text/template">
    <% if ( typeof (heading) != "undefined" ) { %>
        <div class="wpb_element_label"><%= heading %></div>
    <% } %>
        <input type="text" name="<%= param_name %>" class="js_param textfield <%= param_name %>" value="<%= value %>" />
    <% if ( typeof (description) != "undefined" ) { %>
        <span class="vc_description vc_clearfix"><%= description %></span>
    <% } %>
</script>

<script id="wilokepa-number" type="text/template">
    <% if ( typeof (heading) != "undefined" ) { %>
    <div class="wpb_element_label"><%= heading %></div>
    <% } %>
    <input type="number" name="<%= param_name %>" min="0" class="js_param textfield <%= param_name %>" value="<%= value %>" />
    <% if ( typeof (description) != "undefined" ) { %>
    <span class="vc_description vc_clearfix"><%= description %></span>
    <% } %>
</script>


<script id="wilokepa-btn_clone" type="text/template">
    <button disabled="disabled" data-target="<%= param_name %>" class="js_param btn_clone button button-primary"><%= heading %></button>
</script>


<script id="wilokepa-textarea" type="text/template">
    <% if ( typeof (heading) != "undefined" ) { %>
    <div class="wpb_element_label"><%= heading %></div>
    <% } %>
    <textarea name="<%= param_name %>" class="js_param textareafield <%= param_name %>" /><%= value %></textarea>
    <% if ( typeof (description) != "undefined" ) { %>
    <span class="vc_description vc_clearfix"><%= description %></span>
    <% } %>
</script>

<script id="wilokepa-checkbox" type="text/template">
    <div class="wo_wpb_toggle">
    <% if ( typeof (heading) != "undefined" ) { %>
        <div class="wpb_element_label"><%= heading %></div>
    <% } %>
        <label><input type="checkbox" name="<%= param_name %>" class="js_param checkboxfield <%= param_name %>" value="<%= value %>" /><span></span></label>
    </div>

    <% if ( typeof (description) != "undefined" ) { %>
        <span class="vc_description vc_clearfix"><%= description %></span>
    <% } %>
</script>

<script id="wilokepa-radio" type="text/template">
    <% if ( typeof (heading) != "undefined" ) { %>
    <div class="wpb_element_label"><%= heading %></div>
    <% } %>
    <% _.each(options, function(val, name) { %>
    <label>
        <input type="radio" name="<%= param_name %>" <% if (value == val) { checked } %> class="js_param radiofield <%= param_name %>" value="<%= val %>" />
        <%= name %>
    </label>
    <% } %>
    <% if ( typeof (description) != "undefined" ) { %>
    <span class="vc_description vc_clearfix"><%= description %></span>
    <% } %>
</script>

<script id="wilokepa-hidden" type="text/template">
    <input type="hidden" name="<%= param_name %>" class="js_param hiddenfield <%= param_name %>" value="<%= value %>" />
</script>

<script id="wilokepa-select" type="text/template">
    <% if ( typeof (heading) != "undefined" ) { %>
    <div class="wpb_element_label"><%= heading %></div>
    <% } %>
    <select name="<%= param_name %>" class="js_param selectfield <%= param_name %>" <% if ( typeof is_multiple != 'undefined' && !_.isEmpty(is_multiple) ) { %> multiple <% } %>>
        <% _.each(options, function(val, name) {  %>
        <option value="<%= val %>" <% if (value == val ) { %> selected <% } %>><%= name %></option>
        <% }) %>
    </select>
    <% if ( typeof (description) != "undefined" ) { %>
    <span class="vc_description vc_clearfix"><%= description %></span>
    <% } %>
</script>