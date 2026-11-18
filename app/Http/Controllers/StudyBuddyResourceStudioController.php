<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\StudyBuddyResourceStudio as Studio;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StudyBuddyResourceStudioController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notes = Studio::notes($user);
        $files = Studio::files($user);
        $selected = Studio::find($notes, (string) $request->query('note', ''));
        return view('studybuddy.p10.studio', compact('user', 'notes', 'files', 'selected'));
    }

    public function createNote(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'min:2', 'max:100'],
            'body' => ['nullable', 'string', 'max:12000'],
        ]);
        $user = $request->user();
        $notes = Studio::notes($user);
        if (count($notes) >= Studio::NOTE_LIMIT) {
            throw ValidationException::withMessages(['title' => 'Your private library has reached the 25-note limit.']);
        }
        $id = (string) Str::uuid();
        array_unshift($notes, [
            'id' => $id, 'title' => trim($data['title']), 'body' => (string) ($data['body'] ?? ''),
            'created_at' => now()->toIso8601String(), 'updated_at' => now()->toIso8601String(),
        ]);
        Studio::persist($user, 'sb_p10_notes', $notes);
        return redirect()->route('studybuddy.p10.index', ['note' => $id])->with('status', 'Private note created.');
    }

    public function updateNote(Request $request, string $id)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'min:2', 'max:100'],
            'body' => ['nullable', 'string', 'max:12000'],
        ]);
        $user = $request->user();
        $notes = Studio::notes($user);
        abort_unless(Studio::find($notes, $id), 404);
        foreach ($notes as &$note) {
            if ($note['id'] !== $id) continue;
            $note['title'] = trim($data['title']);
            $note['body'] = (string) ($data['body'] ?? '');
            $note['updated_at'] = now()->toIso8601String();
            break;
        }
        unset($note);
        Studio::persist($user, 'sb_p10_notes', $notes);
        return redirect()->route('studybuddy.p10.index', ['note' => $id])->with('status', 'Changes saved to your account.');
    }

    public function deleteNote(Request $request, string $id)
    {
        $user = $request->user();
        $notes = Studio::notes($user);
        abort_unless(Studio::find($notes, $id), 404);
        $notes = array_values(array_filter($notes, fn ($n) => $n['id'] !== $id));
        Studio::persist($user, 'sb_p10_notes', $notes);
        return redirect()->route('studybuddy.p10.index')->with('status', 'Note deleted.');
    }

    public function upload(Request $request)
    {
        $request->validate(['upload' => ['required', 'file', 'max:8192']]);
        /** @var UploadedFile $upload */
        $upload = $request->file('upload');
        $extension = strtolower($upload->getClientOriginalExtension());
        $mime = (string) $upload->getMimeType();
        if (!Studio::allowedMime($extension, $mime)) {
            throw ValidationException::withMessages(['upload' => 'Allowed: PDF, text, Markdown, PNG, JPG, WebP, MP3, WAV or OGG with a matching file type.']);
        }
        $user = $request->user();
        $files = Studio::files($user);
        if (count($files) >= Studio::FILE_LIMIT) {
            throw ValidationException::withMessages(['upload' => 'You have reached the 30-file limit.']);
        }
        $used = array_sum(array_map(fn ($entry) => max(0, (int) ($entry['size'] ?? 0)), $files));
        if ($used + $upload->getSize() > Studio::FILE_BYTES_LIMIT) {
            throw ValidationException::withMessages(['upload' => 'This account has reached its 60 MB private file allowance.']);
        }
        $id = (string) Str::uuid();
        $path = Studio::storageDirectory($user).'/'.$id.'.'.$extension;
        $safeName = substr(preg_replace('/[^A-Za-z0-9._ -]/', '_', basename($upload->getClientOriginalName())), 0, 100);
        $safeName = $safeName !== '' ? $safeName : 'resource.'.$extension;
        $disk = Storage::disk('local');
        $stream = fopen($upload->getRealPath(), 'rb');
        if ($stream === false) abort(500, 'Could not open uploaded file.');
        try {
            $written = $disk->put($path, $stream);
        } finally {
            fclose($stream);
        }
        if (!$written) abort(500, 'Could not store the private resource.');
        array_unshift($files, [
            'id' => $id, 'name' => $safeName, 'path' => $path, 'mime' => $mime,
            'size' => $upload->getSize(), 'created_at' => now()->toIso8601String(),
        ]);
        try {
            Studio::persist($user, 'sb_p10_files', $files);
        } catch (\Throwable $error) {
            $disk->delete($path);
            throw $error;
        }
        return redirect()->route('studybuddy.p10.index')->with('status', 'File added to your private library.');
    }

    public function download(Request $request, string $id)
    {
        $file = $this->ownedFile($request->user(), $id);
        return Storage::disk('local')->download($file['path'], $file['name'], [
            'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function preview(Request $request, string $id): BinaryFileResponse
    {
        $file = $this->ownedFile($request->user(), $id);
        abort_unless(Studio::fileType($file) !== 'document', 404);
        $path = Storage::disk('local')->path($file['path']);
        return response()->file($path, [
            'Content-Type' => $file['mime'], 'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
        ]);
    }

    public function deleteFile(Request $request, string $id)
    {
        $user = $request->user();
        $file = $this->ownedFile($user, $id);
        $files = array_values(array_filter(Studio::files($user), fn ($f) => $f['id'] !== $id));
        // Remove the catalog entry first, preventing repeat access if the disk delete fails.
        Studio::persist($user, 'sb_p10_files', $files);
        Storage::disk('local')->delete($file['path']);
        return redirect()->route('studybuddy.p10.index')->with('status', 'Private file removed.');
    }

    private function ownedFile(User $user, string $id): array
    {
        $file = Studio::find(Studio::files($user), $id);
        abort_unless($file, 404);
        $prefix = Studio::storageDirectory($user).'/';
        abort_unless(str_starts_with((string) ($file['path'] ?? ''), $prefix), 404);
        abort_unless(preg_match('/^'.preg_quote($prefix, '/').'[a-f0-9-]{36}\.(pdf|txt|md|png|jpg|jpeg|webp|mp3|wav|ogg)$/i', $file['path']), 404);
        abort_unless(Storage::disk('local')->exists($file['path']), 404);
        return $file;
    }
}
