@props(['name', 'label', 'type' => 'text', 'optional' => false])

<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="block text-sm font-semibold text-brand-900">
        {{ $label }}
        @if ($optional)
            <span class="font-normal text-brand-700/70">(optional)</span>
        @endif
    </label>
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name) }}"
        @unless ($optional) required @endunless
        @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror
        {{ $attributes->except('class')->class([
            'mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-base shadow-xs outline-none transition placeholder:text-brand-950/35 focus:ring-4',
            'border-brand-950/15 focus:border-brand-700 focus:ring-brand-700/15' => ! $errors->has($name),
            'border-red-500 focus:border-red-600 focus:ring-red-500/15' => $errors->has($name),
        ]) }}
    >
    @error($name)
        <p id="{{ $name }}-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
