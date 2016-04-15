<?php echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<header role="banner" class="navbar">
    <nav role="navigation" class="navbar-inner">
        <div id="topo">
          <div class="logo-wr">
            <a class="logo-img" href="<?php echo $CFG->wwwroot;?>" alt="Logo cursos abertos">
              <img src="<?php echo $OUTPUT->pix_url('logo_negativa', 'theme');?>" alt="Logo" width="84" />
            </a>
          </div>

          <div id="courseheader">
            <h1><?php echo $OUTPUT->page_title(); ?></h1>
          </div>

          <?php echo $OUTPUT->user_menu(); ?>

        </div>

        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#tabs-menu">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>

    </nav>

</header>
