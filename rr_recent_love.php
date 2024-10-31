<?php
/*
Plugin Name: Recent Love - A List of Recent Comments
Version: 0.5
Plugin URI: http://ronrothman.com/public/leftbraned/wordpress-plugin-recent-love-a-list-of-recent-comments/
Author: Ron Rothman
Author URI: http://ronrothman.com/public/leftbraned/
Description: Simple list of recent comments.  Adds template function rr_recent_comments($num_recent_posts = 5, $before = '<li>', $after = '</li>')

/*
Adds template function:
rr_recent_comments($num_recent_posts = 5, $before = '<li>', $after = '</li>')
rr_recent_comments can be called to return a list of recent comments.
When invoked with no parameters, it returns a list of the latest comment
from each of the most recent 5 posts.  Only one comment (the most recent one)
per post is listed.

You can globally exlcude certain posters, based on various criteria
(author email, author name, etc.).  This is helpful, for example,
to permanently exclude yourself from the list.

TO INSTALL:
1. Put this file in wp-content/plugins.
2. Enable the plugin in the WordPress Plugin Admin panel.

TO USE:
1. Add the following to the appropriate theme file (e.g., sidebar.php):
	<?php echo rr_recent_comments(); ?>
*/


function rr_recent_comments (
	$num_recent_posts = 5,
	$before = '<li>',
	$after = '</li>'
)
{

	global $wpdb, $id;
	
	//
	// CONFIGURABLE SETTINGS begin
	//

	//
	// EXCLUDE COMMENTS BY POST AUTHOR?
	//
	// set to true to exclude an author's comments from his/her own posts.
	// set to false to include them.
	// default: true
	$exclude_authors_comments = true;

	//
	// POST TITLE TRUNCATION
	//
	// longest length post title allowed
	// (entries whose titles are longer will be truncated.)
	// set it to 0 to avoid truncation altogether.
	// default: 38
	$max_title_length = 38;

	//
	// INCLUDE LINKS TO COMMENT AUTHORS' WEBSITES?
	//
	// if true, the names of the commenters will be hyperlinked to the websites
	// they specify when filling out the comment form.
	// if false, their names will appear as plain (unlinked) text.
	// default: true
	$link_to_commenters_websites = true;

	//
	// COMMENT TYPES TO INCLUDE
	//
	// set this to true to prevent trackbacks from appearing in comment list
	// set it to false to show trackbacks/pingbacks
	// default: true
	$suppress_trackbacks = true;

	//
	// EXCLUDING CERTAIN COMMENTERS
	//
	// use these two fields to determine how/who to exlcude certain
	// comments from appearing in the list
	//
	// $identify_authors_by:
	// how to determine a unique poster.  valid choices are:
	// no value (empty string), comment_author, comment_author_url,
	// or comment_author_email
	// default: no value
	// 
	#$identify_authors_by = 'comment_author_email';
	$identify_authors_by = '';

	// $excludes_sql_list:
	// list of people to always exclude from the list.
	//
	// format: ('person1', 'person2', 'person3', ...)
	//
	// the entries in this list must match the identify_authors_by choice
	// above.  i.e., if you chose comment_author_email above, then the
	// exclude entries should be email addresses.  if you chose
	// comment_author_url, then they should be URLs, etc.
	//
	// default: none (YOU NEED TO ADD EMAIL ADDRESSES FOR THE DEFAULT TO TAKE EFFECT)
	// 
	$excludes_sql_list = "('someone@yoursite.com')"; // one commenter excluded
	#$excludes_sql_list = "('someone@yoursite.com', 'someoneElse@yoursite.com')"; // multiple commenters excluded

	$debug = false;

	//
	// CONFIGURABLE SETTINGS end
	//

	///////////////////////////////////////////////////////////////


	$sql = "
		SELECT
			comment_post_ID,
			MAX(comment_ID) AS comment_ID
		FROM
			$wpdb->comments C,
			$wpdb->posts P
		WHERE
			C.comment_post_ID = P.ID
		AND comment_approved = '1'
	";

	if (!empty($identify_authors_by)) {
		$sql .= "
			AND $identify_authors_by NOT IN $excludes_sql_list
		";
	}

	if ($exclude_authors_comments) {
		$sql .= "
			AND C.user_id != P.post_author
		";
	}

	if ($suppress_trackbacks) {
		$sql .= "
			AND comment_type = ''
		";
	}

	# hack alert: assumes that comments are continuously ordered by comment_ID
	$sql .= "
		GROUP BY comment_post_ID
		ORDER BY comment_ID DESC
		LIMIT $num_recent_posts
	";


	if ($debug) {
		echo "\n<!-- RUNNING QUERY 1: $sql -->\n"; # debug
	}

	$posts = $wpdb->get_results($sql);


	if (empty($posts)) {
		return '<!-- no posts with comments -->';
	}

	# build IN clause list
	$comment_id_sql_list = '(';

	# we're guaranteed at least one...
	$comment_id_sql_list .= $posts[0]->comment_ID;

	for ($i = 1; $i < (count($posts)); $i++) {
		$comment_id_sql_list .= ',' . $posts[$i]->comment_ID;
	}

	$comment_id_sql_list .= ')';


	$sql = "
		SELECT
			comment_post_ID,
			comment_author,
			comment_author_url,
			post_title
		FROM
			$wpdb->comments C,
			$wpdb->posts P
		WHERE
			C.comment_post_ID = P.ID
		AND comment_approved = '1'
	";

	if (!empty($identify_authors_by)) {
		$sql .= "
			AND $identify_authors_by NOT IN $excludes_sql_list
		";
	}

	if ($exclude_authors_comments) {
		$sql .= "
			AND C.user_id != P.post_author
		";
	}

	$sql .= "
		AND C.comment_ID IN $comment_id_sql_list
		ORDER BY comment_ID DESC
	";

	if ($debug) {
		echo "\n<!-- RUNNING QUERY 2: $sql -->\n"; # debug
	}

	$comments = $wpdb->get_results($sql);


	$output = '';

	foreach ($comments as $comment) {
		$output .= "\n$before";

		$url = $link_to_commenters_websites ? $comment->comment_author_url : '';
		$name = $comment->comment_author;
		$post_id = $comment->comment_post_ID;
		$permalink = get_permalink($post_id);

		$post_title = stripslashes(strip_tags($comment->post_title));

		# truncate post title
		if ($max_title_length and (strlen($post_title) > $max_title_length)) {
			$post_title = htmlspecialchars(rtrim(substr($post_title, 0, $max_title_length))) . '&hellip;';
		}
		else {
			$post_title = htmlspecialchars($post_title);
		}


		# assemble the output

		if (!empty($url)) {
			$output .= "<a href='$url'>";
		}

		$output .= "$name";

		if (!empty($url)) {
			$output .= '</a>';
		}

		# (yes, css would be better)
		$output .= " on <i><a href='$permalink'>$post_title</a></i>";

		$output .= "$after\n";
	}

	return $output;
}

?>
