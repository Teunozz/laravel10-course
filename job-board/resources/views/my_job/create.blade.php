<x-layout>
    <x-breadcrumbs class="mb-4"  :links="['My Jobs' => route('my-jobs.index'), 'Create' => '#']" />

    <x-card>
        <form action="{{ route('my-jobs.store') }}" method="POST">
            @csrf

            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <x-label for="title" :required="true">Job Title</x-label>
                    <x-text-input name="title" type="text" />
                </div>

                <div>
                    <x-label for="location" :required="true">Location</x-label>
                    <x-text-input name="location" type="text" />
                </div>

                <div class="col-span-2">
                    <x-label for="salary" :required="true">Salary</x-label>
                    <x-text-input name="salary" type="number" />
                </div>

                <div class="col-span-2">
                    <x-label for="description" :required="true">Description</x-label>
                    <x-text-input name="description" type="textarea" />
                </div>

                <div>
                    <x-label for="experience">Experience</x-label>
                    <x-radio-group name="experience" :value="old('experience')" 
                        :all-option="false" :required="true" :options="array_combine(array_map('ucfirst', \App\Models\Job::$experience), \App\Models\Job::$experience)" />
                </div>

                <div>
                    <x-label for="category">Category</x-label>
                    <x-radio-group name="category" :value="old('category')" :all-option="false" :required="true" :options="\App\Models\Job::$category" /> 
                </div>
            </div>

            <x-button class="w-full">Create Job</x-button>
        </form>
    </x-card>
</x-layout>