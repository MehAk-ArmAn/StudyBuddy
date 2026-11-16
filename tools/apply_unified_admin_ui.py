from pathlib import Path
import re
import sys

project = Path(
    "/Users/mehakarman/Desktop/StudyBuddy"
)

layout = project / (
    "resources/views/layouts/admin.blade.php"
)

if not layout.exists():
    raise SystemExit(
        "resources/views/layouts/admin.blade.php was not found."
    )

text = layout.read_text()

style_name = "studybuddy-admin-unified.css"
script_name = "studybuddy-admin-unified.js"

style_block = """
    @if(file_exists(public_path('assets/css/studybuddy-admin-unified.css')))
        <link
            rel="stylesheet"
            href="{{ asset('assets/css/studybuddy-admin-unified.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-admin-unified.css')) }}"
        >
    @endif
"""

script_block = """
    @if(file_exists(public_path('assets/js/studybuddy-admin-unified.js')))
        <script
            src="{{ asset('assets/js/studybuddy-admin-unified.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-admin-unified.js')) }}"
            defer
        ></script>
    @endif
"""

if style_name not in text:
    if "</head>" not in text:
        raise SystemExit(
            "The Admin layout has no closing head tag."
        )

    text = text.replace(
        "</head>",
        style_block + "\n</head>",
        1,
    )

if script_name not in text:
    if "</body>" not in text:
        raise SystemExit(
            "The Admin layout has no closing body tag."
        )

    text = text.replace(
        "</body>",
        script_block + "\n</body>",
        1,
    )

layout.write_text(text)

admin_views = project / "resources/views/admin"

changed_titles = 0

for blade in admin_views.rglob("*.blade.php"):
    source = blade.read_text(errors="ignore")

    updated = re.sub(
        r"@section\(\s*['\"]page_title['\"]\s*,",
        "@section('title',",
        source,
    )

    if updated != source:
        blade.write_text(updated)
        changed_titles += 1

required_views = [
    "resources/views/admin/studybuddy/final-platform/index.blade.php",
    "resources/views/admin/studybuddy/content-studio/index.blade.php",
    "resources/views/admin/homepage-cms/index.blade.php",
    "resources/views/admin/users/index.blade.php",
    "resources/views/admin/contact-messages/index.blade.php",
    "resources/views/admin/health/index.blade.php",
    "resources/views/admin/mailing-list/index.blade.php",
]

print("Unified Admin UI installed.")
print(f"Admin title sections normalized: {changed_titles}")
print("")
print("Important Admin views:")

missing = []

for relative in required_views:
    path = project / relative

    if path.exists():
        print(f"FOUND   {relative}")
    else:
        print(f"MISSING {relative}")
        missing.append(relative)

if missing:
    print("")
    print(
        "Some optional or renamed Admin views were not found. "
        "The shared UI still applies to every page using layouts.admin."
    )
