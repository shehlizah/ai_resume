#!/usr/bin/env python3
# -*- coding: utf-8 -*-

content = open('resources/views/frontend/pages/home.blade.php', 'r', encoding='utf-8').read()

# Replace Build CV icon (📝 to 📄)
content = content.replace('<div class="upload-icon">📝</div>\n                <h3>Build CV</h3>', '<div class="upload-icon">📄</div>\n                <h3>Build CV</h3>')

# Replace Find Jobs icon (🔍 to 🎯)
content = content.replace('<div class="upload-icon">🔍</div>\n                <h3>Find Jobs</h3>', '<div class="upload-icon">🎯</div>\n                <h3>Find Jobs</h3>')

open('resources/views/frontend/pages/home.blade.php', 'w', encoding='utf-8').write(content)
print('Icons updated successfully')
