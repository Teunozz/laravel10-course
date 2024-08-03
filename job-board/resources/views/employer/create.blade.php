<x-layout>
    <x-card>
        <form action="{{ route('employer.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <x-label for="company_name" :required="true">Company Name</x-label>
                <x-text-input name="company_name" type="text" />
            </div>

            <x-button class="w-full bg-green-50">Create</x-button>
        </form>
    </x-card>
</x-layout>