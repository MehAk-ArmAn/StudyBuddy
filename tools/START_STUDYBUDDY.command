#!/bin/zsh

set -e

PROJECT="/Users/mehakarman/Desktop/StudyBuddy"
LOG_FILE="/tmp/studybuddy-server.log"
PID_FILE="/tmp/studybuddy-server.pid"
URL="http://127.0.0.1:8000"

cd "$PROJECT"

if [ ! -f artisan ]; then
    echo "StudyBuddy artisan file was not found."
    exit 1
fi

if [ ! -f vendor/autoload.php ]; then
    echo "Composer dependencies are missing."
    exit 1
fi

pkill -f \
    "php artisan serve --host=127.0.0.1 --port=8000" \
    2>/dev/null \
    || true

nohup php artisan serve \
    --host=127.0.0.1 \
    --port=8000 \
    > "$LOG_FILE" \
    2>&1 &

echo $! > "$PID_FILE"

for attempt in {1..20}; do
    if curl \
        --silent \
        --fail \
        --max-time 2 \
        "$URL" \
        >/dev/null
    then
        echo "StudyBuddy is running at $URL"
        open "$URL"
        exit 0
    fi

    sleep 1
done

echo "StudyBuddy did not start."
tail -100 "$LOG_FILE"
exit 1
