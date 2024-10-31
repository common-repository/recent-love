=== Recent Love - A List of Recent Comments ===
Contributors: RonR
Donate link: http://ronrothman.com/public/leftbraned/wordpress-plugin-recent-love-a-list-of-recent-comments
Tags: comments, recent comments
Requires at least: 1.5
Tested up to: 2.9.2
Stable tag: 0.5

Displays a list of recent comments.  Several configuration options exist.

== Description ==

*Displays a list (typically, in your sidebar) of your visitors' recent comments.
By default, shows the latest comment from each of the last five commented-on posts.*

You control:

* how many recent comments to display
* how many characters of the post title to display
* how to style each comment in the list (with open/close tags)
* whether to include or omit trackbacks/pingbacks
* whether to include or omit comments from the post's author
* a list of users for whom never to display comments (e.g., yourself; or use this to create a blacklist).

See "Installation" for easy setup instructions.

### History

A few months ago, I wanted to display a list of recent comments in my blog's
sidebar.  After searching through the countless plugins which return lists of
comments, I finally decided on Scott Reilly's <a
href="http://www.coffee2code.com/archives/2004/07/08/plugin-toprecent-commenters/">Top/Recent
Commenters</a> plug-in.

Unfortunately, I had to tweak Scott's code to get it to work the way I wanted.
One tweak led to another (you know how it is) until I finally just scrapped
the original code and rolled my own.  (You can see it in action on my blog's
<a href="http://ronrothman.com/public/leftbraned/">front page</a>.)

### What It Does

Adds a single template function:

    rr_recent_comments (
        $num_recent_posts = 5,
        $before = '<li>',
        $after = '</li>'
    )

The function `rr_recent_comments()` returns a list of recent comments.

When invoked with no parameters, it returns a list containing the latest comment from
each of the most recent 5 posts.  *Only one comment (the most recent one) per
post is listed.*

By default, any comments made by the author of the post are _not_
included in the list.  (This behaviour can be configurably modified.)  Long
post titles are truncated to a length which you can configure.  Trackbacks and
pingbacks are not included in the list by default.

You can exlcude certain posters, based on various criteria (author email,
author name, etc.).  This is helpful, for example, to exclude yourself from
the list.

You can customize your list two ways: tag parameters and global config
options.


#### Parameters

The function `rr_recent_comments()` takes three parameters, all of which are optional.

1.  `$num_recent_posts`  
The [maximum] number of comments to display.  
*Default*: 5
2.  `$before`  
The text to display before each comment.  
*Default*: '&lt;li&gt;'
3.  `$after`  
The text to display after each comment.  
*Default*: '&lt;/li&gt;'

#### Configuration Options

The behaviour of `rr_recent_comments()` can be customized on a
global basis by editing some values in the plugin file, `rr_recent_comments.php`.
With the possible exception of `$max_title_length`,
most installations will probably not need to
futz with these, but they're there if you want 'em.

* `$max_title_length` (_Default_: 38)
This value represents the longest length that a post title
may be without it being truncated for display.  Titles longer than this will
be indiscriminately chopped, and an ellipses will be appended to them.  Set to
0 to disable truncation altogether (not recommended).

* `$exclude_authors_comments` (_Default_: true)
Set to true to exclude an author's comments from his/her
own posts.  Set to false to include them.

* `$link_to_commenters_websites` (_Default_: true)
Set to true to hyperlink the comment author's name to the
website they enter on your comment form.  Set to false to suppress
hyperlinking.

* `$suppress_trackbacks` (_Default_: true)
If true, trackbacks and pingbacks are excluded from the
comment list.  Set to false to include them.

* `$identify_authors_by` and `$excludes_sql_list`
(_Default_: no exclusions)
These two fields work in conjunction to allow you to
specify a list of comments whose comments should _never_ be included in
the list.  (Note that most blog installation will not need to use this
functionality.)  
First, choose the criterion by which you want to identify the
excluded comments and set `$identify_authors_by` to one of:
	* `'comment_author'`,
	* `'comment_author_url'` or
	* `'comment_author_email'`.  
Then add the excluded commenters to the list `$excludes_sql_list`;
be careful to follow the correct format, which is: `('COMMENTER_1', 'COMMENTER_2', ..., 'COMMENTER_N')`.  
You must specify the COMMENTERs in a way that matches the value of `$identify_authors_by`.  I.e., if `$identify_authors_by`
is `'comment_author_email'`, then the COMMENTERs in `$excludes_sql_list` must all be email addresses.

### Revision History

0.1 _(May 1, 2005)_: Initial revision.

0.2 _(October 30, 2005)_: Rewrite and add new features.

0.3 _(November 11, 2005)_: Clean up for publication.

0.4 _(?)_

0.5 _(April 4, 2007)_: Add `$link_to_commenters_websites` option.



== Installation ==

### How to Install It

1. Download the plugin file, and put it file in your WordPress plugin directory, `wp-content/plugins`.
(Make sure to name it `rr_recent_love`**`.php`**.
1. Enable the plugin in the WordPress Plugin Admin panel.

### How to Use It

Just insert the following call into your theme files, wherever you want the
comment list to appear (e.g., `sidebar.php`):

`<?php echo rr_recent_comments(); ?>`

Just insert the following call into your theme files, wherever you want the
comment list to appear (e.g., `sidebar.php`):

`
<ul class='recent_comments'>
	<?php echo rr_recent_comments(); ?>
</ul>
`


== Frequently Asked Questions ==

= Where can I send suggestions for new features? =

Please see the [Recent Love home page](http://ronrothman.com/public/leftbraned/wordpress-plugin-recent-love-a-list-of-recent-comments "WordPress plug-in Recent Love").

== Screenshots ==

