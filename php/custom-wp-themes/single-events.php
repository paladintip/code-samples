<?php

get_header('events');
$thisPostID = $post->ID;
// get fields
$fields = get_fields();
$img = get_the_post_thumbnail_url();

//var_dump($fields); exit;

$eventLocation = $fields['location'];
$eventTimestamp = $fields['date_time'];
$eventDateObject = DateTime::createFromFormat('U', $eventTimestamp);
$eventDateObject->setTimezone(new DateTimeZone('America/Los_Angeles'));
$currentDate = new DateTime();

$categories = wp_get_object_terms( $thisPostID,  'event_categories' );
$formatedCategories = '';
$categoryName = '';
foreach( $categories as $category ) {
    $formatedCategories .= " " . $category->slug;
    $categoryName = $category->name;
}

$regions = wp_get_object_terms( $thisPostID,  'event_region' );
$formatedRegions = '';
$regionName = '';
foreach( $regions as $region ) {
    $formatedRegions .= " " . $region->slug;
    $regionName = $region->name;
}


$pastEvent = true;
if($eventTimestamp < $currentDate->getTimestamp())
{
    $pastEvent = false;
}

?>

<div class="event-header-wrapper">
    <div class="event-header">

        <div class="event-title">
            <span class="category-type"><?php echo $categoryName. " | " . $fields['event_type']; ?></span>
            <h1><?php the_title(); ?></h1>
            <p class="event-subtitle"><?php echo $fields['subtitle']; ?></p>
        </div>
        <div class="event-info">
            <spsn class="date-time-info">
                <i class="far fa-calender"></i><b><?php echo $eventDateObject->format('l, F t, Y') ?>  </b> <?php echo $eventDateObject->format('g:ia T')?>
            </spsn>
            <span class="location-info">
                <i class="far fa-location"></i><span><?php echo $fields['location']; ?></span>
            </span>
        </div>
    </div>
    <div class = "register-wrapper">
     <?php
    if($pastEvent === true)
    { ?>
        <div>
    <a class="register-link" href="">Register Now</a>
            </div>
      <?php
    }
    else
    {
        ?>
            <p class="past-event-text">This event has passed. Watch the recording below.</p>
        <?php
        }
    ?>

    </div>
</div>
<div class="event-content-wrapper">
    <div class="event-content">
        <div class="main-column">
            <?php if(!empty($fields['video_link']))
            {
            ?>
            <div class="iframe-wrapper">
                <iframe src="<?php echo $fields['video_link']; ?>" width="825" height="465" frameborder="0"   allow="autoplay; fullscreen" allowfullscreen></iframe>
            </div>
            <?php
            }
            ?>


            <div class="main-text">
               <?php the_content(); ?>
            </div>
        </div>
        <div class="event-sidebar">


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
                    if($postID != $thisPostID)
                    {


                    $imageLink = wp_get_attachment_image_url(get_field('portrait', $postID), 'medium' );

                    $eventTitle = get_the_title($postID);
                    $eventLocation = get_field('location',  $postID);
                    $eventTimestamp = get_field('date_time', $postID);
                    $eventDateObject = DateTime::createFromFormat('U', $eventTimestamp);
                    $eventDateObject->setTimezone(new DateTimeZone('America/Los_Angeles'));
                    $currentDate = new DateTime();

                    $categories = wp_get_object_terms( $post->ID,  'event_categories' );
                    $formatedCategories = '';
                    foreach( $categories as $category ) {
                        $formatedCategories .= " " . $category->slug;
                    }

                    $regions = wp_get_object_terms( $post->ID,  'event_region' );
                    $formatedRegions = '';
                    $regionName = '';
                    foreach( $regions as $region ) {
                        $formatedRegions .= " " . $region->slug;
                        $regionName = $region->name;
                    }

                    if($eventTimestamp > $currentDate->getTimestamp())
                    {
                        $upcommingEvents .=
                            "<div class='event'  class='event".$formatedRegions."". $formatedCategories ."'>
                        
                        <a href='". get_the_permalink()."'' class='event-title'>". $eventTitle."</a>   
                        <p class='event-date'> ".$eventDateObject->format('F t, Y')."</p>
                       
                    </div>";
                    }
                    else
                    {
                        $pastEvents .=
                            "<div class='event' href='". get_the_permalink()."' class='event".$formatedRegions."". $formatedCategories ."'>
                        <h4 class='event-title'>". $eventTitle."</h4>   
                        <p class='event-date'> ".$eventDateObject->format('F t, Y')." . ".$regionName."</p>
                    </div>";
                    }
                    }
                }
                /* Restore original Post Data */
                wp_reset_postdata();
            } else {
                // no posts found
            }?>

    <?php
    if(!empty($upcommingEvents))
    { ?>
        <div class="upcoming-events">
            <h3>Upcoming Events</h3>
            <div class="events-blocks">
                <?php echo $upcommingEvents; ?>
            </div>
        </div>
    <?php
    }
    ?>
<!--    --><?php
//    if(!empty($pastEvents))
//    { ?>
<!--        <div class="past-events">-->
<!--            <h3>Past Events & Videos</h3>-->
<!--            <div class="events-blocks">-->
<!--                --><?php //echo $pastEvents; ?>
<!--            </div>-->
<!--        </div>-->
<!--        --><?php
//    }
//    ?>


        </div>
    </div>
</div>

<script>


</script>

<?php get_footer(); ?>
