#!/bin/bash

# Define the filename using the year_month_day_hour_minute format
# Use $(date +"%Y_%m_%d_%H_%M") to generate the timestamp
TIMESTAMP=$(date +"%Y_%m_%d_%H_%M")
FILENAME="db_backup_${TIMESTAMP}.sql"

# Run the WP-CLI command to export the database
# --add-drop-table is recommended for easier restoration later
wp db export "$FILENAME" --add-drop-table

# Optional: Print a success message
echo "Database exported successfully as: $FILENAME"