{{-- ========================================================================= --}}
{{-- ASSET: UNIVERSAL DELETE CONFIRMATION MODAL --}}
{{-- Clean, modern, non-alay confirmation dialog for destructive actions --}}
{{-- ========================================================================= --}}

<div id="global-delete-modal" 
     class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center p-4 transition-all duration-200"
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="global-delete-title">

    <div class="bg-white rounded-2xl sm:rounded-3xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all p-6 sm:p-7 space-y-4 text-left">
        {{-- Header with subtle danger icon --}}
        <div class="flex items-start gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100/90 shadow-2xs mt-0.5">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0 pr-1">
                <h3 id="global-delete-title" class="text-base font-extrabold text-slate-900 tracking-tight leading-snug">
                    Konfirmasi Hapus
                </h3>
                <p id="global-delete-message" class="text-xs sm:text-sm text-slate-500 leading-relaxed mt-1">
                    Apakah Anda yakin ingin menghapus <strong id="global-delete-item-name" class="text-slate-900 font-bold"></strong>?
                </p>
            </div>
            <button type="button" 
                    onclick="closeGlobalDeleteModal()" 
                    class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer shrink-0" 
                    aria-label="Tutup">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Subtle Warning Notice --}}
        <div id="global-delete-warning-box" class="text-xs text-rose-800 bg-rose-50/80 border border-rose-200/80 px-3.5 py-3 rounded-xl flex items-start gap-2.5 leading-relaxed">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="shrink-0 text-rose-600 mt-0.5"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span id="global-delete-warning-text" class="font-medium">Tindakan ini permanen dan data terkait akan dihapus dari sistem.</span>
        </div>

        {{-- Action Buttons --}}
        <form id="global-delete-form" method="POST" data-async data-async-reload class="pt-2 flex items-center justify-end gap-3">
            @csrf
            @method('DELETE')
            <button type="button" 
                    onclick="closeGlobalDeleteModal()" 
                    class="h-10 px-4.5 inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white hover:bg-slate-50 active:bg-slate-100 text-slate-700 font-bold text-xs sm:text-sm transition cursor-pointer leading-normal shadow-2xs">
                Batal
            </button>
            <button type="submit" 
                    id="global-delete-submit-btn" 
                    class="h-10 px-5 inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold text-xs sm:text-sm tracking-wide shadow-xs shadow-rose-600/25 transition cursor-pointer leading-normal">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" class="shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span id="global-delete-submit-text">Hapus Data</span>
            </button>
        </form>
    </div>
</div>

<script>
    /**
     * Open Global Delete Confirmation Modal
     * @param {string} actionUrl - URL target form DELETE
     * @param {string} itemName - Nama objek yang akan dihapus
     * @param {Object} options - Opsi tambahan { title, message, warning, submitText, onConfirm }
     */
    let globalDeleteCallback = null;

    window.confirmDelete = function(actionUrl, itemName = 'data ini', options = {}) {
        const modal = document.getElementById('global-delete-modal');
        const form = document.getElementById('global-delete-form');
        const titleEl = document.getElementById('global-delete-title');
        const nameEl = document.getElementById('global-delete-item-name');
        const msgEl = document.getElementById('global-delete-message');
        const warnEl = document.getElementById('global-delete-warning-text');
        const submitBtn = document.getElementById('global-delete-submit-btn');
        const submitTextEl = document.getElementById('global-delete-submit-text');

        if (!modal || !form) return;

        globalDeleteCallback = options.onConfirm || null;
        form.action = actionUrl || '#';
        if (nameEl) nameEl.textContent = itemName;

        if (options.title) titleEl.textContent = options.title;
        else titleEl.textContent = 'Konfirmasi Hapus';

        if (options.message) {
            msgEl.innerHTML = options.message;
        } else {
            msgEl.innerHTML = `Apakah Anda yakin ingin menghapus <strong class="text-slate-900 font-bold">${itemName}</strong>?`;
        }

        if (options.warning) warnEl.textContent = options.warning;
        
        const btnText = options.submitText || 'Hapus Data';
        if (submitTextEl) {
            submitTextEl.textContent = btnText;
        } else if (submitBtn) {
            submitBtn.textContent = btnText;
        }

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    window.openGlobalDeleteModal = window.confirmDelete;

    window.closeGlobalDeleteModal = function() {
        const modal = document.getElementById('global-delete-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            globalDeleteCallback = null;
        }
    };

    // Handle form submit or custom callback
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('global-delete-form');
        if (form) {
            form.addEventListener('submit', (e) => {
                if (typeof globalDeleteCallback === 'function') {
                    e.preventDefault();
                    const cb = globalDeleteCallback;
                    closeGlobalDeleteModal();
                    cb();
                }
            });
        }

        const modal = document.getElementById('global-delete-modal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeGlobalDeleteModal();
                }
            });
        }

        // Intercept native onsubmit confirm(...) forms across all admin pages
        document.querySelectorAll('form').forEach(f => {
            const onsubmitAttr = f.getAttribute('onsubmit');
            if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                // remove inline handler and replace with modern modal
                f.removeAttribute('onsubmit');
                let match = onsubmitAttr.match(/confirm\(['"]([^'"]+)['"]\)/);
                let msg = match ? match[1] : 'Apakah Anda yakin ingin menghapus data ini?';

                f.addEventListener('submit', function(evt) {
                    if (this.dataset.confirmed === 'true') return;
                    evt.preventDefault();
                    window.confirmDelete(this.action, '', {
                        title: 'Konfirmasi Hapus',
                        message: msg,
                        onConfirm: () => {
                            this.dataset.confirmed = 'true';
                            this.setAttribute('data-async-reload', '');

                            if (typeof window.saveFormAsync === 'function') {
                                window.saveFormAsync(this);
                            } else {
                                this.submit();
                            }
                        }
                    });
                });
            }
        });
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeGlobalDeleteModal();
        }
    });
</script>
