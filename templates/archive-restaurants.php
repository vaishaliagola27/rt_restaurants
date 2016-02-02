<?php

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <?php if (have_posts()) : ?>

            <header class="entry-header">
                <?php
                the_archive_title('<h1 class="page-title">', '</h1>');
                the_archive_description('<div class="taxonomy-description">', '</div>');
                ?>
            </header><!-- .page-header -->

            <?php
            /* Start the Loop */
            while (have_posts()) : the_post();
                ?><div>
                <?php
                the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
                edit_post_link(
                        sprintf(
                                /* translators: %s: Name of current post */
                                esc_html__('Edit %s', '_s'), the_title('<span class="screen-reader-text">"', '"</span>', false)
                        ), '<span class="edit-link">', '</span>'
                );
                ?>
                </div>
                    <?php
                    global $post;
                    get_template_part('templates/content', $post->post_type);

                endwhile;

                the_posts_navigation();

            else :

                get_template_part('templates/content', 'none');

            endif;
            ?>

    </main><!-- #main -->
</div><!-- #primary -->

        <?php
        get_sidebar();
        get_footer();
        