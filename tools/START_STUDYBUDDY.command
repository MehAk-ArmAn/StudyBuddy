#!/bin/zsh
set -u

PROJECT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
PORT="${1:-8000}"
HOST="127.0.0.1"
LOG_FILE="$PROJECT_DIR/storage/logs/studybuddy-local-server.log"
PID_FILE="$PROJECT_DIR/storage/logs/studybuddy-local-server.pid"
URL="http://$HOST:$PORT"

cd "$PROJECT_DIR" || exit 1
mkdir -p "$PROJECT_DIR/storage/logs"

if [ ! -f "$PROJECT_DIR/vendor/autoload.php" ]; then
  echo "StudyBuddy dependencies are missing."
  echo "Run: composer install"
  echo "Then double-click this launcher again."
  read "?Press Return to close..."
  exit 1
fi

RUNNING_PID="$(lsof -tiTCP:$PORT -sTCP:LISTEN 2>/dev/null | head -n 1 || true)"
if [ -n "$RUNNING_PID" ]; then
  echo "StudyBuddy is already running on $URL"
  open "$URL"
  exit 0
fi

php artisan optimize:clear >/dev/null 2>&1 || true
nohup php artisan serve --host="$HOST" --port="$PORT" > "$LOG_FILE" 2>&1 &
echo $! > "$PID_FILE"

for attempt in {1..30}; do
  if curl -fsS "$URL" >/dev/null 2>&1; then
    echo "StudyBuddy is running on $URL"
    open "$URL"
    exit 0
  fi
  sleep 0.35
done

echo "StudyBuddy did not start correctly."
echo "Open this log for the exact error:"
echo "$LOG_FILE"
open -a TextEdit "$LOG_FILE" 2>/dev/null || true
read "?Press Return to close..."
exit 1
