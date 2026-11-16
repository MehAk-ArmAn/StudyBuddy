#!/bin/zsh
set -u

PROJECT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
PORT="${1:-8000}"
PID_FILE="$PROJECT_DIR/storage/logs/studybuddy-local-server.pid"

PID=""
if [ -f "$PID_FILE" ]; then
  PID="$(cat "$PID_FILE" 2>/dev/null || true)"
fi

if [ -n "$PID" ] && kill -0 "$PID" 2>/dev/null; then
  kill "$PID" 2>/dev/null || true
else
  PID="$(lsof -tiTCP:$PORT -sTCP:LISTEN 2>/dev/null | head -n 1 || true)"
  if [ -n "$PID" ]; then
    kill "$PID" 2>/dev/null || true
  fi
fi

rm -f "$PID_FILE"
echo "StudyBuddy local server stopped."
