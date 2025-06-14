<div class="mb-3" id="template-name-wrapper" style="display: none;">
  <label for="template-name" class="form-label fw-bold">Product Name</label>
  <select id="template-name" class="form-select">
    <option value="">Select product name</option>
  </select>
</div>

<div class="mb-3" id="manual-name-wrapper" style="display: none;">
  <label for="manual-name" class="form-label fw-bold">Enter Product Name</label>
  <input type="text" class="form-control" name="name" id="manual-name"
         placeholder="Enter product name manually">
</div>

<div class="row">
  <div class="col-md-4 mb-3">
    <label for="price" class="form-label fw-bold">Price (₱) <span class="text-danger">*</span></label>
    <input type="number" class="form-control" name="price" id="price"
           value="{{ old('price', $product->price ?? '') }}" step="0.01" min="0" required
           placeholder="e.g. 99.00">
  </div>
  <div class="col-md-4 mb-3">
    <label for="stock" class="form-label fw-bold">Stock (kilo) <span class="text-danger">*</span></label>
    <input type="number" class="form-control" name="stock" id="stock"
           value="{{ old('stock', $product->stock ?? '') }}" min="0" required
           placeholder="Enter available stock">
  </div>
  <div class="col-md-4 mb-3">
    <label for="category" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
    <select class="form-select" name="category" id="category" required>
      <option value="">Select category</option>
      @foreach ($categories as $category)
        <option value="{{ $category }}"
          {{ (old('category', $product->category ?? '') == $category) ? 'selected' : '' }}>
          {{ $category }}
        </option>
      @endforeach
    </select>
  </div>
</div>

<div class="mb-3">
  <label for="province" class="form-label">Province <span class="text-danger">*</span></label>
  <input type="text" id="province" name="province" class="form-control"
         value="{{ old('province', $product->province ?? '') }}" required>
</div>

<div class="mb-3">
  <label for="city" class="form-label">City <span class="text-danger">*</span></label>
  <input type="text" id="city" name="city" class="form-control"
         value="{{ old('city', $product->city ?? '') }}" required>
</div>

<div class="mb-3">
  <label for="harvested_at" class="form-label fw-bold">Harvest Date <span class="text-danger">*</span></label>
  <input type="date" class="form-control" name="harvested_at" id="harvested_at"
         value="{{ old('harvested_at', $product->harvested_at ?? '') }}">
</div>

<div class="mb-3">
  <label for="ripeness" class="form-label fw-bold">Ripeness <span class="text-danger">*</span></label>
  
    <div class="mb-3">
      <select name="ripeness" id="ripeness" class="form-select">
        <option value="">Select ripeness level</option>
        @php
          $ripenessOptions = ['Unripe', 'Partially Ripe', 'Ripe', 'Overripe'];
        @endphp
        @foreach ($ripenessOptions as $option)
          <option value="{{ $option }}" {{ old('ripeness', $product->ripeness ?? '') == $option ? 'selected' : '' }}>
            {{ $option }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="row">
    <div class="col-md-6 mb-3">
      <label for="shelf_life" class="form-label fw-bold">Shelf Life</label>
      <input type="text" class="form-control" name="shelf_life" id="shelf_life"
            value="{{ old('shelf_life', $product->shelf_life ?? '') }}"
            placeholder="e.g. 7 days, 2 weeks, 1 month">
    </div>
    <div class="col-md-6 mb-3">
      <label for="storage" class="form-label fw-bold">Storage Instructions</label>
      <input type="text" class="form-control" name="storage" id="storage"
            value="{{ old('storage', $product->storage ?? '') }}"
            placeholder="e.g. Keep refrigerated, store in cool dry place">
    </div>
  </div>


</div>

<div class="mb-3">
  <label for="description" class="form-label fw-bold">Description</label>
  <textarea class="form-control" name="description" id="description" rows="3"
            placeholder="Add short details about the product">{{ old('description', $product->description ?? '') }}</textarea>
</div>

<div class="mb-3">
  <label for="image" class="form-label fw-bold">Product Image <span class="text-danger">*</span></label>
  <input class="form-control" type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)">
  <div class="mt-2">
    <img id="image-preview"
         src="{{ !empty($product) && $product->image ? asset('storage/' . $product->image) : '#' }}"
         alt="Image preview"
         style="max-height: 100px; border: 1px solid #ccc; {{ empty($product) || !$product->image ? 'display: none;' : '' }}">
  </div>
</div>

@if ($errors->any())
  <div class="alert alert-danger mt-3">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

@push('scripts')
<script>
function previewImage(event) {
  const input = event.target;
  const preview = document.getElementById('image-preview');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  }
}

document.addEventListener('DOMContentLoaded', function () {
  const categorySelect = document.getElementById('category');
  const templateWrapper = document.getElementById('template-name-wrapper');
  const manualWrapper = document.getElementById('manual-name-wrapper');
  const templateSelect = document.getElementById('template-name');
  const manualInput = document.getElementById('manual-name');
  const dependentFields = [
  'price', 'stock', 'province', 'city', 'ripeness',
  'harvested_at', 'description', 'image',
  'shelf_life', 'storage'
];


  function setFieldsEnabled(enabled) {
    dependentFields.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.disabled = !enabled;
    });
  }

  function resetNameInputs() {
    const existingHidden = document.getElementById('hidden-product-name');
    if (existingHidden) existingHidden.remove();
    manualInput.name = '';
  }

  categorySelect.addEventListener('change', function () {
    const category = this.value;
    setFieldsEnabled(false);
    resetNameInputs();
    templateWrapper.style.display = 'none';
    manualWrapper.style.display = 'none';
    templateSelect.innerHTML = '';

    if (!category) return;

    fetch(`/farmer/products/templates/${category}`)
      .then(response => response.json())
      .then(data => {
        if (data.length > 0) {
          templateWrapper.style.display = 'block';
          templateSelect.innerHTML = '<option value="">Select product name</option>';
          data.forEach(name => {
            const opt = document.createElement('option');
            opt.value = name;
            opt.text = name;
            templateSelect.appendChild(opt);
          });
          templateSelect.appendChild(new Option('Other', 'other'));
        } else {
          manualWrapper.style.display = 'block';
          manualInput.name = 'name';
          setFieldsEnabled(true);
        }
      });
  });

  templateSelect.addEventListener('change', function () {
    resetNameInputs();

    if (this.value === 'other') {
      manualWrapper.style.display = 'block';
      manualInput.name = 'name';
      setFieldsEnabled(true);
    } else if (this.value) {
      manualWrapper.style.display = 'none';
      const hidden = document.createElement('input');
      hidden.type = 'hidden';
      hidden.name = 'name';
      hidden.id = 'hidden-product-name';
      hidden.value = this.value;
      this.closest('form').appendChild(hidden);
      setFieldsEnabled(true);
    } else {
      setFieldsEnabled(false);
    }
  });

  // Run category fetch if value is pre-selected (edit form)
  if (categorySelect.value) {
    const event = new Event('change');
    categorySelect.dispatchEvent(event);
  } else {
    setFieldsEnabled(false);
  }
});
</script>
@endpush
