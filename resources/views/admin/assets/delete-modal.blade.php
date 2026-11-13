{{-- ========================================================================= --}}
{{-- ASSET: UNIVERSAL DELETE CONFIRMATION MODAL --}}
{{-- Clean, modern, non-alay confirmation dialog for destructive actions --}}
{{-- ========================================================================= --}}

<div id="global-delete-modal" 
     style="display: none; pointer-events: none;"
     class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-[2px] items-center justify-center p-4 transition-all duration-200"
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="global-delete-title">

    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all p-6 space-y-4">
        {{-- Header with subtle danger icon --}}
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100 mt-0.5">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 id="global-delete-title" class="text-[15px] font-bold text-slate-900 tracking-tight">
                    Konfirmasi Hapus
                </h3>
                <p id="global-delete-message" class="text-[13px] text-slate-500 leading-relaxed mt-1">
                    Apakah Anda yakin ingin menghapus <strong id="global-delete-item-name" class="text-slate-800 font-semibold"></strong>?
                </p>
            </div>
            <button type="button" 
                    onclick="closeGlobalDeleteModal()" 
                    class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition" 
                    aria-label="Tutup">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Subtle Warning Notice --}}
        <div id="global-delete-warning-box" class="text-[12px] text-rose-700 bg-rose-50/70 border border-rose-100/80 px-3.5 py-2.5 rounded-xl flex items-center gap-2">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="shrink-0 text-rose-500"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span id="global-delete-warning-text">Tindakan ini permanen dan data terkait akan dihapus dari sistem.</span>
        </div>

        {{-- Action Buttons --}}
        <form id="global-delete-form" method="POST" class="pt-1 flex items-center justify-end gap-2.5">
            @csrf
            @method('DELETE')
            <button type="button" 
                    onclick="closeGlobalDeleteModal()" 
                    class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-[13px] transition cursor-pointer">
                Batal
            </button>
            <button type="submit" 
                    id="global-delete-submit-btn" 
                    class="px-4.5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold text-[13px] shadow-xs shadow-rose-600/20 transition cursor-pointer">
                Hapus Data
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
        let nameEl = document.getElementById('global-delete-item-name');
        const msgEl = document.getElementById('global-delete-message');
        const warnEl = document.getElementById('global-delete-warning-text');
        const submitBtn = document.getElementById('global-delete-submit-btn');

        if (!modal || !form) return;

        globalDeleteCallback = options.onConfirm || null;
        form.action = actionUrl || '#';

        if (titleEl) {
            titleEl.textContent = options.title || 'Konfirmasi Hapus';
        }

        if (options.message) {
            if (msgEl) msgEl.innerHTML = options.message;
        } else {
            if (!nameEl && msgEl) {
                msgEl.innerHTML = `Apakah Anda yakin ingin menghapus <strong id="global-delete-item-name" class="text-slate-800 font-semibold"></strong>?`;
                nameEl = document.getElementById('global-delete-item-name');
            }
            if (nameEl) {
                nameEl.textContent = itemName;
            }
        }

        if (warnEl && options.warning) warnEl.textContent = options.warning;
        if (submitBtn) {
            submitBtn.textContent = options.submitText || 'Hapus Data';
        }

        modal.style.display = 'flex';
        modal.style.pointerEvents = 'auto';
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    window.openGlobalDeleteModal = window.confirmDelete;

    window.closeGlobalDeleteModal = function() {
        const modal = document.getElementById('global-delete-modal');
        if (modal) {
            modal.style.display = 'none';
            modal.style.pointerEvents = 'none';
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
