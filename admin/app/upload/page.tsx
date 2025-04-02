"use client"

import type React from "react"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { ImagePlus, Upload } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Card, CardContent } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Textarea } from "@/components/ui/textarea"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { useToast } from "@/components/ui/use-toast"

export default function UploadPage() {
  const router = useRouter()
  const { toast } = useToast()
  const [isUploading, setIsUploading] = useState(false)
  const [preview, setPreview] = useState<string | null>(null)

  const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0]
    if (file) {
      const reader = new FileReader()
      reader.onloadend = () => {
        setPreview(reader.result as string)
      }
      reader.readAsDataURL(file)
    }
  }

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault()
    setIsUploading(true)

    // Simulate API call
    setTimeout(() => {
      setIsUploading(false)
      toast({
        title: "Artwork uploaded successfully",
        description: "Your artwork has been uploaded and is now visible in your gallery.",
      })
      router.push("/artworks")
    }, 2000)
  }

  return (
    <div className="flex-1 space-y-4 p-4 pt-6 md:p-8">
      <div>
        <h1 className="text-3xl font-bold tracking-tight">Upload Artwork</h1>
        <p className="text-muted-foreground">Add a new artwork to your collection</p>
      </div>

      <form onSubmit={handleSubmit} className="space-y-8">
        <div className="grid gap-6 md:grid-cols-2">
          <Card>
            <CardContent className="p-6">
              <div className="space-y-2">
                <Label htmlFor="image">Artwork Image</Label>
                <div
                  className="flex flex-col items-center justify-center border-2 border-dashed rounded-lg p-12 cursor-pointer hover:bg-muted/50 transition-colors"
                  onClick={() => document.getElementById("image")?.click()}
                >
                  {preview ? (
                    <div className="relative w-full aspect-square">
                      <img
                        src={preview || "/placeholder.svg"}
                        alt="Artwork preview"
                        className="object-contain w-full h-full"
                      />
                    </div>
                  ) : (
                    <div className="flex flex-col items-center justify-center text-center">
                      <ImagePlus className="h-16 w-16 text-muted-foreground mb-4" />
                      <h3 className="font-medium text-lg">Upload Artwork Image</h3>
                      <p className="text-sm text-muted-foreground mt-1">Drag and drop or click to browse</p>
                      <p className="text-xs text-muted-foreground mt-2">Supports JPG, PNG, WEBP up to 10MB</p>
                    </div>
                  )}
                  <Input id="image" type="file" accept="image/*" className="hidden" onChange={handleImageChange} />
                </div>
              </div>
            </CardContent>
          </Card>

          <div className="space-y-6">
            <div className="space-y-2">
              <Label htmlFor="title">Title</Label>
              <Input id="title" placeholder="Enter artwork title" required />
            </div>

            <div className="space-y-2">
              <Label htmlFor="description">Description</Label>
              <Textarea id="description" placeholder="Describe your artwork..." className="min-h-[120px]" required />
            </div>

            <div className="grid gap-4 grid-cols-2">
              <div className="space-y-2">
                <Label htmlFor="price">Price ($)</Label>
                <Input id="price" type="number" min="0" step="0.01" placeholder="0.00" required />
              </div>

              <div className="space-y-2">
                <Label htmlFor="category">Category</Label>
                <Select required>
                  <SelectTrigger id="category">
                    <SelectValue placeholder="Select category" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="abstract">Abstract</SelectItem>
                    <SelectItem value="landscape">Landscape</SelectItem>
                    <SelectItem value="portrait">Portrait</SelectItem>
                    <SelectItem value="digital">Digital</SelectItem>
                    <SelectItem value="sculpture">Sculpture</SelectItem>
                    <SelectItem value="street-art">Street Art</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>

            <div className="space-y-2">
              <Label htmlFor="tags">Tags</Label>
              <Input id="tags" placeholder="Enter tags separated by commas" />
            </div>
          </div>
        </div>

        <div className="flex justify-end gap-4">
          <Button variant="outline" type="button" onClick={() => router.back()}>
            Cancel
          </Button>
          <Button type="submit" disabled={isUploading}>
            {isUploading ? (
              <>Uploading...</>
            ) : (
              <>
                <Upload className="mr-2 h-4 w-4" />
                Upload Artwork
              </>
            )}
          </Button>
        </div>
      </form>
    </div>
  )
}

