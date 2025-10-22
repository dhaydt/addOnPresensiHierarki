<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-between items-center py-5">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">
              <i class="fas fa-sitemap text-blue-600 mr-3"></i>
              Pengaturan Atasan Pegawai (Inertia.js)
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ department.DeptName }}</p>
          </div>
          <div class="flex items-center gap-3">
            <div class="text-right">
              <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
              <p class="text-xs text-gray-500">Administrator</p>
            </div>
            <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center">
              <i class="fas fa-user"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-6 py-8">
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <StatsCard
          title="Total Pegawai"
          :value="stats.totalEmployees"
          icon="fas fa-users"
          color="blue"
        />
        <StatsCard
          title="Sudah Ada Atasan"
          :value="stats.withSuperior"
          icon="fas fa-user-check"
          color="green"
        />
        <StatsCard
          title="Belum Ada Atasan"
          :value="stats.withoutSuperior"
          icon="fas fa-user-clock"
          color="orange"
        />
      </div>

      <!-- Search and Controls -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="p-6 border-b border-gray-100">
          <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900">Hierarki Pegawai</h2>
            <div class="flex items-center gap-3">
              <button
                @click="refreshData"
                :disabled="loading"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 transition-colors flex items-center gap-2"
              >
                <i class="fas fa-sync-alt" :class="{ 'animate-spin': loading }"></i>
                Refresh
              </button>
              <div class="relative">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Cari pegawai..."
                  class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64"
                />
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Hierarchy Tree -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-100">
          <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
            <i class="fas fa-sitemap text-blue-600"></i>
            Struktur Organisasi
            <span class="text-sm font-normal text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
              ({{ stats.totalEmployees }} pegawai)
            </span>
          </h2>
          <p class="text-sm text-gray-600 mt-2">
            <i class="fas fa-info-circle"></i>
            Drag dan drop pegawai untuk mengubah struktur hierarki. Drop ke area kosong untuk menjadikan level teratas.
          </p>
        </div>

        <div class="p-6">
          <div v-if="filteredHierarchy.length > 0" class="space-y-2">
            <HierarchyItem
              v-for="item in filteredHierarchy"
              :key="`item-${item.id}-${item.level}`"
              :item="item"
              :is-last="false"
              :prefix="''"
              :search="searchQuery"
              @move-employee="handleMoveEmployee"
              @set-superior="handleSetSuperior"
              @remove-superior="handleRemoveSuperior"
            />

            <!-- Top Level Drop Zone -->
            <div
              @drop="handleTopLevelDrop"
              @dragover.prevent
              @dragenter.prevent
              class="mt-4 p-4 border-2 border-dashed border-gray-300 rounded-lg text-center text-gray-500 bg-gray-50 opacity-0 transition-opacity duration-300"
              :class="{ 'opacity-100': isDragging }"
              ref="topDropZone"
            >
              <i class="fas fa-level-up-alt"></i>
              <span class="ml-2">Drop di sini untuk menjadikan level teratas (tanpa atasan)</span>
            </div>
          </div>

          <div v-else class="text-center py-12">
            <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
            <p class="text-lg text-gray-600">Belum ada struktur hierarki</p>
            <p class="text-sm text-gray-500 mt-2">Mulai mengatur atasan untuk membuat struktur organisasi</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div v-if="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
        <div class="flex items-center gap-3">
          <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
          <span class="text-gray-700">{{ loadingMessage }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import StatsCard from '@/Components/StatsCard.vue'
import HierarchyItem from '@/Components/HierarchyItem.vue'

export default {
  name: 'HierarchyIndex',
  components: {
    StatsCard,
    HierarchyItem
  },
  props: {
    hierarchyTree: Array,
    employees: Array,
    existingSuperiors: Object,
    potentialSuperiors: Array,
    department: Object,
    user: Object,
    stats: Object
  },
  setup(props) {
    const searchQuery = ref('')
    const loading = ref(false)
    const loadingMessage = ref('Memuat...')
    const isDragging = ref(false)

    // Computed filtered hierarchy based on search
    const filteredHierarchy = computed(() => {
      if (!searchQuery.value) {
        return props.hierarchyTree
      }

      return filterHierarchy(props.hierarchyTree, searchQuery.value)
    })

    // Filter hierarchy recursively
    const filterHierarchy = (items, query) => {
      return items.filter(item => {
        const matchesName = item.employee.name.toLowerCase().includes(query.toLowerCase())
        const hasMatchingSubordinates = item.subordinates && filterHierarchy(item.subordinates, query).length > 0

        if (matchesName || hasMatchingSubordinates) {
          return {
            ...item,
            subordinates: hasMatchingSubordinates ? filterHierarchy(item.subordinates, query) : item.subordinates
          }
        }
        return false
      }).filter(Boolean)
    }

    // Handle drag and drop events
    const handleMoveEmployee = async (event) => {
      console.log('handleMoveEmployee called with event:', event)
      console.log('event.draggedId:', event.draggedId, 'event.targetId:', event.targetId)

      // Validate that we have both IDs
      if (!event.draggedId) {
        console.error('No draggedId provided')
        return
      }

      if (!event.targetId) {
        console.error('No targetId provided - this should not happen for regular drops')
        return
      }

      // Find both employees to determine their levels
      const draggedEmployee = findEmployeeInHierarchy(props.hierarchyTree, event.draggedId) ||
                              props.employees.find(emp => emp.userid == event.draggedId)
      const targetEmployee = findEmployeeInHierarchy(props.hierarchyTree, event.targetId) ||
                             props.employees.find(emp => emp.userid == event.targetId)

      console.log('Employee levels:', {
        dragged: { id: event.draggedId, level: draggedEmployee?.level, name: draggedEmployee?.name },
        target: { id: event.targetId, level: targetEmployee?.level, name: targetEmployee?.name }
      })

      // Special handling for level 0 to level 0 (both should remain at top level)
      if (draggedEmployee?.level === 0 && targetEmployee?.level === 0) {
        console.log('Level 0 to Level 0 detected - keeping both at top level')
        await performMove(event.draggedId, null, 'Memindahkan ke level teratas...')
      } else {
        // Normal case: set target as new superior
        console.log('Normal move - setting target as superior')
        await performMove(event.draggedId, event.targetId, 'Memindahkan pegawai...')
      }
    }

    const handleTopLevelDrop = async (event) => {
      event.preventDefault()
      const draggedId = event.dataTransfer.getData('text/plain')
      if (draggedId) {
        await performMove(draggedId, null, 'Memindahkan ke level teratas...')
      }
      isDragging.value = false
    }

    // Handle set/remove superior
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

    // Perform move operation
    const performMove = async (employeeId, newSuperiorId, message) => {
      console.log('performMove called with:', {
        employeeId: employeeId,
        newSuperiorId: newSuperiorId,
        employeeIdType: typeof employeeId,
        newSuperiorIdType: typeof newSuperiorId
      })

      // Validate parameters
      if (!employeeId) {
        console.error('performMove: employeeId is missing or invalid')
        alert('Error: Employee ID tidak valid')
        return
      }

      let draggedEmployee = findEmployeeInHierarchy(props.hierarchyTree, employeeId)
      let targetEmployee = newSuperiorId ? findEmployeeInHierarchy(props.hierarchyTree, newSuperiorId) : null

      // Fallback: search in props.employees if not found in hierarchy
      if (!draggedEmployee) {
        console.log('Not found in hierarchy, searching in employees list...')
        const employeeInfo = props.employees.find(emp => emp.userid == employeeId)
        if (employeeInfo) {
          draggedEmployee = employeeInfo
          console.log('Found in employees list:', draggedEmployee)
        }
      }

      if (!targetEmployee && newSuperiorId) {
        const employeeInfo = props.employees.find(emp => emp.userid == newSuperiorId)
        if (employeeInfo) {
          targetEmployee = employeeInfo
          console.log('Found target in employees list:', targetEmployee)
        }
      }

      console.log('Found employees:', { draggedEmployee, targetEmployee })
      console.log('newSuperiorId check:', {
        newSuperiorId: newSuperiorId,
        isNull: newSuperiorId === null,
        isUndefined: newSuperiorId === undefined,
        isFalsy: !newSuperiorId,
        type: typeof newSuperiorId
      })

      let confirmMessage = ''
      if (newSuperiorId) {
        confirmMessage = `Apakah Anda yakin ingin memindahkan ${draggedEmployee?.name || 'Employee'} ke bawah ${targetEmployee?.name || 'Target Employee'}?`
        console.log('Using "ke bawah" message because newSuperiorId exists:', newSuperiorId)
      } else {
        // Check if this is a level 0 to level 0 move by checking the message parameter
        if (message && message.includes('level teratas')) {
          confirmMessage = `Apakah Anda yakin ingin memindahkan ${draggedEmployee?.name || 'Employee'} ke level teratas (tanpa atasan)?`
        } else {
          // This is likely a level 0 to level 0 move - should show different message
          confirmMessage = `Apakah Anda yakin ingin memindahkan ${draggedEmployee?.name || 'Employee'} untuk tetap di level teratas?`
        }
        console.log('Using "level teratas" message because newSuperiorId is falsy:', newSuperiorId)
      }

      console.log('Final confirm message:', confirmMessage)

      if (confirm(confirmMessage)) {
        console.log('User confirmed, sending API call...')
        await performApiCall('/hierarchy/move-employee', {
          employeeId,
          newSuperiorId
        }, message)
      }
    }

    // Perform API call with loading state
    const performApiCall = async (url, data, message, method = 'POST') => {
      loading.value = true
      loadingMessage.value = message

      try {
        let response;

        if (method === 'DELETE') {
          await router.delete(url, {
            data: data,
            preserveState: false,
            preserveScroll: true,
            onSuccess: (page) => {
              console.log('Success:', 'Operasi berhasil')
            },
            onError: (errors) => {
              console.error('Errors:', errors)
              alert(errors.message || 'Terjadi kesalahan')
            }
          })
        } else {
          await router.post(url, data, {
            preserveState: false,
            preserveScroll: true,
            onSuccess: (page) => {
              console.log('Success:', 'Operasi berhasil')
            },
            onError: (errors) => {
              console.error('Errors:', errors)
              alert(errors.message || 'Terjadi kesalahan')
            }
          })
        }

      } catch (error) {
        console.error('Error:', error)
        alert('Terjadi kesalahan saat memproses permintaan')
      } finally {
        loading.value = false
      }
    }

    // Utility function to find employee in hierarchy
    const findEmployeeInHierarchy = (items, employeeId) => {
      console.log('Finding employee:', { employeeId, items })

      for (const item of items) {
        console.log('Checking item:', { itemId: item.id, employeeId, match: item.id == employeeId })

        if (item.id == employeeId) {
          console.log('Found employee:', item.employee)
          return item.employee
        }
        if (item.subordinates && item.subordinates.length > 0) {
          const found = findEmployeeInHierarchy(item.subordinates, employeeId)
          if (found) return found
        }
      }

      console.log('Employee not found:', employeeId)
      return null
    }

    // Refresh data
    const refreshData = () => {
      router.reload({
        only: ['hierarchyTree', 'stats'],
        onStart: () => {
          loading.value = true
          loadingMessage.value = 'Memuat ulang data...'
        },
        onFinish: () => {
          loading.value = false
        }
      })
    }

    // Global drag event listeners
    onMounted(() => {
      document.addEventListener('dragstart', () => {
        isDragging.value = true
      })

      document.addEventListener('dragend', () => {
        isDragging.value = false
      })
    })

    return {
      searchQuery,
      loading,
      loadingMessage,
      isDragging,
      filteredHierarchy,
      handleMoveEmployee,
      handleTopLevelDrop,
      handleSetSuperior,
      handleRemoveSuperior,
      refreshData
    }
  }
}
</script>
