<?php
//Template Name: Attorney Page
get_header('att');

// get fields
$fields = get_fields();
$img = get_the_post_thumbnail_url();

//var_dump($fields); exit;
?>

<div class="header-wrapper">
    <div class="att-header">
        <div class="single-image">

            <img src="<?php echo $img; ?>" />
        </div>
        <div class="single-title">
            <h2><?php the_title(); ?></h2>
            <h3 class="att-title"><?php echo $fields['title']; ?></h3>
        </div>

    </div>
    <div class="contact-info-btns">
        <div class="contact-info-btn">
            <a href="mailto:<?php echo $fields['email']; ?>" >
                <i class="far fa-envelope"></i><span>Email</span>
            </a>
        </div>
        <div class="contact-info-btn">
            <a id='vcard-download' >
                <i class="far fa-address-card"></i><span>Download VCard</span>
            </a>
        </div>
        <div class="contact-info-btn">
            <a href="<?php echo $fields['printable_biography']; ?>"  download>
                <i class="fas fa-print"></i><span>Printable Biography</span>
            </a>
        </div>
        <div class="contact-info-btn">
            <a href="<?php echo $fields['linkedin']; ?>">
                <i class="Defaults-linkedin-square"></i><span>Connect on LinkedIn</span>
            </a>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="main-text">
       <?php the_content(); ?>
    </div>
    <div class="att-sidebar">


        <div class="sidebar-list">
            <ul>
                <li>
                    <div class ='list-item'>
                        <div class="aio-icon circle " style="color:#f7f7f7;background:#213c65;font-size:14px;display:inline-block;">
                            <i class="Defaults-phone"></i>
                        </div>
                        <span data-ultimate-target="#list-icon-wrap-1111 .uavc-list-desc"  class="uavc-list-desc ult-responsive" style="">
                            <span style="color: #213c65;"><?php echo $fields['phone']; ?></span>
                        </span>
                    </div>
                </li>
                <li>
                    <div class ='list-item'>
                        <div class="aio-icon circle " style="color:#f7f7f7;background:#213c65;font-size:14px;display:inline-block;">
                            <i class="Defaults-envelope"></i>
                        </div>
                        <span data-ultimate-target="#list-icon-wrap-1112 .uavc-list-desc" class="uavc-list-desc ult-responsive" style="">
                            <span class="phoneNumber" style="color: #213c65;"><?php echo $fields['email']; ?></span>
                        </span>
                    </div>
                </li>
                <li>
                    <div class ='list-item'>
                        <div class="aio-icon circle " style="color:#f7f7f7;background:#213c65;font-size:14px;display:inline-block;">
                            <i class="Defaults-map-marker"></i>
                        </div>
                        <span data-ultimate-target="#list-icon-wrap-1113 .uavc-list-desc"class="uavc-list-desc ult-responsive" style="">
                            <span style="color: #213c65;"><?php echo $fields['region']; ?></span>
                        </span>
                    </div>
                </li>
            </ul>
        </div>
        <div class="sidebar-text">
            <?php echo apply_filters('the_content', $fields['sidebar']); ?>
        </div>
    </div>
</div>

<script>
    let vCardContent = '';
    function toDataURL(src, callback, outputFormat) {//Encodes image into string
        var img = new Image();
        img.crossOrigin = 'Anonymous';
        img.onload = function() {
            var canvas = document.createElement('CANVAS');
            var ctx = canvas.getContext('2d');
            var dataURL;
            canvas.height = this.naturalHeight;
            canvas.width = this.naturalWidth;
            ctx.drawImage(this, 0, 0);
            dataURL = canvas.toDataURL(outputFormat);
            callback(dataURL);
        };
        img.src = src;
        if (img.complete || img.complete === undefined) {
            img.src = "data:image/png;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
            img.src = src;
        }
    }

    toDataURL(
        '<?php echo get_the_post_thumbnail_url(null, 'medium'); ?>',//url
        function(dataUrl) {
            const encodedImage = dataUrl.replace(/\s+/g, '').replace('data:image/png;base64,', '');
            <?php

            $vCardAddressField = '';

            if(!empty($fields['office_location']))
            {
                $officeID = $fields['office_location']->ID;

                //ADR;WORK:;building name/suite;street;city;state;zip;country

                $buildingSuite = get_field('building_name_suite', $officeID);
                $street = get_field('street', $officeID);
                $city = get_field('city', $officeID);
                $state = get_field('state', $officeID);
                $zip = get_field('zip_code', $officeID);
                $country = get_field('country', $officeID);

                $vCardAddressField = "ADR;WORK:;$buildingSuite;$street;$city;$state;$zip;$country\\n".
                "LABEL;WORK;ENCODING=QUOTED-PRINTABLE:$street, $buildingSuite=0D=0A=\\n$city, $state $zip";

            }
            else if (empty($fields['office_location']))
            {
                $region = $fields['region'];
                $vCardAddressField = "ADR;WORK:;;;$region;;;;\\n".
                "LABEL;WORK;ENCODING=QUOTED-PRINTABLE:$region\\n";

            }
            ?>
vCardContent =
`BEGIN:VCARD
VERSION:2.1
N:<?php echo $fields['last_name']; ?>;<?php echo $fields['first_name']; ?>;<?php echo $fields['middle_name']; ?>;;
FN:<?php echo get_the_title(); ?>\nORG:Zuber Lawler LLC
TEL;TYPE=WORK,VOICE:<?php echo $fields['phone']; ?>\n<?php echo $vCardAddressField; ?>\nX-MS-OL-DEFAULT-POSTAL-ADDRESS:0
EMAIL;PREF;INTERNET:<?php echo $fields['email']; ?>\nPHOTO;ENCODING=b;TYPE=PNG:${encodedImage}

X-MS-OL-DESIGN;CHARSET=utf-8:<card xmlns="http://schemas.microsoft.com/office/outlook/12/electronicbusinesscards" ver="1.0" layout="left" bgcolor="ffffff"><img xmlns="" align="tleft" area="32" use="photo"/><fld xmlns="" prop="name" align="left" dir="ltr" style="b" color="000000" size="10"/><fld xmlns="" prop="org" align="left" dir="ltr" color="000000" size="8"/><fld xmlns="" prop="blank" size="8"/><fld xmlns="" prop="telwork" align="left" dir="ltr" color="d48d2a" size="8"><label align="right" color="626262">Work</label></fld><fld xmlns="" prop="email" align="left" dir="ltr" color="d48d2a" size="8"/><fld xmlns="" prop="blank" size="8"/><fld xmlns="" prop="addrwork" align="left" dir="ltr" color="000000" size="8"/><fld xmlns="" prop="blank" size="8"/><fld xmlns="" prop="blank" size="8"/><fld xmlns="" prop="blank" size="8"/><fld xmlns="" prop="blank" size="8"/><fld xmlns="" prop="blank" size="8"/><fld xmlns="" prop="blank" size="8"/><fld xmlns="" prop="blank" size="8"/><fld xmlns="" prop="blank" size="8"/><fld xmlns="" prop="blank" size="8"/></card>
REV:2021-03-11T07:23:52.447Z
END:VCARD`;

        }
    )
    const downloadBtn = document.querySelector("#vcard-download");


    function generateVcard(filename, text) {
        var element = document.createElement('a');
        element.setAttribute('href', 'data:text/vcard;charset=utf-8,' + encodeURIComponent(text));
        element.setAttribute('download', filename);

        element.style.display = 'none';
        document.body.appendChild(element);

        element.click();

        document.body.removeChild(element);
    }

    // Start file download.
    downloadBtn.addEventListener('click', function(event) {
        generateVcard("<?php echo sanitize_title( get_the_title()); ?>.vcf", vCardContent);
    });







</script>

<?php get_footer(); ?>
