<script lang="ts" setup>
import { type HTMLAttributes, computed } from 'vue'
import { CalendarRoot, type CalendarRootEmits, type CalendarRootProps, useForwardPropsEmits } from 'radix-vue'
import { cn } from '@/utils/cn'
import CalendarPrevButton from "@/components/ui/calendar/CalendarPrevButton.vue";
import CalendarHeader from "@/components/ui/calendar/CalendarHeader.vue";
import CalendarNextButton from "@/components/ui/calendar/CalendarNextButton.vue";
import CalendarGrid from "@/components/ui/calendar/CalendarGrid.vue";
import CalendarGridHead from "@/components/ui/calendar/CalendarGridHead.vue";
import CalendarGridRow from "@/components/ui/calendar/CalendarGridRow.vue";
import CalendarHeadCell from "@/components/ui/calendar/CalendarHeadCell.vue";
import CalendarCellTrigger from "@/components/ui/calendar/CalendarCellTrigger.vue";
import CalendarHeading from "@/components/ui/calendar/CalendarHeading.vue";
import CalendarGridBody from "@/components/ui/calendar/CalendarGridBody.vue";
import CalendarCell from "@/components/ui/calendar/CalendarCell.vue";

const props = defineProps<CalendarRootProps & { class?: HTMLAttributes['class'] }>()

const emits = defineEmits<CalendarRootEmits>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props

  return delegated
})

const forwarded = useForwardPropsEmits(delegatedProps, emits)
</script>

<template>
  <CalendarRoot
    v-slot="{ grid, weekDays }"
    :class="cn('p-3', props.class)"
    v-bind="forwarded"
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
            <CalendarHeadCell
              v-for="day in weekDays" :key="day"
            >
              {{ day }}
            </CalendarHeadCell>
          </CalendarGridRow>
        </CalendarGridHead>
        <CalendarGridBody>
          <CalendarGridRow v-for="(weekDates, index) in month.rows" :key="`weekDate-${index}`" class="mt-2 w-full">
            <CalendarCell
              v-for="weekDate in weekDates"
              :key="weekDate.toString()"
              :date="weekDate"
            >
              <CalendarCellTrigger
                :day="weekDate"
                :month="month.value"
              />
            </CalendarCell>
          </CalendarGridRow>
        </CalendarGridBody>
      </CalendarGrid>
    </div>
  </CalendarRoot>
</template>
