#!/bin/bash

# Define the array of categories for the JPNDC project
categories=(
    "Affordable Housing"
    "Community Development"
    "Economic Justice"
    "Youth & Families"
    "Real Estate Development"
    "Events"
)

echo "🚀 Starting category import for JPNDC..."

# Iterate through the list
for cat in "${categories[@]}"; do
    # --idempotent is key here; it skips if the category already exists
    wp term create category "$cat" --idempotent
done

echo "✅ Category import complete."