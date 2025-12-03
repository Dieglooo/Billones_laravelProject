<div id="edit-appointment-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded shadow-lg w-96">
        <h2 class="text-xl font-bold mb-4">Edit Appointment</h2>

        <form id="edit-appointment-form" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="patient_name" class="block mb-1">Patient Name</label>
                <input type="text" name="patient_name" id="patient_name" class="w-full border rounded p-2">
            </div>

            <div class="mb-4">
                <label for="appointment_time" class="block mb-1">Appointment Time</label>
                <input type="datetime-local" name="appointment_time" id="appointment_time" class="w-full border rounded p-2">
            </div>

            <div class="mb-4">
                <label for="dentist_id" class="block mb-1">Dentist</label>
                <select name="dentist_id" id="dentist_id" class="w-full border rounded p-2">
                    @foreach($dentists as $dentist)
                        <option value="{{ $dentist->id }}">{{ $dentist->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('edit-appointment-modal').classList.add('hidden')" class="px-4 py-2 border rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            </div>
        </form>
    </div>
</div>
