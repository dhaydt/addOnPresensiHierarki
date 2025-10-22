# Fix CSRF Token Issue - Inertia.js Hierarchy

## Masalah yang Diperbaiki

**Error**: `SyntaxError: Unexpected token '<', "<!DOCTYPE "... is not valid JSON`

**Penyebab**: 
1. CSRF token tidak tersedia atau expired
2. API endpoint mengembalikan HTML error page alih-alih JSON response
3. Penggunaan native `fetch()` tidak menghandle CSRF token Laravel dengan baik

## Perbaikan yang Dilakukan

### 1. Menambahkan CSRF Meta Tag
**File**: `resources/views/app.blade.php`
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### 2. Menggunakan Inertia Router (Bukan Fetch)
**File**: `resources/js/Pages/Hierarchy/Index.vue`

**Sebelum**:
```javascript
const response = await fetch(url, {
  method,
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
  },
  body: JSON.stringify(data)
})
```

**Sesudah**:
```javascript
// Menggunakan Inertia router yang auto-handle CSRF
await router.post(url, data, {
  preserveState: true,
  preserveScroll: true,
  onSuccess: (page) => {
    router.reload({ only: ['hierarchyTree', 'stats'] })
  },
  onError: (errors) => {
    alert(errors.message || 'Terjadi kesalahan')
  }
})
```

### 3. Mengubah Controller Response Format
**File**: `app/Http/Controllers/InertiaHierarchyController.php`

**Sebelum**:
```php
return response()->json([
    'success' => true,
    'message' => 'Hierarki berhasil diperbarui',
    'hierarchyTree' => $hierarchyTree,
]);
```

**Sesudah**:
```php
return back()->with([
    'hierarchyTree' => $hierarchyTree,
    'message' => 'Hierarki berhasil diperbarui'
]);
```

### 4. Error Handling yang Lebih Baik
**Sebelum**:
```php
return response()->json([
    'success' => false,
    'message' => 'Error message'
], 400);
```

**Sesudah**:
```php
return back()->withErrors([
    'message' => 'Error message'
]);
```

### 5. Perbaikan Validasi dan Relationships
- Fixed foreign key references (`userinfo_id` konsisten)
- Added proper exception handling dengan Laravel Log
- Improved circular dependency detection

## Keuntungan Perbaikan

### ✅ **CSRF Protection**
- Automatic CSRF token handling via Inertia
- No manual token management required
- Secure from CSRF attacks

### ✅ **Better Error Handling**
- Proper Laravel error responses
- Consistent error format
- Better debugging capabilities

### ✅ **Inertia Best Practices**
- Using Inertia router instead of raw fetch
- Proper state preservation
- Better user experience

### ✅ **Performance**
- No full page reloads
- Selective data refreshing with `only` parameter
- Optimized network requests

## Testing

1. **Login**: Akses `/test-login` untuk auto-login
2. **Access**: Buka `/hierarchy` 
3. **Drag & Drop**: Test drag pegawai ke posisi baru
4. **No More Errors**: Tidak ada lagi error CSRF atau JSON parsing

## Technical Notes

### CSRF Token Flow
1. Laravel generates CSRF token saat page load
2. Token disimpan di meta tag `<meta name="csrf-token">`
3. Inertia router automatically mengambil token dari meta tag
4. Token dikirim di header setiap request
5. Laravel middleware memvalidasi token

### Inertia Response Cycle
1. User action (drag & drop)
2. Inertia router sends POST/DELETE request dengan CSRF token
3. Controller process request 
4. Controller returns `back()->with()` response
5. Inertia updates page state
6. Vue component receives updated props
7. UI updates automatically

### Error Handling
- **Validation errors**: `back()->withErrors()`
- **Success responses**: `back()->with()`
- **Exception handling**: Try-catch dengan logging
- **Frontend errors**: Inertia `onError` callback

## Kesimpulan

Perbaikan ini menyelesaikan masalah CSRF token dan mengoptimalkan arsitektur aplikasi dengan:

1. **Security**: Proper CSRF protection
2. **Reliability**: Better error handling  
3. **Performance**: Efficient state updates
4. **Maintainability**: Clean code structure
5. **User Experience**: Smooth interactions tanpa page refresh

Sekarang sistem drag-and-drop hierarchy berjalan dengan smooth tanpa error CSRF atau parsing JSON! 🎉
