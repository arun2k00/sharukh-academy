{{-- Session feedback shared by every layout. --}}
@if (session('status'))
    <x-ui.alert variant="success" class="mb-6">{{ session('status') }}</x-ui.alert>
@endif

@if (session('error'))
    <x-ui.alert variant="danger" class="mb-6">{{ session('error') }}</x-ui.alert>
@endif

@if ($errors->any() && $errors->count() > 1)
    <x-ui.alert variant="danger" title="Please review the highlighted fields" class="mb-6">
        <ul class="mt-1 list-disc space-y-0.5 pl-4">
            @foreach ($errors->unique() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </x-ui.alert>
@endif
