<?php
$use_default_page_header = false;
get_header('events');
date_default_timezone_set('America/Los_Angeles');

?>

<section class="events-title">
    <h1 style="text-align: center;"><?php the_title(); ?></h1>
    <p style="text-align: center;"><?php strip_tags(the_content()); ?></p>
</section>

<section class="events-filters">
    <div class="filters-wrapper">
    <span>Filters</span>
    <div class="area-of-law-wrapper">
        <label for ='area-of-law'>Area of Law</label>
        <select name="area-of-law" class="area-of-law">
            <option value="all">All</option>
            <?php
            $categories = get_categories( array(
                'taxonomy' => 'event_categories',
                'orderby' => 'name',
                'order'   => 'ASC'
            ) );
            foreach( $categories as $category ) {
                ?>
                <option value="<?php echo $category->slug; ?>"><?php echo $category->name; ?></option>
            <?php
            }

            ?>
        </select>
    </div>
    <div class="region-wrapper">
        <label for ='region'>Region</label>
        <select name="region" class="region">
            <option value="all">All</option>
            <?php
            $categories = get_categories( array(
                'taxonomy' => 'event_region',
                'orderby' => 'name',
                'order'   => 'ASC'
            ) );
            foreach( $categories as $category ) {
                ?>
                <option value="<?php echo $category->slug; ?>"><?php echo $category->name; ?></option>
                <?php
            }

            ?>
        </select>
    </div>
    <button class="filter-search">Search</button>
    </div>
</section>

<?php
$upcommingEvents = '';
$pastEvents = '';

$args = array(
    'post_status' => 'publish',
    'post_type' => 'events',
    'meta_key' => 'date_time',
    'orderby' => 'meta_value',
    'order' => 'DESC',
    'posts_per_page' => -1
);
// The Query
$the_query = new WP_Query( $args );

// The Loop
if ( $the_query->have_posts() ) {

    while ( $the_query->have_posts() ) {
        $the_query->the_post();
        $postID = get_the_ID();
        $imageLink = wp_get_attachment_image_url(get_field('portrait', $postID), 'medium' );

        $eventTitle = get_the_title($postID);
        $eventLocation = get_field('location',  $postID);
        $eventTimestamp = get_field('date_time', $postID);
        $eventDateObject = DateTime::createFromFormat('U', $eventTimestamp);
        $eventDateObject->setTimezone(new DateTimeZone('America/Los_Angeles'));
        $currentDate = new DateTime();

        $categories = wp_get_object_terms( $postID,  'event_categories' );
        $formatedCategories = '';
        foreach( $categories as $category ) {
            $formatedCategories .= " " . $category->slug;
        }



        $regions = wp_get_object_terms( $postID,  'event_region' );
        $formatedRegions = '';
        $regionName = '';
        foreach( $regions as $region ) {
            $formatedRegions .= " " . $region->slug;
            $regionName = $region->name;

        }

        if($eventTimestamp > $currentDate->getTimestamp())
        {

            $upcommingEvents .=
                "<div href='". get_the_permalink()."' class='event".$formatedRegions." ". $formatedCategories ."'>
                    <p class='event-date'> <b>".$eventDateObject->format('l, F t')."</b> ".$eventDateObject->format('g:ia T')."</p>
                    <h4 class='event-title'>". $eventTitle."</h4>   
                    <p class='event-location'>".$regionName."</p>
                </div>";
        }
        else
        {
            $pastEvents .=
                "<div href='". get_the_permalink()."' class='event".$formatedRegions."". $formatedCategories ."'>
                    <h4 class='event-title'>". $eventTitle."</h4>   
                    <p class='event-date'> ".$eventDateObject->format('F t, Y')."<b> . </b>".$regionName."</p>
                </div>";
        }
    }
    /* Restore original Post Data */
    wp_reset_postdata();
} else {
    // no posts found
}?>

<section id="content" class="events-content" role="main">
    <?php
    if(!empty($upcommingEvents))
    {


        ?>
        <div class="upcoming-events">
            <h3>Upcoming Events</h3>
            <div class="events-blocks">
                <?php echo $upcommingEvents; ?>
            </div>
        </div>
        <?php
    }
    ?>
    <?php
    if(!empty($pastEvents))
    { ?>
        <div class="past-events">
            <h3>Past Events & Videos</h3>
            <div class="events-blocks">
                <?php echo $pastEvents; ?>
            </div>
        </div>
        <?php
    }
    ?>

</section><!-- #content -->
<script>
    jQuery(function ($) {
        let $regionDropdown = $('select.region');
        let $areaOfLawDropdown = $("select.area-of-law");
        let $searchBtn = $("button.filter-search");
        $searchBtn.click(filterEvents);
        $('.event').click(function(){window.open($(this).attr('href'));});
        function filterEvents()
        {
            $('.event').show();

            if($regionDropdown.val() != 'all')
            {
                $('.event').each(function() {
                    if(!$(this).hasClass($regionDropdown.val()))
                        $(this).hide();

                });
            }

            if($areaOfLawDropdown.val() != 'all')
            {
               $('.event').each(function() {
                    if(!$(this).hasClass($areaOfLawDropdown.val()))
                        $(this).hide();

                });
            }

        }


    });

</script>
<?php get_footer(); ?>
