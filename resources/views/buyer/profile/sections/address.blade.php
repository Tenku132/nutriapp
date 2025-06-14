<div class="card shadow-sm rounded-3">
    <div class="card-header bg-success text-white fw-bold">
        Address Information
    </div>
    <div class="card-body">
        @if (session('address_success'))
            <div class="alert alert-success">{{ session('address_success') }}</div>
        @endif

        <form action="{{ route('buyer.profile.updateAddress') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="address" class="form-label">Current Address</label>
                <textarea name="address" id="address" class="form-control" rows="3" required>{{ old('address', $user->address) }}</textarea>
            </div>
            <button type="submit" class="btn btn-success">Update Address</button>
        </form>
    </div>
</div>
