#!/bin/bash

# Directory to search (default: current directory, change if needed)
DIR="."

# Find all PHP files recursively, excluding vendor/ directory
echo "Finding PHP files..."
FILES=$(find "$DIR" -type f -name "*.php" ! -path "*/vendor/*")

# Check if any PHP files were found
if [ -z "$FILES" ]; then
  echo "No PHP files found in $DIR (excluding vendor/)."
  exit 1
fi

# Count total files for progress reporting
TOTAL=$(echo "$FILES" | wc -l)
echo "Found $TOTAL PHP files to process."

# Counter for processed files
COUNT=0
UPDATED=0

# Create a Python script file for processing
PYTHON_SCRIPT=$(mktemp)
cat > "$PYTHON_SCRIPT" << 'EOF'
#!/usr/bin/env python3
import sys, re
from collections import Counter

# Replace getenv with $_ENV
def convert_getenv(line):
    return re.sub(r"getenv\(['\"]([^'\"]+)['\"]\)", r"$_ENV['\1']", line)

# Detect indentation style in the file
def detect_indentation(lines):
    indent_counts = Counter()
    
    for line in lines:
        if line.strip() == "":  # Skip empty lines
            continue
            
        # Count leading spaces
        leading_spaces = len(line) - len(line.lstrip(' '))
        
        if leading_spaces > 0:
            # Check for common indentation patterns
            if leading_spaces % 2 == 0:
                indent_counts[2] += 1
            if leading_spaces % 4 == 0:
                indent_counts[4] += 1
            if leading_spaces % 8 == 0:
                indent_counts[8] += 1
    
    # Determine most likely indentation
    if not indent_counts:
        return "unknown"
    
    # If we have significantly more 4-space patterns than 2-space only
    if indent_counts[4] > indent_counts[2] * 0.7:
        return "4-space"
    elif indent_counts[2] > 0:
        return "2-space"
    elif indent_counts[8] > 0:
        return "8-space"
    else:
        return "mixed"

# Convert indentation from 4-space to 2-space
def convert_indent(line):
    # Don't process empty lines or lines with no leading whitespace
    if not line or line.lstrip() == line:
        return line
    
    # Count leading spaces (only spaces, not tabs)
    leading_spaces = len(line) - len(line.lstrip(' '))
    
    # Only convert if we have leading spaces
    if leading_spaces > 0:
        # Convert 4-space indentation to 2-space
        # This handles any multiple of 4 spaces
        new_spaces = (leading_spaces // 4) * 2 + (leading_spaces % 4)
        return ' ' * new_spaces + line.lstrip(' ')
    
    return line

# Remove whitespace from empty lines
def clean_empty_lines(line):
    if line.strip() == "":
        return "\n"
    return line

# Main processing
try:
    with open(sys.argv[1], "r", encoding='utf-8') as infile:
        lines = infile.readlines()
    
    # Detect current indentation
    indent_style = detect_indentation(lines)
    
    # If second argument is "detect", just output the detection result
    if len(sys.argv) > 2 and sys.argv[2] == "detect":
        print(indent_style)
        sys.exit(0)
    
    # If already 2-space, output "already-2-space" and exit
    if indent_style == "2-space":
        print("already-2-space")
        sys.exit(0)
    
    # Process the file
    processed_lines = []
    for line in lines:
        # Apply all transformations
        line = convert_getenv(line)
        line = convert_indent(line)
        line = clean_empty_lines(line)
        processed_lines.append(line)
    
    with open(sys.argv[2], "w", encoding='utf-8') as outfile:
        outfile.writelines(processed_lines)
    
    print(f"converted-from-{indent_style}")

except Exception as e:
    print(f"Error processing file: {e}")
    sys.exit(1)
EOF

# Add option to just detect indentation without converting
if [ "$1" = "--detect-only" ]; then
  echo "Detecting indentation styles..."
  while IFS= read -r FILE; do
    [ ! -f "$FILE" ] && continue
    ((COUNT++))
    RESULT=$(python3 "$PYTHON_SCRIPT" "$FILE" detect)
    echo "[$COUNT/$TOTAL] $FILE: $RESULT"
  done <<< "$FILES"
  rm -f "$PYTHON_SCRIPT"
  exit 0
fi

# Process each PHP file
SKIPPED=0
while IFS= read -r FILE; do
  # Skip if not a regular file
  [ ! -f "$FILE" ] && continue

  # Update counter and show progress
  ((COUNT++))
  echo "[$COUNT/$TOTAL] Processing: $FILE"

  # Create a temporary file
  TEMP_FILE=$(mktemp)

  # Process the file using our Python script
  RESULT=$(python3 "$PYTHON_SCRIPT" "$FILE" "$TEMP_FILE" 2>&1)
  
  case "$RESULT" in
    "already-2-space")
      echo "  - Already using 2-space indentation, skipping"
      ((SKIPPED++))
      ;;
    "converted-from-"*)
      if [ -f "$TEMP_FILE" ] && ! cmp -s "$FILE" "$TEMP_FILE"; then
        cp "$TEMP_FILE" "$FILE"
        echo "  ✓ Updated (${RESULT#converted-from-})"
        ((UPDATED++))
      else
        echo "  - No changes needed"
      fi
      ;;
    *)
      echo "  ✗ Error: $RESULT"
      ;;
  esac

  # Always clean up the temp file
  rm -f "$TEMP_FILE"

  # Add a short pause to prevent system overload
  sleep 0.1

done <<< "$FILES"

# Clean up the Python script
rm -f "$PYTHON_SCRIPT"

echo "Processing complete. $UPDATED of $COUNT files updated, $SKIPPED already using 2-space indentation."
