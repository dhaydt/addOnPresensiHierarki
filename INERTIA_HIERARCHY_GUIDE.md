# Implementasi Inertia.js untuk Hierarki Pegawai

## Overview

Implementasi ini menyediakan solusi drag-and-drop hierarki pegawai yang smooth menggunakan Inertia.js + Vue.js 3 sebagai alternatif dari Livewire yang ada.

## Struktur File

### Backend (Laravel)

#### 1. Controller
```
app/Http/Controllers/InertiaHierarchyController.php
```
- `index()`: Menampilkan halaman utama dengan data hierarki
- `moveEmployee()`: API untuk memindahkan pegawai  
- `setSuperior()`: API untuk mengatur atasan
- `removeSuperior()`: API untuk menghapus hubungan atasan
- `buildHierarchyTree()`: Membangun struktur tree hierarki
- `wouldCreateCircularDependency()`: Validasi circular dependency

#### 2. Routes
```
routes/web.php
```
Routes dengan prefix `/hierarchy`:
- GET `/hierarchy` - Halaman utama
- POST `/hierarchy/move-employee` - Pindah pegawai
- POST `/hierarchy/set-superior` - Set atasan  
- DELETE `/hierarchy/remove-superior` - Hapus atasan

#### 3. Middleware
```
app/Http/Middleware/HandleInertiaRequests.php
```
Middleware Inertia untuk sharing data global.

### Frontend (Vue.js 3)

#### 1. Layout
```
resources/views/app.blade.php
```
Root layout dengan Vite integration, Tailwind CSS, FontAwesome.

#### 2. Main Page Component  
```
resources/js/Pages/Hierarchy/Index.vue
```
- Stats cards menampilkan total pegawai
- Search functionality
- Drag-and-drop tree interface
- Loading states dan error handling
- Real-time updates setelah drag-drop

#### 3. Reusable Components

**StatsCard.vue**
```
resources/js/Components/StatsCard.vue
```
- Komponen card untuk menampilkan statistik
- Mendukung berbagai warna (blue, green, orange, red, purple)
- Icon dan formatting angka

**HierarchyItem.vue**  
```
resources/js/Components/HierarchyItem.vue
```
- Komponen item pegawai dengan drag-drop capability
- Recursive rendering untuk subordinates
- Visual feedback saat dragging
- Action menu per item
- Circular dependency prevention

### Configuration

#### 1. Vite Config
```
vite.config.js
```
- Vue 3 plugin integration
- Path alias `@` ke `resources/js`
- Asset handling

#### 2. App.js
```
resources/js/app.js
```  
- Vue 3 + Inertia setup
- Component auto-resolution
- Progress indicator

## Features

### 1. Drag and Drop
- **Multi-level dragging**: Pegawai bisa di-drag ke level mana pun
- **Visual feedback**: Highlight saat dragging dan drop zones
- **Top-level drop**: Drop ke area kosong untuk remove superior
- **Circular prevention**: Validasi mencegah circular dependency

### 2. Real-time Updates
- **API calls**: Menggunakan native fetch untuk API calls
- **State management**: Update hierarchy setelah successful API calls  
- **Error handling**: Alert untuk error cases
- **Loading states**: Loading overlay dengan messages

### 3. Search & Filter
- **Live search**: Filter pegawai berdasarkan nama
- **Recursive filtering**: Pencarian di semua level hierarchy
- **Highlight matches**: Highlight hasil pencarian

### 4. Responsive UI
- **Tailwind CSS**: Modern, responsive styling
- **FontAwesome icons**: Consistent iconography
- **Stats dashboard**: Card-based statistics display
- **Loading indicators**: Smooth loading experience

## Keuntungan vs Livewire

### 1. Performance
- **No DOM re-rendering**: Vue manages state efficiently
- **Faster interactions**: Client-side state updates
- **Better UX**: Smooth drag-drop tanpa server round-trips

### 2. Developer Experience  
- **Component separation**: Cleaner code organization
- **Type safety**: Better IDE support dengan Vue 3
- **Modern tooling**: Vite hot reloading, Vue DevTools

### 3. Scalability
- **SPA-like experience**: Page transitions tanpa full reload
- **Component reusability**: Components bisa digunakan di halaman lain
- **API-first approach**: Backend bisa digunakan untuk mobile apps

## API Endpoints

### GET /hierarchy
Response:
```json
{
  "hierarchyTree": [...],
  "employees": [...], 
  "stats": {
    "totalEmployees": 6,
    "withSuperior": 5,
    "withoutSuperior": 1
  },
  "department": {...},
  "user": {...}
}
```

### POST /hierarchy/move-employee
Request:
```json
{
  "employeeId": 1004,
  "newSuperiorId": 1003
}
```

Response:
```json
{
  "success": true,
  "message": "Pegawai berhasil dipindahkan",
  "hierarchyTree": [...]
}
```

## Installation Steps

1. **Install Dependencies**
```bash
composer require inertiajs/inertia-laravel
php artisan inertia:middleware
npm install vue@^3.3.0 @inertiajs/vue3 @vitejs/plugin-vue
```

2. **Configure Vite**
Update `vite.config.js` dengan Vue plugin dan alias.

3. **Setup Middleware**
Add `HandleInertiaRequests` ke `bootstrap/app.php`.

4. **Create Components**
Buat Vue components di `resources/js/Pages/` dan `resources/js/Components/`.

5. **Build Assets**
```bash
npm run build
# atau untuk development
npm run dev
```

## Usage

1. **Access**: Buka `/hierarchy` setelah login
2. **Drag**: Drag pegawai dari grip handle (ikon ⋮⋮)
3. **Drop**: Drop ke pegawai lain untuk set sebagai bawahan
4. **Top-level**: Drop ke area kosong untuk remove superior
5. **Search**: Gunakan search box untuk filter pegawai
6. **Actions**: Klik menu (⋯) untuk actions per pegawai

## Error Handling

- **Circular dependency**: Automatic validation dengan user feedback
- **API errors**: Alert messages untuk error responses  
- **Loading states**: Visual indicators selama API calls
- **Network issues**: Error handling untuk network failures

## Future Enhancements

1. **Drag animations**: Smooth drag animations dengan libraries seperti Vue Draggable
2. **Undo/Redo**: Action history untuk undo changes
3. **Bulk operations**: Select multiple employees untuk bulk actions
4. **Export**: Export hierarchy ke PDF/Excel
5. **Real-time sync**: WebSocket integration untuk multi-user updates

## Testing

Untuk testing implementasi:

1. **Setup data**: Jalankan `php artisan migrate:fresh --seed`
2. **Login**: Akses `/test-login` untuk login otomatis
3. **Test drag-drop**: Coba drag berbagai pegawai ke posisi berbeda
4. **Test search**: Coba search functionality
5. **Test validation**: Coba drag yang akan membuat circular dependency

## Troubleshooting

### Build Issues
- Pastikan Vue 3 installed: `npm list vue`
- Clear cache: `npm run build` dan `php artisan view:clear`

### Component Issues  
- Check browser console untuk Vue errors
- Verify component imports dan naming conventions
- Ensure all props passed correctly

### API Issues
- Check network tab untuk API responses
- Verify CSRF token dalam requests  
- Check Laravel logs: `tail -f storage/logs/laravel.log`

## Conclusion

Implementasi Inertia.js ini menyediakan pengalaman drag-and-drop yang jauh lebih smooth dibanding Livewire karena:

1. **No DOM re-rendering issues**: Vue menangani state secara efisien
2. **Better performance**: Client-side interactions lebih responsive  
3. **Modern architecture**: API-first approach lebih scalable
4. **Better UX**: Smooth animations dan instant feedback

Dengan implementasi ini, user mendapat pengalaman drag-and-drop yang natural dan responsive tanpa masalah re-initialization yang ada di versi Livewire.
