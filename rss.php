<?php if (!defined('BASE_DIR')) die('No direct script access allowed');

define('ORIG_ROOT', uri::scheme() . substr(strstr(uri::host(),'.'),1) . '/');

$blog_url = explode(".", uri::host());
$blog_url = $blog_url[0];

$blog_id = $blog->get_id_by('blog_url', $blog_url);
$blog_info = $blog->info($blog_id);

header("Content-Type: application/rss+xml");

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>"; ?>

<rss version="2.0">
	<channel>
		<title><![CDATA[<?php echo $blog_info['blog_name']; ?>]]></title>
		<link><?php echo uri::scheme() . uri::host(); ?>/</link>
		<description><![CDATA[]]></description>
		<language>sv-se</language>
		<lastBuildDate><?php echo date("D, d M Y H:i:s T"); ?></lastBuildDate>
		<generator><?php echo ORIG_ROOT; ?></generator>
<?php $list = $blog->get_post_list($blog_id);
foreach ($list AS $v) : ?>
	<item>
		<title><![CDATA[<?php echo $v['post_headline']; ?>]]></title>
		<link><?php echo uri::scheme() . $blog_url . "." . uri::host(); ?>/post/<?php echo $v['post_id']; ?>/<?php echo $v['date_year']; ?>/<?php echo $v['date_month']; ?>/<?php echo $v['date_day']; ?>/<?php echo $v['post_url']; ?></link>
		<guid><?php echo uri::scheme() . $blog_url . "." . uri::host(); ?>/post/<?php echo $v['post_id']; ?>/<?php echo $v['date_year']; ?>/<?php echo $v['date_month']; ?>/<?php echo $v['date_day']; ?>/<?php echo $v['post_url']; ?></guid>
		<description><![CDATA[<?php echo $v['post_content']; ?>]]></description>
		<pubDate><?php echo date("D, d M Y H:i:s T", strtotime($v['post_date'])); ?></pubDate>
	</item>
<?php endforeach; ?>
  </channel>
</rss>