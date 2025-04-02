"use client"

import type * as React from "react"
import {
  XAxis,
  YAxis,
  Tooltip,
  ResponsiveContainer,
  LineChart as RechartsLineChart,
  Line,
  CartesianGrid,
} from "recharts"

interface ChartContainerProps {
  className?: string
  children: React.ReactNode
}

export function ChartContainer({ className, children }: ChartContainerProps) {
  return <div className={`rounded-md border bg-card text-card-foreground shadow-sm ${className}`}>{children}</div>
}

interface ChartTooltipProps {
  formatter?: (value: number) => string
}

export function ChartTooltip({ formatter }: ChartTooltipProps) {
  return (
    <Tooltip
      separator=""
      className="dark:bg-slate-800 bg-white border dark:border-slate-700 border-slate-200 rounded-md shadow-md"
      contentStyle={{ padding: "8px 12px" }}
      labelStyle={{ fontWeight: 600, marginBottom: 4 }}
      itemStyle={{ padding: 0, display: "flex", alignItems: "center" }}
      formatter={(value) => {
        if (formatter) {
          return formatter(value)
        }
        return value
      }}
      labelFormatter={(label) => label}
    />
  )
}

interface LineChartProps {
  data: any[]
  categories: string[]
  index: string
  colors: string[]
  valueFormatter?: (value: number) => string
  yAxisWidth?: number
  showAnimation?: boolean
}

export function LineChart({
  data,
  categories,
  index,
  colors,
  valueFormatter,
  yAxisWidth = 40,
  showAnimation = false,
}: LineChartProps) {
  return (
    <ResponsiveContainer width="100%" height="100%">
      <RechartsLineChart data={data} margin={{ top: 20, right: 20, left: 0, bottom: 20 }}>
        <CartesianGrid strokeDasharray="3 3" />
        <XAxis dataKey={index} />
        <YAxis width={yAxisWidth} />
        {categories.map((category, i) => (
          <Line
            key={category}
            type="monotone"
            dataKey={category}
            stroke={`var(--${colors[i]}-500)`}
            strokeWidth={2}
            dot={false}
            animationDuration={showAnimation ? 1000 : 0}
          />
        ))}
      </RechartsLineChart>
    </ResponsiveContainer>
  )
}

