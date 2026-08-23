from pathlib import Path
from datetime import datetime
import re

stamp = datetime.now().strftime("%Y%m%d_%H%M%S")

views = [
    Path("resources/views/auth/login.blade.php"),
    Path("resources/views/auth/register.blade.php"),
]

error_block = r'''
            @if ($errors->any())
                <div class="sb-auth-error-summary" role="alert" aria-live="polite">
                    <div class="sb-auth-error-icon">!</div>
                    <div>
                        <strong>Almost there — fix these first:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
'''

for view in views:
    if not view.exists():
        print(f"skip missing {view}")
        continue

    text = view.read_text()
    view.with_suffix(view.suffix + f".bak_{stamp}").write_text(text)

    # Remove old duplicate summary blocks if this script is run again.
    text = re.sub(
        r"\s*@if \(\$errors->any\(\)\)\s*<div class=\"sb-auth-error-summary\".*?</div>\s*</div>\s*@endif",
        "",
        text,
        flags=re.S,
    )

    if "sb-auth-error-summary" not in text:
        text = text.replace("@csrf", "@csrf\n" + error_block, 1)

    view.write_text(text)
    print(f"✅ patched visible error summary in {view}")

css_path = Path("public/assets/css/sb-auth-role-ui.css")
if not css_path.exists():
    css_path.parent.mkdir(parents=True, exist_ok=True)
    css_path.write_text("")

css = css_path.read_text()
css_path.with_suffix(css_path.suffix + f".bak_{stamp}").write_text(css)

css = re.sub(
    r"/\* === StudyBuddy visible auth errors === \*/.*?/\* === End StudyBuddy visible auth errors === \*/",
    "",
    css,
    flags=re.S,
)

css += r'''

/* === StudyBuddy visible auth errors === */
.sb-auth-error-summary {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 12px;
    align-items: flex-start;
    border: 1px solid rgba(248, 113, 113, .55);
    border-radius: 22px;
    padding: 14px 16px;
    margin: 4px 0 6px;
    color: #fee2e2;
    background:
        radial-gradient(circle at 0% 0%, rgba(248, 113, 113, .20), transparent 34%),
        rgba(127, 29, 29, .30);
    box-shadow:
        0 0 0 4px rgba(248, 113, 113, .10),
        0 20px 44px rgba(127, 29, 29, .22);
}

.sb-auth-error-icon {
    width: 34px;
    height: 34px;
    display: grid;
    place-items: center;
    border-radius: 999px;
    color: #450a0a;
    background: #fecaca;
    font-weight: 950;
}

.sb-auth-error-summary strong {
    display: block;
    color: #ffffff;
    font-weight: 950;
    margin-bottom: 6px;
}

.sb-auth-error-summary ul {
    margin: 0;
    padding-left: 18px;
}

.sb-auth-error-summary li {
    margin: 4px 0;
    color: #fecaca;
    font-weight: 800;
    line-height: 1.45;
}

.sb-auth-form small {
    display: block;
    margin-top: 6px;
    border: 1px solid rgba(248, 113, 113, .42);
    border-radius: 14px;
    padding: 8px 10px;
    color: #fecaca !important;
    background: rgba(127, 29, 29, .22);
    font-size: .82rem;
    font-weight: 900;
    line-height: 1.35;
}

.sb-auth-form small::before {
    content: "⚠ ";
}

.sb-auth-form label:has(small) input,
.sb-auth-form label:has(small) select,
.sb-auth-form label:has(small) textarea {
    border-color: rgba(248, 113, 113, .85) !important;
    background: rgba(254, 226, 226, .96) !important;
    color: #450a0a !important;
    box-shadow:
        0 0 0 4px rgba(248, 113, 113, .16),
        0 14px 34px rgba(127, 29, 29, .16) !important;
}

.sb-auth-form input:invalid:not(:placeholder-shown),
.sb-auth-form textarea:invalid:not(:placeholder-shown) {
    border-color: rgba(251, 146, 60, .70);
}

.sb-auth-form input:focus,
.sb-auth-form select:focus,
.sb-auth-form textarea:focus {
    outline: none;
    border-color: rgba(34, 211, 238, .90) !important;
    box-shadow:
        0 0 0 4px rgba(34, 211, 238, .18),
        0 18px 42px rgba(2, 6, 23, .18) !important;
}

.sb-auth-form label:has(small) span {
    color: #fecaca !important;
}

.sb-auth-status,
.sb-auth-alert.good {
    border: 1px solid rgba(34, 197, 94, .48);
    border-radius: 18px;
    padding: 12px 14px;
    color: #bbf7d0;
    background: rgba(20, 83, 45, .28);
    box-shadow: 0 0 0 4px rgba(34, 197, 94, .10);
    font-weight: 850;
}

@media (max-width: 620px) {
    .sb-auth-error-summary {
        grid-template-columns: 1fr;
    }

    .sb-auth-error-icon {
        width: 30px;
        height: 30px;
    }
}
/* === End StudyBuddy visible auth errors === */
'''

css_path.write_text(css)
print("✅ added visible Tailwind-style auth error CSS")
