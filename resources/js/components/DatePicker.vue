<script setup lang="ts">
import { computed, ref } from 'vue';
import { type CalendarDate, type DateValue, DateFormatter, getLocalTimeZone, parseDate, today } from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover';
import { Calendar } from '@/components/ui/calendar';

interface Props {
  modelValue?: string;
  placeholder?: string;
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Selecione uma data',
});

const emit = defineEmits<{
  'update:modelValue': [value: string | undefined];
}>();

const df = new DateFormatter('pt-BR', {
  dateStyle: 'long',
});

const dateValue = computed<DateValue | undefined>({
  get: () => {
    if (!props.modelValue) return undefined;
    try {
      return parseDate(props.modelValue);
    } catch {
      return undefined;
    }
  },
  set: (val) => {
    if (val) {
      emit('update:modelValue', val.toString());
    } else {
      emit('update:modelValue', undefined);
    }
  },
});

const placeholderValue = today(getLocalTimeZone()) as DateValue;
</script>

<template>
  <Popover>
    <PopoverTrigger as-child>
      <Button
        variant="outline"
        :class="cn(
          'w-full justify-start text-left font-normal',
          !modelValue && 'text-muted-foreground'
        )"
      >
        <CalendarIcon class="mr-2 h-4 w-4" />
        {{ dateValue ? df.format(dateValue.toDate(getLocalTimeZone())) : props.placeholder }}
      </Button>
    </PopoverTrigger>
    <PopoverContent class="w-auto p-0">
      <Calendar v-model="dateValue" :placeholder="placeholderValue" />
    </PopoverContent>
  </Popover>
</template>

