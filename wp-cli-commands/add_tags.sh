#!/bin/bash

# Define the array of tags for the JPNDC project
tags=(
    "Affordable Housing"
    "Community Development"
    "Jamaica Plain"
    "Economic Justice"
    "Real Estate"
    "Youth Programs"
)

echo "🚀 Starting tag import..."

# Iterate through the list
for tag in "${tags[@]}"; do
    # --idempotent prevents errors if the tag already exists
    wp term create post_tag "$tag" --idempotent
done

echo "✅ Tag import complete."