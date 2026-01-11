<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { cn } from "@/lib/utils"

const props = defineProps<{
  class?: HTMLAttributes["class"],
    size?: 'sm' | 'md' | 'lg',
    title?: string,
    len?: number,
    color?: string
}>()

const sizeClasses = {
    sm: 'h-96 m-4 p-4 w-80 overflow-y-auto',
    md: 'h-96 m-4 p-4 w-80 overflow-y-auto',
    lg: 'h-96 m-4 p-4 w-80 overflow-y-auto',
}
</script>



<template>

  <div
    data-slot="card"
    :class="
      cn(
        'bg-card text-card-foreground flex flex-col gap-6 rounded-xl border py-6 shadow-sm',
        props.class,
        sizeClasses[props.size ?? 'md']
      )
    "
  >
      <h1 class="text-2xl font-mono">{{props.title}} - <span :class="`text-${props.color}-500`">({{props.len}})</span></h1>
    <slot />
  </div>
</template>
