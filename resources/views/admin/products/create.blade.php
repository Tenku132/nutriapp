@extends('layouts.admin')

@section('title', 'Add New Product')

@section('content')
<div class="container py-4">
    <h4 class="mb-4" style="color: white;">Add New Product</h4>
    <div class="card p-4 shadow-sm">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="user_id" class="form-label">Select Farmer</label>
                <select name="user_id" id="user_id" class="form-select" required>
                    <option value="">-- Choose a farmer --</option>
                    @foreach ($farmers as $farmer)
                        <option value="{{ $farmer->id }}">{{ $farmer->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label for="price" class="form-label">Price (₱)</label>
                    <input type="number" name="price" class="form-control" step="0.01" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4"></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="image" class="form-label">Product Image</label>
                    <input type="file" name="image" class="form-control">
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    💾 Save Product
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("form");
        const inputs = form.querySelectorAll("input[required], textarea[required]");

        inputs.forEach(input => {
            input.addEventListener("input", () => {
                validateField(input);
            });
        });

        form.addEventListener("submit", function (e) {
            let hasError = false;

            inputs.forEach(input => {
                if (!validateField(input)) {
                    hasError = true;
                }
            });

            if (hasError) {
                e.preventDefault();
            }
        });

        function validateField(field) {
            const error = field.nextElementSibling;
            if (field.value.trim() === "") {
                showError(field, "This field is required.");
                return false;
            } else if (field.name === "price" && (isNaN(field.value) || parseFloat(field.value) <= 0)) {
                showError(field, "Enter a valid price greater than 0.");
                return false;
            } else if (field.name === "stock" && (isNaN(field.value) || parseInt(field.value) < 0)) {
                showError(field, "Stock must be a positive number.");
                return false;
            } else {
                clearError(field);
                return true;
            }
        }

        function showError(field, message) {
            clearError(field);
            const error = document.createElement("div");
            error.className = "text-danger small mt-1 validation-error";
            error.innerText = message;
            field.classList.add("is-invalid");
            field.parentNode.appendChild(error);
        }

        function clearError(field) {
            field.classList.remove("is-invalid");
            const next = field.parentNode.querySelector(".validation-error");
            if (next) next.remove();
        }
    });
</script>
@endpush

@endsection
