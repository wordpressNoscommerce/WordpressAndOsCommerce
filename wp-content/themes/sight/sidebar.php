<div class="sidebar">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar') ) :
$widget_args = array(
            'after_widget' => '</div></div>',
            'before_title' => '<h3>',
            'after_title' => '</h3><div class="widget-body clear">'
            );
            ?>

<?php endif; ?>
</div>
