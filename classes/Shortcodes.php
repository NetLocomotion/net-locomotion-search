<?php

if (!class_exists('Net_Locomotion_Search_Shortcodes')) {
  class Net_Locomotion_Search_Shortcodes {
    public function __construct() {
      add_shortcode('NLS_Search_Form', [ $this, 'searchForm' ]);
      add_shortcode('NLS_Results_Page', [ $this, 'resultsPage' ]);
    }

    /**
     * Show the search form
     */
    public function searchForm() {
      ?>
      <form method="get" action="/search/">
        <input type="text" name="q" placeholder="Search" class="nl-search-field" /><button type="submit" class="nl-search-button" aria-label="Search"><i class="fa fa-search"> </i></button>
      </form>
      <style type="text/css">
      .nl-search-button {
      	background-color: #81153F;
      	color: #fff;
      	border: none;
        border-radius: 0;
        height: 38px;
        padding: 0 10px;
      }
      .nl-search-field {
        padding: 0 6px;
        border-radius: 0;
        border: 1px solid #81153F;
        height: 38px;
      }
      </style>
      <?php
    }

    /**
     * Show the search results
     */
    public function resultsPage() {
      // Set pagination amount
      $limit = 10;
      // Get the query
      $q = isset($_REQUEST['q']) ? $_REQUEST['q'] : '';
      $q = sanitize_text_field($q);

      // Get pagination
      $p = isset($_REQUEST['pp']) ? $_REQUEST['pp'] : 1;
      $p = sanitize_text_field($p);
      $p = $p - 1;

      // Don't show any results if the query's empty
      if (empty($q)) {
        return;
      }

      // Do the DB query
      global $wpdb;
      $results = $wpdb->get_results(
        'SELECT ID, post_title, post_excerpt, post_date, post_content, post_type
         FROM ' . $wpdb->posts . '
         WHERE (post_title LIKE "%' . $q . '%"
         OR post_content LIKE "%' . $q . '%")
         AND post_status = "publish"
         AND post_type IN ("post", "page")
         LIMIT ' . ($p * $limit) . ', ' . $limit
      );
      $total = $wpdb->get_results(
        'SELECT COUNT(1) as total
         FROM ' . $wpdb->posts . '
         WHERE (post_title LIKE "%' . $q . '%"
         OR post_content LIKE "%' . $q . '%")
         AND post_status = "publish"
         AND post_type = "post"'
      );
      $total = $total[0]->total;

      // Go through the results
      foreach ($results as $result) {
        $content = strip_shortcodes($result->post_content);
        $content = strip_tags($content);
        ?>
        <div class="nl-search-result-wrapper">
          <p class="nl-search-result-title">
            <a href="<?php echo get_permalink($result->ID); ?>"><h3><?php echo $result->post_title; ?></h3></a>
          </p>
          <p class="nl-search-date">
            <?php echo date('D jS F Y', strtotime($result->post_date)); ?>
          </p>
          <p class="nl-search-result-excerpt">
            <?php echo !empty($result->post_excerpt) ?
              strip_shortcodes($result->post_excerpt) :
              substr($content, 0, 150) . (strlen($content) > 150 ? '...' : ''); ?>
            <?php //var_dump($result->post_content); ?>
          </p>
        </div>
        <?php
      }

      ?>
      <div class="nl-pager-wrapper">
      <?php
      // Left arrow
      if ($p > 0) {
        echo '<a href="?q=' . $q . '&pp=' . ($p) . '">&#10096;</a>';
      }

      // Pagination
      for ($i = 1; $i < ceil($total / $limit) + 1; $i++) {
        echo ($p + 1 == $i ? '<b>' : '');
        echo '<a href="?q=' . $q . '&pp=' . $i . '">' . $i . '</a> ';
        echo ($p + 1 == $i ? '</b>' : '');
      }

      // Right arrow
      if ($total > $limit && ($p + 1) != ceil($total / $limit)) {
        echo '<a href="?q=' . $q . '&pp=' . ($p + 2) . '">&#10097;</a>';
      }
      ?>
      </div>

      <style type="text/css">
      .nl-search-date {
      	font-size: 0.75em;
      	padding-bottom: 0;
      	margin-bottom: .35em;
      	color: #555;
      	font-style: italic;
      }
      .nl-search-result-excerpt {
      	margin-bottom: 3em;
      }
      .nl-pager-wrapper {
      	text-align: center;
      }
      .nl-pager-wrapper a {
      	margin: 0 .5em;
      }
      </style>
      <?php
    }
  }
}
