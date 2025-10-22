<template>
  <div class="relative">
    <!-- Drop Zone Before Item (for reordering) -->
    <div
      v-if="isDragOver && !isDragging"
      class="h-1 bg-blue-500 rounded-full mx-4 mb-2 opacity-75"
    ></div>

    <!-- Employee Item -->
    <div
      :draggable="true"
      @dragstart="handleDragStart"
      @dragend="handleDragEnd"
      @drop="handleDrop"
      @dragover.prevent="handleDragOver"
      @dragenter.prevent="handleDragEnter"
      @dragleave="handleDragLeave"
      class="group flex items-center p-4 bg-white border border-gray-200 rounded-lg mb-2 cursor-move hover:shadow-md transition-all duration-200"
      :class="{
        'opacity-50': isDragging,
        'ring-2 ring-blue-500 bg-blue-50': isDragOver && !isDragging,
        'bg-yellow-50 border-yellow-300': isSearchMatch
      }"
    >
      <!-- Drag Handle -->
      <div class="flex-shrink-0 mr-3">
        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-blue-100 transition-colors">
          <i class="fas fa-grip-vertical text-gray-400 group-hover:text-blue-600"></i>
        </div>
      </div>

      <!-- Avatar -->
      <div class="flex-shrink-0 mr-4">
        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center text-white font-semibold">
          {{ getInitials(item.employee.name) }}
        </div>
      </div>

      <!-- Employee Info -->
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2">
          <h3 class="text-sm font-semibold text-gray-900 truncate">
            {{ item.employee.name }}
          </h3>
          <span v-if="item.level === 1" class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
            Level {{ item.level }}
          </span>
          <span v-else-if="item.level === 2" class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
            Level {{ item.level }}
          </span>
          <span v-else class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">
            Level {{ item.level }}
          </span>
        </div>
        <div class="flex items-center gap-3 mt-1">
          <p class="text-xs text-gray-600">
            <i class="fas fa-id-badge mr-1"></i>
            ID: {{ item.employee.userid }}
          </p>
          <p v-if="item.subordinates && item.subordinates.length > 0" class="text-xs text-green-600">
            <i class="fas fa-users mr-1"></i>
            {{ item.subordinates.length }} bawahan
          </p>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex-shrink-0 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
        <button
          @click="showActions = !showActions"
          class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
        >
          <i class="fas fa-ellipsis-v"></i>
        </button>
      </div>
    </div>

    <!-- Drop Zone Between Items -->
    <div
      v-if="isDragOver && !isDragging"
      class="h-2 bg-blue-500 rounded-full mx-4 mb-2 opacity-75"
    ></div>

    <!-- Subordinates -->
    <div v-if="item.subordinates && item.subordinates.length > 0" class="ml-8 mt-2 relative">
      <!-- Connector Line -->
      <div class="absolute -left-4 top-0 bottom-0 w-0.5 bg-gray-300"></div>

      <HierarchyItem
        v-for="(subordinate, index) in item.subordinates"
        :key="`subordinate-${subordinate.id}-${subordinate.level}`"
        :item="subordinate"
        :is-last="index === item.subordinates.length - 1"
        :prefix="prefix + '├─ '"
        :search="search"
        @move-employee="$emit('move-employee', $event.draggedId, $event.targetId)"
        @set-superior="$emit('set-superior', $event.employeeId, $event.superiorId)"
        @remove-superior="$emit('remove-superior', $event.employeeId)"
      />
    </div>

    <!-- Action Menu -->
    <div
      v-if="showActions"
      class="absolute top-full left-0 z-50 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2"
      @click.stop
    >
      <button
        @click="handleRemoveSuperior"
        class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center gap-2"
      >
        <i class="fas fa-unlink"></i>
        Hapus Hubungan Atasan
      </button>
      <button
        @click="showActions = false"
        class="w-full px-4 py-2 text-left text-sm text-gray-600 hover:bg-gray-50 flex items-center gap-2"
      >
        <i class="fas fa-times"></i>
        Tutup
      </button>
    </div>

    <!-- Overlay when dragging -->
    <div
      v-if="showActions"
      class="fixed inset-0 z-40"
      @click="showActions = false"
    ></div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'

export default {
  name: 'HierarchyItem',
  emits: ['move-employee', 'set-superior', 'remove-superior'],
  props: {
    item: {
      type: Object,
      required: true
    },
    isLast: {
      type: Boolean,
      default: false
    },
    prefix: {
      type: String,
      default: ''
    },
    search: {
      type: String,
      default: ''
    }
  },
  setup(props, { emit }) {
    const isDragging = ref(false)
    const isDragOver = ref(false)
    const isHighlighted = ref(false)
    const showActions = ref(false)

    // Check if current item matches search
    const isSearchMatch = computed(() => {
      if (!props.search) return false
      return props.item.employee.name.toLowerCase().includes(props.search.toLowerCase())
    })

    // Get initials from name
    const getInitials = (name) => {
      return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2)
    }

    // Drag and Drop Handlers
    const handleDragStart = (event) => {
      isDragging.value = true
      event.dataTransfer.setData('text/plain', props.item.id)
      event.dataTransfer.effectAllowed = 'move'

      // Create drag image
      const dragImage = event.target.cloneNode(true)
      dragImage.style.opacity = '0.8'
      dragImage.style.transform = 'rotate(5deg)'
      document.body.appendChild(dragImage)
      event.dataTransfer.setDragImage(dragImage, 0, 0)

      setTimeout(() => {
        document.body.removeChild(dragImage)
      }, 0)
    }

    const handleDragEnd = () => {
      isDragging.value = false
      isDragOver.value = false
      isHighlighted.value = false
    }

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

    const handleDrop = (event) => {
      event.preventDefault()
      event.stopPropagation()

      const draggedId = event.dataTransfer.getData('text/plain')
      const targetId = props.item.id

      console.log('Drop event:', { draggedId, targetId })

      if (draggedId !== targetId) {
        // Check if this would create circular dependency
        if (!wouldCreateCircularDependency(draggedId, targetId)) {
          console.log('Emitting move-employee:', { draggedId, targetId })
          emit('move-employee', { draggedId, targetId })
        } else {
          alert('Tidak dapat memindahkan: akan membuat hubungan melingkar')
        }
      }

      isDragOver.value = false
      isHighlighted.value = false
    }

    const handleRemoveSuperior = () => {
      if (confirm(`Hapus hubungan atasan untuk ${props.item.employee.name}?`)) {
        emit('remove-superior', { employeeId: props.item.id })
      }
      showActions.value = false
    }

    // Utility function to check circular dependency
    const wouldCreateCircularDependency = (draggedId, targetId) => {
      // This is a simplified check - in real implementation,
      // you'd want to traverse the full hierarchy
      return draggedId === targetId ||
             isSubordinateOf(targetId, draggedId, props.item)
    }

    const isSubordinateOf = (checkId, parentId, item) => {
      if (item.id === parentId) {
        return checkSubordinates(item.subordinates, checkId)
      }

      if (item.subordinates) {
        return item.subordinates.some(sub => isSubordinateOf(checkId, parentId, sub))
      }

      return false
    }

    const checkSubordinates = (subordinates, checkId) => {
      if (!subordinates) return false

      return subordinates.some(sub => {
        if (sub.id === checkId) return true
        return checkSubordinates(sub.subordinates, checkId)
      })
    }

    return {
      isDragging,
      isDragOver,
      isHighlighted,
      showActions,
      isSearchMatch,
      getInitials,
      handleDragStart,
      handleDragEnd,
      handleDragOver,
      handleDragEnter,
      handleDragLeave,
      handleDrop,
      handleRemoveSuperior
    }
  }
}
</script>

<style scoped>
/* Custom drag styles */
.group:hover .fas.fa-grip-vertical {
  color: #2563eb;
}

/* Smooth transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}
</style>
