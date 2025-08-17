<?php
/**
 * Media handling class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Asarinos_Media {
    
    /**
     * Sideload image from URL
     */
    public static function sideload_image($file, $post_id, $desc = null, $return = 'html') {
        if (empty($file)) {
            return new WP_Error('empty_file', 'File URL is empty');
        }

        // Set variables for storage, fix file filename for query strings
        preg_match('/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches);
        
        if (empty($matches)) {
            return new WP_Error('invalid_file', 'Invalid file format');
        }

        $matches[1] = 'jpg';
        $file_array = array();
        $file_array['name'] = $matches[0] . '.jpg';

        // Download file to temp location
        $saveTo = ABSPATH . '/wp-content/uploads/asarinos' . rand(1, 99999) . '.jpg';
        
        $file_content = file_get_contents($file);
        if ($file_content === false) {
            return new WP_Error('download_failed', 'Failed to download file');
        }
        
        file_put_contents($saveTo, $file_content);
        $file_array['tmp_name'] = $saveTo;

        // If error storing temporarily, return the error
        if (is_wp_error($file_array['tmp_name'])) {
            return $file_array['tmp_name'];
        }

        // Do the validation and storage stuff
        $id = media_handle_sideload($file_array, $post_id, $desc);

        // If error storing permanently, unlink
        if (is_wp_error($id)) {
            @unlink($file_array['tmp_name']);
            return $id;
        } elseif ($return === 'id') {
            return $id;
        }

        $src = wp_get_attachment_url($id);

        // Finally, check to make sure the file has been saved, then return the HTML
        if (!empty($src)) {
            if ($return === 'src') {
                return $src;
            }
        } else {
            return new WP_Error('image_sideload_failed');
        }
        
        return $src;
    }
    
    /**
     * Set thumbnail from URL
     */
    public static function set_thumbnail_from_url($post_id, $url) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Load the image
        $result = self::sideload_image($url, $post_id);

        $attachments = get_posts(array(
            'numberposts' => '1',
            'post_parent' => $post_id,
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'order' => 'ASC'
        ));
        
        // Set image as the post thumbnail
        if (sizeof($attachments) > 0) {
            set_post_thumbnail($post_id, $attachments[0]->ID);
        }
    }
    
    /**
     * Insert attachment
     */
    public static function insert_attachment($file_handler, $post_id, $setthumb = false) {
        if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        require_once(ABSPATH . "wp-admin/includes/image.php");
        require_once(ABSPATH . "wp-admin/includes/file.php");
        require_once(ABSPATH . "wp-admin/includes/media.php");

        $attach_id = media_handle_upload($file_handler, $post_id);
        
        if ($setthumb) {
            set_post_thumbnail($post_id, $attach_id);
        }
        
        return $attach_id;
    }
    
    /**
     * Edit attachments
     */
    public static function edit_attachments($post_id = 0, $field_name = null, $gallery_files = array(), $exists_files = array(), $post_files = array(), $files_array_prefix = null, $valid_formats = array("jpg", "png", "gif"), $max_file_size = "4MB") {
        global $post;
        
        $max_file_size = wp_convert_hr_to_bytes('15MB');
        $attachmnets = $attach_id = $message = array();
        
        if (empty($post_id)) {
            $post_id = $post->ID;
        }

        if (!empty($exists_files)) {
            $results = array_diff($gallery_files, $exists_files);

            if (!empty($results)) {
                foreach ($results as $res => $val) {
                    wp_delete_attachment($res, true);
                    unset($gallery_files[$res]);
                }
            }
        } else {
            if (!empty($gallery_files)) {
                foreach ($gallery_files as $res => $val) {
                    wp_delete_attachment($res, true);
                    unset($gallery_files[$res]);
                }
            }
        }

        if (!empty($exists_files)) {
            foreach ($exists_files as $key => $val) {
                $attachmnets[$key] = esc_url($val);
            }
        }

        if (!empty($post_files['name'])) {
            $tmp_files = $_FILES;
            
            foreach ($post_files['name'] as $f => $name) {
                if ($post_files['error'][$f] == 4) continue;
                
                if ($post_files['error'][$f] == 0) {
                    if ($post_files['size'][$f] > $max_file_size) {
                        $message[] = sprintf(__(' is too large!', 'asarinos'), $name);
                        continue;
                    } elseif (!in_array(pathinfo($name, PATHINFO_EXTENSION), $valid_formats)) {
                        $message[] = sprintf(__(' is not a valid format!', 'asarinos'), $name);
                        continue;
                    } else {
                        if ($post_files['name'][$f]) {
                            $file = array(
                                'name' => $post_files['name'][$f],
                                'type' => $post_files['type'][$f],
                                'tmp_name' => $post_files['tmp_name'][$f],
                                'error' => $post_files['error'][$f],
                                'size' => $post_files['size'][$f]
                            );
                        }

                        $_FILES = array($files_array_prefix => $file);
                        
                        foreach ($_FILES as $file => $array) {
                            $attach_id[] = self::insert_attachment($file, $post_id);
                        }
                        
                        $_FILES = $tmp_files;
                    }
                }
            }
        }

        if (!empty($attach_id)) {
            foreach ($attach_id as $val) {
                $file_url = wp_get_attachment_url($val);
                $attachmnets[$val] = $file_url;
            }
        }

        update_post_meta($post_id, $field_name, $attachmnets);

        return $message;
    }
}