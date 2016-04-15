<?php require_once('includes/header.php'); ?>

<div id="course-page" class="container-fluid">

  <div id="page-content" class="row-fluid">
      <div id="region-main-box" class="col-md-9">
          <section id="region-main">
              <?php
              echo $OUTPUT->course_content_header();
              echo $OUTPUT->main_content();
              echo $OUTPUT->course_content_footer();
              ?>
          </section>
      </div>
      <?php echo $OUTPUT->blocks('side-post', 'col-md-3 blocks'); ?>
  </div>

</div>

<?php require_once('includes/footer.php'); ?>

</body>
</html>
