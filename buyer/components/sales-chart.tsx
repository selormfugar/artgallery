"use client"

import { ChartContainer, ChartTooltip, LineChart } from "@/components/ui/chart"

const data = [
  {
    date: "Jan",
    Sales: 2500,
    Revenue: 1400,
    Views: 4000,
  },
  {
    date: "Feb",
    Sales: 1800,
    Revenue: 1200,
    Views: 3500,
  },
  {
    date: "Mar",
    Sales: 3000,
    Revenue: 1800,
    Views: 5000,
  },
  {
    date: "Apr",
    Sales: 2800,
    Revenue: 1600,
    Views: 4800,
  },
  {
    date: "May",
    Sales: 3500,
    Revenue: 2200,
    Views: 6000,
  },
  {
    date: "Jun",
    Sales: 4000,
    Revenue: 2600,
    Views: 7000,
  },
]

export function SalesChart() {
  return (
    <ChartContainer className="aspect-[4/3] sm:aspect-[2/1] h-[350px]">
      <LineChart
        data={data}
        categories={["Sales", "Revenue", "Views"]}
        index="date"
        colors={["emerald", "violet", "blue"]}
        valueFormatter={(value) => `$${value}`}
        yAxisWidth={60}
        showAnimation
      />
      <ChartTooltip />
    </ChartContainer>
  )
}

