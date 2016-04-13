<footer id="page-footer">
    <div id="course-footer"><?php echo $OUTPUT->course_footer(); ?></div>
    <p class="helplink"><?php echo $OUTPUT->page_doc_link(); ?></p>
    <?php
    echo $OUTPUT->login_info();
    echo $OUTPUT->home_link();
    echo $OUTPUT->standard_footer_html();
    ?>
</footer>
<div id="madewithlove">
  <p>made with <i class="glyphicon glyphicon-heart"></i> by <a href="http://conecti.me">conecti.me</a></p>
</div>

<?php echo $OUTPUT->standard_end_of_body_html() ?>
