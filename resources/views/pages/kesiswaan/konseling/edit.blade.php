<x-layouts.page
    :title="'Ubah Sesi Konseling'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="konseling.index">

    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Ubah Sesi Konseling</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Perbarui catatan konseling — ubah detail sesi, tindakan, atau level kerahasiaan.
            </p>
        </div>

        @if ($errors->any())
            <x-ui.alert variant="danger" class="mb-6">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

        <form method="POST" action="{{ route('konseling.update', $session) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-ui.sheet>
                <x-ui.form-section title="Data Sesi" description="Pilih siswa dan tentukan jenis konseling.">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="class_group_id" class="block text-xs font-semibold text-ink-soft mb-1.5">Kelas <span class="text-danger">*</span></label>
                            <x-ui.select name="class_group_id" id="class_group_id" :options="$classes->pluck('name', 'id')" :selected="$selectedClassId" placeholder="Pilih kelas" />
                        </div>
                        <div>
                            <label for="student_enrollment_id" class="block text-xs font-semibold text-ink-soft mb-1.5">Siswa <span class="text-danger">*</span></label>
                            <x-ui.select name="student_enrollment_id" id="student_enrollment_id" :options="$students" :selected="old('student_enrollment_id', $session->student_enrollment_id)" />
                            @error('student_enrollment_id') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="session_date" class="block text-xs font-semibold text-ink-soft mb-1.5">Tanggal Sesi <span class="text-danger">*</span></label>
                            <x-ui.input type="date" name="session_date" id="session_date" value="{{ old('session_date', $session->session_date->format('Y-m-d')) }}" required />
                            @error('session_date') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="counseling_type" class="block text-xs font-semibold text-ink-soft mb-1.5">Jenis Konseling <span class="text-danger">*</span></label>
                            <x-ui.select name="counseling_type" id="counseling_type" :options="$counselingTypes" :selected="old('counseling_type', $session->counseling_type)" />
                            @error('counseling_type') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </x-ui.form-section>

                <x-ui.form-section title="Isi Konseling" description="Topik, permasalahan, asesmen, dan tindakan.">
                    <div class="space-y-4">
                        <div>
                            <label for="topic" class="block text-xs font-semibold text-ink-soft mb-1.5">Topik <span class="text-danger">*</span></label>
                            <x-ui.input type="text" name="topic" id="topic" value="{{ old('topic', $session->topic) }}" required />
                            @error('topic') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="problem_description" class="block text-xs font-semibold text-ink-soft mb-1.5">Permasalahan</label>
                            <textarea name="problem_description" id="problem_description" rows="3" class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">{{ old('problem_description', $session->problem_description) }}</textarea>
                            @error('problem_description') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="assessment_result" class="block text-xs font-semibold text-ink-soft mb-1.5">Hasil Asesmen</label>
                            <textarea name="assessment_result" id="assessment_result" rows="3" class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">{{ old('assessment_result', $session->assessment_result) }}</textarea>
                            @error('assessment_result') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="action_taken" class="block text-xs font-semibold text-ink-soft mb-1.5">Tindakan</label>
                            <textarea name="action_taken" id="action_taken" rows="3" class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">{{ old('action_taken', $session->action_taken) }}</textarea>
                            @error('action_taken') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="follow_up_plan" class="block text-xs font-semibold text-ink-soft mb-1.5">Rencana Tindak Lanjut</label>
                            <textarea name="follow_up_plan" id="follow_up_plan" rows="3" class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">{{ old('follow_up_plan', $session->follow_up_plan) }}</textarea>
                            @error('follow_up_plan') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </x-ui.form-section>

                <x-ui.form-section title="Kerahasiaan & Status" description="Tentukan siapa yang boleh melihat catatan ini.">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="confidentiality_level" class="block text-xs font-semibold text-ink-soft mb-1.5">Tingkat Kerahasiaan <span class="text-danger">*</span></label>
                            <x-ui.select name="confidentiality_level" id="confidentiality_level" :options="$confidentialityLevels" :selected="old('confidentiality_level', $session->confidentiality_level)" />
                            @error('confidentiality_level') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="status" class="block text-xs font-semibold text-ink-soft mb-1.5">Status <span class="text-danger">*</span></label>
                            <x-ui.select name="status" id="status" :options="['aktif' => 'Aktif', 'ditutup' => 'Ditutup']" :selected="old('status', $session->status)" />
                            @error('status') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </x-ui.form-section>

                <x-ui.form-section title="Lampiran" description="Ganti lampiran jika diperlukan.">
                    @if ($session->attachment_path)
                        <div class="mb-3 flex items-center gap-2 text-sm text-ink-soft">
                            <x-svg-paper-clip class="size-4" />
                            <span>Lampiran saat ini: <strong class="font-semibold text-ink">{{ basename($session->attachment_path) }}</strong></span>
                        </div>
                    @endif
                    <input type="file" name="attachment" id="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary file:mr-3 file:rounded-[var(--radius-control)] file:border-0 file:bg-primary-soft file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-primary-strong hover:file:bg-primary/10">
                    <p class="mt-1 text-xs text-ink-faint">PDF, JPG, PNG, DOC (maks 5MB). Kosongkan jika tidak ingin mengganti.</p>
                    @error('attachment') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </x-ui.form-section>

                <div class="flex items-center justify-end gap-3 border-t border-rule/70 px-5 py-4 sm:px-6">
                    <x-ui.button variant="ghost" href="{{ route('konseling.index') }}">Batal</x-ui.button>
                    <x-ui.button type="submit" variant="primary" icon="check">Perbarui Konseling</x-ui.button>
                </div>
            </x-ui.sheet>
        </form>
    </div>
</x-layouts.page>

@script
<script>
    document.getElementById('class_group_id').addEventListener('change', function() {
        const classId = this.value;
        const baseUrl = '{{ route("konseling.edit", $session) }}';

        if (!classId) {
            return;
        }

        window.location.href = baseUrl + '?class_group_id=' + classId;
    });
</script>
@endscript
