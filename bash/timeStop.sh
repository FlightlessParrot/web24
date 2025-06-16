#!/bin/bash

LOG_FILE="$HOME/time_log.txt"

# Parse arguments
MESSAGE=""
while getopts ":m:" opt; do
  case $opt in
    m) MESSAGE="$OPTARG" ;;
    \?) echo "Invalid option: -$OPTARG" >&2; exit 1 ;;
    :) echo "Option -$OPTARG requires a message." >&2; exit 1 ;;
  esac
done

# Get timestamp
timestamp=$(date +"%Y-%m-%d %H:%M:%S")

# Log to file and show user
if [ -z "$MESSAGE" ]; then
  echo "[STOP]  $timestamp" | tee -a "$LOG_FILE"
else
  echo "[STOP]  $timestamp - $MESSAGE" | tee -a "$LOG_FILE"
fi
