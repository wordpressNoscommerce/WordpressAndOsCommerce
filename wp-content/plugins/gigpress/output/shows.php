<?php

/** from admin/shows.php  function gigpress_admin_shows() { **/
function gigpress_admin_shows_ref() {

	global $wpdb, $gpo;

	// Checks for filtering and pagination
	$url_args = '';
	$further_where = '';
	$pagination_args = array();

	switch($_GET['scope']) {
		case 'upcoming':
			$condition = ">= '" . GIGPRESS_NOW . "'";
			$url_args .= '&amp;scope=upcoming';
			$pagination_args['scope'] = 'upcoming';
			break;
		case 'past':
			$condition = "< '" . GIGPRESS_NOW . "'";
			$url_args .= '&amp;scope=past';
			$pagination_args['scope'] = 'past';
			break;
		default:
			$condition = 'IS NOT NULL';
	}

	global $current_user;
	get_currentuserinfo();

	switch($_GET['sort']) {
		case 'asc':
			$sort = 'ASC';
			update_usermeta($current_user->ID, 'gigpress_sort', $sort);
			break;
		case 'desc':
			$sort = 'DESC';
			update_usermeta($current_user->ID, 'gigpress_sort', $sort);
			break;
	}

	if(!isset($_GET['sort'])) {
		if( ! $sort = get_usermeta($current_user->ID, 'gigpress_sort')) {
			$sort = 'DESC';
			update_usermeta($current_user->ID, 'gigpress_sort', $sort);
		}
	}
	fb($condition.$sort);

	if(isset($_GET['gp-page'])) $url_args .= '&amp;gp-page=' . $_GET['gp-page'];

	if(isset($_GET['artist_id']) && $_GET['artist_id'] != '-1') {
		$further_where .= ' AND s.show_artist_id = ' . $wpdb->prepare('%d', $_GET['artist_id']) . ' ';
		$pagination_args['artist_id'] = $_GET['artist_id'];
		$url_args .= '&amp;artist_id=' . $_GET['artist_id'];
	}

	if(isset($_GET['tour_id']) && $_GET['tour_id'] != '-1') {
		$further_where .= ' AND s.show_tour_id = ' . $wpdb->prepare('%d', $_GET['tour_id']) . ' ';
		$pagination_args['tour_id'] = $_GET['tour_id'];
		$url_args .= '&amp;tour_id=' . $_GET['tour_id'];
	}

	if(isset($_GET['venue_id']) && $_GET['venue_id'] != '-1') {
		$further_where .= ' AND s.show_venue_id = ' . $wpdb->prepare('%d', $_GET['venue_id']) . ' ';
		$pagination_args['venue_id'] = $_GET['venue_id'];
		$url_args .= '&amp;venue_id=' . $_GET['venue_id'];
	}

	// Build pagination
	$show_count = $wpdb->get_var(
		"SELECT COUNT(*) FROM " . GIGPRESS_ARTISTS . " AS a, " . GIGPRESS_VENUES . " as v, " . GIGPRESS_SHOWS ." AS s LEFT JOIN  " . GIGPRESS_TOURS . " AS t ON s.show_tour_id = t.tour_id WHERE show_expire " . $condition . " AND show_status != 'deleted' AND s.show_artist_id = a.artist_id AND s.show_venue_id = v.venue_id " . $further_where . "ORDER BY show_date " . $sort . ",show_time " . $sort
		);
	if($show_count) {
		$pagination_args['page'] = 'gigpress-shows';
		$pagination = gigpress_admin_pagination($show_count, 10, $pagination_args);
	}

	$limit = (isset($_GET['gp-page'])) ? $pagination['offset'].','.$pagination['records_per_page'] : 10;

	// Build the query
	$shows = $wpdb->get_results("
		SELECT * FROM " . GIGPRESS_ARTISTS . " AS a, " . GIGPRESS_VENUES . " as v, " . GIGPRESS_SHOWS ." AS s LEFT JOIN  " . GIGPRESS_TOURS . " AS t ON s.show_tour_id = t.tour_id WHERE show_expire " . $condition . " AND show_status != 'deleted' AND s.show_artist_id = a.artist_id AND s.show_venue_id = v.venue_id " . $further_where . "ORDER BY show_date " . $sort . ",show_expire " . $sort . ",show_time " . $sort . " LIMIT " . $limit);

	?>

	<div class="wrap gigpress">

		<?php screen_icon('gigpress'); ?>
		<h2><?php _e("Shows", "gigpress"); ?></h2>

		<ul class="subsubsub">
		<?php
			$all = $wpdb->get_var("SELECT COUNT(show_id) FROM " . GIGPRESS_SHOWS ." WHERE show_status != 'deleted'");
			$upcoming = $wpdb->get_var("SELECT count(show_id) FROM " . GIGPRESS_SHOWS . " WHERE show_expire >= '" . GIGPRESS_NOW . "' AND show_status != 'deleted'");
			$past = $wpdb->get_var("SELECT count(show_id) FROM " . GIGPRESS_SHOWS . " WHERE show_expire < '" . GIGPRESS_NOW . "' AND show_status != 'deleted'");
			echo('<li><a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=gigpress-shows"');
			if(!isset($_GET['scope'])) echo(' class="current"');
			echo('>' . __("All", "gigpress") . '</a> <span class="count">(' . $all	. ')</span> | </li>');
			echo('<li><a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=gigpress-shows&amp;scope=upcoming"');
			if(isset($_GET['scope']) && $_GET['scope'] == 'upcoming') echo(' class="current"');
			echo('>' . __("Upcoming", "gigpress") . '</a> <span class="count">(' . $upcoming	. ')</span> | </li>');
			echo('<li><a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=gigpress-shows&amp;scope=past"');
			if(isset($_GET['scope']) && $_GET['scope'] == 'past') echo(' class="current"');
			echo('>' . __("Past", "gigpress") . '</a> <span class="count">(' . $past	. ')</span></li>');
		?>
		</ul>

		<div class="tablenav">
			<div class="alignleft">
				<form action="" method="get">
					<div>
						<input type="hidden" name="page" value="gigpress-shows" />
						<?php if(isset($_GET['scope'])) : ?>
						<input type="hidden" name="scope" value="<?php echo $_GET['scope']; ?>" />
						<?php endif; ?>
						<select name="artist_id">
							<option value="-1"><?php _e("View all artists", "gigpress"); ?></option>
						<?php $artistdata = fetch_gigpress_artists();
						if($artistdata) {
							foreach($artistdata as $artist) {
								$selected = (isset($_GET['artist_id']) && $_GET['artist_id'] == $artist->artist_id) ? ' selected="selected"' : '';
								echo('<option value="' . $artist->artist_id . '"' . $selected . '>' . gigpress_db_out($artist->artist_name) . '</option>');
							}
						} else {
							echo('<option value="-1">' . __("No artists in the database", "gigpress") . '</option>');
						}
						?>
						</select>

						<select name="tour_id">
							<option value="-1"><?php _e("View all tours", "gigpress"); ?></option>
						<?php $tourdata = fetch_gigpress_tours();
						if($tourdata) {
							foreach($tourdata as $tour) {
								$selected = (isset($_GET['tour_id']) && $_GET['tour_id'] == $tour->tour_id) ? ' selected="selected"' : '';
								echo('<option value="' . $tour->tour_id . '"' . $selected . '>' . gigpress_db_out($tour->tour_name) . '</option>');
							}
						} else {
							echo('<option value="-1">' . __("No tours in the database", "gigpress") . '</option>');
						}
						?>
						</select>

						<select name="venue_id">
							<option value="-1"><?php _e("View all venues", "gigpress"); ?></option>
						<?php $venuedata = fetch_gigpress_venues();
						if($venuedata) {
							foreach($venuedata as $venue) {
								$selected = (isset($_GET['venue_id']) && $_GET['venue_id'] == $venue->venue_id) ? ' selected="selected"' : '';
								echo('<option value="' . $venue->venue_id . '"' . $selected . '>' . gigpress_db_out($venue->venue_name) . '</option>');
							}
						} else {
							echo('<option value="-1">' . __("No venues in the database", "gigpress") . '</option>');
						}
						?>
						</select>

						<select name="sort">
							<option value="desc"<?php if($sort == 'DESC') echo(' selected="selected"'); ?>><?php _e("Descending", "gigpress"); ?></option>
							<option value="asc"<?php if($sort == 'ASC') echo(' selected="selected"'); ?>><?php _e("Ascending", "gigpress"); ?></option>
						</select>
						<input type="submit" value="Filter" class="button-secondary" />
					</div>
				</form>
			</div>
			<?php if($pagination) echo $pagination['output']; ?>
			<div class="clear"></div>
		</div>

		<form action="" method="post">
			<?php wp_nonce_field('gigpress-action') ?>
			<input type="hidden" name="gpaction" value="delete" />

		<table class="widefat">
			<thead>
				<tr>
					<th scope="col" class="column-cb check-column"><input type="checkbox" /></th>
					<th scope="col"><?php _e("Date", "gigpress"); ?></th>
					<th scope="col"><?php _e("Artist", "gigpress"); ?></th>
					<th scope="col"><?php _e("City", "gigpress"); ?></th>
					<th scope="col"><?php _e("Venue", "gigpress"); ?></th>
					<th scope="col"><?php _e("Country", "gigpress"); ?></th>
					<th scope="col"><?php _e("Tour", "gigpress") ?></th>
					<th class="gp-centre" scope="col"><?php _e("Actions", "gigpress"); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th scope="col" class="column-cb check-column"><input type="checkbox" /></th>
					<th scope="col"><?php _e("Date", "gigpress"); ?></th>
					<th scope="col"><?php _e("Artist", "gigpress"); ?></th>
					<th scope="col"><?php _e("City", "gigpress"); ?></th>
					<th scope="col"><?php _e("Venue", "gigpress"); ?></th>
					<th scope="col"><?php _e("Country", "gigpress"); ?></th>
					<th scope="col"><?php _e("Tour", "gigpress") ?></th>
					<th class="gp-centre" scope="col"><?php _e("Actions", "gigpress"); ?></th>
				</tr>
			</tfoot>
			<tbody>
		<?php

		// Do we have dates?
		if($shows != FALSE) {

			foreach($shows as $show) {

				$showdata = gigpress_prepare($show, 'admin');

				?>
				<tr class="<?php echo 'gigpress-' . $showdata['status']; ?>">
					<th scope="row" class="check-column"><input type="checkbox" name="show_id[]" value="<?php echo $show->show_id; ?>" /></th>
					<td><span class="gigpress-date"><?php echo $showdata['date']; if($showdata['end_date']) { echo(' - ') . $showdata['end_date']; } ?></span>
					</td>
					<td><?php echo $showdata['artist']; ?></td>
					<td><?php echo $showdata['city']; ?></td>
					<td><?php echo $showdata['venue']; if($showdata['address']) echo(' (' . $showdata['address'] . ')'); ?></td>
					<td><?php echo $showdata['country']; ?></td>
					<td><?php echo $showdata['tour']; ?></td>
					<td class="gp-centre">
						<a href="<?php bloginfo('wpurl') ?>/wp-admin/admin.php?page=gigpress/gigpress.php&amp;gpaction=edit&amp;show_id=<?php echo $show->show_id; ?>" class="edit" title="<?php _e("Edit", "gigpress"); ?>"><?php _e("Edit", "gigpress"); ?></a>&nbsp;|&nbsp;<a href="<?php bloginfo('wpurl') ?>/wp-admin/admin.php?page=gigpress/gigpress.php&amp;gpaction=copy&amp;show_id=<?php echo $show->show_id; ?>" class="edit" title="<?php _e("Copy", "gigpress"); ?>"><?php _e("Copy", "gigpress"); ?></a>
					</td>
				</tr>
				<tr class="<?php echo 'alternate' . ' gigpress-' . $showdata['status']; ?>">
					<td colspan="8"><small>
					<?php
						if($showdata['time']) echo $showdata['time'] . '. ';
						if($showdata['price']) echo __("Price", "gigpress") . ': ' . $showdata['price'] . '. ';
						if($showdata['admittance']) echo $showdata['admittance'] . '. ';
						if($showdata['ticket_link']) echo $showdata['ticket_link'] . '. ';
						if($showdata['ticket_phone']) echo __('Box office', "gigpress") . ': ' . $showdata['ticket_phone'] . '. ';
						echo $showdata['notes'] . ' ';
						echo $showdata['related_edit'];
					?>
					</small></td>
				</tr>
			<?php } // end foreach
		} else { // No results from the query
		?>
			<tr><td colspan="8"><?php _e("Sorry, no shows to display based on your criteria.", "gigpress"); ?></td></tr>
		<?php } ?>
			</tbody>
		</table>
		<div class="tablenav">
			<div class="alignleft">
				<input type="submit" value="<?php _e('Trash selected shows', 'gigpress'); ?>" class="button-secondary" /> &nbsp;
				<?php
				if($tour_count = $wpdb->get_var("SELECT count(*) FROM ". GIGPRESS_TOURS ." WHERE tour_status = 'deleted'")) {
					$tours = $tour_count;
				} else {
					$tours = 0;
				}

				if($show_count = $wpdb->get_var("SELECT count(*) FROM ". GIGPRESS_SHOWS ." WHERE show_status = 'deleted'")) {
					$shows = $show_count;
				} else {
					$shows = 0;
				}
				if($tour_count || $show_count) {
					echo('<small>'. __("You have", "gigpress"). ' <strong>'. $shows .' '. __("shows", "gigpress"). '</strong> '. __("and", "gigpress"). ' <strong>'. $tours .' '. __("tours", "gigpress") .'</strong> '. __("in your trash", "gigpress").'.');
					if($shows != 0 || $tours != 0) {
						echo(' <a href="'. wp_nonce_url(get_bloginfo('wpurl').'/wp-admin/admin.php?page=gigpress-shows&amp;gpaction=trash' . $url_args, 'gigpress-action') .'">'. __("Take out the trash now", "gigpress") .'</a>.');
					}
					echo('</small>');
				}
				?>
				</div>

			<?php if($pagination) echo $pagination['output']; ?>

		</div>
		</form>
	</div>
<?php }

function gigpress_my_shows ($filter = null, $content = null) {

	global $wpdb, $gpo;
	$further_where = $limit = '';

	extract(shortcode_atts(array(
			'tour' => FALSE,
			'artist' => FALSE,
			'venue' => FALSE,
			'limit' => FALSE,
			'scope' => 'upcoming',
			'sort' => FALSE,
			'group_artists' => 'yes',
			'artist_order' => 'custom',
			'show_menu' => FALSE,
			'show_menu_count' => FALSE,
			'menu_sort' => FALSE,
			'menu_title' => FALSE,
			'year' => FALSE,
			'month' => FALSE
		), $filter)
	);

	$total_artists = $wpdb->get_var("SELECT count(*) from " . GIGPRESS_ARTISTS);

	// Date conditionals and sorting based on scope
	switch($scope) {
		case 'upcoming':
			$date_condition = "show_expire >= '" . GIGPRESS_NOW . "'";
			if(empty($sort)) $sort = 'asc';
			break;
		case 'past':
			$date_condition = "show_expire < '" . GIGPRESS_NOW . "'";
			if(empty($sort)) $sort = 'desc';
			break;
		case 'today':
			$date_condition = "show_expire >= '".GIGPRESS_NOW."' AND show_date <= '".GIGPRESS_NOW."'";
			if(empty($sort)) $sort = 'asc';
			break;
		case 'all':
			$date_condition = "show_expire != ''";
			if(empty($sort)) $sort = 'desc';
			break;
	}

	// Artist, tour and venue filtering
	if($artist) $further_where .= ' AND show_artist_id = ' . $wpdb->prepare('%d', $artist);
	if($tour) $further_where .= ' AND show_tour_id = ' . $wpdb->prepare('%d', $tour);
	if($venue) $further_where .= ' AND show_venue_id = ' . $wpdb->prepare('%d', $venue);

	// Date filtering

	// Query vars take precedence over function vars
	if(isset($_REQUEST['gpy'])) {
		$year = $_REQUEST['gpy'];

		if(isset($_REQUEST['gpm'])) {
			$month = $_REQUEST['gpm'];
		} else {
			unset($month);
		}
		$no_limit = TRUE;
	}


	// Validate year and date parameters
	if($year || $month) {

		if($year) {
			if(is_numeric($year) && strlen($year) == 4) {
				$year = round($year);
			} else {
				$year = date('Y', current_time('timestamp'));
			}
		} else {
			// We've only specified a month, so we'll assume the year is current
			$year = date('Y', current_time('timestamp'));
		}

		if($month) {
			if($month == 'current') {
				$month = date('m', current_time('timestamp'));
			} elseif(round($month) == 0) {
				// Probably using a month name
				$month = date('m', strtotime($month));
			} elseif(round($month) < 10) {
				// Make sure the month is padded through 09
				$month = str_pad($month, 2, 0, STR_PAD_LEFT);
			} elseif(round($month) < 13) {
				// Between 10 and 12 we're OK
				$month = $month;
			} else {
				// Bogus month value (not a string and > 12)
				// Sorry, bailing out. Your "month" will be ignored. Dink.
				$month = FALSE;
			}
			$start_month = $end_month = $month;
		}

		if(!$month) {
			$start_month = '01';
			$end_month = '12';
		}

		$start = $year.'-'.$start_month.'-01';
		$end = $year.'-'.$end_month.'-31';
		$further_where .= ' AND show_date BETWEEN '.$wpdb->prepare('%s', $start).' AND '.$wpdb->prepare('%s', $end);
	}


	$limit = ($limit && !$no_limit) ? ' LIMIT ' . $wpdb->prepare('%d', $limit) : '';
	$artist_order = ($artist_order == 'custom') ?  "artist_order ASC," : '';

	// With the new 'all' scope, we should probably have a third message option, but I'm too lazy
	// Really, there should just be one generic 'no shows' message. Oh well.
	$no_results_message = ($scope == 'upcoming') ? wptexturize($gpo['noupcoming']) : wptexturize($gpo['nopast']);

	ob_start();

	// Are we showing our menu?
	if($show_menu) {
		$menu_options = array();
		$menu_options['scope'] = $scope;
		$menu_options['type'] = $show_menu;
		if($menu_title) $menu_options['title'] = $menu_title;
		if($show_menu_count) $menu_options['show_count'] = $show_menu_count;
		if($menu_sort) $menu_options['sort'] = $menu_sort;
		if($artist) $menu_options['artist'] = $artist;
		if($tour) $menu_options['tour'] = $tour;
		if($venue) $menu_options['venue'] = $venue;

		include gigpress_template('before-menu');
		echo gigpress_menu($menu_options);
		include gigpress_template('after-menu');
	}

	// If we're grouping by artist, we'll unfortunately have to first get all artists
	// Then  make a query for each one. Looking for a better way to do this.

	if($group_artists == 'yes' && !$artist && $total_artists > 1) {

		$artists = $wpdb->get_results("SELECT * FROM " . GIGPRESS_ARTISTS . " ORDER BY " . $artist_order . "artist_name ASC");

		foreach($artists as $artist_group) {
			$shows = $wpdb->get_results("SELECT * FROM " . GIGPRESS_ARTISTS . " AS a, " . GIGPRESS_VENUES . " as v, " . GIGPRESS_SHOWS ." AS s LEFT JOIN  " . GIGPRESS_TOURS . " AS t ON s.show_tour_id = t.tour_id WHERE " . $date_condition . " AND show_status != 'deleted' AND s.show_artist_id = " . $artist_group->artist_id . " AND s.show_artist_id = a.artist_id AND s.show_venue_id = v.venue_id " . $further_where . " ORDER BY s.show_date " . $sort . ",s.show_expire " . $sort . ",s.show_time ". $sort . $limit);

			if($shows) {
				// For each artist group

				$some_results = TRUE;
				$current_tour = '';
				$i = 0;
				$showdata = array(
					'artist' => wptexturize($artist_group->artist_name),
					'artist_id' => $artist_group->artist_id
				);

				include gigpress_template('shows-artist-heading');
				include gigpress_template('shows-list-start');

				foreach($shows as $show) {

					// For each individual show

					$showdata = gigpress_prepare($show, 'public');

					if($showdata['tour'] && $showdata['tour'] != $current_tour && !$tour) {
						$current_tour = $showdata['tour'];
						include gigpress_template('shows-tour-heading');
					}

					$class = $showdata['status'];
					++ $i; $class .= ($i % 2) ? '' : ' gigpress-alt';
					if(!$showdata['tour'] && $current_tour) {
						$current_tour = '';
						$class .= ' divider';
					}
					$class .= ($showdata['tour'] && !$tour) ? ' gigpress-tour' : '';

					include gigpress_template('shows-list');

				}

				include gigpress_template('shows-list-end');
			}
		}

		if($some_results) {
		// After all artist groups
			include gigpress_template('shows-list-footer');
		} else {
			// No shows from any artist
			include gigpress_template('shows-list-empty');
		}

	} else {

		// Not grouping by artists

		$shows = $wpdb->get_results("
			SELECT * FROM " . GIGPRESS_ARTISTS . " AS a, " . GIGPRESS_VENUES . " as v, " . GIGPRESS_SHOWS ." AS s LEFT JOIN  " . GIGPRESS_TOURS . " AS t ON s.show_tour_id = t.tour_id WHERE " . $date_condition . " AND show_status != 'deleted' AND s.show_artist_id = a.artist_id AND s.show_venue_id = v.venue_id " . $further_where . " ORDER BY s.show_date " . $sort . ",s.show_expire " . $sort . ",s.show_time " . $sort . $limit);

		if($shows) {

			$current_tour = '';
			$i = 0;

			include gigpress_template('shows-list-start');

			foreach($shows as $show) {

				// For each individual show
				$showdata = gigpress_prepare($show, 'public');

				if($showdata['tour'] && $showdata['tour'] != $current_tour && !$tour) {
					$current_tour = $showdata['tour'];
					include gigpress_template('shows-tour-heading');
				}

				$class = $showdata['status'];
				++ $i; $class .= ($i % 2) ? '' : ' gigpress-alt';
				if(!$showdata['tour'] && $current_tour) {
					$current_tour = '';
					$class .= ' divider';
				}
				$class .= ($showdata['tour'] && !$tour) ? ' gigpress-tour' : '';

				include gigpress_template('shows-list');
			}

			include gigpress_template('shows-list-end');
			include gigpress_template('shows-list-footer');

		} else {
			// No shows to display
			include gigpress_template('shows-list-empty');
		}

	}

	echo('<!-- Generated by GigPress ' . GIGPRESS_VERSION . ' -->
	');
	return ob_get_clean();
}

function gigpress_my_menu($options = null) {

	global $wpdb, $wp_locale, $gpo;

	extract(shortcode_atts(array(
		'type' => 'monthly',
		'base' => get_permalink(),
		'scope' => 'upcoming',
		'title' => FALSE,
		'id' => 'gigpress_menu',
		'show_count' => FALSE,
		'artist' => FALSE,
		'tour' => FALSE,
		'venue' => FALSE,
		'sort' => 'desc'
	), $options));

	$base .= (strpos($base, '?') === FALSE) ? '?' : '&amp;';

	// Date conditionals based on scope
	switch($scope) {
		case 'upcoming':
			$date_condition = ">= '" . GIGPRESS_NOW . "'";
			break;
		case 'past':
			$date_condition = "< '" . GIGPRESS_NOW . "'";
			break;
		case 'all':
			$date_condition = "!= ''";
	}

	$further_where = '';

	// Artist, tour and venue filtering
	if($artist) $further_where .= ' AND show_artist_id = ' . $wpdb->prepare('%d', $artist);
	if($tour) $further_where .= ' AND show_tour_id = ' . $wpdb->prepare('%d', $tour);
	if($venue) $further_where .= ' AND show_venue_id = ' . $wpdb->prepare('%d', $venue);

	// Variable operajigamarations based on monthly vs. yearly
	switch($type) {
		case 'monthly':
			$sql_select_extra = 'MONTH(show_date) AS month, ';
			$sql_group_extra = ', MONTH(show_date)';
			$title = ($title) ? wptexturize(strip_tags($title)) : __('Select Month');
			$current = (isset($_REQUEST['gpy']) && isset($_REQUEST['gpm'])) ? $_REQUEST['gpy'].$_REQUEST['gpm'] : '';
			break;
		case 'yearly':
			$sql_select_extra = $sql_group_extra = '';
			$title = ($title) ? wptexturize(strip_tags($title)) : __('Select Year');
			$current = (isset($_REQUEST['gpy'])) ? $_REQUEST['gpy'] : '';
	}

	// Build query
	$dates = $wpdb->get_results("
		SELECT YEAR(show_date) AS year, " . $sql_select_extra . " count(show_id) as shows
		FROM ".GIGPRESS_SHOWS."
		WHERE show_status != 'deleted'
		AND show_date " . $date_condition . $further_where . "
		GROUP BY YEAR(show_date)" . $sql_group_extra . "
		ORDER BY show_date " . $sort);

	ob_start();

	if($dates) : ?>

			<select name="gigpress_menu" class="gigpress_menu shit_menu" id="<?php echo $id; ?>">
				<option value="<?php echo $base; ?>"><?php echo $title; ?></option>
			<?php foreach($dates as $date) : ?>
				<?php $this_date = ($type == 'monthly') ? $date->year.$date->month : $date->year; ?>
				<option value="<?php echo $base.'gpy='.$date->year; if($type == 'monthly') echo '&amp;gpm='.$date->month; ?>"<?php if($this_date == $current) : ?> selected="selected"<?php endif; ?>>
					<?php if($type == 'monthly') echo $wp_locale->get_month($date->month).' '; echo $date->year; ?>
					<?php if($show_count && $show_count == 'yes') : ?>(<?php echo $date->shows; ?>)<?php endif; ?>
				</option>
			<?php endforeach; ?>
			</select>

	<?php endif;

	return ob_get_clean();
}