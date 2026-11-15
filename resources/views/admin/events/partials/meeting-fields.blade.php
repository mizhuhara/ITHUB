@php
    $meetingUrl = old('meeting_url', $event?->meeting_url ?? '');
    $meetingMode = old('mode', $event?->mode ?? 'In-Person');
    $showMeetingFields = $meetingMode === 'Online';
@endphp

<div id="meeting-fields" class="{{ $showMeetingFields ? '' : 'hidden' }}">
    <label for="meeting_url" class="block text-xs font-bold text-slate-700 mb-1.5">
        Link Meeting (Zoom / Google Meet)
    </label>
    <input id="meeting_url" name="meeting_url" type="url"
           value="{{ $meetingUrl }}"
           placeholder="https://zoom.us/j/... atau https://meet.google.com/..."
           class="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 placeholder-slate-400 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
    <p class="text-[11px] text-slate-400 mt-1.5">
        Link asli hanya dibagikan ke peserta terdaftar. Setiap peserta mendapat link unik untuk absensi dan sertifikat.
    </p>
</div>

@push('scripts')
<script>
    (function () {
        const mode = document.getElementById('mode');
        const fields = document.getElementById('meeting-fields');

        if (!mode || !fields) {
            return;
        }

        const toggle = function () {
            fields.classList.toggle('hidden', mode.value !== 'Online');
        };

        mode.addEventListener('change', toggle);
        toggle();
    })();
</script>
@endpush
