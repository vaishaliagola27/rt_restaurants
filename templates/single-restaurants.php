<?php

get_header();
?>

<div id="primary" class="content-area">
    <div id="content" class="site-content" role="main">
        <?php
        // Start the Loop.
        while (have_posts()) : the_post();
        
            //load content of restaurant
            global $post;
            get_template_part('templates/content', $post->post_type);

            // Previous/next post navigation.
            the_post_navigation();

            // If comments are open or we have at least one comment, load up the comment template.
            if (comments_open() || get_comments_number()) {
                comments_template();
            }
        endwhile;
        ?>
    </div><!-- #content -->
</div><!-- #primary -->

<?php
get_sidebar('content');
get_sidebar();
get_footer();
