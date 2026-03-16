#!/bin/bash

echo "Starting cache purge for JPNDC..."

# 1. Flush the WordPress Object Cache (Redis/Memcached)
echo "Cleaning Object Cache..."
wp cache flush

# 2. Reset Avada Dynamic CSS (Forces recompilation of stylesheets)
echo "Resetting Avada Dynamic CSS..."
wp eval "if(class_exists('Fusion_Dynamic_CSS')){Fusion_Dynamic_CSS::get_instance()->reset_all_caches();}"

# 3. Clear Fusion Component Cache (Clears element-specific HTML/logic)
echo "Clearing Fusion Component Cache..."
wp eval "if(function_exists('fusion_component_cache')){fusion_component_cache()->clear_cache();}"

echo "✅ All caches cleared successfully."