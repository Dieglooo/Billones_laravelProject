<x-layouts.app.sidebar 
    title="Dashboard"
    :totalAppointments="$totalAppointments"
    :totalDentists="$totalDentists"
    :staticCard="$staticCard"
    :appointments="$appointments"
    :dentists="$dentists"
>

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

    .stat-card {
        background: linear-gradient(180deg, rgba(255,255,255,0.9), #ffffff);
        border-radius: 12px;
        padding: 18px;
        border: 1px solid rgba(15,23,42,0.04);
    }

    .pastel-stripe {
        height: 6px;
        border-radius: 6px;
        background: var(--pastel-1);
        margin-bottom: 14px;
        width: 96px;
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

    .input-soft {
        background: #ffffff;
        border: 1px solid rgba(15,23,42,0.06);
        padding: 10px;
        border-radius: 8px;
        width: 100%;
    }

    input:focus, textarea:focus, select:focus {
        outline: 2px solid rgba(168,237,234,0.35);
        outline-offset: 2px;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 50;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }

    .modal-content {
        background-color: #fff;
        margin: 10% auto;
        padding: 20px;
        border-radius: 12px;
        width: 400px;
        box-shadow: var(--soft-shadow);
    }
</style>

<div class="flex flex-col gap-6">

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="rounded-lg bg-green-50 p-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Dashboard Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="stat-card">
            <p class="text-sm font-medium text-gray-500">Total Appointments</p>
            <h3 class="mt-2 text-3xl font-bold text-neutral-900">{{ $totalAppointments }}</h3>
        </div>

        <div class="stat-card">
            <p class="text-sm font-medium text-gray-500">Total Dentists</p>
            <h3 class="mt-2 text-3xl font-bold text-neutral-900">{{ $totalDentists }}</h3>
        </div>

        <div class="stat-card">
            <p class="text-sm font-medium text-gray-500">Static</p>
            <h3 class="mt-2 text-3xl font-bold text-neutral-900">{{ $staticCard }}</h3>
        </div>

    </div>

    {{-- Add Appointment Form --}}
    <div class="page-card">
        <div class="pastel-stripe"></div>
        <h2 class="text-xl font-semibold text-neutral-900 mb-4">Add Appointment</h2>

        <form method="POST" action="{{ route('appointments.store') }}" class="flex flex-col gap-3">
            @csrf

            <input name="patient_name" class="input-soft" placeholder="Patient Name">
            @error('patient_name') 
                <p class="text-red-500 text-sm">{{ $message }}</p> 
            @enderror

            <input type="datetime-local" name="appointment_time" class="input-soft">
            @error('appointment_time') 
                <p class="text-red-500 text-sm">{{ $message }}</p> 
            @enderror

            <select name="dentist_id" class="input-soft">
                <option value="">-- No Dentist --</option>
                @foreach($dentists as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
            </select>

            <button class="btn-primary w-fit">Add Appointment</button>
        </form>
    </div>

    {{-- Appointments Table --}}
    <div class="page-card">
        <div class="pastel-stripe"></div>
        <h2 class="text-lg font-semibold mb-4 text-neutral-900">Appointments</h2>

        <table class="w-full dept-table">
            <thead>
                <tr class="text-left border-b">
                    <th class="p-2">Patient</th>
                    <th class="p-2">Time</th>
                    <th class="p-2">Dentist</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($appointments as $a)
                <tr class="border-b">
                    <td class="p-2">{{ $a->patient_name }}</td>
                    <td class="p-2">{{ $a->appointment_time }}</td>
                    <td class="p-2">{{ $a->dentist->name ?? 'N/A' }}</td>
                    <td class="p-2">{{ $a->status }}</td>
                    <td class="p-2 flex gap-3">

                        <button 
                            onclick='openEdit(@json($a))' 
                            class="action-link text-blue-500 hover:underline"
                        >
                            Edit
                        </button>

                        <form 
                            action="{{ route('appointments.destroy', $a->id) }}" 
                            method="POST"
                            onsubmit="return confirm('Delete this appointment?')"
                        >
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 hover:opacity-75">Delete</button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

{{-- Edit Appointment Modal --}}
<div id="editAppointmentModal" class="modal">
    <div class="modal-content">
        <h2 class="text-xl font-semibold mb-4 text-neutral-900">Edit Appointment</h2>

        <form method="POST" id="editForm">
            @csrf
            @method('PUT')

            <input type="text" id="editPatientName" name="patient_name" class="input-soft" placeholder="Patient Name" required>
            <input type="datetime-local" id="editAppointmentTime" name="appointment_time" class="input-soft" required>
            <select name="dentist_id" id="editDentistId" class="input-soft">
                <option value="">-- No Dentist --</option>
                @foreach($dentists as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
            </select>

            <div class="flex gap-3 mt-4">
                <button type="submit" class="btn-primary">Update</button>
                <button type="button" class="btn-ghost" onclick="closeEdit()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEdit(appointment) {
        const modal = document.getElementById('editAppointmentModal');
        modal.style.display = 'block';

        document.getElementById('editPatientName').value = appointment.patient_name;
        document.getElementById('editAppointmentTime').value = appointment.appointment_time;
        document.getElementById('editDentistId').value = appointment.dentist_id || '';
        document.getElementById('editForm').action = `/appointments/${appointment.id}`;
    }

    function closeEdit() {
        document.getElementById('editAppointmentModal').style.display = 'none';
    }

    // Close modal if clicked outside content
    window.onclick = function(event) {
        const modal = document.getElementById('editAppointmentModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</x-layouts.app.sidebar>
