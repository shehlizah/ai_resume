with open('resources/views/frontend/pages/home.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Add gradient styles to the upload-icon divs
content = content.replace(
    '<div class="upload-icon">📄</div>\n                <h3>Build CV</h3>',
    '<div class="upload-icon" style="background: linear-gradient(135deg, #F97316, #EA580C);">📄</div>\n                <h3>Build CV</h3>'
)
content = content.replace(
    '<div class="upload-icon">🎯</div>\n                <h3>Find Jobs</h3>',
    '<div class="upload-icon" style="background: linear-gradient(135deg, #8B5CF6, #7C3AED);">🎯</div>\n                <h3>Find Jobs</h3>'
)

with open('resources/views/frontend/pages/home.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print('Styles added successfully')
