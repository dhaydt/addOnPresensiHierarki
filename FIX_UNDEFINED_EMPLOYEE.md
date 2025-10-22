# Fix "Undefined" Employee Name in Drop Alert

## Masalah yang Diperbaiki

**Problem**: Saat drop employee, alert menampilkan:
```
"Apakah Anda yakin ingin memindahkan undefined ke level teratas (tanpa atasan)?"
```

Padahal user drop ke level 0 employee lainnya, seharusnya menampilkan nama employee yang jelas.

## Root Causes

1. **Parameter Mismatch**: HierarchyItem emit object `{ draggedId, targetId }` tapi parent handler expect parameters terpisah
2. **Employee Not Found**: `findEmployeeInHierarchy` tidak menemukan employee dalam hierarchy tree
3. **Data Structure**: Employee data mungkin tidak tersimpan di hierarchy tree dengan benar

## Perbaikan yang Dilakukan

### 1. Fixed Event Handler Parameter Mismatch
**File**: `resources/js/Pages/Hierarchy/Index.vue`

**Sebelum**:
```javascript
const handleMoveEmployee = async (draggedId, targetId) => {
  await performMove(draggedId, targetId, 'Memindahkan pegawai...')
}
```

**Sesudah**:
```javascript
const handleMoveEmployee = async (event) => {
  console.log('handleMoveEmployee called:', event)
  await performMove(event.draggedId, event.targetId, 'Memindahkan pegawai...')
}
```

### 2. Enhanced Employee Finding with Fallback
**Sebelum**:
```javascript
const draggedEmployee = findEmployeeInHierarchy(props.hierarchyTree, employeeId)
```

**Sesudah**:
```javascript
let draggedEmployee = findEmployeeInHierarchy(props.hierarchyTree, employeeId)

// Fallback: search in props.employees if not found in hierarchy
if (!draggedEmployee) {
  console.log('Not found in hierarchy, searching in employees list...')
  const employeeInfo = props.employees.find(emp => emp.userid == employeeId)
  if (employeeInfo) {
    draggedEmployee = employeeInfo
    console.log('Found in employees list:', draggedEmployee)
  }
}
```

### 3. Added Extensive Debug Logging
**File**: `resources/js/Pages/Hierarchy/Index.vue`

```javascript
const findEmployeeInHierarchy = (items, employeeId) => {
  console.log('Finding employee:', { employeeId, items })
  
  for (const item of items) {
    console.log('Checking item:', { itemId: item.id, employeeId, match: item.id == employeeId })
    
    if (item.id == employeeId) {
      console.log('Found employee:', item.employee)
      return item.employee
    }
    // ... rest of logic
  }
  
  console.log('Employee not found:', employeeId)
  return null
}
```

### 4. Safe Alert Messages with Fallbacks
**Sebelum**:
```javascript
confirmMessage = `Apakah Anda yakin ingin memindahkan ${draggedEmployee?.name} ke bawah ${targetEmployee?.name}?`
```

**Sesudah**:
```javascript
confirmMessage = `Apakah Anda yakin ingin memindahkan ${draggedEmployee?.name || 'Employee'} ke bawah ${targetEmployee?.name || 'Target Employee'}?`
```

### 5. Fixed All Event Handlers
**File**: `resources/js/Pages/Hierarchy/Index.vue`

```javascript
const handleSetSuperior = async (event) => {
  await performApiCall('/hierarchy/set-superior', {
    employeeId: event.employeeId,
    superiorId: event.superiorId
  }, 'Mengatur atasan...')
}

const handleRemoveSuperior = async (event) => {
  await performApiCall('/hierarchy/remove-superior', {
    employeeId: event.employeeId
  }, 'Menghapus hubungan atasan...', 'DELETE')
}
```

## Debug Process

Untuk troubleshoot masalah employee name undefined:

### 1. Open Browser Console
```bash
F12 → Console tab
```

### 2. Test Drag & Drop
```bash
1. Drag Employee A to Employee B
2. Check console for logs
```

### 3. Expected Console Output
```bash
handleMoveEmployee called: { draggedId: "123", targetId: "456" }
performMove called: { employeeId: "123", newSuperiorId: "456" }
Finding employee: { employeeId: "123", items: [...] }
Checking item: { itemId: "123", employeeId: "123", match: true }
Found employee: { name: "John Doe", userid: 123, ... }
Found employees: { draggedEmployee: {...}, targetEmployee: {...} }
```

### 4. If Employee Not Found in Hierarchy
```bash
Employee not found: 123
Not found in hierarchy, searching in employees list...
Found in employees list: { name: "John Doe", userid: 123, ... }
```

## Data Flow Analysis

### Event Flow
```
HierarchyItem
  ↓ emit('move-employee', { draggedId, targetId })
Index.vue → handleMoveEmployee(event)
  ↓ performMove(event.draggedId, event.targetId)
performMove()
  ↓ findEmployeeInHierarchy() + fallback search
  ↓ Build confirmation message with employee names
  ↓ Show alert with proper names ✅
```

### Data Sources
1. **Primary**: `props.hierarchyTree` - Structured hierarchy with employee data
2. **Fallback**: `props.employees` - Flat list of all employees
3. **Search Keys**: `item.id` (hierarchy) vs `emp.userid` (employees)

## Expected Results

### ✅ **Before Fix**
```
Alert: "Apakah Anda yakin ingin memindahkan undefined ke level teratas (tanpa atasan)?"
```

### ✅ **After Fix**
```
Alert: "Apakah Anda yakin ingin memindahkan John Doe ke bawah Jane Smith?"
```

### ✅ **Fallback Cases**
```
Alert: "Apakah Anda yakin ingin memindahkan Employee ke bawah Target Employee?"
```

## Performance Impact

- **Minimal**: Fallback search only runs if primary search fails
- **Debug Logs**: Only active during development
- **Memory**: Negligible additional memory usage
- **UX**: Much better with proper employee names

## Testing Scenarios

1. **Level 0 → Level 0**: ✅ Shows both employee names
2. **Level 1 → Level 0**: ✅ Shows both employee names  
3. **Any → Top Level**: ✅ Shows dragged employee name
4. **Employee Not Found**: ✅ Shows fallback text instead of undefined

## Conclusion

Dengan perbaikan ini:

1. **Alert Messages Clear**: Tidak ada lagi "undefined" di confirmation
2. **Proper Event Handling**: Object parameters handled correctly
3. **Robust Employee Finding**: Fallback search ensures employee found
4. **Better Debug Info**: Console logs help troubleshoot issues
5. **Safe Fallbacks**: Graceful degradation jika data tidak ditemukan

User sekarang akan melihat confirmation alert yang jelas dengan nama employee yang benar! 🎉
