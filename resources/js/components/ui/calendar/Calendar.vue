<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { type DateValue, getLocalTimeZone, today } from '@internationalized/date'
import { CalendarRoot as CalendarPrimitive } from 'reka-ui'
import { cn } from '@/lib/utils'
import CalendarCell from './CalendarCell.vue'
import CalendarCellTrigger from './CalendarCellTrigger.vue'
import CalendarGrid from './CalendarGrid.vue'
import CalendarGridBody from './CalendarGridBody.vue'
import CalendarGridHead from './CalendarGridHead.vue'
import CalendarGridRow from './CalendarGridRow.vue'
import CalendarHeadCell from './CalendarHeadCell.vue'
import CalendarHeader from './CalendarHeader.vue'
import CalendarHeading from './CalendarHeading.vue'
import CalendarNextButton from './CalendarNextButton.vue'
import CalendarPrevButton from './CalendarPrevButton.vue'

const props = withDefaults(
  defineProps<{
    modelValue?: DateValue
    placeholder?: DateValue
    locale?: string
    class?: HTMLAttributes['class']
  }>(),
  {
    placeholder: () => today(getLocalTimeZone()),
    locale: 'pt-BR',
  },
)

const emits = defineEmits<{
  'update:modelValue': [value: DateValue | undefined]
}>()
</script>

<template>
  <CalendarPrimitive
    v-slot="{ weekDays, grid }"
    :model-value="modelValue"
    :locale="locale"
    :class="cn('p-3', props.class)"
    @update:model-value="(val) => emits('update:modelValue', val)"
  >
    <CalendarHeader>
      <CalendarPrevButton />
      <CalendarHeading />
      <CalendarNextButton />
    </CalendarHeader>

    <div class="flex flex-col gap-y-4 mt-4 sm:flex-row sm:gap-x-4 sm:gap-y-0">
      <CalendarGrid v-for="month in grid" :key="month.value.toString()">
        <CalendarGridHead>
          <CalendarGridRow>
            <CalendarHeadCell v-for="day in weekDays" :key="day">
              {{ day }}
            </CalendarHeadCell>
          </CalendarGridRow>
        </CalendarGridHead>
        <CalendarGridBody>
          <CalendarGridRow
            v-for="(weekDates, index) in month.rows"
            :key="`weekDate-${index}`"
            class="mt-2 w-full"
          >
            <CalendarCell
              v-for="weekDate in weekDates"
              :key="weekDate.toString()"
              :date="weekDate"
            >
              <CalendarCellTrigger :day="weekDate" :month="month.value" />
            </CalendarCell>
          </CalendarGridRow>
        </CalendarGridBody>
      </CalendarGrid>
    </div>
  </CalendarPrimitive>
</template>
