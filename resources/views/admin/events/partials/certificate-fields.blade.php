@php
    $certificateEnabled = (bool) old('certificate_enabled', $event->certificate_enabled ?? false);
    $certificateX = old('certificate_name_x', $event->certificate_name_x ?? 50);
    $certificateY = old('certificate_name_y', $event->certificate_name_y ?? 50);
    $certificateFontSize = old('certificate_name_font_size', $event->certificate_name_font_size ?? 48);
    $certificateColor = old('certificate_name_color', $event->certificate_name_color ?? '#111827');
    $certificateTemplateUrl = ! empty($event?->certificate_template)
        ? asset('storage/'.$event->certificate_template)
        : null;
@endphp

<div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-4">
    <h2 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-3">
        Sertifikat Peserta
    </h2>

    <label class="inline-flex items-center gap-2.5 cursor-pointer">
        <input type="checkbox" id="certificate-enabled" name="certificate_enabled" value="1"
               class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
               {{ $certificateEnabled ? 'checked' : '' }}>
        <span class="text-xs font-bold text-slate-700">Sediakan sertifikat untuk peserta</span>
    </label>

    <div id="certificate-fields" class="space-y-4 {{ $certificateEnabled ? '' : 'hidden' }}">
        <div>
            <label for="certificate-template-input" class="block text-xs font-bold text-slate-700 mb-1.5">
                Template Sertifikat (PNG / JPG)
            </label>
            <input id="certificate-template-input" type="file" name="certificate_template" accept="image/*"
                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition cursor-pointer">
            <p class="text-[11px] text-slate-400 mt-1.5">
                Maksimal 4MB. Kosongkan jika tidak ingin mengganti template saat ini.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="certificate-name-x" class="block text-xs font-bold text-slate-700 mb-1.5">Posisi X (%)</label>
                <input id="certificate-name-x" name="certificate_name_x" type="number" min="0" max="100" step="0.1"
                       value="{{ $certificateX }}"
                       class="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
            </div>
            <div>
                <label for="certificate-name-y" class="block text-xs font-bold text-slate-700 mb-1.5">Posisi Y (%)</label>
                <input id="certificate-name-y" name="certificate_name_y" type="number" min="0" max="100" step="0.1"
                       value="{{ $certificateY }}"
                       class="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
            </div>
            <div>
                <label for="certificate-name-font-size" class="block text-xs font-bold text-slate-700 mb-1.5">Ukuran Font (pt)</label>
                <input id="certificate-name-font-size" name="certificate_name_font_size" type="number" min="8" max="200"
                       value="{{ $certificateFontSize }}"
                       class="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
            </div>
            <div>
                <label for="certificate-name-color" class="block text-xs font-bold text-slate-700 mb-1.5">Warna Nama</label>
                <input id="certificate-name-color" name="certificate_name_color" type="color"
                       value="{{ $certificateColor }}"
                       class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50/50 p-1 cursor-pointer">
            </div>
        </div>

        <div id="certificate-preview" class="relative overflow-hidden rounded-xl border border-slate-200 {{ $certificateTemplateUrl ? '' : 'hidden' }}">
            <img id="certificate-preview-image" src="{{ $certificateTemplateUrl }}" alt="Preview Sertifikat"
                 class="block w-full">
            <span id="certificate-preview-name"
                  class="absolute whitespace-nowrap font-bold"
                  style="left: {{ $certificateX }}%; top: {{ $certificateY }}%; transform: translate(-50%, -50%); color: {{ $certificateColor }}; font-size: {{ max(10, $certificateFontSize * 0.4) }}px;">
                Nama Peserta
            </span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const toggle = document.getElementById('certificate-enabled');
        const fields = document.getElementById('certificate-fields');
        const templateInput = document.getElementById('certificate-template-input');
        const preview = document.getElementById('certificate-preview');
        const previewImage = document.getElementById('certificate-preview-image');
        const previewName = document.getElementById('certificate-preview-name');
        const xInput = document.getElementById('certificate-name-x');
        const yInput = document.getElementById('certificate-name-y');
        const sizeInput = document.getElementById('certificate-name-font-size');
        const colorInput = document.getElementById('certificate-name-color');

        if (!toggle || !fields || !previewName) {
            return;
        }

        const updatePreview = function () {
            previewName.style.left = (xInput.value || 50) + '%';
            previewName.style.top = (yInput.value || 50) + '%';
            previewName.style.fontSize = Math.max(10, parseFloat(sizeInput.value || 48) * 0.4) + 'px';
            previewName.style.color = colorInput.value;
        };

        toggle.addEventListener('change', function () {
            fields.classList.toggle('hidden', !toggle.checked);
        });

        [xInput, yInput, sizeInput, colorInput].forEach(function (input) {
            input.addEventListener('input', updatePreview);
        });

        if (templateInput) {
            templateInput.addEventListener('change', function () {
                const file = this.files[0];

                if (!file) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (event) {
                    previewImage.src = event.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            });
        }
    })();
</script>
@endpush
