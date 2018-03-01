<?php
/**
 * All Emulates to be config here
 * @since 1.0
 */
?>
<script id="wilokepa-emulate-addnew" type="text/template">
    <div class="grid-item cube draggable" data-size="cube"></div>
</script>

<script id="wilokepa-emulate-list" type="text/template">

</script>

<script id="wilokepa-emulate-with-packery" type="text/template">
    <div class="wil_masonry <%= layout %>_layout wo_wpb_grid wo_wpb_grid--custom" data-isdragdrop="yes" data-gap="0" data-lg-col="<%= devices_settings.large.items_per_row %>" data-md-col="<%= devices_settings.medium.items_per_row %>" data-small-col="<%= devices_settings.small.items_per_row %>" data-xs-col="<%= devices_settings.extra_small.items_per_row %>" data-col="<%= current_col %>">
        <div class="grid-sizer"></div>
        <% for ( var i = 0, totalItem = items_size.length; i < general_settings.number_of_posts; i++ ) { %>
            <% if ( i >= totalItem ) { %>
            <div class="grid-item <%= items_size[i - totalItem] %>" data-size="<%= items_size[i - totalItem] %>"></div>
            <% }else{ %>
            <div class="grid-item <%= items_size[i] %>" data-size="<%= items_size[i] %>"></div>
            <% } %>
        <% } %>
    </div>

    <div class="design-actions">
        <span class="design-minus wiloke-remove-items" data-clicked="0">-</span>
        <input type="text" id="wo_number_of_items" name="number_of_posts" class="design-items wo_number_of_items wo_general_settings wpb_vc_param_value wpb-textinput textfield" value="<%= general_settings.number_of_posts %>">
        <span class="design-plus wiloke-add-items" data-clicked="0">+</span>
    </div>

</script>
