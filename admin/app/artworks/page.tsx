import { Suspense } from "react"
import Link from "next/link"
import { Edit, MoreHorizontal, Plus, Trash } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Card, CardContent } from "@/components/ui/card"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import { Skeleton } from "@/components/ui/skeleton"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Badge } from "@/components/ui/badge"

export default function ArtworksPage() {
  return (
    <div className="flex-1 space-y-4 p-4 pt-6 md:p-8">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold tracking-tight">My Artworks</h1>
          <p className="text-muted-foreground">Manage and organize your artwork collection</p>
        </div>
        <Button asChild>
          <Link href="/upload">
            <Plus className="mr-2 h-4 w-4" />
            Upload New Artwork
          </Link>
        </Button>
      </div>
      <Tabs defaultValue="all" className="space-y-4">
        <div className="flex justify-between">
          <TabsList>
            <TabsTrigger value="all">All Artworks</TabsTrigger>
            <TabsTrigger value="active">Active</TabsTrigger>
            <TabsTrigger value="sold">Sold</TabsTrigger>
            <TabsTrigger value="hidden">Hidden</TabsTrigger>
          </TabsList>
          <div className="flex items-center gap-2">
            <Button variant="outline" size="sm">
              Filter
            </Button>
            <Button variant="outline" size="sm">
              Sort
            </Button>
          </div>
        </div>
        <TabsContent value="all" className="space-y-4">
          <Suspense fallback={<ArtworkGridSkeleton />}>
            <ArtworkGrid />
          </Suspense>
        </TabsContent>
        <TabsContent value="active" className="space-y-4">
          <Suspense fallback={<ArtworkGridSkeleton />}>
            <ArtworkGrid filter="active" />
          </Suspense>
        </TabsContent>
        <TabsContent value="sold" className="space-y-4">
          <Suspense fallback={<ArtworkGridSkeleton />}>
            <ArtworkGrid filter="sold" />
          </Suspense>
        </TabsContent>
        <TabsContent value="hidden" className="space-y-4">
          <Suspense fallback={<ArtworkGridSkeleton />}>
            <ArtworkGrid filter="hidden" />
          </Suspense>
        </TabsContent>
      </Tabs>
    </div>
  )
}

function ArtworkGrid({ filter }: { filter?: string }) {
  // This would normally fetch from your database
  const artworks = [
    {
      id: "1",
      title: "Sunset Overdrive",
      description: "A stunning abstract piece.",
      price: 500.0,
      category: "Abstract",
      image: "/placeholder.svg?height=300&width=300",
      status: "sold",
      views: 1245,
      created: "2023-05-15",
    },
    {
      id: "2",
      title: "Mountain View",
      description: "A realistic painting of a mountain.",
      price: 750.0,
      category: "Landscape",
      image: "/placeholder.svg?height=300&width=300",
      status: "sold",
      views: 987,
      created: "2023-06-22",
    },
    {
      id: "3",
      title: "Cyber Dream",
      description: "A futuristic digital artwork.",
      price: 300.0,
      category: "Digital",
      image: "/placeholder.svg?height=300&width=300",
      status: "active",
      views: 756,
      created: "2023-07-10",
    },
    {
      id: "4",
      title: "Marble Form",
      description: "A minimalist sculpture in marble.",
      price: 1200.0,
      category: "Sculpture",
      image: "/placeholder.svg?height=300&width=300",
      status: "active",
      views: 543,
      created: "2023-08-05",
    },
    {
      id: "5",
      title: "City Lights",
      description: "A graffiti-inspired street art piece.",
      price: 450.0,
      category: "Street Art",
      image: "/placeholder.svg?height=300&width=300",
      status: "hidden",
      views: 321,
      created: "2023-09-18",
    },
    {
      id: "6",
      title: "Ocean Waves",
      description: "A serene seascape painting.",
      price: 600.0,
      category: "Landscape",
      image: "/placeholder.svg?height=300&width=300",
      status: "active",
      views: 432,
      created: "2023-10-30",
    },
  ]

  const filteredArtworks = filter ? artworks.filter((artwork) => artwork.status === filter) : artworks

  return (
    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      {filteredArtworks.map((artwork) => (
        <Card key={artwork.id} className="overflow-hidden">
          <div className="relative aspect-square">
            <img src={artwork.image || "/placeholder.svg"} alt={artwork.title} className="object-cover w-full h-full" />
            <div className="absolute top-2 right-2">
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <Button variant="ghost" size="icon" className="bg-background/80 backdrop-blur-sm">
                    <MoreHorizontal className="h-4 w-4" />
                    <span className="sr-only">Actions</span>
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                  <DropdownMenuLabel>Actions</DropdownMenuLabel>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem>
                    <Edit className="mr-2 h-4 w-4" />
                    <span>Edit</span>
                  </DropdownMenuItem>
                  <DropdownMenuItem>
                    <Trash className="mr-2 h-4 w-4" />
                    <span>Delete</span>
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </div>
            {artwork.status === "sold" && <Badge className="absolute top-2 left-2 bg-primary">Sold</Badge>}
            {artwork.status === "hidden" && (
              <Badge className="absolute top-2 left-2 bg-muted" variant="outline">
                Hidden
              </Badge>
            )}
          </div>
          <CardContent className="p-4">
            <div className="space-y-2">
              <h3 className="font-semibold">{artwork.title}</h3>
              <p className="text-sm text-muted-foreground line-clamp-2">{artwork.description}</p>
              <div className="flex justify-between items-center">
                <span className="font-medium">${artwork.price.toFixed(2)}</span>
                <span className="text-xs text-muted-foreground">{artwork.views} views</span>
              </div>
              <div className="flex items-center justify-between pt-2">
                <Badge variant="outline">{artwork.category}</Badge>
                <span className="text-xs text-muted-foreground">Added {artwork.created}</span>
              </div>
            </div>
          </CardContent>
        </Card>
      ))}
    </div>
  )
}

function ArtworkGridSkeleton() {
  return (
    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      {Array.from({ length: 8 }).map((_, i) => (
        <Card key={i} className="overflow-hidden">
          <Skeleton className="aspect-square" />
          <CardContent className="p-4">
            <div className="space-y-2">
              <Skeleton className="h-5 w-3/4" />
              <Skeleton className="h-4 w-full" />
              <div className="flex justify-between items-center">
                <Skeleton className="h-4 w-1/4" />
                <Skeleton className="h-3 w-1/4" />
              </div>
              <div className="flex items-center justify-between pt-2">
                <Skeleton className="h-5 w-1/3 rounded-full" />
                <Skeleton className="h-3 w-1/4" />
              </div>
            </div>
          </CardContent>
        </Card>
      ))}
    </div>
  )
}

