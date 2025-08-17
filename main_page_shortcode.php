<?php

// create shortcode asarinos_main_page
function asarinos_main_page_shortcode($atts) {
    // get last added 3 propertys and all the meta
    $args = array(
        'post_type' => 'property',
        'posts_per_page' => 3,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    $query = new WP_Query($args);
    $propertys = $query->posts;

    //var_dump($propertys);

    ob_start();
    ?>
    <section class="elementor-section elementor-top-section elementor-element elementor-element-638511d elementor-element-638511dd  elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="638511d" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
<div class="elementor-container elementor-column-gap-default">
<a href="<?php echo get_permalink($propertys[0]->ID); ?>" class="front-photos">
<div class="elementor-column elementor-col-33 elementor-top-column elementor-element elementor-element-2b0ca37 animated fadeInUp" data-id="2b0ca37" data-element_type="column" data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:100}">
			<div class="elementor-widget-wrap elementor-element-populated">
						<section class="elementor-section elementor-inner-section elementor-element elementor-element-55be363 elementor-section-height-min-height elementor-section-boxed elementor-section-height-default animated fadeInUp" data-id="55be363" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:100}"
                        <?php
                        // set background image
                        if (has_post_thumbnail($propertys[0]->ID)) {
                            echo 'style="background-image: url(' . get_the_post_thumbnail_url($propertys[0]->ID) . ')"';
                        }

?>

                        >
							<div class="elementor-background-overlay"></div>
					
							<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-7590581 animated slideInUp" data-id="7590581" data-element_type="column" data-settings="{&quot;animation&quot;:&quot;slideInUp&quot;,&quot;animation_delay&quot;:200}">
			<div class="elementor-widget-wrap elementor-element-populated">
						<div class="elementor-element elementor-element-1db7ee7 elementor-widget elementor-widget-heading" data-id="1db7ee7" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h2 class="elementor-heading-title elementor-size-default"><?php
            // price and ' PLN'  and delete last 3 characters
            echo substr(get_post_meta($propertys[0]->ID, 'price', true), 0, -3) . 'zł';
            ?></h2>		</div>
				</div>
					</div>
		</div>
				<div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-afa0038 animated fadeIn" data-id="afa0038" data-element_type="column" data-settings="{&quot;animation&quot;:&quot;fadeIn&quot;,&quot;animation_delay&quot;:200}">
			<div class="elementor-widget-wrap elementor-element-populated">
						<div class="elementor-element elementor-element-b4e2900 elementor-widget__width-auto elementor-widget elementor-widget-heading" data-id="b4e2900" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h2 class="elementor-heading-title elementor-size-default"><?php
                $property_status = get_post_meta($propertys[0]->ID, 'transaction', true);
                switch ($property_status) {
                    case 131 :
                        echo 'Sprzedaż';
                        break;
                    case 132 :
                        echo 'Wynajem';
                        break;
                }
                ?></h2>		</div>
				</div>
					</div>
		</div>
					</div>
		</section>
				<section class="elementor-section elementor-inner-section elementor-element elementor-element-5910ea8 elementor-section-boxed elementor-section-height-default elementor-section-height-default animated fadeInDown" data-id="5910ea8" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;animation&quot;:&quot;fadeInDown&quot;,&quot;animation_delay&quot;:200}">
						<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-100 elementor-inner-column elementor-element elementor-element-9ec218e" data-id="9ec218e" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
						<div class="elementor-element elementor-element-1d1599b elementor-widget elementor-widget-heading" data-id="1d1599b" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<!--<h2 class="elementor-heading-title elementor-size-default">Apartament</h2>-->		</div>
				</div>
				<div class="elementor-element elementor-element-e2a580c elementor-widget elementor-widget-heading" data-id="e2a580c" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h2 class="elementor-heading-title elementor-size-default"><?php
                // show title
                echo $propertys[0]->post_title;
            ?></h2>		</div>
				</div>
				<div class="elementor-element elementor-element-747fbb1 elementor-widget elementor-widget-text-editor" data-id="747fbb1" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
							<p>
                                <?php
                                // show first 100 characters of content, delete all tags
                                echo substr(strip_tags($propertys[0]->post_content), 0, 100) . '...';
                                ?>
                            </p>						</div>
				</div>
				<div class="elementor-element elementor-element-207d736 elementor-icon-list--layout-inline elementor-mobile-align-center elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list" data-id="207d736" data-element_type="widget" data-widget_type="icon-list.default">
				<div class="elementor-widget-container">
					<ul class="elementor-icon-list-items elementor-inline-items">
							<li class="elementor-icon-list-item elementor-inline-item">
											<span class="elementor-icon-list-icon">
							<svg aria-hidden="true" class="e-font-icon-svg e-fas-bed" viewBox="0 0 640 512" xmlns="http://www.w3.org/2000/svg"><path d="M176 256c44.11 0 80-35.89 80-80s-35.89-80-80-80-80 35.89-80 80 35.89 80 80 80zm352-128H304c-8.84 0-16 7.16-16 16v144H64V80c0-8.84-7.16-16-16-16H16C7.16 64 0 71.16 0 80v352c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16v-48h512v48c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V240c0-61.86-50.14-112-112-112z"></path></svg>						</span>
										<span class="elementor-icon-list-text"><?php
                                        // apartmentBedroomNumber
                                        echo get_post_meta($propertys[0]->ID, 'apartmentBedroomNumber', true);
                                        ?></span>
									</li>
								<li class="elementor-icon-list-item elementor-inline-item">
											<span class="elementor-icon-list-icon">
							<svg aria-hidden="true" class="e-font-icon-svg e-fas-shower" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M304,320a16,16,0,1,0,16,16A16,16,0,0,0,304,320Zm32-96a16,16,0,1,0,16,16A16,16,0,0,0,336,224Zm32,64a16,16,0,1,0-16-16A16,16,0,0,0,368,288Zm-32,32a16,16,0,1,0-16-16A16,16,0,0,0,336,320Zm-32-64a16,16,0,1,0,16,16A16,16,0,0,0,304,256Zm128-32a16,16,0,1,0-16-16A16,16,0,0,0,432,224Zm-48,16a16,16,0,1,0,16-16A16,16,0,0,0,384,240Zm-16-48a16,16,0,1,0,16,16A16,16,0,0,0,368,192Zm96,32a16,16,0,1,0,16,16A16,16,0,0,0,464,224Zm32-32a16,16,0,1,0,16,16A16,16,0,0,0,496,192Zm-64,64a16,16,0,1,0,16,16A16,16,0,0,0,432,256Zm-32,32a16,16,0,1,0,16,16A16,16,0,0,0,400,288Zm-64,64a16,16,0,1,0,16,16A16,16,0,0,0,336,352Zm-32,32a16,16,0,1,0,16,16A16,16,0,0,0,304,384Zm64-64a16,16,0,1,0,16,16A16,16,0,0,0,368,320Zm21.65-218.35-11.3-11.31a16,16,0,0,0-22.63,0L350.05,96A111.19,111.19,0,0,0,272,64c-19.24,0-37.08,5.3-52.9,13.85l-10-10A121.72,121.72,0,0,0,123.44,32C55.49,31.5,0,92.91,0,160.85V464a16,16,0,0,0,16,16H48a16,16,0,0,0,16-16V158.4c0-30.15,21-58.2,51-61.93a58.38,58.38,0,0,1,48.93,16.67l10,10C165.3,138.92,160,156.76,160,176a111.23,111.23,0,0,0,32,78.05l-5.66,5.67a16,16,0,0,0,0,22.62l11.3,11.31a16,16,0,0,0,22.63,0L389.65,124.28A16,16,0,0,0,389.65,101.65Z"></path></svg>						</span>
										<span class="elementor-icon-list-text"><?php
                                        // properties_bathrooms
                                        echo get_post_meta($propertys[0]->ID, 'properties_bathrooms', true);
                                        ?></span>
									</li>
								<li class="elementor-icon-list-item elementor-inline-item">
											<span class="elementor-icon-list-icon">
							<svg aria-hidden="true" class="e-font-icon-svg e-fas-car" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M499.99 176h-59.87l-16.64-41.6C406.38 91.63 365.57 64 319.5 64h-127c-46.06 0-86.88 27.63-103.99 70.4L71.87 176H12.01C4.2 176-1.53 183.34.37 190.91l6 24C7.7 220.25 12.5 224 18.01 224h20.07C24.65 235.73 16 252.78 16 272v48c0 16.12 6.16 30.67 16 41.93V416c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32v-32h256v32c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32v-54.07c9.84-11.25 16-25.8 16-41.93v-48c0-19.22-8.65-36.27-22.07-48H494c5.51 0 10.31-3.75 11.64-9.09l6-24c1.89-7.57-3.84-14.91-11.65-14.91zm-352.06-17.83c7.29-18.22 24.94-30.17 44.57-30.17h127c19.63 0 37.28 11.95 44.57 30.17L384 208H128l19.93-49.83zM96 319.8c-19.2 0-32-12.76-32-31.9S76.8 256 96 256s48 28.71 48 47.85-28.8 15.95-48 15.95zm320 0c-19.2 0-48 3.19-48-15.95S396.8 256 416 256s32 12.76 32 31.9-12.8 31.9-32 31.9z"></path></svg>						</span>
										<span class="elementor-icon-list-text"><?php
                                        // properties_garages
                                        echo get_post_meta($propertys[0]->ID, 'properties_garages', true);
                                        ?></span>
									</li>
								<li class="elementor-icon-list-item elementor-inline-item">
											<span class="elementor-icon-list-icon">
							<svg aria-hidden="true" class="e-font-icon-svg e-fas-ruler-combined" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M160 288h-56c-4.42 0-8-3.58-8-8v-16c0-4.42 3.58-8 8-8h56v-64h-56c-4.42 0-8-3.58-8-8v-16c0-4.42 3.58-8 8-8h56V96h-56c-4.42 0-8-3.58-8-8V72c0-4.42 3.58-8 8-8h56V32c0-17.67-14.33-32-32-32H32C14.33 0 0 14.33 0 32v448c0 2.77.91 5.24 1.57 7.8L160 329.38V288zm320 64h-32v56c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-56h-64v56c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-56h-64v56c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-56h-41.37L24.2 510.43c2.56.66 5.04 1.57 7.8 1.57h448c17.67 0 32-14.33 32-32v-96c0-17.67-14.33-32-32-32z"></path></svg>						</span>
										<span class="elementor-icon-list-text"><?php
                                        // areaTotal
                                        echo get_post_meta($propertys[0]->ID, 'areaTotal', true);
                                        ?></span>
									</li>
						</ul>
				</div>
				</div>
					</div>
		</div>
					</div>
		</section>
				<section class="elementor-section elementor-inner-section elementor-element elementor-element-2a9458b elementor-section-boxed elementor-section-height-default elementor-section-height-default animated fadeInDown" data-id="2a9458b" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;animation&quot;:&quot;fadeInDown&quot;,&quot;animation_delay&quot;:300}">
						<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-100 elementor-inner-column elementor-element elementor-element-69b8a23" data-id="69b8a23" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
						<div class="elementor-element elementor-element-dd02cb3 elementor-icon-list--layout-inline elementor-mobile-align-center elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list" data-id="dd02cb3" data-element_type="widget" data-widget_type="icon-list.default">
				<div class="elementor-widget-container">
					<ul class="elementor-icon-list-items elementor-inline-items">
							<li class="elementor-icon-list-item elementor-inline-item">
											<span class="elementor-icon-list-icon">
							<svg aria-hidden="true" class="e-font-icon-svg e-fas-user" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"></path></svg>						</span>
										<span class="elementor-icon-list-text"><?php
                                        // contactFirstname and contactLastname
                                        echo get_post_meta($propertys[0]->ID, 'contactFirstname', true) . ' ' . get_post_meta($propertys[0]->ID, 'contactLastname', true);
                                        ?></span>
									</li>
						</ul>
				</div>
				</div>
					</div>
		</div>
					</div>
		</section>
					</div>
		</div>
			</a>
		<a href="<?php echo get_permalink($propertys[1]->ID); ?>" class="front-photos">
        <div class="elementor-column elementor-col-33 elementor-top-column elementor-element elementor-element-6193c1a animated fadeInUp" data-id="6193c1a" data-element_type="column" data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:300}">
			<div class="elementor-widget-wrap elementor-element-populated">
						<section class="elementor-section elementor-inner-section elementor-element elementor-element-67d57fb elementor-section-height-min-height elementor-section-boxed elementor-section-height-default animated fadeInUp" data-id="67d57fb" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:100}"
                        <?php
                        // post thumbnail
                        if (has_post_thumbnail($propertys[1]->ID)) {
                            echo 'style="background-image: url(' . get_the_post_thumbnail_url($propertys[1]->ID) . ')"';
                        }
                        ?>
                        >
							<div class="elementor-background-overlay"></div>
							<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-784402f animated slideInUp" data-id="784402f" data-element_type="column" data-settings="{&quot;animation&quot;:&quot;slideInUp&quot;,&quot;animation_delay&quot;:200}">
			<div class="elementor-widget-wrap elementor-element-populated">
						<div class="elementor-element elementor-element-aff1416 elementor-widget elementor-widget-heading" data-id="aff1416" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h2 class="elementor-heading-title elementor-size-default"><?php
            // price and ' PLN'  and delete last 3 characters
            echo substr(get_post_meta($propertys[1]->ID, 'price', true), 0, -3) . 'zł';
            ?></h2>		</div>
				</div>
					</div>
		</div>
				<div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-5188570 animated fadeIn" data-id="5188570" data-element_type="column" data-settings="{&quot;animation&quot;:&quot;fadeIn&quot;,&quot;animation_delay&quot;:200}">
			<div class="elementor-widget-wrap elementor-element-populated">
						<div class="elementor-element elementor-element-ef947e2 elementor-widget__width-auto elementor-widget elementor-widget-heading" data-id="ef947e2" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h2 class="elementor-heading-title elementor-size-default">
                <?php
                $property_status = get_post_meta($propertys[1]->ID, 'transaction', true);
                switch ($property_status) {
                    case 131 :
                        echo 'Sprzedaż';
                        break;
                    case 132 :
                        echo 'Wynajem';
                        break;
                }
                ?>
            </h2>		</div>
				</div>
					</div>
		</div>
					</div>
		</section>
				<section class="elementor-section elementor-inner-section elementor-element elementor-element-1df6174 elementor-section-boxed elementor-section-height-default elementor-section-height-default animated fadeInDown" data-id="1df6174" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;animation&quot;:&quot;fadeInDown&quot;,&quot;animation_delay&quot;:200}">
						<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-100 elementor-inner-column elementor-element elementor-element-fd44f01" data-id="fd44f01" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
						<div class="elementor-element elementor-element-062dc7c elementor-widget elementor-widget-heading" data-id="062dc7c" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<!--<h2 class="elementor-heading-title elementor-size-default">Willa</h2>-->		</div>
				</div>
				<div class="elementor-element elementor-element-20759bb elementor-widget elementor-widget-heading" data-id="20759bb" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h2 class="elementor-heading-title elementor-size-default">
                <?php
                // show title
                echo $propertys[1]->post_title;
                ?>
            </h2>		</div>
				</div>
				<div class="elementor-element elementor-element-4470aac elementor-widget elementor-widget-text-editor" data-id="4470aac" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
							<p>
                                <?php
                                echo substr(strip_tags($propertys[1]->post_content), 0, 100) . '...';
                                ?>
                            </p>						</div>
				</div>
				<div class="elementor-element elementor-element-6380fa0 elementor-icon-list--layout-inline elementor-mobile-align-center elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list" data-id="6380fa0" data-element_type="widget" data-widget_type="icon-list.default">
				<div class="elementor-widget-container">
					<ul class="elementor-icon-list-items elementor-inline-items">
							<li class="elementor-icon-list-item elementor-inline-item">
											<span class="elementor-icon-list-icon">
							<svg aria-hidden="true" class="e-font-icon-svg e-fas-bed" viewBox="0 0 640 512" xmlns="http://www.w3.org/2000/svg"><path d="M176 256c44.11 0 80-35.89 80-80s-35.89-80-80-80-80 35.89-80 80 35.89 80 80 80zm352-128H304c-8.84 0-16 7.16-16 16v144H64V80c0-8.84-7.16-16-16-16H16C7.16 64 0 71.16 0 80v352c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16v-48h512v48c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V240c0-61.86-50.14-112-112-112z"></path></svg>						</span>
										<span class="elementor-icon-list-text">
                                            <?php
                                            // apartmentBedroomNumber
                                            echo get_post_meta($propertys[1]->ID, 'apartmentBedroomNumber', true);
                                            ?>
                                        </span>
									</li>
								<li class="elementor-icon-list-item elementor-inline-item">
											<span class="elementor-icon-list-icon">
							<svg aria-hidden="true" class="e-font-icon-svg e-fas-shower" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M304,320a16,16,0,1,0,16,16A16,16,0,0,0,304,320Zm32-96a16,16,0,1,0,16,16A16,16,0,0,0,336,224Zm32,64a16,16,0,1,0-16-16A16,16,0,0,0,368,288Zm-32,32a16,16,0,1,0-16-16A16,16,0,0,0,336,320Zm-32-64a16,16,0,1,0,16,16A16,16,0,0,0,304,256Zm128-32a16,16,0,1,0-16-16A16,16,0,0,0,432,224Zm-48,16a16,16,0,1,0,16-16A16,16,0,0,0,384,240Zm-16-48a16,16,0,1,0,16,16A16,16,0,0,0,368,192Zm96,32a16,16,0,1,0,16,16A16,16,0,0,0,464,224Zm32-32a16,16,0,1,0,16,16A16,16,0,0,0,496,192Zm-64,64a16,16,0,1,0,16,16A16,16,0,0,0,432,256Zm-32,32a16,16,0,1,0,16,16A16,16,0,0,0,400,288Zm-64,64a16,16,0,1,0,16,16A16,16,0,0,0,336,352Zm-32,32a16,16,0,1,0,16,16A16,16,0,0,0,304,384Zm64-64a16,16,0,1,0,16,16A16,16,0,0,0,368,320Zm21.65-218.35-11.3-11.31a16,16,0,0,0-22.63,0L350.05,96A111.19,111.19,0,0,0,272,64c-19.24,0-37.08,5.3-52.9,13.85l-10-10A121.72,121.72,0,0,0,123.44,32C55.49,31.5,0,92.91,0,160.85V464a16,16,0,0,0,16,16H48a16,16,0,0,0,16-16V158.4c0-30.15,21-58.2,51-61.93a58.38,58.38,0,0,1,48.93,16.67l10,10C165.3,138.92,160,156.76,160,176a111.23,111.23,0,0,0,32,78.05l-5.66,5.67a16,16,0,0,0,0,22.62l11.3,11.31a16,16,0,0,0,22.63,0L389.65,124.28A16,16,0,0,0,389.65,101.65Z"></path></svg>						</span>
										<span class="elementor-icon-list-text">
                                            <?php
                                            // properties_bathrooms
                                            echo get_post_meta($propertys[1]->ID, 'properties_bathrooms', true);
                                            ?>
                                        </span>
									</li>
								<li class="elementor-icon-list-item elementor-inline-item">
											<span class="elementor-icon-list-icon">
							<svg aria-hidden="true" class="e-font-icon-svg e-fas-car" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M499.99 176h-59.87l-16.64-41.6C406.38 91.63 365.57 64 319.5 64h-127c-46.06 0-86.88 27.63-103.99 70.4L71.87 176H12.01C4.2 176-1.53 183.34.37 190.91l6 24C7.7 220.25 12.5 224 18.01 224h20.07C24.65 235.73 16 252.78 16 272v48c0 16.12 6.16 30.67 16 41.93V416c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32v-32h256v32c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32v-54.07c9.84-11.25 16-25.8 16-41.93v-48c0-19.22-8.65-36.27-22.07-48H494c5.51 0 10.31-3.75 11.64-9.09l6-24c1.89-7.57-3.84-14.91-11.65-14.91zm-352.06-17.83c7.29-18.22 24.94-30.17 44.57-30.17h127c19.63 0 37.28 11.95 44.57 30.17L384 208H128l19.93-49.83zM96 319.8c-19.2 0-32-12.76-32-31.9S76.8 256 96 256s48 28.71 48 47.85-28.8 15.95-48 15.95zm320 0c-19.2 0-48 3.19-48-15.95S396.8 256 416 256s32 12.76 32 31.9-12.8 31.9-32 31.9z"></path></svg>						</span>
										<span class="elementor-icon-list-text">1</span>
									</li>
								<li class="elementor-icon-list-item elementor-inline-item">
											<span class="elementor-icon-list-icon">
							<svg aria-hidden="true" class="e-font-icon-svg e-fas-ruler-combined" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M160 288h-56c-4.42 0-8-3.58-8-8v-16c0-4.42 3.58-8 8-8h56v-64h-56c-4.42 0-8-3.58-8-8v-16c0-4.42 3.58-8 8-8h56V96h-56c-4.42 0-8-3.58-8-8V72c0-4.42 3.58-8 8-8h56V32c0-17.67-14.33-32-32-32H32C14.33 0 0 14.33 0 32v448c0 2.77.91 5.24 1.57 7.8L160 329.38V288zm320 64h-32v56c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-56h-64v56c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-56h-64v56c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-56h-41.37L24.2 510.43c2.56.66 5.04 1.57 7.8 1.57h448c17.67 0 32-14.33 32-32v-96c0-17.67-14.33-32-32-32z"></path></svg>						</span>
										<span class="elementor-icon-list-text">
                                            <?php
                                            // areaTotal
                                            echo get_post_meta($propertys[1]->ID, 'areaTotal', true);
                                            ?>
                                        </span>
									</li>
						</ul>
				</div>
				</div>
					</div>
		</div>
					</div>
		</section>
				<section class="elementor-section elementor-inner-section elementor-element elementor-element-1b764a7 elementor-section-boxed elementor-section-height-default elementor-section-height-default animated fadeInDown" data-id="1b764a7" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;animation&quot;:&quot;fadeInDown&quot;,&quot;animation_delay&quot;:300}">
						<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-100 elementor-inner-column elementor-element elementor-element-b712779" data-id="b712779" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
						<div class="elementor-element elementor-element-6cf3298 elementor-icon-list--layout-inline elementor-mobile-align-center elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list" data-id="6cf3298" data-element_type="widget" data-widget_type="icon-list.default">
				<div class="elementor-widget-container">
					<ul class="elementor-icon-list-items elementor-inline-items">
							<li class="elementor-icon-list-item elementor-inline-item">
											<span class="elementor-icon-list-icon">
							<svg aria-hidden="true" class="e-font-icon-svg e-fas-user" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"></path></svg>						</span>
										<span class="elementor-icon-list-text">
                                            <?php
                                            // contactFirstname and contactLastname
                                            echo get_post_meta($propertys[1]->ID, 'contactFirstname', true) . ' ' . get_post_meta($propertys[1]->ID, 'contactLastname', true);
                                            ?>
                                        </span>
									</li>
						</ul>
				</div>
				</div>
					</div>
		</div>
					</div>
		</section>
					</div>
		</div>
			</a>
		<a href="<?php echo get_permalink($propertys[2]->ID); ?>" class="front-photos">
        <div class="elementor-column elementor-col-33 elementor-top-column elementor-element elementor-element-e73d527 animated fadeInUp" data-id="e73d527" data-element_type="column" data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:500}">
			<div class="elementor-widget-wrap elementor-element-populated">
						<section class="elementor-section elementor-inner-section elementor-element elementor-element-8dc6fe0 elementor-section-height-min-height elementor-section-boxed elementor-section-height-default animated fadeInUp" data-id="8dc6fe0" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;animation&quot;:&quot;fadeInUp&quot;,&quot;animation_delay&quot;:100}"
                        <?php
                        // post thumbnail
                        if (has_post_thumbnail($propertys[2]->ID)) {
                            echo 'style="background-image: url(' . get_the_post_thumbnail_url($propertys[2]->ID) . ')"';
                        }
                        ?>
                        >
							<div class="elementor-background-overlay"></div>
							<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-ae6b2d8 animated slideInUp" data-id="ae6b2d8" data-element_type="column" data-settings="{&quot;animation&quot;:&quot;slideInUp&quot;,&quot;animation_delay&quot;:200}">
			<div class="elementor-widget-wrap elementor-element-populated">
						<div class="elementor-element elementor-element-8a48cd5 elementor-widget elementor-widget-heading" data-id="8a48cd5" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h2 class="elementor-heading-title elementor-size-default">
                <?php
                // price and ' PLN'  and delete last 3 characters
                echo substr(get_post_meta($propertys[2]->ID, 'price', true), 0, -3) . 'zł';
                ?>
            </h2>		</div>
				</div>
					</div>
		</div>
				<div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-71036aa animated fadeIn" data-id="71036aa" data-element_type="column" data-settings="{&quot;animation&quot;:&quot;fadeIn&quot;,&quot;animation_delay&quot;:200}">
			<div class="elementor-widget-wrap elementor-element-populated">
						<div class="elementor-element elementor-element-c106307 elementor-widget__width-auto elementor-widget elementor-widget-heading" data-id="c106307" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h2 class="elementor-heading-title elementor-size-default">
                <?php
                $property_status = get_post_meta($propertys[2]->ID, 'transaction', true);
                switch ($property_status) {
                    case 131 :
                        echo 'Sprzedaż';
                        break;
                    case 132 :
                        echo 'Wynajem';
                        break;
                }
                ?>
            </h2>		</div>
				</div>
					</div>
		</div>
					</div>
		</section>
				<section class="elementor-section elementor-inner-section elementor-element elementor-element-79bc4d9 elementor-section-boxed elementor-section-height-default elementor-section-height-default animated fadeInDown" data-id="79bc4d9" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;animation&quot;:&quot;fadeInDown&quot;,&quot;animation_delay&quot;:200}">
						<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-100 elementor-inner-column elementor-element elementor-element-84fde47" data-id="84fde47" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
						<div class="elementor-element elementor-element-1b5e3bc elementor-widget elementor-widget-heading" data-id="1b5e3bc" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<!--<h2 class="elementor-heading-title elementor-size-default">Apartament</h2>		</div>-->
				</div>
				<div class="elementor-element elementor-element-da4da68 elementor-widget elementor-widget-heading" data-id="da4da68" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
			<h2 class="elementor-heading-title elementor-size-default">
                <?php
                // show title
                echo $propertys[2]->post_title;
                ?>
            </h2>		</div>
				</div>
				<div class="elementor-element elementor-element-631fed4 elementor-widget elementor-widget-text-editor" data-id="631fed4" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
							<p>
                                <?php
                                echo substr(strip_tags($propertys[1]->post_content), 0, 100) . '...';
                                ?>
                            </p>						</div>
				</div>
				<div class="elementor-element elementor-element-3964530 elementor-icon-list--layout-inline elementor-mobile-align-center elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list" data-id="3964530" data-element_type="widget" data-widget_type="icon-list.default">
				<div class="elementor-widget-container">
					<ul class="elementor-icon-list-items elementor-inline-items">
							<li class="elementor-icon-list-item elementor-inline-item">
											<span class="elementor-icon-list-icon">
							<svg aria-hidden="true" class="e-font-icon-svg e-fas-bed" viewBox="0 0 640 512" xmlns="http://www.w3.org/2000/svg"><path d="M176 256c44.11 0 80-35.89 80-80s-35.89-80-80-80-80 35.89-80 80 35.89 80 80 80zm352-128H304c-8.84 0-16 7.16-16 16v144H64V80c0-8.84-7.16-16-16-16H16C7.16 64 0 71.16 0 80v352c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16v-48h512v48c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V240c0-61.86-50.14-112-112-112z"></path></svg>						</span>
										<span class="elementor-icon-list-text">
                                            <?php
                                            // apartmentBedroomNumber
                                            echo get_post_meta($propertys[2]->ID, 'apartmentBedroomNumber', true);
                                            ?>
                                        </span>
									</li>
								<li class="elementor-icon-list-item elementor-inline-item">
											<span class="elementor-icon-list-icon">
							<svg aria-hidden="true" class="e-font-icon-svg e-fas-shower" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M304,320a16,16,0,1,0,16,16A16,16,0,0,0,304,320Zm32-96a16,16,0,1,0,16,16A16,16,0,0,0,336,224Zm32,64a16,16,0,1,0-16-16A16,16,0,0,0,368,288Zm-32,32a16,16,0,1,0-16-16A16,16,0,0,0,336,320Zm-32-64a16,16,0,1,0,16,16A16,16,0,0,0,304,256Zm128-32a16,16,0,1,0-16-16A16,16,0,0,0,432,224Zm-48,16a16,16,0,1,0,16-16A16,16,0,0,0,384,240Zm-16-48a16,16,0,1,0,16,16A16,16,0,0,0,368,192Zm96,32a16,16,0,1,0,16,16A16,16,0,0,0,464,224Zm32-32a16,16,0,1,0,16,16A16,16,0,0,0,496,192Zm-64,64a16,16,0,1,0,16,16A16,16,0,0,0,432,256Zm-32,32a16,16,0,1,0,16,16A16,16,0,0,0,400,288Zm-64,64a16,16,0,1,0,16,16A16,16,0,0,0,336,352Zm-32,32a16,16,0,1,0,16,16A16,16,0,0,0,304,384Zm64-64a16,16,0,1,0,16,16A16,16,0,0,0,368,320Zm21.65-218.35-11.3-11.31a16,16,0,0,0-22.63,0L350.05,96A111.19,111.19,0,0,0,272,64c-19.24,0-37.08,5.3-52.9,13.85l-10-10A121.72,121.72,0,0,0,123.44,32C55.49,31.5,0,92.91,0,160.85V464a16,16,0,0,0,16,16H48a16,16,0,0,0,16-16V158.4c0-30.15,21-58.2,51-61.93a58.38,58.38,0,0,1,48.93,16.67l10,10C165.3,138.92,160,156.76,160,176a111.23,111.23,0,0,0,32,78.05l-5.66,5.67a16,16,0,0,0,0,22.62l11.3,11.31a16,16,0,0,0,22.63,0L389.65,124.28A16,16,0,0,0,389.65,101.65Z"></path></svg>						</span>
										<span class="elementor-icon-list-text">
                                            <?php
                                            // properties_bathrooms
                                            echo get_post_meta($propertys[2]->ID, 'properties_bathrooms', true);
                                            ?>
                                        </span>
									</li>
								<li class="elementor-icon-list-item elementor-inline-item">
											<span class="elementor-icon-list-icon">
							<svg aria-hidden="true" class="e-font-icon-svg e-fas-car" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M499.99 176h-59.87l-16.64-41.6C406.38 91.63 365.57 64 319.5 64h-127c-46.06 0-86.88 27.63-103.99 70.4L71.87 176H12.01C4.2 176-1.53 183.34.37 190.91l6 24C7.7 220.25 12.5 224 18.01 224h20.07C24.65 235.73 16 252.78 16 272v48c0 16.12 6.16 30.67 16 41.93V416c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32v-32h256v32c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32v-54.07c9.84-11.25 16-25.8 16-41.93v-48c0-19.22-8.65-36.27-22.07-48H494c5.51 0 10.31-3.75 11.64-9.09l6-24c1.89-7.57-3.84-14.91-11.65-14.91zm-352.06-17.83c7.29-18.22 24.94-30.17 44.57-30.17h127c19.63 0 37.28 11.95 44.57 30.17L384 208H128l19.93-49.83zM96 319.8c-19.2 0-32-12.76-32-31.9S76.8 256 96 256s48 28.71 48 47.85-28.8 15.95-48 15.95zm320 0c-19.2 0-48 3.19-48-15.95S396.8 256 416 256s32 12.76 32 31.9-12.8 31.9-32 31.9z"></path></svg>						</span>
										<span class="elementor-icon-list-text">
                                            <?php
                                            // parkingSpaces
                                            echo get_post_meta($propertys[2]->ID, 'parkingSpaces', true);
                                            ?>
                                        </span>
									</li>
								<li class="elementor-icon-list-item elementor-inline-item">
											<span class="elementor-icon-list-icon">
							<svg aria-hidden="true" class="e-font-icon-svg e-fas-ruler-combined" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M160 288h-56c-4.42 0-8-3.58-8-8v-16c0-4.42 3.58-8 8-8h56v-64h-56c-4.42 0-8-3.58-8-8v-16c0-4.42 3.58-8 8-8h56V96h-56c-4.42 0-8-3.58-8-8V72c0-4.42 3.58-8 8-8h56V32c0-17.67-14.33-32-32-32H32C14.33 0 0 14.33 0 32v448c0 2.77.91 5.24 1.57 7.8L160 329.38V288zm320 64h-32v56c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-56h-64v56c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-56h-64v56c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-56h-41.37L24.2 510.43c2.56.66 5.04 1.57 7.8 1.57h448c17.67 0 32-14.33 32-32v-96c0-17.67-14.33-32-32-32z"></path></svg>						</span>
										<span class="elementor-icon-list-text">
                                            <?php
                                            // areaTotal
                                            echo get_post_meta($propertys[2]->ID, 'areaTotal', true);
                                            ?>
                                        </span>
									</li>
						</ul>
				</div>
				</div>
					</div>
		</div>
					</div>
		</section>
				<section class="elementor-section elementor-inner-section elementor-element elementor-element-4c76d02 elementor-section-boxed elementor-section-height-default elementor-section-height-default animated fadeInDown" data-id="4c76d02" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;animation&quot;:&quot;fadeInDown&quot;,&quot;animation_delay&quot;:300}">
						<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-100 elementor-inner-column elementor-element elementor-element-f8e9891" data-id="f8e9891" data-element_type="column">
			<div class="elementor-widget-wrap elementor-element-populated">
						<div class="elementor-element elementor-element-b7f0d00 elementor-icon-list--layout-inline elementor-mobile-align-center elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list" data-id="b7f0d00" data-element_type="widget" data-widget_type="icon-list.default">
				<div class="elementor-widget-container">
					<ul class="elementor-icon-list-items elementor-inline-items">
							<li class="elementor-icon-list-item elementor-inline-item">
											<span class="elementor-icon-list-icon">
							<svg aria-hidden="true" class="e-font-icon-svg e-fas-user" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"></path></svg>						</span>
										<span class="elementor-icon-list-text">
                                            <?php
                                            // contactFirstname and contactLastname
                                            echo get_post_meta($propertys[2]->ID, 'contactFirstname', true) . ' ' . get_post_meta($propertys[2]->ID, 'contactLastname', true);
                                            ?>
                                        </span>
									</li>
						</ul>
				</div>
				</div>
					</div>
		</div>
					</div>
		</section>
					</div>
		</div>
			</a>
    </div>
    <section class="elementor-section elementor-top-section elementor-element elementor-element-638511d elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="638511d" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
        <style>
            .elementor-244 .elementor-element.elementor-element-638511dd {
                display: block !important;
            }
</style>
        <?php
    return ob_get_clean();
}
add_shortcode('asarionos_mainpage', 'asarinos_main_page_shortcode');

