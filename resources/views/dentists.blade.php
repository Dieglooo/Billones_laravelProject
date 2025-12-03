<x-layouts.app.sidebar title="Dentists">

<style>
    :root{
        --pastel-1: linear-gradient(90deg, rgba(168,237,234,0.95), rgba(254,214,227,0.95));
        --soft-shadow: 0 8px 24px rgba(15,23,42,0.06);
        --muted: #6b7280;
        --accent-text: #0f172a;
    }

    .page-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: var(--soft-shadow);
        border: 1px solid rgba(15,23,42,0.04);
        padding: 20px;
    }

    .pastel-stripe {
        height: 6px;
        border-radius: 6px;
        background: var(--pastel-1);
        margin-bottom: 14px;
        width: 96px;
    }

    .input-soft {
        background: #ffffff;
        border: 1px solid rgba(15,23,42,0.06);
        padding: 10px;
        border-radius: 8px;
        width: 100%;
        color: var(--accent-text);
    }

    .btn-primary {
        background-image: var(--pastel-1);
        color: #07203a;
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        box-shadow: 0 6px 18px rgba(168,237,234,0.12);
        font-weight: 600;
    }

    .btn-ghost {
        background: transparent;
        border: 1px solid rgba(15,23,42,0.06);
        padding: 8px 14px;
        border-radius: 8px;
        color: var(--muted);
    }

    .action-link {
        color: #0f172a;
        opacity: 0.85;
    }

    input:focus, textarea:focus, select:focus {
        outline: 2px solid rgba(168,237,234,0.35);
        outline-offset: 2px;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background-color: rgba(0,0,0,0.4);
        z-index: 50;
        align-items: center;
        justify-content: center;
    }

    .modal.show {
        display: flex;
    }

    .modal-wrap {
        width: 400px;
    }
</style>

<div class="flex flex-col gap-6 p-6">

    {{-- ADD DENTIST CARD --}}
    <div class="page-card">
        <div class="pastel-stripe"></div>
        <h2 class="text-xl font-semibold mb-4 text-neutral-900">Add Dentist</h2>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 p-3 text-green-700 mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-lg bg-red-50 p-3 text-red-700 mb-4">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('dentists.store') }}" method="POST" class="flex flex-col gap-3 mb-6">
            @csrf
            <input name="name" class="input-soft" placeholder="Dentist Name" required>
            <input name="specialization" class="input-soft" placeholder="Specialization">
            <button class="btn-primary w-fit">Add Dentist</button>
        </form>
    </div>

    {{-- DENTIST LIST --}}
    <div class="page-card">
        <div class="pastel-stripe"></div>
        <h2 class="text-lg font-semibold mb-4 text-neutral-900">Dentist List</h2>

        <table class="w-full dept-table">
            <thead>
                <tr class="border-b text-left text-neutral-600">
                    <th class="p-2">Name</th>
                    <th class="p-2">Specialization</th>
                    <th class="p-2">Appointments</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse($dentists as $d)
                    <tr class="hover:bg-neutral-50" data-id="{{ $d->id }}">
                        <td class="p-2"><span id="dentist-name-{{ $d->id }}">{{ $d->name }}</span></td>
                        <td class="p-2"><span id="dentist-specialization-{{ $d->id }}">{{ $d->specialization }}</span></td>
                        <td class="p-2">{{ $d->appointments_count }}</td>
                        <td class="p-2 flex gap-3 items-center">
                            <button
                                type="button"
                                onclick='editDentist(@json($d))'
                                class="action-link hover:underline"
                            >
                                Edit
                            </button>
                            <span class="text-neutral-300">|</span>
                            <form 
                                action="{{ route('dentists.destroy', $d) }}"
                                method="POST"
                                onsubmit="return confirm('Delete dentist?')"
                                class="inline"
                            >
                                @csrf
                                @method('DELETE')
                                <button class="text-red-500 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-500">
                            No dentists found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $dentists->links() }}
        </div>
    </div>

</div>

{{-- EDIT MODAL --}}
<div id="editDentistModal" class="modal" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="modal-wrap page-card">
        <h2 class="text-lg font-semibold mb-4 text-neutral-900">Edit Dentist</h2>

        <form id="editDentistForm" method="POST" class="flex flex-col gap-3">
            @csrf
            @method('PUT')

            <input type="text" name="name" id="editDentistName" class="input-soft" placeholder="Dentist Name" required>
            <input type="text" name="specialization" id="editDentistSpecialization" class="input-soft" placeholder="Specialization">
            <div id="editDentistMessage" class="text-sm mt-1"></div>

            <div class="flex justify-end gap-2 mt-3">
                <button type="button" onclick="closeEditDentistModal()" class="btn-ghost">Cancel</button>
                <button type="submit" id="editDentistSubmit" class="btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    const BASE_DENTISTS_URL = "{{ url('dentists') }}";

    function editDentist(d) {
        const modal = document.getElementById('editDentistModal');
        const form = document.getElementById('editDentistForm');

        form.action = `${BASE_DENTISTS_URL}/${d.id}`;
        document.getElementById('editDentistName').value = d.name ?? '';
        document.getElementById('editDentistSpecialization').value = d.specialization ?? '';

        // show modal via class and update accessibility
        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');

        // focus the name input for accessibility/UX
        setTimeout(() => {
            const nameInput = document.getElementById('editDentistName');
            if (nameInput) nameInput.focus();
        }, 50);
    }

    function closeEditDentistModal() {
        const modal = document.getElementById('editDentistModal');
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
    }

    // Close modal with Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeEditDentistModal();
    });

    // Close modal if clicked outside
    window.addEventListener('click', (e) => {
        const modal = document.getElementById('editDentistModal');
        if(e.target === modal) closeEditDentistModal();
    });

    // AJAX submit for edit form
    document.getElementById('editDentistForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const msg = document.getElementById('editDentistMessage');
        msg.textContent = '';
        const submitBtn = document.getElementById('editDentistSubmit');
        const origBtnText = submitBtn ? submitBtn.textContent : '';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Updating...';
        }

        const formData = new FormData(form);
        // Ensure method override for PUT
        if (!formData.get('_method')) formData.append('_method', 'PUT');

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            });

            if (res.ok) {
                const data = await res.json().catch(() => null);
                // Update table row
                const id = data?.dentist?.id || form.action.split('/').pop();
                const nameEl = document.getElementById('dentist-name-' + id);
                const specEl = document.getElementById('dentist-specialization-' + id);
                const newName = data?.dentist?.name ?? document.getElementById('editDentistName').value;
                const newSpec = data?.dentist?.specialization ?? document.getElementById('editDentistSpecialization').value;
                if (nameEl) nameEl.textContent = newName;
                if (specEl) specEl.textContent = newSpec;

                // show success and close
                msg.className = 'text-sm mt-1 text-green-600';
                msg.textContent = 'Updated';
                setTimeout(() => {
                    closeEditDentistModal();
                    msg.textContent = '';
                }, 800);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = origBtnText;
                }
            } else {
                const err = await res.json().catch(() => null);
                if (err && err.errors) {
                    // show first validation error
                    const first = Object.values(err.errors)[0];
                    msg.className = 'text-sm mt-1 text-red-600';
                    msg.textContent = Array.isArray(first) ? first[0] : first;
                } else {
                    msg.className = 'text-sm mt-1 text-red-600';
                    msg.textContent = 'Update failed';
                }
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = origBtnText;
                }
            }
        } catch (ex) {
            msg.className = 'text-sm mt-1 text-red-600';
            msg.textContent = 'Network error';
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = origBtnText;
            }
        }
    });
</script>

</x-layouts.app.sidebar>
