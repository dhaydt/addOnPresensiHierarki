<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-gray-600">{{ title }}</p>
        <p class="text-3xl font-bold mt-2" :class="colorClasses.text">{{ formattedValue }}</p>
      </div>
      <div class="flex-shrink-0">
        <div class="w-12 h-12 rounded-lg flex items-center justify-center" :class="colorClasses.bg">
          <i :class="icon" class="text-xl"></i>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'

export default {
  name: 'StatsCard',
  props: {
    title: {
      type: String,
      required: true
    },
    value: {
      type: [Number, String],
      required: true
    },
    icon: {
      type: String,
      required: true
    },
    color: {
      type: String,
      default: 'blue',
      validator: (value) => ['blue', 'green', 'orange', 'red', 'purple'].includes(value)
    }
  },
  setup(props) {
    const colorClasses = computed(() => {
      const colors = {
        blue: {
          bg: 'bg-blue-100 text-blue-600',
          text: 'text-blue-600'
        },
        green: {
          bg: 'bg-green-100 text-green-600',
          text: 'text-green-600'
        },
        orange: {
          bg: 'bg-orange-100 text-orange-600',
          text: 'text-orange-600'
        },
        red: {
          bg: 'bg-red-100 text-red-600',
          text: 'text-red-600'
        },
        purple: {
          bg: 'bg-purple-100 text-purple-600',
          text: 'text-purple-600'
        }
      }
      return colors[props.color] || colors.blue
    })

    const formattedValue = computed(() => {
      if (typeof props.value === 'number') {
        return props.value.toLocaleString('id-ID')
      }
      return props.value
    })

    return {
      colorClasses,
      formattedValue
    }
  }
}
</script>
