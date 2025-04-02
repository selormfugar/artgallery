"use client"

import { useState } from "react"
import { ArrowUpDown, MoreHorizontal } from "lucide-react"

import { Button } from "@/components/ui/button"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { Badge } from "@/components/ui/badge"

export function SalesTable() {
  const [sorting, setSorting] = useState<{ column: string; direction: "asc" | "desc" } | null>(null)

  // This would normally fetch from your database
  const transactions = [
    {
      id: "INV001",
      artwork: "Sunset Overdrive",
      customer: "Olivia Martin",
      email: "olivia.martin@email.com",
      amount: "$500.00",
      status: "completed",
      date: "2023-11-14",
    },
    {
      id: "INV002",
      artwork: "Mountain View",
      customer: "Jackson Lee",
      email: "jackson.lee@email.com",
      amount: "$750.00",
      status: "completed",
      date: "2023-11-12",
    },
    {
      id: "INV003",
      artwork: "Cyber Dream",
      customer: "William Kim",
      email: "william.kim@email.com",
      amount: "$300.00",
      status: "pending",
      date: "2023-11-10",
    },
    {
      id: "INV004",
      artwork: "Marble Form",
      customer: "Sofia Davis",
      email: "sofia.davis@email.com",
      amount: "$1,200.00",
      status: "failed",
      date: "2023-11-08",
    },
    {
      id: "INV005",
      artwork: "City Lights",
      customer: "Isabella Nguyen",
      email: "isabella.nguyen@email.com",
      amount: "$450.00",
      status: "completed",
      date: "2023-11-05",
    },
  ]

  return (
    <div className="rounded-md border">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead className="w-[100px]">
              <Button variant="ghost" className="p-0 font-medium">
                Invoice
                <ArrowUpDown className="ml-2 h-4 w-4" />
              </Button>
            </TableHead>
            <TableHead>
              <Button variant="ghost" className="p-0 font-medium">
                Artwork
                <ArrowUpDown className="ml-2 h-4 w-4" />
              </Button>
            </TableHead>
            <TableHead>Customer</TableHead>
            <TableHead>
              <Button variant="ghost" className="p-0 font-medium">
                Amount
                <ArrowUpDown className="ml-2 h-4 w-4" />
              </Button>
            </TableHead>
            <TableHead>
              <Button variant="ghost" className="p-0 font-medium">
                Status
                <ArrowUpDown className="ml-2 h-4 w-4" />
              </Button>
            </TableHead>
            <TableHead>
              <Button variant="ghost" className="p-0 font-medium">
                Date
                <ArrowUpDown className="ml-2 h-4 w-4" />
              </Button>
            </TableHead>
            <TableHead className="text-right">Actions</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {transactions.map((transaction) => (
            <TableRow key={transaction.id}>
              <TableCell className="font-medium">{transaction.id}</TableCell>
              <TableCell>{transaction.artwork}</TableCell>
              <TableCell>
                <div className="flex flex-col">
                  <span>{transaction.customer}</span>
                  <span className="text-xs text-muted-foreground">{transaction.email}</span>
                </div>
              </TableCell>
              <TableCell>{transaction.amount}</TableCell>
              <TableCell>
                <Badge
                  variant={
                    transaction.status === "completed"
                      ? "default"
                      : transaction.status === "pending"
                        ? "outline"
                        : "destructive"
                  }
                >
                  {transaction.status}
                </Badge>
              </TableCell>
              <TableCell>{transaction.date}</TableCell>
              <TableCell className="text-right">
                <DropdownMenu>
                  <DropdownMenuTrigger asChild>
                    <Button variant="ghost" className="h-8 w-8 p-0">
                      <span className="sr-only">Open menu</span>
                      <MoreHorizontal className="h-4 w-4" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end">
                    <DropdownMenuLabel>Actions</DropdownMenuLabel>
                    <DropdownMenuItem>View details</DropdownMenuItem>
                    <DropdownMenuItem>View customer</DropdownMenuItem>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem>Download invoice</DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </div>
  )
}

