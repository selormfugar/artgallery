import { CheckCircle2, Circle } from "lucide-react"

import { cn } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import { Checkbox } from "@/components/ui/checkbox"

interface Task {
  id: string
  title: string
  description: string
  completed: boolean
  dueDate: string
}

export function UpcomingTasks() {
  const tasks: Task[] = [
    {
      id: "1",
      title: "Upload new artwork",
      description: "Prepare and upload the new landscape series",
      completed: false,
      dueDate: "Tomorrow",
    },
    {
      id: "2",
      title: "Respond to buyer messages",
      description: "3 unread messages from potential buyers",
      completed: false,
      dueDate: "Today",
    },
    {
      id: "3",
      title: "Update artwork descriptions",
      description: "Add more details to recent uploads",
      completed: true,
      dueDate: "Yesterday",
    },
    {
      id: "4",
      title: "Prepare for upcoming exhibition",
      description: "Finalize artwork selection for gallery showing",
      completed: false,
      dueDate: "Next week",
    },
  ]

  return (
    <div className="space-y-4">
      {tasks.map((task) => (
        <div
          key={task.id}
          className={cn("flex items-start space-x-4 rounded-md border p-4", task.completed && "bg-muted/50")}
        >
          <Checkbox id={`task-${task.id}`} checked={task.completed} className="mt-1" />
          <div className="flex-1 space-y-1">
            <label
              htmlFor={`task-${task.id}`}
              className={cn("font-medium", task.completed && "line-through text-muted-foreground")}
            >
              {task.title}
            </label>
            <p className="text-sm text-muted-foreground">{task.description}</p>
            <div className="flex items-center pt-2">
              <span className="text-xs text-muted-foreground">Due: {task.dueDate}</span>
              {task.dueDate === "Today" && (
                <span className="ml-2 rounded-full bg-orange-500/10 px-2 py-0.5 text-xs font-medium text-orange-500">
                  Urgent
                </span>
              )}
            </div>
          </div>
          <Button
            variant="ghost"
            size="icon"
            className={cn("rounded-full", task.completed ? "text-primary" : "text-muted-foreground")}
          >
            {task.completed ? <CheckCircle2 className="h-5 w-5" /> : <Circle className="h-5 w-5" />}
            <span className="sr-only">{task.completed ? "Mark as incomplete" : "Mark as complete"}</span>
          </Button>
        </div>
      ))}
    </div>
  )
}

