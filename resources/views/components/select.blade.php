@props(['name', 'label', 'options' => [], 'value' => null])

{{-- A dropdown styled to match <x-field>; options are value => label. h-11.5 pins its height to
     <x-field>'s (py-2.5 + 1.5rem line + border), since browsers size selects differently. --}}
<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="block text-sm font-semibold text-brand-900">{{ $label }}</label>
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        required
        @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror
        {{ $attributes->except('class')->class([
            'mt-1.5 block h-11.5 w-full rounded-lg border bg-white px-3.5 py-2.5 text-base shadow-xs outline-none transition focus:ring-4',
            'border-brand-950/15 focus:border-brand-700 focus:ring-brand-700/15' => ! $errors->has($name),
            'border-red-500 focus:border-red-600 focus:ring-red-500/15' => $errors->has($name),
        ]) }}
    >
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected(old($name, $value) === $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @error($name)
        <p id="{{ $name }}-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
