# Fix Level 0 to Level 0 Drag & Drop Issue

## Masalah yang Diperbaiki

**Problem**: Level 0 employees (top-level) tidak bisa di-drag ke level 0 employee lainnya untuk membuat hierarki baru.

**Scenario**: 
- Employee A (level 0) ingin dijadikan bawahan Employee B (level 0)
- Drag Employee A ke Employee B seharusnya membuat A menjadi subordinate B
- Tapi drag-drop tidak berfungsi atau tidak memberikan feedback visual yang jelas

## Root Causes

1. **Visual Feedback Kurang**: Drop zone tidak jelas saat drag antar level 0
2. **Event Handling**: Drag events tidak ter-handle dengan baik
3. **Debug Info**: Tidak ada logging untuk troubleshoot masalah drag-drop

## Perbaikan yang Dilakukan

### 1. Enhanced Visual Feedback
**File**: `resources/js/Components/HierarchyItem.vue`

**Ditambahkan**:
```vue
<!-- Drop Zone Before Item (for reordering) -->
<div
  v-if="isDragOver && !isDragging"
  class="h-1 bg-blue-500 rounded-full mx-4 mb-2 opacity-75"
></div>

<!-- Enhanced drag events -->
@dragover.prevent="handleDragOver"
@dragenter.prevent="handleDragEnter" 
@dragleave="handleDragLeave"

:class="{
  'ring-2 ring-blue-500 bg-blue-50': isDragOver && !isDragging,
}"
```

### 2. Better Event Handling

**Sebelum**:
```javascript
@dragover.prevent
@dragenter.prevent
```

**Sesudah**:
```javascript
const handleDragOver = (event) => {
  event.preventDefault()
  if (!isDragging.value) {
    isDragOver.value = true
  }
}

const handleDragEnter = (event) => {
  event.preventDefault()
  if (!isDragging.value) {
    isDragOver.value = true
  }
}

const handleDragLeave = (event) => {
  // Only hide if we're actually leaving the element
  if (!event.currentTarget.contains(event.relatedTarget)) {
    isDragOver.value = false
  }
}
```

### 3. Debug Logging

**File**: `resources/js/Pages/Hierarchy/Index.vue`

```javascript
const handleMoveEmployee = async (draggedId, targetId) => {
  console.log('handleMoveEmployee called:', { draggedId, targetId })
  await performMove(draggedId, targetId, 'Memindahkan pegawai...')
}

const performMove = async (employeeId, newSuperiorId, message) => {
  console.log('performMove called:', { employeeId, newSuperiorId })
  
  const draggedEmployee = findEmployeeInHierarchy(props.hierarchyTree, employeeId)
  const targetEmployee = newSuperiorId ? findEmployeeInHierarchy(props.hierarchyTree, newSuperiorId) : null

  console.log('Found employees:', { draggedEmployee, targetEmployee })
}
```

**File**: `resources/js/Components/HierarchyItem.vue`

```javascript
const handleDrop = (event) => {
  const draggedId = event.dataTransfer.getData('text/plain')
  const targetId = props.item.id
  
  console.log('Drop event:', { draggedId, targetId })
  
  if (draggedId !== targetId) {
    console.log('Emitting move-employee:', { draggedId, targetId })
    emit('move-employee', { draggedId, targetId })
  }
}
```

## Visual Improvements

### 1. Drop Zone Indicators
- **Blue line** muncul di atas target saat dragging
- **Blue ring + background** pada target item
- **Opacity reduction** pada item yang sedang di-drag

### 2. Better Drag States
- `isDragging`: Item yang sedang di-drag (opacity 50%)
- `isDragOver`: Target drop zone (blue ring + background)
- `isHighlighted`: Additional highlighting when needed

### 3. Smooth Transitions
```css
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}
```

## How Level 0 → Level 0 Works

### Scenario: Drag Employee A to Employee B (both level 0)

1. **Drag Start**: Employee A gets `opacity-50` dan `isDragging: true`
2. **Drag Over Employee B**: Employee B shows blue ring + background
3. **Drop**: Event handler emits `move-employee` dengan `{ draggedId: A, targetId: B }`
4. **API Call**: POST `/hierarchy/move-employee` dengan `employeeId: A, newSuperiorId: B`
5. **Result**: Employee A becomes subordinate of Employee B
6. **UI Update**: Page redirects dan Employee A muncul di bawah Employee B

## Debug Process

Untuk debug masalah drag-drop level 0:

### 1. Open Browser Console
```
F12 → Console tab
```

### 2. Test Drag & Drop
```
1. Drag Employee A (level 0)
2. Hover over Employee B (level 0)  
3. Drop
```

### 3. Check Console Logs
```
Drop event: { draggedId: "123", targetId: "456" }
handleMoveEmployee called: { draggedId: "123", targetId: "456" }
performMove called: { employeeId: "123", newSuperiorId: "456" }
Found employees: { draggedEmployee: {...}, targetEmployee: {...} }
User confirmed, sending API call...
```

### 4. Check for Errors
- CSRF token issues
- Validation errors
- Network failures
- Controller errors

## Expected Behavior

### ✅ **Level 0 → Level 0 Drag**
- Employee A (level 0) dragged to Employee B (level 0)
- Result: A becomes subordinate of B
- B becomes level 0, A becomes level 1

### ✅ **Level 0 → Top Drop Zone**
- Employee A dragged to empty area
- Result: A stays level 0 (no superior)

### ✅ **Level 0 → Any Level**
- Employee A dragged to any employee
- Result: A becomes subordinate of target

## Troubleshooting

### Problem: No Visual Feedback
**Solution**: Check CSS classes dan drag event handlers

### Problem: Drop Not Working  
**Solution**: Check console logs untuk event flow

### Problem: API Errors
**Solution**: Check Laravel logs dan network tab

### Problem: Wrong Hierarchy
**Solution**: Check controller logic dan database state

## Conclusion

Dengan perbaikan ini, drag-and-drop antar level 0 employees sekarang:

1. **Visual Clear**: Drop zones dan feedback jelas
2. **Responsive**: Event handling yang proper
3. **Debuggable**: Console logs untuk troubleshooting
4. **Functional**: Level 0 → Level 0 drag works as expected

User sekarang bisa dengan mudah membuat hierarki baru dengan drag level 0 employees ke level 0 lainnya! 🎉
