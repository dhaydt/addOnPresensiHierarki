# Debug: False "Level Teratas" Alert Issue

## Masalah yang Didebug

**Problem**: Ketika drop level 0 employee ke level 1 employee, muncul alert "Apakah Anda yakin ingin memindahkan Employee ke level teratas (tanpa atasan)?" padahal seharusnya ada target/atasan.

**Expected**: Alert seharusnya "Apakah Anda yakin ingin memindahkan [Name] ke bawah [Target Name]?"

## Debug Strategy

### 1. Enhanced Logging
**File**: `resources/js/Pages/Hierarchy/Index.vue`

Ditambahkan comprehensive logging untuk trace issue:

```javascript
// In handleMoveEmployee
console.log('handleMoveEmployee called with event:', event)
console.log('event.draggedId:', event.draggedId, 'event.targetId:', event.targetId)

// In performMove  
console.log('performMove called with:', { 
  employeeId: employeeId, 
  newSuperiorId: newSuperiorId,
  employeeIdType: typeof employeeId,
  newSuperiorIdType: typeof newSuperiorId
})

console.log('newSuperiorId check:', { 
  newSuperiorId: newSuperiorId, 
  isNull: newSuperiorId === null,
  isUndefined: newSuperiorId === undefined,
  isFalsy: !newSuperiorId,
  type: typeof newSuperiorId
})

console.log('Using "ke bawah" message because newSuperiorId exists:', newSuperiorId)
// OR
console.log('Using "level teratas" message because newSuperiorId is falsy:', newSuperiorId)
```

### 2. Parameter Validation
Ditambahkan validation untuk memastikan data yang diterima valid:

```javascript
// Validate that we have both IDs
if (!event.draggedId) {
  console.error('No draggedId provided')
  return
}

if (!event.targetId) {
  console.error('No targetId provided - this should not happen for regular drops')
  return
}

// Validate parameters in performMove
if (!employeeId) {
  console.error('performMove: employeeId is missing or invalid')
  alert('Error: Employee ID tidak valid')
  return
}
```

## Potential Root Causes

### 1. Parameter Passing Issue
**Possible**: HierarchyItem sends `{ draggedId, targetId }` but somewhere in the chain, `targetId` becomes `null`

**Check**: 
```javascript
// HierarchyItem.vue
emit('move-employee', { draggedId, targetId })

// Index.vue  
handleMoveEmployee(event) → performMove(event.draggedId, event.targetId)
```

### 2. Data Type Mismatch
**Possible**: `targetId` is string but comparison expects number, or vice versa

**Check**: Console will show `typeof` for both IDs

### 3. Circular Dependency Check
**Possible**: `wouldCreateCircularDependency()` might be preventing the action

**Check**: HierarchyItem logs will show if circular dependency is detected

### 4. Event Propagation Issue
**Possible**: Multiple drop events atau event data corruption

**Check**: HierarchyItem drop event logs should show clean data

## Debug Process

### Step 1: Open Console
```
F12 → Console tab
Clear console logs
```

### Step 2: Perform Drag & Drop
```
1. Drag Level 0 Employee (e.g., "John Manager")
2. Drop on Level 1 Employee (e.g., "Bob Employee") 
3. Watch console logs
```

### Step 3: Analyze Console Output

**Expected Flow**:
```
[HierarchyItem] Drop event: { draggedId: "1002", targetId: "1004" }
[HierarchyItem] Emitting move-employee: { draggedId: "1002", targetId: "1004" }
[Index] handleMoveEmployee called with event: { draggedId: "1002", targetId: "1004" }
[Index] event.draggedId: 1002 event.targetId: 1004
[Index] performMove called with: { employeeId: "1002", newSuperiorId: "1004", ... }
[Index] newSuperiorId check: { newSuperiorId: "1004", isNull: false, isFalsy: false, ... }
[Index] Using "ke bawah" message because newSuperiorId exists: 1004
[Index] Final confirm message: Apakah Anda yakin ingin memindahkan John Manager ke bawah Bob Employee?
```

**If Issue Occurs**:
```
[Index] newSuperiorId check: { newSuperiorId: null, isNull: true, isFalsy: true, ... }
[Index] Using "level teratas" message because newSuperiorId is falsy: null
```

### Step 4: Identify Issue Location

**Case 1**: `targetId` is null in HierarchyItem
- Problem: Drop event tidak ter-capture dengan benar
- Solution: Fix drag event handlers

**Case 2**: `targetId` hilang di handleMoveEmployee  
- Problem: Event object structure tidak sesuai
- Solution: Fix parameter destructuring

**Case 3**: `newSuperiorId` menjadi null di performMove
- Problem: Parameter passing atau type conversion
- Solution: Fix parameter mapping

## Common Issues & Solutions

### Issue: `targetId` is `undefined`
**Solution**:
```javascript
// Check drag event data setting
event.dataTransfer.setData('text/plain', props.item.id)

// Check drop event data getting  
const draggedId = event.dataTransfer.getData('text/plain')
const targetId = props.item.id // Make sure this exists
```

### Issue: Parameter destructuring fails
**Solution**:
```javascript
// Instead of assuming object structure
const handleMoveEmployee = async (event) => {
  const { draggedId, targetId } = event
  
// Use defensive programming
const handleMoveEmployee = async (event) => {
  const draggedId = event?.draggedId
  const targetId = event?.targetId
```

### Issue: Type conversion problems
**Solution**:
```javascript
// Ensure consistent types
const targetId = String(props.item.id)
const draggedId = String(event.dataTransfer.getData('text/plain'))
```

## Expected Debug Output

With proper functionality, console should show:

```
Drop event: { draggedId: "123", targetId: "456" }
handleMoveEmployee called with event: { draggedId: "123", targetId: "456" }
event.draggedId: 123 event.targetId: 456
performMove called with: { employeeId: "123", newSuperiorId: "456", ... }
newSuperiorId check: { newSuperiorId: "456", isNull: false, isFalsy: false, type: "string" }
Using "ke bawah" message because newSuperiorId exists: 456
Final confirm message: Apakah Anda yakin ingin memindahkan John Manager ke bawah Bob Employee?
```

## Next Steps

1. **Test with Console Open**: Perform drag-drop dan check logs
2. **Identify Break Point**: Lihat di mana `targetId`/`newSuperiorId` menjadi null
3. **Fix Root Cause**: Perbaiki di lokasi yang teridentifikasi  
4. **Verify Fix**: Test ulang dan pastikan alert message benar

Dengan debug logging ini, kita akan bisa pinpoint exactly di mana masalahnya terjadi! 🔍
