<?php get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <section class="landing">
            <div class="grid">
            <div class="landing-text">

                <?php
                $args = array(
                    'post_type' => 'page',
                    'name' => 'home',
                    'post_status' => 'publish',
                    'posts_per_page' => 3
                );

                $loop = new WP_Query( $args );

                while ( $loop->have_posts() ) : $loop->the_post();
                    echo the_content();

                ?>
                <?php
                endwhile;

                wp_reset_postdata();
                ?>
            </div>
            <?php $bg_img_url = get_attachment_url_by_slug('eHorizon_Landing_BG'); ?>
            <img src="<?php echo $bg_img_url?>" class="main-bg">
            </div>
        </section>
        <section class="brand-posts">
            <ul id="lightSlider">

            <?php
            
            $args = array(
            'post_type' => 'sub_brand',
            'post_status' => 'publish',
            'posts_per_page' => 3
            );

            $loop = new WP_Query( $args );

            while ( $loop->have_posts() ) : $loop->the_post();

                $post = get_post();
                $post_id = $post->ID;
                ?>
                <li>
                    <div class="slide-content">
                    <img class="sub-brand-illustration" src="<?php echo get_the_post_thumbnail_url() ?>"/>
                        <div class="slide-text <?php echo  strtolower($post->post_title); ?>">
                            <h3 class="sub-brand-headline"> <img class='headset-icon-sm' src="<?php echo get_attachment_url_by_slug('icon_headset_' . strtolower($post->post_title)); ?>"><?php echo $post->post_title; ?></h3>
                            <p class="sub-brand-excerpt"> <?php echo the_excerpt(); ?> </p>
                            <span class="sub-brand-action">
                                <a  href="<?php echo get_permalink((get_page_by_path(strtolower($post->post_title))->ID)); ?>">Learn More</a>
                            </span>
                        </div>
                    </div>
                </li>
                <?php
            endwhile;

            wp_reset_postdata();
            ?>
            </ul>
<!--            <div class="slide-controls">-->
<!--                <button class="prev"><img src="--><?php //get_attachment_url_by_slug('left-arrow');?><!--//"></button>-->
<!--                <button class="next"><img src="--><?php //get_attachment_url_by_slug('left-right');?><!--//"></button>-->
<!--            </div>-->
            <script type="text/javascript">
                jQuery(document).ready(function(){
                    let slider = {};

                    let x = window.matchMedia("(max-width: 1200px)")

                    // window.addEventListener('resize', responsiveSlider);
                    // responsiveSlider();
                    function responsiveSlider() {
                        if (x.matches) { // If media query matches
                            console.log('Query matched');
                            console.log(slider);


                        } else {
                            console.log('attempted to destroy slider');
                            if (slider.lightSlider) {
                                slider.destroy();
                            }

                        }
                    }

                    //slider = jQuery("#lightSlider").lightSlider({
                    //    item: 1,
                    //    autoWidth: false,
                    //    slideMove: 1, // slidemove will be 1 if loop is true
                    //    slideMargin: 10,
                    //
                    //    addClass: '',
                    //    mode: "slide",
                    //    useCSS: false,
                    //    cssEasing: 'ease', //'cubic-bezier(0.25, 0, 0.25, 1)',//
                    //    easing: 'linear', //'for jquery animation',////
                    //
                    //    speed: 400, //ms'
                    //    auto: false,
                    //    loop: false,
                    //    slideEndAnimation: true,
                    //    pause: 2000,
                    //
                    //    keyPress: false,
                    //    controls: true,
                    //    prevHtml: '<img src="<?php //get_attachment_url_by_slug('left-arrow');?>//">',
                    //    nextHtml: '<img src="<?php //get_attachment_url_by_slug('left-right');?>//">',
                    //
                    //    rtl: false,
                    //    adaptiveHeight: false,
                    //
                    //    vertical: false,
                    //    verticalHeight: 500,
                    //    vThumbWidth: 100,
                    //
                    //    thumbItem: 10,
                    //    pager: false,
                    //    gallery: false,
                    //    galleryMargin: 5,
                    //    thumbMargin: 5,
                    //    currentPagerPosition: 'middle',
                    //
                    //    enableTouch: true,
                    //    enableDrag: true,
                    //    freeMove: true,
                    //    swipeThreshold: 40,
                    //
                    //    responsive: [],
                    //
                    //    onBeforeStart: function (el) {
                    //    },
                    //    onSliderLoad: function (el) {
                    //    },
                    //    onBeforeSlide: function (el) {
                    //    },
                    //    onAfterSlide: function (el) {
                    //    },
                    //    onBeforeNextSlide: function (el) {
                    //    },
                    //    onBeforePrevSlide: function (el) {
                    //    }
                    //});
                    // slider.goToSlide(3);
                    // slider.goToPrevSlide();
                    // slider.goToNextSlide();
                    // slider.getCurrentSlideCount();
                    // slider.refresh();
                    // slider.play();
                    // slider.pause();
                });
            </script>
        </section>
    </main><!-- .site-main -->
</div><!-- .content-area -->


<?php get_footer(); ?>
