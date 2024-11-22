@error($inputName)
    @foreach ($errors->get($inputName) as $message)
        <span class="error">{{ $message }}</span>
    @endforeach
@enderror
