@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="container py-4">
    <h4 class="mb-4 text-white">Edit Product</h4>
    <div class="card p-4 shadow-sm">
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Update failed:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Price (₱)</label>
                    <input type="number" name="price" class="form-control" step="0.01" value="{{ $product->price }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-control" value="{{ $product->stock }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2" required>{{ $product->description }}</textarea>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Current Image</label><br>
                    <img src="{{ asset('storage/' . $product->image) }}" class="img-thumbnail" width="150">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Upload New Image</label>
                    <input type="file" name="image" class="form-control">
                    <small class="text-muted">Leave blank if you don't want to change the image.</small>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.products.index') }}" class="btn btn-reset">Cancel</a>
                <button type="submit" class="btn btn-add-product">
                    💾 Update Product
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
