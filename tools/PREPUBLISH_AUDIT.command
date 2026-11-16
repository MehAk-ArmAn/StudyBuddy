#!/bin/zsh

set -u
set -o pipefail

export PATH="/opt/homebrew/bin:/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin:$PATH"

PROJECT="/Users/mehakarman/Desktop/StudyBuddy"
BASE_URL="http://127.0.0.1:8000"
REPORT="/tmp/studybuddy-prepublish-report.txt"
ROUTES_JSON="/tmp/studybuddy-routes.json"
SERVER_LOG="/tmp/studybuddy-prepublish-server.log"

FAILURES=0
WARNINGS=0

cd "$PROJECT" || exit 1

exec > >(tee "$REPORT") 2>&1

pass() {
    echo "PASS: $1"
}

fail() {
    echo "FAIL: $1"
    FAILURES=$((FAILURES + 1))
}

warn() {
    echo "WARNING: $1"
    WARNINGS=$((WARNINGS + 1))
}

section() {
    echo ""
    echo "================================================"
    echo "$1"
    echo "================================================"
}

env_value() {
    local key="$1"

    grep -E "^${key}=" .env 2>/dev/null \
        | tail -1 \
        | cut -d= -f2- \
        | sed -E 's/^["'\'']//; s/["'\'']$//'
}

section "1. REQUIRED PROJECT FILES"

REQUIRED_FILES=(
    "artisan"
    ".env"
    "composer.json"
    "composer.lock"
    "vendor/autoload.php"
    "routes/web.php"
    "resources/views/layouts/app.blade.php"
    "resources/views/home.blade.php"
    "resources/views/studybuddy/final/apps.blade.php"
    "resources/views/studybuddy/info/roles.blade.php"
    "public/index.php"
)

for FILE in "${REQUIRED_FILES[@]}"
do
    if [ -e "$FILE" ]; then
        pass "$FILE exists"
    else
        fail "$FILE is missing"
    fi
done

if [ -e "public/.env" ]; then
    fail "A public/.env file exists and could expose secrets"
else
    pass "No public/.env exposure"
fi

section "2. PHP SYNTAX"

PHP_FAILED=0

while IFS= read -r -d '' FILE
do
    RESULT=$(php -l "$FILE" 2>&1)

    if [ $? -ne 0 ]; then
        echo "$RESULT"
        PHP_FAILED=1
    fi
done < <(
    find \
        app \
        routes \
        database \
        tools \
        -type f \
        -name "*.php" \
        -print0
)

if [ "$PHP_FAILED" -eq 0 ]; then
    pass "All project PHP files passed syntax validation"
else
    fail "One or more PHP files contain syntax errors"
fi

section "3. JAVASCRIPT SYNTAX"

JS_FAILED=0

while IFS= read -r -d '' FILE
do
    RESULT=$(node --check "$FILE" 2>&1)

    if [ $? -ne 0 ]; then
        echo "$FILE"
        echo "$RESULT"
        JS_FAILED=1
    fi
done < <(
    find public/assets/js \
        -type f \
        -name "*.js" \
        -print0
)

for FILE in \
    public/studybuddy-sw.js \
    public/service-worker.js
do
    if [ -f "$FILE" ]; then
        RESULT=$(node --check "$FILE" 2>&1)

        if [ $? -ne 0 ]; then
            echo "$FILE"
            echo "$RESULT"
            JS_FAILED=1
        fi
    fi
done

if [ "$JS_FAILED" -eq 0 ]; then
    pass "All StudyBuddy JavaScript files passed syntax validation"
else
    fail "One or more JavaScript files contain syntax errors"
fi

section "4. BLADE REFERENCES, ROUTES AND ASSETS"

if php artisan route:list --json > "$ROUTES_JSON"; then
    pass "Laravel generated the complete route list"
else
    fail "Laravel could not generate the route list"
fi

python3 - "$ROUTES_JSON" <<'PY'
from pathlib import Path
from collections import Counter
import json
import re
import sys

routes_path = Path(sys.argv[1])
views_root = Path("resources/views")
public_root = Path("public")

errors = []
warnings = []

if not routes_path.exists():
    errors.append("Route JSON was not generated.")
    routes = []
else:
    routes = json.loads(routes_path.read_text())

route_names = {
    route.get("name")
    for route in routes
    if route.get("name")
}

duplicate_names = [
    name
    for name, count in Counter(
        route.get("name")
        for route in routes
        if route.get("name")
    ).items()
    if count > 1
]

if duplicate_names:
    errors.append(
        "Duplicate route names: "
        + ", ".join(sorted(duplicate_names))
    )

signatures = []

for route in routes:
    methods = route.get("method", "").split("|")
    uri = route.get("uri", "")

    for method in methods:
        if method != "HEAD":
            signatures.append((method, uri))

duplicate_signatures = [
    f"{method} {uri}"
    for (method, uri), count
    in Counter(signatures).items()
    if count > 1
]

if duplicate_signatures:
    errors.append(
        "Duplicate route method/URI combinations: "
        + ", ".join(sorted(duplicate_signatures))
    )

route_pattern = re.compile(
    r"""route\(\s*['"]([^'"]+)['"]"""
)

include_pattern = re.compile(
    r"""@include\(\s*['"]([^'"]+)['"]\s*\)"""
)

extends_pattern = re.compile(
    r"""@extends\(\s*['"]([^'"]+)['"]\s*\)"""
)

asset_pattern = re.compile(
    r"""asset\(\s*['"]([^'"]+)['"]\s*\)"""
)

template_pattern = re.compile(
    r"""(?i)\b(
        lorem\s+ipsum
        |replace\s+this\s+text
        |your\s+company\s+name
        |template\s+placeholder
        |dummy\s+content
        |sample\s+content
        |todo:
        |fixme:
    )\b""",
    re.X,
)

emoji_pattern = re.compile(
    "["
    "\U0001F300-\U0001FAFF"
    "\u2600-\u27BF"
    "\uFE0F"
    "]"
)

for path in views_root.rglob("*.blade.php"):
    text = path.read_text(errors="ignore")

    for route_name in route_pattern.findall(text):
        if route_name not in route_names:
            errors.append(
                f"{path}: missing route name '{route_name}'"
            )

    for view_name in (
        include_pattern.findall(text)
        + extends_pattern.findall(text)
    ):
        view_path = (
            views_root
            / (
                view_name.replace(".", "/")
                + ".blade.php"
            )
        )

        if not view_path.exists():
            errors.append(
                f"{path}: missing required view '{view_name}'"
            )

    for asset_path in asset_pattern.findall(text):
        if (
            asset_path.startswith(("http://", "https://"))
            or "{{" in asset_path
            or "$" in asset_path
        ):
            continue

        candidate = public_root / asset_path.lstrip("/")

        if not candidate.exists():
            warnings.append(
                f"{path}: local asset not found: {asset_path}"
            )

    for number, line in enumerate(
        text.splitlines(),
        start=1
    ):
        if template_pattern.search(line):
            warnings.append(
                f"{path}:{number}: possible template text"
            )

        if emoji_pattern.search(line):
            warnings.append(
                f"{path}:{number}: visible emoji detected"
            )

for message in errors:
    print("ERROR:", message)

for message in warnings[:100]:
    print("WARNING:", message)

if len(warnings) > 100:
    print(
        "WARNING:",
        len(warnings) - 100,
        "additional warnings were omitted."
    )

print(f"PY_ERRORS={len(errors)}")
print(f"PY_WARNINGS={len(warnings)}")

if errors:
    sys.exit(2)
PY

REFERENCE_STATUS=$?

if [ "$REFERENCE_STATUS" -eq 0 ]; then
    pass "Blade views, route names and required includes are valid"
else
    fail "Blade, route or required-view references contain errors"
fi

section "5. DATABASE CONNECTION AND MIGRATIONS"

MYSQLADMIN="/Applications/XAMPP/xamppfiles/bin/mysqladmin"

DB_HOST="$(env_value DB_HOST)"
DB_PORT="$(env_value DB_PORT)"
DB_DATABASE="$(env_value DB_DATABASE)"
DB_USERNAME="$(env_value DB_USERNAME)"

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_USERNAME="${DB_USERNAME:-root}"

if [ -x "$MYSQLADMIN" ]; then
    if "$MYSQLADMIN" \
        --protocol=tcp \
        --host="$DB_HOST" \
        --port="$DB_PORT" \
        --user="$DB_USERNAME" \
        ping \
        >/dev/null 2>&1
    then
        pass "XAMPP MySQL is responding"
    else
        fail "XAMPP MySQL is not responding"
    fi
else
    fail "XAMPP mysqladmin was not found"
fi

if MIGRATION_OUTPUT=$(php artisan migrate:status 2>&1); then
    echo "$MIGRATION_OUTPUT"

    if echo "$MIGRATION_OUTPUT" \
        | grep -q "Pending"
    then
        fail "One or more database migrations are pending"
    else
        pass "All database migrations have run"
    fi
else
    echo "$MIGRATION_OUTPUT"
    fail "Laravel could not read migration status"
fi

section "6. STORAGE AND WRITABLE DIRECTORIES"

for DIRECTORY in \
    storage \
    storage/framework \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache
do
    if [ -d "$DIRECTORY" ] && [ -w "$DIRECTORY" ]; then
        pass "$DIRECTORY is writable"
    else
        fail "$DIRECTORY is missing or not writable"
    fi
done

if [ -L "public/storage" ]; then
    pass "public/storage is linked"
else
    warn "public/storage is not currently linked"
fi

section "7. BLADE COMPILATION AND OPTIMIZATION"

if php artisan view:clear \
    && php artisan view:cache
then
    pass "All Blade templates compiled successfully"
else
    fail "Blade template compilation failed"
fi

php artisan view:clear >/dev/null 2>&1 || true

if php artisan optimize; then
    pass "Laravel production optimization completed"
else
    fail "Laravel production optimization failed"
fi

section "8. START LOCAL SERVER"

if ! curl \
    --silent \
    --fail \
    --max-time 4 \
    "$BASE_URL/" \
    >/dev/null
then
    pkill -f \
        "php artisan serve --host=127.0.0.1 --port=8000" \
        2>/dev/null \
        || true

    nohup php artisan serve \
        --host=127.0.0.1 \
        --port=8000 \
        > "$SERVER_LOG" \
        2>&1 &

    echo $! > /tmp/studybuddy-prepublish.pid

    for ATTEMPT in {1..15}
    do
        if curl \
            --silent \
            --fail \
            --max-time 3 \
            "$BASE_URL/" \
            >/dev/null
        then
            break
        fi

        sleep 1
    done
fi

if curl \
    --silent \
    --fail \
    --max-time 5 \
    "$BASE_URL/" \
    >/dev/null
then
    pass "StudyBuddy local server is responding"
else
    tail -120 "$SERVER_LOG" 2>/dev/null || true
    fail "StudyBuddy local server did not start"
fi

section "9. PUBLIC PAGE SMOKE TESTS"

check_status() {
    local url_path="$1"
    local expected="$2"
    local code

    code=$(
        curl \
            --silent \
            --output /tmp/studybuddy-http-body.html \
            --write-out "%{http_code}" \
            --max-time 20 \
            "$BASE_URL$url_path"
    )

    if [ "$code" = "$expected" ]; then
        pass "$url_path returned HTTP $code"
    else
        echo "Expected: $expected"
        echo "Received: $code"
        head -40 /tmp/studybuddy-http-body.html
        fail "$url_path returned an unexpected status"
    fi
}

check_status "/" "200"
check_status "/apps" "200"
check_status "/roles" "200"
check_status "/login" "200"
check_status "/register" "200"
check_status "/logout" "200"
check_status "/studybuddy-final-missing-page-test" "404"

section "10. INTERNAL LINKS AND LOCAL ASSETS"

python3 - "$BASE_URL" <<'PY'
from html.parser import HTMLParser
from urllib.parse import urljoin, urlparse
from urllib.request import (
    Request,
    build_opener,
    HTTPRedirectHandler,
)
from urllib.error import HTTPError, URLError
import sys

base = sys.argv[1]
origin = urlparse(base).netloc

seeds = [
    "/",
    "/apps",
    "/roles",
    "/login",
    "/register",
    "/logout",
]

class Parser(HTMLParser):
    def __init__(self):
        super().__init__()
        self.urls = set()

    def handle_starttag(self, tag, attrs):
        values = dict(attrs)

        for key in ("href", "src"):
            value = values.get(key)

            if not value:
                continue

            if value.startswith(
                (
                    "#",
                    "mailto:",
                    "tel:",
                    "javascript:",
                    "data:",
                    "blob:",
                )
            ):
                continue

            absolute = urljoin(base, value)
            parsed = urlparse(absolute)

            if parsed.netloc == origin:
                clean = parsed._replace(
                    fragment=""
                ).geturl()

                self.urls.add(clean)

opener = build_opener(HTTPRedirectHandler())
all_urls = set()
errors = []

for seed in seeds:
    url = urljoin(base, seed)

    try:
        request = Request(
            url,
            headers={
                "User-Agent":
                    "StudyBuddy Prepublish Audit"
            },
        )

        with opener.open(
            request,
            timeout=20,
        ) as response:
            content_type = response.headers.get(
                "Content-Type",
                "",
            )

            body = response.read()

            if "text/html" in content_type:
                parser = Parser()
                parser.feed(
                    body.decode(
                        "utf-8",
                        errors="ignore",
                    )
                )
                all_urls.update(parser.urls)

    except Exception as exception:
        errors.append(
            f"{url}: {exception}"
        )

for url in sorted(all_urls)[:400]:
    try:
        request = Request(
            url,
            headers={
                "User-Agent":
                    "StudyBuddy Prepublish Audit"
            },
        )

        with opener.open(
            request,
            timeout=20,
        ) as response:
            status = response.status

            if status >= 400:
                errors.append(
                    f"{url}: HTTP {status}"
                )

    except HTTPError as exception:
        errors.append(
            f"{url}: HTTP {exception.code}"
        )

    except URLError as exception:
        errors.append(
            f"{url}: {exception.reason}"
        )

    except Exception as exception:
        errors.append(
            f"{url}: {exception}"
        )

for error in errors:
    print("BROKEN:", error)

print(f"Checked {len(all_urls)} internal links and assets.")
print(f"BROKEN_COUNT={len(errors)}")

if errors:
    sys.exit(2)
PY

if [ $? -eq 0 ]; then
    pass "Rendered internal links and local assets are reachable"
else
    fail "Broken rendered links or local assets were found"
fi

section "11. REAL MAILING-LIST SUBMISSION"

COOKIE_FILE="/tmp/studybuddy-newsletter-cookies.txt"
HOME_HTML="/tmp/studybuddy-newsletter-home.html"
TOKEN_FILE="/tmp/studybuddy-newsletter-token.txt"
TEST_EMAIL="prepublish-test-$(date +%s)@example.com"

curl \
    --silent \
    --cookie-jar "$COOKIE_FILE" \
    "$BASE_URL/" \
    > "$HOME_HTML"

python3 - "$HOME_HTML" "$TOKEN_FILE" <<'PY'
from pathlib import Path
import html
import re
import sys

page = Path(sys.argv[1]).read_text(
    errors="ignore"
)

form = re.search(
    r"""<form
        [^>]*
        action=["'][^"']*/updates/subscribe["']
        [^>]*
    >
        (.*?)
    </form>""",
    page,
    flags=re.I | re.S | re.X,
)

if not form:
    raise SystemExit(
        "The updates subscription form was not found."
    )

token = re.search(
    r"""name=["']_token["']
        [^>]*
        value=["']([^"']+)["']
        |
        value=["']([^"']+)["']
        [^>]*
        name=["']_token["']
    """,
    form.group(1),
    flags=re.I | re.S | re.X,
)

if not token:
    raise SystemExit(
        "The newsletter CSRF token was not found."
    )

value = token.group(1) or token.group(2)

Path(sys.argv[2]).write_text(
    html.unescape(value)
)
PY

if [ $? -ne 0 ]; then
    fail "Newsletter form or CSRF token is missing"
else
    TOKEN="$(cat "$TOKEN_FILE")"

    NEWSLETTER_STATUS=$(
        curl \
            --silent \
            --output /tmp/studybuddy-newsletter-response.html \
            --write-out "%{http_code}" \
            --cookie "$COOKIE_FILE" \
            --cookie-jar "$COOKIE_FILE" \
            --request POST \
            --data-urlencode "_token=$TOKEN" \
            --data-urlencode "email=$TEST_EMAIL" \
            --data-urlencode "website=" \
            "$BASE_URL/updates/subscribe"
    )

    if [ "$NEWSLETTER_STATUS" = "302" ]; then
        pass "Updates form accepted a real subscription"
    else
        cat /tmp/studybuddy-newsletter-response.html
        fail "Updates form returned HTTP $NEWSLETTER_STATUS"
    fi

    MYSQL="/Applications/XAMPP/xamppfiles/bin/mysql"
    DB_PASSWORD="$(env_value DB_PASSWORD)"

    MYSQL_ARGS=(
        --protocol=tcp
        --host="$DB_HOST"
        --port="$DB_PORT"
        --user="$DB_USERNAME"
        "$DB_DATABASE"
    )

    if [ -n "$DB_PASSWORD" ]; then
        MYSQL_ARGS=(
            --protocol=tcp
            --host="$DB_HOST"
            --port="$DB_PORT"
            --user="$DB_USERNAME"
            "--password=$DB_PASSWORD"
            "$DB_DATABASE"
        )
    fi

    SAVED_COUNT=$(
        "$MYSQL" "${MYSQL_ARGS[@]}" \
            --batch \
            --skip-column-names \
            --execute="
                SELECT COUNT(*)
                FROM studybuddy_mailing_list_subscribers
                WHERE email = '$TEST_EMAIL';
            " \
            2>/dev/null
    )

    if [ "$SAVED_COUNT" = "1" ]; then
        pass "Subscription was saved in the Admin mailing-list table"

        "$MYSQL" "${MYSQL_ARGS[@]}" \
            --execute="
                DELETE
                FROM studybuddy_mailing_list_subscribers
                WHERE email = '$TEST_EMAIL';
            " \
            >/dev/null 2>&1
    else
        fail "Subscription was not saved in the database"
    fi
fi

section "12. PRODUCTION ENVIRONMENT SAFETY"

APP_ENV_VALUE="$(env_value APP_ENV)"
APP_DEBUG_VALUE="$(env_value APP_DEBUG)"
APP_URL_VALUE="$(env_value APP_URL)"
APP_KEY_VALUE="$(env_value APP_KEY)"

if [ -n "$APP_KEY_VALUE" ]; then
    pass "APP_KEY is configured"
else
    fail "APP_KEY is missing"
fi

if [ "$APP_ENV_VALUE" = "production" ]; then
    if [ "$APP_DEBUG_VALUE" = "false" ]; then
        pass "APP_DEBUG is false in production"
    else
        fail "APP_DEBUG must be false in production"
    fi

    if [[ "$APP_URL_VALUE" == https://* ]]; then
        pass "Production APP_URL uses HTTPS"
    else
        fail "Production APP_URL must use HTTPS"
    fi

    if [ "$(env_value SESSION_SECURE_COOKIE)" = "true" ]; then
        pass "Production session cookies require HTTPS"
    else
        fail "SESSION_SECURE_COOKIE must be true in production"
    fi
else
    echo "INFO: Local environment detected. Production values will be checked after live deployment."
fi

section "13. SECURITY HEADER CHECK"

HEADERS=$(
    curl \
        --silent \
        --dump-header - \
        --output /dev/null \
        "$BASE_URL/"
)

for HEADER in \
    "X-Content-Type-Options" \
    "Referrer-Policy" \
    "X-Frame-Options"
do
    if echo "$HEADERS" \
        | grep -iq "^${HEADER}:"
    then
        pass "$HEADER is present"
    else
        warn "$HEADER is not currently present"
    fi
done

section "14. RECENT APPLICATION ERRORS"

if [ -f "storage/logs/laravel.log" ]; then
    RECENT_ERRORS=$(
        tail -1000 storage/logs/laravel.log \
            | grep -cE \
                "\.(ERROR|CRITICAL|ALERT|EMERGENCY):" \
            || true
    )

    if [ "$RECENT_ERRORS" -eq 0 ]; then
        pass "No errors in the latest Laravel log entries"
    else
        warn "$RECENT_ERRORS recent Laravel error entries exist"

        tail -1000 storage/logs/laravel.log \
            | grep -E \
                "\.(ERROR|CRITICAL|ALERT|EMERGENCY):" \
            | tail -20
    fi
else
    pass "No Laravel error log exists yet"
fi

section "FINAL RESULT"

echo "Failures: $FAILURES"
echo "Warnings: $WARNINGS"
echo "Report: $REPORT"

if [ "$FAILURES" -gt 0 ]; then
    echo ""
    echo "STUDYBUDDY IS NOT READY TO PUBLISH."
    echo "Fix every FAIL item before deployment."
    exit 1
fi

if [ "$WARNINGS" -gt 0 ]; then
    echo ""
    echo "CORE TESTS PASSED, BUT WARNINGS STILL NEED REVIEW."
    exit 2
fi

echo ""
echo "ALL PREPUBLISH CHECKS PASSED."
echo "STUDYBUDDY IS READY FOR THE LIVE-SERVER AUDIT."
