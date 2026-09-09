@props(['action', 'confirm' => 'This cannot be undone. Continue?', 'label' => 'Delete'])

<form method="POST" action="{{ $action }}" onsubmit="return confirm('{{ $confirm }}')" {{ $attributes }}>
    @csrf
    @method('DELETE')
    <button type="submit" class="text-[0.8125rem] text-ink-500 hover:text-red-700">{{ $label }}</button>
</form>
