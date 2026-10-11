<?php
/**
 * Sensei LMS adjustments.
 *
 * Loaded from functions.php. The filters here only act on Sensei LMS text,
 * so they do nothing when the plugin isn't active.
 *
 * @package NRDS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Hide Sensei's "There are no published lessons in this course yet." notice
 * on courses that have no published lessons.
 */
add_filter( 'gettext', 'hide_sensei_no_lessons_message', 20, 3 );
function hide_sensei_no_lessons_message( $translated_text, $text, $domain ) {
    if ( 'sensei-lms' === $domain && 'There are no published lessons in this course yet.' === $text ) {
        return ''; // Returns an empty string instead of the notice
    }
    return $translated_text;
}

// Remove the self enrollment not allowed notice
// ("Please contact the course administrator to sign up for this course.").
// Sensei adds it at priority 20, so the same priority is needed to remove it.
remove_action( 'sensei_single_course_content_inside_before', [ 'Sensei_Course', 'self_enrollment_not_allowed_message' ], 20 );
