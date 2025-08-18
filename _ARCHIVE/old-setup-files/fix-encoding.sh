#!/bin/bash

# Script to fix encoding issues in all PHP files

echo "Fixing encoding issues in PHP files..."

# Find all PHP files
find /root/culture-radar -name "*.php" -type f | while read file; do
    echo "Processing: $file"
    
    # Convert to UTF-8 if needed
    iconv -f UTF-8 -t UTF-8//IGNORE "$file" -o "${file}.tmp" 2>/dev/null && mv "${file}.tmp" "$file"
    
    # Fix common character issues
    sed -i 's/ï¿½/?/g' "$file"
    sed -i 's/Ã©/é/g' "$file"
    sed -i 's/Ã¨/è/g' "$file"
    sed -i 's/Ã /à/g' "$file"
    sed -i 's/Ãª/ê/g' "$file"
    sed -i 's/Ã§/ç/g' "$file"
    sed -i 's/Ã®/î/g' "$file"
    sed -i 's/Ã¢/â/g' "$file"
    sed -i 's/Ã´/ô/g' "$file"
    sed -i 's/Ã¹/ù/g' "$file"
    sed -i 's/Ãˆ/È/g' "$file"
    sed -i 's/Ã‰/É/g' "$file"
    sed -i 's/Ã€/À/g' "$file"
    sed -i 's/Ã¯/ï/g' "$file"
    sed -i 's/Å"/œ/g' "$file"
    sed -i 's/Ã/É/g' "$file"
    sed -i 's/â€™/'"'"'/g' "$file"
    sed -i 's/â€œ/"/g' "$file"
    sed -i 's/â€/"/g' "$file"
    sed -i 's/â€¢/•/g' "$file"
    sed -i 's/â€"/—/g' "$file"
    sed -i 's/â€"/–/g' "$file"
    sed -i 's/Ã¼/ü/g' "$file"
    sed -i 's/Ã¶/ö/g' "$file"
    sed -i 's/Ã¤/ä/g' "$file"
    sed -i 's/Ã‹/Ë/g' "$file"
    sed -i 's/Ã"/Ô/g' "$file"
    sed -i 's/Ãœ/Ü/g' "$file"
    sed -i 's/Ã–/Ö/g' "$file"
    sed -i 's/Ã„/Ä/g' "$file"
    
    # Fix special character icons
    sed -i 's/<µ/🎵/g' "$file"
    sed -i 's/<¨/🎨/g' "$file"
    sed -i 's/<­/🎭/g' "$file"
    sed -i 's/<¤/🎤/g' "$file"
    sed -i 's/<¬/🎬/g' "$file"
    sed -i 's/<ª/🎪/g' "$file"
    sed -i 's/<¼/🎼/g' "$file"
    sed -i 's/<¯/📡/g' "$file"
    sed -i 's/=¼/🖼️/g' "$file"
    sed -i 's/=Å/📅/g' "$file"
    sed -i 's/=Ú/📚/g' "$file"
    sed -i 's/=ñ/📱/g' "$file"
    sed -i 's/=h=»/👨‍💻/g' "$file"
    sed -i 's/>\16/🤖/g' "$file"
    sed -i 's/P /⭐/g' "$file"
    
    # Fix euro symbol
    sed -i 's/¬/€/g' "$file"
    sed -i 's/â¬/€/g' "$file"
    
    # Fix bullet point
    sed -i 's/"/•/g' "$file"
done

echo "Encoding fixes completed!"