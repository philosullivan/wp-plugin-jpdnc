#!/bin/bash

echo "🚀 Starting JPNDC Comment Purge and Disable..."

# 1. Disable comments and pings on future posts
wp option update default_comment_status closed
wp option update default_ping_status closed
wp option update default_comment_status 0

# 2. Close comments on all existing posts/pages/attachments
wp post list --post_type=post,page,attachment --format=ids | xargs wp post update --comment_status=closed --ping_status=closed

# 3. Nuclear delete of all existing comment data
wp db query "DELETE FROM $(wp db prefix)comments; DELETE FROM $(wp db prefix)commentmeta;"

# 4. Reset comment counts on all posts
wp db query "DELETE FROM $(wp db prefix)comments;" --socket=/Users/phil/Library/Application\ Support/Local/run/XXXXX/mysql.sock
#wp db query "UPDATE $(wp db prefix)posts SET comment_count = 0;"

wp widget list --fields=id,name | grep "Recent Comments" | awk '{print $1}' | xargs wp widget delete

# 5. Flush object cache and Avada caches
wp cache flush
wp eval "if(function_exists('fusion_component_cache')){fusion_component_cache()->clear_cache();}"

echo "✅ Site cleaned! Comments disabled and database purged."