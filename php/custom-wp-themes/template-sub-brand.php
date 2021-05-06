<?php
/*
 * Template Name: Sub Brand
 * description: Template for displaying sub-brand post content
 */


get_header();

    $args = array(
    'post_type' => 'sub_brand',
    'post_status' => 'publish',
    'posts_per_page' => 3
    );
?>

<?php
    $loop = new WP_Query( $args );

    while ( $loop->have_posts() ) : $loop->the_post();

        $post = get_post();


        $page = ltrim(strtolower(wp_title('', false, '')), ' ');

        if(strtolower($post->post_title) === $page) {
            $post_id = $post->ID;
            $headline = get_field( "headline", $post_id  );
            $content = $post->post_content;
            $bg = get_field( "background_image", $post_id  );
            $featureImages = array( get_field( "image_1", $post_id  )['url'], get_field( "image_2", $post_id  )['url'], get_field( "image_3", $post_id  )['url']);
            $featureContent = array( get_field( "feature_1", $post_id  ), get_field( "feature_2", $post_id  ), get_field( "feature_3", $post_id  ));
            $callToActionText = get_field( "call_to_action", $post_id  );
            ?>
            <div class="sub-brand-page" style="background-image: url('<?php echo $bg['url']; ?>'), url('<?php echo get_attachment_url_by_slug('white'); ?>')">
            <div class="head-content <?php echo  strtolower($post->post_title); ?>">
                <img class="sub-brand-illustration" src="<?php echo get_the_post_thumbnail_url() ?>"/>
                <div class="sub-brand-text <?php echo  strtolower($post->post_title); ?>">
                    <h2 class="sub-brand-headline"> <?php echo $headline ?></h2>
                    <p class="sub-brand-content"> <?php echo $content; ?> </p>
                    <div class="buttons">
                        <?php if($page == 'connect')
                        {?>
                        <span class="view-webinars">
                            <a  href="/index.php/events/">VIEW PAST EVENTS</a>
                        </span>
                        <?php } ?>
                        <span class="contact-us">
                            <a  href="<?php echo get_permalink((get_page_by_path(strtolower('contact'))->ID)); ?>">CONTACT US</a>
                        </span>




                    </div>
                </div>
            </div>
            <div class="features <?php echo  strtolower($post->post_title); ?>">
                <?php for ($x = 0 ;  $x < 3; $x++) {?>
                    <div class="feature">

                        <img src="<?php echo $featureImages[$x] ?>" class="feature-image">
                        <div class="feature-text  <?php echo  strtolower($post->post_title); ?>" >
                            <?php echo $featureContent[$x] ?>
                        </div>
                    </div>
                <?php  } ?>
	    </div>

        <div class="call-to-action">
            <h4> <?php echo $callToActionText ?></h4>
            <p></p>
            <span class="contact-us">
                <a  href="<?php echo get_permalink((get_page_by_path(strtolower('contact'))->ID)); ?>">CONTACT US</a>
            </span>

        </div>
            <?php

        }

    endwhile;

wp_reset_postdata();
?>
    </div>

<?php get_footer(); ?>
