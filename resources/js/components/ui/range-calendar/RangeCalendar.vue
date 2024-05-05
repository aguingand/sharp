<script lang="ts" setup>
import { type HTMLAttributes, computed } from 'vue'
import { RangeCalendarRoot, type RangeCalendarRootEmits, type RangeCalendarRootProps, useForwardPropsEmits } from 'radix-vue'
import RangeCalendarPrevButton from "@/components/ui/range-calendar/RangeCalendarPrevButton.vue";
import RangeCalendarHeader from "@/components/ui/range-calendar/RangeCalendarHeader.vue";
import RangeCalendarNextButton from "@/components/ui/range-calendar/RangeCalendarNextButton.vue";
import RangeCalendarGrid from "@/components/ui/range-calendar/RangeCalendarGrid.vue";
import RangeCalendarGridHead from "@/components/ui/range-calendar/RangeCalendarGridHead.vue";
import RangeCalendarGridRow from "@/components/ui/range-calendar/RangeCalendarGridRow.vue";
import RangeCalendarHeadCell from "@/components/ui/range-calendar/RangeCalendarHeadCell.vue";
import RangeCalendarCellTrigger from "@/components/ui/range-calendar/RangeCalendarCellTrigger.vue";
import RangeCalendarHeading from "@/components/ui/range-calendar/RangeCalendarHeading.vue";
import RangeCalendarGridBody from "@/components/ui/range-calendar/RangeCalendarGridBody.vue";
import RangeCalendarCell from "@/components/ui/range-calendar/RangeCalendarCell.vue";
import { cn } from '@/utils/cn'

const props = defineProps<RangeCalendarRootProps & { class?: HTMLAttributes['class'] }>()

const emits = defineEmits<RangeCalendarRootEmits>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props

  return delegated
})

const forwarded = useForwardPropsEmits(delegatedProps, emits)
</script>

<template>
  <RangeCalendarRoot
    v-slot="{ grid, weekDays }"
    :class="cn('p-3', props.class)"
    v-bind="forwarded"
  >
    <RangeCalendarHeader>
      <RangeCalendarPrevButton />
      <RangeCalendarHeading />
      <RangeCalendarNextButton />
    </RangeCalendarHeader>

    <div class="flex flex-col gap-y-4 mt-4 sm:flex-row sm:gap-x-4 sm:gap-y-0">
      <RangeCalendarGrid v-for="month in grid" :key="month.value.toString()">
        <RangeCalendarGridHead>
          <RangeCalendarGridRow>
            <RangeCalendarHeadCell
              v-for="day in weekDays" :key="day"
            >
              {{ day }}
            </RangeCalendarHeadCell>
          </RangeCalendarGridRow>
        </RangeCalendarGridHead>
        <RangeCalendarGridBody>
          <RangeCalendarGridRow v-for="(weekDates, index) in month.rows" :key="`weekDate-${index}`" class="mt-2 w-full">
            <RangeCalendarCell
              v-for="weekDate in weekDates"
              :key="weekDate.toString()"
              :date="weekDate"
            >
              <RangeCalendarCellTrigger
                :day="weekDate"
                :month="month.value"
              />
            </RangeCalendarCell>
          </RangeCalendarGridRow>
        </RangeCalendarGridBody>
      </RangeCalendarGrid>
    </div>
  </RangeCalendarRoot>
</template>
