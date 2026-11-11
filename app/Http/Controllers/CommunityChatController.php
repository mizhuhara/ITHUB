<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\CommunityMessage;
use App\Models\CommunityMessageAttachment;
use App\Models\CommunityMessageRead;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class CommunityChatController extends Controller
{
    /**
     * Dangerous file extensions forbidden from uploading.
     */
    protected const DISALLOWED_EXTENSIONS = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps', 'phar',
        'exe', 'bat', 'cmd', 'sh', 'bash', 'com', 'vbs', 'scr', 'msi',
        'jsp', 'asp', 'aspx', 'cgi', 'pl', 'jar', 'app', 'vb', 'wsf',
    ];

    /**
     * Max allowed single file size (50MB).
     */
    protected const MAX_FILE_SIZE_BYTES = 52428800; // 50 MB

    /**
     * Max allowed total payload size per message (100MB).
     */
    protected const MAX_TOTAL_SIZE_BYTES = 104857600; // 100 MB

    /**
     * Max files in a single batch or folder upload.
     */
    protected const MAX_FILES_COUNT = 150;

    /**
     * Display the chat interface for a community.
     */
    public function index(Request $request, Community $community): View
    {
        abort_unless(($community->status === 'active' && $community->approval_status === 'approved') || $request->user()?->role === 'super_admin', 404);
        abort_unless($community->canAccessChat($request->user()), 403, 'Akses ke chat komunitas ini dibatasi untuk anggota.');

        $isMember = $request->user() && $community->canSendChatMessage($request->user());
        $isOwnerOrAdmin = $request->user() && $community->isOwnerOrAdmin($request->user());

        // Initial messages: last 60 messages
        $rawMessages = $community->messages()
            ->with(['user', 'attachments'])
            ->orderBy('id', 'desc')
            ->take(60)
            ->get()
            ->reverse()
            ->values();

        $formattedMessages = $rawMessages->map(fn ($msg) => $this->formatMessage($msg, $request->user(), $community))->values();
        $canModerate = $request->user() && $community->canModerate($request->user());
        $initialMembers = $this->formatMemberList($community, $request->user());

        // Mark community chat as read for this user
        if ($request->user()) {
            $latestMsgId = $community->messages()->max('id') ?? 0;
            CommunityMessageRead::updateOrCreate(
                ['user_id' => $request->user()->id, 'community_id' => $community->id],
                ['last_read_message_id' => $latestMsgId, 'last_read_at' => now()]
            );
        }

        return view('community.chat', [
            'community' => $community,
            'isMember' => $isMember,
            'isOwnerOrAdmin' => $isOwnerOrAdmin,
            'canModerate' => $canModerate,
            'initialMessages' => $formattedMessages,
            'initialMembers' => $initialMembers,
            'maxFileSizeMb' => self::MAX_FILE_SIZE_BYTES / 1048576,
            'maxTotalSizeMb' => self::MAX_TOTAL_SIZE_BYTES / 1048576,
        ]);
    }

    /**
     * Fetch messages via JSON API for real-time polling.
     */
    public function messages(Request $request, Community $community): JsonResponse
    {
        abort_unless($community->canAccessChat($request->user()), 403, 'Akses ditolak.');

        $query = $community->messages()
            ->with(['user', 'attachments']);

        if ($request->filled('after_id')) {
            $query->where('id', '>', (int) $request->query('after_id'))
                ->orderBy('id', 'asc');
        } else {
            $query->orderBy('id', 'desc')->take(60);
        }

        $rawMessages = $query->get();
        if (! $request->filled('after_id')) {
            $rawMessages = $rawMessages->reverse()->values();
        }

        $formatted = $rawMessages->map(fn ($msg) => $this->formatMessage($msg, $request->user(), $community));

        if ($request->user() && $rawMessages->isNotEmpty()) {
            $latestId = $community->messages()->max('id') ?? 0;
            CommunityMessageRead::updateOrCreate(
                ['user_id' => $request->user()->id, 'community_id' => $community->id],
                ['last_read_message_id' => $latestId, 'last_read_at' => now()]
            );
        }

        return response()->json([
            'success' => true,
            'messages' => $formatted,
            'community_id' => $community->id,
            'last_id' => $rawMessages->last()?->id ?? (int) $request->query('after_id', 0),
            'is_member' => $community->canSendChatMessage($request->user()),
            'members_count' => (int) $community->members,
        ]);
    }

    /**
     * Get member list for chat members modal.
     */
    public function membersList(Request $request, Community $community): JsonResponse
    {
        abort_unless($community->canAccessChat($request->user()), 403, 'Akses ditolak.');

        $members = $this->formatMemberList($community, $request->user());

        return response()->json([
            'success' => true,
            'members' => $members,
            'total' => $members->count(),
            'can_moderate' => $community->canModerate($request->user()),
        ]);
    }

    /**
     * Format community member collection with roles and kick permissions.
     */
    protected function formatMemberList(Community $community, ?User $currentUser)
    {
        return $community->members()
            ->withPivot(['role', 'is_banned'])
            ->orderByPivot('role')
            ->get()
            ->map(function ($member) use ($community, $currentUser) {
                $isOwner = $member->id === $community->user_id;
                $role = $isOwner ? 'admin' : ($member->pivot->role ?: 'member');

                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'avatar_url' => $member->avatar_url,
                    'initials' => strtoupper(substr($member->name, 0, 2)),
                    'role' => $role,
                    'role_label' => match ($role) {
                        'admin' => $isOwner ? 'Pemilik' : 'Admin',
                        'moderator' => 'Moderator',
                        default => 'Anggota',
                    },
                    'is_owner' => $isOwner,
                    'is_self' => $currentUser && $currentUser->id === $member->id,
                    'is_banned' => (bool) $member->pivot->is_banned,
                    'can_be_kicked' => $community->canKickMember($currentUser, $member),
                    'joined_at' => $member->pivot?->created_at?->translatedFormat('d M Y')
                        ?: ($isOwner ? $community->created_at?->translatedFormat('d M Y') : ($member->created_at?->translatedFormat('d M Y') ?: '-')),
                    'email' => $member->email ?: '',
                ];
            });
    }

    /**
     * Send a message with optional text, photos, videos, files, or folder.
     */
    public function send(Request $request, Community $community): JsonResponse|RedirectResponse
    {
        abort_unless($community->canSendChatMessage($request->user()), 403, 'Anda tidak memiliki hak untuk mengirim pesan di komunitas ini.');

        // Validate basic inputs
        $hasText = $request->filled('message') && trim((string) $request->input('message')) !== '';
        $hasFiles = $request->hasFile('files');
        $hasPreUploaded = $request->filled('attachment_ids') && is_array($request->input('attachment_ids'));

        if (! $hasText && ! $hasFiles && ! $hasPreUploaded) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Pesan atau lampiran tidak boleh kosong.'], 422);
            }

            return back()->with('error', 'Pesan atau lampiran tidak boleh kosong.');
        }

        $files = $request->file('files', []);
        if (! is_array($files)) {
            $files = [$files];
        }

        // Validate files count
        if (count($files) > self::MAX_FILES_COUNT) {
            $msg = 'Jumlah file melebihi batas maksimal ('.self::MAX_FILES_COUNT.' file).';

            return $request->expectsJson()
                ? response()->json(['success' => false, 'error' => $msg], 422)
                : back()->with('error', $msg);
        }

        $folderName = $request->input('folder_name');
        $relativePaths = $request->input('relative_paths', []);
        $totalSize = 0;
        $preparedAttachments = [];

        // Validate each file
        foreach ($files as $index => $file) {
            if (! $file->isValid()) {
                $msg = 'File '.$file->getClientOriginalName().' gagal diunggah atau rusak.';

                return $request->expectsJson()
                    ? response()->json(['success' => false, 'error' => $msg], 422)
                    : back()->with('error', $msg);
            }

            $origName = $file->getClientOriginalName();
            $ext = strtolower($file->getClientOriginalExtension());
            $size = $file->getSize();
            $totalSize += $size;

            // Security: check disallowed extensions
            if (in_array($ext, self::DISALLOWED_EXTENSIONS, true)) {
                $msg = "File \"{$origName}\" dengan tipe ekstensi .{$ext} tidak diizinkan demi keamanan.";

                return $request->expectsJson()
                    ? response()->json(['success' => false, 'error' => $msg], 422)
                    : back()->with('error', $msg);
            }

            // Size check
            if ($size > self::MAX_FILE_SIZE_BYTES) {
                $msg = "File \"{$origName}\" melebihi batas maksimal ".(self::MAX_FILE_SIZE_BYTES / 1048576).' MB.';

                return $request->expectsJson()
                    ? response()->json(['success' => false, 'error' => $msg], 422)
                    : back()->with('error', $msg);
            }

            // Sanitized relative path
            $relPath = isset($relativePaths[$index]) ? $this->sanitizeRelativePath($relativePaths[$index]) : null;

            $fileType = $this->determineFileType($ext, (string) $file->getMimeType());

            $preparedAttachments[] = [
                'file' => $file,
                'orig_name' => $this->sanitizeFileName($origName),
                'ext' => $ext,
                'size' => $size,
                'mime' => $file->getMimeType() ?: 'application/octet-stream',
                'file_type' => $fileType,
                'folder_name' => $folderName ? $this->sanitizeFileName($folderName) : null,
                'relative_path' => $relPath,
            ];
        }

        if ($totalSize > self::MAX_TOTAL_SIZE_BYTES) {
            $msg = 'Total ukuran file lampiran melebihi batas '.(self::MAX_TOTAL_SIZE_BYTES / 1048576).' MB.';

            return $request->expectsJson()
                ? response()->json(['success' => false, 'error' => $msg], 422)
                : back()->with('error', $msg);
        }

        // Determine message type
        $messageType = 'text';
        if ($folderName || (! empty($preparedAttachments) && count($preparedAttachments) > 1 && $request->input('is_folder') === '1')) {
            $messageType = 'folder';
        } elseif (! empty($preparedAttachments)) {
            $types = array_unique(array_column($preparedAttachments, 'file_type'));
            if (count($types) === 1 && in_array($types[0], ['image', 'video'], true)) {
                $messageType = 'media';
            } else {
                $messageType = 'document';
            }
        }

        // Store message & attachments in DB transaction
        $communityMessage = DB::transaction(function () use ($community, $request, $messageType, $preparedAttachments, $hasPreUploaded) {
            $msg = CommunityMessage::create([
                'community_id' => $community->id,
                'user_id' => $request->user()->id,
                'message' => $request->filled('message') ? trim((string) $request->input('message')) : null,
                'type' => $messageType,
            ]);

            // Save new direct uploaded files
            foreach ($preparedAttachments as $item) {
                $storedPath = $item['file']->store('community_chat/'.$community->id, 'local');

                CommunityMessageAttachment::create([
                    'message_id' => $msg->id,
                    'community_id' => $community->id,
                    'user_id' => $request->user()->id,
                    'file_name' => $item['orig_name'],
                    'storage_path' => $storedPath,
                    'mime_type' => $item['mime'],
                    'file_size' => $item['size'],
                    'file_type' => $item['file_type'],
                    'folder_name' => $item['folder_name'],
                    'relative_path' => $item['relative_path'],
                ]);
            }

            // Attach any pre-uploaded chunked attachments
            if ($hasPreUploaded) {
                CommunityMessageAttachment::whereIn('id', $request->input('attachment_ids'))
                    ->where('user_id', $request->user()->id)
                    ->where('community_id', $community->id)
                    ->whereNull('message_id')
                    ->update(['message_id' => $msg->id]);
            }

            return $msg;
        });

        $communityMessage->load(['user', 'attachments']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $this->formatMessage($communityMessage, $request->user(), $community),
            ]);
        }

        return back()->with('success', 'Pesan berhasil dikirim.');
    }

    /**
     * Chunked upload handler for large files.
     */
    public function uploadChunk(Request $request, Community $community): JsonResponse
    {
        abort_unless($community->canSendChatMessage($request->user()), 403, 'Akses ditolak.');

        $request->validate([
            'file' => ['required', 'file'],
            'upload_id' => ['required', 'string', 'max:100'],
            'chunk_index' => ['required', 'integer', 'min:0'],
            'total_chunks' => ['required', 'integer', 'min:1'],
            'file_name' => ['required', 'string', 'max:255'],
        ]);

        $uploadId = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string) $request->input('upload_id'));
        $chunkIndex = (int) $request->input('chunk_index');
        $totalChunks = (int) $request->input('total_chunks');
        $origName = $this->sanitizeFileName((string) $request->input('file_name'));
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        // Security check
        if (in_array($ext, self::DISALLOWED_EXTENSIONS, true)) {
            return response()->json([
                'success' => false,
                'error' => "Ekstensi file .{$ext} dilarang.",
            ], 422);
        }

        $chunkDir = storage_path("app/private/chunks/{$uploadId}");
        if (! File::isDirectory($chunkDir)) {
            File::makeDirectory($chunkDir, 0755, true);
        }

        // Store chunk
        $chunkFile = $request->file('file');
        $chunkFile->move($chunkDir, "chunk_{$chunkIndex}");

        // If not all chunks yet, return progress status
        if ($chunkIndex < $totalChunks - 1) {
            return response()->json([
                'success' => true,
                'completed' => false,
                'chunk_index' => $chunkIndex,
                'total_chunks' => $totalChunks,
            ]);
        }

        // Final chunk arrived: merge chunks
        $targetDir = "community_chat/{$community->id}";
        $targetFilename = Str::random(40).($ext ? ".{$ext}" : '');
        $finalStoragePath = "{$targetDir}/{$targetFilename}";
        $finalAbsolutePath = storage_path("app/private/{$finalStoragePath}");

        if (! File::isDirectory(dirname($finalAbsolutePath))) {
            File::makeDirectory(dirname($finalAbsolutePath), 0755, true);
        }

        $out = fopen($finalAbsolutePath, 'wb');
        for ($i = 0; $i < $totalChunks; $i++) {
            $partPath = "{$chunkDir}/chunk_{$i}";
            if (! File::exists($partPath)) {
                fclose($out);
                File::deleteDirectory($chunkDir);

                return response()->json([
                    'success' => false,
                    'error' => "Chunk bagian {$i} hilang. Harap ulangi upload.",
                ], 422);
            }
            $in = fopen($partPath, 'rb');
            stream_copy_to_stream($in, $out);
            fclose($in);
        }
        fclose($out);

        // Clean up chunk directory
        File::deleteDirectory($chunkDir);

        $finalSize = File::size($finalAbsolutePath);
        $mime = File::mimeType($finalAbsolutePath) ?: 'application/octet-stream';
        $fileType = $this->determineFileType($ext, $mime);

        $folderName = $request->input('folder_name');
        $relPath = $request->input('relative_path')
            ? $this->sanitizeRelativePath($request->input('relative_path'))
            : null;

        // Create detached attachment record
        $attachment = CommunityMessageAttachment::create([
            'message_id' => null, // Placeholder, updated when message is sent
            'community_id' => $community->id,
            'user_id' => $request->user()->id,
            'file_name' => $origName,
            'storage_path' => $finalStoragePath,
            'mime_type' => $mime,
            'file_size' => $finalSize,
            'file_type' => $fileType,
            'folder_name' => $folderName ? $this->sanitizeFileName($folderName) : null,
            'relative_path' => $relPath,
        ]);

        return response()->json([
            'success' => true,
            'completed' => true,
            'attachment_id' => $attachment->id,
            'file_name' => $origName,
            'file_size' => $finalSize,
            'formatted_size' => $attachment->formattedSize(),
            'file_type' => $fileType,
            'mime_type' => $mime,
        ]);
    }

    /**
     * Preview inline media/document with safe headers.
     */
    public function preview(Request $request, Community $community, CommunityMessageAttachment $attachment): BinaryFileResponse|Response
    {
        abort_unless($attachment->community_id === $community->id, 404);
        abort_unless($community->canAccessChat($request->user()), 403, 'Akses ke lampiran dibatasi.');

        $storagePath = $attachment->storage_path;
        if (! Storage::disk('local')->exists($storagePath)) {
            abort(404, 'File lampiran tidak ditemukan di server.');
        }

        $fullPath = Storage::disk('local')->path($storagePath);
        $mime = $attachment->mime_type ?: Storage::disk('local')->mimeType($storagePath) ?: 'application/octet-stream';

        return response()->file($fullPath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.addslashes($attachment->file_name).'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }

    /**
     * Securely download an individual attachment.
     */
    public function download(Request $request, Community $community, CommunityMessageAttachment $attachment): BinaryFileResponse
    {
        abort_unless($attachment->community_id === $community->id, 404);
        abort_unless($community->canAccessChat($request->user()), 403, 'Akses unduhan dibatasi.');

        $storagePath = $attachment->storage_path;
        if (! Storage::disk('local')->exists($storagePath)) {
            abort(404, 'File lampiran tidak ditemukan.');
        }

        $fullPath = Storage::disk('local')->path($storagePath);

        return response()->download($fullPath, $attachment->file_name, [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Package and download all files of a folder message as a ZIP archive.
     */
    public function downloadFolder(Request $request, Community $community, CommunityMessage $message): BinaryFileResponse
    {
        abort_unless($message->community_id === $community->id, 404);
        abort_unless($community->canAccessChat($request->user()), 403, 'Akses unduhan dibatasi.');

        $attachments = $message->attachments;
        if ($attachments->isEmpty()) {
            abort(404, 'Folder tidak berisi file.');
        }

        $folderName = $message->folderName() ?: ($community->name.'_Folder_'.$message->id);
        $safeZipName = Str::slug($folderName, '_').'.zip';

        $tempDir = storage_path('app/private/temp_zips');
        if (! File::isDirectory($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $zipPath = $tempDir.'/'.Str::random(30).'.zip';
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat file arsip ZIP.');
        }

        foreach ($attachments as $att) {
            if (Storage::disk('local')->exists($att->storage_path)) {
                $absPath = Storage::disk('local')->path($att->storage_path);
                // Keep relative path if present; otherwise use file name
                $entryPath = $att->relative_path ?: ($folderName.'/'.$att->file_name);
                // Clean entry path to prevent zip-slip
                $entryPath = ltrim(preg_replace('#\.\.[\\\/]#', '', $entryPath), '/\\');
                $zip->addFile($absPath, $entryPath);
            }
        }

        $zip->close();

        return response()->download($zipPath, $safeZipName, [
            'Content-Type' => 'application/zip',
            'X-Content-Type-Options' => 'nosniff',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Format a community message for JSON response and template.
     */
    protected function formatMessage(CommunityMessage $message, ?User $currentUser, Community $community): array
    {
        $isMe = $currentUser && $message->user_id === $currentUser->id;
        $attachments = $message->attachments->map(function ($att) use ($community) {
            return [
                'id' => $att->id,
                'file_name' => $att->file_name,
                'file_size' => $att->file_size,
                'formatted_size' => $att->formattedSize(),
                'file_type' => $att->file_type,
                'mime_type' => $att->mime_type,
                'folder_name' => $att->folder_name,
                'relative_path' => $att->relative_path,
                'is_image' => $att->isImage(),
                'is_video' => $att->isVideo(),
                'extension' => $att->extension(),
                'preview_url' => route('community.chat.preview', [$community, $att]),
                'download_url' => route('community.chat.download', [$community, $att]),
            ];
        });

        $folderSummary = null;
        if ($message->isFolder()) {
            $folderSummary = [
                'folder_name' => $message->folderName() ?: 'Folder Berkas',
                'files_count' => $attachments->count(),
                'total_size' => $message->totalAttachmentSize(),
                'formatted_total_size' => $message->formattedTotalSize(),
                'download_zip_url' => route('community.chat.download_folder', [$community, $message]),
            ];
        }

        $author = $message->user;
        $authorRole = $author ? $community->getUserRole($author) : 'member';
        $isOwner = $author && $author->id === $community->user_id;
        $canKick = $currentUser && $author ? $community->canKickMember($currentUser, $author) : false;

        $authorJoinedAt = '-';
        if ($author) {
            $pivotDate = DB::table('community_user')
                ->where('community_id', $community->id)
                ->where('user_id', $author->id)
                ->value('created_at');

            if ($pivotDate) {
                $authorJoinedAt = Carbon::parse($pivotDate)->translatedFormat('d M Y');
            } elseif ($isOwner) {
                $authorJoinedAt = $community->created_at?->translatedFormat('d M Y') ?: '-';
            } else {
                $authorJoinedAt = $author->created_at?->translatedFormat('d M Y') ?: '-';
            }
        }

        return [
            'id' => $message->id,
            'community_id' => $message->community_id,
            'user_id' => $message->user_id,
            'user_name' => $author?->name ?: 'Pengguna',
            'user_initials' => strtoupper(substr((string) ($author?->name ?: 'U'), 0, 2)),
            'user_avatar_url' => $author?->avatar_url,
            'user_role' => $authorRole,
            'user_role_label' => match ($authorRole) {
                'admin' => $isOwner ? 'Pemilik' : 'Admin',
                'moderator' => 'Moderator',
                default => 'Anggota',
            },
            'can_be_kicked' => $canKick,
            'user_joined_at' => $authorJoinedAt,
            'user_email' => $author?->email ?: '',
            'is_me' => $isMe,
            'message' => $message->message,
            'type' => $message->type,
            'created_at' => $message->created_at?->toIso8601String(),
            'time_formatted' => $message->created_at?->format('H:i'),
            'date_formatted' => $message->created_at?->translatedFormat('d M Y'),
            'attachments' => $attachments,
            'folder_summary' => $folderSummary,
        ];
    }

    /**
     * Determine file type categorization based on extension and mime type.
     */
    protected function determineFileType(string $ext, string $mime): string
    {
        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'avif'];
        $videoExts = ['mp4', 'webm', 'ogg', 'mov', 'mkv', 'avi', 'm4v'];
        $docExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf', 'csv', 'json', 'md', 'xml'];
        $archiveExts = ['zip', 'rar', '7z', 'tar', 'gz'];

        if (in_array($ext, $imageExts, true) || str_starts_with($mime, 'image/')) {
            return 'image';
        }
        if (in_array($ext, $videoExts, true) || str_starts_with($mime, 'video/')) {
            return 'video';
        }
        if (in_array($ext, $docExts, true) || str_contains($mime, 'pdf') || str_contains($mime, 'document') || str_contains($mime, 'sheet')) {
            return 'document';
        }
        if (in_array($ext, $archiveExts, true) || str_contains($mime, 'zip') || str_contains($mime, 'compressed')) {
            return 'archive';
        }

        return 'other';
    }

    /**
     * Sanitize relative paths to prevent directory traversal.
     */
    protected function sanitizeRelativePath(string $path): string
    {
        // Normalize slashes
        $path = str_replace('\\', '/', $path);
        // Remove null bytes
        $path = str_replace("\0", '', $path);
        // Remove directory traversal elements like ../ or ./
        $segments = explode('/', $path);
        $safeSegments = [];
        foreach ($segments as $seg) {
            $seg = trim($seg);
            if ($seg === '' || $seg === '.' || $seg === '..') {
                continue;
            }
            $safeSegments[] = $this->sanitizeFileName($seg);
        }

        return implode('/', $safeSegments);
    }

    /**
     * Sanitize filename for safe storage and display.
     */
    protected function sanitizeFileName(string $filename): string
    {
        $filename = basename($filename);
        $filename = preg_replace('/[^\p{L}\p{N}\._\-\s\(\)]/u', '', $filename);

        return trim((string) $filename) ?: 'unnamed_file';
    }

    /**
     * Redirect to user's most active community chat, or community index if none.
     */
    public function all(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $summary = self::getUserChatSummary($user);

        if (! empty($summary['communities'])) {
            return redirect()->to($summary['communities'][0]['chat_url']);
        }

        return redirect()->route('community.index')
            ->with('info', 'Anda belum bergabung dengan komunitas manapun. Bergabunglah dengan komunitas untuk mulai mengobrol!');
    }

    /**
     * Get JSON data of user's communities and unread messages for the navbar chat dropdown.
     */
    public function dropdownSummary(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['success' => false, 'error' => 'Unauthenticated.'], 401);
        }

        $summary = self::getUserChatSummary($user);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Mark a community's chat as read for the authenticated user.
     */
    public function markAsRead(Request $request, Community $community): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['success' => false, 'error' => 'Unauthenticated.'], 401);
        }

        abort_unless($community->canAccessChat($user), 403, 'Akses ditolak.');

        $maxId = $community->messages()->max('id') ?? 0;

        CommunityMessageRead::updateOrCreate(
            ['user_id' => $user->id, 'community_id' => $community->id],
            ['last_read_message_id' => $maxId, 'last_read_at' => now()]
        );

        $summary = self::getUserChatSummary($user);

        return response()->json([
            'success' => true,
            'community_id' => $community->id,
            'total_unread_count' => $summary['total_unread_count'],
        ]);
    }

    /**
     * Compute full community chat summary for the authenticated user.
     */
    public static function getUserChatSummary(User $user): array
    {
        $communities = Community::query()
            ->where('status', 'active')
            ->where('approval_status', 'approved')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('members', function ($m) use ($user) {
                        $m->where('users.id', $user->id)
                            ->where('community_user.is_banned', false);
                    });
            })
            ->with(['creator'])
            ->get();

        $reads = CommunityMessageRead::where('user_id', $user->id)
            ->whereIn('community_id', $communities->pluck('id'))
            ->get()
            ->keyBy('community_id');

        $palette = [
            ['#2563eb', '#1d4ed8'],
            ['#7c3aed', '#6d28d9'],
            ['#059669', '#047857'],
            ['#d97706', '#b45309'],
            ['#db2777', '#be185d'],
            ['#0891b2', '#0e7490'],
        ];

        $items = [];
        $totalUnreadCount = 0;

        foreach ($communities as $community) {
            if (! $community->canAccessChat($user)) {
                continue;
            }

            $lastReadId = $reads->get($community->id)?->last_read_message_id ?? 0;

            $unreadCount = CommunityMessage::where('community_id', $community->id)
                ->where('id', '>', $lastReadId)
                ->where('user_id', '!=', $user->id)
                ->count();

            $totalUnreadCount += $unreadCount;

            $latestMsg = CommunityMessage::where('community_id', $community->id)
                ->with(['user', 'attachments'])
                ->latest('id')
                ->first();

            $idx = abs(crc32($community->name)) % count($palette);
            $bgColors = $palette[$idx];

            if ($latestMsg) {
                $isMe = $latestMsg->user_id === $user->id;
                $senderName = $isMe ? 'Anda' : ($latestMsg->user?->name ?: 'Anggota');
                $snippet = self::formatSnippet($latestMsg);
                $timeFormatted = self::formatTimeAgo($latestMsg->created_at);
                $timestamp = $latestMsg->created_at?->timestamp ?? 0;
            } else {
                $senderName = null;
                $snippet = 'Belum ada obrolan. Mulai menyapa!';
                $timeFormatted = '';
                $timestamp = $community->created_at?->timestamp ?? 0;
            }

            $items[] = [
                'id' => $community->id,
                'name' => $community->name,
                'category' => $community->category,
                'logo_url' => $community->logo_url,
                'initials' => strtoupper(substr((string) $community->name, 0, 2)),
                'gradient' => $bgColors,
                'sender_name' => $senderName,
                'snippet' => $snippet,
                'time_formatted' => $timeFormatted,
                'timestamp' => $timestamp,
                'unread_count' => $unreadCount,
                'is_unread' => $unreadCount > 0,
                'chat_url' => route('community.chat', $community),
            ];
        }

        // Sort items by timestamp DESC so the most active community appears first
        usort($items, fn ($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        $firstChatUrl = ! empty($items) ? $items[0]['chat_url'] : route('community.index');

        return [
            'communities' => $items,
            'total_unread_count' => $totalUnreadCount,
            'all_chat_url' => $firstChatUrl,
        ];
    }

    /**
     * Format a compact text snippet for dropdown previews.
     */
    protected static function formatSnippet(CommunityMessage $message): string
    {
        $text = trim((string) $message->message);

        if ($message->isFolder()) {
            $folder = $message->folderName() ?: 'Berkas';

            return '📁 Folder: '.$folder;
        }

        if ($message->type === 'media') {
            $hasVideo = $message->attachments->contains(fn ($a) => $a->isVideo());
            $label = $hasVideo ? '🎥 Video' : '📷 Foto';

            return $text !== '' ? "{$label} • {$text}" : $label;
        }

        if ($message->type === 'document') {
            $doc = $message->attachments->first();
            $fileName = $doc?->file_name ?: 'Dokumen';

            return $text !== '' ? "📄 {$fileName} • {$text}" : "📄 {$fileName}";
        }

        if ($text !== '') {
            return Str::limit($text, 45);
        }

        if ($message->attachments->isNotEmpty()) {
            return '📎 Lampiran berkas';
        }

        return 'Pesan obrolan';
    }

    /**
     * Format a human-friendly timestamp for dropdown chat items.
     */
    protected static function formatTimeAgo(?Carbon $time): string
    {
        if (! $time) {
            return '';
        }

        if ($time->isToday()) {
            return $time->format('H:i');
        }

        if ($time->isYesterday()) {
            return 'Kemarin';
        }

        if ($time->year === now()->year) {
            return $time->translatedFormat('d M');
        }

        return $time->format('d/m/y');
    }
}
