<?php

$knownregionpre = $PAGE->blocks->is_known_region('side-pre');
$knownregionpost = $PAGE->blocks->is_known_region('side-post');

$PAGE->set_popup_notification_allowed(false);
echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <?php echo $OUTPUT->standard_head_html(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimal-ui">
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<nav role="navigation" class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header logo col-md-2 col-sm-2 col-lg-2">
        <a href="<?php echo $CFG->wwwroot;?>">
          <img src="<?php echo $CFG->wwwroot ?>/theme/awesome/pix/logo_negativa.png" alt="Awesome">
        </a>
      </div>
      <div class="navbar-header col-md-10 col-sm-10 col-lg-10">
        <?php echo $OUTPUT->page_heading(); ?>

        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#tabs-menu">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
      </div>

      <div id="moodle-navbar" class="navbar-collapse collapse navbar-right">
          <?php echo $OUTPUT->user_menu(); ?>
          <?php echo $OUTPUT->custom_menu(); ?>
          <ul class="nav pull-right">
              <li><?php echo $OUTPUT->page_heading_menu(); ?></li>
          </ul>
      </div>
    </div>
</nav>
<div id="course-page" class="container-fluid">
    <div id="page-content" class="row">
        <div id="region-main" class="col-sm-9 col-lg-9">
            <?php
            echo $OUTPUT->course_content_header();

            echo $OUTPUT->main_content();
            echo $OUTPUT->course_content_footer();
            ?>
        </div>

        <div id="region-blocks" class="col-sm-3 col-lg-3">

          <?php
            if ($knownregionpre) {
                echo $OUTPUT->blocks('side-pre');
            }

            if ($knownregionpost) {
                echo $OUTPUT->blocks('side-post');
            }
          ?>

        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
