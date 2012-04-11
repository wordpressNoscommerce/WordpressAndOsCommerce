
</div>
<!-- /Content -->

<?php get_sidebar(); ?>

</div>
<!-- /Container -->

<div class="footer">
  <p class="copyright">
    &copy; 2011 <a href="<?php bloginfo('home'); ?>"><?php bloginfo('name'); ?> </a>. All Rights Reserved.
<?php //    <br />
//     <a href="#" onClick="toggleDebugDiv();" style="font-size: 150%; color: red;" ><b>toggleDebugDiv</b> </a>  ?>
    <span>Powered by <a href="http://wordpress.org">WordPress</a>.</span>
  </p>
  <p class="credits">
    Designed by <a href="http://wpshower.com">WPSHOWER</a>&nbsp;&&nbsp;<a href="">UV<img id="uv"
      src="/wp-content/themes/sight/images/moebius.png"
    /> </a>
  </p>
</div>
</div>
<div id="debugDiv" class="ui-draggable resizable">
  <span id="closeButton" onClick="toggleDebugDiv();">X</span>
  <?php // show included files in a toggable div
  $included_files = get_included_files();
  foreach ($included_files as $filename) {
      echo "$filename<br>";
  }
  ?>
</div>
<!-- Page generated: <?php timer_stop(1); ?> s, <?php echo get_num_queries(); ?> queries -->
  <?php wp_footer(); ?>

  <?php echo (get_option('ga')) ? get_option('ga') : '' ?>

</body>
</html>
